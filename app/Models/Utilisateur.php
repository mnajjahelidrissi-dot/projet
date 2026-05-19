<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword, HasApiTokens;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'utilisateurs';

    /**
     * Les attributs qui sont autorisés pour l'assignation de masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'telephone',
        'actif',
    ];

    /**
     * Les attributs qui doivent être masqués pour la sérialisation JSON (API).
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être typés (castés).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'actif' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Hachage automatique sécurisé depuis Laravel 10
    ];

    /**
     * Les attributs personnalisés à inclure automatiquement dans les réponses JSON.
     *
     * @var array<int, string>
     */
    protected $appends = ['nom_complet'];

                /*
                |--------------------------------------------------------------------------
                | Accessors & Mutators (Nouvelle Syntaxe Laravel)
                |--------------------------------------------------------------------------
                */

    /**
     * Génère dynamiquement le nom complet de l'utilisateur.
     * Accessible côté Front via : user.nom_complet
     */
    protected function nomComplet(): Attribute
    {
        return Attribute::make(
            get: fn() => trim("{$this->prenom} {$this->nom}"),
        );
    }

                /*
                |--------------------------------------------------------------------------
                | Logique Métier / Rôles (Utilisés par le Dashboard et les Polices)
                |--------------------------------------------------------------------------
                */

    /**
     * Vérifie si l'utilisateur possède le rôle administrateur.
     */
    public function estAdministrateur(): bool
    {
        return $this->role === 'administrateur';
    }

    /**
     * Vérifie si l'utilisateur possède le rôle responsable.
     */
    public function estResponsable(): bool
    {
        return $this->role === 'responsable';
    }

    /**
     * Vérifie si l'utilisateur possède le rôle agent.
     */
    public function estAgent(): bool
    {
        return $this->role === 'agent';
    }

                /*
                |--------------------------------------------------------------------------
                | Relations Éloquentes (Typées explicitement)
                |--------------------------------------------------------------------------
                */

    /**
     * Dossiers assignés à cet agent pour traitement.
     */
    public function dossiersAffectes(): HasMany
    {
        return $this->hasMany(Dossier::class, 'agent_id');
    }

    /**
     * Dossiers créés à l'origine par cet utilisateur.
     */
    public function dossiersCreees(): HasMany
    {
        return $this->hasMany(Dossier::class, 'cree_par');
    }

    /**
     * Dossiers ouverts et initialisés par cet utilisateur.
     */
    public function dossiersOuverts(): HasMany
    {
        return $this->hasMany(Dossier::class, 'ouvert_par');
    }
}
