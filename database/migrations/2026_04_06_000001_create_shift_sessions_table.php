<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('cash_drawer_id')->constrained();
            $table->enum('shift_type', ['AM', 'PM']);
            $table->date('shift_date');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('closing_cash', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['branch_id', 'cash_drawer_id', 'shift_date', 'shift_type'], 'shift_sessions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_sessions');
    }
};
