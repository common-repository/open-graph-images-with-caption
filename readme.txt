=== Open Graph Images With Caption ===
Contributors: glashkoff
Donate link: https://glashkoff.com/donate/
Author URI: https://glashkoff.com
Plugin URI:	https://glashkoff.com/open-graph-images-with-caption
Tags: text picture, text pictures, open graph image, open graph, featured image, featured images, generate thumbnail, generate thumbnails, twitter cards, twitter, facebook, vk, vkontakte
Requires at least: 3.5
Tested up to: 5.1.1
Stable tag: 1.0.4
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate an Open Graph image with custom caption.

== Description ==

This plugin add box to posts editor to easy generate an Open Graph images from the post featured images and captions. In popular social networks that support Open Graph or Twitter Cards (Facebook, Google+, Twitter, VK.com (Vkontakte), OK.ru (Odnoklassniki)), links to pages on your site will look noticeable. This should improve traffic.
Colors and styles are customizable. If post has no featured image, plugin uses solid color.

== Installation ==

1. Upload to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings (optional, but encouraged)

== Frequently Asked Questions ==

= When does the plugin create an image for my new blog post? =

When you save post and click "Create images" in metabox "Open Graph Image".

= How to create own styled Open Graph images? =

In the future I want to add more different styles.
But there is one trick that allows you to use any image as Open Graph images: just put your images in the /wp-content/uploads/social dir with the following names:
xx-fb.jpg
xx-tw.jpg
Where "xx" - post or page ID. Image resolutions can be any, but i recommend 1200x630 for Facebook and 1024x512 for Twitter.
In other words, it is possible to activate my plugin and use third-party software to create any Open Graph images, they will be loaded automatically.

= Why plugin not generate images automatically? =

Because is so slowly. Might be take a time over default 60 seconds for PHP scripts, so might break saving post process.

= How about compatibility with SEO plugins? =

For SEO by Yoast you need uncheck "Generate all opengraph tags..." option, check "Enable Yoast SEO compatibility mode". Then everything will be fine.
For other plugins that generate Open Graph tags, uncheck the option "Generate all opengraph tags...". Also need to make sure that other plugins do not create the tags "og:image" and "twitter:image" in HTML code of pages.

= How to correctly uninstall plugin? =

1. Delete plugin in WP dashboard - Plugins (as all WP plugins are usually removed).
2. Delete folder with images: /wp-content/uploads/social.
Done!

== Screenshots ==
1. Plugin settings in admin panel
2. Plugin image creator in posts editor
3. Preview post with OG tags: Faceboook
4. Preview post with OG tags: vk.com
5. Preview post with OG tags: twitter
6. Different styles

== Changelog ==

= 1.0.4 =
* Fix for exclude archive and system pages

= 1.0.3 =
* Improved Yoast SEO compatibility

= 1.0.2 =
* Added images custom sizes
* Fix plugin name in readme.txt
* Small improvements and fixes

= 1.0.1 =
* Added styles
* Small improvements and fixes
* Changes file naming strategy

= 1.0.0 =
* Initial release