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
        Schema::create('frontdesk_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('raw_file')->nullable();
            $table->string('frontdesk_outgoing')->nullable();
            $table->string('frontdesk_incoming')->nullable();
            $table->timestamp('shift_opened')->nullable();
            $table->timestamp('shift_closed')->nullable();
            //cash drawer
            $table->string('opening_cash_amount')->nullable();
            $table->string('opening_cash_sub_amount')->nullable();
            $table->string('opening_cash_remark')->nullable();
            
            $table->string('key_amount')->nullable();
            $table->string('key_sub_amount')->nullable();
            $table->string('key_remarks')->nullable();
            
            $table->string('guest_deposit_amount')->nullable();
            $table->string('guest_deposit_sub_amount')->nullable();
            $table->string('guest_deposit_amount_remark')->nullable();
            
            $table->string('forwarding_balance_amount')->nullable();
            $table->string('forwarding_balance_sub_amount')->nullable();
            $table->string('forwarding_balance_remark')->nullable();
            
            $table->string('total_cash_amount')->nullable();
            $table->string('total_cash_sub_amount')->nullable();
            $table->string('total_cash_remark')->nullable();

            //frontdesk operation a 
            $table->string('new_check_in_number')->nullable();
            $table->string('new_check_in_amount')->nullable();
            $table->string('extension_number')->nullable();
            $table->string('extension_amount')->nullable();
            $table->string('transfer_number')->nullable();
            $table->string('transfer_amount')->nullable();
            $table->string('miscellaneous_number')->nullable();
            $table->string('miscellaneous_amount')->nullable();
            $table->string('food_number')->nullable();
            $table->string('food_amount')->nullable();
            $table->string('drink_number')->nullable();
            $table->string('drink_amount')->nullable();
            $table->string('other_number')->nullable();
            $table->string('other_amount')->nullable();
            $table->string('total_number')->nullable();
            $table->string('total_amount')->nullable();

            //frontdesk operation b
            $table->string('forwarded_room_check_in_number')->nullable();
            $table->string('forwarded_room_check_in_amount')->nullable();
            $table->string('key_remote_number')->nullable();
            $table->string('key_remote_amount')->nullable();
            $table->string('forwarded_guest_deposit_number')->nullable();
            $table->string('forwarded_guest_deposit_amount')->nullable();
            $table->string('current_guest_deposit_number')->nullable();
            $table->string('current_guest_deposit_amount')->nullable();
            $table->string('total_check_out_number')->nullable();
            $table->string('total_check_out_amount')->nullable();
            $table->string('expenses_number')->nullable();
            $table->string('expenses_amount')->nullable();

             //final sales
            $table->string('gross_sales')->nullable();
            $table->string('refund')->nullable();
            $table->string('expenses')->nullable();
            $table->string('discount')->nullable();
            $table->string('net_sales')->nullable();

            
            //cash position summary
            $table->string('opening_cash')->nullable();
            $table->string('forwarded_balance')->nullable();
            $table->string('cash_net_sales')->nullable();
            $table->string('remittance')->nullable();            

            //cash reconcillation
            $table->string('expected_cash')->nullable();
            $table->string('actual_cash')->nullable();
            $table->string('difference')->nullable();

           
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
        Schema::dropIfExists('frontdesk_shifts');
    }
};
