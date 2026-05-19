<?php
// FICHIER: database/migrations/2024_01_03_create_dossiers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {   //Creation de la table Dossiers
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();

            // Définition simple des colonnes
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('ouvert_par')->nullable();

            $table->string('titre', 200);
            $table->text('description')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'valide', 'rejete'])->default('en_attente');
            $table->timestamps();
        });

        //  les clés étrangères
        Schema::table('dossiers', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('utilisateurs')->onDelete('set null');
            $table->foreign('ouvert_par')->references('id')->on('utilisateurs')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
