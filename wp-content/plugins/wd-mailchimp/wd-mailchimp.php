<?php
/**
 * Plugin Name: MailChimp WD
 * Plugin URI: https://web-dorado.com/products/wordpress-mailchimp-wd.html
 * Description: MailChimp WD is a functional plugin developed to create MailChimp subscribe/unsubscribe forms and manage lists from your WordPress site.
 * Version: 5.0.19
 * Author: WebDorado
 * Author URI: https://web-dorado.com/
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$upload_dir = wp_upload_dir();
define('MWD_DIR', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
define('MWD_URL', plugins_url(plugin_basename(dirname(__FILE__))));
define('MWD_MAIN_FILE', plugin_basename(__FILE__));
define('MWD_UPLOAD_DIR', $upload_dir['basedir'] . '/wd-mailchimp');


function mwd_options_panel() {
	add_menu_page('MailChimp WD', 'MailChimp WD', 'manage_options', 'manage_mwd', 'mailchimp_wd', MWD_URL . '/images/mailchimp_wd.png');
	
	$manage_page = add_submenu_page('manage_mwd', 'MailChimp WD', 'MailChimp WD', 'manage_options', 'manage_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $manage_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $manage_page, 'mwd_manage_scripts');

	$manage_lists = add_submenu_page('manage_mwd', 'Lists', 'Lists', 'manage_options', 'manage_lists', 'mailchimp_wd');
	add_action('admin_print_styles-' . $manage_lists, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $manage_lists, 'mwd_manage_scripts');

	$manage_forms = add_submenu_page('manage_mwd', 'Forms', 'Forms', 'manage_options', 'manage_forms', 'mailchimp_wd');
	add_action('admin_print_styles-' . $manage_forms, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $manage_forms, 'mwd_manage_scripts');

	$submissions_page = add_submenu_page('manage_mwd', 'Submissions', 'Submissions', 'manage_options', 'submissions_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $submissions_page, 'mwd_submissions_styles');
	add_action('admin_print_scripts-' . $submissions_page, 'mwd_submissions_scripts');

	$themes_page = add_submenu_page('manage_mwd', 'Themes', 'Themes', 'manage_options', 'themes_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $themes_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $themes_page, 'mwd_manage_scripts');

	$global_options_page = add_submenu_page('manage_mwd', 'Global Options', 'Global Options', 'manage_options', 'goptions_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $global_options_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $global_options_page, 'mwd_manage_scripts');

	$blocked_ips_page = add_submenu_page('manage_mwd', 'Blocked IPs', 'Blocked IPs', 'manage_options', 'blocked_ips', 'mailchimp_wd');
	add_action('admin_print_styles-' . $blocked_ips_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $blocked_ips_page, 'mwd_manage_scripts');

	$uninstall_page = add_submenu_page('manage_mwd', 'Uninstall', 'Uninstall', 'manage_options', 'uninstall_mwd', 'mailchimp_wd');
	add_action('admin_print_styles-' . $uninstall_page, 'mwd_manage_styles');
	add_action('admin_print_scripts-' . $uninstall_page, 'mwd_manage_scripts');

}
add_action('admin_menu', 'mwd_options_panel', 9);

if( !class_exists("DoradoWeb") ){
	require_once(MWD_DIR . '/wd/start.php');
}

dorado_web_init( array (
	"prefix" => "mwd",
	"wd_plugin_id" => 164,
	"plugin_title" => "MailChimp WD", 
	"plugin_wordpress_slug" => "wd-mailchimp", 
	"plugin_dir" => MWD_DIR,
	"plugin_main_file" => __FILE__,
	"description" => __('MailChimp WD is a functional plugin developed to create MailChimp subscribe/unsubscribe forms and manage lists from your WordPress site.', 'mwd-text'), 
	 // from web-dorado.com
	 "plugin_features" => array(
		0 => array(
			"title" => __("Simple Set-up", "mwd-text"),
			"description" => __("Activate the plugin and simply grab the API key from your MailChimp account. Quickly create multiple Subscribe/Unsubscribe forms, connect to corresponding MailChimp lists. Manage subscriptions in an easy-to-use admin.
", "mwd-text"),
		),
		1 => array(
			"title" => __("Customizable", "mwd-text"),
			"description" => __("Make the forms look and feel exactly as you want. The WordPress plugin allows to customize almost every aspect of the forms. Choose a theme that best fits your website, add an image, choose an image animation, add new fields and more.", "mwd-text"),
		),
		2 => array(
			"title" => __("Drag & Drop", "mwd-text"),
			"description" => __("Use the user-friendly drag and drop function to move the fields around, change the order of fields and create columns within the form.", "mwd-text"),
		),
		3 => array(
			"title" => __("Form Display Options", "mwd-text"),
			"description" => __("You can display the forms in 4 different ways - Embedded, Popup, Top bar and Scroll box. Each of the views has its customization options, including animation effect for pop-up, display pages, categories, frequency and more.", "mwd-text"),
		), 
		4 => array(
			"title" => __("Custom Messages", "mwd-text"),
			"description" => __("MailChimp WD WordPress plugin allows you to use notifications from MailChimp or set-up custom subscribe/u1nsubscribe emails for each form. Display customized messages after the user has submitted the form and the data has been successfully sent to MailChimp. You can also customize error messages, invalid email notes and other messages.", "mwd-text"),
		),   
		5 => array(
			"title" => __("Captcha", "mwd-text"),
			"description" => __("The more subscribers you have the better. But don't forget about the quality. Add captcha to your opt-in/opt-out forms to avoid spammy subscriptions. Choose Simple, Arithmetic Captchas or Recaptcha. Customize the field position, size, additional specs.", "mwd-text"),
		),    
		6 => array(
			"title" => __("Conditional Fields", "mwd-text"),
			"description" => __("Build smarter and more complex forms. Set conditions for forms to automatically show or hide fields in the form a certain condition is met.", "mwd-text"),
		), 
	
		7 => array(
			"title" => __("Themes", "mwd-text"),
			"description" => __("The Mailchimp plugin for WordPress comes with 13 pre-built themes you can choose from. If none of the themes suit your website, you can add a new theme or customize the existing themes. Change fonts, borders, margins, colors and much more. View changes you make instantly in the dashboard preview. If you wish to have it with the styles of your website theme, choose Inherit From Theme option.", "mwd-text"),
		) 		
	 ),
	 // user guide from web-dorado.com
	 "user_guide" => array(
		0 => array(
			"main_title" => __("Installation", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/installing.html",
			"titles" => array(
			)
		),
		1 => array(
			"main_title" => __("Introduction", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/introduction.html",
			"titles" => array()
		),
		2 => array(
			"main_title" => __("Configuring API", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/configuring-api.html",
			"titles" => array()
		),
		3 => array(
			"main_title" => __("Creating a form", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/creating-form.html",
			"titles" => array(
				array(
					"title" => __("Form fields", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/creating-form/form-fields.html",
				)
			)
		),
		4 => array(
			"main_title" => __("Display Options and Publishing", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/options-publishing.html",
			"titles" => array(
				array(
					"title" => __("Embedded", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/options-publishing/embedded.html",
				),
				array(
					"title" => __("Popup", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/options-publishing/popup.html",
				),
				array(
					"title" => __("Topbar", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/options-publishing/topbar.html",
				),
				array(
					"title" => __("Scrollbox", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/options-publishing/scrollbox.html",
				),
			)
		), 
		5 => array(
			"main_title" => __("Custom Fields", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/custom-fields.html",
			"titles" => array()
		), 
		6 => array(
			"main_title" => __("Form Options", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options.html",				
			"titles" => array(
				array(
					"title" => __("MailChimp Options", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options/mailchimp-options.html",
				),
				array(
					"title" => __("Email Options", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options/email-options.html",
				),
				array(
					"title" => __("Email to User", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options/email-user.html",
				),
				array(
					"title" => __("Email to Administrator", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options/email-administrator.html",
				),
				array(
					"title" => __("Custom Messages", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options/custom-messages.html",
				),
				array(
					"title" => __("Actions after submission", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options/after-submission.html",
				),
				array(
					"title" => __("Payment Options", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options/payment-options.html",
				),
				array(
					"title" => __("JavaScript", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options/javascript.html",
				),
				array(
					"title" => __("Conditional Fields", "mwd-text"),
					"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/form-options/conditional-fields.html",
				),
			)
		),
		7 => array(
			"main_title" => __("Themes", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/themes.html",				
			"titles" => array(
			)
		),
		8 => array(
			"main_title" => __("Managing Lists", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/managing-lists.html",				
			"titles" => array(
			)
		),
		9 => array(
			"main_title" => __("Submissions", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/submissions.html",				
			"titles" => array(
			)
		),	
		10 => array(
			"main_title" => __("Blocking IPs", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/blocking-ips.html",				
			"titles" => array(
			)
		), 
		11 => array(
			"main_title" => __("Publishing as a Widget", "mwd-text"),
			"url" => "https://web-dorado.com/wordpress-mailchimpwd-guide/publishing-widget.html",				
			"titles" => array(
			)
		),                     
	 ), 
	 "video_youtube_id" => null,  // e.g. https://www.youtube.com/watch?v=acaexefeP7o youtube id is the acaexefeP7o
	 "overview_welcome_image" => null,
	 "plugin_wd_url" => "https://web-dorado.com/products/wordpress-mailchimp-wd.html", 
	 "plugin_wd_demo_link" => "http://wpdemo.web-dorado.com/mailchimp/?_ga=1.173456035.1816631379.1476890738", 
	 "plugin_wd_addons_link" => "", 
	 "plugin_wizard_link" => null, 
	 "plugin_menu_title" => "MailChimp WD", 
	 "plugin_menu_icon" => MWD_URL . '/images/mailchimp_wd.png', 
	 "deactivate" => false, 
	 "subscribe" => false,
	 "custom_post" => "manage_mwd",  // if true => edit.php?post_type=contact
	 "menu_capability" => "manage_options",  
	 "menu_position" => null,  	
	 "after_subscribe" => "admin.php?page=overview_mwd", // this can be plagin overview page or set up page
));


function mailchimp_wd() {
	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) {
			die('Access Denied');
		}
	}
	else {
		die('Access Denied');
	}
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('page');

	if (($page != '') && (($page == 'manage_mwd') || ($page == 'goptions_mwd') || ($page == 'manage_lists') || ($page == 'manage_forms') || ($page == 'submissions_mwd') || ($page == 'themes_mwd') || ($page == 'uninstall_mwd') || ($page == 'Formswindow') || ($page == 'blocked_ips'))) {
		require_once (MWD_DIR . '/admin/controllers/MWDController' . ucfirst(strtolower($page)) . '.php');
		$controller_class = 'MWDController' . ucfirst(strtolower($page));
		$controller = new $controller_class();
		$controller->execute();
	}
}

function updates_mwd() {
	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) {
			die('Access Denied');
		}
	}
	else {
		die('Access Denied');
	}
	require_once(MWD_DIR . '/featured/updates.php');
}


add_action('wp_ajax_manage_mwd', 'mwd_ajax');
add_action('wp_ajax_helper', 'mwd_ajax'); //Mailchimp params
add_action('wp_ajax_ListsGenerete_csv', 'mwd_ajax');
add_action('wp_ajax_conditions', 'mwd_ajax');  //conditions

add_action('wp_ajax_get_stats', 'mailchimp_wd'); //Show statistics
add_action('wp_ajax_view_submits', 'mailchimp_wd'); //Show statistics
add_action('wp_ajax_FormsGenerete_csv', 'mwd_ajax'); // Export csv.
add_action('wp_ajax_FormsSubmits', 'mwd_ajax'); // Export csv.
add_action('wp_ajax_FormsGenerete_xml', 'mwd_ajax'); // Export xml.
add_action('wp_ajax_FormsPreview', 'mwd_ajax');
add_action('wp_ajax_Formswdcaptcha', 'mwd_ajax'); // Generete captcha image and save it code in session.
add_action('wp_ajax_nopriv_Formswdcaptcha', 'mwd_ajax'); // Generete captcha image and save it code in session for all users.
add_action('wp_ajax_Formswdmathcaptcha', 'mwd_ajax'); // Generete math captcha image and save it code in session.
add_action('wp_ajax_nopriv_Formswdmathcaptcha', 'mwd_ajax'); // Generete math captcha image and save it code in session for all users.
add_action('wp_ajax_mwdpaypal_info', 'mwd_ajax'); // Paypal info in submissions page.
add_action('wp_ajax_formeditcountry', 'mwd_ajax'); // Open country list.
add_action('wp_ajax_product_option', 'mwd_ajax'); // Open product options on add paypal field.
add_action('wp_ajax_submitter_ip', 'mailchimp_wd'); // Open ip in submissions.
add_action('wp_ajax_show_matrix', 'mwd_ajax'); // Edit matrix in submissions.

add_action('wp_ajax_mwdcheckpaypal', 'mwd_ajax'); // Notify url from Paypal Sandbox.
add_action('wp_ajax_nopriv_mwdcheckpaypal', 'mwd_ajax'); // Notify url from Paypal Sandbox for all users.
add_action('wp_ajax_select_interest_groups', 'mwd_ajax'); // select data from db.
add_action('wp_ajax_Formswindow', 'mwd_ajax');

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( 'includes/mwd_admin_class.php' );
	add_action( 'plugins_loaded', array( 'MWD_Admin', 'get_instance' ) );
}

function mwd_ajax() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('action');
	if ($page != 'Formswdcaptcha' && $page != 'Formswdmathcaptcha' && $page != 'mwdcheckpaypal') {
		if (function_exists('current_user_can')) {
			if (!current_user_can('manage_options')) {
				die('Access Denied');
			}
		}
		else {
			die('Access Denied');
		}
	}

	if ( $page != '' ) {
	    if( $page == 'mwdcheckpaypal' ) {
            $page = 'checkpaypal';
        } elseif ( $page == 'mwdpaypal_info' ) {
            $page = 'paypal_info';
        }
		require_once (MWD_DIR . '/admin/controllers/MWDController' . ucfirst($page) . '.php');
		$controller_class = 'MWDController' . ucfirst($page);
		$controller = new $controller_class();
		$controller->execute();
	}
}

function mwd_ajax_frontend() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('action');
	$task = MWD_Library::get('task');

	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) {
			die('Access Denied');
		}
	}
	else {
		die('Access Denied');
	}

	if ($page != '') {
		require_once (MWD_DIR . '/frontend/controllers/MWDController' . ucfirst($page) . '.php');
		$controller_class = 'MWDController' . ucfirst($page);
		$controller = new $controller_class();
		$controller->$task();
	}
}

function mwd_add_button($buttons) {
	array_push($buttons, "MWD_mce");
	return $buttons;
}

function mwd_register($plugin_array) {
	$url = MWD_URL . '/js/mwd_editor_button.js';
	$plugin_array["MWD_mce"] = $url;
	return $plugin_array;
}

add_filter('mce_external_plugins', 'mwd_register');
add_filter('mce_buttons', 'mwd_add_button', 0);
function mwd_admin_ajax() { ?>
	<script>
		var forms_admin_ajax = '<?php echo add_query_arg(array('action' => 'Formswindow'), admin_url('admin-ajax.php')); ?>';
		var plugin_url = '<?php echo MWD_URL; ?>';
		var content_url = '<?php echo content_url() ?>';
		var admin_url = '<?php echo admin_url('admin.php'); ?>';
		var nonce_mwd = '<?php echo wp_create_nonce('nonce_mwd') ?>';
	</script>
	<?php
}
add_action('admin_head', 'mwd_admin_ajax');

function mwd_output_buffer() {
	ob_start();
}
add_action('init', 'mwd_output_buffer');


add_shortcode('mwd-mailchimp', 'mwd_shortcode');
function mwd_shortcode($attrs) {
	ob_start();
	MWD_load_forms($attrs, 'embedded');
	return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());
}

if (!is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
	add_action('wp_footer', 'MWD_load_forms');
	add_action('wp_enqueue_scripts', 'mwd_front_end_scripts');
}

function MWD_load_forms($params = array(), $type = '') {
	$form_id = isset($params['id']) ? (int)$params['id'] : 0;
	require_once(MWD_DIR . '/includes/mwd_library.php');
	require_once (MWD_DIR . '/frontend/controllers/MWDControllerForms.php');
	$controller = new MWDControllerForms();
	$form = $controller->execute($form_id, $type);
	echo $form;
	return;
}

add_shortcode('mwd_optin_confirmation', 'mwd_optin_confirmation');
function mwd_optin_confirmation() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	require_once(MWD_DIR . '/frontend/controllers/MWDControllerCustom.php');
    $controller_class = 'MWDControllerCustom';
    $controller = new $controller_class();
    $controller->execute('optin_confirmation');
}

add_shortcode('mwd_unsubscribe', 'mwd_unsubscribe_shortcode');
function mwd_unsubscribe_shortcode() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	require_once(MWD_DIR . '/frontend/controllers/MWDControllerCustom.php');
    $controller_class = 'MWDControllerCustom';
    $controller = new $controller_class();
    $controller->execute('unsubscribe');
}

if (class_exists('WP_Widget')) {
  add_action('widgets_init', 'mwd_register_widgets');
}

/**
 * Register widgets.
 */
