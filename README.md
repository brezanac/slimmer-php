# slimmer-php

An extremely minimalistic PHP skeleton application which is specifically aimed at creating small applications without introducing unnecessary complexity that usually comes with today's frameworks.

That means no fancy terms like service providers, PSR standards, dependency injection containers or endless levels of abstraction which in the end defeat the original purpose of having a very small and compact code base, that runs very fast on any hardware.

## Features

The skeleton provides following features:

 * very fast routing, provided by the excellent [FastRoute](https://github.com/nikic/FastRoute) package
 * full integration of [Monolog](https://github.com/Seldaek/monolog), for logging purposes
 * [Twig](https://twig.symfony.com/) support, for easy and convenient template support
 * support for Dotenv (.env) files, provided by the [phpdotenv](https://github.com/vlucas/phpdotenv) package
 * environment specific cascading configuration (different settings for development, production etc.)
 * optional fully configured Docker support, for running the app with Docker
 * fully implemented support for client-side development through [gulp](https://gulpjs.com/), including revision support for static assets

## Usage

### Docker

If you plan to use the supplied Docker configuration, please consult [docker-amp-template](https://github.com/brezanac/docker-amp-template) to learn how to use it.

## Client side

On the client-side, [gulp](https://gulpjs.com/) is used to control the entire workflow of static assets (stylesheets, images etc.)

First install all the required `npm` dependencies.

```
npm install
```

**NOTE:** you should run this command separately on each server where the application will be running since storing the `node_modules` folder in revision control is heavily discouraged.

After that simply run the following line to start the watchers that are used during development.

```
gulp
```

Once you are ready to deploy the application in production, or maybe test it, use the following line.

```
gulp production
```

This will, among others, create a manifest file for the revisioning system, which will make sure to perform cache-busting for changed files.

**NOTE:** if you are running `gulp` in development mode, please note that no revisioning will be performed, which means that during testing you might want to disable caching in your browser.

## Server side

Before running the application you need to install all the Composer dependencies.

```
composer install
```

**NOTE:** again, you should execute this command separately on every server where the code will be running, because storing the `vendor` folder in revision control is pretty much a (very) bad idea.

## Configuration

Simply open the files located in the `config` folder and make changes, if required.

These settings will be applied globally, unless overridden by those specified in environment specific folders (`config/development`, `config/production` etc.)

The active environment is determined by the value of `APP_ENV` in the `.env` file.

If `APP_ENV` is supplied, general settings from the `config` folder will be overwritten by specific values from the corresponding environment folder (`config/development`, `config/production` etc.).

## Environmental values

Make a copy of the `.env.example` file and name it `.env`. After that, change or add values which will be made available to the application through the `$_ENV` array.

If you have sensitive data like API keys, database connection parameters etc. **THIS** is the place where you want to have them stored.

Each `.env` file should be specific for one and only one environment (server) the application is running on. It should **NEVER** be committed to revision control like Git.

## Routes

Routes are specified in the `config/routes.php` file, and their environment specific configurations of course (`config/production/routes.php` etc.)

For details on how to specify routes please consult the [FastRoute](https://github.com/nikic/FastRoute) documentation.

**NOTE:** due to a pretty greedy nature of the `/[{uri}]` route in `routes.php`, make sure to always specify a more specific route (i.e. `/blog/{uri}`) if you want the request to be routed to something else than the `App\Content\Page\display()` method.

## Twig

Twig templates are stored in the `resources/templates` folder.

For instructions on how to use them please consult [Twig documentation](https://twig.symfony.com/doc/3.x/).

## Code structure

The skeleton really doesn't make any restrictions in terms on how you are going to structure your code.

If you want to follow a proper MVC (sort of) structure you could easily create an `App\Models` namespace and store your models there.

## Slimmer?

The name was coined as a joke in reference to the [Slim](http://www.slimframework.com/) framework, which I used extensively up to the release of Slim 4 and the decision of the authors to move Slim 3 to maintenance-only mode.

Since the added complexity of Slim 4 ultimately defeated the main purpose of why I've used Slim (small, mostly content driven applications) it didn't make sense for me to use it anymore.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
