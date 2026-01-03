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
        Schema::create('bicycles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->float('wheel_size');
            $table->integer('gears');
            $table->string('sex', 10);
            $table->string('type', 10);
            $table->string('size', 10)->nullable();
            $table->string('color', 80)->nullable();
            $table->foreignId('manufacturer_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bicycles');
    }
};