function mwd_register_widgets() {
  require_once(MWD_DIR . '/admin/controllers/MWDControllerWidget.php');
  register_widget("MWDControllerWidget");
}

// Register mwd_optin_conf post type
add_action('init', 'register_mwdoptinconfirmation_cpt');
function register_mwdoptinconfirmation_cpt(){
	$args = array(
		'public' => true,
		'label'  => 'MWD Opt-In confirmation'
	);

	register_post_type( 'mwd_optin_conf', $args );
	if(!get_option('mwd_optin_conf')) {
		flush_rewrite_rules();
		add_option('mwd_optin_conf', true);
	}
}

function mwd_activate() {
	$version = get_option("mwd_version");
	$new_version = '5.0.19';
	/* if ( !file_exists( MWD_UPLOAD_DIR ) ) {
		wp_mkdir_p( MWD_UPLOAD_DIR );
	} 
	
	if( !file_exists( MWD_UPLOAD_DIR. '/headers' ) ) {
		wp_mkdir_p( MWD_UPLOAD_DIR. '/headers' );
		$files = scandir(MWD_DIR. '/images/themes/headers');
		$source = MWD_DIR. '/images/themes/headers/';
		$destination = MWD_UPLOAD_DIR. '/headers/';

		foreach ($files as $file) {
			if (in_array($file, array(".",".."))) continue;
			if (copy($baseSource.$file, $destination.$file)) {
				$delete[] = $source.$file;
			}
		}

		foreach ($delete as $file) {
			unlink($file);
		}
	} */
	
	if (!$version) {
		require_once MWD_DIR . "/includes/mwd_insert.php";
		mwd_insert();
		add_option('mwd_version', $new_version);
		add_option('mwd_pro', 'yes');
		add_option('mwd_api_key', '');
		add_option('mwd_api_validation', 'invalid_api');
		add_option('mwd_settings', array('public_key' => '', 'private_key' => ''));
	}
	else{
		if (version_compare(substr($version,2), substr($new_version,2), '<') || get_option('mwd_pro') == 'no') {
			require_once MWD_DIR . "/includes/mwd_update.php";
			mwd_update($version);
		}	
		
		update_option('mwd_version', $new_version);
		update_option('mwd_pro', 'yes');
	}
}

