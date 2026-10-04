<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'mosque_id',
        'name',
        'email',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function qurbanSavings(): HasMany
    {
        return $this->hasMany(QurbanSaving::class);
    }

    private function normalizedSlug(string $slug): string
    {
        return strtolower(trim($slug));
    }

    /**
     * @return list<string>
     */
    private function permissionAliases(string $slug): array
    {
        $normalized = $this->normalizedSlug($slug);
        $aliases = [$normalized];

        if (str_starts_with($normalized, 'report.')) {
            $aliases[] = 'reports.'.substr($normalized, 7);
        }

        if (str_starts_with($normalized, 'reports.')) {
            $aliases[] = 'report.'.substr($normalized, 8);
        }

        return array_values(array_unique($aliases));
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function permissions(): Collection
    {
        return $this->roles
            ->loadMissing('permissions:id,slug')
            ->pluck('permissions')
            ->flatten()
            ->pluck('slug')
            ->map(fn ($slug) => $this->normalizedSlug((string) $slug))
            ->unique()
            ->values();
    }

    public function hasPermissionTo(string $slug): bool
    {
        if ($this->hasRole('super-admin') || $this->hasRole('admin')) {
            return true;
        }

        $userPermissions = $this->permissions();

        foreach ($this->permissionAliases($slug) as $candidate) {
            if ($userPermissions->contains($candidate)) {
                return true;
            }
        }

        return false;
    }
}
