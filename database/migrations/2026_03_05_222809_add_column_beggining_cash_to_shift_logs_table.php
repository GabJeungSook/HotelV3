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
            $table->decimal('beginning_cash', 15, 2)->default(0)->after('cash_drawer_id');
            $table->decimal('end_cash', 15, 2)->default(0)->after('beginning_cash');
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
            $table->dropColumn('beginning_cash');
            $table->dropColumn('end_cash');
        });
    }
};
