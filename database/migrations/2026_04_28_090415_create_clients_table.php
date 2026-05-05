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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('numero_client')->unique(); // ex: CLI-2024-0001
            $table->string('nom');
            $table->string('prenom');
            $table->string('cin')->unique();
            $table->date('date_naissance')->nullable();
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('profession')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->foreignId('cree_par')->nullable()->constrained('utilisateurs')->nullOnDelete();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
