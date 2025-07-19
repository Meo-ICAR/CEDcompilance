<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nis2_dettagli', function (Blueprint $table) {
            $table->id();
            $table->string('id_voce');
            $table->string('voce');
            $table->string('id_sottovoce');
            $table->string('sottovoce');
            $table->text('adempimento');
            $table->text('documentazione');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nis2_dettagli');
    }
};
