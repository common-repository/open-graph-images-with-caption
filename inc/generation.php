<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ogi_do_create( $post_id, $ogi_title ) {
	$ogi_def_fb_w = absint( get_option( 'ogi_def_fb_w', '1200' ) );
	if ( ! ( $ogi_def_fb_w > 63 and $ogi_def_fb_w < 4001 ) ) {
		$ogi_def_fb_w = '1200';
		update_option( '$ogi_def_fb_w', '1200' );
	};
	$ogi_def_fb_h = absint( get_option( 'ogi_def_fb_h', '630' ) );
	if ( ! ( $ogi_def_fb_h > 63 and $ogi_def_fb_h < 4001 ) ) {
		$ogi_def_fb_h = '630';
		update_option( 'ogi_def_fb_h', '630' );
	};
	$ogi_def_tw_w = absint( get_option( 'ogi_def_tw_w', '1024' ) );
	if ( ! ( $ogi_def_tw_w > 63 and $ogi_def_tw_w < 4001 ) ) {
		$ogi_def_tw_w = '1024';
		update_option( 'ogi_def_tw_w', '1024' );
	};
	$ogi_def_tw_h = absint( get_option( 'ogi_def_tw_h', '512' ) );
	if ( ! ( $ogi_def_tw_h > 63 and $ogi_def_tw_h < 4001 ) ) {
		$ogi_def_tw_h = '512';
		update_option( 'ogi_def_tw_h', '512' );
	};
	ogi_create_image( $post_id, $ogi_def_fb_w, $ogi_def_fb_h, 'facebook', $ogi_title );
	ogi_create_image( $post_id, $ogi_def_tw_w, $ogi_def_tw_h, 'twitter', $ogi_title );
}

