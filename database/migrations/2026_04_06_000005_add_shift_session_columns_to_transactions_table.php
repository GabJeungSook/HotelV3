<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('shift_session_id')->nullable()->after('shift_log_id')->constrained('shift_sessions')->nullOnDelete();
            $table->foreignId('processed_by_user_id')->nullable()->after('shift_session_id')->constrained('users')->nullOnDelete();
            $table->enum('deposit_type', ['room_key', 'guest'])->nullable()->after('transaction_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shift_session_id');
            $table->dropConstrainedForeignId('processed_by_user_id');
            $table->dropColumn('deposit_type');
        });
    }
};
