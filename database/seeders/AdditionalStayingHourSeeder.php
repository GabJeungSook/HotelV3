<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StayingHour;

class AdditionalStayingHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         StayingHour::create([
            'branch_id' => 2,
            'number' => 6,
        ]);
        StayingHour::create([
            'branch_id' => 2,
            'number' => 12,
        ]);
        StayingHour::create([
            'branch_id' => 2,
            'number' => 18,
        ]);
        StayingHour::create([
            'branch_id' => 2,
            'number' => 24,
        ]);
    }
}
