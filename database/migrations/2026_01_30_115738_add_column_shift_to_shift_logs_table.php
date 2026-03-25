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
        Schema::table('shift_logs', function (Blueprint $table) {
            $table->string('shift')->nullable()->after('frontdesk_ids');
            $table->unsignedBigInteger('cash_drawer_id')->nullable()->after('id');
        });
        
        Schema::table('assigned_frontdesks', function (Blueprint $table) {
            $table->string('shift')->nullable()->after('frontdesk_id');
            $table->unsignedBigInteger('cash_drawer_id')->nullable()->after('branch_id');
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
            $table->dropColumn('shift');
            $table->dropColumn('cash_drawer_id');
        });

        Schema::table('assigned_frontdesks', function (Blueprint $table) {
            $table->dropColumn('shift');
            $table->dropColumn('cash_drawer_id');
        });
    }
};
