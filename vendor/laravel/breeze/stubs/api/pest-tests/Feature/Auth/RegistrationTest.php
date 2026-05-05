<?php


test('new users can register', function () {
    $response = $this->post('/register', [
        'nom' => 'Test User',
        'prenom' => 'Test User First Name',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();
});
