<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weekly_goals', function (Blueprint $table) {
            $table->id();
            $table->date('week_start');
            $table->json('goals'); // array of goal strings
            $table->json('met')->nullable(); // array of booleans (filled in during next week's review)
            $table->text('biggest_win')->nullable();
            $table->text('biggest_blocker')->nullable();
            $table->text('priority_next_week')->nullable();
            $table->timestamps();

            $table->unique('week_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_goals');
    }
};
