<?php

namespace Tests\Feature\Admin;

use App\Models\Mosque;
use App\Models\PrayerSchedule;
use App\Models\Role;
use App\Models\ServiceSchedule;
use App\Models\User;
use Database\Seeders\AdminMvpSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScheduleFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminMvpSeeder::class);
    }

    public function test_marbot_can_create_prayer_schedule(): void
    {
        $user = $this->userWithRole('marbot');

        $response = $this->actingAs($user)->post(route('schedules.store-prayer'), [
            'kind' => 'prayer',
            'schedule_date' => now()->addDays(7)->toDateString(),
            'prayer_name' => 'subuh',
            'prayer_time' => '04:45',
            'imam_name' => 'Ustadz Salman',
            'muadzin_name' => 'Bapak Fikri',
            'status' => 'draft',
            'notes' => 'Menunggu konfirmasi imam.',
        ]);

        $response->assertRedirect();

        $schedule = PrayerSchedule::query()
            ->where('mosque_id', $user->mosque_id)
            ->where('prayer_name', 'subuh')
            ->latest('id')
            ->first();

        $this->assertNotNull($schedule);
        $this->assertSame('draft', $schedule->status);
        $this->assertSame('Ustadz Salman', $schedule->imam_name);
    }

    public function test_marbot_can_publish_existing_prayer_schedule(): void
    {
        $user = $this->userWithRole('marbot');

        $schedule = PrayerSchedule::query()->create([
            'mosque_id' => $user->mosque_id,
            'schedule_date' => now()->addDays(2)->toDateString(),
            'prayer_name' => 'isya',
            'prayer_time' => '19:10',
            'imam_name' => 'Ustadz Zaki',
            'muadzin_name' => 'Bapak Ilham',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('schedules.prayer-status', $schedule), [
            'status' => 'published',
        ]);

        $response->assertRedirect();

        $schedule->refresh();

        $this->assertSame('published', $schedule->status);
    }

    public function test_panitia_can_create_and_complete_service_schedule(): void
    {
        $user = $this->userWithRole('panitia');

        $createResponse = $this->actingAs($user)->post(route('schedules.store-service'), [
            'kind' => 'service',
            'title' => 'Petugas Kajian Remaja',
            'role_type' => 'kegiatan',
            'person_name' => 'Tim Remaja Masjid',
            'location' => 'Aula Samping',
            'scheduled_at' => now()->addDays(5)->setTime(18, 30)->format('Y-m-d H:i:s'),
            'status' => 'published',
            'notes' => 'Siapkan registrasi dan dokumentasi.',
        ]);

        $createResponse->assertRedirect();

        $schedule = ServiceSchedule::query()
            ->where('mosque_id', $user->mosque_id)
            ->where('title', 'Petugas Kajian Remaja')
            ->latest('id')
            ->first();

        $this->assertNotNull($schedule);
        $this->assertSame('published', $schedule->status);

        $updateResponse = $this->actingAs($user)->patch(route('schedules.service-status', $schedule), [
            'status' => 'completed',
        ]);

        $updateResponse->assertRedirect();

        $schedule->refresh();

        $this->assertSame('completed', $schedule->status);
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
