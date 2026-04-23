<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserUnitTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_user_can_be_created_with_valid_data()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => bcrypt('Senha@123'),
            'celular' => '(11) 99999-9999',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@email.com', $user->email);
        $this->assertEquals('(11) 99999-9999', $user->celular);
        $this->assertNotNull($user->password);
    }

    #[Test]
    public function test_user_email_must_be_unique()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        User::create([
            'name' => 'User 1',
            'email' => 'duplicate@email.com',
            'password' => bcrypt('Senha@123'),
            'celular' => '(11) 99999-9999',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'name' => 'User 2',
            'email' => 'duplicate@email.com',
            'password' => bcrypt('Senha@123'),
            'celular' => '(11) 98888-8888',
        ]);
    }

    #[Test]
    public function test_user_soft_delete_sets_deleted_at()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => bcrypt('Senha@123'),
            'celular' => '(11) 99999-9999',
        ]);

        $this->assertNull($user->deleted_at);

        $user->delete();

        $this->assertNotNull($user->fresh()->deleted_at);
    }

    #[Test]
    public function test_user_can_be_restored_after_soft_delete()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => bcrypt('Senha@123'),
            'celular' => '(11) 99999-9999',
        ]);

        $user->delete();
        $this->assertNotNull($user->fresh()->deleted_at);

        $user->restore();
        $this->assertNull($user->fresh()->deleted_at);
    }
}