<?php

namespace AleBatistella\DuskApiConf;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class DuskApiConfServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     *
     * @author Alexandre Batistella
     * @version 1.0.0
     * @since 1.0.0
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/Route.php');
        $this->loadViewsFrom(__DIR__ . '/Resources/Views', 'duskapiconf');
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'alebatistella.duskapiconf'
        );

        $contents = Storage::disk(config('alebatistella.duskapiconf.disk'))
            ->get(config('alebatistella.duskapiconf.file'));

        $decoded = json_decode($contents, true);

        foreach (array_keys($decoded) as $k) {
            config([$k => $decoded[$k]]);
        }

        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('duskapiconf.php'),
        ]);
    }

    /**
     * @inheritDoc
     *
     * @author Alexandre Batistella
     * @version 1.0.0
     * @since 1.0.0
     *
     * @return void
     */
    public function booted()
    {
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', \AleBatistella\DuskApiConf\Middleware\ConfigStoreMiddleware::class);
    }

    /**
     * @inheritDoc
     *
     * @author Alexandre Batistella
     * @version 1.0.0
     * @since 1.0.0
     *
     * @return void
     */
    public function register()
    {
    }
}