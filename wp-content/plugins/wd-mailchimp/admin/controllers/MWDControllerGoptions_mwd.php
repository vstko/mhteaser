<?php

class MWDControllerGoptions_mwd {
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
	public function execute() {
		$task = MWD_Library::get('task');
		$id = (int)MWD_Library::get('current_id', 0);
		$message = MWD_Library::get('message');
		echo MWD_Library::message_id($message);
		if (method_exists($this, $task)) {
			check_admin_referer('nonce_mwd', 'nonce_mwd');
			$this->$task($id);
		}
		else {
			$this->display();
		}
	}

	public function display() {
		require_once MWD_DIR . "/admin/models/MWDModelGoptions_mwd.php";
		$model = new MWDModelGoptions_mwd();

		require_once MWD_DIR . "/admin/views/MWDViewGoptions_mwd.php";
		$view = new MWDViewGoptions_mwd($model);
		$view->display();
	}

	public function save() {
		$message = $this->save_db();
		$page = MWD_Library::get('page');
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
	}
  
	public function save_db() {
		global $wpdb;
		$public_key = (isset($_POST['public_key']) ? esc_html(stripslashes( $_POST['public_key'])) : '');
		$private_key = (isset($_POST['private_key']) ?  esc_html(stripslashes( $_POST['private_key'])) : '');
		$csv_delimiter = (isset($_POST['csv_delimiter']) && $_POST['csv_delimiter']!='' ? esc_html(stripslashes( $_POST['csv_delimiter'])) : ',');
		update_option('mwd_settings', array('public_key' => $public_key, 'private_key' => $private_key, 'csv_delimiter' => $csv_delimiter));	
		return 1;
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