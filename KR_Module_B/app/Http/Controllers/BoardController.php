<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index()
    {
        return view('board.index', [
            'stations' => Station::orderBy('code')->get()
        ]);
    }

    public function show(Request $request, Station $station)
    {
        $limit = max(1, min(20, (int) $request->input('limit', 8)));

        $departures = $this->nextFrom($station, $limit)
            ->map(fn($departure) => [
                ...$departure,
                'departs_in' => $this->departsIn($departure['departs_at'])
            ]);


        return view('board.show', [
            'station' => $station,
            'departures' => $departures
        ]);
    }
}
