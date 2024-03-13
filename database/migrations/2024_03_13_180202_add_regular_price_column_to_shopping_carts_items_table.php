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
        Schema::table('shopping_carts_items', function (Blueprint $table) {
			$table->decimal('regular_price', unsigned: true)
				->after('name')
				->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopping_carts_items', function (Blueprint $table) {
            $table->dropColumn('regular_price');
        });
    }
};
