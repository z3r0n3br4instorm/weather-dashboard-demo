<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;

class CheckRoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_allows_access_when_user_has_required_role()
    {
        // Arrange
        $user = User::factory()->create([
            "role" => "admin",
        ]);

        // Define a test route with the middleware
        Route::middleware(["role:admin"])->get("/admin-test", function () {
            return "Admin access granted";
        });

        // Act
        $response = $this->actingAs($user)->get("/admin-test");

        // Assert
        $response->assertStatus(200);
        $response->assertSee("Admin access granted");
    }

    public function test_middleware_redirects_when_user_doesnt_have_required_role()
    {
        // Arrange
        $user = User::factory()->create([
            "role" => "user",
        ]);

        // Define a test route with the middleware
        Route::middleware(["role:admin"])->get("/admin-test", function () {
            return "Admin access granted";
        });

        // Act
        $response = $this->actingAs($user)->get("/admin-test");

        // Assert
        $response->assertStatus(302); // Redirect
        $response->assertRedirect("/");
        $this->assertEquals("Unauthorized access", session("error"));
    }

    public function test_middleware_redirects_when_user_not_authenticated()
    {
        // Define a test route with the middleware
        Route::middleware(["role:admin"])->get("/admin-test", function () {
            return "Admin access granted";
        });

        // Act
        $response = $this->get("/admin-test");

        // Assert
        $response->assertStatus(302); // Redirect
        $response->assertRedirect("/");
        $this->assertEquals("Unauthorized access", session("error"));
    }

    public function test_direct_middleware_functionality()
    {
        // Arrange
        $middleware = new CheckRole();
        $user = User::factory()->create(["role" => "admin"]);

        $request = Request::create("/admin-test", "GET");
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Act
        $response = $middleware->handle(
            $request,
            function ($req) {
                return response("Next middleware called");
            },
            "admin"
        );

        // Assert
        $this->assertEquals("Next middleware called", $response->getContent());
    }
}
