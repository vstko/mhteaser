<?php	
/**
 * Plugin Name: WD manager
 * Plugin URI: https://web-dorado.com/
 * Description:  Manage Web-Dorado plugins, themes and add-ons
 * Version: 1.4.1
 * Author: WebDorado
 * Author URI: https://web-dorado.com/
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
  if ( ! defined( 'ABSPATH' ) ) {
    exit;
  }

  define('WDD_URL', plugins_url(plugin_basename(dirname(__FILE__))));
  $wdd_debug = (isset($_GET['wdd_debug']) && $_GET['wdd_debug'] == '1') ? "&wdd_debug=1" : "";
  define('WDD_CURRENT_PGE', isset($_REQUEST["page"]) ? "admin.php?page=".$_REQUEST["page"].$wdd_debug : "");

	define( 'WDD_VERSION', '1.4.1' );

	add_action( 'plugins_loaded', 'wdd_activation' );

	function wdd_activation() {
		$wdd_version = get_site_option("wdd_version");
		$wdd_new_version = WDD_VERSION;

		if ($wdd_version && version_compare($wdd_version, $wdd_new_version, '<')) {
			update_site_option("wdd_version", $wdd_new_version);
		}
		elseif ( $wdd_version === false) {
			add_site_option("wdd_version", $wdd_new_version);

			/*after update , store settings for multisite */
			if( is_multisite() && get_option("wdd_user_hash") !== false) {

				add_site_option( 'wdd_activate',         get_option("wdd_activate") );
				add_site_option( 'wdd_user_hash',        get_option("wdd_user_hash") );

				delete_option( "wdd_activate" );
				delete_option( "wdd_first_gift" );
				delete_option( "wdd_special_offers" );
				delete_option( "wdd_all_pro_plugins" );
				delete_option( "wdd_all_pro_themes" );
				delete_option( "wdd_notices" );
				delete_option( "wdd_server_time_diff" );
				delete_option( "wdd_coupons" );
				delete_option( "wdd_offers_date" );
				delete_option( "wdd_user_full_name" );
				delete_option( "wdd_user_hash" );




			}
		}
	}




  

	
    add_action('plugins_loaded', 'wdd_init_manager_plugin');


    function wdd_init_manager_plugin(){

			if(!is_admin()){

				 return;
			}

			// load files
			require_once dirname( __FILE__ ) . '/require.php';


        wdd_init(  array (
            "prefix" => "WDD",
            "plugin_main_file" => __FILE__,
            "plugin_menu_parent_slug" => 'WDD_plugins',
            "after_activate" => 'admin.php?page=WDD_plugins'
        ));


			add_action( 'init', 'wdd_load_textdomain' );

			add_action( 'all_admin_notices', array('WDD','print_notices' ));



    }


   function wdd_init($options) {
		if( get_site_option('wdd_user_hash') && get_site_option('wdd_user_hash') != 'nohash' )	{
			$wd = WDD::get_instance($options);
			$wd->wdd_init($options);
		}
		else{
			$wd = WDDLogin::get_instance();
		}
   }
	
  //add_action( 'admin_enqueue_scripts', 'wdd_scripts'  );


/**
 * Load plugin textdomain.
 *
 */
function wdd_load_textdomain() {
	load_plugin_textdomain( "wd-manager", false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}


function wdd_common_scripts_styles(){
	wp_enqueue_style( 'wd_api_css', WDD_URL_CSS . '/api.css', array(), WDD_VERSION);
	wp_enqueue_script( 'wd-api-js', WDD_URL_JS . '/api.js', array(), WDD_VERSION);
	$user_hash = (get_site_option("wdd_user_hash") && get_site_option("wdd_user_hash") != 'nohash') ? get_site_option("wdd_user_hash") : "";
	wp_localize_script( 'wd-api-js', 'WDAPIVars', array(
		"user_hash" => $user_hash,
		"user_data_url" => WDD_USER_DATA,
		"ajax_url" => admin_url('admin-ajax.php'),
		"nonce" =>  wp_create_nonce( 'nonce_WDD' ),
		'network_admin' => is_network_admin()
	));
	wp_localize_script( 'wd-api-js', 'WDAdminVars', array(
		"is_multisite" => is_multisite(),
	));

}
        
