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
        Schema::create('rischios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('titolo');
            $table->text('descrizione')->nullable();
            $table->enum('probabilita', ['bassa', 'media', 'alta']);
            $table->enum('impatto', ['basso', 'medio', 'alto']);
            $table->string('stato')->default('aperto');
            $table->text('azioni_mitigazione')->nullable();
            $table->date('data_valutazione')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rischios');
    }
};
