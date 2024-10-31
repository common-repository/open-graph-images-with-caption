<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_option( 'ogi_og_print_tags', 'yes' );
add_option( 'ogi_og_yoast', '' );
add_option( 'ogi_og_tw_site', '' );
add_option( 'ogi_og_tw_creator', '' );
add_option( 'ogi_title_length', 70 );
add_option( 'ogi_def_box_color', '#ffffff' );
add_option( 'ogi_def_text_color', '#0093C2' );
add_option( 'ogi_def_shadow_color', '#aaaaaa' );
add_option( 'ogi_def_back_color', '#0093C2' );
add_option( 'ogi_def_overlay_color', '#ffffff' );
add_option( 'ogi_def_overlay_transparency', '90' );
add_option( 'ogi_def_blur', 'yes' );
add_option( 'ogi_def_og', 'yes' );
add_option( 'ogi_def_fb_w', '1200' );
add_option( 'ogi_def_fb_h', '630' );
add_option( 'ogi_def_tw_w', '1024' );
add_option( 'ogi_def_tw_h', '512' );
add_option( 'ogi_def_style', '1' );
add_option( 'ogi_plugin_version', '1.0.2' );

function ogi_register_image() {
	register_setting( 'ogi_group', 'ogi_og_print_tags' );
	register_setting( 'ogi_group', 'ogi_og_yoast' );
	register_setting( 'ogi_group', 'ogi_og_tw_site' );
	register_setting( 'ogi_group', 'ogi_og_tw_creator' );
	register_setting( 'ogi_group', 'ogi_title_length' );
	register_setting( 'ogi_group', 'ogi_def_box_color' );
	register_setting( 'ogi_group', 'ogi_def_text_color' );
	register_setting( 'ogi_group', 'ogi_def_shadow_color' );
	register_setting( 'ogi_group', 'ogi_def_back_color' );
	register_setting( 'ogi_group', 'ogi_def_overlay_color' );
	register_setting( 'ogi_group', 'ogi_def_overlay_transparency' );
	register_setting( 'ogi_group', 'ogi_def_blur' );
	register_setting( 'ogi_group', 'ogi_def_og' );
	register_setting( 'ogi_group', 'ogi_def_fb_w' );
	register_setting( 'ogi_group', 'ogi_def_fb_h' );
	register_setting( 'ogi_group', 'ogi_def_tw_w' );
	register_setting( 'ogi_group', 'ogi_def_tw_h' );
	register_setting( 'ogi_group', 'ogi_def_style' );
	register_setting( 'ogi_group', 'ogi_plugin_version' );
}

function ogi_settings() {
	add_options_page( 'Open Graph Images With Caption', 'OG Images With Caption', 'manage_options', 'open-graph-images-with-caption.php', 'ogi_settings_page' );
	add_filter( 'plugin_action_links', 'ogi_settings_link', 10, 2 );
	add_action( 'admin_init', 'ogi_register_image' );
}

function ogi_settings_link( $links, $file ) {
	static $this_plugin;
	if ( ! $this_plugin ) {
		$this_plugin = plugin_basename( __FILE__ );
	}
	if ( $file == $this_plugin ) {
		$ogi_settings_link = '<a href="options-general.php?page=open-graph-images-with-caption.php">' . __( 'Settings', 'open-graph-images-with-caption' ) . '</a>';
		array_unshift( $links, $ogi_settings_link );
	}
	return $links;
}

add_action( 'admin_menu', 'ogi_settings' );

function ogi_css_head() {
	if ( 'open-graph-images-with-caption.php' == ( isset( $_GET['page'] ) ) && ( $_GET['page'] ) ) {
		?>
<style type="text/css">
#ogi_settings {
	display: inline-block;
}
 #ogi_settings li {
	list-style-type: circle;
	margin-left: 2em;
}
 #ogi_settings input[type=radio], #ogi_settings input[type=checkbox] {
	margin-top: 2px;
}
 .group_1 input[type=text] {
	width: 15em;
}
 .group_1 input[type=number] {
	width: 5em;
}
 #ogi_settings label {
	vertical-align: baseline;
}
 #ogi_settings .wp-picker-container {
	margin: 1em 0;
}
 .ogi_group {
	background-color: #fff;
	border: #ccc 1px solid;
	padding: 0 1em;
	margin-bottom: 1em;
}
 #ogi_info {
	float: right;
	margin-right: -280px;
	width: 200px;
}
 #ogi_info ul {
	list-style-type: disc;
	margin-left: 30px;
}
 #ogi h2, #ogi h3 {
	clear: both;
}
 #ogi input[type=submit] {
	clear: both;
	display: block;
	margin-bottom: 30px;
}
 .clear {
	clear: both;
}
 .left {
	float: left;
}
 .spoiler > input + label:after{
	content: "+";
	float: right;
	font-family: monospace;
	font-weight: bold;
}
 .spoiler > input:checked + label:after{
	content: "-";
	float: right;
	font-family: monospace;
	font-weight: bold;
}
 .spoiler > input{
	display:none;
}
 .spoiler > input + label , .spoiler > .spoiler_body{
	color: #fff;
	background:#0093c2;
	padding:5px 15px;
	overflow:hidden;
	width:100%;
	box-sizing: border-box;
	display: block;
}
 .spoiler > input + label + .spoiler_body{
	display:none;
}
 .spoiler > input:checked + label + .spoiler_body{
	display: block;
}
 .spoiler > .spoiler_body{
	background: #fff;
	border: 1px solid #0093c2;
	border-top: none;
}
</style>
		<?php
	}
}

