<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LineResource;
use App\Models\Line;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class LineController extends Controller
{
    public function index()
    {
        $lines = Line::latest('code')->get();

        return LineResource::collection($lines);
    }

    public function show(Line $line)
    {
        return new LineResource($line);
    }

    public function timetable(Request $request, Line $line)
    {
        $validated = $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
            'station' => 'nullable|exists:stations,code'
        ]);

        $date = Carbon::parse($validated['date'] ?? today());
        $stationCode = $validated['station'] ?? null;

        if($stationCode && !in_array($stationCode, [$line->stationA->code, $line->stationB->code])) {
            return response()->json([
                'message' => 'Validation failed'
            ], 422);
        }

        $departures = $this->forLineDate($line, $date)
            ->when($stationCode, fn($collection) =>
                $collection->filter(fn($departure) => $departure['origin']['code'] === $stationCode)
            )
            ->map(fn($departure) => Arr::except($departure, ['departs_at', 'line']))
            ->values();

        return response()->json(['data' => $departures]);
    }
}
