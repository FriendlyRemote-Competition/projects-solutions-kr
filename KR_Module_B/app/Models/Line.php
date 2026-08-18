<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Line extends Model
{
    public function stationA(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function stationB(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function serviceWindows(): HasMany
    {
        return $this->hasMany(ServiceWindow::class, 'line_id');
    }
}
