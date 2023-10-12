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
                ->nullable(false);
            $table->index('client_id', 'idx-shopping_carts-client_id');
            $table->foreign('client_id', 'fk-shopping_carts-client_id')
                ->references('id')
                ->on('clients')
                ->cascadeOnUpdate();

            $table->index('client_hash', 'idx-shopping_carts-client_hash');
            $table->foreign('client_hash', 'fk-shopping_carts-client_hash')
                ->references('hash')
                ->on('clients')
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

            $table->dropForeign('fk-shopping_carts-client_hash');
            $table->dropIndex('idx-shopping_carts-client_hash');
        });
    }
};
