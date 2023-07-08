<?php
namespace AleBatistella\DuskApiConf\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;


class ConfigStoreMiddleware
{

    /**
     * If the temporary config file exists, retrieve the content and 
     * change dynamically the current live configuration
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return void
     */
    public function handle($request, Closure $next)
    {
        if (!Storage::disk(config('alebatistella.duskapiconf.disk'))->exists(config('alebatistella.duskapiconf.file'))) {
            return $next($request);
        }

        $contents = Storage::disk(config('alebatistella.duskapiconf.disk'))->get(config('alebatistella.duskapiconf.file'));

        $decoded = json_decode($contents, true);
        foreach (array_keys($decoded) as $k) {
            config([$k => $decoded[$k]]);
        }

        return $next($request);
    }
}