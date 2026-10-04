<?php

namespace Database\Seeders;

use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
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

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@masjid.local'],
            [
                'mosque_id' => $defaultMosqueId,
                'name' => 'Admin Masjid',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $panitiaUsers = User::factory(2)->create();
        $relawanUsers = User::factory(10)->create();
        $donaturUsers = User::factory(20)->create();

        User::query()->whereNull('mosque_id')->update(['mosque_id' => $defaultMosqueId]);

        $admin->roles()->syncWithoutDetaching([
            Role::query()->where('slug', 'admin')->firstOrFail()->id,
            Role::query()->where('slug', 'super-admin')->firstOrFail()->id,
        ]);

        $panitiaRoleId = Role::query()->where('slug', 'panitia')->firstOrFail()->id;
        $marbotRoleId = Role::query()->where('slug', 'marbot')->firstOrFail()->id;
        $donaturRoleId = Role::query()->where('slug', 'donatur')->firstOrFail()->id;

        $panitiaUsers->each(fn (User $user) => $user->roles()->syncWithoutDetaching([$panitiaRoleId]));
        $relawanUsers->each(fn (User $user) => $user->roles()->syncWithoutDetaching([$marbotRoleId]));
        $donaturUsers->each(fn (User $user) => $user->roles()->syncWithoutDetaching([$donaturRoleId]));
    }
}
