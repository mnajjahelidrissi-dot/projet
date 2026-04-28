<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['nom', 'prenom', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    public function isAdmin(): bool       { return $this->role === 'administrateur'; }
    public function isAgent(): bool       { return $this->role === 'agent'; }
    public function isResponsable(): bool { return $this->role === 'responsable'; }

    public function dossiers()     { return $this->hasMany(Dossier::class, 'agent_id'); }
    public function dossierOuverts(){ return $this->hasMany(Dossier::class, 'ouvert_par'); }
}
