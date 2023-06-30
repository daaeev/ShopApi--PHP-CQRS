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
        Schema::create('catalogue_categories_content', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category')->nullable(false);
            $table->string('language', 10)->nullable(false);
            $table->string('name')->nullable();
            $table->timestamps();

            $table->index('category', 'idx-catalogue_categories_content-category');
            $table->foreign('category', 'fk-catalogue_categories_content-category')
                ->references('id')
                ->on('catalogue_categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogue_categories_content');
    }
};
