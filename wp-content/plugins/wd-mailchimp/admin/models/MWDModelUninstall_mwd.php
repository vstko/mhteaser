<?php

class MWDModelUninstall_mwd {
	////////////////////////////////////////////////////////////////////////////////////////
	// Events                                                                             //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constants                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Variables                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constructor & Destructor                                                           //
	////////////////////////////////////////////////////////////////////////////////////////
	public function __construct() {
	}
	////////////////////////////////////////////////////////////////////////////////////////
	// Public Methods                                                                     //
	////////////////////////////////////////////////////////////////////////////////////////
	public function delete_db_tables() {
		global $wpdb;
		delete_option("mwd_version");
		delete_option("mwd_api_key");
		delete_option("mwd_api_validation");

		delete_option("mwd_unsubscribe");
		delete_option("mwd_form_params");
		delete_option("mwd_optin_conf");
		delete_option("mwd_pro");
		delete_option("mwd_subscribe_done");

		for($i=200; $i>0; $i-- ){
			if(get_option('mwd_confirmation-'.$i)) {
				delete_option('mwd_confirmation-'.$i);
			}
		}

		global $wp_post_types;
		if (isset($wp_post_types['mwd_optin_conf'])) {
			unset($wp_post_types['mwd_optin_conf']);
		}

		$wpdb->query("DELETE a,b,c FROM " . $wpdb->prefix . "posts a LEFT JOIN " . $wpdb->prefix . "term_relationships b ON (a.ID=b.object_id) LEFT JOIN " . $wpdb->prefix . "postmeta c ON (a.ID=c.post_id) WHERE a.post_type='mwd_optin_conf'");

		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "mwd_forms");
		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "mwd_forms_submits");
		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "mwd_forms_views");
		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "mwd_themes");
		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "mwd_forms_sessions");
		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "mwd_display_options");
		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "mwd_forms_blocked");
		$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "mwd_forms_backup");
		return true;
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
