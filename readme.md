![Manyapp DuskApiConf](https://many.app/wp-content/uploads/2019/07/manyapp_duskapiconf.png)

**A Laravel module to perform live configuration changes from your Dusk tests**

## The issue

Currently, the only way to define the configuration of your Laravel app during Dusk tests is to set the relevant variables in a dedicated `.env.dusk.local` file. This file is copied and read during the application's boot, and therefore cannot be changed within Dusk tests. 

This behaviour can be problematic, as a lot of developers need to change the configuration in specific tests to see if the application reacts accordingly. 

As mentionned [here](https://github.com/laravel/dusk/issues/599), there is no easy way to tackle this problem. 

## The solution

This modules offers an easy possibility to change the configuration of your application during the runtime of your Dusk tests.

See how it works on our [blog article](https://many.app/changing-laravel-configuration-during-dusk-tests/). 

## Installation

Install the module with:

```
composer require manyapp/duskapiconf --dev
```

Then, you will have to modify your `DustTestCase.php` to add three methods. Alternatively, you can add the following methods to the Trait of your choice and use the Trait in your Dusk tests.

```
/**
* Set live config option
*
* @param string $key 
* @param mixed $value
* @return void
*/
public function setConfig($key, $value)
{
    $encoded = base64_encode(json_encode($value));
    $query = "?key=".$key.'&value='.$encoded;
    $this->browse(function($browser) use ($query) {
        $data = $browser->visit('/duskapiconf/set'.$query)->element('.content')->getText();
        $data = trim($data);
        if ($data !== 'ok') {
            $this->assertTrue(false);
        }
    });
}


/**
* Get a current configuration item
*
* @param string $key
* @return mixed
*/
public function getConfig($key)
{
    $query = "?key=".$key;
    $result = null;
    $this->browse(function($browser) use ($query, &$result) {
        $data = $browser->visit('/duskapiconf/get'.$query)->element('.content')->getAttribute('innerHTML');
        $result = json_decode(base64_decode($data), true);
    });
    return $result;
}

/**
* Reset the configuration to its initial status
*
* @return void
*/
public function resetConfig()
{
    $this->browse(function($browser) {
        $browser->visit('/duskapiconf/reset');
    });
}

```

## Usage

To use it, use the defined methods below directly in your Dusk tests.

```
/** @test */
public function your_dusk_test()
{
    // Get a config variable
    // Here, $appName will be "Laravel" on a fresh install
    $appName = $this->getConfig('app.name');

    // Change a config variable
    $this->setConfig('app.name', 'Laravel is fantastic');

    // Here, $appName will be Laravel is fantastic
    $appName = $this->getConfig('app.name');

    // Your tests with assertions

    // You can reset all config variables set before.
    // This is not mandatory: you can keep the variables set for the next test if you want.
    $this->resetConfig();
}
```

## Change location of the config temporary file

Type the following commands:

```
php artisan vendor:publish --provider="Manyapp\DuskApiConf\DuskApiConfServiceProvider"
```

Modify the Storage disk and the name of the temporary file.

## Contribute

For any bug or feature request, use Github. 

For any other feedback, let us a comment on this [blog article](https://many.app/changing-laravel-configuration-during-dusk-tests/) or [contact us](https://many.app/contact/).

## License

MIT.
