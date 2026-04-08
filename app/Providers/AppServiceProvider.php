<?php

namespace App\Providers;

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
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                \Illuminate\Support\Facades\View::share('global_app_title', \App\Models\SystemSetting::get('app_title', 'Gestión de Lotes'));
                \Illuminate\Support\Facades\View::share('global_app_logo', \App\Models\SystemSetting::get('app_logo_path'));
            } else {
                \Illuminate\Support\Facades\View::share('global_app_title', 'Gestión de Lotes');
                \Illuminate\Support\Facades\View::share('global_app_logo', null);
            }
        } catch (\Exception $e) {
            // Ignore DB errors during testing or migrations
            \Illuminate\Support\Facades\View::share('global_app_title', 'Gestión de Lotes');
            \Illuminate\Support\Facades\View::share('global_app_logo', null);
        }
    }
}
