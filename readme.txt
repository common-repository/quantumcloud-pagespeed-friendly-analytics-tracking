=== QuantumCloud PageSpeed Friendly Analytics Tracking ===
Contributors: quantumcloud
Donate link: http://www.quantumcloud.com
Tags: analytics, google analytics, analytics tracking code, pagespeed, improve pagespeed, pagespeed score, leverarge browser caching, tracker, analytics tracker, Google analytics tracker
Requires at least: 4.0
Tested up to: 6.1
Requires PHP: 5.4.0
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.quantumcloud.com

QuantumCloud PageSpeed Friendly Analytics Tracking plugin adds the tracking code to all pages of your WordPress site. 

== Description ==
QuantumCloud PageSpeed Friendly Analytics Tracking for Google does the simple job of adding tracking code to your WordPress website in all pages. But in addition to that it solves another annoying issue with google pagespeed. Have you ever tried to increase your pagespeed score in google pagespeed insight? If you did, then you most likely ran into an annoying issue as part of the last few issues to solve. Google’s pagespeed bot will report in the Leverage browser caching section that http://www.google-analytics.com/analytics.js has an expiry date set to only 2 hours and thus prevent you from getting a Passed rule marker for the Leverage browser caching area.

Most of us cannot avoid using google analytics for statistics on our website. But how can you control page expiry date on google server? You cannot. This is one of those stupid things we have to deal with. There are complex work-arounds for this. Like copying the analytics code to your own server and then run a cron job to update the analytics javascript every few hours. These work-arounds are usually quite complex and not recommended.

QuantumCloud's Google Analytics Tracker Plugin for WordPress is Google pagespeed friendly. The issue is overcome by not adding the analytics code in your source code when the plugin detects that it is google pagespeed bot that is requesting the page. The option is turned off by default. You can turn on the options should you want a perfect score for Leverage browser caching.


Website: http://www.quantumcloud.com/
Support Plugin Page: https://www.quantumcloud.com/blog/wordpress/pagespeed-friendly-analytics-tracking-code-plugin-wordpress/

== Installation ==

1. Download the plugin zip file. Extract and upload quantumcloud-pagespeed-friendly-analytics-tracking in your wp-content/plugins folder.
2. From the wp-admin panel go to plugins and activate "QuantumCloud PageSpeed Friendly Analytics Tracking"
3. Go to Settings and Add your tracking code
4. You are done.

== Frequently Asked Questions ==
Please check: https://www.quantumcloud.com/blog/wordpress/google-pagespeed-friendly-analytics-tracking-code-plugin-wordpress/


== Use ==
1. After activating the plugin, add your analytics tracking id
2. Select the option Increase Page Speed if you want to hide the analytics code from Google Pagespeed bot and improve your Google pagespeed score.

== Screenshots ==

1. QuantumCloud Pagespeed Friendly Analytics Tracking settings page
2. QuantumCloud Page Speed Leverage Browser Caching Failed
3. QuantumCloud Page Speed Leverage Browser Caching Passed
 



== Changelog ==
1.2.0
Removed allow url fopen requirement

= 1.0 =
*Initial version of QuantumCloud PageSpeed Friendly Analytics Tracking

= 1.0 =

 == Upgrade Notice ==
 
* Initial version of QuantumCloud PageSpeed Friendly Analytics Tracking