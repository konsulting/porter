# Porter

A [Docker](https://www.docker.com) based multi-site setup for local development. Inspired by [Laravel Valet](https://github.com/laravel/valet) & [Homestead](https://github.com/laravel/homestead) and [Shipping Docker's Vessel](https://github.com/shipping-docker/vessel), [Shipping Docker](https://serversforhackers.com/shipping-docker) and [Docker For Developers](https://bitpress.io/docker-for-php-developers/).

We're still learning Docker, and open to improvements to this set up and we're 'dog-fooding' it as we go. 

Our aim is to use this for day-to-day development with simple, portable usage. We use Macs for our development, but given the portable nature of Docker we'd like to enable this offering to allow usage across each of MacOS, Linux and Windows.

Porter is developed using [Laravel-Zero](https://laravel-zero.com/).

Contributions are welcome.  We are a small agency, so please be patient is your question or pull request need to wait a little.

## Installation

 - Install [Docker](https://www.docker.com/community-edition)
 - Set up routing. Use `/etc/hosts` or optionally install DMSmasq (or similar) to handle directing traffic to the Porter services.
   
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
 
 - In your terminal `cd` to your Code directory where your sites are located, and run `porter begin`
 
 ## Notes
 - Sites are added manually for use with Porter. It means we can set up Nginx sites neatly.  Add them by heading to the directory and running `porter sites:unsecure` or `porter sites:secure`. Alternative run `porter sites:unsecure {site}` or `porter sites:secure {site}` from anywhere.
 - Change the php version for a site with `porter sites:php {site}`
 
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

 - `porter sites:list` 
 - `porter sites:unsecure {site}` - Set up a site to use http
 - `porter sites:secure {site}` - Set up a site to use https
 - `porter sites:remove {site}` - Remove a site 
 - `porter sites:php {site?}` - Choose the PHP version for site
 - `porter sites:nginx-config {site?}` - Choose NGiNX config template for a site, ships with default (/public such as Laravel) and project_root

### PHP
 - `porter php:default` - Set default PHP version
 - `porter php:list` - List the available PHP versions
 - `porter php:open {version?}` - Open the PHP cli for the project, run in project dir, or run a specific version

### Node (npm/yarn)
 - `porter node:open` - Open Node cli, run in project dir

### MySQL
Enabled by default available on the host machine @ localhost:13306. 

 - `porter mysql:on`
 - `porter mysql:off`
 - `porter mysql:open` - Open MySQL cli

### Redis

Enabled by default.  Available on the host machine @ localhost:16379.

 - `porter redis:on`
 - `porter redis:off`
 - `porter redis:open` - Open Redis cli
