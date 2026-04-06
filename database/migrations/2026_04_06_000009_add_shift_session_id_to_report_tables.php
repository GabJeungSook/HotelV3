<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'new_guest_reports',
            'check_out_guest_reports',
            'extended_guest_reports',
            'room_boy_reports',
            'transfered_guest_reports',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('shift_session_id')->nullable()->constrained('shift_sessions')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'new_guest_reports',
            'check_out_guest_reports',
            'extended_guest_reports',
            'room_boy_reports',
            'transfered_guest_reports',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('shift_session_id');
                });
            }
        }
    }
};
