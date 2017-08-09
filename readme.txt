=== Fix Multiple Redirects ===
Contributors: jurajpuchky
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NSHFBHQ9XM9NJ
Tags: multiple, canonical, redirects, fix, seo, htaccess, speed, tune up, fix www
Requires at least: 2.6
Tested up to: 3.3.2 CS
Stable tag: 1.2.3
Version: 1.2.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Fix multiple redirects and canonical redirects. And increase speed of wordpress fine url recognition for SEO by .htaccess file.

== Description ==

Help users to fix problems with multiple redirects and canonical redirects.
- advice to configure DNS records, prevent canonical problems.
- By disable canonical redirect filters.
- By disable redicrecting filters.
- By fixing .htaccess file.
- Some FAQ with experience.
- Increase speed of wordpress fine url recognition for SEO by .htaccess file.
- Fixed issue with force redirect to WWW
- If you wish you can promote us, with small link in tail of post/page or donate only ONE $.

Home page: [Devtech](http://www.devtech.cz/ "Devtech - supports preshashop plugin,wordpress plugin,vpn,b2b,eshop,blog,annonce,link,seo,proxy,mailing,affiliate")

== Installation ==

1. Upload plugin folder `fix-multiple-redirects` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Setup options 'Settings/Multiple Redirects Fix' menu in WordPress by default are all settings not configured, follow Frequently Asked Questions.
4. You can promote us, with small link in tail of post/page or donate only ONE $.

== Frequently Asked Questions ==

What i need to know about configuration, before i start configure?
Configuration of fix is devided to few main parts
- configure properly your DNS records
       specify direct A record for subdomain.domain.tld address
       - setup same domain in General Options, http://subdomain.domain.tld and address of showed URL.
       - other else canonical issue will solve part of .htaccess file, followed.
       - dont use only CNAME *.domain.tld with A domain.tld record!
- for debuging extend your apache server config file with following configuration in VirtualHost part
        <IfModule mod_rewrite.c>
        RewriteLog /home_path/logs/rewrite_log
        RewriteLogLevel 3
        </IfModule>
- disabling canonical redirect filters
- disabling redirect filters
- fix .htaccess file by manualy or generated/replacing existing one.
Purpose of this fix is to prevent cyclic redirects and coninical issues for subdomain replacing, you have to go carefuly throuth filters and .htaccess rules
and disable all options which are going to do redirect of cycled urls, what is our issue.

Other known issues
- Fix JavaScript code for Google Plus One Like button, which contained buggy code with multiple redirecting or remove component
from page.
- Remove another JavaScript code which doing replace of URL over window.location object seems to be buggy for IE6, Firefox.

Which configuration is already tested in Wordpress 3.3.2?
- Disable following canonical filters:
- Disable following redirect filters:
- Use option to fix .htaccess by replacing existing one
* remove Google One button and others buggy JavaScripts

== Screenshots ==

Not needed yet.

== Changelog ==

= 1.2.3 =
* Fixed issue with force redirect to WWW

= 1.2.2 =
* Fixed description

= 1.2.1 =
* Fixed version number

= 1.2 =
* Extended .htaccess with fixing duplicity of index.php in SEO stats

= 1.1 =
* Fixed module init

= 1.0 =
* Initial version

== Upgrade Notice ==

= 1.2.3 =
* Fixed issue with force redirect to WWW

= 1.2 =
* Extended .htaccess with fixing duplicity of index.php in SEO stats

= 1.1 =
* Fixed module init

= 1.0 =
* Initial version
