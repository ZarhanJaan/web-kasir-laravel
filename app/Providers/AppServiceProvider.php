<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $settingsPath = storage_path('app/settings.json');
        $storeName = 'web';
        if (File::exists($settingsPath)) {
            $settings = json_decode(File::get($settingsPath), true);
            if (isset($settings['store_name'])) {
                $storeName = $settings['store_name'];
            }
        }
        View::share('store_name', $storeName);
    }
}
