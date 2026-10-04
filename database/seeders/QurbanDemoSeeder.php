<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Distribution;
use App\Models\Mosque;
use App\Models\Participant;
use App\Models\QurbanSaving;
use App\Models\Slaughtering;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QurbanDemoSeeder extends Seeder
{
    private const DEFAULT_MOSQUE_SLUG = 'default-masjid';

    public function run(): void
    {
        $defaultMosqueId = Mosque::query()->firstOrCreate(
            ['slug' => self::DEFAULT_MOSQUE_SLUG],
            [
                'name' => 'Masjid Utama',
                'address' => null,
                'is_active' => true,
            ]
        )->id;

        $users = User::query()->limit(20)->get();

        foreach ($users as $user) {
            $saving = QurbanSaving::query()->create([
                'mosque_id' => $defaultMosqueId,
                'user_id' => $user->id,
                'target_amount' => 3000000,
                'current_balance' => rand(500000, 3500000),
                'status' => 'active',
                'eligible_kambing' => false,
                'eligible_sapi_share' => false,
            ]);

            $saving->update([
                'eligible_kambing' => (float) $saving->current_balance >= 2500000,
                'eligible_sapi_share' => (float) $saving->current_balance >= 3000000,
            ]);
        }

        $cows = collect(range(1, 8))->map(fn () => Animal::query()->create([
            'mosque_id' => $defaultMosqueId,
            'type' => 'sapi',
            'weight' => rand(250, 400),
            'price' => rand(18000000, 25000000),
            'supplier' => 'Supplier A',
            'location' => 'Area Masjid',
            'slaughter_type' => 'onsite',
            'status' => 'available',
            'qr_token' => (string) Str::uuid(),
        ]));

        collect(range(1, 14))->each(fn () => Animal::query()->create([
            'mosque_id' => $defaultMosqueId,
            'type' => 'kambing',
            'weight' => rand(20, 40),
            'price' => rand(2500000, 4000000),
            'supplier' => 'Supplier B',
            'location' => 'Area Masjid',
            'slaughter_type' => 'onsite',
            'status' => 'available',
            'qr_token' => (string) Str::uuid(),
        ]));

        $savings = QurbanSaving::query()->limit(56)->get();

        $cowIdx = 0;
        foreach ($savings as $idx => $saving) {
            $animal = $cows[$cowIdx % $cows->count()];
            $slot = ($idx % 7) + 1;
            Participant::query()->create([
                'mosque_id' => $defaultMosqueId,
                'animal_id' => $animal->id,
                'qurban_saving_id' => $saving->id,
                'name' => 'Peserta '.($idx + 1),
                'phone' => '08123'.str_pad((string) $idx, 6, '0', STR_PAD_LEFT),
                'amount_due' => 3000000,
                'amount_paid' => rand(500000, 3000000),
                'payment_status' => 'partial',
                'slot_number' => $slot,
            ]);

            if ($slot === 7) {
                $animal->update(['status' => 'assigned']);
                $cowIdx++;
            }
        }

        $volunteers = User::query()->limit(10)->get()->map(fn (User $user) => Volunteer::query()->create([
            'mosque_id' => $defaultMosqueId,
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => '08111'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
            'role_type' => 'administrasi',
            'qr_token' => (string) Str::uuid(),
            'is_active' => true,
        ]));

        $cows->take(3)->each(function (Animal $cow) use ($defaultMosqueId, $volunteers): void {
            $slaughtering = Slaughtering::query()->create([
                'mosque_id' => $defaultMosqueId,
                'animal_id' => $cow->id,
                'date' => now()->toDateString(),
                'location' => 'Masjid Utama',
                'distribution_status' => 'pending',
            ]);

            $slaughtering->volunteers()->sync(
                $volunteers->take(7)->mapWithKeys(fn (Volunteer $v): array => [$v->id => ['team_role' => 'administrasi']])->all()
            );
        });

        Distribution::query()->create([
            'mosque_id' => $defaultMosqueId,
            'recipient_name' => 'Mustahik 1',
            'recipient_type' => 'mustahik',
            'package_count' => 100,
            'status' => 'pending',
        ]);
    }
}
