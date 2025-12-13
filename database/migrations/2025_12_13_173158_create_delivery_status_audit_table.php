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
        Schema::create('delivery_status_audit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_delivery');
            $table->enum('old_status', ['DITUGASKAN', 'DIKIRIM', 'SELESAI', 'DIBATALKAN']);
            $table->enum('new_status', ['DITUGASKAN', 'DIKIRIM', 'SELESAI', 'DIBATALKAN']);
            $table->timestamp('timestamp')->useCurrent();

            // Foreign key
            $table->foreign('id_delivery')
                ->references('id')
                ->on('deliveries')
                ->onDelete('cascade');

            // Indexes for performance
            $table->index('id_delivery');
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_status_audit');
    }
};
