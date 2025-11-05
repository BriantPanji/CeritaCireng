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
        Schema::create('delivery_mistake_items', function (Blueprint $table) {
            $table->foreignId('id_delivery_mistake')->references('id')->on('delivery_mistakes')->onDelete('cascade');
            $table->foreignId('id_item')->references('id')->on('items');
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_mistake_items');
    }
};
