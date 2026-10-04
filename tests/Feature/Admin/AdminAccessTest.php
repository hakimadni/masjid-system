<?php

namespace Tests\Feature\Admin;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authenticated_user_can_open_dashboard(): void
    {
        $user = User::factory()->create([
            'mosque_id' => Mosque::query()->where('slug', 'default-masjid')->value('id'),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_bendahara_can_access_finance_module(): void
    {
        $user = $this->userWithRole('bendahara');

        $response = $this->actingAs($user)->get(route('finance.index'));

        $response->assertOk();
    }

    public function test_marbot_cannot_access_finance_module(): void
    {
        $user = $this->userWithRole('marbot');

        $response = $this->actingAs($user)->get(route('finance.index'));

        $response->assertForbidden();
    }

    public function test_marbot_can_access_schedule_module(): void
    {
        $user = $this->userWithRole('marbot');

        $response = $this->actingAs($user)->get(route('schedules.index'));

        $response->assertOk();
    }

    private function userWithRole(string $roleSlug): User
    {
        $user = User::factory()->create([
            'mosque_id' => Mosque::query()->where('slug', 'default-masjid')->value('id'),
        ]);

        $roleId = Role::query()->where('slug', $roleSlug)->value('id');
        $user->roles()->sync([$roleId]);

        return $user;
    }
}
