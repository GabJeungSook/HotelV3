<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shift_members', function (Blueprint $table) {
            $table->decimal('cash_at_leave', 12, 2)->nullable()->after('left_at');
        });
    }

    public function down(): void
    {
        Schema::table('shift_members', function (Blueprint $table) {
            $table->dropColumn('cash_at_leave');
        });
    }
};
