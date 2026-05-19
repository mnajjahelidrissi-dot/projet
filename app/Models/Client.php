<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'clients';

    /**
     * Les attributs qui sont autorisés pour l'assignation de masse.
     *
     * @var array<int, string>
     */
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

    /**
     * Les attributs qui doivent être typés (castés).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_naissance' => 'date:Y-m-d',
        'created_at'     => 'datetime:Y-m-d H:i:s',
        'updated_at'     => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Les attributs personnalisés à inclure automatiquement dans les réponses JSON.
     *
     * @var array<int, string>
     */
    protected $appends = ['nom_complet'];

    /**
     * Le "booting" du modèle.
     * Gère la génération automatique et unique du numéro de client.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($client) {
            $annee = date('Y');

            // Approche plus robuste : on cherche le dernier client créé CETTE année
            $dernierClient = self::whereYear('created_at', $annee)
                ->orderBy('id', 'desc')
                ->first();

            if ($dernierClient && preg_match('/-(\d+)$/', $dernierClient->numero_client, $matches)) {
                $increment = (int)$matches[1] + 1;
            } else {
                $increment = 1;
            }

            $client->numero_client = 'CLI-' . $annee . '-' . str_pad($increment, 4, '0', STR_PAD_LEFT);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Nouvelle Syntaxe)
    |--------------------------------------------------------------------------
    */

    /**
     * Génère dynamiquement le nom complet du client.
     * Accessible côté Front via : client.nom_complet
     */
    protected function nomComplet(): Attribute
    {
        return Attribute::make(
            get: fn() => trim("{$this->prenom} {$this->nom}"),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relations Éloquentes
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des dossiers associés à ce client.
     */
    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class, 'client_id');
    }

    /**
     * Utilisateur (Agent/Responsable) ayant enregistré ce client.
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par');
    }
}
