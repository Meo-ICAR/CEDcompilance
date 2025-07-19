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
        Schema::create('organizzaziones', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('partita_iva')->unique();
            $table->string('indirizzo');
            $table->string('citta');
            $table->string('provincia', 2);
            $table->string('cap', 5);
            $table->string('paese')->default('Italia');
            $table->string('referente');
            $table->string('email_referente');
            $table->string('telefono_referente')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizzaziones');
    }
};
