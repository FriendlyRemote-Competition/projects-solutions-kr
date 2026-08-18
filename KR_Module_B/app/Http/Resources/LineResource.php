<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "code" => $this->code,
            "name" => $this->name,
            "status" => $this->status,
            "station_a" => ["code" => $this->stationA->code, "name" => $this->stationA->name],
            "station_b" => ["code" => $this->stationA->code, "name" => $this->stationA->name],
            "seat_capacity" => $this->seat_capacity,
            "crossing_minutes" => $this->crossing_minutes,
            "fare_cny" => $this->fare_cny,
            "service_windows" => $this->serviceWindows()->orderBy('start_time')->get()->map(fn($s) => ["start_time" => substr($s->start_time, 0, 5), "end_time" => substr($s->end_time, 0, 5), "interval_minutes" => $s->interval_minutes]
            )
        ];
    }
}
