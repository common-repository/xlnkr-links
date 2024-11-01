<?php
/*
Plugin Name: Xlnkr Links
Plugin URI: http://xlnkr.com/wordpress
Description: Fetches links that your site is to link to in the network and embeds them in a page of your choice.  Provides settings so that you can update the published page url at Xlnkr directly from the plugin.
Version: 1.1
Author: George Pearce
Author URI: http://iampearce.com/

PLEASE NOTE IT IS AGAINST THE LAW TO REMOVE THE CORRECT AUTHOR CREDIT FROM THIS SECTION.
*/

//right then, lets get going. Options first. 
add_action('admin_menu', 'xlnkr_admin_add_page');
function xlnkr_admin_add_page() {
add_options_page('Xlnkr', 'Xlnkr', 'manage_options', 'xlnkr', 'xlnkr_options_page');
}

function xlnkr_options_page() {
	?>
	<div>
		<h2>Xlnkr Setup</h2>
		Provide your link API Key and the URL where you will publish links on your site below and you are all set.  Your
		link api key is available from your <a href="https://xlnkr.com/account/dashboard/" target="_NEW">xlnkr account dashboard</a>.
			
		<form action="options.php" method="post">
		    <?php settings_fields('xlnkr_options'); do_settings_sections('xlnkr'); ?>
		    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes');?>" />
		</form>

		<h2>Additional Help</h2>
		<h4>Using the Short Code</h4>
		<p>Simply place <code>[xlnkr_links]</code> into your Display URL page, the page that matches the URL that you
		provide above and the plugin will do the rest.  The links that you need to publish will be fetched and
		embedded into that page automatically.  Don't include any other content in the body of that page.</p>

	    <h4>Caching</h4>
	    <p>We encourage you to enable WP Super Cache or a similar plugin for caching, and ensure that your links page
	    is appropriately cached.  Links do change, please keep your page cache time to under a week so that your
	    links will always be accurately published.</p>

		<p>For full information please visit <a href="https://xlnkr.com/wordpress">https://xlnkr.com/wordpress</a>.</p>

		<h4>Link API Key</h4>
		<p>You get this link when you signup for the Wheel Link service at Xlnkr.  You may retrieve it by visiting
		<a href="https://xlnkr.com/dashboard/" target="_NEW">your dashboard</a> and copying it from the Wheel Link service
		you wish to use on this site.  You may have more than one if you have signed up for multiple Wheel Link services.
		We use this API key to identify your site and determine which sites your site will link out to.</p>

		<h4>Display URL</h4>
		<p>Please keep this setting up to date.  We will try to crawl your site and find the published links, and we
		start with this page.  If we are unable to find and verify your links then we will notify you, but ultimately,
		we may pull your link from the network.  The best way to avoid any confusion is to make sure this URL is accurate,
		and that this URL is listed in your Wordpress sitemap file.</p>
	</div>
	<?php
}

add_action('admin_init', 'xlnkr_admin_init');
function xlnkr_section_text() {
}
function xlnkr_under_text() {
	echo '';
}

function xlnkr_admin_init() {
	register_setting( 'xlnkr_options', 'xlnkr_options', 'xlnkr_options_validate');
	add_settings_section('xlnkr_main', 'API and Address', 'xlnkr_section_text', 'xlnkr'); 
	add_settings_section('xlnkr_under', '', 'xlnkr_under_text', 'xlnkr'); 
	add_settings_field('xlnkr_api', 'API Key', 'xlnkr_api_setting_string', 'xlnkr', 'xlnkr_main');
	add_settings_field('xlnkr_url', 'Display URL', 'xlnkr_url_setting_string', 'xlnkr', 'xlnkr_main');
}

function xlnkr_api_setting_string() {
	$options = get_option('xlnkr_options');
	echo "<input id='xlnkr_api' name='xlnkr_options[api]' size='64' type='text' value='{$options['api']}' />";

}
function xlnkr_url_setting_string() {
	$options = get_option('xlnkr_options');
	echo "<input id='xlnkr_url' name='xlnkr_options[url]' size='64' type='text' value='{$options['url']}' />";

}
function xlnkr_options_validate($input) {
	return $input;
}

// okay so now we've done the settings, we're going to use file_get_contents to get hte result of the API call.

function xlnkr_shortcode_return() {
	$options = get_option('xlnkr_options');
	$api = $options['api'];
	$url = $options['url'];
	$setuplink = "https://xlnkr.com/x/links/{$api}/fetch/?p={$url}";
	$contents = file_get_contents($setuplink);
	return $contents;
}

add_shortcode('xlnkr_links', 'xlnkr_shortcode_return')
?>