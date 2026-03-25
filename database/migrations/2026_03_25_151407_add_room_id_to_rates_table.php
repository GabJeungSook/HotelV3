<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->after('type_id');
        });

        // Migrate existing data: for each room, copy rates from its type
        $rooms = DB::table('rooms')->get();
        foreach ($rooms as $room) {
            $typeRates = DB::table('rates')
                ->where('type_id', $room->type_id)
                ->where('branch_id', $room->branch_id)
                ->whereNull('room_id')
                ->get();

            foreach ($typeRates as $rate) {
                DB::table('rates')->insert([
                    'branch_id' => $rate->branch_id,
                    'type_id' => $room->type_id,
                    'room_id' => $room->id,
                    'staying_hour_id' => $rate->staying_hour_id,
                    'amount' => $rate->amount,
                    'is_available' => $rate->is_available,
                    'has_discount' => $rate->has_discount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Remove room-specific rates (keep original type-based ones)
        DB::table('rates')->whereNotNull('room_id')->delete();

        Schema::table('rates', function (Blueprint $table) {
            $table->dropColumn('room_id');
        });
    }
};
