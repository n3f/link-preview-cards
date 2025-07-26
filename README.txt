=== Link Preview Cards ===
Contributors: neffff
Tags: open-graph, embed, block, gutenberg, social-media
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 8.2
Stable tag: 0.1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Plugin URI: https://github.com/n3f/link-preview-cards
Author URI: https://github.com/n3f

A Gutenberg block that displays link preview cards for any URL, replacing
traditional embeds with rich social media previews.

== Description ==

Link Preview Cards is a Gutenberg block that fetches and displays link preview
data from any URL, creating beautiful social media-style cards instead of
traditional embeds.

**Features:**

* Automatically fetches link preview metadata (title, description, image)
* Displays cards in a social media style format
* Supports alignment options (left, center, right, wide, full)
* Works with any URL that has link preview metadata
* Lightweight and fast

**Perfect for:**
* Blog posts linking to external articles
* Social media previews
* Rich link previews
* Content curation

Simply add the "Link Preview Cards" block, paste a URL, and the plugin will
automatically fetch and display the link preview data in a beautiful card format.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/link-preview-cards` directory, or install the plugin through the WordPress admin interface.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the "Link Preview Cards" block in the Gutenberg editor

== Frequently Asked Questions ==

= What is link preview data? =

Link preview data includes metadata like title, description, and image that
websites provide to create rich previews when shared on social media platforms.

= Does this work with any URL? =

The URL must have [link preview metadata](https://ogp.me/) for the plugin to
display rich data. If no link preview data is available, the block will show
basic link information.

= Is this compatible with my theme? =

Yes, this plugin works with any theme that supports Gutenberg blocks.

= Can I customize the card appearance? =

The card styling is designed to work well with most themes. Future versions may
include customization options.

== Screenshots ==

1. Link Preview Cards block in the editor
2. Example of a rendered link preview card

== Changelog ==

= 0.1.2 =
* Changed class and CSS structure for cards to avoid conflicts with "card".

= 0.1 =
* Initial release
* Basic link preview card functionality
* Gutenberg block integration

== Upgrade Notice ==

= 0.1.2 =
Fix for 'card' class names.

= 0.1.0 =
Initial version
