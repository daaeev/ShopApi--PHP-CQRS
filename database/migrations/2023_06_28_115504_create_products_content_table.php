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
        Schema::create('products_content', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product')->nullable(false);
            $table->string('language', 10)->nullable(false);
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('product', 'idx-products_content-product');
            $table->foreign('product', 'fk-products_content-product')
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
        Schema::dropIfExists('products_content');
    }
};