function mwd_del_trans() {
	delete_transient('mwd_update_check');
}
register_activation_hook(__FILE__, 'mwd_activate');
register_activation_hook(__FILE__, 'mwd_del_trans');

if (!isset($_GET['action']) || $_GET['action'] != 'deactivate') {
	add_action('admin_init', 'mwd_activate');
}

function mwd_deactivate() {
	/* delete_option('mwd_api_key');
	update_option('mwd_api_validation', 'invalid_api'); */
}
register_deactivation_hook(__FILE__, 'mwd_deactivate');


/* back-end styles */
function mwd_manage_styles() {
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('page');
	wp_admin_css('thickbox');
	wp_enqueue_style('mwd-mailchimp', MWD_URL . '/css/mwd-mailchimp.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-forms', MWD_URL . '/css/mwd-forms.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-bootstrap', MWD_URL . '/css/mwd-bootstrap.css', array(), get_option("mwd_version"));
	wp_enqueue_style('jquery-ui', MWD_URL . '/css/jquery-ui-1.10.3.custom.css');
	wp_enqueue_style('jquery-ui-spinner', MWD_URL . '/css/jquery-ui-spinner.css');
	wp_enqueue_style('mwd-style', MWD_URL . '/css/style.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-colorpicker', MWD_URL . '/css/spectrum.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-font-awesome', MWD_URL . '/css/frontend/font-awesome/font-awesome.css', array(), get_option("mwd_version"));
	if($page == "uninstall_mwd") {
		wp_enqueue_style('mwd_deactivate-css',  MWD_URL . '/wd/assets/css/deactivate_popup.css', array(), get_option("mwd_version"));
	}	
}

