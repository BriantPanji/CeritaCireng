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
        Schema::create('daily_report_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_report');
            $table->enum('action', ['UPDATED', 'VALIDATED', 'INVALIDATED']);
            $table->boolean('old_is_validated')->nullable();
            $table->boolean('new_is_validated');
            $table->timestamp('timestamp')->useCurrent();

            // Foreign key
            $table->foreign('id_report')
                ->references('id')
                ->on('daily_outlet_reports')
                ->onDelete('cascade');

            // Indexes for performance
            $table->index('id_report');
            $table->index('action');
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_log');
    }
};
