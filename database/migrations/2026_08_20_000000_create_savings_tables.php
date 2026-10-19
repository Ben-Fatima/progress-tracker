<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_goals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_amount', 12, 2)->default(0);
            $table->string('color')->default('#7C3AED');
            $table->boolean('is_active')->default(true);
            $table->date('target_date')->nullable();
            $table->timestamps();
        });

        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('savings_goal_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2); // positive = deposit, negative = withdrawal
            $table->string('type'); // deposit or withdrawal
            $table->date('date');
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index(['savings_goal_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_transactions');
        Schema::dropIfExists('savings_goals');
    }
};
