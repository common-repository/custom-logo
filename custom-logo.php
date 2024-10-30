<?php
/*
Plugin Name:  Custom Logo
Plugin URI: http://www.wiso.cz/
Description: Replace WordPress default login/register page logo with custom one.
Version: 2.2
Author: Martin Wiso
Author URI: http://www.wiso.cz/
*/
require_once('wpol/wpol.inc.php');

/** Front functions **/
if(!function_exists('cl_logo_css')) {	
  function cl_logo_css() {	
    // changed login dialog based on shuttle project we must replace original background
    $versioncode = '';
    if (get_bloginfo('version') >= 2.5) { 
      $versioncode = 'var obj = document.getElementById(\'login\'); if (obj) { obj.firstChild.firstChild.style.background = \'url('.get_option('wpcl_custom_logo_path').') no-repeat center\'; }';
    } else if (get_bloginfo('version') >= 2.1) { 
      $versioncode = "var obj = document.getElementById('login');
			if (obj) { 
				obj.style.background = 'url(".get_settings('home')."/wp-content/plugins/custom-logo/images/2.1/login-bkg-tile.gif) no-repeat top center';
				var bgObj2 = document.getElementById('loginform');
				bgObj2.style.background = 'url(".get_settings('home')."/wp-content/plugins/custom-logo/images/2.1/login-bkg-bottom.gif) no-repeat bottom center';
			}";
    } else {			
      $versioncode = 'var obj = document.getElementsByTagName(\'H1\')[0];
			if (obj) {
				obj.style.background = \'url('.get_option('wpcl_custom_logo_path').') no-repeat center\';
				obj.style.margin = \'0\';
			}';
    }
    		
    // replace logo
    printf('<script type="text/javascript">
    //<![CDATA[
    window.onload = function() {
	try {	
		%s		
	}
	catch (e) {
		var message = (e.description) ? e.description : e;
		alert(message);
	}
    };
    </script>', $versioncode);
  }
}

/** Admin functions **/
if(!function_exists('cl_header')) {	
  function cl_header() {
    wpol_add_settings_page("Custom Logo Options", "Custom Logo", "administrator", "custom-logo/custom-logo.php", "cl_logo_options");    
    cl_register_settings();
  }
}
if(!function_exists('cl_init')) {
  function cl_init() {
    wpol_init('custom-logo');
    wpol_enqueue_css('srlcss', wpol_option('siteurl').'wp-content/plugins/custom-logo/media/style.css');
  }
}
if(!function_exists('cl_logo_options')) {
  function cl_logo_options() {

    // basic info
    $plugin = 'custom-logo';
    $path = 'wp-content/plugins/'.$plugin;
    $imagesDir = get_option('siteurl').'/'.$path.'/images/';

    // create HTML for with logos based on WP version and description
    $wp_version = get_bloginfo('version');
    $description = '';
    $logos = array();
    if ($wp_version >= 2.5) {      
      $logos = array('wp-2.0-button-trans.gif', 'wp-2.0-button-small-trans.gif', 'dandy-logo.png');
      $description = 'Original WordPress logo size is width 290px and height 66px. Because you are using WordPress '.$wp_version.', be aware of different height (change from 2.5)!';
    } else if ($wp_version >= 2.1) {
      $logos = array('wp-2.0-button-trans.gif', 'wp-2.0-button-small-trans.gif', 'dandy-logo.png', 'wp-2.0-square-button-trans.gif');
      $description = 'Original WordPress logo size is width 250px and height 68px. Because you are using WordPress '.$wp_version.', your new logo image must have transparent background (changed login dialog background image at 2.1)!';
    } else {
      $logos = array('wp-2.0-button2.gif', 'wp-2.0-square-button-trans.gif', 'wp-2.0-button.gif');
      $description = 'Original WordPress logo size is width 250px and height 68px.';
    }
    
    // create UI
    $page  = wpol_label('Path to logo image', 'wpcl_custom_logo_path');
    $page .= wpol_text('wpcl_custom_logo_path', get_option('wpcl_custom_logo_path'), '', array('style'=>'width:560px'));
    $page .= wpol_br();
    $page .= '<table width="100%" cellpadding="5" class="form-table" border="0" align="left"><tr valign="middle">';
    foreach ($logos as $logo) {
      $page .= sprintf('<td align="center"><a href="javascript:void(0);" onclick="return setCustomLogo(\'%s\');" title="Select by clicking here"><img src="%s/%s" border="0" /></a></td>%s', $logo, $imagesDir, $logo, "\n");
    }
    $page .= '</table><p align="center">You can choose between these sample logo for main logo. Just click on logo you like and update options.</p>';

    // prepare custom JavaScript code
    $script = 'function setCustomLogo(filename) {
      try {
	var obj = document.getElementById(\'wpcl_custom_logo_path\');
	if (obj) { obj.value = \''.$imagesDir.'\' + filename; }
      }
      catch (e) { /* alert(\'Error: \' + e.description); */ }	
      return false;
    }';
	  
    // render page
    wpol_settings_page($plugin, 'Custom Logo Options', $page, $script);
  }
}
function cl_register_settings() {
  register_setting('custom-logo', 'wpcl_custom_logo_path');
}

// register actions
if(is_admin()) {
  add_action('admin_menu', 'cl_header');
  add_action('admin_init', 'cl_register_settings');
} else {
  add_action('login_form', 'cl_logo_css');
  add_action('register_form', 'cl_logo_css');
}

// end of file
?>