/* back-end scripts */
function mwd_manage_scripts() {
	wp_enqueue_script('thickbox');
	global $wp_scripts;
	require_once(MWD_DIR . '/includes/mwd_library.php');
	$page = MWD_Library::get('page');	
	if (isset($wp_scripts->registered['jquery'])) {
		$jquery = $wp_scripts->registered['jquery'];
		if (!isset($jquery->ver) OR version_compare($jquery->ver, '1.8.2', '<')) {
			wp_deregister_script('jquery');
			wp_register_script('jquery', FALSE, array('jquery-core', 'jquery-migrate'), '1.10.2' );
		}
	}

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-spinner');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('jquery-effects-shake');
	wp_enqueue_script('mwd-colorpicker', MWD_URL . '/js/spectrum.js', array(), get_option("mwd_version"));

	wp_enqueue_script('mwd_mailchimp', MWD_URL . '/js/mwd_mailchimp.js', array(), get_option("mwd_version"));
	wp_enqueue_script('forms_admin', MWD_URL . '/js/forms_admin.js', array(), get_option("mwd_version"));
	wp_enqueue_script('forms_manage', MWD_URL . '/js/forms_manage.js', array(), get_option("mwd_version"));
	wp_enqueue_media();	
}

function mwd_submissions_styles() {
	wp_admin_css('thickbox');
	wp_enqueue_style('mwd-forms', MWD_URL . '/css/mwd-forms.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-mailchimp', MWD_URL . '/css/mwd-mailchimp.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-bootstrap', MWD_URL . '/css/mwd-bootstrap.css', array(), get_option("mwd_version"));
	wp_enqueue_style('jquery-ui', MWD_URL . '/css/jquery-ui-1.10.3.custom.css', array(), '1.10.3');
	wp_enqueue_style('jquery-ui-spinner', MWD_URL . '/css/jquery-ui-spinner.css', array(), '1.10.3');
	wp_enqueue_style('jquery.fancybox', MWD_URL . '/js/fancybox/jquery.fancybox.css', array(), '2.1.5');
	wp_enqueue_style('mwd-style', MWD_URL . '/css/style.css', array(), get_option("mwd_version"));
}

