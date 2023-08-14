<?php

Route::group(
    [
        'middleware' => ['web'],
        'prefix'     => 'duskapiconf',
        'namespace'  => 'AleBatistella\DuskApiConf\Controllers'
    ],
    function () {
        Route::get('get', 'DuskApiConfController@get')->name('duskapiconf.get');
        Route::get('set', 'DuskApiConfController@set')->name('duskapiconf.set');
        Route::get('reset', 'DuskApiConfController@reset')->name('duskapiconf.reset');
    }
);