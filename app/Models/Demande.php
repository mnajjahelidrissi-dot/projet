<?php
// FICHIER: app/Models/Demande.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    protected $fillable = [
        'dossier_id', 'type_demande', 'description', 'montant', 'cree_par',
    ];

    protected $casts = ['montant' => 'decimal:2'];

    public function dossier() { return $this->belongsTo(Dossier::class); }
    public function creePar() { return $this->belongsTo(Utilisateur::class, 'cree_par'); }
}
