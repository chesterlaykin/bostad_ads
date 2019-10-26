<?php 

namespace Joels\Bostad;

use Illuminate\Support\ServiceProvider;

class BostadServiceProvider extends ServiceProvider {

   /**
    * Bootstrap the application services.
    *
    * @return void
    */
   public function boot()
   {
        $this->loadViewsFrom(__DIR__ . '/views', 'joels/bostad');
   }

   /**
    * Register the application services.
    *
    * @return void
    */
   public function register()
   {
     
       //Register Our Package routes
    //    include __DIR__.'/routes.php';
 
       // Let Laravel Ioc Container know about our Controller
       $this->app->make('Joels\Bostad\BostadController'); 
     
  }

}