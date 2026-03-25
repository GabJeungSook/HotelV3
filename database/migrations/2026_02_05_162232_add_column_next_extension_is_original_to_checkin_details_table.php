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
        Schema::table('checkin_details', function (Blueprint $table) {
            $table->boolean('next_extension_is_original')->default(false)->after('number_of_hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkin_details', function (Blueprint $table) {
            $table->dropColumn('next_extension_is_original');
        });
    }
};
