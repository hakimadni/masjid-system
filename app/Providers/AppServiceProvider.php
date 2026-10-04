<?php

namespace App\Providers;

use App\Repositories\Contracts\AnimalRepositoryInterface;
use App\Repositories\Contracts\ParticipantRepositoryInterface;
use App\Repositories\Eloquent\AnimalRepository;
use App\Repositories\Eloquent\ParticipantRepository;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AnimalRepositoryInterface::class, AnimalRepository::class);
        $this->app->bind(ParticipantRepositoryInterface::class, ParticipantRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
