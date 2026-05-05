<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Utilisateur extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory,Notifiable, CanResetPassword;

    // Nom de la table
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $keyType = 'int';
    // Le champ mot_de_passe remplace 'password' par défaut de Laravel
    protected $authPasswordName = 'password';
    public function getKey()
    {
        return $this->attributes[$this->getKeyName()];
    }

    public function getKeyName()
    {
        return $this->primaryKey;
    }
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'telephone',
        'actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'email_verified_at' => 'datetime',
    ];
    // Getter pour le nom complet
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Vérification des rôles
    public function estAdministrateur(): bool
    {
        return $this->role === 'administrateur';
    }

    public function estAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function estResponsable(): bool
    {
        return $this->role === 'responsable';
    }

    // Relations
    public function dossiersAffectes()
    {
        return $this->hasMany(Dossier::class, 'agent_id');
    }

    public function dossiersCreees()
    {
        return $this->hasMany(Dossier::class, 'cree_par');
    }
    public function dossiersOuverts() {
        // Correction : votre table Dossier utilise 'ouvert_par'
        return $this->hasMany(Dossier::class, 'ouvert_par');
    }

    // Laravel attend getAuthPassword() pour l'authentification
    public function getAuthPassword(): string
    {
        return $this->password;
    }
    public function getEmailForPasswordReset()
    {
    return $this->email;
    }
}
