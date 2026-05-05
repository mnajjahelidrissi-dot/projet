<?php
// FICHIER: database/migrations/2024_01_04_create_demandes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();
            $table->enum('type_demande', [
                'ouverture_compte', 'demande_carte',
                'demande_credit', 'reclamation', 'autre',
            ]);
            $table->text('description');
            $table->decimal('montant', 15, 2)->nullable();
            $table->foreignId('cree_par')->nullable()->constrained('utilisateurs')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('demandes'); }
};
