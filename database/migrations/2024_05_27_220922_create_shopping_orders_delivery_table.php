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
        Schema::create('shopping_orders_delivery', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id', unsigned: true)->nullable(false)->unique();
            $table->string('service')->nullable(false);
            $table->string('country')->nullable(false);
            $table->string('city')->nullable(false);
            $table->string('street')->nullable(false);
            $table->string('house_number')->nullable(false);

            $table->index('order_id', 'idx-shopping_orders_delivery-order_id');
            $table->foreign('order_id', 'fk-shopping_orders_delivery-order_id')
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
        Schema::dropIfExists('shopping_orders_delivery');
    }
};
