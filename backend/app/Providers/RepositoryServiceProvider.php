<?php

namespace App\Providers;

use App\Repositories\RefreshTokenRepository\RefreshTokenRepository;
use App\Repositories\RefreshTokenRepository\RefreshTokenRepositoryInterface;
use App\Repositories\UserRepository\UserRepository;
use App\Repositories\UserRepository\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RefreshTokenRepositoryInterface::class, RefreshTokenRepository::class);
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
