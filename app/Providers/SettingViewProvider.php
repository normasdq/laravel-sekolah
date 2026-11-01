<?php

namespace App\Providers;

use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingViewProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Membuat variabel $settings tersedia di SEMUA file view Blade,
        // KECUALI halaman admin (karena admin butuh format Array, bukan Object)
        View::composer('*', function ($view) {
            if (! str_contains($view->getName(), 'admin.')) {
                $view->with('settings', app(SettingService::class));
            }
        });
    }
}
