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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique();
            $table->integer('cost')->min(0);
            $table->enum('unit', ['pcs', 'gr', 'ml', 'unit']);
            $table->enum('type', ['BAHAN_MENTAH', 'BAHAN_PENUNJANG', 'KEMASAN']);
            $table->string('image', 512)->nullable()->default('https://placehold.co/600x400.webp?text=Foto+Item');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
