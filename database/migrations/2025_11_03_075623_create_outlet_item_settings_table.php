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
        Schema::create('outlet_item_settings', function (Blueprint $table) {
            $table->foreignId('id_outlet')->references('id')->on('outlets')->onDelete('cascade');
            $table->foreignId('id_item')->references('id')->on('items')->onDelete('cascade');
            $table->integer('quantity')->min(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_item_settings');
    }
};