function mwd_submissions_scripts() {
	wp_enqueue_script('thickbox');
	global $wp_scripts;
	if (isset($wp_scripts->registered['jquery'])) {
		$jquery = $wp_scripts->registered['jquery'];
		if (!isset($jquery->ver) OR version_compare($jquery->ver, '1.8.2', '<')) {
			wp_deregister_script('jquery');
			wp_register_script('jquery', FALSE, array('jquery-core', 'jquery-migrate'), '1.10.2' );
		}
	}
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'jquery-ui-progressbar' );
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-spinner');
	wp_enqueue_script('jquery-ui-mouse');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker');

	wp_enqueue_script('forms_admin', MWD_URL . '/js/forms_admin.js', array(), get_option("mwd_version"));
	wp_enqueue_script('forms_manage', MWD_URL . '/js/forms_manage.js', array(), get_option("mwd_version"));
	wp_enqueue_script('mwd_submissions', MWD_URL . '/js/mwd_submissions.js', array(), get_option("mwd_version"));

	wp_enqueue_script('mwd_main_frontend', MWD_URL . '/js/mwd_main_frontend.js', array(), get_option("mwd_version"));
	wp_localize_script('mwd_main_frontend', 'mwd_objectL10n', array('plugin_url' => MWD_URL));
	wp_enqueue_script('jquery.fancybox.pack', MWD_URL . '/js/fancybox/jquery.fancybox.pack.js', array(), '2.1.5');

}

