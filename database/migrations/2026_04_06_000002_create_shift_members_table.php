<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->enum('role', ['primary', 'partner'])->default('primary');
            $table->dateTime('joined_at');
            $table->dateTime('left_at')->nullable();
            $table->timestamps();

            $table->unique(['shift_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_members');
    }
};
