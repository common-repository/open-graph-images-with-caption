<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ogi_get_img_preview( $post_id ) {
	$upload_dir  = wp_upload_dir();
	$base_dir    = $upload_dir['basedir'];
	$base_url    = $upload_dir['baseurl'];
	$img_file_fb = $upload_dir['basedir'] . '/social/' . $post_id . '-fb.jpg';
	$img_url_fb  = $upload_dir['baseurl'] . '/social/' . $post_id . '-fb.jpg?v=' . time();
	$img_file_tw = $upload_dir['basedir'] . '/social/' . $post_id . '-tw.jpg';
	$img_url_tw  = $upload_dir['baseurl'] . '/social/' . $post_id . '-tw.jpg?v=' . time();
	if ( file_exists( $img_file_fb ) ) {
		$html_img_fb = '<a id="ogi_link_fb" href="' . $img_url_fb . '" target="_blank"><img id="ogi_img_fb" src="' . $img_url_fb . '"></a>';
	} else {
		$html_img_fb = '<a id="ogi_link_fb" href="#ogi_metabox_images"><img id="ogi_img_fb" src="' . plugin_dir_url( __DIR__ ) . 'images/none.png"></a>';
	}
	if ( file_exists( $img_file_tw ) ) {
		$html_img_tw = '<a id="ogi_link_tw" href="' . $img_url_tw . '" target="_blank"><img id="ogi_img_tw" src="' . $img_url_fb . '"></a>';
	} else {
		$html_img_tw = '<a id="ogi_link_tw" href="#ogi_metabox_images"><img id="ogi_img_tw" src="' . plugin_dir_url( __DIR__ ) . 'images/none.png"></a>';
	}
	printf( '<label>Facebook:</label>' . $html_img_fb . '<label>Twitter:</label>' . $html_img_tw );
}

function ogi_add_meta_box() {
	add_meta_box( 'ogi_meta_box', 'Open Graph Image', 'ogi_meta_box_func', 'post', 'side', 'low' );
	add_meta_box( 'ogi_meta_box', 'Open Graph Image', 'ogi_meta_box_func', 'page', 'side', 'low' );
}
add_action( 'add_meta_boxes', 'ogi_add_meta_box' );

function ogi_meta_box_func( $post ) {
	$meta_box_title = get_post_meta( $post->ID, '_ogi_title', 1 );
	if ( '' == $meta_box_title ) {
		$meta_box_title = get_the_title( $post->ID );
	}

	$id_blur = get_post_meta( $post->ID, '_ogi_blur', 1 );

	if ( '' == $id_blur ) {
		if ( get_option( 'ogi_def_blur' ) == 'yes' ) {
			$id_blur = true;
		} else {
			$id_blur = false;
		}
	}

	$id_og = get_post_meta( $post->ID, '_ogi_og', 1 );

	if ( '' == $id_og ) {
		if ( get_option( 'ogi_def_og' ) == 'yes' ) {
			$id_og = true;
		} else {
			$id_og = false;
		}
	} ?>
<div id="ogi_preloader"></div>
<div class="ogi_metabox_settings">
	<a id="ogi_options" href="/wp-admin/options-general.php?page=open-graph-images-with-caption.php" target="_blank">Settings</a>
	<label><input type="hidden" name="aog[_ogi_og]" value="2" />
		<input type="checkbox" name="aog[_ogi_og]" value="1" <?php checked( $id_og, true ); ?>
		/>Enable</label>
	<label>Image caption:
		<input name="aog[_ogi_title]" type="text" value="<?php printf( html_entity_decode( strip_tags( $meta_box_title ) ), ENT_QUOTES, 'UTF-8' ); ?>">
	</label>
	<label><input type="hidden" name="aog[_ogi_blur]" value="2" />
		<input type="checkbox" name="aog[_ogi_blur]" value="1" <?php checked( $id_blur, true ); ?>
		/>Blurred background</label>
	<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce( __FILE__ ); ?>" />
	<input type="hidden" name="ogi_ajax_nonce" value="<?php echo wp_create_nonce( 'ogi_ajax_action' ); ?>" />
</div>
<div class="ogi_metabox_preview">
	<a name="ogi_metabox_images"></a>
	<?php ogi_get_img_preview( $post->ID ); ?>
</div>
<div>
	<button class="button" id="ogi-box-submit">Create images</button>
	<button class="button" id="ogi-box-remove">Remove</button>
	<?php
	$thumb_id = get_post_thumbnail_id( $post->ID );
	$image    = wp_get_attachment_image_src( $thumb_id, 'full' );
	if ( ! $image ) {
		printf( '<div id="ogi_notice">Notice: To use the featured image as background, you must save or publish the post.</div>' );
	};
	?>
</div>
<style type="text/css">
	#ogi_meta_box input[type=text] {
		width: 100%;
	}

	.ogi_metabox_settings label {
		display: block;
		margin: 1em 0;
	}

	.ogi_metabox_preview label {
		display: block;
		margin: 1em 0 0 0;
	}

	#ogi_meta_box img {
		width: 100%;
		max-width: 30em;
		border: #ccc 1px solid;
	}

	#ogi_preloader {
		display: none;
		background: url('/wp-admin/images/wpspin_light.gif') no-repeat center;
		background-size: 16px 16px;
		background-color: #fff;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		opacity: .7;
		filter: alpha(opacity=70);
		z-index: 10;
	}

	#ogi-box-submit {
		margin-top: 1em;
	}

	#ogi-box-remove {
		margin-top: 1em;
		float: right;
	}

	#ogi_options {
		float: right;
	}

	#ogi_notice {
		font-size: 0.9em;
		color: #888;
		margin-top: 1em;
	}
