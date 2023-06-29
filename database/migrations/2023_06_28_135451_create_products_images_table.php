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
        Schema::create('products_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product')->nullable(false);
            $table->string('image')->nullable(false);
            $table->string('disk')->nullable(false);
            $table->boolean('is_preview')->default(false);
            $table->timestamps();

            $table->index('product', 'idx-products_images-product');
            $table->foreign('product', 'fk-products_images-product')
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
        Schema::dropIfExists('products_images');
    }
};
