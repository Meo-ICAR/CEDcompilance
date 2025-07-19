<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentazione_nis2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punto_nis2_id')->constrained('punto_nis2')->onDelete('cascade');
            $table->string('documento');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentazione_nis2');
    }
};
