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
        $this->loadRoutesFrom(__DIR__.'/routes.php');
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
        AliasLoader::getInstance()->alias('RApi', \Dingo\Api\Facade\Route::class);
        AliasLoader::getInstance()->alias('Intervention', \Intervention\Image\Facades\Image::class);

        if(!file_exists(public_path("/storage"))){
          //symlink(storage_path("/app/public"), public_path("/storage"));
        }
    }
}
