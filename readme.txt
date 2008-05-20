=== Accessible News Ticker (ANT) ===
Contributors: pixline
Donate link: http://pixline.chipin.com/
Tags: admin, post, javascript, scroller, news, ticker, widget, widgets, posts
Requires at least: 2.1
Tested up to: 2.5.1
Stable tag: 0.3.1

A news ticker/scroller widget that displays posts and RSS with accessible javascript).

== Description ==

A news ticker widget, able to display latest posts as well as remote RSS feed. This widget is written in Unobtrusive Javascript, so this means that it WILL degrade gracefully if users don't have javascript, allowing them as well as search engines to even index these links.

Based on the wonderful [DOMNews 1.0](http://onlinetools.org/tools/domnews/) (by Chris Heilmann) and [SimplePie RSS-parsing php class](http://simplepie.org)

[Accessible News Ticker support forum](http://talks.pixline.net/forum.php?id=4)

== Installation ==

IMPORTANT: This widget is tested on the default theme only, so it may require some hacks to suit your theme. If you need help please submit working markup and/or web location to see what the problem is, and I'll make my best to fix it.

REALLY IMPORTANT: Make sure that the plugin creates a folder `cache` in your wp-content folder to cache RSS feeds, and that this folder is writeable (chmod 755 or chmod 777).

REALLY REALLY IMPORTANT: check your theme's header.php and footer.php for presence of wp_head(); (in the <head> section) and wp_footer(); (near </body>), otherwise this plugin can't work. if you can't find them, INSERT THEM!

1. Place and activate like *all* the others WP plugins in the world.
1. (Optional) Tweak .css file to suit your need.
1. Enjoy! :-)

[Accessible News Ticker support forum](http://talks.pixline.net/forum.php?id=4)
