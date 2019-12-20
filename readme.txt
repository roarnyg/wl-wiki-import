=== WL Wikipedia Import og Forfatterdatabase ===
Contributors: iverok, wphostingdev, webloeft
Tags: books, authors, webloeft
Requires at least: 5.0
Tested up to: 5.4
Stable tag: trunk
Requires PHP: 7.0
License: AGPLv3 or later
License URI: http://www.gnu.org/licenses/agpl-3.0.html

This plugin will create an 'Author' custom post type and integration with WP, the contents of which will be completed from Wikipedia.

== Description ==

This plugin will create a custom post type 'Author' and fill it with custom fields using the Advanced Custom Fields. It will also augment the search features with these special fields (filter by region, gender etc). These posts will be inserted directly from Wikipedia and be synchronized with that. Intended to create and semi-automatically keep a database of all autors for a region.

= Shortcodes =
 * `[advanced_search]` - prints out a search form with filters for municipality, gender, genre, period etc
 * `[authors]` - prints out a list of the authors

== Requirements ==
This plugins requires Advanced Custom Fields

== Installation ==

Upload this plugin to your blog. On the settings page, select your WikiPedia source and your region


== Changelog ==

= 1.0.0 - 2019-xx-xx =
* Initial version
