Laravel Credentials
===================

Laravel Credentials was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and is a cool way to authenticate in [Laravel 5](http://laravel.com). It utilises many of my packages and Cartalyst's [Sentry](https://github.com/BootstrapCMS/Sentry) package. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/BootstrapCMS/Credentials/releases), [license](LICENSE), [api docs](http://docs.grahamjcampbell.co.uk), and [contribution guidelines](CONTRIBUTING.md).

![Laravel Credentials](https://cloud.githubusercontent.com/assets/2829600/4432313/c1664f5c-468c-11e4-8469-c6874c21aede.PNG)

<p align="center">
<a href="https://travis-ci.org/BootstrapCMS/Credentials"><img src="https://img.shields.io/travis/BootstrapCMS/Credentials/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/BootstrapCMS/Credentials/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/BootstrapCMS/Credentials.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/BootstrapCMS/Credentials"><img src="https://img.shields.io/scrutinizer/g/BootstrapCMS/Credentials.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/BootstrapCMS/Credentials/releases"><img src="https://img.shields.io/github/release/BootstrapCMS/Credentials.svg?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

[PHP](https://php.net) 5.5+ or [HHVM](http://hhvm.com) 3.3+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Credentials, simply add the following line to the require block of your `composer.json` file:

```
"graham-campbell/credentials": "0.4.*"
```

You'll also need to make sure our fork of Sentry is included in your repositories list:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/BootstrapCMS/Sentry"
        }
    ],
}
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

You will need to register many service providers before you attempt to load the Laravel Credentials service provider. Open up `config/app.php` and add the following to the `providers` key.

* `'McCool\LaravelAutoPresenter\LaravelAutoPresenterServiceProvider'`
* `'Cartalyst\Sentry\SentryServiceProvider'`
* `'GrahamCampbell\Core\CoreServiceProvider'`
* `'GrahamCampbell\Security\SecurityServiceProvider'`
* `'GrahamCampbell\Binput\BinputServiceProvider'`
* `'GrahamCampbell\Throttle\ThrottleServiceProvider'`

Once Laravel Credentials is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Credentials\CredentialsServiceProvider'`

You can register the three facades in the `aliases` key of your `config/app.php` file if you like.

* `'UserProvider' => 'GrahamCampbell\Credentials\Facades\UserProvider'`
* `'GroupProvider' => 'GrahamCampbell\Credentials\Facades\GroupProvider'`
* `'Credentials' => 'GrahamCampbell\Credentials\Facades\Credentials'`

#### Looking for a laravel 4 compatable version?

Checkout the [0.3 branch](https://github.com/BootstrapCMS/Credentials/tree/0.3), installable by requiring `"graham-campbell/credentials": "0.3.*"`.


## Configuration

Laravel Credentials supports optional configuration.

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
```

This will create a `config/credentials.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.

There are two config options:

##### Enable Public Registration

This option (`'regallowed'`) defines if public registration is allowed. The default value for this setting is `true`.

##### Require Account Activation

This option (`'activation'`) defines if public registration requires email activation. The default value for this setting is `true`.

##### Revision Model

This option (`'revision'`) defines the revision model to be used. The default value for this setting is `'GrahamCampbell\Credentials\Models\Revision'`.

##### Additional Configuration

You may want to check out the config for both `cartalyst/sentry` and `graham-campbell/core` too. For Laravel Credentials to function correctly, you must set the models to the following, or to a class which extends the following:

* `'GrahamCampbell\Credentials\Models\Group'`
* `'GrahamCampbell\Credentials\Models\User'`
* `'GrahamCampbell\Credentials\Models\Throttle'`


## Usage

There is currently no usage documentation besides the [API Documentation](http://docs.grahamjcampbell.co.uk) for Laravel Credentials.


## License

Laravel Credentials is licensed under [The MIT License (MIT)](LICENSE).
