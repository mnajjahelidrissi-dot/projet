<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dossier extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'dossiers';

    /**
     * Les attributs qui sont autorisés pour l'assignation de masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_dossier', // Ajout nécessaire pour l'archivage et l'export
        'client_id',
        'agent_id',
        'ouvert_par',
        'titre',
        'description',
        'statut',
    ];

    /**
     * Configuration des statuts applicables et leurs propriétés d'affichage.
     */
    public const STATUTS = [
        'en_attente' => ['label' => 'En attente', 'color' => 'yellow'],
        'en_cours'   => ['label' => 'En cours',   'color' => 'blue'],
        'valide'     => ['label' => 'Validé',     'color' => 'green'],
        'rejete'     => ['label' => 'Rejeté',     'color' => 'red'],
    ];

    /**
     * Les attributs personnalisés à inclure automatiquement dans les réponses JSON.
     *
     * @var array<int, string>
     */
    protected $appends = ['statut_label', 'statut_color'];

    /**
     * Les attributs qui doivent être typés (castés).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Le "booting" du modèle.
     * Génère automatiquement un numéro de dossier incrémental et unique par année.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($dossier) {
            $annee = date('Y');

            // Recherche du dernier dossier de l'année en cours
            $dernierDossier = self::whereYear('created_at', $annee)
                ->orderBy('id', 'desc')
                ->first();

            if ($dernierDossier && preg_match('/-DOS-(\d+)$/', $dernierDossier->numero_dossier, $matches)) {
                $increment = (int)$matches[1] + 1;
            } else {
                $increment = 1;
            }

            $dossier->numero_dossier = $annee . '-DOS-' . str_pad($increment, 5, '0', STR_PAD_LEFT);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Nouvelle Syntaxe Laravel)
    |--------------------------------------------------------------------------
    */

    /**
     * Extrait le libellé lisible du statut actuel.
     * Front : dossier.statut_label
     */
    protected function statutLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::STATUTS[$this->statut]['label'] ?? $this->statut,
        );
    }

    /**
     * Extrait le code couleur lié au statut actuel.
     * Front : dossier.statut_color
     */
    protected function statutColor(): Attribute
    {
        return Attribute::make(
            get: fn() => self::STATUTS[$this->statut]['color'] ?? 'gray',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relations Éloquentes
    |--------------------------------------------------------------------------
    */

    /**
     * Client associé au dossier.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Agent chargé du traitement de ce dossier.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'agent_id');
    }

    /**
     * Utilisateur système ayant initialisé le dossier.
     */
    public function ouvertPar(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'ouvert_par');
    }

    /**
     * Liste des demandes associées au dossier.
     */
    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class, 'dossier_id');
    }

    /**
     * Documents d'identification ou justificatifs joints au dossier.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'dossier_id');
    }

    /**
     * Historique global de traçabilité des actions sur ce dossier.
     */
    public function historique(): HasMany
    {
        return $this->hasMany(Historique::class, 'dossier_id');
    }
}
