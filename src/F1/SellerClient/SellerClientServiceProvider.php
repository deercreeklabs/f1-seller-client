<?php namespace F1\SellerClient;

use Illuminate\Support\ServiceProvider;

class SellerClientServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('f1/seller-client');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('SellerClient', 'F1\SellerClient\SellerClient');
        });
        $this->app['seller-client'] = $this->app->share(function($app)
        {
            return new SellerClient;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('seller-client');
    }

}
