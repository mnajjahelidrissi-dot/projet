<?php

/*namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

   public function test_password_can_be_updated(): void
{
    $user = Utilisateur::factory()->create([
        'actif' => true,
        'password' => Hash::make('password'),
    ]);

    // ✅ Changez .post par .put ici
    $response = $this->actingAs($user)->put('/password', [
        'current_password' => 'password',
        'password' => 'newPassword123',
        'password_confirmation' => 'newPassword123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $this->assertTrue(Hash::check('newPassword123', $user->fresh()->password));
}
}
*/


namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function creerUtilisateur(): Utilisateur
    {
        return Utilisateur::create([
            'nom'      => 'TEST',
            'prenom'   => 'Password',
            'email'    => 'password@test.ma',
            'password' => Hash::make('AncienMotDePasse123'),
            'role'     => 'agent',
            'actif'    => true,
        ]);
    }

    public function test_password_can_be_updated(): void
    {
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user)->put('/password', [
            'current_password' => 'AncienMotDePasse123',
            'password' => 'NouveauMotDePasse456',
            'password_confirmation' => 'NouveauMotDePasse456',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertTrue(Hash::check('NouveauMotDePasse456', $user->fresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update(): void
    {
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user)->put('/password', [
            'current_password' => 'MauvaisMotDePasse',
            'password' => 'NouveauMotDePasse456',
            'password_confirmation' => 'NouveauMotDePasse456',
        ]);

        // Au lieu de vérifier une erreur de session, vérifions la réponse
        $response->assertSessionHasErrors();
        $response->assertRedirect(); // Ou assertStatus selon votre code

        $this->assertTrue(Hash::check('AncienMotDePasse123', $user->fresh()->password));
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user)->put('/password', [
            'current_password' => 'AncienMotDePasse123',
            'password' => 'NouveauMotDePasse456',
            'password_confirmation' => 'ConfirmationDifferente',
        ]);

        $response->assertSessionHasErrors();
    }
}