add_action( 'admin_head', 'ogi_css_head' );

add_action( 'admin_enqueue_scripts', 'ogi_enqueue_color_picker' );
function ogi_enqueue_color_picker( $hook_suffix ) {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'my-script-handle', plugins_url( 'admin-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

function ogi_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die();
	};

	$ogi_def_overlay_transparency = absint( get_option( 'ogi_def_overlay_transparency', '90' ) );
	if ( ! ( $ogi_def_overlay_transparency > 0 and $ogi_def_overlay_transparency < 127 ) ) {
		$ogi_def_overlay_transparency = '90';
		update_option( 'ogi_def_overlay_transparency', '90' );
	};
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

	?>

<div id="ogi">

	<h2>Open Graph Images With Caption Settings</h2>

	<div id="ogi_settings">

		<form method="post" action="options.php">
			<?php settings_fields( 'ogi_group' ); ?>

			<div class="ogi_group group_1">

			<h3>Open Graph Options:</h3>

			<p><input type="checkbox" name="ogi_og_print_tags" class="left" value="yes"
			<?php
			if ( get_option( 'ogi_og_print_tags' ) == 'yes' ) {
				echo ' checked="checked"';
			}
			?>
				/><label for="ogi_og_print_tags">Generate all opengraph tags, not only "og:image" and "twitter:image" <strong>(uncheck this if Yoast SEO or similar plugin
					was installed!)</strong>.</label></p>

			<p><input type="checkbox" name="ogi_og_yoast" class="left" value="yes"
			<?php
			if ( get_option( 'ogi_og_yoast' ) == 'yes' ) {
				echo ' checked="checked"';
			}
			?>
				/><label for="ogi_og_yoast">Enable Yoast SEO compatibility mode (overrides default OG images from Yoast SEO).</label></p>

			<div id="ogi_og_tw_site">
				<p><label for="ogi_og_tw_site">Name in tag "twitter:site" (without "@"):</label>
					<input name="ogi_og_tw_site" type="text" size="10" id="ogi_og_tw_site" value="<?php form_option( 'ogi_og_tw_site' ); ?>" /></p>
			</div>

			<div id="ogi_og_tw_creator">
				<p><label for="ogi_og_tw_creator">Name in tag "twitter:creator" (without "@"):</label>
					<input name="ogi_og_tw_creator" type="text" size="10" id="ogi_og_tw_creator" value="<?php form_option( 'ogi_og_tw_creator' ); ?>" /></p>
			</div>

			<div id="ogi_def_fb">
				<p><label for="ogi_def_fb">Open Graph Image Resolution (width x height pixels):</label>
					<input name="ogi_def_fb_w" type="number" min="64" max="4000" size="4" id="ogi_def_fb_w" value="<?php echo $ogi_def_fb_w; ?>" /> x <input name="ogi_def_fb_h" type="number" min="64" max="4000" size="4" id="ogi_def_fb_h" value="<?php echo $ogi_def_fb_h; ?>" /></p>
			</div>

			<div id="ogi_def_tw">
				<p><label for="ogi_def_tw">Twitter Cards Image Resolution (width x height pixels):</label>
					<input name="ogi_def_tw_w" type="number" min="64" max="4000" size="4" id="ogi_def_tw_w" value="<?php echo $ogi_def_tw_w; ?>" /> x <input name="ogi_def_tw_h" type="number" min="64" max="4000" size="4" id="ogi_def_tw_h" value="<?php echo $ogi_def_tw_h; ?>" /></p>
			</div>

			</div>

			<div class="ogi_group">

			<h3>Default Settings in Posts:</h3>

			<p><input type="checkbox" name="ogi_def_og" class="left" value="yes"
			<?php
			if ( get_option( 'ogi_def_og' ) == 'yes' ) {
				echo ' checked="checked"';
			}
			?>
				/><label for="ogi_def_og">Generate Open Graph tags.</label></p>

				<p><input type="checkbox" name="ogi_def_blur" class="left" value="yes"
			<?php
			if ( get_option( 'ogi_def_blur' ) == 'yes' ) {
				echo ' checked="checked"';
			}
			?>
				/><label for="ogi_def_blur">Enable blur effect for background image.</label></p>

			</div>
			<div class="ogi_group">

			<h3>Open Graph Images Styling:</h3>

				<div class="spoiler">
					<input type="checkbox" id="spoilerid_1"><label for="spoilerid_1">
					Styles preview
					</label>
					<div class="spoiler_body">
						<img src="<?php printf( plugin_dir_url( __DIR__ ) . 'images/styles.png' ); ?>">
					</div>
				</div>

				<p><label for="ogi_def_style">Style:</label>
				<select name="ogi_def_style" id="ogi_def_style">
					<option value='1'
					<?php
					if ( '1' == ( get_option( 'ogi_def_style' ) ) ) {
						echo ' selected';}
					?>
					>1</option>
					<option value='2'
					<?php
					if ( '2' == ( get_option( 'ogi_def_style' ) ) ) {
						echo ' selected';}
					?>
					>2</option>
					<option value='3'
					<?php
					if ( '3' == ( get_option( 'ogi_def_style' ) ) ) {
						echo ' selected';}
					?>
					>3</option>
					<option value='4'
					<?php
					if ( '4' == ( get_option( 'ogi_def_style' ) ) ) {
						echo ' selected';}
					?>
					>4</option>
					<option value='5'
					<?php
					if ( '5' == ( get_option( 'ogi_def_style' ) ) ) {
						echo ' selected';}
					?>
					>5</option>
					<option value='6'
					<?php
					if ( '6' == ( get_option( 'ogi_def_style' ) ) ) {
						echo ' selected';}
					?>
					>6</option>
				</select></p>

				<p><label for="ogi_title_length">Caption text length limit (symbols):</label>
					<input name="ogi_title_length" type="number" min="1" max="200" size="3" id="ogi_title_length" value="<?php form_option( 'ogi_title_length' ); ?>" /></p>

				<p><label for="ogi_def_text_color">Text color:</label>
					<input name="ogi_def_text_color" type="text" size="10" id="ogi_def_text_color" class="my-color-field" data-default-color="#0093C2" value="<?php form_option( 'ogi_def_text_color' ); ?>" /></p>

				<p><label for="ogi_def_box_color">Box background color:</label>
					<input name="ogi_def_box_color" type="text" size="10" id="ogi_def_box_color" class="my-color-field" data-default-color="#ffffff" value="<?php form_option( 'ogi_def_box_color' ); ?>" /></p>

				<p><label for="ogi_def_shadow_color">Box shadow (border) color:</label>
					<input name="ogi_def_shadow_color" type="text" size="10" id="ogi_def_shadow_color" class="my-color-field" data-default-color="#aaaaaa" value="<?php form_option( 'ogi_def_shadow_color' ); ?>" /></p>

				<p><label for="ogi_def_overlay_color">Overlay color:</label>
					<input name="ogi_def_overlay_color" type="text" size="10" id="ogi_def_overlay_color" class="my-color-field" data-default-color="#ffffff" value="<?php form_option( 'ogi_def_overlay_color' ); ?>" /></p>

				<p><label for="ogi_def_overlay_transparency">Overlay transparency (0-127):</label>
					<input name="ogi_def_overlay_transparency" type="number" min="0" max="127" size="3" id="ogi_def_overlay_transparency"
						value="<?php echo $ogi_def_overlay_transparency; ?>" /></p>

				<p><label for="ogi_def_back_color">Background color (if featured image not set):</label>
					<input name="ogi_def_back_color" type="text" size="10" id="ogi_def_back_color" class="my-color-field" data-default-color="#0093C2" value="<?php form_option( 'ogi_def_back_color' ); ?>" /></p>

			</div>

			<p><input type="submit" class="button" value="Save Settings" /></p>

		</form>

		<div class="ogi_group">

			<h3>About:</h3>
			<p><strong>Author:</strong> Dmitriy Glashkov</p>
			<p><strong>Plugin homepage:</strong> <a href="https://glashkoff.com/open-graph-images-with-caption">glashkoff.com/open-graph-images-with-caption</a></p>
			<p><strong>WordPress Plugin Directory:</strong> <a href="https://wordpress.org/plugins/open-graph-images-with-caption/">wordpress.org/plugins/open-graph-images-with-caption/</a></p>
			<p><strong>Contacts:</strong>
				<ul>
				<li>Email: <a href="mailto:dmitri@glashkoff.com">dmitri@glashkoff.com</a></li>
				<li>Telegram: <a href="https://telegram.me/glashkov">telegram.me/glashkov</a></li>
				<li>Facebook: <a href="https://www.facebook.com/glashkov">facebook.com/glashkov</a></li>
				<li>Twitter: <a href="https://twitter.com/glashkoff">twitter.com/glashkoff</a></li>
				<li>Vkontake: <a href="https://vk.com/glashkov">vk.com/glashkov</a></li>
				</ul>
			</p>
			<p><strong>Donate:</strong>
				<ul>
				<li>Paypal: <a href="https://www.paypal.me/glashkov/">www.paypal.me/glashkov/</a></li>
				<li>Yandex Money: <a href="https://money.yandex.ru/to/41001662331895/100">money.yandex.ru/to/41001662331895/100</a></li>
				</ul>
			</p>
		</div>

	</div>


	<?php
}

function check_for_update() {
	// Need for compatibility with older versions
	$ogi_fnaming = get_option( 'ogi_def_file_naming', '0' );
	if ( '0' == $ogi_fnaming ) {

	}
}
