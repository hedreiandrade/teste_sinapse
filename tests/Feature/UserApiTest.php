<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    private $validUserData = [
        'name' => 'João Silva',
        'email' => 'joao@email.com',
        'password' => 'SenhaForte@2024',
        'password_confirmation' => 'SenhaForte@2024',
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

    #[Test]
    public function test_can_list_users()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'email', 'celular', 'created_at', 'updated_at']
                     ],
                 ]);
    }

    #[Test]
    public function test_can_create_user_with_valid_data()
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

    #[Test]
    public function test_can_show_single_user()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $user->id,
                         'name' => $user->name,
                         'email' => $user->email,
                         'celular' => $user->celular,
                     ]
                 ]);
        
        $this->assertArrayNotHasKey('password', $response->json('data'));
    }

    #[Test]
    public function test_can_update_user()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

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

    #[Test]
    public function test_can_soft_delete_user()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Usuário deletado com sucesso'
                 ]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    // ========== FLUXO DE ERRO ==========

    #[Test]
    public function test_cannot_create_user_with_invalid_data()
    {
        $response = $this->postJson('/api/users', $this->invalidUserData);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors'
                 ])
                 ->assertJsonValidationErrors(['name', 'email', 'password', 'celular']);
    }

    #[Test]
    public function test_cannot_create_user_with_duplicate_email()
    {
        User::factory()->create([
            'email' => 'joao@email.com',
            'email_verified_at' => null
        ]);

        $response = $this->postJson('/api/users', $this->validUserData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function test_cannot_create_user_with_invalid_celular()
    {
        $invalidCelular = $this->validUserData;
        $invalidCelular['celular'] = '11999999999';

        $response = $this->postJson('/api/users', $invalidCelular);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['celular']);
    }

    #[Test]
    public function test_returns_404_for_nonexistent_user()
    {
        $response = $this->getJson('/api/users/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Usuário não encontrado'
                 ]);
    }
}