<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A step can now be worked on across multiple days. Each row is one
     * planned day; `minutes` optionally overrides the even split of the
     * step's estimate across its plan days.
     */
    public function up(): void
    {
        Schema::create('step_plan_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedSmallInteger('minutes')->nullable();
            $table->timestamps();
            $table->unique(['step_id', 'date']);
            $table->index('date');
        });

        // Carry over anything already planned before dropping the column.
        DB::table('steps')->whereNotNull('planned_date')->get()->each(function ($step) {
            DB::table('step_plan_days')->insert([
                'step_id' => $step->id,
                'date' => $step->planned_date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Schema::table('steps', function (Blueprint $table) {
            $table->dropIndex(['planned_date']);
            $table->dropColumn('planned_date');
        });
    }

    public function down(): void
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->date('planned_date')->nullable()->index()->after('due_date');
        });

        DB::table('step_plan_days')->orderBy('date')->get()
            ->groupBy('step_id')
            ->each(function ($days, $stepId) {
                DB::table('steps')->where('id', $stepId)
                    ->update(['planned_date' => $days->first()->date]);
            });

        Schema::dropIfExists('step_plan_days');
    }
};
