Laravel Credentials
===================


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/GrahamCampbell/Laravel-Credentials/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
[![Build Status](https://travis-ci.org/GrahamCampbell/Laravel-Credentials.png)](https://travis-ci.org/GrahamCampbell/Laravel-Credentials)
[![Coverage Status](https://coveralls.io/repos/GrahamCampbell/Laravel-Credentials/badge.png)](https://coveralls.io/r/GrahamCampbell/Laravel-Credentials)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Credentials/badges/quality-score.png?s=b384661adefa74fb4c695e50c7832c7f1ceea470)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Credentials)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/223971eb-99e6-47b4-8107-ee5b9a4b4446/mini.png)](https://insight.sensiolabs.com/projects/223971eb-99e6-47b4-8107-ee5b9a4b4446)
[![Software License](https://poser.pugx.org/graham-campbell/credentials/license.png)](https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md)
[![Latest Version](https://poser.pugx.org/graham-campbell/credentials/v/stable.png)](https://packagist.org/packages/graham-campbell/credentials)


## WARNING

#### This code is subject to extreme change and refactoring.


## What Is Laravel Credentials?

Laravel Credentials is a cool way to authenticate in [Laravel 4.1](http://laravel.com).

* Laravel Credentials was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell).
* Laravel Credentials relies on my [Laravel Core](https://github.com/GrahamCampbell/Laravel-Core) package.
* Laravel Credentials uses [Travis CI](https://travis-ci.org/GrahamCampbell/Laravel-Credentials) with [Coveralls](https://coveralls.io/r/GrahamCampbell/Laravel-Credentials) to check everything is working.
* Laravel Credentials uses [Scrutinizer CI](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Credentials) and [SensioLabsInsight](https://insight.sensiolabs.com/projects/223971eb-99e6-47b4-8107-ee5b9a4b4446) to run additional checks.
* Laravel Credentials uses [Composer](https://getcomposer.org) to load and manage dependencies.
* Laravel Credentials provides a [change log](https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Credentials/releases), and [api docs](http://grahamcampbell.github.io/Laravel-Credentials).
* Laravel Credentials is licensed under the Apache License, available [here](https://github.com/GrahamCampbell/Laravel-Credentials/blob/master/LICENSE.md).


## System Requirements

* PHP 5.4.7+ or PHP 5.5+ is required.
* You will need [Laravel 4.1](http://laravel.com) because this package is designed for it.
* You will need [Composer](https://getcomposer.org) installed to load the dependencies of Laravel Credentials.


## Installation

Please check the system requirements before installing Laravel Credentials.

To get the latest version of Laravel Credentials, simply require `"graham-campbell/credentials": "0.1.*@alpha"` in your `composer.json` file. You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

You will need to register the [Laravel Core](https://github.com/GrahamCampbell/Laravel-Core) service provider before you attempt to load the Laravel Credentials service provider. Open up `app/config/app.php` and add the following to the `providers` key.

* `'Lightgear\Asset\AssetServiceProvider'`
* `'Cartalyst\Sentry\SentryServiceProvider'`
* `'GrahamCampbell\Core\CoreServiceProvider'`
* `'GrahamCampbell\Viewer\ViewerServiceProvider'`
* `'GrahamCampbell\Queuing\QueuingServiceProvider'`
* `'GrahamCampbell\Security\SecurityMinServiceProvider'`
* `'GrahamCampbell\Binput\BinputServiceProvider'`
* `'GrahamCampbell\Passwd\PasswdServiceProvider'`
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

There are many config options:

**Home Page URL**

This option (`'home'`) defines the url to use for the home page. The default value for this setting is `'/'`.

**Enable Public Registration**

This option (`'regallowed'`) defines if public registration is allowed. The default value for this setting is `true`.

**Email Verification On Registration**

This option (`'regemail'`) defines if public registration requires email activation. The default value for this setting is `true`.

**Login Page**

This option (`'login'`) defines the view that is used for the login page. The default value for this setting is `'graham-campbell/credentials::account.login'`.

**Registration Page**

This option (`'registration'`) defines the view that is used for the registration page. The default value for this setting is `'graham-campbell/credentials::account.register'`.

**Forgot Password Page**

This option (`'reset'`) defines the view that is used for the forgot password page. The default value for this setting is `'graham-campbell/credentials::account.reset'`.

**Profile Password Page**

This option (`'profile'`) defines the view that is used for the profile page. The default value for this setting is `'graham-campbell/credentials::account.profile'`.

**Additional Configuration**

You may want to check out the config for `cartalyst/sentry` too. For Laravel Credentials to function correctly, you must set the models to the following, or to a class which extends the following:

* `'GrahamCampbell\Credentials\Models\Group'`
* `'GrahamCampbell\Credentials\Models\User'`
* `'GrahamCampbell\Credentials\Models\Throttle'`


## Usage

There is currently no usage documentation besides the [API Documentation](http://grahamcampbell.github.io/Laravel-Credentials
) for Laravel Credentials.

You may see an example of implementation in [CMS Core](https://github.com/GrahamCampbell/CMS-Core).


## Updating Your Fork

Before submitting a pull request, you should ensure that your fork is up to date.

You may fork Laravel Credentials:

    git remote add upstream git://github.com/GrahamCampbell/Laravel-Credentials.git

The first command is only necessary the first time. If you have issues merging, you will need to get a merge tool such as [P4Merge](http://perforce.com/product/components/perforce_visual_merge_and_diff_tools).

You can then update the branch:

    git pull --rebase upstream master
    git push --force origin <branch_name>

Once it is set up, run `git mergetool`. Once all conflicts are fixed, run `git rebase --continue`, and `git push --force origin <branch_name>`.


## Pull Requests

Please review these guidelines before submitting any pull requests.

* When submitting bug fixes, check if a maintenance branch exists for an older series, then pull against that older branch if the bug is present in it.
* Before sending a pull request for a new feature, you should first create an issue with [Proposal] in the title.
* Please follow the [PSR-2 Coding Style](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) and [PHP-FIG Naming Conventions](https://github.com/php-fig/fig-standards/blob/master/bylaws/002-psr-naming-conventions.md).


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
