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
        Schema::table('temporary_check_in_kiosks', function (Blueprint $table) {
            $table->boolean('is_opened')->default(false)->after('terminated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temporary_check_in_kiosks', function (Blueprint $table) {
            $table->dropColumn('is_opened');
        });
    }
};
