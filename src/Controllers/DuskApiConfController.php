<?php

namespace AleBatistella\DuskApiConf\Controllers;
use Illuminate\Support\Facades\Storage;

class DuskApiConfController
{
    /**
     * Get a configuration variable
     *
     * @return \Illuminate\View\View
     */
    public function get()
    {
        $request = request();
        if (! $request->filled('key')) {
            return view('duskapiconf::data', ['value' => 'error_not_all_parameters']);
        }
        $value = config($request->input('key'));
        return view('duskapiconf::data', ['value' => base64_encode(json_encode($value))]);
    }

    /**
     * Set a configuration variable
     *
     * @return \Illuminate\View\View
     */
    public function set()
    {
        $request = request();

        if ( (! $request->filled('key')) || (! $request->filled('value')) ) {
            return view('duskapiconf::data', ['value' => 'error_not_all_parameters']);
        } else {

            $value = json_decode(base64_decode($request->input('value')), true);

            $currentContent = [];
            if (Storage::disk(config('alebatistella.duskapiconf.disk'))->exists(config('alebatistella.duskapiconf.file'))) {
                $decoded = Storage::get(config('alebatistella.duskapiconf.file'));
                $currentContent = json_decode($decoded, true);
            }

            $currentContent[$request->input('key')] = $value;
            Storage::disk(config('alebatistella.duskapiconf.disk'))->put(config('alebatistella.duskapiconf.file'), json_encode($currentContent));

            return view('duskapiconf::data', ['value' => 'ok']);
        }
    }

    /**
     * Reset any temporary configuration by deleting the tmp file
     *
     * @return \Illuminate\View\View
     */
    public function reset()
    {
        $request = request();
        Storage::disk(config('alebatistella.duskapiconf.disk'))->delete(config('alebatistella.duskapiconf.file'));
        return view('duskapiconf::data', ['value' => 'ok']);
    }
}