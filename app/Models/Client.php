<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Utilisateur;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'numero_client',
        'nom',
        'prenom',
        'cin',
        'date_naissance',
        'telephone',
        'email',
        'adresse',
        'ville',
        'profession',
        'statut',
        'cree_par',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    // Génère automatiquement le numéro client avant la création
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($client) {
            $annee = date('Y');
            $dernier = Client::whereYear('created_at', $annee)->count() + 1;
            $client->numero_client = 'CLI-' . $annee . '-' . str_pad($dernier, 4, '0', STR_PAD_LEFT);
        });
    }

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Relations
    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class, 'client_id');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par');
    }
}
