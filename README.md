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
Add porter to $PATH for simplicity.

 - `porter begin` - initial set up (migrations etc), run in code home to set immediately

 - `porter start`
 - `porter stop`
 - `porter build` - (re)build the containers

### Basic settings

 - `porter sites:tld {tld}` - set tld
 - `porter sites:home {dir?}` - set the home dir for sites, run in the dir to use it directly
 - `porter sites:default-php` - select the default php version to use
 
 ### Site settings
 
 - `porter sites:unsecure {site}` - add an unsecured site
 - `porter sites:secure {site}` - add a secured site
 - `porter sites:remove {site}` - remove a site 
 - `porter sites:php {site?}` - choose php version for site
 - `porter sites:nginx-type {site?}` - choose nginx conf type to use, ships with default (/public such as Laravel) and project_root

### Working on the cli (composer/npm etc.)
 - `porter php {version?}` - enter the php cli container for the project, run in project dir, or set version.
 - `porter node` - run in project dir
