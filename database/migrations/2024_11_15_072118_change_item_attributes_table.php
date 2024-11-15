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
        Schema::table('item_attributes', function (Blueprint $table) {
          $table->unsignedBigInteger('attribute_id')->nullable();
          $table->unsignedBigInteger('attribute_value_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_attributes', function (Blueprint $table) {
            $table->dropColumn('attribute_id');
            $table->dropColumn('attribute_value_id');
        });
    }
};