function mwd_front_end_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-spinner');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('jquery-effects-shake');

	wp_enqueue_style('jquery-ui', MWD_URL . '/css/jquery-ui-1.10.3.custom.css');
	wp_enqueue_style('jquery-ui-spinner', MWD_URL . '/css/jquery-ui-spinner.css');
	wp_enqueue_style('mwd-mailchimp-frontend', MWD_URL . '/css/frontend/mwd-mailchimp-frontend.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-font-awesome', MWD_URL . '/css/frontend/font-awesome/font-awesome.css', array(), get_option("mwd_version"));
	wp_enqueue_style('mwd-animate', MWD_URL . '/css/frontend/mwd-animate.css', array(), get_option("mwd_version"));
	wp_enqueue_script('file-upload-frontend', MWD_URL . '/js/file-upload-frontend.js');

	wp_enqueue_script('mwd_main_frontend', MWD_URL . '/js/mwd_main_frontend.js', array(), get_option("mwd_version"));
	wp_localize_script('mwd_main_frontend', 'mwd_objectL10n', array('plugin_url' => MWD_URL));

	require_once(MWD_DIR . '/includes/mwd_library.php');
	$google_fonts = MWD_Library::mwd_get_google_fonts();
	$fonts = implode("|", str_replace(' ', '+', $google_fonts));
	wp_enqueue_style('mwd_googlefonts', 'https://fonts.googleapis.com/css?family=' . $fonts . '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic', null, null);
}

