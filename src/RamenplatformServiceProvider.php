<?php

namespace Ordent\Ramenplatform;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
class RamenplatformServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
        __DIR__.'ramen.php' => config_path('ramen.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Dingo\Api\Provider\LaravelServiceProvider::class);
        $this->app->register(\Intervention\Image\ImageServiceProvider::class);
        $this->app->register(\Alaouy\Youtube\YoutubeServiceProvider::class);
        $this->app->register(\Dawson\Youtube\YoutubeServiceProvider::class);
        AliasLoader::getInstance()->alias('RApi', \Dingo\Api\Facade\Route::class);
        AliasLoader::getInstance()->alias('Intervention', \Intervention\Image\Facades\Image::class);
        AliasLoader::getInstance()->alias('Youtube', \Dawson\Youtube\YoutubeFacade::class);
    }
}
