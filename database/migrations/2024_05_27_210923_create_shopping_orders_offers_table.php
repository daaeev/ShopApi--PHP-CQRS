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
        Schema::create('shopping_orders_offers', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable(false)->unique();
            $table->bigInteger('order_id', unsigned: true)->nullable(false);
            $table->bigInteger('product_id', unsigned: true)->nullable(false);
            $table->string('product_name')->nullable(false);
            $table->unsignedDecimal('price')->nullable(false);
            $table->unsignedDecimal('regular_price')->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
            $table->string('size')->nullable();
            $table->string('color')->nullable();

            $table->index('order_id', 'idx-shopping_orders_offers-order_id');
            $table->foreign('order_id', 'fk-shopping_orders_offers-order_id')
                ->references('id')
                ->on('shopping_orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_orders_offers');
    }
};