</style>
	<?php
}

add_action( 'wp_ajax_ogi_ajax_action', 'ogi_ajax_action' );

function ogi_ajax_action() {
	if ( ! isset( $_POST['nonce'] ) ) {
		wp_die();
	}
	if ( wp_create_nonce( __FILE__ ) != $_POST['nonce'] ) {
		wp_die();
	}
	if ( ! current_user_can( 'publish_posts' ) ) {
		wp_die();
	}

	$post_id = sanitize_text_field( $_POST['postid'] );

	if ( isset( $_POST['remove'] ) ) {
		if ( '1' == $_POST['remove'] ) {
			$upload_dir = wp_upload_dir();
			if ( true == file_exists( $upload_dir['basedir'] . '/social/' . $post_id . '-fb.jpg' ) ) {
				unlink( $upload_dir['basedir'] . '/social/' . $post_id . '-fb.jpg' );
			};
			if ( true == file_exists( $upload_dir['basedir'] . '/social/' . $post_id . '-tw.jpg' ) ) {
				unlink( $upload_dir['basedir'] . '/social/' . $post_id . '-tw.jpg' );
			};
				delete_post_meta( $post_id, '_ogi_fb_url' );
				delete_post_meta( $post_id, '_ogi_tw_url' );
			if ( 'yes' == get_option( 'ogi_og_yoast' ) ) {
				delete_post_meta( $post_id, '_yoast_wpseo_opengraph-image' );
				delete_post_meta( $post_id, '_yoast_wpseo_twitter-image' );
			};
		}
	} else {
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
		ogi_create_image( $post_id, $ogi_def_fb_w, $ogi_def_fb_h, 'facebook', sanitize_text_field( $_POST['title'] ), sanitize_text_field( $_POST['blur'] ) );
		ogi_create_image( $post_id, $ogi_def_tw_w, $ogi_def_tw_h, 'twitter', sanitize_text_field( $_POST['title'] ), sanitize_text_field( $_POST['blur'] ) );
	}

	$set_blur = sanitize_text_field( $_POST['blur'] );
	if ( 'true' == $set_blur ) {
		$set_blur = '1';
	} else {
		$set_blur = '2';
	}
	$set_og = sanitize_text_field( $_POST['og'] );
	if ( 'true' == $set_og ) {
		$set_og = '1';
	} else {
		$set_og = '2';
	}

	$ogi_settings = array(
		'_ogi_title' => sanitize_text_field( $_POST['title'] ),
		'_ogi_blur'  => $set_blur,
		'_ogi_og'    => $set_og,
	);
	foreach ( $ogi_settings as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key );
			continue;
		}

		update_post_meta( $post_id, $key, $value );
	}

	wp_send_json_success();
	wp_die();
}

