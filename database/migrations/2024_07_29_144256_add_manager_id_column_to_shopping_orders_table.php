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
        Schema::table('shopping_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->nullable()->after('client_hash');
            $table->string('manager_name')->nullable()->after('manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopping_orders', function (Blueprint $table) {
            $table->dropColumn('manager_id');
            $table->dropColumn('manager_name');
        });
    }
};
