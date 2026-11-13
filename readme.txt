=== MK Simple Scroll Progress ===
Tags: scroll, progress, reading
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, configurable scroll progress bar for your WordPress site.

== Description ==

Display a decorative progress bar at the top or bottom of the page. Customize its color, opacity, height and corner radius, or exclude individual pages/posts by ID. No tracking, external services or jQuery.

The bar measures scrolling through the whole document. It updates when the page is resized, restored or its ordinary content height changes.

== Installation ==

1. Upload the plugin ZIP through Plugins > Add New > Upload Plugin.
2. Activate MK Simple Scroll Progress.
3. Open Scroll Progress in the main admin menu to configure it.

== Frequently Asked Questions ==

= Can I exclude the blog page? =

Yes. Add the ID of the page assigned as your posts page under Settings > Reading.

= Why is the bar empty on a short page? =

There is no scrollable distance, so the bar stays empty.

= Are settings retained when updating? =

Yes. Valid existing settings are supported. Numeric settings are limited to opacity 0–1, height 1–100px and radius 0–100px. Invalid settings use defaults.

== Changelog ==

= 1.0.1 =
* Fix PHP compatibility, validation, page exclusions and the Settings link.
* Fix scroll calculations and updates after layout changes.
* Add a native color selector, accessible labels and regression tests.

= 1.0.0 =
* Initial release.
