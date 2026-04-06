<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkin_details', function (Blueprint $table) {
            $table->decimal('deposit_balance', 15, 2)->default(0)->after('total_deduction');
            $table->decimal('room_deposit_balance', 15, 2)->default(0)->after('deposit_balance');
            $table->foreignId('check_in_shift_session_id')->nullable()->after('room_deposit_balance')->constrained('shift_sessions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('checkin_details', function (Blueprint $table) {
            $table->dropColumn('deposit_balance');
            $table->dropColumn('room_deposit_balance');
            $table->dropConstrainedForeignId('check_in_shift_session_id');
        });
    }
};
