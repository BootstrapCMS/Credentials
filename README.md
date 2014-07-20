Laravel Credentials
===================


[![Build Status](https://img.shields.io/travis/GrahamCampbell/Laravel-Credentials/master.svg?style=flat)](https://travis-ci.org/GrahamCampbell/Laravel-Credentials)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Credentials.svg?style=flat)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Credentials/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Credentials.svg?style=flat)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Credentials)
[![Software License](https://img.shields.io/badge/license-Apache%202.0-brightgreen.svg?style=flat)](LICENSE.md)
[![Latest Version](https://img.shields.io/github/release/GrahamCampbell/Laravel-Credentials.svg?style=flat)](https://github.com/GrahamCampbell/Laravel-Credentials/releases)


## Introduction

Laravel Credentials was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and is a cool way to authenticate in [Laravel 4.2+](http://laravel.com). It utilises many of my packages and Cartalyst's [Sentry](https://github.com/cartalyst/sentry) package. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Credentials/releases), [license](LICENSE.md), [api docs](http://docs.grahamjcampbell.co.uk), and [contribution guidelines](CONTRIBUTING.md).


## Installation

[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.2+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Credentials, simply require `"graham-campbell/credentials": "~0.3"` in your `composer.json` file. You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

You will need to register many service providers before you attempt to load the Laravel Credentials service provider. Open up `app/config/app.php` and add the following to the `providers` key.

* `'McCool\LaravelAutoPresenter\LaravelAutoPresenterServiceProvider'`
* `'Lightgear\Asset\AssetServiceProvider'`
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


## Configuration

Laravel Credentials supports optional configuration.

To get started, first publish the package config file:

    php artisan config:publish graham-campbell/credentials

There are two config options:

##### Enable Public Registration

This option (`'regallowed'`) defines if public registration is allowed. The default value for this setting is `true`.

##### Require Account Activation

This option (`'activation'`) defines if public registration requires email activation. The default value for this setting is `true`.

##### Additional Configuration

You may want to check out the config for `cartalyst/sentry` too. For Laravel Credentials to function correctly, you must set the models to the following, or to a class which extends the following:

* `'GrahamCampbell\Credentials\Models\Group'`
* `'GrahamCampbell\Credentials\Models\User'`
* `'GrahamCampbell\Credentials\Models\Throttle'`


## Usage

There is currently no usage documentation besides the [API Documentation](http://docs.grahamjcampbell.co.uk) for Laravel Credentials.

You may see an example of implementation in [Bootstrap CMS](https://github.com/GrahamCampbell/Bootstrap-CMS).


## License

Apache License

Copyright 2013-2014 Graham Campbell

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
