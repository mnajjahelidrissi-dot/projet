<?php

namespace Tests\Feature\Auth;

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