function ogi_create_image( $post_id, $ogi_width, $ogi_height, $social_site, $ogi_title, $ogi_blur ) {
	$post = get_post( $post_id );

	if ( ! isset( $post->ID ) ) {
		return;
	}

	$ogi_title_length = absint( get_option( 'ogi_title_length' ) );

	$ogi_font_size = round( sqrt( $ogi_width ** 2 + $ogi_height ** 2 ) / 30 );

	$ogi_text_title = html_entity_decode( strip_tags( $ogi_title ), ENT_QUOTES, 'UTF-8' );

	if ( '' == $ogi_text_title ) {
		return;
	}

	if ( mb_strlen( $ogi_text_title ) > $ogi_title_length ) {
		$ogi_text_title  = mb_substr( $ogi_text_title, 0, $ogi_title_length );
		$ogi_text_title .= '...';
	}

	$ogi_def_overlay_transparency = absint( get_option( 'ogi_def_overlay_transparency', '90' ) );
	if ( ! ( $ogi_def_overlay_transparency > 0 and $ogi_def_overlay_transparency < 127 ) ) {
		$ogi_def_overlay_transparency = '90';
		update_option( 'ogi_def_overlay_transparency', '90' );
	};

	$thumb_id      = get_post_thumbnail_id( $post->ID );
	$image         = wp_get_attachment_image_src( $thumb_id, 'full' );
	$upload_dir    = wp_upload_dir();
	$base_dir      = $upload_dir['basedir'];
	$base_url      = $upload_dir['baseurl'];
	$imagepath     = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $image[0] );
	$default_image = false;
	if ( file_exists( $imagepath ) ) {
		if ( ( pathinfo( $imagepath, PATHINFO_EXTENSION ) == 'jpg' ) or ( pathinfo( $imagepath, PATHINFO_EXTENSION ) == 'jpeg' ) ) {
			$new_featured_img = imagecreatefromjpeg( $imagepath );
		} elseif ( pathinfo( $imagepath, PATHINFO_EXTENSION ) == 'png' ) {
			$new_featured_img = imagecreatefrompng( $imagepath );
		} else {
			return;
		}
	} else {
		$new_featured_img                                 = imagecreatetruecolor( $ogi_width, $ogi_height );
		list($back_color_r, $back_color_g, $back_color_b) = sscanf( get_option( 'ogi_def_back_color' ), '#%02x%02x%02x' );
		$back_color                                       = imagecolorallocate( $new_featured_img, $back_color_r, $back_color_g, $back_color_b );
		imagefilledrectangle( $new_featured_img, 0, 0, $ogi_width, $ogi_height, $back_color );
		$default_image = true;
	}

	$width  = imagesx( $new_featured_img );
	$height = imagesy( $new_featured_img );

	$original_aspect = $width / $height;
	$ogi_aspect      = $ogi_width / $ogi_height;

	if ( $original_aspect >= $ogi_aspect ) {
		$new_height = $ogi_height;
		$new_width  = $width / ( $height / $ogi_height );
	} else {
		$new_width  = $ogi_width;
		$new_height = $height / ( $width / $ogi_width );
	}

	$ogi_image = imagecreatetruecolor( $ogi_width, $ogi_height );

	if ( ! $default_image ) {
		imagecopyresampled(
			$ogi_image,
			$new_featured_img,
			0 - ( $new_width - $ogi_width ) / 2,
			0 - ( $new_height - $ogi_height ) / 2,
			0,
			0,
			$new_width,
			$new_height,
			$width,
			$height
		);

		if ( 'true' == $ogi_blur ) {
			for ( $i = 0; $i < 25; $i++ ) {
				if ( 0 == ( $i % 10 ) ) {
					imagefilter( $ogi_image, IMG_FILTER_SMOOTH, -7 );
				}
				imagefilter( $ogi_image, IMG_FILTER_GAUSSIAN_BLUR );
			}
		}

		$new_featured_img = $ogi_image;
	}

	$ogi_post_caption = $ogi_text_title;

	$ogi_post_caption = str_replace( '  ', ' ', $ogi_post_caption );
	$ogi_post_caption = str_replace( '&#160;', ' ', $ogi_post_caption );
	$ogi_post_caption = str_replace( ' ', ' ', $ogi_post_caption );
	$ogi_post_caption = str_replace( "\r", '', $ogi_post_caption );
	$ogi_post_caption = str_replace( '&#13;', '', $ogi_post_caption );
	$ogi_post_caption = str_replace( "\n", ' ', $ogi_post_caption );

	$words = explode( ' ', $ogi_post_caption );

	list($box_color_r, $box_color_g, $box_color_b)             = sscanf( get_option( 'ogi_def_box_color' ), '#%02x%02x%02x' );
	list($text_color_r, $text_color_g, $text_color_b)          = sscanf( get_option( 'ogi_def_text_color' ), '#%02x%02x%02x' );
	list($shadow_color_r, $shadow_color_g, $shadow_color_b)    = sscanf( get_option( 'ogi_def_shadow_color' ), '#%02x%02x%02x' );
	list($overlay_color_r, $overlay_color_g, $overlay_color_b) = sscanf( get_option( 'ogi_def_overlay_color' ), '#%02x%02x%02x' );

	if ( $default_image ) {
		$ogi_def_overlay_transparency = '127';
	}

	$box_color     = imagecolorallocate( $new_featured_img, $box_color_r, $box_color_g, $box_color_b );
	$text_color    = imagecolorallocate( $new_featured_img, $text_color_r, $text_color_g, $text_color_b );
	$shadow_color  = imagecolorallocate( $new_featured_img, $shadow_color_r, $shadow_color_g, $shadow_color_b );
	$overlay_color = imagecolorallocatealpha( $new_featured_img, $overlay_color_r, $overlay_color_g, $overlay_color_b, $ogi_def_overlay_transparency );
	$border_offset = round( $ogi_font_size / 3 );
	$shadow_offset = round( $ogi_font_size / 9 );

	imagefilledrectangle( $new_featured_img, 0, 0, $ogi_width, $ogi_height, $overlay_color );

	$new_featured_img = ogi_paint_text( $new_featured_img, $ogi_post_caption, 30, $text_color, $ogi_height / 10, $ogi_height / 10, $ogi_height / 10, $ogi_height / 10, $box_color, $shadow_color, get_option( 'ogi_def_style', '1' ) );

	$regex = array( '/[^\p{L}\p{N}\s]/u', '/\s/' );
	$repl  = array( '', '-' );
	switch ( $social_site ) {
		case 'facebook':
			$newimg     = $upload_dir['basedir'] . '/social/' . $post->ID . '-fb.jpg';
			$social_url = $upload_dir['baseurl'] . '/social/' . $post->ID . '-fb.jpg';
			break;
		case 'twitter':
			$newimg     = $upload_dir['basedir'] . '/social/' . $post->ID . '-tw.jpg';
			$social_url = $upload_dir['baseurl'] . '/social/' . $post->ID . '-tw.jpg';
			break;
	}
	if ( ! file_exists( $upload_dir['basedir'] . '/social' ) ) {
		mkdir( $upload_dir['basedir'] . '/social' );
	}
	imagejpeg( $new_featured_img, $newimg, 85 );
	switch ( $social_site ) {
		case 'facebook':
			update_post_meta( $post->ID, '_ogi_fb_url', stripslashes( $social_url ) );
			break;
		case 'twitter':
			update_post_meta( $post->ID, '_ogi_tw_url', stripslashes( $social_url ) );
			break;
	}

}

