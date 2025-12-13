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
        Schema::create('item_change_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_item');
            $table->enum('action', ['CREATE', 'UPDATE', 'DELETE', 'RESTORE']);
            $table->string('field_changed', 64)->nullable();
            $table->string('old_value', 512)->nullable();
            $table->string('new_value', 512)->nullable();
            $table->timestamp('timestamp')->useCurrent();

            // Foreign key
            $table->foreign('id_item')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');

            // Indexes for performance
            $table->index('id_item');
            $table->index('action');
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_change_log');
    }
};
