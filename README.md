Laravel Credentials
===================

Laravel Credentials was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and is a cool way to authenticate in [Laravel 4.2](http://laravel.com). It utilises many of my packages and Cartalyst's [Sentry](https://github.com/cartalyst/sentry) package. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Credentials/releases), [license](LICENSE), [api docs](http://docs.grahamjcampbell.co.uk), and [contribution guidelines](CONTRIBUTING.md).

![Laravel Credentials](https://cloud.githubusercontent.com/assets/2829600/4432311/c15fa92c-468c-11e4-93fe-79fb532da1e9.PNG)

<p align="center">
<a href="https://travis-ci.org/GrahamCampbell/Laravel-Credentials"><img src="https://img.shields.io/travis/GrahamCampbell/Laravel-Credentials/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Credentials/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Credentials.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Credentials"><img src="https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Credentials.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/GrahamCampbell/Laravel-Credentials/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Laravel-Credentials.svg?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.3+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Credentials, simply add the following line to the require block of your `composer.json` file:

```
"graham-campbell/credentials": "0.3.*"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

You will need to register many service providers before you attempt to load the Laravel Credentials service provider. Open up `app/config/app.php` and add the following to the `providers` key.

* `'McCool\LaravelAutoPresenter\LaravelAutoPresenterServiceProvider'`
* `'Cartalyst\Sentry\SentryServiceProvider'`
* `'GrahamCampbell\Core\CoreServiceProvider'`
* `'GrahamCampbell\Security\SecurityServiceProvider'`
* `'GrahamCampbell\Binput\BinputServiceProvider'`
* `'GrahamCampbell\Throttle\ThrottleServiceProvider'`

Once Laravel Credentials is installed, you need to register the service provider. Open up `app/config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Credentials\CredentialsServiceProvider'`

You can register the three facades in the `aliases` key of your `app/config/app.php` file if you like.

* `'UserProvider' => 'GrahamCampbell\Credentials\Facades\UserProvider'`
* `'GroupProvider' => 'GrahamCampbell\Credentials\Facades\GroupProvider'`
* `'Credentials' => 'GrahamCampbell\Credentials\Facades\Credentials'`

#### Looking for a laravel 5 compatable version?

Checkout the [master branch](https://github.com/GrahamCampbell/Laravel-Credentials/tree/master), installable by requiring `"graham-campbell/credentials": "0.4.*"`.


## Configuration

Laravel Credentials supports optional configuration.

To get started, first publish the package config file:

```bash
$ php artisan config:publish graham-campbell/credentials
```

There are two config options:

##### Enable Public Registration

This option (`'regallowed'`) defines if public registration is allowed. The default value for this setting is `true`.

##### Require Account Activation

This option (`'activation'`) defines if public registration requires email activation. The default value for this setting is `true`.

##### Revision Model

This option (`'revision'`) defines the revision model to be used. The default value for this setting is `'GrahamCampbell\Credentials\Models\Revision'`.

##### Additional Configuration

You may want to check out the config for `cartalyst/sentry` too. For Laravel Credentials to function correctly, you must set the models to the following, or to a class which extends the following:

* `'GrahamCampbell\Credentials\Models\Group'`
* `'GrahamCampbell\Credentials\Models\User'`
* `'GrahamCampbell\Credentials\Models\Throttle'`


## Usage

There is currently no usage documentation besides the [API Documentation](http://docs.grahamjcampbell.co.uk) for Laravel Credentials.


## License

Laravel Credentials is licensed under [The MIT License (MIT)](LICENSE).
