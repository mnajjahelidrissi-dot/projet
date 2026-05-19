<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Demande extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'demandes';

    /**
     * Les attributs qui sont autorisés pour l'assignation de masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dossier_id',
        'type_demande',
        'description',
        'montant',
        'cree_par',
    ];

    /**
     * Les attributs qui doivent être typés (castés).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'montant'    => 'decimal:2',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Les attributs personnalisés à inclure automatiquement dans les réponses JSON.
     *
     * @var array<int, string>
     */
    protected $appends = ['type_demande_label'];

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Nouvelle Syntaxe Laravel)
    |--------------------------------------------------------------------------
    */

    /**
     * Génère dynamiquement le libellé lisible associé au type de demande.
     * Front : demande.type_demande_label
     */
    protected function typeDemandeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $types = [
                    'ouverture_compte' => 'Ouverture de compte',
                    'demande_carte'    => 'Demande de carte',
                    'demande_credit'   => 'Demande de crédit',
                    'reclamation'      => 'Réclamation',
                    'autre'            => 'Autre',
                ];

                return $types[$this->type_demande] ?? $this->type_demande;
            },
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relations Éloquentes
    |--------------------------------------------------------------------------
    */

    /**
     * Dossier d'instruction auquel est rattachée la demande.
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
    }

    /**
     * Utilisateur système ayant enregistré cette demande.
     */
    public function creePar(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par');
    }
}
