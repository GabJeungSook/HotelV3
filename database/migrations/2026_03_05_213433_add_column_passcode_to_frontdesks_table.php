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
        Schema::table('frontdesks', function (Blueprint $table) {
            $table->string('passcode')->nullable()->after('number')->default('12345');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('frontdesks', function (Blueprint $table) {
            $table->dropColumn('passcode');
        });
    }
};
