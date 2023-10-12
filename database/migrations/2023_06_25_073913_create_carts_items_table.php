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
        Schema::create('shopping_carts_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id')->nullable(false);
            $table->unsignedInteger('product')->nullable(false);
            $table->string('name')->nullable(false);
            $table->decimal('price', unsigned: true)->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();

            $table->index('cart_id', 'idx-shopping_carts_items-cart_id');
            $table->foreign('cart_id', 'fk-shopping_carts_items-cart_id')
                ->references('id')
                ->on('shopping_carts')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_carts_items');
    }
};
