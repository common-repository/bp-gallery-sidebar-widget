=== BP-Gallery Sidebar Widget ===
Contributors: ecurtain
Donate link: http://crimsoncurtain.com/sites/wpdev
Tags: BuddyPress, image, picture, photo, widgets, gallery, images, bp-gallery
Requires at least: WordPress 2.9.2, BuddyPress 1.2.2.1
Tested up to: 2.9.2
Stable tag: 1.2

A widget to show photos from BP-Gallery galleries in your sidebar.

== Description ==

NOTE:  This plugin works with BP Gallery RC2 and RC3.

I needed a sidebar solution to show links to bp-gallery galleries, so I wrote this widget. This widget selects from galleries of
type "photo".  The widget lets you specify the following parameters

- Maximum Galleries: the number of galleries to show.  
- Gallery Order: choose from Random, Earliest, and Latest
- Thumbnail: choose cover, first, or random image to determine which image is used for a thumbail in the sidebar.  Cover image is specified in bp-gallery. 
- Thumbnail width/height: the HTML attributes width & height.
- Thumbnail padding:  used for padding-right and padding-bottom for each thumbnail

== Installation ==

1. Upload `bp-gallery-sidebar-widget.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the widget in the widget editor.
4. For best results, use the same dimensions for thumnail display that are specified for bp-gallery.  
	The bp-gallery thumbnail is	used for this widget, so using the same size means nice thumbnails.

== Frequently Asked Questions ==

Q: Does this work standalone?
A: No, sorry, it requires the BP Gallery plugin installed in BuddyPress

Q: WordPress single-user vs. WP-MU -- does it matter?
A: Nope.  It should work fine for both.

== Changelog ==
	
= 1.2 =
* Added use of gallery slug, so that thumbails link to their galleries instead of their owner profiles.
= 1.1 =
* Modified for WP-MU compatability.  Now references the BP Gallery tables using the wpdb->base_prefix variable.

== Upgrade Notice ==

= 1.1 =
* This version is necessary for WP-MU compatability.
= 1.2 =
* This version now correctly links each thumbnail to its gallery.

== Screenshots ==

* screenshot-1.jpg - Shows the widget on the front end.  Thumbnail size is 120 x 100, and padding is 1.
* screenshot-2.jpg - Shows the back end.  

== Kudos ==

* Shout out to Brajesh Singh for creating the bp-gallery plugin 
* Shout out to Mathias Geat, whose nextgen-gallery-sidebar widget code was a big help while I was writing this plugin.
