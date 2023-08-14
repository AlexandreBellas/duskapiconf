<?php

namespace AleBatistella\DuskApiConf\Traits;

use AleBatistella\DuskApiConf\Exceptions\DuskSetConfigException;
use Laravel\Dusk\Browser;

trait UsesDuskApiConfig
{
    /**
     * Defines a config value.
     * 
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setConfig(string $key, mixed $value): void
    {
        $encoded = base64_encode(json_encode($value));
        $query = "?key=" . $key . '&value=' . $encoded;

        $this->browse(function (Browser $browser) use ($query) {
            $element = $browser
                ->visit(route('duskapiconf.set') . $query)
                ->element('.content');

            $text = $element->getText();

            if (trim($text) !== 'ok') {
                throw new DuskSetConfigException(
                    'It was not possible to set up the config value.'
                );
            }
        });
    }

    /**
     * Retrieves a config value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getConfig(string $key): mixed
    {
        $query = "?key=" . $key;
        $result = null;

        $this->browse(function (Browser $browser) use ($query, &$result) {
            $data = $browser
                ->visit(route('duskapiconf.get') . $query)
                ->element('.content')
                ->getAttribute('innerHTML');

            $result = json_decode(base64_decode($data), true);
        });

        return $result;
    }

    /**
     * Resets the configuration to its initial state.
     *
     * @return void
     */
    public function resetConfig(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('duskapiconf.reset');
        });
    }
}