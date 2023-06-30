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
        Schema::create('catalogue_categories_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable(false);
            $table->unsignedBigInteger('product_id')->nullable(false);

            $table->index('category_id', 'idx-catalogue_categories_products-category_id');
            $table->foreign('category_id', 'fk-catalogue_categories_products-category_id')
                ->references('id')
                ->on('catalogue_categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->index('product_id', 'idx-catalogue_categories_products-product_id');
            $table->foreign('product_id', 'fk-catalogue_categories_products-product_id')
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
        Schema::dropIfExists('catalogue_categories_products');
    }
};
