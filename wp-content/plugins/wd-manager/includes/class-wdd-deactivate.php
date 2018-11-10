<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    class WDDeactivate{
        ////////////////////////////////////////////////////////////////////////////////////////
        // Events                                                                             //
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        // Constants                                                                          //
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        // Variables                                                                          //
        ////////////////////////////////////////////////////////////////////////////////////////
		public $deactivate_reasons = array();
		// Reason IDs
		const REASON_TEMPORARY_DEACTIVATION = 1;
		const REASON_USING_ANOTHER_PLUGIN = 2;
		const REASON_PLUGIN_IS_DIFFICULT_TO_USE = 3;
		const REASON_NO_LONGER_NEED_THE_PLUGIN = 4;
		const REASON_TECHNICAL_PROBLEMS_POOR_QUALITY = 5;
		const REASON_BAD_NO_SUPPORT_BY_DEVELOPER = 6;
		const REASON_OTHER = 7;
		const REASON_DONT_LIKE_TO_SHARE_MY_INFORMATION = 8;
		
		

		
        ////////////////////////////////////////////////////////////////////////////////////////
        // Constructor & Destructor                                                           //
        ////////////////////////////////////////////////////////////////////////////////////////
        public function __construct() {
			global $wdd_options;

			$this->deactivate_reasons = array(
				"reason-temporary-deactivation" => array(
					'id'    => self::REASON_TEMPORARY_DEACTIVATION,
					'text'  => __( 'Temporary deactivation', $wdd_options->prefix ),	
				),
				"reason-using-another-plugin" => array(
					'id'    => self::REASON_USING_ANOTHER_PLUGIN,
					'text'  => __( 'Using another plugin', $wdd_options->prefix ),	
				),
				"reason-plugin-is-difficult-to-use" => array(
					'id'    => self::REASON_PLUGIN_IS_DIFFICULT_TO_USE,
					'text'  => __( 'Plugin is difficult to use', $wdd_options->prefix ),	
				),					
				"reason-no-longer-need-the-plugin" => array(
					'id'    => self::REASON_NO_LONGER_NEED_THE_PLUGIN,
					'text'  => __( 'No longer need the plugin', $wdd_options->prefix ),	
				),
				"reason-technical-problems-poor-quality" => array(
					'id'    => self::REASON_TECHNICAL_PROBLEMS_POOR_QUALITY,
					'text'  => __( 'Technical problems/Poor quality', $wdd_options->prefix ),	
				),
				"reason-bad-no-support-by-developer" => array(
					'id'    => self::REASON_BAD_NO_SUPPORT_BY_DEVELOPER,
					'text'  => __( 'Bad/No Support by developer', $wdd_options->prefix ),	
				),				

				"reason-other" => array(
					'id'    => self::REASON_OTHER,
					'text'  => __( 'Other', $wdd_options->prefix ),	
				),
				"reason-dont-like-to-share-my-information" => array(
					'id'    => self::REASON_DONT_LIKE_TO_SHARE_MY_INFORMATION,
					'text'  => __( 'Don\'t like to share my information', $wdd_options->prefix ),	
				),				
			);
			
			add_action( 'admin_init', array( $this, 'add_deactivation_feedback_dialog_box' ) );	
			add_action( 'admin_init', array( $this, 'submit_and_deactivate' ) );	
			
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );			

        }
        ////////////////////////////////////////////////////////////////////////////////////////
        // Public Methods                                                                     //
        ////////////////////////////////////////////////////////////////////////////////////////
        public function add_deactivation_feedback_dialog_box(){
			$deactivate_reasons = $this->deactivate_reasons;
			global $wdd_options;
			require_once( WDD_DIR_TEMPLATES . '/display_deactivation_popup.php' );
		}

		public function scripts(){
			global $wdd_options;
			wp_enqueue_style( $wdd_options->prefix . '-deactivate-popup', WDD_URL_CSS . '/deactivate_popup.css', array(), WDD_VERSION );
			wp_enqueue_script( $wdd_options->prefix . '-deactivate-popup', WDD_URL_JS . '/deactivate_popup.js', array(), WDD_VERSION);

		    wp_localize_script( $wdd_options->prefix . '-deactivate-popup', 'WDDeactivateVars', array(
				"prefix" => $wdd_options->prefix ,
				"deactivate_class" => $wdd_options->prefix . '_deactivate_link',
				"site_url" => site_url(),
			));
			 
		}
		public function submit_and_deactivate(){
			global $wdd_options;
			if( isset( $_GET[$wdd_options->prefix . "_submit_and_deactivate"] ) &&  $_GET[$wdd_options->prefix . "_submit_and_deactivate"] == 1 ){
				$deactivate_url = 
					add_query_arg(
						array(
							'action' => 'deactivate',
							'plugin' => $wdd_options->plugin_wordpress_slug . '/' . $wdd_options->plugin_wordpress_slug . '.php',		
							'_wpnonce' => wp_create_nonce( 'deactivate-plugin_' . $wdd_options->plugin_wordpress_slug. '/' . $wdd_options->plugin_wordpress_slug . '.php')
						),
						network_admin_url( 'plugins.php' )
					);  
			   wp_redirect( $deactivate_url ); 

			}
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
	
	
