=== Age Gate ===
Contributors: philsbury
Tags: age, age verification, age gate, adult, age restriction, age verify, adults-only, modal, over 16, over 18, over 19, over 20, over 21, pop-up, popup, restrict, splash, beer, alcohol, tobacco, vape, restriction
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donate%40wordpressagegate%2ecom&lc=GB&item_name=Age%20Gate&item_number=Age%20Gate%20Donation&no_note=0&currency_code=GBP&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Requires at least: 6.0.0
Requires PHP: 7.4
Tested up to: 6.1.1
Stable tag: 3.0.11
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to check the age of a visitor before view site or specified content

== Description ==
There are many uses for restricting content based on age, be it movie trailers, beer or other adult themes. This plugin allows you to set a restriction on what content can been seen or restricted based on the age of the user.

__Features__

* Ask users to verify their age on page load
* SEO Friendly - common bots and crawlers are omitted from age checks
* Ability to add custom user agents for less common bots
* Shortcode for in content restrictions
* Choose to restrict an entire site, or selected content
* Select a different age on individual content
* Allow certain content to not be age gated under "all content" mode
* Three choices for input; dropdowns, input fields or a simple yes/no button
* Customise the order of the inputs based on your region (DD MM YYYY or MM DD YYYY)
* Allow a "remember me" check box if desired
* Ability to omit logged in users from being checked
* Add your own logo
* Update the text displayed on the entry form
* Select background colour/image, foreground colour and text colour
* Use built in styling out of the box, or your own custom style
* Ability to add legal note or information to the bottom of the form
* Redirect failed logins to a URL of your choice e.g. an alcohol awareness website.
* Ability to use a non caching version
* Various hooks to add even more customisation such as additional form fields
* Compatible with multilingual plugins WPML, Polylang (2.3+), WP Multilang

== Installation ==
1. Upload the 'age-gate' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit 'Age Gate' admin section and adjust your configuration.

__Important__
Be sure to check the 'Access' settings tab to grant permissions and omit any post types you don't wish to display Age Gate options on.

== Frequently Asked Questions ==
= I can't get past the Age Gate / The Age Gate only worked once =

The most likely cause for this is caching on your site either via a plugin or your hosting provider. If the Age Gate appears stuck try using the JavaScript mode in the advanced tab and clear any caches.

= Can I restrict a particular page? =

You can. If you use selected content, a checkbox will appear in content pages

= Can I add my own logo/branding? =

Of course, it's your site

= I'm in X country, can I format the date style? =

Yes! DD MM YYYY and MM DD YYYY are supported along with a choice of how the dates are entered.

= I use caching, will that be affected? =

From version 1.4.0 those using caching can select the "Cache Bypass" option to allow age gating even with caching on. Be sure to empty your cache when making changes to the plugin settings. From 2.0.0 this option is in Advanced -> Use uncachable version

== Screenshots ==
1. An example of Age Gate
2. The Restrictions settings page
3. Age Gate has a variety of customisable message settings
4. The appearance can be tailored to fit your website
5. Advanced options include the use of a JavaScript Age Gate and a custom CSS editor
6. Manage what users can change Age Gate's setting, restrict posts and exclude settings from certain post types.

== Changelog ==

= 3.0.10 =
* Fixed - Issue where Breeze cache wouldn't render Age Gate.
* Updated - translation file

= 3.0.10 =
* Fixed - Error message translation
* Fixed - Autotabbing when using regions addon
* Fixed - Multilingual messages not translation if langauage is the same
* Fixed - potential error thrown in array flatterning method
* Fixed - Anonymous mode allowing undesired users through
* Fixed - Selects not repopulating in PHP mode
* Added - option to sort select years
* Added - option to render loading icon as image
* Changed - default minimum select year to 1900
* Changed - button submission handler to (hopefully) resolve lingering mobile issues
* Removed - unused admin modal styles

= 3.0.9 =
* Fixed - remove buttonshowing even with no image selected
* Added - Legacy hook for age_gate_logo
* Added - New hooks for logo manipulation

= 3.0.8 =
* Fixed - Multilingual fields missing since 3.0.6
* Added - CSS Variable --ag-transition-timing
* Changed - CSS Variable --ag-transition to --ag-transition-duration

