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
        Schema::create('return_confirmations', function (Blueprint $table) {
            $table->foreignId('id_return')->references('id')->on('returns')->onDelete('cascade');
            $table->foreignId('id_inventaris')->references('id')->on('users');
            $table->string('notes', 1024)->nullable();
            $table->dateTime('confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_confirmations');
    }
};
