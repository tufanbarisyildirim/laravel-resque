<?php namespace Awellis13\Resque\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class RedisServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('redis', function ($app) {
            $config = \Config::get('database.redis.default');

            if (!isset($config['port'])) {
                $config['port'] = 6379;
            }

            $server = "{$config['host']}:{$config['port']}";
            return  new \Resque_Redis($server, $config['database']);

        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('redis');
    }

}