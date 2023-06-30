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
        Schema::create('catalogue_products_colors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable(false);
            $table->string('type', 30)->nullable(false);
            $table->string('color')->nullable(false);

            $table->index('product_id', 'idx-catalogue_products_colors-product_id');
            $table->foreign('product_id', 'fk-catalogue_products_colors-product_id')
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
        Schema::dropIfExists('catalogue_products_colors');
    }
};
