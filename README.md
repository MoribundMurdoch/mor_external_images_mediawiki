# Mor External Images for MediaWiki

A small MediaWiki parser tag extension for safely embedding whitelisted external images with an `<extimg>` tag.

## Related Tools

- Config builder blog post: https://techmoribundmurdoch.blogspot.com/2026/05/mor-externalimages-config-builder-for.html
- Browser extension helper: https://github.com/MoribundMurdoch/mor_mediawiki_external_image_gui_helper

## Example

```wiki
<extimg src="https://pbs.twimg.com/media/example.jpg" alt="Example image" width="600" />
Purpose

MediaWiki normally prefers locally uploaded files. This extension adds a controlled way to embed external images from explicitly allowed image hosts.

It is designed for personal, indie, educational, or small MediaWiki installs where the administrator wants to allow external images from selected image/CDN hosts.

Administrators choose which external image hosts they trust.

Installation

Copy this repository into your MediaWiki extensions folder:

extensions/ExternalImages/

Then add this to LocalSettings.php:

require_once "$IP/extensions/ExternalImages/ExternalImages.php";
What is LocalSettings.php?

LocalSettings.php is the main configuration file for a MediaWiki website.

It is where a wiki administrator loads extensions, changes site settings, and controls how the wiki behaves.

For this extension, LocalSettings.php is where you choose which external image hosts your wiki trusts.

Configuration

You can use the config builder here:

https://techmoribundmurdoch.blogspot.com/2026/05/mor-externalimages-config-builder-for.html

Or write the config manually:

$wgExternalImagesAllowedHosts = [
	'pbs.twimg.com',
	'blogger.googleusercontent.com',
	'i.imgur.com',
	'imgur.com',
	'preview.redd.it',
	'i.redd.it',
	'external-preview.redd.it',
	'64.media.tumblr.com',
	'cdn.bsky.app',
	'imgflip.com',
	'i.imgflip.com',
];

$wgExternalImagesDefaultWidth = 600;
$wgExternalImagesMaxWidth = 1200;
$wgExternalImagesMaxHeight = 1200;
Supported attributes
<extimg
  src="https://i.imgur.com/example.png"
  alt="Example image"
  title="Example title"
  width="600"
  height="400"
  class="extimg"
/>
Security Notes

This extension does not fetch remote images server-side. It only emits an HTML <img> tag after validating the URL scheme and host.

Only HTTPS URLs are allowed.
Only configured hosts are allowed.
Width and height are clamped.
HTML attributes are escaped before output.
referrerpolicy="no-referrer" is added to generated image tags.
License

MIT.