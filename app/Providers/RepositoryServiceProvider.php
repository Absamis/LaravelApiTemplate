<?php

namespace App\Providers;

use App\Repositories\UserRepository;
use App\Interfaces\IAuthRepository;
use App\Interfaces\IUserRepository;
use App\Repositories\AuthRepository;
use App\Services\VerificationService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(IAuthRepository::class, AuthRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->scoped(VerificationService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
