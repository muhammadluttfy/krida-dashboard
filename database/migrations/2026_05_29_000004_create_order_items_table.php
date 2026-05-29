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
        Schema::create('order_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete(); // orderId
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete(); // itemId
            $table->unsignedInteger('quantity'); // qty
            $table->decimal('unit_price', 15, 2); // price
            $table->decimal('discount_amount', 15, 2)->default(0); // discAmount
            $table->decimal('line_total', 15, 2); // totalItem
            $table->timestamps();

            $table->index(['order_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item');
    }
};
