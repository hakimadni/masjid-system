<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminMvpSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AdminEventsModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminMvpSeeder::class);
    }

    public function test_sekretaris_can_open_events_module_with_summary_data(): void
    {
        $user = $this->userWithRole('sekretaris');
        $mosqueId = $user->mosque_id;

        Event::query()->create([
            'mosque_id' => $mosqueId,
            'title' => 'Pelatihan Relawan',
            'start_at' => now()->addDays(10)->setTime(9, 0),
            'end_at' => now()->addDays(10)->setTime(11, 0),
            'location' => 'Ruang Serbaguna',
            'pic_name' => 'Koordinator Panitia',
            'status' => 'draft',
            'notes' => 'Sesi orientasi relawan baru.',
        ]);

        $response = $this->actingAs($user)->get(route('events.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Events/Index')
            ->where('summary.records_total', Event::query()->where('mosque_id', $mosqueId)->count())
            ->has('events.data')
        );
    }

    public function test_sekretaris_can_create_and_publish_event(): void
    {
        $user = $this->userWithRole('sekretaris');

        $createResponse = $this->actingAs($user)->post(route('events.store'), [
            'title' => 'Pelatihan Relawan Baru',
            'start_at' => now()->addDays(8)->setTime(9, 0)->format('Y-m-d H:i:s'),
            'end_at' => now()->addDays(8)->setTime(11, 0)->format('Y-m-d H:i:s'),
            'location' => 'Ruang Serbaguna',
            'pic_name' => 'Koordinator Panitia',
            'status' => 'draft',
            'notes' => 'Orientasi relawan operasional acara.',
        ]);

        $createResponse->assertRedirect();

        $event = Event::query()
            ->where('mosque_id', $user->mosque_id)
            ->where('title', 'Pelatihan Relawan Baru')
            ->latest('id')
            ->first();

        $this->assertNotNull($event);
        $this->assertSame('draft', $event->status);

        $publishResponse = $this->actingAs($user)->patch(route('events.update-status', $event), [
            'status' => 'published',
        ]);

        $publishResponse->assertRedirect();

        $event->refresh();

        $this->assertSame('published', $event->status);
    }

    private function userWithRole(string $roleSlug): User
    {
        $user = User::factory()->create([
            'mosque_id' => Mosque::query()->where('slug', 'default-masjid')->value('id'),
        ]);

        $user->roles()->sync([Role::query()->where('slug', $roleSlug)->value('id')]);

        return $user;
    }
}
