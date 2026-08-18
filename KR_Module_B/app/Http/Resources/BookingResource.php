<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "booking_code" => $this->booking_code,
            "status" => $this->status,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "phone" => $this->phone,
            "seats" => $this->seats,
            "fare_cny" => $this->fare_cny,
            "total_fare_cny" => number_format($this->seats * $this->fare_cny, 2, '.', ''),
            "departure_code" => $this->departure_code,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "cancelled_at" => $this->cancelled_at
        ];
    }
}
