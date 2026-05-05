<?php
// FICHIER: database/migrations/2024_01_05_create_documents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->cascadeOnDelete();
            $table->enum('type_document', [
                'cin', 'domicile', 'contrat', 'releve_compte', 'autre',
            ]);
            $table->string('nom_fichier');
            $table->string('chemin');
            $table->unsignedBigInteger('taille');
            $table->string('mime_type', 100);
            $table->foreignId('uploade_par')->nullable()->constrained('utilisateurs')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('documents'); }
};
