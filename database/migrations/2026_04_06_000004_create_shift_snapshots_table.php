<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_session_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained();

            // Header
            $table->string('frontdesk_names', 500)->nullable();
            $table->dateTime('shift_opened_at');
            $table->dateTime('shift_closed_at');

            // Cash Drawer
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('closing_cash', 15, 2)->default(0);

            // Operations A: Sales by type (count + amount)
            $table->integer('checkin_count')->default(0);
            $table->decimal('checkin_amount', 15, 2)->default(0);
            $table->integer('extension_count')->default(0);
            $table->decimal('extension_amount', 15, 2)->default(0);
            $table->integer('transfer_count')->default(0);
            $table->decimal('transfer_amount', 15, 2)->default(0);
            $table->integer('damage_count')->default(0);
            $table->decimal('damage_amount', 15, 2)->default(0);
            $table->integer('amenity_count')->default(0);
            $table->decimal('amenity_amount', 15, 2)->default(0);
            $table->integer('food_count')->default(0);
            $table->decimal('food_amount', 15, 2)->default(0);
            $table->integer('unclaimed_count')->default(0);
            $table->decimal('unclaimed_amount', 15, 2)->default(0);

            // Operations B: Room summary
            $table->integer('forwarded_room_count')->default(0);
            $table->decimal('forwarded_room_amount', 15, 2)->default(0);
            $table->integer('current_room_count')->default(0);
            $table->decimal('current_room_amount', 15, 2)->default(0);

            // Deposits
            $table->decimal('room_deposit_collected', 15, 2)->default(0);
            $table->decimal('guest_deposit_collected', 15, 2)->default(0);
            $table->decimal('cashout_amount', 15, 2)->default(0);
            $table->integer('fwd_room_deposit_count')->default(0);
            $table->decimal('fwd_room_deposit_amount', 15, 2)->default(0);
            $table->integer('fwd_guest_deposit_count')->default(0);
            $table->decimal('fwd_guest_deposit_amount', 15, 2)->default(0);

            // Checkout
            $table->integer('checkout_count')->default(0);
            $table->decimal('checkout_room_deposit', 15, 2)->default(0);

            // Forwarded out (remaining at shift close)
            $table->integer('remaining_room_deposit_count')->default(0);
            $table->decimal('remaining_room_deposit_amount', 15, 2)->default(0);
            $table->integer('remaining_guest_deposit_count')->default(0);
            $table->decimal('remaining_guest_deposit_amount', 15, 2)->default(0);

            // Final Sales
            $table->decimal('gross_sales', 15, 2)->default(0);
            $table->decimal('expenses_amount', 15, 2)->default(0);
            $table->decimal('net_sales', 15, 2)->default(0);

            // Cash Reconciliation
            $table->decimal('forwarded_balance', 15, 2)->default(0);
            $table->decimal('remittance_amount', 15, 2)->default(0);
            $table->decimal('expected_cash', 15, 2)->default(0);
            $table->decimal('actual_cash', 15, 2)->default(0);
            $table->decimal('cash_difference', 15, 2)->default(0);

            // BigBoss floor breakdown
            $table->json('floor_summary')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_snapshots');
    }
};
