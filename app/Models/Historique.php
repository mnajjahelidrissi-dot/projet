<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historique extends Model
{
    protected $table = 'historiques';

    protected $fillable = [
        'dossier_id',
        'utilisateur_id',
        'action',
        'details',
        'ancienne_valeur',
        'nouvelle_valeur',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Constantes pour les types d'actions
     */
    public const ACTION_CREATION = 'création';
    public const ACTION_MODIFICATION = 'modification';
    public const ACTION_CHANGEMENT_STATUT = 'changement_statut';
    public const ACTION_UPLOAD_DOCUMENT = 'upload_document';
    public const ACTION_SUPPRESSION_DOCUMENT = 'suppression_document';
    public const ACTION_AJOUT_DEMANDE = 'ajout_demande';
    public const ACTION_SUPPRESSION_DEMANDE = 'suppression_demande';
    public const ACTION_AFFECTATION_AGENT = 'affectation_agent';
    public const ACTION_CONNEXION = 'connexion';
    public const ACTION_DECONNEXION = 'déconnexion';
    public const ACTION_SUPPRESSION = 'suppression';

    /**
     * Relations
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    /**
     * Accesseurs
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            self::ACTION_CREATION => 'Création',
            self::ACTION_MODIFICATION => 'Modification',
            self::ACTION_CHANGEMENT_STATUT => 'Changement de statut',
            self::ACTION_UPLOAD_DOCUMENT => 'Upload de document',
            self::ACTION_SUPPRESSION_DOCUMENT => 'Suppression de document',
            self::ACTION_AJOUT_DEMANDE => 'Ajout de demande',
            self::ACTION_SUPPRESSION_DEMANDE => 'Suppression de demande',
            self::ACTION_AFFECTATION_AGENT => 'Affectation d\'agent',
            self::ACTION_CONNEXION => 'Connexion',
            self::ACTION_DECONNEXION => 'Déconnexion',
            self::ACTION_SUPPRESSION => 'Suppression',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Méthode utilitaire pour enregistrer une action
     */
    public static function enregistrer(
        ?int $dossierId,
        string $action,
        ?string $details = null,
        ?string $ancienneValeur = null,
        ?string $nouvelleValeur = null
    ): self {
        return self::create([
            'dossier_id' => $dossierId,
            'utilisateur_id' => auth()->id(),
            'action' => $action,
            'details' => $details,
            'ancienne_valeur' => $ancienneValeur,
            'nouvelle_valeur' => $nouvelleValeur,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
