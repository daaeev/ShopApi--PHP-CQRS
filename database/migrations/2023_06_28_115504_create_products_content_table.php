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
        Schema::create('catalogue_products_content', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product')->nullable(false);
            $table->string('language', 10)->nullable(false);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('product', 'idx-catalogue_products_content-product');
            $table->foreign('product', 'fk-catalogue_products_content-product')
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
        Schema::dropIfExists('catalogue_products_content');
    }
};
