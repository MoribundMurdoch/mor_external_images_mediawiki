<?php
/**
 * ExternalImages
 *
 * Adds <extimg> for safely embedding whitelisted external images.
 *
 * Example:
 * <extimg src="https://pbs.twimg.com/media/example.jpg" alt="Example image" width="600" />
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

$wgExtensionCredits['parserhook'][] = [
	'name' => 'ExternalImages',
	'author' => 'MoribundMurdoch',
	'description' => 'Allows safe embedding of whitelisted external images via <extimg>.',
	'version' => '1.3',
];

$GLOBALS['wgExternalImagesAllowedHosts'] ??= [
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

$GLOBALS['wgExternalImagesDefaultWidth'] ??= 600;
$GLOBALS['wgExternalImagesMaxWidth'] ??= 1200;
$GLOBALS['wgExternalImagesMaxHeight'] ??= 1200;

$wgHooks['ParserFirstCallInit'][] = static function ( Parser $parser ) {
	$parser->setHook( 'extimg', 'ExternalImagesRender' );
	return true;
};

function ExternalImagesRender( $input, array $args, Parser $parser, PPFrame $frame ) {
	$src = trim( $args['src'] ?? '' );
	$alt = trim( $args['alt'] ?? '' );
	$title = trim( $args['title'] ?? '' );

	$defaultWidth = (int)( $GLOBALS['wgExternalImagesDefaultWidth'] ?? 600 );
	$maxWidth = (int)( $GLOBALS['wgExternalImagesMaxWidth'] ?? 1200 );
	$maxHeight = (int)( $GLOBALS['wgExternalImagesMaxHeight'] ?? 1200 );

	$width = isset( $args['width'] ) ? (int)$args['width'] : $defaultWidth;
	$height = isset( $args['height'] ) ? (int)$args['height'] : 0;

	$class = trim( $args['class'] ?? 'extimg' );

	if ( $src === '' ) {
		return ExternalImagesError( 'Missing image source.' );
	}

	$parsed = parse_url( $src );

	if (
		!$parsed ||
		!isset( $parsed['scheme'], $parsed['host'] ) ||
		strtolower( $parsed['scheme'] ) !== 'https'
	) {
		return ExternalImagesError( 'Invalid image source. Only HTTPS image URLs are allowed.' );
	}

	$host = strtolower( $parsed['host'] );

	if ( !ExternalImagesHostIsAllowed( $host ) ) {
		return ExternalImagesError( 'External image domain is not allowed.' );
	}

	if ( !ExternalImagesLooksLikeImage( $src ) ) {
		return ExternalImagesError( 'External image URL does not look like a direct image.' );
	}

	$width = ExternalImagesClampInt( $width, 1, $maxWidth, $defaultWidth );

	if ( $height > 0 ) {
		$height = ExternalImagesClampInt( $height, 1, $maxHeight, 0 );
	}

	$safeSrc = htmlspecialchars( $src, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
	$safeAlt = htmlspecialchars( $alt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
	$safeTitle = htmlspecialchars( $title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
	$safeClass = htmlspecialchars( $class, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

	$html = '<img'
		. ' src="' . $safeSrc . '"'
		. ' alt="' . $safeAlt . '"'
		. ' width="' . $width . '"';

	if ( $height > 0 ) {
		$html .= ' height="' . $height . '"';
	}

	if ( $title !== '' ) {
		$html .= ' title="' . $safeTitle . '"';
	}

	if ( $class !== '' ) {
		$html .= ' class="' . $safeClass . '"';
	}

	$html .= ' loading="lazy"'
		. ' decoding="async"'
		. ' referrerpolicy="no-referrer"'
		. '>';

	return [
		$html,
		'noparse' => true,
		'isHTML' => true,
	];
}

function ExternalImagesHostIsAllowed( string $host ): bool {
	$allowedHosts = $GLOBALS['wgExternalImagesAllowedHosts'] ?? [];

	foreach ( $allowedHosts as $allowedHost ) {
		$allowedHost = strtolower( trim( (string)$allowedHost ) );

		if ( $allowedHost === '' ) {
			continue;
		}

		if ( $host === $allowedHost ) {
			return true;
		}
	}

	return false;
}

function ExternalImagesLooksLikeImage( string $url ): bool {
	$path = parse_url( $url, PHP_URL_PATH );

	if ( !is_string( $path ) || $path === '' ) {
		return false;
	}

	$path = strtolower( $path );

	$allowedExtensions = [
		'jpg',
		'jpeg',
		'png',
		'gif',
		'webp',
		'avif',
	];

	$extension = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );

	if ( in_array( $extension, $allowedExtensions, true ) ) {
		return true;
	}

	$imagePathPatterns = [
		'/media/',
		'/profile_banners/',
		'/img/',
		'/images/',
		'/image/',
		'/photo/',
		'/photos/',
		'/public/',
		'/feed_fullsize/',
		'/bafkre',
		'/bafy',
	];

	foreach ( $imagePathPatterns as $pattern ) {
		if ( strpos( $path, $pattern ) !== false ) {
			return true;
		}
	}

	return false;
}

function ExternalImagesClampInt( int $value, int $min, int $max, int $fallback ): int {
	if ( $value < $min ) {
		return $fallback;
	}

	if ( $value > $max ) {
		return $max;
	}

	return $value;
}

function ExternalImagesError( string $message ) {
	$safeMessage = htmlspecialchars( $message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

	return [
		'<strong class="error extimg-error">' . $safeMessage . '</strong>',
		'noparse' => true,
		'isHTML' => true,
	];
}