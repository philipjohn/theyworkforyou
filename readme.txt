=== TheyWorkForYou ===
Contributors: philipjohn
Donate link: http://www.mysociety.org/donate
Tags: theyworkforyou, democracy, politics
Requires at least: 5.8.0
Tested up to: 5.9.1
Stable tag: 1.0.1
License: WTFPL
License URI: http://wtfpl.net

Provides tools for bloggers based on mySociety's TheyWorkForYou.com

== Description ==

TheyWorkForYou lets you find out what your MP, MSP or MLA is doing in your name, read debates, written answers, see what’s coming up in Parliament, and sign up for email alerts when there’s past or future activity on someone or something you’re interested in.

This plugin adds a block to your WordPress site that allows you to show the recent activity of an MP.

== Installation ==

See http://codex.wordpress.org/Managing_Plugins#Installing_Plugins

Then, grab an API key from [TheyWorkForYou.com](http://www.theyworkforyou.com/api/key) and enter it at Settings > TheyWorkForYou.

== Frequently Asked Questions ==

= Can I remove the "Data service provided by TheyWorkForYou" notice? =

Nope! It's a kinda requirement of [TWFY's API](http://www.theyworkforyou.com/api/) and besides, you should be glad to point folks at their amazing resource.

== Changelog ==

= 1.0.0 =
* Completely re-factored to be Gutenberg compatible, using a block instead of a widget!

= 0.4.2 =
* TWFY attribution

= 0.4.1 =
* Fixing bug where MP activity isn't refreshed when changed in the widget

= 0.4 =
* Structural changes
* Implemented API key setting

= 0.3 =
* Implemented caching of MP activity

= 0.2 =
* Implementing caching of MPs list

= 0.1 =
* Initial plugin, revived from 2009 version
* MPs Recent Activity widget

== Upgrade Notice ==

= 1.0.0 =
The widget is removed in this version, and will need to be replaced on your site by the new block.

= 0.1 =
If using v0.1b you must first uninstall and remove that plugin, before installing this version.
