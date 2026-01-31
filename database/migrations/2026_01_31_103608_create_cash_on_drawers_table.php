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
        Schema::create('cash_on_drawers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('frontdesk_id');
            $table->unsignedBigInteger('cash_drawer_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('transaction_date');
            $table->string('transaction_type'); // e.g., 'initial', 'top-up', 'withdrawal'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_on_drawers');
    }
};
