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
        Schema::create('shopping_discounts_promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('code')
                ->unique('unique-shopping_discounts_promocodes-code')
                ->nullable(false);
            $table->boolean('active')->default(true);
            $table->tinyInteger(
                'discount_percent',
                unsigned: true
            )->nullable(false);
            $table->timestamp('start_date')->nullable(false);
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_discounts_promocodes');
    }
};
