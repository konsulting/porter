# Porter

A [Docker](https://www.docker.com) based multi-site setup for local development. Inspired by [Laravel Valet](https://github.com/laravel/valet) & [Homestead](https://github.com/laravel/homestead) and [Shipping Docker's Vessel](https://github.com/shipping-docker/vessel), [Shipping Docker](https://serversforhackers.com/shipping-docker) and [Docker For Developers](https://bitpress.io/docker-for-php-developers/).

We're still learning Docker, and open to improvements to this set up and we're 'dog-fooding' it as we go. 

Our aim is to use this for day-to-day development with simple, portable usage. We use Macs for our development, but given the portable nature of Docker we'd like to enable this offering to allow usage across each of MacOS, Linux and Windows.

Porter is developed using [Laravel-Zero](https://laravel-zero.com/).

Contributions are welcome.  We are a small company, so please be patient if your question or pull request need to wait a little.

## Requirements

 - Docker 18.06+
 - Docker Compose (1.22+)
 - PHP 7.1+ on host machine

## Installation

 - Install [Docker](https://www.docker.com/community-edition)
 - Clone Porter to a directory and install it's dependencies.
 
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
 
 - Set up routing... you have some options.
   
   1. Use the DNS container shipped with Porter.  Update your machine's network DNS settings to point to 127.0.0.1. The container will resolve the domain for Porter and it will forward other request to Cloudflare's DNS (1.1.1.1). You will need to turn off locally any installed DNSmasq since the DNS container opens to port 53 on localhost. (e.g. `brew services stop dnsmasq`)
   
   2. Use your existing Laravel Valet domain - which uses DNSmasq installed locally on a Mac.
   
   3. Manually edit your `/etc/hosts` file for each domain.
   
   4. Roll your own solution.

 - Porter binds to ports 80 and 443, so you need to turn Valet off (`valet stop`) or any other services that are bound to them before using it.
 
 - In your terminal `cd` to the directory where your sites are located, and run `porter begin`
 
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

## Commands:

 - `porter begin` - Migrate and seed the sqlite database, and publish config files to `storage/config`. It will set Porter home to the working directory when you run the command.  It will also download the required docker images.
 - `porter start`
 - `porter stop`
 - `porter build` - (Re)build the containers
 - `porter make-files` - (Re)make all the files and restart. Run after changing config files.
 - `porter pull-images` - Pull the Konsulting, MySQL and Redis images - which will then be used by docker-compose rather than building (unless you tweak the DockerFiles).
 - `porter build-images` - Build the Konsulting images.
 
### Basic settings

 - `porter domain {domain}` - Set tld ('test' is the default for domains such as sample.test)
 - `porter home {dir?}` - Set the home dir for sites, run in the dir to use it directly - or set it specifically
 
### Site settings

Site commands will pick up the current working directory automatically.  They also allow you to specify the site by the directory name.

 - `porter site:list` 
 - `porter site:unsecure {site?}` - Set up a site to use http
 - `porter site:secure {site?}` - Set up a site to use https.
 - `porter site:remove {site?}` - Remove a site 
 - `porter site:php {site?}` - Choose the PHP version for site
 - `porter site:nginx-config {site?}` - Choose NGiNX config template for a site, ships with default (/public such as Laravel) and project_root
 - `porter site:renew-certs {--clear-ca}` - Renew the certificates for all secured sites, optionally rebuild CA.s

Site NGiNX config files are created programmatically using the templates in `resources/views/nginx`. The config files are stored in `storage/config/nginx/conf.d`.

NGiNX logs are stored in `storage/logs/nginx`

### PHP

 - `porter php:default` - Set default PHP version
 - `porter php:list` - List the available PHP versions
 - `porter php:open {version?}` - Open the PHP cli for the project, run in project dir, or run a specific version

`php.ini` files are stored in `storage/config` by PHP version. If you change one, you'll need to run `porter php:restart` for changes to be picked up. 

We currently ship with containers for PHP 5.6, 7.0, 7.1 and 7.2.

### Node (npm/yarn)
 - `porter node:open` - Open Node cli, run in project dir

### MySQL
Enabled by default. Available on the host machine on port 13306. The user is `root` and the password `secret`. You can connect with your favourite GUI if you want to.

 - `porter mysql:on`
 - `porter mysql:off`
 - `porter mysql:open` - Open MySQL cli

MySQL data is stored in `storage/data/mysql`.
Logs are stored in `storage/logs/mysql`.

### Redis

Enabled by default. Available on the host machine on port 16379`.

 - `porter redis:on`
 - `porter redis:off`
 - `porter redis:open` - Open Redis cli

Redis data is stored in `storage/data/redis`.

## Email

We have a [MailHog](https://github.com/mailhog/MailHog) container, all emails are routed to this container from PHP when using the `mail()` function. 

You can review received emails in MailHog's UI at [http://localhost:8025](http://localhost:8025/). Or, you can use the MailHog API to inspect received emails.

## PHP Extensions

We have added a number of PHP extensions to the containers that we use frequently. These include:

 - GD
 - Imagick
 - MbString
 - MySQLi [& MySQL on 5.6]
 - Opcache [defaulted to off as we're using the setup for development]
 - PDO with PDO_MySQL PDO_PGSQL 
 - SOAP
 - Xdebug
 - Zip

### Xdebug

Xdebug is available on each PHP container. `xdebug.ini` files are stored in `storage/config` by PHP version. Each version uses a different port, the fpm and cli containers use the same port for any given PHP version.

|PHP Version|Port|
|---|---|
|5.6|9501|
|7.0|9502|
|7.1|9503|
|7.2|9504|
