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
        Schema::table('order_logs', function (Blueprint $table) {
            $table->string('time_end',20)->nullable()->after('time_started');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropColumn('time_end');
        });
    }
};
