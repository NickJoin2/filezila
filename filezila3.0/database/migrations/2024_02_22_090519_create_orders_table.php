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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id');
            $table->unsignedBigInteger('shift_workers');
            $table->foreign('table_id')->references('id')->on('tables');
            $table->foreign('shift_workers')->references('shift_workers_id')->on('worker_shifts');
            $table->timestamp('create_at');
            $table->string('status');
            $table->integer('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