= 3.0.7 =
* Fixed - Wrong input being selected by default when not DDMMYYYY
* Fixed - JS hooks continuously rechallenging users
* Fixed - toolbar toggle inoperable on insecure sites

= 3.0.6 =
* Fixed - JS Hook when age gate already passed
* Fixed - JavaScript exit transitions
* Added - AJAX fallback if REST unreachable
* Added - age_gate_hidden JavaScript event
* Changed - Admin nav behaviour
* Changed - Admin validation behaviour

= 3.0.5 =
* Fixed - typos in README
* Fixed - additional tools not displaying
* Fixed - per page controls not showing
* Added - checks for dependencies
* Added - filter for cookie domain
* Changed - default cookie domain is current domain
* Changed - cookie name filter
* Changed - default colours in line with v2
* Changed - Legacy hook class name

= 3.0.4 =
* Fixed - Restriction display in admin
* Fixed - Implemented new polyfill for Safari event submitter
* Fixed - Phantom validation errors in Content section
* Fixed - Double escaping in buttons prompt
* Added - cleanup for previous cron schedule
* Changed - Removed use of PHP short tags

= 3.0.3 =
* Fixed - Fatal error under some settings combination
* Fixed - Hide display of default scrollbar

= 3.0.2 =
* Fixed - 0 valued opacity not being reflected on the site
* Fixed - iOS Safari scrolling when Age Gate visible
* Fixed - Older versions of Safari not operating with buttons
* Fixed - Inability to store appearance settings
* Fixed - Improved data sanitation
* Fixed - Custom title output in js/munge mode
* Added - CSS variable for loader colour
* Added - Default post types to ignore in admin e.g. shop orders or attachment
* Changed - Default max-width on logo to be 100%
* Changed - Button styles prefixed with element
* Changed - Cookie length filter to be time and length

= 3.0.1 =
* Added - Standard cookie length filter age_gate/cookie/length
* Added - PHP version check message
* Fixed - per content toggle display incorrect status
* Fixed - minor stylistic elements for wider default support
* Fixed - Javascript error in focus trap when using buttons
* Fixed - Excessively strict validation rules
* Fixed - Escaped characters showing on front end
* Removed - API Error warning as return false flags
* Removed - Unused API endpoint

= 3.0.0 =
* Complete rewrite
* Improved Taxonomy inheritance
* Removed: Use of admin-ajax in JS mode
* Removed: Custom CSS editor
* Removed: jQuery dependencies
* Changed: Custom editor to support markdown and be stricter
* Added: Ability to override templates in theme
* Added: Easily add classes and other attributes
* Added: Option to load Age Gate earlier in the DOM
* Various performance and security improvements


== Upgrade Notice ==
= 2.16.3 =
* This release fixes a possible, though relatively unlikely, vulnerability to XSS.

= 2.13.5 =
* Fixed a potential security risk. Update recommended.

= 2.3.0 =
* Background colour now has it's own element `.age-gate-background-colour` rather that styling `.age-gate-wrapper`

= 2.2.4 =
* Fixes an issue in Safari (Desktop and Mobile) where the Age Gate wasn't displayed.

= 2.0.6 =
* Critical fix for anyone on a standard timezone setting in Wordpress

= 2.0.1 =
* Fixes issue where users could not register regardless of Age Gate settings
* Fixes and issue where posts home would require Age Checking if the first post was restricted
* Minor CSS update for themes not using border-box
* Fixed missing closing form tag in admin

= 2.0.0 =
* Completely reworked, this release should be considered a breaking change. Testing on local/staging environment is recommended.

= 1.4.8 =
* Adds user preference to alter page title when Age Gate is displayed
* Added additional test for Bots in "Cache Bypass" version

= 1.4.5 =
* Woocommerce users using "Selected content" should update to show age gate on the product page

= 1.4.1 =
* Fixes a bug when using Cache Bypass mode but not using Remember me

= 1.4.0 =
* Adds support for sites with caching enabled

= 1.2.0 =
* Contains vital fix for correct input not accepting some browsers
