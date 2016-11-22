<?php
/**
 * Created by Damián Imrich / Haze Studio.
 * Date: 22.11.2016
 * Time: 14:45
 */

namespace HazeStudio\LaravelGoPaySDK;

use Illuminate\Support\ServiceProvider;

class GopayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('gopay.php'),
        ]);

        require_once __DIR__.'/../vendor/autoload.php'; //Develop
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config.php', 'gopay'
        );

        $this->app['gopay-sdk'] = $this->app->share(function($app)
        {
            return new GoPaySDK($app);
        });
    }
}