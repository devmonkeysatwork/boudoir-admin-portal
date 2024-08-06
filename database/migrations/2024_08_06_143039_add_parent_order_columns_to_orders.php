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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('parentOrder')->nullable();
            $table->enum('orderType',[\App\Models\Orders::parentType,\App\Models\Orders::singleType,\App\Models\Orders::childType]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('parentOrder');
            $table->dropColumn('orderType');
        });
    }
};
