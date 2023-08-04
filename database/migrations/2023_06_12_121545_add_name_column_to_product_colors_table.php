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
        Schema::table('catalogue_products_colors', function (Blueprint $table) {
            $table->string('name')->after('color')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogue_products_colors', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
