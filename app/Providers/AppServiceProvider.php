<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Vercel terminates SSL at edge — force HTTPS so Laravel generates
        // correct https:// URLs for redirects, form actions, and cookies.
        // Applies to all non-local environments (production + preview deployments).
        if (!$this->app->environment('local')) {
            URL::forceScheme('https');
        }

        Paginator::useTailwind();

        Gate::define('admin-only', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('mahasiswa-only', function (User $user) {
            return $user->role === 'mahasiswa';
        });
    }
}
