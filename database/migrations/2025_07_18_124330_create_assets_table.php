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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizzazione_id')->constrained('organizzaziones')->onDelete('cascade');
            $table->string('nome');
            $table->string('categoria');
            $table->text('descrizione')->nullable();
            $table->string('ubicazione')->nullable();
            $table->string('responsabile')->nullable();
            $table->string('stato')->default('attivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
