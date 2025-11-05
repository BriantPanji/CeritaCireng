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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_inventaris')->references('id')->on('users');
            $table->foreignId('id_kurir')->references('id')->on('users');
            $table->foreignId('id_outlet')->references('id')->on('outlets');
            $table->enum('status', ['DITUGASKAN', 'DIKIRIM', 'SELESAI', 'DIBATALKAN'])->default('DITUGASKAN');
            $table->string('photo_evidence', 1024)->nullable();
            $table->dateTime('assigned_at');
            $table->dateTime('delivered_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
