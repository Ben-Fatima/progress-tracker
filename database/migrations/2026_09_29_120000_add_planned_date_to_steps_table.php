<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * planned_date is when you intend to WORK on a step, kept separate from
     * due_date (the deadline) so planning never turns tasks overdue.
     */
    public function up(): void
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->date('planned_date')->nullable()->index()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->dropIndex(['planned_date']);
            $table->dropColumn('planned_date');
        });
    }
};
