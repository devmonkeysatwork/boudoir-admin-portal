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
        Schema::create('timeline_pool', function (Blueprint $table) {
            $table->id();
            $table->string('item');
            $table->string('attribute')->nullable();
            $table->string('attribute_value')->nullable();
            $table->integer('days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timeline_pool');
    }
};
