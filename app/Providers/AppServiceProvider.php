<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use App\Models\Qris;

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

        // We wrap database calls in a try-catch so artisan commands don't break if the table doesn't exist
        $qrisImage = null;
        $qrisName = null;
        try {
            $qris = Qris::first();
            if ($qris) {
                $qrisImage = $qris->image_path;
                $qrisName = $qris->name;
            }
        } catch (\Exception $e) {
            // Do nothing if table doesn't exist yet
        }

        View::share('store_name', $storeName);
        View::share('qris_image', $qrisImage);
        View::share('qris_name', $qrisName);
    }
}
