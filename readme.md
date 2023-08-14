# Dusk API configuration

A Laravel module to perform live configuration changes from your Dusk tests.
Forked from [Manyapp DuskApiConf repository](https://github.com/manyapp/duskapiconf).

## The issue

Currently, the only way to define the configuration of your Laravel app during
Dusk tests is to set the relevant variables in a dedicated `.env.dusk.local`
file. This file is copied and read during the application's boot, and therefore
cannot be changed within Dusk tests.

This behavior can be problematic, as a lot of developers need to change the
configuration in specific tests to see if the application reacts accordingly.

As mentioned [here](https://github.com/laravel/dusk/issues/599), there is no
easy way to tackle this problem.

## The solution

This modules offers an easy possibility to change the configuration of your
application during the runtime of your Dusk tests.

It works by making available a hidden API route to register the configuration in
a temporary file, which is read on the further requests from the dusk tests.

## Installation

```
composer require alebatistella/duskapiconf --dev
```

You will have to add the trait to your `DustTestCase.php` as shown:

```php
<?php

use Laravel\Dusk\TestCase as BaseTestCase;
use AleBatistella\DuskApiConfig\Traits\UsesDuskApiConfig;

abstract class DuskTestCase extends BaseTestCase {
    use UsesDuskApiConfig;

    // ...
}

```

## Usage

To use it, use the defined methods below directly in your Dusk tests.

```
/** @test */
public function test_should_use_dusk_for_something ()
{
    // Get a config variable
    // Here, $appName will be "Laravel" on a fresh install
    $appName = $this->getConfig('app.name');

    // Change a config variable
    $this->setConfig('app.name', 'Laravel is fantastic');

    // Here, $appName will be "Laravel is fantastic"
    $appName = $this->getConfig('app.name');

    // Your tests with assertions
    // ...

    // You can reset all config variables set before.
    // This is not mandatory: you can keep the variables set for the next test
    // if you want (even though it is not a good practice).
    $this->resetConfig();
}
```

## Publish configuration file

```
php artisan vendor:publish --provider="AleBatistella\DuskApiConf\DuskApiConfServiceProvider"
```

With this command, you'll create a new `duskapiconf.php` in the `config` folder.
Then, you can modify the storage disk and the name of the temporary file,
including configuration about the used environment.

## License

MIT.