add_action( 'admin_print_footer_scripts', 'ogi_action_javascript', 99 );
function ogi_action_javascript() {
	$upload_dir = wp_upload_dir();
	$post_id    = get_the_ID();
	?>
<script>
	jQuery(function($) {
		$('#ogi-box-submit').on('click', function(e) {
			e.preventDefault();
			$('#ogi_preloader').css("display", "block");
			if ($('input[name="aog[_ogi_title]"]').val() == '') {
				$('input[name="aog[_ogi_title]"]').val(
					"<?php printf( get_the_title( $post_id ) ); ?>");
			}
			var data = {
				'action': 'ogi_ajax_action',
				'postid': '<?php printf( $post_id ); ?>',
				'title': $('input[name="aog[_ogi_title]"]').val(),
				'og': $('input[name="aog[_ogi_og]"]').last().prop('checked'),
				'blur': $('input[name="aog[_ogi_blur]"]').last().prop('checked'),
				'nonce': '<?php printf( wp_create_nonce( __FILE__ ) ); ?>',
			};
			$.post(ajaxurl, data, function(response) {
				if (response == '0' || response == '-1') {
					$("#ogi_img_fb").attr("src",
						"<?php printf( plugin_dir_url( __DIR__ ) . 'images/error.png' ); ?>"
					);
					$("#ogi_img_tw").attr("src",
						"<?php printf( plugin_dir_url( __DIR__ ) . 'images/error.png' ); ?>"
					);
					$("#ogi_link_fb").attr("href", "#ogi_metabox_images")
					$("#ogi_link_fb").removeAttr("target")
					$("#ogi_link_tw").attr("href", "#ogi_metabox_images")
					$("#ogi_link_tw").removeAttr("target")
					$('#ogi_preloader').css("display", "none");
				} else {
					var fb =
						"<?php printf( $upload_dir['baseurl'] . '/social/' . $post_id . '-fb.jpg?v=' ); ?>" +
						$.now();
					var tw =
						"<?php printf( $upload_dir['baseurl'] . '/social/' . $post_id . '-tw.jpg?v=' ); ?>" +
						$.now();
					$("#ogi_img_fb").attr("src", fb);
					$("#ogi_img_tw").attr("src", tw);
					$("#ogi_link_fb").attr("href", fb)
					$("#ogi_link_fb").attr("target", "_blank")
					$("#ogi_link_tw").attr("href", tw)
					$("#ogi_link_tw").attr("target", "_blank")
					$('#ogi_preloader').css("display", "none");
				}
			});
		});

	});

	jQuery(function($) {
		$('#ogi-box-remove').on('click', function(e) {
			e.preventDefault();
			$('#ogi_preloader').css("display", "block");
			var data = {
				'action': 'ogi_ajax_action',
				'postid': '<?php printf( $post_id ); ?>',
				'title': $('input[name="aog[_ogi_title]"]').val(),
				'og': $('input[name="aog[_ogi_og]"]').last().prop('checked'),
				'blur': $('input[name="aog[_ogi_blur]"]').last().prop('checked'),
				'nonce': '<?php printf( wp_create_nonce( __FILE__ ) ); ?>',
				'remove' : '1',
			};
			$.post(ajaxurl, data, function(response) {
				if (response == '0' || response == '-1') {
					$("#ogi_img_fb").attr("src",
						"<?php printf( plugin_dir_url( __DIR__ ) . 'images/error.png' ); ?>"
					);
					$("#ogi_img_tw").attr("src",
						"<?php printf( plugin_dir_url( __DIR__ ) . 'images/error.png' ); ?>"
					);
					$("#ogi_link_fb").attr("href", "#ogi_metabox_images")
					$("#ogi_link_fb").removeAttr("target")
					$("#ogi_link_tw").attr("href", "#ogi_metabox_images")
					$("#ogi_link_tw").removeAttr("target")
					$('#ogi_preloader').css("display", "none");
				} else {
					$("#ogi_img_fb").attr("src",
						"<?php printf( plugin_dir_url( __DIR__ ) . 'images/none.png' ); ?>"
					);
					$("#ogi_img_tw").attr("src",
						"<?php printf( plugin_dir_url( __DIR__ ) . 'images/none.png' ); ?>"
					);
					$("#ogi_link_fb").attr("href", "#ogi_metabox_images")
					$("#ogi_link_fb").removeAttr("target")
					$("#ogi_link_tw").attr("href", "#ogi_metabox_images")
					$("#ogi_link_tw").removeAttr("target")
					$('#ogi_preloader').css("display", "none");
				}
			});
		});

	});
</script>
	<?php
}

add_action( 'save_post', 'ogi_meta_box_save' );

function ogi_meta_box_save( $post_id ) {
	remove_action( 'save_post', 'ogi_meta_box_save' );
	if (
		empty( $_POST['ogi'] )
		|| ! wp_verify_nonce( $_POST['extra_fields_nonce'], __FILE__ )
		|| wp_is_post_autosave( $post_id )
		|| wp_is_post_revision( $post_id )
		|| ! current_user_can( 'edit_post', $post_id )
	) {
		return false;
		wp_die();
	}

	$_POST['ogi'] = array_map( 'sanitize_text_field', $_POST['ogi'] );
	foreach ( $_POST['ogi'] as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $key );
			continue;
		}

		update_post_meta( $post_id, $key, $value );
	}
	return $post_id;
	wp_die();
}
