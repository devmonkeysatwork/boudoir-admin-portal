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
            $table->string('payment_method')->nullable();
            $table->string('payment_method_title')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('currency', 3)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('total_discount', 10, 2)->nullable();
            $table->decimal('total_tax', 10, 2)->nullable();
            $table->decimal('shipping_total', 10, 2)->nullable();
            $table->decimal('shipping_tax', 10, 2)->nullable();
            $table->text('customer_note')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_method_title',
                'transaction_id',
                'currency',
                'total_amount',
                'subtotal',
                'total_discount',
                'total_tax',
                'shipping_total',
                'shipping_tax',
                'customer_note',
                'customer_name',
                'customer_email'
            ]);
        });
    }
};
