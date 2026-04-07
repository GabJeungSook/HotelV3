<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rate;
use App\Models\Room;
use App\Models\StayingHour;
use App\Models\ExtensionRate;

class RateSeeder extends Seeder
{
    public function run()
    {
        // Amount mapping: type_id => [staying_hour_id => amount]
        // staying_hour_id: 1=6h, 2=12h, 4=24h (no rates for 18h, matching V2)
        $amounts = [
            1 => [1 => 224, 2 => 336, 4 => 560],   // Single size Bed
            2 => [1 => 280, 2 => 392, 4 => 616],   // Double size Bed
            3 => [1 => 336, 2 => 448, 4 => 672],   // Twin size Bed
        ];

        $rooms = Room::where('branch_id', 1)->get();

        foreach ($rooms as $room) {
            if (!isset($amounts[$room->type_id])) {
                continue;
            }

            foreach ($amounts[$room->type_id] as $stayingHourId => $amount) {
                Rate::create([
                    'branch_id' => 1,
                    'room_id' => $room->id,
                    'staying_hour_id' => $stayingHourId,
                    'type_id' => $room->type_id,
                    'amount' => $amount,
                    'is_available' => 1,
                ]);
            }
        }

        // Extension rates for Branch 1
        ExtensionRate::create(['branch_id' => 1, 'hour' => 6, 'amount' => 112]);
        ExtensionRate::create(['branch_id' => 1, 'hour' => 12, 'amount' => 224]);
        ExtensionRate::create(['branch_id' => 1, 'hour' => 18, 'amount' => 336]);
        ExtensionRate::create(['branch_id' => 1, 'hour' => 24, 'amount' => 448]);
    }
}
