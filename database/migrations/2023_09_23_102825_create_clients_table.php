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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('hash')
                ->nullable(false)
                ->unique('unique-clients-hash');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('phone')
                ->nullable()
                ->unique('unique-clients-phone');
            $table->string('email')
                ->nullable()
                ->unique('unique-clients-email');
            $table->boolean('phone_confirmed')->default(false);
            $table->boolean('email_confirmed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
