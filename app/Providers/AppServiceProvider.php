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
        $qrisImage = null;
        $qrisName = null;
        if (File::exists($settingsPath)) {
            $settings = json_decode(File::get($settingsPath), true);
            if (isset($settings['store_name'])) {
                $storeName = $settings['store_name'];
            }
            if (isset($settings['qris_image'])) {
                $qrisImage = $settings['qris_image'];
            }
            if (isset($settings['qris_name'])) {
                $qrisName = $settings['qris_name'];
            }
        }
        View::share('store_name', $storeName);
        View::share('qris_image', $qrisImage);
        View::share('qris_name', $qrisName);
    }
}
