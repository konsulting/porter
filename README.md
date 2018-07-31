# Porter

A [Docker](https://www.docker.com) based multi-site setup for local development. Inspired by [Laravel Valet](https://github.com/laravel/valet) & [Homestead](https://github.com/laravel/homestead) and [Shipping Docker's Vessel](https://github.com/shipping-docker/vessel), [Shipping Docker](https://serversforhackers.com/shipping-docker) and [Docker For Developers](https://bitpress.io/docker-for-php-developers/).

We're still learning Docker, and open to improvements to this set up and we're 'dog-fooding' it as we go. 

Our aim is to use this for day-to-day development with simple, portable usage. We use Macs for our development, but given the portable nature of Docker we'd like to enable this offering to allow usage across each of MacOS, Linux and Windows.

Porter is developed using [Laravel-Zero](https://laravel-zero.com/).

Contributions are welcome.  We are a small agency, so please be patient if your question or pull request need to wait a little.

## Installation

 - Install [Docker](https://www.docker.com/community-edition)
 - Set up routing. Use `/etc/hosts` or optionally install DNSmasq (or similar) to handle directing traffic to the Porter services.
   
   We are currently using the DNSmasq setup from Valet - but wish to offer an automated setup for each OS.
   
 - Porter binds to ports 80 and 443, so you need to turn Valet off (or any other services that are bound to them) before using it.
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

 - `porter tld {tld}` - Set tld ('test' is the default for domains such as sample.test)
 - `porter home {dir?}` - Set the home dir for sites, run in the dir to use it directly - or set it specifically
 
### Site settings

Site commands will pick up the current working directory automatically.  They also allow you to specify the site by the directory name.

 - `porter sites:list` 
 - `porter sites:unsecure {site?}` - Set up a site to use http
 - `porter sites:secure {site?}` - Set up a site to use https
 - `porter sites:remove {site?}` - Remove a site 
 - `porter sites:php {site?}` - Choose the PHP version for site
 - `porter sites:nginx-config {site?}` - Choose NGiNX config template for a site, ships with default (/public such as Laravel) and project_root

Site NGiNX config files are created programmatically using the templates in `resources/views/nginx`. The config files are stored in `storage/config/nginx/conf.d`.

NGiNX logs are stored in `storage/logs/nginx`

### PHP

 - `porter php:default` - Set default PHP version
 - `porter php:list` - List the available PHP versions
 - `porter php:open {version?}` - Open the PHP cli for the project, run in project dir, or run a specific version

`php.ini` files are stored in `storage/config` by container. If you change one, you'll need to run `porter php:restart` for changes to be picked up.

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

