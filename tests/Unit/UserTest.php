<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_role_returns_true_when_role_matches()
    {
        // Arrange
        $user = User::factory()->create([
            "role" => "admin",
        ]);

        // Act & Assert
        $this->assertTrue($user->hasRole("admin"));
    }

    public function test_user_has_role_returns_false_when_role_doesnt_match()
    {
        // Arrange
        $user = User::factory()->create([
            "role" => "user",
        ]);

        // Act & Assert
        $this->assertFalse($user->hasRole("admin"));
    }

    public function test_fillable_attributes()
    {
        // Arrange
        $user = new User();

        // Act & Assert
        $this->assertEquals(
            ["name", "email", "password", "role"],
            $user->getFillable()
        );
    }

    public function test_hidden_attributes()
    {
        // Arrange
        $user = new User();

        // Act & Assert
        $this->assertEquals(["password", "remember_token"], $user->getHidden());
    }

    public function test_casts_attributes()
    {
        // Arrange
        $user = new User();

        // Act
        $casts = $user->getCasts();

        // Assert
        $this->assertArrayHasKey("email_verified_at", $casts);
        $this->assertArrayHasKey("password", $casts);
        $this->assertEquals("datetime", $casts["email_verified_at"]);
        $this->assertEquals("hashed", $casts["password"]);
    }
}