function mwd_language_load() {
	load_plugin_textdomain('mwd-text', FALSE, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'mwd_language_load');

// Enqueue block editor assets for Gutenberg.
function mwd_register_block_editor_assets($assets) {
  $version = '2.0.1';
  $js_path = MWD_URL . '/js/tw-gb/block.js';
  $css_path = MWD_URL . '/css/tw-gb/block.css';
  if (!isset($assets['version']) || version_compare($assets['version'], $version) === -1) {
    $assets['version'] = $version;
    $assets['js_path'] = $js_path;
    $assets['css_path'] = $css_path;
  }
  return $assets;
}
add_filter('tw_get_block_editor_assets', 'mwd_register_block_editor_assets');

function mvd_enqueue_block_editor_assets() {
  $key = 'tw/wd-mailchimp';
  $plugin_name = 'WD Mailchimp';
  $icon_url = MWD_URL . '/images/tw-gb/icon.svg';
  $icon_svg = MWD_URL . '/images/tw-gb/icon_grey.svg';
/*  $url = add_query_arg(array('action' => 'Formswindow'), admin_url('admin-ajax.php'));*/
  require_once(MWD_DIR . '/includes/mwd_library.php');
  $data = MWD_Library::get_shortcode_data();
  ?>
  <script>
    if ( !window['tw_gb'] ) {
      window['tw_gb'] = {};
    }
    if ( !window['tw_gb']['<?php echo $key; ?>'] ) {
        window['tw_gb']['<?php echo $key; ?>'] = {
        title: '<?php echo $plugin_name; ?>',
        titleSelect: '<?php echo sprintf(__('Select %s', 'wde'), $plugin_name); ?>',
        iconUrl: '<?php echo $icon_url; ?>',
        iconSvg: {
          width: '20',
          height: '20',
          src: '<?php echo $icon_svg; ?>'
        },
        isPopup: false,
        containerClass: 'tw-container-wrap-800-540',
        data: '<?php echo $data ?>'
      };
    }
  </script>
  <?php
  // Remove previously registered or enqueued versions
  $wp_scripts = wp_scripts();
  foreach ( $wp_scripts->registered as $key => $value ) {
    // Check for an older versions with prefix.
    if (strpos($key, 'tw-gb-block') > 0) {
      wp_deregister_script( $key );
      wp_deregister_style( $key );
    }
  }
  // Get the last version from all 10Web plugins.
  $assets = apply_filters('tw_get_block_editor_assets', array());
  // Not performing unregister or unenqueue as in old versions all are with prefixes.
  wp_enqueue_script('tw-gb-block', $assets['js_path'], array( 'wp-blocks', 'wp-element' ), $assets['version']);
  wp_localize_script('tw-gb-block', 'tw_obj', array(
    'nothing_selected' => __('Nothing selected.', 'wde'),
    'empty_item' => __('- Select -', 'wde'),
  ));
  wp_enqueue_style('tw-gb-block', $assets['css_path'], array( 'wp-edit-blocks' ), $assets['version']);
}

add_action('enqueue_block_editor_assets', 'mvd_enqueue_block_editor_assets');
?>
