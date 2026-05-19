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
        Schema::create('historiques', function (Blueprint $table) {
            $table->id();

            // Relation avec le dossier
            $table->foreignId('dossier_id')
                ->nullable()
                ->constrained('dossiers')
                ->onDelete('cascade');

            // Relation avec l'utilisateur qui a effectué l'action
            $table->foreignId('utilisateur_id')
                ->constrained('utilisateurs')
                ->onDelete('cascade');

            // Type d'action
            $table->string('action');

            // Description détaillée de l'action
            $table->text('details')->nullable();

            // Ancienne valeur
            $table->text('ancienne_valeur')->nullable();

            // Nouvelle valeur
            $table->text('nouvelle_valeur')->nullable();

            // Adresse IP de l'utilisateur
            $table->string('ip_address', 45)->nullable();

            // User Agent du navigateur
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Index pour accélérer les recherches
            $table->index('dossier_id');
            $table->index('utilisateur_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiques');
    }
};
