<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'departure_code' => 'required|string',
            'phone' => 'nullable|string',
            'first_name' => 'required|string|max:60',
            'last_name' => 'required|string|max:60',
            'email' => 'required|email',
            'seats' => 'required|integer|between:1,16',
        ]);

        $departure = $this->resolveDeparture($validated['departure_code']);

        if (!$departure) {
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        }

        if ($departure['seats_available'] < $validated['seats']) {
            throw ValidationException::withMessages(['seats' => 'Not enough seats available']);
        }

        do {
            $bookingCode = 'HPF-' . Str::upper(Str::random(6));
        } while (Booking::where('booking_code', $bookingCode)->exists());

        $booking = Booking::create([
            ...$validated,
            'booking_code' => $bookingCode,
            'line_id' => $departure['line']->id,
            'station_id' => Station::where('code', $departure['origin']['code'])->value('id'),
            'departure_date' => $departure['departure_date'],
            'departure_time' => $departure['departure_time'],
            'fare_cny' => $departure['line']->fare_cny,
            'status' => 'confirmed'
        ]);

        return BookingResource::make($booking)->response()->setStatusCode(201);
    }

    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'booking_code' => 'required|string',
            'first_name' => 'required|string|max:60',
            'last_name' => 'required|string|max:60',
        ]);

        return BookingResource::make($this->findBookingOrFail($validated['booking_code'], $validated));
    }

    public function update(Request $request, $code)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:60',
            'last_name' => 'required|string|max:60',
            'seats' => 'required|integer|between:1,16'
        ]);

        $booking = $this->findBookingOrFail($code, $validated);

        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Booking is already cancelled'
            ], 422);
        }

        $departure = $this->resolveDeparture($booking->departure_code);

        if ($departure) {
            $available = $departure['seats_available'] + $booking->seats;

            if ($available < $validated['seats']) {
                throw ValidationException::withMessages(['seats' => 'Not enough seats available.']);
            }
        }

        $booking->update(['seats' => $validated['seats']]);

        return BookingResource::make($booking);
    }

    public function cancel(Request $request, $code)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:60',
            'last_name' => 'required|string|max:60',
        ]);

        $booking = $this->findBookingOrFail($code, $validated);

        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Booking is already cancelled'
            ], 422);
        }

        $departsAt = Carbon::parse("{$booking->departure_date} {$booking->departure_time}");

        if(now()->gte($departsAt->copy()->subMinutes(5))) {
            return response()->json([
                'message' => 'Booking closed for this departure'
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return BookingResource::make($booking);
    }
}
