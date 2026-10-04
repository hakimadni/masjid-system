<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin' => 'Admin Legacy',
            'super-admin' => 'Super Admin',
            'ketua-dkm' => 'Ketua DKM',
            'bendahara' => 'Bendahara',
            'sekretaris' => 'Sekretaris',
            'panitia' => 'Panitia Qurban',
            'marbot' => 'Marbot',
            'jamaah' => 'Jamaah',
            'donatur' => 'Donatur',
            'relawan' => 'Relawan Legacy',
        ];

        foreach ($roles as $slug => $name) {
            Role::query()->updateOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $permissions = [
            ['slug' => 'dashboard.view', 'name' => 'View Dashboard'],
            ['slug' => 'mosques.manage', 'name' => 'Manage Mosques'],
            ['slug' => 'users.manage', 'name' => 'Manage Users'],
            ['slug' => 'roles.manage', 'name' => 'Manage Roles'],
            ['slug' => 'finance.view', 'name' => 'View Finance'],
            ['slug' => 'finance.create', 'name' => 'Create Finance Entries'],
            ['slug' => 'finance.update', 'name' => 'Update Finance Entries'],
            ['slug' => 'finance.delete', 'name' => 'Delete Finance Entries'],
            ['slug' => 'finance.approve', 'name' => 'Approve Finance Entries'],
            ['slug' => 'finance.reject', 'name' => 'Reject Finance Entries'],
            ['slug' => 'finance.report', 'name' => 'View Finance Reports'],
            ['slug' => 'finance.proof.view', 'name' => 'View Finance Proofs'],
            ['slug' => 'donation.view', 'name' => 'View Donations'],
            ['slug' => 'donation.create', 'name' => 'Create Donations'],
            ['slug' => 'donation.update', 'name' => 'Update Donations'],
            ['slug' => 'donation.delete', 'name' => 'Delete Donations'],
            ['slug' => 'donation.confirm', 'name' => 'Confirm Donations'],
            ['slug' => 'donation.reject', 'name' => 'Reject Donations'],
            ['slug' => 'donation.report', 'name' => 'View Donation Reports'],
            ['slug' => 'donation.sensitive.view', 'name' => 'View Sensitive Donation Data'],
            ['slug' => 'schedule.view', 'name' => 'View Schedules'],
            ['slug' => 'schedule.manage', 'name' => 'Manage Schedules'],
            ['slug' => 'event.view', 'name' => 'View Events'],
            ['slug' => 'event.manage', 'name' => 'Manage Events'],
            ['slug' => 'event.report', 'name' => 'View Event Reports'],
            ['slug' => 'jamaah.view', 'name' => 'View Jamaah'],
            ['slug' => 'jamaah.manage', 'name' => 'Manage Jamaah'],
            ['slug' => 'jamaah.sensitive.view', 'name' => 'View Sensitive Jamaah Data'],
            ['slug' => 'asset.view', 'name' => 'View Assets'],
            ['slug' => 'asset.manage', 'name' => 'Manage Assets'],
            ['slug' => 'document.view', 'name' => 'View Documents'],
            ['slug' => 'document.manage', 'name' => 'Manage Documents'],
            ['slug' => 'document.delete', 'name' => 'Delete Documents'],
            ['slug' => 'document.private.view', 'name' => 'View Private Documents'],
            ['slug' => 'announcement.view', 'name' => 'View Announcements'],
            ['slug' => 'announcement.manage', 'name' => 'Manage Announcements'],
            ['slug' => 'reports.view', 'name' => 'View Reports (Repo slug)'],
            ['slug' => 'reports.export', 'name' => 'Export Reports (Repo slug)'],
            ['slug' => 'reports.public.manage', 'name' => 'Manage Public Reports (Repo slug)'],
            ['slug' => 'report.view', 'name' => 'View Reports (Guide slug)'],
            ['slug' => 'report.export', 'name' => 'Export Reports (Guide slug)'],
            ['slug' => 'report.public.manage', 'name' => 'Manage Public Reports (Guide slug)'],
            ['slug' => 'public.manage', 'name' => 'Manage Public Portal'],
            ['slug' => 'setting.view', 'name' => 'View Settings'],
            ['slug' => 'setting.manage', 'name' => 'Manage Settings'],
            ['slug' => 'zakat.view', 'name' => 'View Zakat'],
            ['slug' => 'zakat.manage', 'name' => 'Manage Zakat'],
            ['slug' => 'wakaf.view', 'name' => 'View Wakaf'],
            ['slug' => 'wakaf.manage', 'name' => 'Manage Wakaf'],
            ['slug' => 'audit_log.view', 'name' => 'View Audit Logs'],
            ['slug' => 'qurban.view', 'name' => 'View Qurban'],
            ['slug' => 'qurban.manage', 'name' => 'Manage Qurban'],
            ['slug' => 'qurban_savings.manage', 'name' => 'Manage Qurban Savings'],
            ['slug' => 'animals.manage', 'name' => 'Manage Animals'],
            ['slug' => 'participants.manage', 'name' => 'Manage Participants'],
            ['slug' => 'transactions.manage', 'name' => 'Manage Transactions'],
            ['slug' => 'slaughterings.manage', 'name' => 'Manage Slaughterings'],
            ['slug' => 'volunteers.manage', 'name' => 'Manage Volunteers'],
            ['slug' => 'distributions.manage', 'name' => 'Manage Distributions'],
            ['slug' => 'notification.manage', 'name' => 'Manage Notifications'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(['slug' => $permission['slug']], ['name' => $permission['name']]);
        }

        $permissionIds = Permission::query()->pluck('id', 'slug');
        $rolePermissions = [
            'admin' => $permissionIds->keys()->all(),
            'super-admin' => $permissionIds->keys()->all(),
            'ketua-dkm' => [
                'dashboard.view',
                'finance.view',
                'finance.approve',
                'finance.reject',
                'finance.report',
                'finance.proof.view',
                'donation.view',
                'donation.create',
                'donation.confirm',
                'donation.reject',
                'donation.report',
                'donation.sensitive.view',
                'schedule.view',
                'schedule.manage',
                'event.view',
                'event.manage',
                'event.report',
                'jamaah.view',
                'jamaah.manage',
                'jamaah.sensitive.view',
                'asset.view',
                'asset.manage',
                'document.view',
                'document.manage',
                'document.delete',
                'document.private.view',
                'announcement.view',
                'announcement.manage',
                'reports.view',
                'reports.export',
                'audit_log.view',
                'public.manage',
                'setting.view',
                'setting.manage',
                'zakat.view',
                'zakat.manage',
                'wakaf.view',
                'wakaf.manage',
                'qurban.view',
                'notification.manage',
            ],
            'bendahara' => [
                'dashboard.view',
                'finance.view',
                'finance.create',
                'finance.update',
                'finance.approve',
                'finance.reject',
                'finance.report',
                'finance.proof.view',
                'donation.view',
                'donation.create',
                'donation.update',
                'donation.confirm',
                'donation.reject',
                'donation.report',
                'donation.sensitive.view',
                'reports.view',
                'reports.export',
                'audit_log.view',
                'setting.view',
                'qurban.view',
            ],
            'sekretaris' => [
                'dashboard.view',
                'schedule.view',
                'schedule.manage',
                'event.view',
                'event.manage',
                'event.report',
                'jamaah.view',
                'document.view',
                'document.manage',
                'document.delete',
                'document.private.view',
                'announcement.view',
                'announcement.manage',
                'reports.view',
                'reports.export',
                'setting.view',
                'setting.manage',
                'zakat.view',
                'wakaf.view',
                'audit_log.view',
                'public.manage',
                'notification.manage',
            ],
            'panitia' => [
                'dashboard.view',
                'finance.view',
                'finance.create',
                'donation.view',
                'donation.create',
                'schedule.view',
                'schedule.manage',
                'event.view',
                'event.manage',
                'qurban.view',
                'qurban.manage',
            ],
            'marbot' => [
                'dashboard.view',
                'schedule.view',
                'schedule.manage',
                'asset.view',
                'asset.manage',
                'event.view',
                'announcement.view',
            ],
            'jamaah' => [
                'dashboard.view',
            ],
            'donatur' => [
                'dashboard.view',
            ],
            'relawan' => [
                'dashboard.view',
                'schedule.view',
                'asset.view',
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            $role = Role::query()->where('slug', $roleSlug)->firstOrFail();
            $role->permissions()->sync(
                collect($permissionSlugs)
                    ->map(fn (string $slug): ?int => $permissionIds->get($slug))
                    ->filter()
                    ->values()
                    ->all()
            );
        }
    }
}
