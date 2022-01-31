<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Schema::defaultStringLength(191);
        $languages = ['ar', 'en'];
        App::setLocale('ar');
        date_default_timezone_set('Asia/Riyadh');


        $lang = request()->header('lang');
        if ($lang) {
            if (in_array($lang, $languages)) {
                App::setLocale($lang);
            } else {
                App::setLocale('ar');
            }
        }

//        ini_set('max_execution_time', -1); //6 minutes
//        ini_set('post_max_size', '200M');
//        ini_set('upload_max_filesize', '200M');
        dd(ini_get('upload_max_filesize'));

    }
}
