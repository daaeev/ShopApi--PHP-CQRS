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
        Schema::create('shopping_discounts_promotions_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id')->nullable(false);
            $table->string('type')->nullable(false);
            $table->json('data')->nullable();

            $table->index('promotion_id', 'idx-shopping_discounts_promotions_discounts-promotion_id');
            $table->foreign('promotion_id', 'fk-shopping_discounts_promotions_discounts-promotion_id')
                ->on('shopping_discounts_promotions')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_discounts_promotions_discounts');
    }
};
