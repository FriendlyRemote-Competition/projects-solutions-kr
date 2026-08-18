<?php

namespace Database\Seeders;

use App\Models\CancelledDeparture;
use App\Models\Line;
use App\Models\Station;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $adminCsv = public_path('data/admins.csv');

        if(($handle = fopen($adminCsv, 'r')) !== false) {
            $headerCheck = false;

            while(($data = fgetcsv($handle, escape: '')) !== false) {
                if($headerCheck) {
                    User::create([
                        'email' => $data[0],
                        'password' => $data[1],
                        'name' => $data[2],
                        'role' => $data[3],
                        'is_active' => $data[4],
                    ]);
                }

                $headerCheck = true;
            }
        }

        $stationCsv = public_path('data/stations.csv');

        if(($handle = fopen($stationCsv, 'r')) !== false) {
            $headerCheck = false;

            while(($data = fgetcsv($handle, escape: '')) !== false) {
                if($headerCheck) {
                    Station::create([
                        'code' => $data[0],
                        'name' => $data[1],
                        'bank' => $data[2],
                        'district' => $data[3],
                        'address' => $data[4],
                    ]);
                }

                $headerCheck = true;
            }
        }

        $lineCsv = public_path('data/lines.csv');

        if(($handle = fopen($lineCsv, 'r')) !== false) {
            $headerCheck = false;

            while(($data = fgetcsv($handle, escape: '')) !== false) {
                if($headerCheck) {
                    $line = Line::firstOrCreate([
                        'code' => $data[0]
                    ], [
                        'name' => $data[1],
                        'status' => $data[2],
                        'station_a_id' => Station::where('code', $data[3])->value('id'),
                        'station_b_id' => Station::where('code', $data[5])->value('id'),
                        'seat_capacity' => $data[7],
                        'crossing_minutes' => $data[8],
                        'fare_cny' => $data[9],
                    ]);

                    $line->serviceWindows()->create([
                        'start_time' => $data[10],
                        'end_time' => $data[11],
                        'interval_minutes' => $data[12]
                    ]);
                }

                $headerCheck = true;
            }
        }

        $cancelledCsv = public_path('data/cancelled_departures.csv');

        if(($handle = fopen($cancelledCsv, 'r')) !== false) {
            $headerCheck = false;

            while(($data = fgetcsv($handle, escape: '')) !== false) {
                if($headerCheck) {
                    CancelledDeparture::create([
                        'departure_code' => sprintf(
                            '%s-%s-%s-%s',
                            $data[0],
                            str_replace('-', '', $data[1]),
                            str_replace(':', '', $data[2]),
                            $data[3]
                        ),
                        'line_id' => Line::where('code', $data[0])->value('id'),
                        'station_id' => Station::where('code', $data[3])->value('id'),
                        'departure_date' => $data[1],
                        'departure_time' => $data[2],
                        'reason' => $data[4],
                        'cancelled_at' => $data[5]
                    ]);
                }

                $headerCheck = true;
            }
        }
    }
}
