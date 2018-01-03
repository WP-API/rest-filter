=== WP REST Filter ===
Contributors: jack50n9, sk8tech, rachelbaker, joehoyle
Donate link: https://sk8.tech
Tags: wp, rest, api, rest api, filter, acf, cpt, json
Requires at least: 4.7.0
Tested up to: 4.9.1
Requires PHP: 5.2.4
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Since WordPress 4.7 the `filter` argument for any post endpoint was removed. This plugin restores the `filter` parameter for websites that were previously using it.
 
== Description ==
 
The `filter` argument in WP REST API allows the posts to be filtered using `WP_Query` public query vars. 

Since WordPress 4.7 the `filter` argument for any post endpoint was removed. 

This plugin restores the `filter` parameter for websites that were previously using it.

= Usage =

Use the `filter` parameter on any Post, or Custom Post Type endpoint such as `/wp/v2/posts` or `/wp/v2/cpt` as an array of `WP_Query`
argument. ACF values are also supported. 

Default Post:
```
fetch( 'https://domain.com/wp-json/acf/v3/post?filter[meta_key]=acfkey&filter[meta_value]=acfvalue');
```

Custom Post Type
```
// fetch( 'https://domain.com/wp-json/acf/v3/customposttype?filter[meta_key]=acfkey&filter[meta_value]=acfvalue');
fetch( 'https://domain.com/wp-json/acf/v3/ads?filter[meta_key]=currency&filter[meta_value]=AUD'); // here 'ads' is the endpoint for CPT
```

 
== Installation ==
  
1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
 
== Frequently Asked Questions ==
 
= Why do I need WP REST Filter? =
 
If you're planning on using your WordPress website as a Backend, and you're consuming RESTful api, you'll most probably need to filter results on your GET request. This is percicely what this plugin does.
 
= Does this work with Custom Post Type? =
 
Yes! This plugin is designed to work with CPT types too!
 
= Does this work with Advanced Custom Field? =
 
Yes! This plugin is designed to work with ACF values too!
 
= There's a bug, what do I do? =

Issues and [pull requests](https://github.com/sk8-pty-ltd/wp-rest-filter/pulls) are welcome at [Github repo](https://github.com/sk8-pty-ltd/wp-rest-filter).
 
== Screenshots ==
 
1. /assets/screenshot.png
 
== Changelog ==
 
= 1.0.1 =
* Minor README fixes.
 
= 1.0.0 =
* Initial Release.

== Upgrade Notice ==

Nothing to worry! Install away!
 
== Contact Us ==

Based in Sydney, [SK8Tech](https://sk8.tech) is a innovative company providing IT services to SMEs, including [Web Design](https://sk8.tech/services/web-design), App Development and more.