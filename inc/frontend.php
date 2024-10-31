<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'ogi_add_opengraph' );
function ogi_add_opengraph() {
	if ( is_singular() ) {
		global $post;
		$post_id    = $post->ID;
		$yoast_mode = get_option( 'ogi_og_yoast', '' );
		if ( is_single() and ogi_check_thumb( $post_id ) and 'yes' != $yoast_mode ) {
			if ( get_option( 'ogi_og_print_tags' ) == 'yes' ) {
				$fb         = '<meta property="og:type" content="article" />';
				$tw         = '<meta name="twitter:card" content="summary_large_image" />';
				$tw_site    = esc_attr( get_option( 'ogi_og_tw_site', '' ) );
				$tw_creator = esc_attr( get_option( 'ogi_og_tw_creator', '' ) );

				$fb .= PHP_EOL . '<meta property="og:site_name" content="' . ogi_clear_str( get_bloginfo( 'name' ) ) . '"/>';
				$fb .= PHP_EOL . '<meta property="og:url" content="' . get_the_permalink() . '"/>';
				$fb .= PHP_EOL . '<meta property="og:title" content="' . ogi_clear_str( get_the_title() ) . '"/>';
				$tw .= PHP_EOL . '<meta name="twitter:title" content="' . ogi_clear_str( get_the_title() ) . '"/>';

				if ( '' != $tw_site ) {
					$tw .= PHP_EOL . '<meta name="twitter:site" content="@' . ogi_clear_str( $tw_site ) . '"/>';
				}
				if ( '' != $tw_creator ) {
					$tw .= PHP_EOL . '<meta name="twitter:creator" content="@' . ogi_clear_str( $tw_creator ) . '"/>';
				}

				if ( has_excerpt() ) {
					$fb .= PHP_EOL . '<meta property="og:description" content="' . esc_attr( get_the_excerpt() ) . '"/>';
					$tw .= PHP_EOL . '<meta name="twitter:description" content="' . esc_attr( get_the_excerpt() ) . '"/>';
				} else {
					$fb .= PHP_EOL . '<meta property="og:description" content="' . esc_attr( get_the_content() ) . '"/>';
					$tw .= PHP_EOL . '<meta name="twitter:description" content="' . esc_attr( get_the_content() ) . '"/>';
				}

				$img_url_fb = wp_upload_dir()['baseurl'] . '/social/' . $post_id . '-fb.jpg';
				$img_url_tw = wp_upload_dir()['baseurl'] . '/social/' . $post_id . '-tw.jpg';
				$fb        .= PHP_EOL . '<meta property="og:image" content="' . $img_url_fb . '"/>';
				$tw        .= PHP_EOL . '<meta name="twitter:image" content="' . $img_url_tw . '"/>';
				printf( $fb . PHP_EOL . $tw . PHP_EOL );
			} else {
				$img_url_fb = wp_upload_dir()['baseurl'] . '/social/' . $post_id . '-fb.jpg';
				$img_url_tw = wp_upload_dir()['baseurl'] . '/social/' . $post_id . '-tw.jpg';
				$fb         = PHP_EOL . '<meta property="og:image" content="' . $img_url_fb . '"/>';
				$tw         = '<meta name="twitter:card" content="summary_large_image" />' . PHP_EOL . '<meta name="twitter:image" content="' . $img_url_tw . '"/>';
				printf( $fb . PHP_EOL . $tw . PHP_EOL );
			}
		}
	}
}

function ogi_clear_str( $clrstr ) {
	$clrstr = preg_replace( '/[\n\t\r]/', ' ', $clrstr );
	$clrstr = preg_replace( '/ {2,}/', ' ', $clrstr );
	$clrstr = preg_replace( '/ +$/', '', $clrstr );
	$clrstr = esc_attr( $clrstr );
	return $clrstr;
}

function ogi_check_thumb( $post_id ) {
	$img_file_fb = wp_upload_dir()['basedir'] . '/social/' . $post_id . '-fb.jpg';
	$img_file_tw = wp_upload_dir()['basedir'] . '/social/' . $post_id . '-tw.jpg';
	if ( file_exists( $img_file_fb ) and file_exists( $img_file_tw ) ) {
		return true;
	}
	return false;
}

add_filter( 'language_attributes', 'ogi_add_og_tag' );

function ogi_add_og_tag( $output ) {
	if ( is_singular() ) {
		global $post;
		$post_id    = $post->ID;
		$yoast_mode = get_option( 'ogi_og_yoast', '' );
		if ( is_singular() and ogi_check_thumb( $post_id ) and 'yes' != $yoast_mode ) {
			return $output . ' prefix="og: http://ogp.me/ns#"';
		} else {
			return $output;
		}
	}
}

function ogi_fb_yoast() {
	if ( ( 'yes' == get_option( 'ogi_og_yoast' ) ) & is_singular() ) {
		if ( in_array( 'wordpress-seo/wp-seo.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			global $post;
			$post_id    = $post->ID;
			$upload_dir = wp_upload_dir();
			$base_url   = $upload_dir['baseurl'];

			if ( metadata_exists( 'post', $post_id, '_ogi_fb_url' ) ) {
				if ( ! ( metadata_exists( 'post', $post_id, '_yoast_wpseo_opengraph-image' ) ) ) {
					add_filter( 'wpseo_opengraph_image', '__return_false' );
				}
			}
		}
	}
}
add_action( 'wp_head', 'ogi_fb_yoast', 1 );

function ogi_tw_yoast() {
	if ( ( 'yes' == get_option( 'ogi_og_yoast' ) ) & is_singular() ) {
		if ( in_array( 'wordpress-seo/wp-seo.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			global $post;
			$post_id    = $post->ID;
			$upload_dir = wp_upload_dir();
			$base_url   = $upload_dir['baseurl'];

			if ( metadata_exists( 'post', $post_id, '_ogi_tw_url' ) ) {
				if ( ! ( metadata_exists( 'post', $post_id, '_yoast_wpseo_twitter-image' ) ) ) {
					add_filter( 'wpseo_twitter_image', '__return_false' );
				}
			}
		}
	}
}
add_action( 'wp_head', 'ogi_tw_yoast', 1 );

add_action( 'wp_head', 'ogi_add_opengraph_yoast' );
function ogi_add_opengraph_yoast() {
	if ( is_singular() ) {
		$post_id    = get_the_ID();
		$yoast_mode = get_option( 'ogi_og_yoast', '' );
		if ( 'yes' == $yoast_mode ) {
			$img_url_fb = wp_upload_dir()['baseurl'] . '/social/' . $post_id . '-fb.jpg';
			$img_url_tw = wp_upload_dir()['baseurl'] . '/social/' . $post_id . '-tw.jpg';
			$fb         = '';
			$tw         = '';

			if ( metadata_exists( 'post', $post_id, '_ogi_fb_url' ) ) {
				if ( ! ( metadata_exists( 'post', $post_id, '_yoast_wpseo_opengraph-image' ) ) ) {
					$fb = PHP_EOL . '<meta property="og:image" content="' . $img_url_fb . '"/>';
				}
			}
			if ( metadata_exists( 'post', $post_id, '_ogi_tw_url' ) ) {
				if ( ! ( metadata_exists( 'post', $post_id, '_yoast_wpseo_twitter-image' ) ) ) {
					$tw = PHP_EOL . '<meta name="twitter:image" content="' . $img_url_tw . '"/>';
				}
			}
			printf( $fb . $tw . PHP_EOL );
		}
	}
}
