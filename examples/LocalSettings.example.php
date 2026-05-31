<?php

require_once "$IP/extensions/ExternalImages/ExternalImages.php";

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

	// Mastodon / Fediverse image hosts
	'files.mastodon.social',
	'media.mastodon.social',
	'cdn.masto.host',

	// Lemmy / Pictrs
	'pictrs.lemmy.world',
	'lemmy.world',

	// Your Neocities sites
	'42mur.neocities.org',
	'moribundmurdoch.neocities.org',
];

$wgExternalImagesDefaultWidth = 600;
$wgExternalImagesMaxWidth = 1200;
$wgExternalImagesMaxHeight = 1200;
