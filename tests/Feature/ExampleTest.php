<?php

namespace Tests\Feature\test;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que la page de connexion répond correctement.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // La page racine redirige vers /connexion
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
