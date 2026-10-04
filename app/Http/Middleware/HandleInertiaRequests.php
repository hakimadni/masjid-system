<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user) {
            $user->loadMissing(['roles.permissions:id,slug', 'roles:id,slug', 'mosque:id,name,slug']);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'role_slugs' => $user?->roles->pluck('slug')->values()->all() ?? [],
                'permission_slugs' => $user?->permissions()->all() ?? [],
                'active_mosque' => $user?->mosque?->only(['id', 'name', 'slug']),
            ],
            'navigation' => [
                'primary' => [
                    'dashboard',
                    'finance.index',
                    'donations.index',
                    'schedules.index',
                    'events.index',
                    'jamaah.index',
                    'assets.index',
                    'documents.index',
                    'announcements.index',
                    'reports.index',
                    'settings.index',
                ],
                'qurban' => [
                    'qurban.dashboard',
                    'savings.index',
                    'animals.index',
                    'participants.index',
                    'slaughterings.index',
                    'volunteers.index',
                    'distributions.index',
                ],
            ],
        ];
    }
}
