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
        Schema::create('worker_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('shift_workers_id');
            $table->foreign('shift_id')->references('id')->on('shifts');
            $table->foreign('shift_workers_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
