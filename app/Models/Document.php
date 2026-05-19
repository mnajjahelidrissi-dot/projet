<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'documents';

    /**
     * Les attributs qui sont autorisés pour l'assignation de masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dossier_id',
        'type_document',
        'nom_fichier',
        'chemin',
        'taille',
        'mime_type',
        'uploade_par',
    ];

    /**
     * Les attributs personnalisés à inclure automatiquement dans les réponses JSON.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'taille_formatee',
        'type_document_label',
        'download_url'
    ];

    /**
     * Les attributs qui doivent être typés (castés).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'taille'     => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Nouvelle Syntaxe Laravel)
    |--------------------------------------------------------------------------
    */

    /**
     * Formate la taille du fichier pour une lecture humaine.
     * Front : document.taille_formatee
     */
    protected function tailleFormatee(): Attribute
    {
        return Attribute::make(
            get: function () {
                $kb = $this->taille / 1024;
                return $kb > 1024
                    ? number_format($kb / 1024, 2) . ' MB'
                    : number_format($kb, 1) . ' KB';
            },
        );
    }

    /**
     * Traduit le code du document en un libellé compréhensible.
     * Front : document.type_document_label
     */
    protected function typeDocumentLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $types = [
                    'cin'           => 'CIN',
                    'domicile'      => 'Justificatif de domicile',
                    'contrat'       => 'Contrat',
                    'releve_compte' => 'Relevé de compte',
                    'autre'         => 'Autre',
                ];
                return $types[$this->type_document] ?? $this->type_document;
            },
        );
    }

    /**
     * Génère l'URL de l'API permettant de télécharger le fichier de manière sécurisée.
     * Front : document.download_url
     */
    protected function downloadUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => url("/api/documents/{$this->id}/download"),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Logique Métier / Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si le fichier associé est un document PDF.
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /*
    |--------------------------------------------------------------------------
    | Relations Éloquentes
    |--------------------------------------------------------------------------
    */

    /**
     * Dossier d'instruction auquel est rattaché le document.
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
    }

    /**
     * Utilisateur système ayant téléversé et validé le fichier.
     */
    public function uploadePar(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'uploade_par');
    }
}
