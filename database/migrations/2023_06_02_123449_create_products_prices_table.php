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
        Schema::create('products_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable(false);
            $table->string('currency', 15)->nullable(false);
            $table->unsignedDecimal('price')->nullable(false);

            $table->foreign('product_id', 'fk-products_prices-product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_prices');
    }
};
