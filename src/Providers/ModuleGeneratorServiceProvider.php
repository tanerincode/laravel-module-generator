<?php


namespace TanerInCode\ModuleGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use TanerInCode\ModuleGenerator\Commands\ControllerGenerator;
use TanerInCode\ModuleGenerator\Commands\FacadeGenerator;
use TanerInCode\ModuleGenerator\Commands\InterfaceGenerator;
use TanerInCode\ModuleGenerator\Commands\ModelGenerator;
use TanerInCode\ModuleGenerator\Commands\ModuleGenerator;
use TanerInCode\ModuleGenerator\Commands\ProviderGenerator;
use TanerInCode\ModuleGenerator\Commands\RepositoryGenerator;
use TanerInCode\ModuleGenerator\Commands\ServiceGenerator;

class ModuleGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ( $this->app->runningInConsole() )
        {
            $this->publishes([
                __DIR__.'/../../config/mgenerator.php' => config_path('mgenerator.php'),
            ], 'mgenerator.config');


            $this->commands([
                ControllerGenerator::class,
                FacadeGenerator::class,
                InterfaceGenerator::class,
                ProviderGenerator::class,
                RepositoryGenerator::class,
                ServiceGenerator::class,
                ModuleGenerator::class
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    	
    }
}
