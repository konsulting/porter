# Porter

A docker based multi-site setup for local development.  Inspired by Laravel Valet, Homestead and Vessel.

## Installation

We use Macs for development - and therefore only tested on there.

 - Install [Docker](https://www.docker.com/community-edition)
 - Set up routing. Use `/etc/hosts` or optionally install DMSmasq (or similar) to handle directing traffic to the Porter services. 
 
   (I am using the DNSmasq setup from Valet for now.)
 - Porter binds to ports 80 and 443, so you need to turn Valet off [or any other services that are bound to them] before using it.)
 - Clone Porter to a directory.
 
    `git clone git@github.com:konsulting/porter.git`
 
 - Add Porter to your $PATH (e.g. in .bash_profile)
 
    `export PATH="[path to porter]:$PATH"`
 
 - In your terminal `cd` to your Code directory where your sites are located, and run `porter begin`
 
 ## Notes
 - Sites are added manually for use with Porter. It means we can set up Nginx sites neatly.  Add them by heading to the directory and running `porter sites:unsecure` or `porter sites:secure`. Alternative run `porter sites:unsecure {site}` or `porter sites:secure {site}` from anywhere.
 - Change the php version for a site with `porter sites:php {site}`
 
## Commands:

 - `porter begin` - Initial set up (migrations etc), run in code home to set immediately

 - `porter start`
 - `porter stop`
 - `porter build` - (Re)build the containers
 - `porter make-files` - (Re)make all the files and restart. Run after changing config files.

### Basic settings

 - `porter tld {tld}` - Set tld ('test' is the default for domains such as sample.test)
 - `porter home {dir?}` - Set the home dir for sites, run in the dir to use it directly - or set it specifically
 
### Site settings
 
 - `porter sites:unsecure {site}` - Set up a site to use http
 - `porter sites:secure {site}` - Set up a site to use https
 - `porter sites:remove {site}` - Remove a site 
 - `porter sites:php {site?}` - Choose the PHP version for site
 - `porter sites:nginx-config {site?}` - Choose NGiNX config template for a site, ships with default (/public such as Laravel) and project_root

### PHP
 - `porter php:default` - Set default PHP version
 - `porter php:open {version?}` - Open the PHP cli for the project, run in project dir, or run a specific version

### Node (npm/yarn)
 - `porter node` - Open Node cli, run in project dir

### MySQL
Enabled by default available on the host machine @ localhost:13306. 

 - `porter mysql:on`
 - `porter mysql:off`
 - `porter mysql:open` - Open MySQL cli

### Redis

Enabled by default.  Available on the host machine @ localhost:16379.

 - `porter redis:on`
 - `porter redis:off`
 - `porter redis` - Open Redis cli
