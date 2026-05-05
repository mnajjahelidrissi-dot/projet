<?php
// FICHIER: app/Models/Document.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'dossier_id', 'type_document', 'nom_fichier',
        'chemin', 'taille', 'mime_type', 'uploade_par',
    ];

    public function dossier()    { return $this->belongsTo(Dossier::class); }
    public function uploadePar() { return $this->belongsTo(Utilisateur::class, 'uploade_par'); }

    public function getTailleFormateeAttribute(): string
    {
        $kb = $this->taille / 1024;
        return $kb > 1024
            ? number_format($kb / 1024, 2) . ' MB'
            : number_format($kb, 1) . ' KB';
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}
