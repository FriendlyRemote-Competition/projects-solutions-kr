<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CancelledDeparture;
use App\Models\Line;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Support\Str;

abstract class Controller
{
    protected function timesFor(Line $line, Carbon $date)
    {
        if ($line->status === 'suspended') return collect();

        return $line->serviceWindows->flatMap(function ($window) use ($date) {
            $times = collect();
            $cursor = $date->copy()->setTimeFromTimeString($window->start_time);
            $end = $date->copy()->setTimeFromTimeString($window->end_time);

            while ($cursor->lte($end)) {
                $times->push($cursor->copy());
                $cursor->addMinutes($window->interval_minutes);
            }
            return $times;
        })->sort()->values();
    }

    protected function forLineDate(Line $line, Carbon $date)
    {
        $line->loadMissing('stationA', 'stationB', 'serviceWindows');

        return $this->timesFor($line, $date)->flatMap(fn($time) => [
            $this->buildDeparture($line, $time, $line->stationA, $line->stationB),
            $this->buildDeparture($line, $time, $line->stationB, $line->stationA),
        ])->sortBy(fn($departure) => $departure['departure_time'] . $departure['origin']['code'])->values();
    }

    protected function resolveDeparture(?string $code)
    {
        $parts = explode('-', (string)$code);
        if (count($parts) !== 4) return null;

        $line = Line::firstWhere('code', $parts[0]);
        $date = rescue(fn() => Carbon::createFromFormat('Ymd', $parts[1])->startOfDay(), null, false);

        if(!$line || !$date) return null;

        return $this->forLineDate($line, $date)->firstWhere('code', $code);
    }

    protected function nextFrom(Station $station, int $limit)
    {
        $lines = Line::with('stationA', 'stationB', 'serviceWindows')
            ->where('status', 'active')
            ->where(fn($query) => $query
                ->where('station_a_id', $station->id)
                ->orWhere('station_b_id', $station->id)
            )->get();

        return collect([today(), today()->addDay()])
            ->flatMap(fn($date) => $lines->flatMap(fn($line) => $this->forLineDate($line, $date)))
            ->filter(fn($departure) => $departure['origin']['code'] === $station->code && $departure['departs_at']->isFuture())
            ->sortBy(fn($departure) => $departure['departs_at'])
            ->take($limit)
            ->values();
    }

    private function buildDeparture(Line $line, Carbon $time, Station $origin, Station $destination)
    {
        $code = sprintf('%s-%s-%s-%s', $line->code, $time->format('Ymd'), $time->format('Hi'), $origin->code);
        $cancellation = CancelledDeparture::firstWhere('departure_code', $code);
        $seatsBooked = (int)Booking::where('departure_code', $code)->where('status', 'confirmed')->sum('seats');

        return [
            "code" => $code,
            "origin" => ["code" => $origin->code, "name" => $origin->name],
            "destination" => ["code" => $destination->code, "name" => $destination->name],
            "departure_date" => $time->toDateString(),
            "departure_time" => $time->format('H:i'),
            "arrival_time" => $time->copy()->addMinutes($line->crossing_minutes)->format('H:i'),
            "seats_booked" => $seatsBooked,
            "seats_available" => $line->seat_capacity - $seatsBooked,
            "fare_cny" => $line->fare_cny,
            "status" => match (true) {
                $cancellation !== null => 'cancelled',
                $time->lte(now()) => 'departed',
                default => 'scheduled'
            },
            "cancellation_reason" => $cancellation?->reason,
            'departs_at' => $time->copy(),
            'line' => $line
        ];
    }

    protected function findBookingOrFail(string $code, array $names)
    {
        $booking = Booking::where('booking_code', $code)
            ->whereRaw('LOWER(TRIM(first_name)) = ?', [Str::lower(trim($names['first_name']))])
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [Str::lower(trim($names['last_name']))])
            ->first();

        abort_if(!$booking, 404, 'Resource not found');

        return $booking;
    }

    protected function departsIn(Carbon $departsAt)
    {
        $minutes = max(0, (int) floor(now()->diffInMinutes($departsAt, false)));
        $hours = intdiv($minutes, 60);
        $rest = $minutes % 60;

        return trim(sprintf(
            '%s %s',
            $hours > 0 ? $hours . ' ' . Str::plural('hour', $hours) : '',
            $rest . ' ' . Str::plural('minute', $rest)
        ));
    }
}
