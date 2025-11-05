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
        Schema::create('product_items', function (Blueprint $table) {
            $table->foreignId('id_product')->references('id')->on('products')->onDelete('cascade');
            $table->foreignId('id_item')->references('id')->on('items')->onDelete('cascade');
            $table->integer('quantity')->min(0);
            $table->boolean('optional')->default(FALSE);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
