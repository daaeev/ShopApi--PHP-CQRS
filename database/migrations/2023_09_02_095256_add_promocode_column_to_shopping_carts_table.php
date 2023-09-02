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
        Schema::table('shopping_carts', function (Blueprint $table) {
            $table->unsignedBigInteger('promocode_id')
                ->nullable()
                ->after('currency');
            $table->index('promocode_id', 'idx-shopping_carts-promocode_id');
            $table->foreign('promocode_id', 'fk-shopping_carts-promocode_id')
                ->references('id')
                ->on('shopping_discounts_promocodes')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopping_carts', function (Blueprint $table) {
            $table->dropForeign('fk-shopping_carts-promocode_id');
            $table->dropIndex('idx-shopping_carts-promocode_id');
            $table->dropColumn('promocode_id');
        });
    }
};
