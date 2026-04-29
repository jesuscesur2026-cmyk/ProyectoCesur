<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Rol;

class AuthFlowTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        // Migrate is run by DatabaseMigrations trait; seed roles
        Rol::insert([
            ['rol' => 'usuario'],
            ['rol' => 'socio'],
            ['rol' => 'administrador'],
        ]);
    }

    public function test_registration_creates_user()
    {
        $response = $this->post('/register', [
            'nombre' => 'Test',
            'ape1' => 'User',
            'ape2' => 'T',
            'fechaNac' => '1990-01-01',
            'email' => 'testuser@example.com',
            'password' => 'Password123',
            'password-confirm' => 'Password123',
        ]);

        $this->assertDatabaseHas('usuario', ['email' => 'testuser@example.com']);
    }
}
