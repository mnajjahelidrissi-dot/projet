<?php

namespace Tests\Feature\test;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie qu'un endpoint de base ou d'authentification répond.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // On teste simplement la route de login par défaut qui doit renvoyer une erreur de méthode
        // ou être interceptée si on fait un GET (car configurée en POST)
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401); // Doit renvoyer non connecté
    }
}
