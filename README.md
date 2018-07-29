# Porter

A docker based multi-site setup for local development.  Inspired by Laravel Valet, Homestead and Vessel.

For now, it uses the same DNSmasq that Valet sets up.  This also binds to ports 80 and 443, so you need to turn valet off before using it.

## Commands:
Add porter to $PATH for simplicity.

 - `porter begin` - initial set up (migrations etc), run in code home to set immediately

 - `porter start`
 - `porter stop`

 - `porter sites:tld {tld}` - set tld
 - `porter sites:home {dir?}` - set the home dir for sites, run in the dir to use it directly
 
 - `porter sites:unsecure {site}` - add an unsecured site
 - `porter sites:secure {site}` - add a secured site
 - `porter sites:remove {site}` - remove a site 
 - `porter sites:php {site?}` - choose php version for site

 - `porter php {version?}` - enter the php cli container for the project, run in project dir, or set version.
 - `porter node` - run in project dir
