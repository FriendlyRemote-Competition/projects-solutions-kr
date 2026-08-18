<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\LineResource;
use App\Models\Booking;
use App\Models\CancelledDeparture;
use App\Models\Line;
use App\Models\Station;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function lineStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|regex:/^[A-Z]{2,4}$/|unique:lines,code',
            'name' => 'required|string',
            'station_a_code' => 'required|exists:stations,code',
            'station_b_code' => 'required|exists:stations,code|different:station_a_code',
            'seat_capacity' => 'required|integer|between:1,500',
            'crossing_minutes' => 'required|integer|between:1,120',
            'fare_cny' => 'required|numeric|between:0,999.99',
            'status' => 'nullable|in:active,suspended'
        ]);

        $line = Line::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'station_a_id' => Station::where('code', $validated['station_a_code'])->value('id'),
            'station_b_id' => Station::where('code', $validated['station_b_code'])->value('id'),
            'seat_capacity' => $validated['seat_capacity'],
            'crossing_minutes' => $validated['crossing_minutes'],
            'fare_cny' => $validated['fare_cny'],
            'status' => $validated['status'] ?? 'active',
        ]);

        return LineResource::make($line)->response()->setStatusCode(201);
    }

    public function lineUpdate(Request $request, Line $line)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'station_a_code' => 'required|exists:stations,code',
            'station_b_code' => 'required|exists:stations,code|different:station_a_code',
            'seat_capacity' => 'required|integer|between:1,500',
            'crossing_minutes' => 'required|integer|between:1,120',
            'fare_cny' => 'required|numeric|between:0,999.99',
            'status' => 'nullable|in:active,suspended'
        ]);

        $maxBooked = Booking::where('line_id', $line->id)
            ->where('status', 'confirmed')
            ->whereRaw('TIMESTAMP(departure_date, departure_time) > NOW()')
            ->groupBy('departure_code')
            ->selectRaw('SUM(seats) as total')
            ->orderByDesc('total')
            ->value('total');

        if ($validated['seat_capacity'] < (int)$maxBooked) {
            return response()->json([
                'message' => 'Capacity is lower than seats already booked'
            ], 422);
        }

        $line->update([
            'name' => $validated['name'],
            'station_a_id' => Station::where('code', $validated['station_a_code'])->value('id'),
            'station_b_id' => Station::where('code', $validated['station_b_code'])->value('id'),
            'seat_capacity' => $validated['seat_capacity'],
            'crossing_minutes' => $validated['crossing_minutes'],
            'fare_cny' => $validated['fare_cny'],
            'status' => $validated['status'] ?? 'active',
        ]);

        return LineResource::make($line);
    }

    public function serviceWindowStore(Request $request, Line $line)
    {
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'interval_minutes' => "required|integer|between:{$line->crossing_minutes},120"
        ]);

        $overlaps = $line->serviceWindows()
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($overlaps) {
            return response()->json([
                'message' => 'Service window overlaps an existing window'
            ], 422);
        }

        $line->serviceWindows()->create($validated);

        return response()->json([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'interval_minutes' => (int)$validated['interval_minutes'],
        ], 201);
    }

    public function serviceWindowDestroy(Line $line, string $startTime)
    {
        $window = $line->serviceWindows()->where('start_time', $startTime)->first();

        if (!$window) {
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        }

        $window->delete();

        return response()->json(['message' => 'Service window deleted']);
    }

    public function bookings(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
            'line_code' => 'nullable|exists:lines,code',
            'status' => 'nullable|in:confirmed,cancelled',
            'search' => 'nullable|string',
            'page' => 'nullable|integer|min:1'
        ]);

        $bookings = Booking::query()
            ->where('departure_date', $validated['date'] ?? today()->toDateString())
            ->when($validated['line_code'] ?? null, fn($query, $lineCode) => $query->whereRelation('line', 'code', $lineCode))
            ->when($validated['status'] ?? null, fn($query, $status) => $query->where('status', $status))
            ->when($validated['search'] ?? null, fn($query, $term) => $query->where(fn($sub) => $sub->where('booking_code', 'like', "%{$term}%")
                ->orWhere('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
            )
            )
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->orderBy('booking_code')
            ->paginate(15);

        return response()->json([
            'data' => BookingResource::collection($bookings->items()),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total()
            ]
        ]);
    }

    public function cancelBooking(Request $request, $code)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:200'
        ]);

        $departure = $this->resolveDeparture($code);

        if (!$departure) return response()->json([
            'message' => 'Resource not found'
        ], 404);
        if ($departure['status'] === 'cancelled') {
            return response()->json([
                'message' => 'Departure is already cancelled'
            ], 422);
        }
        if ($departure['departs_at']->lte(now())) {
            return response()->json([
                'message' => 'Departure has already departed'
            ], 422);
        }

        CancelledDeparture::create([
            'departure_code' => $code,
            'line_id' => $departure['line']->id,
            'station_id' => Station::where('code', $departure['origin']['code'])->value('id'),
            'departure_date' => $departure['departure_date'],
            'departure_time' => $departure['departure_time'],
            'reason' => $validated['reason'] ?? null,
            'cancelled_at' => now()
        ]);

        $affected = Booking::where('departure_code', $code)
            ->where('status', 'confirmed')
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

        return response()->json(['data' => ['affected_bookings' => $affected]]);
    }
}
