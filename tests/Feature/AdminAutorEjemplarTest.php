<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Rol;
use App\Models\Usuario;

class AdminAutorEjemplarTest extends TestCase
{
    use DatabaseMigrations;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        Rol::insert([
            ['rol' => 'usuario'],
            ['rol' => 'socio'],
            ['rol' => 'administrador'],
        ]);

        $adminRol = Rol::where('rol', 'administrador')->first();

        $this->admin = Usuario::create([
            'nombre' => 'Admin',
            'apellido1' => 'Sys',
            'apellido2' => 'Admin',
            'fecNacimiento' => '1985-01-01',
            'email' => 'admin@example.com',
            'password' => bcrypt('Password123'),
            'idRol' => $adminRol->idRol,
        ]);
    }

    public function test_admin_can_access_admin_panel()
    {
        $response = $this->actingAs($this->admin)->get(route('admin'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_author()
    {
        $response = $this->actingAs($this->admin)->post(route('autor.crear'), [
            'nombre' => 'Nuevo',
            'ape1' => 'Autor',
            'ape2' => 'Test',
        ]);

        $this->assertDatabaseHas('autor', ['nomAutor' => 'Nuevo']);
    }

    public function test_any_user_can_view_ejemplares()
    {
        $response = $this->get(route('ejemplar.ejemplares'));
        $response->assertStatus(200);
    }
}
