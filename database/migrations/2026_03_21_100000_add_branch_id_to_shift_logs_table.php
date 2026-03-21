<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Step 1: Add branch_id as nullable
        Schema::table('shift_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');
        });

        // Step 2: Backfill from frontdesk (user) relationship
        DB::statement('
            UPDATE shift_logs
            INNER JOIN users ON users.id = shift_logs.frontdesk_id
            SET shift_logs.branch_id = users.branch_id
            WHERE shift_logs.frontdesk_id IS NOT NULL
        ');

        // Step 3: Add foreign key constraint
        Schema::table('shift_logs', function (Blueprint $table) {
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_logs', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
