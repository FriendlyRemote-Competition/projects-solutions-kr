<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $casts = [
        'cancelled_at' => 'datetime'
    ];

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
