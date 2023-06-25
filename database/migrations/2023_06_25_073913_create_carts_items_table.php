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
        Schema::create('carts_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id')->nullable(false);
            $table->unsignedInteger('product')->nullable(false);
            $table->string('name')->nullable(false);
            $table->decimal('price', unsigned: true)->nullable(false);
            $table->unsignedInteger('quantity')->nullable(false);
            $table->string('size')->nullable(true);
            $table->string('color')->nullable(true);
            $table->timestamps();

            $table->index('cart_id', 'idx-carts_items-cart_id');
            $table->foreign('cart_id', 'fk-carts_items-cart_id')
                ->references('id')
                ->on('carts')
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts_items');
    }
};
