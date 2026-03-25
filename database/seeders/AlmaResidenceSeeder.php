<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StayingHour;
use App\Models\Type;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Rate;
use App\Models\HotelItems;
use App\Models\RequestableItem;

class AlmaResidenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StayingHour::create([
            'branch_id' => 1,
            'number' => 6,
        ]);
        StayingHour::create([
            'branch_id' => 1,
            'number' => 12,
        ]);
        StayingHour::create([
            'branch_id' => 1,
            'number' => 18,
        ]);
        StayingHour::create([
            'branch_id' => 1,
            'number' => 24,
        ]);

        $single = Type::create([
            'branch_id' => 1,
            'name' => 'Single size Bed',
        ]);
        $double = Type::create([
            'branch_id' => 1,
            'name' => ' Double size Bed',
        ]);
        $twin = Type::create([
            'branch_id' => 1,
            'name' => 'Twin size Bed',
        ]);

        $firstFloor = Floor::create([
            'branch_id' => 1,
            'number' => '1',
        ]);
        $secondFloor = Floor::create([
            'branch_id' => 1,
            'number' => '2',
        ]);
        $thirdFloor = Floor::create([
            'branch_id' => 1,
            'number' => '3',
        ]);
        $fourthFloor = Floor::create([
            'branch_id' => 1,
            'number' => '4',
        ]);
        $fifthFloor = Floor::create([
            'branch_id' => 1,
            'number' => '5',
        ]);

        Room::create([ 'branch_id' => 1, 'number' => 1, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 2, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 3, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 4, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 5, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 6, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 7, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 8, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 9, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 10, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 11, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 12, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 14, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 15, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 16, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 17, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 18, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 19, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 20, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 21, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 22, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 23, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 24, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 25, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 26, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 27, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 28, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 29, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 30, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 31, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 32, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 33, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 34, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 35, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 36, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 37, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 38, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 39, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 50, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 51, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 52, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 53, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $firstFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 60, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 61, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 62, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 63, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 64, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 65, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 66, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 67, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 68, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 69, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 70, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 71, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 72, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 73, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 74, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 75, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 76, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 77, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 78, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 79, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 80, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 81, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 82, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 83, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 84, 'type_id' => $twin->id,
'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 85, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 86, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 87, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 88, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 89, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 90, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 91, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 92, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 93, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 94, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 95, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 96, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 97, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 98, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 99, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 100, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 101, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $secondFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 120, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 121, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 122, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 123, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 124, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 125, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 126, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 127, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 128, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 129, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 130, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 131, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 132, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 133, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 134, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 135, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 136, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 137, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 138, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 139, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 150, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 151, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 152, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 153, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 154, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 155, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 156, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 157, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 158, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 159, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 160, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 161, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 162, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 163, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 164, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 165, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 166, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 167, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 168, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 169, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 170, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 171, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '3A', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '3B', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '3C', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '3D', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '3E', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '3F', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '3G', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $thirdFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 200, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 201, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 202, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 203, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 204, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 205, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 206, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 207, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 208, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 209, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 210, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 211, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 212, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 214, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 215, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 216, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 217, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 218, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 219, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 220, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 221, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 222, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 223, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 224, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 225, 'type_id' =>
$twin->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 226, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 227, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 228, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 229, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 230, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 231, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 232, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 233, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 234, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 235, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 236, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 237, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 238, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 239, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 250, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 251, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 252, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '4A', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '4B', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '4C', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '4D', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '4E', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '4F', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '4G', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fourthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 253, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 254, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 255, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 256, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 257, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 258, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 259, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 260, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 261, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 262, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 263, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 264, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 265, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 266, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 267, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 268, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 269, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 270, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 271, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 272, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 273, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 274, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 275, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 276, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 277, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 278, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 279, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 280, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 281, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 282, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 283, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 284, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 285, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 286, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 287, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 288, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 289, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 290, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 291, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 292, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 293, 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => 294, 'type_id' =>
$double->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '5A', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '5B', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '5C', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '5D', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '5E', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '5F', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);

Room::create([ 'branch_id' => 1, 'number' => '5G', 'type_id' =>
$single->id, 'status' => 'Available', 'floor_id' => $fifthFloor->id,]);


        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 1,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 10,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 11,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 12,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 14,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 15,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 16,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 17,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 18,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 19,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 2,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 20,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 21,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 22,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 23,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 24,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 25,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 26,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 27,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 28,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 29,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 3,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 30,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 31,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 32,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 33,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 34,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 35,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 36,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 37,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 38,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 39,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 4,
        //     'type_id' => $single->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 5,
        //     'type_id' => $single->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 50,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 51,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 52,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 53,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 6,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 7,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 8,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 9,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $firstFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 100,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 101,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 60,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 61,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 62,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 63,
        //     'type_id' => $single->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 64,
        //     'type_id' => $single->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 65,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 66,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 67,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 68,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 69,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 70,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 71,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 72,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 73,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 74,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 75,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 76,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 77,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 78,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 79,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 80,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 81,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 82,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 83,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 84,
        //     'type_id' => $twin->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 85,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 86,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 87,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 88,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 89,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'number' => 90,
        //     'type_id' => $double->id,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 91,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 92,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 93,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 94,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 95,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 96,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 97,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 98,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 99,
        //     'status' => 'Available',
        //     'floor_id' => $secondFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 120,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 121,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 122,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $single->id,
        //     'number' => 123,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $single->id,
        //     'number' => 124,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 125,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 126,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 127,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 128,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 129,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 130,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 131,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 132,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 133,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 134,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 135,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 136,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 137,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 138,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 139,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 150,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 151,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 152,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 153,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 154,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 155,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 156,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 157,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 158,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 159,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 160,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 161,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 162,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 163,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 164,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 165,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 166,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 167,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 168,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 169,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 170,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 171,
        //     'status' => 'Available',
        //     'floor_id' => $thirdFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 200,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 201,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 202,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $single->id,
        //     'number' => 203,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $single->id,
        //     'number' => 204,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 205,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 206,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 207,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 208,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 209,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 210,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 211,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 212,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 214,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 215,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 216,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 217,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 218,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 219,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 220,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 221,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 222,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 223,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 224,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $twin->id,
        //     'number' => 225,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 226,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 227,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 228,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 229,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 230,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 231,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 232,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 233,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 234,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 235,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 236,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 237,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 238,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 239,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 250,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 251,
        //     'status' => 'Available',
        //     'floor_id' => $fourthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 253,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 254,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 255,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $single->id,
        //     'number' => 256,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $single->id,
        //     'number' => 257,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 258,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 259,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 260,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 261,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 262,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 263,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 264,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 265,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 266,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 267,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 268,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 269,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 270,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 271,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 272,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 273,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 274,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 275,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 276,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 277,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 278,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 279,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 280,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 281,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 282,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 283,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 284,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 285,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 286,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 287,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 288,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 289,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $single->id,
        //     'number' => 290,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        // Room::create([
        //     'branch_id' => 1,
        //     'type_id' => $double->id,
        //     'number' => 294,
        //     'status' => 'Available',
        //     'floor_id' => $fifthFloor->id,
        // ]);

        HotelItems::create([
            'branch_id' => 1,
            'name' => 'MAIN DOOR',
            'price' => 5000,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'PURTAHAN SA C.R.',
            'price' => 2500,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SUGA SA ROOM',
            'price' => 150,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SUGA SA C.R.',
            'price' => 130,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SAMIN SULOD SA ROOM',
            'price' => 1000,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SAMIN SULOD SA C.R.',
            'price' => 1000,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SAMIN SA GAWAS',
            'price' => 1500,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SALOG SA ROOM PER TILES',
            'price' => 1200,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SALOG SA C.R. PER TILE',
            'price' => 1200,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'RUG/ TRAPO SA SALOG',
            'price' => 40,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'UNLAN',
            'price' => 500,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'HABOL',
            'price' => 500,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'PUNDA',
            'price' => 200,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'PUNDA WITH MANTSA(HAIR DYE)',
            'price' => 500,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'BEDSHEET WITH INK',
            'price' => 500,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'BED PAD WITH INK',
            'price' => 800,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'BED SKIRT WITH INK',
            'price' => 1500,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'TOWEL',
            'price' => 300,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'DOORKNOB C.R.',
            'price' => 350,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'MAIN DOOR DOORKNOB',
            'price' => 500,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'T.V.',
            'price' => 30000,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'TELEPHONE',
            'price' => 1000,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'DECODER PARA SA CABLE',
            'price' => 1600,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'CORD SA DECODER',
            'price' => 100,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'CHARGER SA DECODER',
            'price' => 400,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'WIRING SA TELEPHONE',
            'price' => 100,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'WIRINGS SA T.V.',
            'price' => 200,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'WIRING SA DECODER',
            'price' => 50,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'CEILING',
            'price' => 0,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SHOWER HEAD',
            'price' => 800,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SHOWER BULB',
            'price' => 800,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'BIDET',
            'price' => 400,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'HINGES/ TOWEL BAR',
            'price' => 200,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'TAKLOB SA TANGKE',
            'price' => 1200,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'TANGKE SA BOWL',
            'price' => 3000,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'TAKLOB SA BOWL',
            'price' => 1000,
        ]);

        HotelItems::create([
            'branch_id' => 1,
            'name' => 'ILALOM SA LABABO',
            'price' => 0,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'SINK/LABABO',
            'price' => 1500,
        ]);
        HotelItems::create([
            'branch_id' => 1,
            'name' => 'BASURAHAN',
            'price' => 70,
        ]);
        RequestableItem::create([
            'branch_id' => 1,
            'name' => 'EXTRA PERSON WITH FREE PILLOW, BLANKET,TOWEL',
            'price' => '100',
        ]);
        RequestableItem::create([
            'branch_id' => 1,
            'name' => 'EXTRA PILLOW',
            'price' => '20',
        ]);
        RequestableItem::create([
            'branch_id' => 1,
            'name' => 'EXTRA TOWEL',
            'price' => '20',
        ]);
        RequestableItem::create([
            'branch_id' => 1,
            'name' => 'EXTRA BLANKET',
            'price' => '20',
        ]);
        RequestableItem::create([
            'branch_id' => 1,
            'name' => 'EXTRA FITTED SHEET',
            'price' => '20',
        ]);
    }
}
