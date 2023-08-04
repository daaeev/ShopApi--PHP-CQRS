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
        Schema::create('catalogue_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('slug')
                ->nullable(false)
                ->unique('unique-catalogue_categories-slug');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            $table->index('parent_id', 'idx-catalogue_categories-parent_id');
            $table->foreign('parent_id', 'fk-catalogue_categories-parent_id')
                ->references('id')
                ->on('catalogue_categories')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogue_categories');
    }
};
