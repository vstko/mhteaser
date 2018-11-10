<?php

class MWDControllerConditions {
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
		if( !class_exists('Mailchimp') ) {
			include_once( MWD_DIR . '/includes/Mailchimp.php' );
		}
		$task = MWD_Library::get('task');
		$id = (int)MWD_Library::get('current_id', 0);
		if (method_exists($this, $task)) {
			/* $nonce_mwd = MWD_Library::get('nonce_mwd');
			if(!wp_verify_nonce($nonce_mwd, 'nonce_mwd')) {
				MWD_Library::mwd_redirect(add_query_arg(array('message' => '10'), admin_url('admin.php?page=manage_mwd')));
			}	 */
			$this->$task($id);
		}
		else {
			$this->display();
		}
	}

	public function display() {
		require_once MWD_DIR . "/admin/models/MWDModelConditions.php";
		$model = new MWDModelConditions();

		require_once MWD_DIR . "/admin/views/MWDViewConditions.php";
		$view = new MWDViewConditions($model);
		$view->display();
	}
	
	public function add_condition() {
		require_once MWD_DIR . "/admin/models/MWDModelConditions.php";
		$model = new MWDModelConditions();

		require_once MWD_DIR . "/admin/views/MWDViewConditions.php";
		$view = new MWDViewConditions($model);
		$view->add_condition();
	}
	
	public function add_condition_fields() {
		require_once MWD_DIR . "/admin/models/MWDModelConditions.php";
		$model = new MWDModelConditions();

		require_once MWD_DIR . "/admin/views/MWDViewConditions.php";
		$view = new MWDViewConditions($model);
		$view->add_condition_fields();
	}
	
	public function change_choices() {
		require_once MWD_DIR . "/admin/models/MWDModelConditions.php";
		$model = new MWDModelConditions();

		require_once MWD_DIR . "/admin/views/MWDViewConditions.php";
		$view = new MWDViewConditions($model);
		$view->change_choices();
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