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
        Schema::table('nis2_dettagli', function (Blueprint $table) {
            $table->string('documenti')->nullable()->after('adempimento')->comment('Nome della sottodirectory per i documenti specifici');
            $table->text('driveurl')->nullable()->after('documenti')->comment('URL della cartella Google Drive corrispondente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nis2_dettagli', function (Blueprint $table) {
            $table->dropColumn(['documenti', 'driveurl']);
        });
    }
};
