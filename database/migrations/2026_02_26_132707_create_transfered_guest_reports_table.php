<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfered_guest_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkin_detail_id');
            $table->foreignId('previous_room_id');
            $table->foreignId('new_room_id');
            $table->foreignId('rate_id');
            $table->decimal('previous_amount', 15, 2)->default(0);
            $table->decimal('new_amount', 15, 2)->default(0);
            $table->datetime('original_check_in_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfered_guest_reports');
    }
};
