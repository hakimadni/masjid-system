<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait InteractsWithAdminTables
{
    protected function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    protected function tableHasColumn(string $table, string $column): bool
    {
        return $this->tableExists($table) && Schema::hasColumn($table, $column);
    }

    protected function filterKnownColumns(string $table, array $payload): array
    {
        if (! $this->tableExists($table)) {
            return [];
        }

        return Arr::only($payload, Schema::getColumnListing($table));
    }

    protected function emptyPaginator(string $path, array $query = [], int $perPage = 10): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage, 1, [
            'path' => $path,
            'query' => $query,
        ]);
    }

    protected function paginate(Builder $query, int $page, int $perPage, string $path, array $queryParams = []): LengthAwarePaginator
    {
        $total = (clone $query)->count();
        $items = $query->forPage($page, $perPage)->get();

        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => $path,
            'query' => $queryParams,
        ]);
    }

    protected function prefixedReference(string $prefix): string
    {
        return sprintf('%s-%s%s', $prefix, now()->format('YmdHis'), Str::upper(Str::random(4)));
    }

    protected function sumTransactionAmounts(string $referencePrefix, array $types, ?callable $callback = null): float
    {
        if (! $this->tableExists('transactions')) {
            return 0.0;
        }

        $query = DB::table('transactions')
            ->where('reference_no', 'like', $referencePrefix.'-%')
            ->whereIn('type', $types);

        if ($callback !== null) {
            $callback($query);
        }

        return (float) $query->sum('amount');
    }
}
