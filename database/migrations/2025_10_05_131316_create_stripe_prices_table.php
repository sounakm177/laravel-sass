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
        Schema::create('stripe_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('stripe_products')->onDelete('cascade');
            $table->string('stripe_price_id')->unique();
            $table->integer('unit_amount');
            $table->string('currency', 10)->default('usd');
            $table->string('interval')->default('month'); // month, year, etc.
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_prices');
    }
};
