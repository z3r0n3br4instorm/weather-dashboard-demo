<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard()
    {
        // Arrange - Skip creating controller/route, just testing middleware
        $adminUser = User::factory()->create([
            "role" => "admin",
        ]);

        // Set up a test route that uses the role middleware
        Route::middleware(["auth", "role:admin"])
            ->get("/admin/dashboard", function () {
                return view("admin.dashboard", [
                    "activeSensors" => 10,
                    "inactiveSensors" => 2,
                    "readingsToday" => 500,
                    "adminUsers" => 3,
                ]);
            })
            ->name("admin.dashboard");

        // Act
        $response = $this->actingAs($adminUser)->get("/admin/dashboard");

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs("admin.dashboard");
        $response->assertSee("Administration Dashboard");
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        // Arrange
        $regularUser = User::factory()->create([
            "role" => "user",
        ]);

        // Set up a test route that uses the role middleware
        Route::middleware(["auth", "role:admin"])
            ->get("/admin/dashboard", function () {
                return "admin dashboard";
            })
            ->name("admin.dashboard");

        // Act
        $response = $this->actingAs($regularUser)->get("/admin/dashboard");

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect("/");
        $this->assertEquals("Unauthorized access", session("error"));
    }
}
