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
        Schema::create('shopping_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->nullable();
            $table->string('client_hash')->nullable(false);
            $table->string('first_name')->nullable(false);
            $table->string('last_name')->nullable(false);
            $table->string('phone')->nullable(false);
            $table->string('email')->nullable();

            $table->string('status')->nullable(false);
            $table->string('payment_status')->nullable(false);

            $table->string('currency')->nullable(false);
            $table->integer('total_price')->nullable(false);
            $table->integer('regular_price')->nullable(false);

            $table->bigInteger('promocode_id')->nullable();
            $table->string('promocode')->nullable();
            $table->tinyInteger('promocode_discount_percent', unsigned: true)->nullable();

            $table->text('customer_comment')->nullable();
            $table->text('manager_comment')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_orders');
    }
};
