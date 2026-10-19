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
        Schema::table('focus_sessions', function (Blueprint $table) {
            $table->timestamp('paused_at')->nullable()->after('started_at');
            $table->integer('paused_seconds')->default(0)->after('ended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('focus_sessions', function (Blueprint $table) {
            $table->dropColumn(['paused_at', 'paused_seconds']);
        });
    }
};
