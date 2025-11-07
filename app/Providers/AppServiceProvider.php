<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\AppointmentRepositoryInterface;
use App\Repositories\AppointmentRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Aplica Dependency Injection configurando os bindings
     * das interfaces para suas implementações concretas.
     */
    public function register(): void
    {
        // Repository Pattern - Dependency Injection
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
