<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    private $validUserData = [
        'name' => 'João Silva',
        'email' => 'joao@email.com',
        'password' => 'Senha@123',
        'password_confirmation' => 'Senha@123',
        'celular' => '(11) 99999-9999',
    ];

    private $invalidUserData = [
        'name' => '',
        'email' => 'email-invalido',
        'password' => '123',
        'password_confirmation' => '456',
        'celular' => '11999999999',
    ];

    // ========== FLUXO FELIZ ==========

    /** @test */
    public function can_list_users()
    {
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'email', 'celular', 'created_at', 'updated_at']
                     ],
                     'message'
                 ]);
    }

    /** @test */
    public function can_create_user_with_valid_data()
    {
        $response = $this->postJson('/api/users', $this->validUserData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Usuário criado com sucesso'
                 ]);

        $this->assertDatabaseHas('users', [
            'name' => 'João Silva',
            'email' => 'joao@email.com',
        ]);
    }

    /** @test */
    public function can_show_single_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $user->id,
                         'name' => $user->name,
                         'email' => $user->email,
                     ]
                 ]);
        
        $this->assertArrayNotHasKey('password', $response->json('data'));
    }

    /** @test */
    public function can_update_user()
    {
        $user = User::factory()->create();

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'Nome Atualizado',
            'celular' => '(11) 98888-8888',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Usuário atualizado com sucesso'
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Atualizado',
        ]);
    }

    /** @test */
    public function can_soft_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Usuário deletado com sucesso'
                 ]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    // ========== FLUXO DE ERRO ==========

    /** @test */
    public function cannot_create_user_with_invalid_data()
    {
        $response = $this->postJson('/api/users', $this->invalidUserData);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Erro de validação'
                 ])
                 ->assertJsonValidationErrors(['name', 'email', 'password', 'celular']);
    }

    /** @test */
    public function cannot_create_user_with_duplicate_email()
    {
        User::factory()->create(['email' => 'joao@email.com']);

        $response = $this->postJson('/api/users', $this->validUserData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function cannot_create_user_with_invalid_celular()
    {
        $invalidCelular = $this->validUserData;
        $invalidCelular['celular'] = '11999999999';

        $response = $this->postJson('/api/users', $invalidCelular);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['celular']);
    }

    /** @test */
    public function returns_404_for_nonexistent_user()
    {
        $response = $this->getJson('/api/users/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Usuário não encontrado'
                 ]);
    }
}