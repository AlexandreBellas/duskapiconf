<?php

namespace AleBatistella\DuskApiConf\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DuskApiConfController
{
    private array $successResponseData = ['value' => 'ok'];
    private array $errorResponseData = ['value' => 'error_not_all_parameters'];

    /**
     * Get a configuration variable.
     *
     * @return \Illuminate\View\View
     */
    public function get(Request $request)
    {
        if (!$request->filled('key')) {
            return view('duskapiconf::data', $this->errorResponseData);
        }

        $value = config($request->input('key'));
        return view('duskapiconf::data', [
            'value' => base64_encode(json_encode($value))
        ]);
    }

    /**
     * Set a configuration variable.
     *
     * @return \Illuminate\View\View
     */
    public function set(Request $request)
    {
        if ((!$request->filled('key')) || (!$request->filled('value'))) {
            return view(
                'duskapiconf::data',
                $this->errorResponseData
            );
        } else {
            $filesystem = Storage::disk(config('duskapiconf.storage.disk'));
            $value = json_decode(base64_decode($request->input('value')), true);

            $currentContent = [];
            if ($filesystem->exists(config('duskapiconf.storage.file'))) {
                $decoded = Storage::get(config('duskapiconf.storage.file'));
                $currentContent = json_decode($decoded, true);
            }

            $currentContent[$request->input('key')] = $value;
            $filesystem->put(
                config('duskapiconf.storage.file'),
                json_encode($currentContent)
            );

            return view('duskapiconf::data', $this->successResponseData);
        }
    }

    /**
     * Reset any temporary configuration by deleting the temporary file.
     *
     * @return \Illuminate\View\View
     */
    public function reset()
    {
        Storage::disk(config('duskapiconf.storage.disk'))->delete(
            config('duskapiconf.storage.file')
        );

        return view('duskapiconf::data', $this->successResponseData);
    }
}