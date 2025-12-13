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
        Schema::create('inventory_change_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_item');
            $table->integer('old_stock');
            $table->integer('new_stock');
            $table->integer('change_amount');
            $table->timestamp('timestamp')->useCurrent();

            // Foreign key
            $table->foreign('id_item')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');

            // Indexes for performance
            $table->index('id_item');
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_change_log');
    }
};
