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
        Schema::create('catalogue_products_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product')->nullable(false);
            $table->string('image')->nullable(false);
            $table->string('disk')->nullable(false);
            $table->boolean('is_preview')->default(false);
            $table->timestamps();

            $table->index('product', 'idx-catalogue_products_images-product');
            $table->foreign('product', 'fk-catalogue_products_images-product')
                ->references('id')
                ->on('catalogue_products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogue_products_images');
    }
};
