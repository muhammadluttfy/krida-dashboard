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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // orderNo
            $table->date('order_date'); // oderDate
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete(); // custId
            $table->decimal('subtotal', 15, 2)->default(0); // subtotal
            $table->decimal('discount_amount', 15, 2)->default(0); // discAmount
            $table->decimal('net_amount', 15, 2)->default(0); // netto
            $table->decimal('dpp_amount', 15, 2)->default(0); // dpp
            $table->decimal('ppn_amount', 15, 2)->default(0); // ppn
            $table->decimal('grand_total', 15, 2)->default(0); // grandtotal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
