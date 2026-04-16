<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_be_created_with_valid_data()
    {
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

    /** @test */
    public function user_email_must_be_unique()
    {
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

    /** @test */
    public function user_soft_delete_sets_deleted_at()
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

    /** @test */
    public function user_can_be_restored_after_soft_delete()
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