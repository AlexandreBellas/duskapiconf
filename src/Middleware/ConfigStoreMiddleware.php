<?php

namespace AleBatistella\DuskApiConf\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;

class ConfigStoreMiddleware
{
    /**
     * If the temporary config file exists, retrieve the content and change
     * dynamically the current live configuration.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return void
     */
    public function handle($request, Closure $next)
    {
        $filesystem = Storage::disk(config('duskapiconf.storage.disk'));

        if (!$filesystem->exists(config('duskapiconf.storage.file'))) {
            return $next($request);
        }

        $contents = $filesystem->get(config('duskapiconf.storage.file'));

        $decoded = json_decode($contents, true);
        foreach (array_keys($decoded) as $key) {
            config([$key => $decoded[$key]]);
        }

        return $next($request);
    }
}