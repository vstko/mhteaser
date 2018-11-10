<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    class WDDLogin{
        ////////////////////////////////////////////////////////////////////////////////////////
        // Events                                                                             //
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        // Constants                                                                          //
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        // Variables                                                                          //
        ////////////////////////////////////////////////////////////////////////////////////////
		private $api;
		public static $instance;
        ////////////////////////////////////////////////////////////////////////////////////////
        // Constructor & Destructor                                                           //
        ////////////////////////////////////////////////////////////////////////////////////////
        protected function __construct() {
			$this->api = new WDDApi();
			add_action( 'wp_ajax_get_user_hash',  array( $this, 'get_userhash' ) );
					if(is_multisite() === true){
						add_action( 'network_admin_menu', array( $this, 'wd_login_page' ), 24 );
					}else{
						add_action( 'admin_menu', array( $this, 'wd_login_page' ), 24 );
					}


					add_action( 'admin_enqueue_scripts', array($this, 'wdd_scripts'));

        }
        ////////////////////////////////////////////////////////////////////////////////////////
        // Public Methods                                                                     //
        ////////////////////////////////////////////////////////////////////////////////////////
        public static function get_instance() {
            if ( null == self::$instance ) {
                self::$instance = new self;
            }
            return self::$instance;
        }
		public function wd_login_page(){
			add_menu_page('Manager', 'Manager', 'manage_options', 'WDD_plugins', array( $this, 'display_login' ), WDD_URL_IMG . '/wd_logo.png',2);

		}

		public function wdd_scripts($hook){

			wdd_common_scripts_styles();
		}

		public function get_userhash(){
			check_ajax_referer( 'nonce_WDD', 'nonce_WDD' );
      $userhash = isset( $_POST["user_hash"] ) ? $_POST["user_hash"] : 'nohash';
			$userfullname = isset( $_POST["user_full_name"] ) ? $_POST["user_full_name"] : '';
			$username = isset( $_POST["user_name"] ) ? $_POST["user_name"] : '';
			if($userhash != 'nohash'){
				if(get_site_option("wdd_user_hash")){
					update_site_option( "wdd_user_hash", $userhash );
					update_site_option( "wdd_user_full_name", $userfullname );
				}
				else{
					add_site_option( "wdd_user_hash", $userhash );
					add_site_option( "wdd_user_full_name", $userfullname );
					if(get_site_option("wdd_activate") === false ){
						add_site_option( "wdd_activate", time() );
					}
				}
			}
			echo $userhash;
			die();
		}
		public function display_login(){

			require_once ( WDD_DIR_TEMPLATES . '/display_login_form.php' );
		}


        ////////////////////////////////////////////////////////////////////////////////////////
        // Getters & Setters                                                                  //
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        // Private Methods                                                                    //
        ////////////////////////////////////////////////////////////////////////////////////////

        ////////////////////////////////////////////////////////////////////////////////////////
        // Listeners                                                                          //
        ////////////////////////////////////////////////////////////////////////////////////////

    }