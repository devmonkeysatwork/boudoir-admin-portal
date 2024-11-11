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
        Schema::table('workstations', function (Blueprint $table) {
            $table->dropColumn(['workstation_number', 'num_orders']);
            $table->unsignedBigInteger('status_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workstations', function (Blueprint $table) {
            $table->string('workstation_number');
            $table->string('num_orders')->default(0);
            $table->dropColumn('status_id');
        });
    }
};
