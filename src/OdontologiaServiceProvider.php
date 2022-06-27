<?php

namespace Juarismi\Odontologia;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Juarismi\Base\Http\Middleware\Cors;

class OdontologiaServiceProvider extends ServiceProvider
{

	public function boot(){

        $this->app->singleton('cors', function ($app) {
            return new Cors();
        });

	    $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
	   	$this->registerRoutes();
    }

    public function register(){
    	
    }

    protected function registerRoutes(){
    	Route::group([ 'prefix' => 'api/v1' ], function(){
    		$this->loadRoutesFrom(__DIR__.'/Routes/api.php', 'jBase');
    	});
    }

}