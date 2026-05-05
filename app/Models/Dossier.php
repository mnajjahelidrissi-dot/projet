<?php

// FICHIER: app/Models/Dossier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dossier extends Model
{
    protected $fillable = [
        'client_id', 'agent_id', 'ouvert_par',
        'titre', 'description', 'statut',
    ];
    public static $typesDemandes = [
        'ouverture_compte' => 'Ouverture de compte',
        'demande_carte'    => 'Demande de carte',
        'demande_credit'   => 'Demande de crédit',
    ];

    public const STATUTS = [
        'en_attente' => ['label' => 'En attente',  'color' => 'yellow'],
        'en_cours'   => ['label' => 'En cours',    'color' => 'blue'],
        'valide'     => ['label' => 'Validé',      'color' => 'green'],
        'rejete'     => ['label' => 'Rejeté',      'color' => 'red'],
    ];

    public function client()    { return $this->belongsTo(Client::class); }
    public function agent()     { return $this->belongsTo(Utilisateur::class, 'agent_id'); }
    public function ouvertPar() { return $this->belongsTo(Utilisateur::class, 'ouvert_par'); }
    public function demandes()  { return $this->hasMany(Demande::class); }
    public function documents() { return $this->hasMany(Document::class); }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut]['label'] ?? $this->statut;
    }

    public function getStatutColorAttribute(): string
    {
        return self::STATUTS[$this->statut]['color'] ?? 'gray';
    }
}
