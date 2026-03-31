<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->foreignId('type_id');
            $table->foreignId('staying_hour_id');
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();

            $table->unique(['branch_id', 'type_id', 'staying_hour_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_configurations');
    }
};
