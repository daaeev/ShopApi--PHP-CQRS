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
        Schema::create('clients_confirmations', function (Blueprint $table) {
            $table->string('uuid')->primary();
            $table->unsignedBigInteger('client_id')->nullable(false);
            $table->string('code')->nullable(false);
            $table->timestamp('expired_at')->nullable(false);
            $table->timestamps();

            $table->index('client_id', 'idx-clients_confirmations-client_id');
            $table->foreign('client_id', 'fk-clients_confirmations-client_id')
                ->references('id')
                ->on('clients')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients_confirmations');
    }
};
