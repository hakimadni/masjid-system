<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'action' => ['nullable', 'string', 'max:50'],
            'entity' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $query = AuditLog::query()
            ->with('user:id,name,email')
            ->whereHas('user', fn($q) => $q->where('mosque_id', $mosqueId));

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('action', 'like', '%'.$search.'%')
                    ->orWhere('entity_type', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%'.$search.'%'));
            });
        }

        if ($filters['action'] ?? null) {
            $query->where('action', $filters['action']);
        }

        if ($filters['entity'] ?? null) {
            $query->where('entity_type', 'like', '%'.$filters['entity'].'%');
        }

        if ($filters['start_date'] ?? null) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if ($filters['end_date'] ?? null) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $logs = $query->latest()->paginate(50)->withQueryString();

        $actions = AuditLog::whereHas('user', fn($q) => $q->where('mosque_id', $mosqueId))
            ->distinct()->pluck('action')->filter()->values();

        return Inertia::render('Admin/AuditLogs/Index', [
            'filters' => $filters,
            'logs' => $logs->through(fn (AuditLog $log): array => [
                'id' => $log->id,
                'action' => $log->action,
                'entity_type' => class_basename($log->entity_type),
                'entity_id' => $log->entity_id,
                'user_name' => $log->user?->name ?? 'System',
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at->toDateTimeString(),
            ]),
            'actions' => $actions,
        ]);
    }
}