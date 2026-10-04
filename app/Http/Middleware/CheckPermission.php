<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $permissions = explode('|', $permission);
        $hasPermission = false;

        foreach ($permissions as $p) {
            if ($user->hasPermissionTo($p)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403, 'Unauthorized. Permission required: ' . str_replace('|', ' or ', $permission));
        }

        return $next($request);
    }
}
