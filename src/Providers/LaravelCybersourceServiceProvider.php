<?php namespace JustGeeky\LaravelCybersource\Providers;

use JustGeeky\LaravelCybersource\Configs\Factory as ConfigsFactory;
use JustGeeky\LaravelCybersource\Cybersource;
use JustGeeky\LaravelCybersource\SOAPClient;
use JustGeeky\LaravelCybersource\SOAPClientFactory;
use JustGeeky\LaravelCybersource\SOAPRequester;
use Illuminate\Support\ServiceProvider;

class LaravelCybersourceServiceProvider extends ServiceProvider {

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../app/config/cybersource.php' => config_path('cybersource.php'),
            __DIR__.'/../../app/config/cybersource-profiles.php' => config_path('cybersource-profiles.php'),
            __DIR__.'/../../app/views/cybersource/secure' => resource_path('views/cybersource/secure'),
            __DIR__.'/../../app/views/cybersource/assets' => public_path('cybersource/assets'),
        ], 'cybersource');
        $this->loadRoutesFrom(__DIR__.'/../../app/routes/cybersource.php');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('cybersource', function() {
            $configs = (new ConfigsFactory())->getFromConfigFile();
            $client = new SOAPClient($configs, []);
            $factory = new SOAPClientFactory();
            $requester = new SOAPRequester($client, $factory);
            return new Cybersource($requester);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('cybersource');
    }

}
