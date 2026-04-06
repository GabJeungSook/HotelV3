<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_forwarded_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('checkin_detail_id')->constrained('checkin_details');
            $table->foreignId('room_id')->constrained();
            $table->decimal('room_charge_amount', 15, 2)->default(0);
            $table->decimal('room_deposit_amount', 15, 2)->default(0);
            $table->decimal('guest_deposit_balance', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['shift_session_id', 'checkin_detail_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_forwarded_guests');
    }
};