function ogi_paint_text( $canvas_img, $ogi_post_caption, $fontsize, $text_color, $ogi_top_padding, $ogi_right_padding, $ogi_bottom_padding, $ogi_left_padding, $box_color, $shadow_color, $style ) {
	$font            = plugin_dir_path( __DIR__ ) . 'fonts/OpenSans-Regular.ttf';
	$width           = imagesx( $canvas_img );
	$height          = imagesy( $canvas_img );
	$original_aspect = $width / $height;
	$ogi_aspect      = $width / $height;
	$ogi_font_size   = round( sqrt( $width ** 2 + $height ** 2 ) / $fontsize );

	$ogi_image = imagecreatetruecolor( $width, $height );

	$ogi_top_bottom_padding = $ogi_top_padding + $ogi_bottom_padding;
	$ogi_left_right_padding = $ogi_left_padding + $ogi_right_padding;

	$words = explode( ' ', $ogi_post_caption );

	$ogi_font_size = $ogi_font_size + 3;

	do {
		$ogi_font_size = $ogi_font_size - 3;

		if ( isset( $ogi_text_title_x ) ) {
			unset( $ogi_text_title_x, $ogi_text_title_xx, $ogi_text_title_y, $row );
		};

		$ogi_text_title_array = imagettfbbox( $ogi_font_size, 0, $font, $ogi_post_caption );

		$ogi_text_title_x[]  = ( $width - $ogi_text_title_array[2] ) / 2;
		$ogi_text_title_xx[] = $ogi_text_title_array[2];
		$ogi_text_title_y[]  = abs( $ogi_text_title_array[5] );

		$string       = '';
		$tmp_string   = '';
		$before_break = '';
		$after_break  = '';

		$ogi_text_title_array['height'] = abs( $ogi_text_title_array[7] ) - abs( $ogi_text_title_array[1] );
		if ( $ogi_text_title_array[3] > 0 ) {
			$ogi_text_title_array['height'] = abs( $ogi_text_title_array[7] - $ogi_text_title_array[1] ) - 1;
		};
		$lineheight = $ogi_text_title_array['height'] + 10;

		$ny = 0;
		for ( $i = 0; $i < count( $words ) || '' != $before_break; $i++ ) {
			if ( '' != $before_break ) {
				$tmp_string   = $after_break;
				$before_break = '';
			};

			if ( $i >= count( $words ) ) {
				$words[ $i ] = '';
			};
			$tmp_string .= $words[ $i ] . ' ';

			if ( mb_substr( $tmp_string, 0, 4 ) == '#10;' ) {
				$tmp_string = mb_substr( $tmp_string, 4 );
			};

			$dim = imagettfbbox( $ogi_font_size, 0, $font, rtrim( $tmp_string ) );

			$before_break = mb_strstr( $tmp_string, '#10;', true );
			$after_break  = mb_strstr( $tmp_string, '#10;' );

			if ( $dim[4] < ( $width - $ogi_left_right_padding ) ) {
				if ( '' != $before_break ) {
					$string     = rtrim( $before_break );
					$row[ $ny ] = rtrim( $before_break );

					$ogi_text_title_array = imagettfbbox( $ogi_font_size, 0, $font, rtrim( $string ) );

					$ogi_text_title_x[ $ny ]     = ( $width - $ogi_text_title_array[2] ) / 2;
					$ogi_text_title_xx[ $ny ]    = $ogi_text_title_array[2];
					$ogi_text_title_y[ $ny + 1 ] = $ogi_text_title_y[ $ny ] + $lineheight;
					$ny++;
				} else {
					$string     = rtrim( $tmp_string );
					$row[ $ny ] = rtrim( $tmp_string );
				}
			} else {
				$tmp_string   = '';
				$before_break = '';
				$after_break  = '';

				$ogi_text_title_array = imagettfbbox( $ogi_font_size, 0, $font, rtrim( $string ) );

				$ogi_text_title_x[ $ny ]  = ( $width - $ogi_text_title_array[2] ) / 2;
				$ogi_text_title_xx[ $ny ] = $ogi_text_title_array[2];

				$row[ $ny ]                  = $string;
				$string                      = '';
				$ogi_text_title_y[ $ny + 1 ] = $ogi_text_title_y[ $ny ] + $lineheight;
				$i--;
				$ny++;
			};
		};

		$ogi_text_title_array = imagettfbbox( $ogi_font_size, 0, $font, $string );

		$ogi_text_title_x[ $ny ]  = ( $width - $ogi_text_title_array[2] ) / 2;
		$ogi_text_title_xx[ $ny ] = $ogi_text_title_array[2];

		$rowsoftext     = count( $row );
		$bottom_of_text = ( $lineheight * $rowsoftext ) - 10;
		$longest_row_x  = min( $ogi_text_title_x );
		$longest_row_xx = max( $ogi_text_title_xx );
	} while ( ( $bottom_of_text > ( $height - $ogi_top_bottom_padding ) ) || ( $longest_row_xx > ( $width - $ogi_left_right_padding ) ) );

	$offset = ( $height - $ogi_top_bottom_padding - $bottom_of_text ) / 2 + $ogi_top_padding;

	$border_offset = round( $ogi_font_size / 3 );
	$shadow_offset = round( $ogi_font_size / 9 );

	switch ( $style ) {
		case '1':
			// Box with shadow
			imagefilledrectangle(
				$canvas_img,
				$ogi_text_title_x[0] - $border_offset + $shadow_offset - 15,
				$offset - $border_offset + $shadow_offset - 10,
				$width - $ogi_text_title_x[0] + $border_offset + $shadow_offset + 15,
				$offset + $bottom_of_text + $border_offset + $shadow_offset * 2 + 10,
				$shadow_color
			);
			imagefilledrectangle(
				$canvas_img,
				$ogi_text_title_x[0] - $border_offset - $shadow_offset - 15,
				$offset - $border_offset - $shadow_offset - 10,
				$width - $ogi_text_title_x[0] + $border_offset - $shadow_offset + 15,
				$offset + $bottom_of_text + $border_offset + 10,
				$box_color
			);
			break;
		case '2':
			// Box without shadow
			imagefilledrectangle(
				$canvas_img,
				$ogi_text_title_x[0] - $border_offset - 15,
				$offset - $border_offset - 10,
				$width - $ogi_text_title_x[0] + $border_offset + 15,
				$offset + $bottom_of_text + $border_offset + 10,
				$box_color
			);
			break;
		case '3':
			// Without box
			break;
		case '4':
			// Box with border
			imagefilledrectangle(
				$canvas_img,
				$ogi_text_title_x[0] - $border_offset - 20,
				$offset - $border_offset - 15,
				$width - $ogi_text_title_x[0] + $border_offset + $shadow_offset + 15,
				$offset + $bottom_of_text + $border_offset + 15,
				$shadow_color
			);
			imagefilledrectangle(
				$canvas_img,
				$ogi_text_title_x[0] - $border_offset - 15,
				$offset - $border_offset - 10,
				$width - $ogi_text_title_x[0] + $border_offset + 15,
				$offset + $bottom_of_text + $border_offset + 10,
				$box_color
			);
			break;
		case '5':
			// Fullwidth box
			imagefilledrectangle(
				$canvas_img,
				0,
				$offset - $border_offset - 10,
				$width,
				$offset + $bottom_of_text + $border_offset + 10,
				$box_color
			);
			break;
		case '6':
			// Fullwidth box with border
			imagefilledrectangle(
				$canvas_img,
				0,
				$offset - $border_offset - 15,
				$width,
				$offset + $bottom_of_text + $border_offset + 15,
				$shadow_color
			);
			imagefilledrectangle(
				$canvas_img,
				0,
				$offset - $border_offset - 10,
				$width,
				$offset + $bottom_of_text + $border_offset + 10,
				$box_color
			);
			break;
	}

	for ( $i = 0; $i < $rowsoftext; $i++ ) {
		$ogi_text_title_x[ $i ] = $ogi_text_title_x[ $i ] + $ogi_left_padding - $ogi_right_padding;
	};

		$i                = 0;
		$row              = apply_filters( 'ogi_pro_before_write_rows', $row );
		$ogi_text_title_x = apply_filters( 'ogi_pro_before_write_rows', $ogi_text_title_x );

	while ( $i < $rowsoftext ) {
		imagettftext( $canvas_img, $ogi_font_size, 0, $ogi_text_title_x[ $i ], $ogi_text_title_y[ $i ] + $offset, $text_color, $font, rtrim( $row[ $i ] ) );
		$i++;
	};

		return $canvas_img;
}
