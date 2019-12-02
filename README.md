# Porter

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/konsulting/porter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/konsulting/porter/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/konsulting/porter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/konsulting/porter/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/konsulting/porter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/konsulting/porter/build-status/master)

A [Docker](https://www.docker.com) based multi-site setup for local development. Inspired by [Laravel Valet](https://github.com/laravel/valet) & [Homestead](https://github.com/laravel/homestead) and [Shipping Docker's Vessel](https://github.com/shipping-docker/vessel), [Shipping Docker](https://serversforhackers.com/shipping-docker) and [Docker For Developers](https://bitpress.io/docker-for-php-developers/).

We're still learning Docker, and open to improvements to this set up and we're 'dog-fooding' it as we go. **Porter is currently in Beta state**, we're refining as we move along.

Our aim is to use this for day-to-day development with simple, portable usage. We use Macs for our development, but given the portable nature of Docker we'd like to enable this offering to allow usage across each of MacOS, Linux and Windows.

Porter is developed using [Laravel Zero](https://laravel-zero.com/).

Contributions are welcome.  We are a small company, so please be patient if your question or pull request needs to wait a little.

## Requirements

 - Docker 18.06+
 - Docker Compose (1.22+)
 - PHP 7.1+ on host machine

## Installation

 - Install [Docker](https://www.docker.com/community-edition)
 
 - Login to docker (this will allow Porter to pull the images it needs). `docker login`
 
 - Clone Porter to a directory and install its dependencies.
 
    ```
    git clone git@github.com:konsulting/porter.git
    cd porter
    composer install
    ```
 
 - Add Porter to your $PATH (e.g. in .bash_profile)
 
    ```
    export PATH="[path to porter]:$PATH" 
    source .bash_profile
    ```
 
 - Set up DNS resolution... you have some options.
   
   1. Use the DNS container shipped with Porter.  Update your machine's network DNS settings to point to 127.0.0.1 before other name servers. The container will resolve the domain for Porter. You will need to turn off locally any installed DNSmasq since the DNS container opens to port 53 on localhost. (e.g. `brew services stop dnsmasq`)
   
   2. Use your existing Laravel Valet domain - which uses DNSmasq installed locally on a Mac.
   
   3. Manually edit your `/etc/hosts` file for each domain.
   
   4. Roll your own solution.

 - Porter binds to ports 80 and 443, so you need to turn Valet off (`valet stop`) or any other services that are bound to them before using it.
 
 - In your terminal `cd` to the directory where your sites are located, and run `porter begin`. 
 
    This command will ask for your home directory (the root of your sites) and will generate a CA Certificate (and ask your permission to trust it on Mac).
 
 - Finally run `porter start`
 
## Usage

Porter uses a simple set of commands for interaction (see below).

Sites are added manually. This allows us to set up each one up with its own NGiNX config. To add your first site, move to its directory and run `porter unsecure`.

Porter adds two simple environment variables to the PHP containers. 

1. `RUNNING_ON_PORTER=true` allowing you to identify when a site is running on Porter. 
2. `HOST_MACHINE_NAME=host.docker.internal` allowing you to resolve to services running directly on the host machine. The value for this changes every now and then, so this means you have less to remember.

Access them in PHP using:
```php
getenv('RUNNING_ON_PORTER')
getenv('HOST_MACHINE_NAME')
```

## DNS Resolution Notes

If you plan on running development sites which resolve other development sites (e.g. with Curl, or use hot reloading with [Laravel Mix](https://laravel-mix.com)), there is an additional command to run (after each Host machine reboot) `porter dns:set-host`. Please see [DNS](#dns) for more details. 

As we work on Macs, this command only works on MacOS for now, but we welcome any contributions to set the IP to a relevant IP on other OS's.  Some additional helpful background at [Eventuate](http://eventuate.io/docs/usingdocker.html).

We have deliberately chosen not to make this automatic, nor permanent to avoid digging into your machine too much.

## Commands

 - `porter begin {--home?} {--force?}` - Migrate and seed the sqlite database, and publish config files to `~/.porter/config`. It will set Porter home to the working directory when you run the command (or you can specify with the `--home` option).  It will also create a CA certificate (asking you to trust it on a Mac) and it will download the required docker images.
 - `porter start`
 - `porter status` - show the status of containers
 - `porter stop`
 - `porter restart` - Restart existing containers (e.g. pick up config changes for PHP FPM)
 - `porter logs {service}` - Show container logs, optionally pass in the service
  
### Basic settings

 - `porter domain {domain}` - Set TLD ('test' is the default for domains such as sample.test)
 - `porter home {dir?} {--show}` - Set the home dir for sites, run in the dir to use it directly - or set it specifically. Use `--show` to see the current setting.
 
### Site settings

Site commands will pick up the current working directory automatically.  They also allow you to specify the site by the directory name.

 - `porter site:list` 
 - `porter site:unsecure {site?}` - Set up a site to use http
 - `porter site:secure {site?}` - Set up a site to use https.
 - `porter site:remove {site?}` - Remove a site 
 - `porter site:php {site?}` - Choose the PHP version for site
 - `porter site:nginx {site?}` - Choose NGiNX config template for a site, ships with default (/public such as Laravel) and project_root
 - `porter site:renew-certs {--clear-ca}` - Renew the certificates for all secured sites, optionally rebuild CA.

Site NGiNX config files are created programmatically using the templates in `resources/views/nginx`. The config files are stored in `~/.porter/config/nginx/conf.d`.

NGiNX logs are visible from the `porter logs nginx`.

Porter will try to set your Mac up to trust the SSL certificates it generates by adding the generated CA to the keychain (it will request sudo permission). This works for Safari and Chrome, but not for Firefox.
In Firefox, you will need to manually add the certificate, which is located in `~/.porter/ssl/KleverPorterSelfSigned.pem` (Preferences -> Privacy & Security -> View Certificates -> Import).

### PHP

 - `porter php:default` - Set default PHP version
 - `porter php:list` - List the available PHP versions
 - `porter php:open {run?} {--p|php-version?}` - Open the PHP cli for the project, if run from a project directory, it will select the associated version. Otherwise, you can select a version or use the default. Optionally run a command, such as `vendor/bin/phpunit` (if you need to pass arguments, wrap in quotes). 
 - `porter php:tinker` - Run Artisan Tinker in the project directory
  
`php.ini` files are stored in `~/.porter/config` by PHP version. If you change one, you'll need to run `porter php:restart` for changes to be picked up. 

We currently ship with containers for PHP 5.6, 7.0, 7.1, 7.2 and 7.3.

### Node (npm/yarn)
 - `porter node:open {run?}` - Open Node cli, run in project dir. Optionally run a command, such as `npm run production` (if you need to pass arguments, wrap in quotes). 

We use [Laravel Mix](https://laravel-mix.com) in quite a few projects, since it makes setup of JS dev tools straightforward.  In order to use `npm run hot` in the node container, you will need to adjust the node devServer settings so that compiled assets become available.

In your `webpack.mix.js` file add the following setup:

```javascript
mix.webpackConfig({
    devServer: {
        host: '0.0.0.0',
        port: 8080,
    }
});
```

Ports `3000`, `3001` and `8080` are bound to localhost when running the Node container. These provide the development proxy, BrowserSync interface and access to the compiled assets.

### MySQL
Enabled by default. Available on the host machine on port 13306. The user is `root` and the password `secret`. You can connect with your favourite GUI if you want to.

 - `porter mysql:on`
 - `porter mysql:off`
 - `porter mysql:open` - Open MySQL cli

MySQL data is stored in `~/.porter/data/mysql`.

### Redis

Enabled by default. Available on the host machine on port 16379`.

 - `porter redis:on`
 - `porter redis:off`
 - `porter redis:open` - Open Redis cli

Redis data is stored in `~/.porter/data/redis`.

## DNS

 - `porter dns:on` - Turn the DNS container on (the default status).
 
 - `porter dns:off` - Turn the DNS container off.
 
 - `porter dns:flush` - flush your local machine's DNS in cases where it's getting a bit confused, saves you looking up the command we hope.
 
 - `porter dns:set-host {--restore}` - see below. The `--restore` will remove the setup. 
 
 There are some times where pointing the DNS to 127.0.0.1 in order to access the development sites doesn't work out well. 
 
 For example when resolving development sites inside a container - when making a request to another site hosted in Porter, the container ends up looking at itself rather than the host machine). 
 
 On MacOS this command will create an alias of `10.200.10.1` to the `loopback` address, and then use this when resolving DNS for the Porter domain.  The default for all systems is `127.0.0.1`. 
 
 We leave it up to you to run this command at the moment, it is a temporary setting and resets on a machine restart. Not everyone will need it, and we'd rather not dig much deeper into your system to make it permanent.

## Ngrok

 - `porter ngrok {site?} {--region=eu} {--no-inspection}`
 
It's sometimes handy to share your progress on a site without deploying it to a server out there in the world. [ngrok](https://ngrok.com) provides a decent solution for this. Porter provides an ngrok container which is able to forward your local site to an external url. 

You can optionally specify a site to ngrok (e.g. `konsulting.test` would be `konsulting`). You can also specify a region and optionally disable inspections of responses by ngrok.

The ngrok UI interface will be available here: [http://0.0.0.0:4040](http://0.0.0.0:4040)

In order to use ngrok, you need to use an alternative loopback address to 127.0.0.1, since this resolves to the container that a request is sent from otherwise.  This can be done by following [these instructions](#dns). 

## Email

We have a [MailHog](https://github.com/mailhog/MailHog) container; all emails are routed to this container from PHP when using the `mail()` function. 

You can review received emails in MailHog's UI at [http://localhost:8025](http://localhost:8025/). Or, you can use the MailHog API to inspect received emails.

## PHP Extensions

We have added a number of PHP extensions to the containers that we use frequently. Notable ones are Imagick and Xdebug. 

### Xdebug

Xdebug is available on each PHP container. `xdebug.ini` files are stored in `storage/config` by PHP version.

It is set up for use with PhpStorm, and on demand - you can use an extension such as Xdebug helper in Chrome to send the Cookie required to activate a debugging session ([Jetbrains article](https://confluence.jetbrains.com/display/PhpStorm/Configure+Xdebug+Helper+for+Chrome+to+be+used+with+PhpStorm)).

Xdebug is set up to communicate with the host machine on port 9001 to avoid clashes with any locally installed PHP-fpm.

## Browser Testing

We like [Laravel Dusk](https://laravel.com/docs/5.6/dusk), and also help with [Orchestra Testbench Dusk](https://github.com/orchestral/testbench-dusk) for package development. Porter provides a browser container with Chrome and Chromedriver for browser testing.

The browser container can be turned on and off (default on), in case it is not required.

- `porter browser:on`
- `porter browser:off`

Notes for your test setup...

 - Dusk and Testbench Dusk use a PHP based server running from the command line. With Porter, the server must be run at `0.0.0.0:8000` for it to be available to the browser container
 - The remote web-driver must point to the browser container at `http://browser:9515`
 - The url for testing needs to be the hostname of the PHP CLI container (where the tests are running) - which can be retrieved through `getenv('HOSTNAME')`
 - Finally, we need to add `--no-sandbox` to the options for Chrome and it should run '--headless'.

## SSH Keys

Porter include a `~/.porter/config/user/ssh` directory which is linked to the root user `.ssh` dir in the PHP cli containers and the Node container.

This means you can add the ssh keys you want to use in your dev environment specifically (if any).
 
## Docker Sync and Mutagen (consider early Alpha)

Very early stage work has begun on including [mutagen](https://mutagen.io) and [docker-sync](https://docker-sync.io) in order to attempt to speed up some disk usage.  There are issues with the amount of time it takes either to set up these solutions, or the amount of storage required in containers for them.
We do not recommend using them at the moment, due to the way Porter is set up, they are attempting to use far too many files in the home code directory for initial set up to be satisfactory.

### Docker Sync
- `porter docker-sync:install`
- `porter docker-sync:on`
- `porter docker-sync:off`

### Mutagen
 - `porter mutagen:install`
 - `porter mutagen:on`
 - `porter mutagen:off`
  
Adding these items required the addition of some core events to allow intercepting the docker-compose.yaml file production process and the starting/stopping of containers. To do this a number of events were introduced.

 - `App\Events\StartingPorter`
 - `App\Events\StartingPorterService($service)`
 - `App\Events\StoppingPorter`
 - `App\Events\StoppingPorterService($service)`
 - `App\Events\BuiltDockerCompose($dockerComposeFilePath)` 
  
## Tweaking things

As Porter is based on Docker, it is easy to add new containers as required or to adjust the way the existing containers are built. 

The docker-compose.yaml file is built using the views in the image set's docker-compose directory.  To locate the image set directory, look in `resources/image_sets` and `~/.porter/image_sets`.

The NGiNX config templates are in `resources/views/nginx`.

The following commands will be useful if you change these items.

 - `porter build` - (Re)build the containers.
 - `porter images:build` - Build the current container images.
 - `porter images:pull` - Pull the current images - which will then be used by docker-compose where it can.
 - `porter images:set` - Change the image set used for Porter. The default is `konsulting/porter-ubuntu`.
 - `porter make-files` - (Re)make the docker-compose.yaml, and the NGiNX config files.

We store personal config in the `.porter` directory in your home directory - keeping config and data separate from the main application. This is the PorterLibrary. It includes:

 - `composer` - a composer cache dir, allowing the containers to avoid pulling as much info when using composer
 - `config` - containing the specific files for customisation of the containers/services
 - `data` - containing data for the MySQL and Redis containers by default
 - `ssl` - the generated SSL certificates used by Porter
 - `views` - allows the override and addition of views for building NGiNX configurations for example
 - an `image_sets` directory can be added to include alternative docker scripts similar to the original `konsulting/porter-ubuntu` in the project's `resources/image_sets` directory, and the `docker-compose.yaml` views
