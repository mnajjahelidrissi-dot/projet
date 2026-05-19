<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Historique extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'historiques';

    /**
     * Les attributs qui sont autorisés pour l'assignation de masse.
     *
     * @var array<int, string>
     */
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

    /**
     * Les attributs qui doivent être typés (castés).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ancienne_valeur' => 'array', // Permet de stocker et lire du JSON proprement
        'nouvelle_valeur' => 'array', // Permet de stocker et lire du JSON proprement
        'created_at'      => 'datetime:Y-m-d H:i:s',
        'updated_at'      => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Les attributs personnalisés à inclure automatiquement dans les réponses JSON.
     *
     * @var array<int, string>
     */
    protected $appends = ['action_label'];

    /**
     * Référentiel des types d'actions pour le système d'audit log.
     */
    public const ACTION_CREATION            = 'création';
    public const ACTION_MODIFICATION        = 'modification';
    public const ACTION_CHANGEMENT_STATUT   = 'changement_statut';
    public const ACTION_UPLOAD_DOCUMENT     = 'upload_document';
    public const ACTION_SUPPRESSION_DOCUMENT = 'suppression_document';
    public const ACTION_AJOUT_DEMANDE       = 'ajout_demande';
    public const ACTION_SUPPRESSION_DEMANDE = 'suppression_demande';
    public const ACTION_AFFECTATION_AGENT   = 'affectation_agent';
    public const ACTION_CONNEXION           = 'connexion';
    public const ACTION_DECONNEXION         = 'déconnexion';
    public const ACTION_SUPPRESSION         = 'suppression';

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Nouvelle Syntaxe Laravel)
    |--------------------------------------------------------------------------
    */

    /**
     * Traduit le type d'action en libellé formaté pour le Front-End.
     * Front : historique.action_label
     */
    protected function actionLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    self::ACTION_CREATION            => 'Création',
                    self::ACTION_MODIFICATION        => 'Modification',
                    self::ACTION_CHANGEMENT_STATUT   => 'Changement de statut',
                    self::ACTION_UPLOAD_DOCUMENT     => 'Téléversement de document',
                    self::ACTION_SUPPRESSION_DOCUMENT => 'Suppression de document',
                    self::ACTION_AJOUT_DEMANDE       => 'Ajout de demande',
                    self::ACTION_SUPPRESSION_DEMANDE => 'Suppression de demande',
                    self::ACTION_AFFECTATION_AGENT   => 'Affectation d\'agent',
                    self::ACTION_CONNEXION           => 'Connexion',
                    self::ACTION_DECONNEXION         => 'Déconnexion',
                    self::ACTION_SUPPRESSION         => 'Suppression',
                ];

                return $labels[$this->action] ?? ucfirst($this->action);
            },
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Méthode Utilitaire Globale (Audit Logging)
    |--------------------------------------------------------------------------
    */

    /**
     * Enregistre une trace d'audit pour n'importe quelle action de l'application.
     *
     * @param int|null $dossierId ID du dossier concerné (si applicable)
     * @param string $action Constante d'action héritée de la classe Historique
     * @param string|null $details Descriptif textuel de l'événement
     * @param array|string|null $ancienneValeur État des attributs avant modification
     * @param array|string|null $nouvelleValeur État des attributs après modification
     */
    public static function enregistrer(
        ?int $dossierId,
        string $action,
        ?string $details = null,
        mixed $ancienneValeur = null,
        mixed $nouvelleValeur = null
    ): self {
        return self::create([
            'dossier_id'      => $dossierId,
            'utilisateur_id'  => Auth::id(), // Retourne null si l'action est système (ex: tâche planifiée)
            'action'          => $action,
            'details'         => $details,
            'ancienne_valeur' => $ancienneValeur,
            'nouvelle_valeur' => $nouvelleValeur,
            'ip_address'      => request()->ip(), // Utilisation sécurisée du helper global de requête
            'user_agent'      => request()->userAgent(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relations Éloquentes
    |--------------------------------------------------------------------------
    */

    /**
     * Dossier sur lequel l'action a été exécutée.
     */
    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
    }

    /**
     * L'utilisateur (Agent / Administrateur) auteur de l'action.
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
