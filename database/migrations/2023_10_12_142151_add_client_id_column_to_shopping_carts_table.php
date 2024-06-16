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
            $table->unsignedBigInteger('client_id')
                ->after('client_hash')
                ->nullable();

            $table->index('client_id', 'idx-shopping_carts-client_id');
            $table->foreign('client_id', 'fk-shopping_carts-client_id')
                ->references('id')
                ->on('clients')
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
            $table->dropForeign('fk-shopping_carts-client_id');
            $table->dropIndex('idx-shopping_carts-client_id');
            $table->dropColumn('client_id');
        });
    }
};
