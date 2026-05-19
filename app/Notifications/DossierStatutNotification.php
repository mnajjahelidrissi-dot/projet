<?php
//Pour avoir une notification par email et en base de données lors du changement de statut d'un dossier (en attente, en cours, validé, rejeté)
namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DossierStatutNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $dossier;
    protected $ancienStatut;
    protected $nouveauStatut;

    public function __construct(Dossier $dossier, $ancienStatut, $nouveauStatut)
    {
        $this->dossier = $dossier;
        $this->ancienStatut = $ancienStatut;
        $this->nouveauStatut = $nouveauStatut;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $statuts = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
        ];

        return (new MailMessage)
            ->subject('Changement de statut du dossier #' . ($this->dossier->numero_dossier ?? $this->dossier->id))
            ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->nom)
            ->line('Le statut de votre dossier a été modifié.')
            ->line('**Dossier :** ' . $this->dossier->titre)
            ->line('**Ancien statut :** ' . ($statuts[$this->ancienStatut] ?? $this->ancienStatut))
            ->line('**Nouveau statut :** ' . ($statuts[$this->nouveauStatut] ?? $this->nouveauStatut))
            ->action('Voir le dossier', url('/dossiers/' . $this->dossier->id))
            ->line('Merci de votre confiance.');
    }

    public function toArray($notifiable)
    {
        return [
            'dossier_id' => $this->dossier->id,
            'dossier_titre' => $this->dossier->titre,
            'ancien_statut' => $this->ancienStatut,
            'nouveau_statut' => $this->nouveauStatut,
            'message' => 'Le statut du dossier "' . $this->dossier->titre . '" est passé à ' . $this->nouveauStatut,
        ];
    }
}
