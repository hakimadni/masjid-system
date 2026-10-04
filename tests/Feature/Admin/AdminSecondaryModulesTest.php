<?php

namespace Tests\Feature\Admin;

use App\Models\Announcement;
use App\Models\Asset;
use App\Models\Event;
use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminMvpSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AdminSecondaryModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminMvpSeeder::class);
    }

    public function test_sekretaris_can_create_and_publish_announcement(): void
    {
        $user = $this->userWithRole('sekretaris');

        $createResponse = $this->actingAs($user)->post(route('announcements.store'), [
            'title' => 'Perubahan Jadwal Kajian',
            'content' => 'Kajian malam Jumat dimulai ba da Maghrib pekan ini.',
            'status' => 'published',
            'published_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $createResponse->assertRedirect();

        $announcement = Announcement::query()
            ->where('mosque_id', $user->mosque_id)
            ->where('title', 'Perubahan Jadwal Kajian')
            ->latest('id')
            ->first();

        $this->assertNotNull($announcement);
        $this->assertSame('published', $announcement->status);
        $this->assertNotNull($announcement->published_at);

        $archiveResponse = $this->actingAs($user)->patch(route('announcements.update-status', $announcement), [
            'status' => 'archived',
        ]);

        $archiveResponse->assertRedirect();

        $announcement->refresh();

        $this->assertSame('archived', $announcement->status);
    }

    public function test_marbot_can_create_and_mark_asset_for_maintenance(): void
    {
        $user = $this->userWithRole('marbot');

        $createResponse = $this->actingAs($user)->post(route('assets.store'), [
            'name' => 'Speaker Serambi',
            'category' => 'Audio',
            'location' => 'Serambi',
            'quantity' => 2,
            'condition' => 'perlu_perbaikan',
            'status' => 'active',
            'notes' => 'Salah satu channel kadang putus.',
        ]);

        $createResponse->assertRedirect();

        $asset = Asset::query()
            ->where('mosque_id', $user->mosque_id)
            ->where('name', 'Speaker Serambi')
            ->latest('id')
            ->first();

        $this->assertNotNull($asset);
        $this->assertSame('active', $asset->status);

        $maintenanceResponse = $this->actingAs($user)->patch(route('assets.update-status', $asset), [
            'status' => 'maintenance',
        ]);

        $maintenanceResponse->assertRedirect();

        $asset->refresh();

        $this->assertSame('maintenance', $asset->status);
    }

    public function test_bendahara_can_open_reports_page_with_expected_sections(): void
    {
        $user = $this->userWithRole('bendahara');

        Event::query()->create([
            'mosque_id' => $user->mosque_id,
            'title' => 'Aksi Sosial Jumat',
            'start_at' => now()->addDays(4)->setTime(8, 0),
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Reports/Index')
            ->has('summary')
            ->has('recentFinance')
            ->has('campaignBreakdown')
            ->has('upcomingEvents')
            ->has('assetConditions')
        );
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
