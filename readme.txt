=== Post Author Comment Notification ===
Contributors: axelseaa
Tags: comment, notification, author
Requires at least: 2.3
Tested up to: 2.6
Stable tag: trunk

== Description ==

Overrides the `wp_notify_moderator` function located in pluggable.php.  By default, wordpress does not notify a comment author of a moderation 
event.  This plugin will look at all users in the blog, and notify anyone who can moderate the plugin, along with the admins and the author 
of that post.  This has been tested and does work with WPMU as well.

== Installation ==

For Wordpress:

1. Unzip the ZIP file and drop the folder straight into your `wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

For Wordpress MU:

1. Unzip the ZIP file and drop the folder straight into your `wp-content/mu-plugins/` directory.
2. That's it!
