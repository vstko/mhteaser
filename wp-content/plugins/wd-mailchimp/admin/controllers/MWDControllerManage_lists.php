<?php

class MWDControllerManage_lists {
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
		if( ! class_exists( 'Mailchimp' ) ) {
			include_once( MWD_DIR . '/includes/Mailchimp.php' );
		}

		$apikey = get_option('mwd_api_key', '');
    require_once MWD_DIR . "/admin/models/MWDModelHelper.php";
    $model = new MWDModelHelper();
    $model->mwd_validate_api($apikey);

		$message = MWD_Library::get('message');
		echo MWD_Library::message_id($message);
		$task = MWD_Library::get('task');
		if (method_exists($this, $task)) {
			$this->$task();
		}
		else {
			$this->display();
		}
	}

	public function display() {
		require_once MWD_DIR . "/admin/models/MWDModelManage_lists.php";
		$model = new MWDModelManage_lists();

		require_once MWD_DIR . "/admin/views/MWDViewManage_lists.php";
		$view = new MWDViewManage_lists($model);
		$view->display();
	}
	
	public function view() {
		$list_id = MWD_Library::get('list_id', 0);
		require_once MWD_DIR . "/admin/models/MWDModelManage_lists.php";
		$model = new MWDModelManage_lists();

		require_once MWD_DIR . "/admin/views/MWDViewManage_lists.php";
		$view = new MWDViewManage_lists($model);
		$view->view($list_id);
	}
	
	public function subscriber_info() {
		require_once MWD_DIR . "/admin/models/MWDModelManage_lists.php";
		$model = new MWDModelManage_lists();

		require_once MWD_DIR . "/admin/views/MWDViewManage_lists.php";
		$view = new MWDViewManage_lists($model);
		
		$sub_id = (int)MWD_Library::get('sub_id', 0);
		$list_id = MWD_Library::get('list_id', 0);
		$view->subscriber_info($sub_id, $list_id); 
	}
	
	public function unsubscribe() {
		$sub_id = MWD_Library::get('sub_id', 0);
		$list_id = MWD_Library::get('list_id', 0);
		$nonce_mwd = MWD_Library::get('nonce_mwd');
		if(!wp_verify_nonce($nonce_mwd, 'nonce_mwd')) {
			MWD_Library::mwd_redirect(add_query_arg(array('list_id' => $list_id, 'message' => '10'), admin_url('admin.php?page=manage_lists&task=view')));
		}
		
		require_once MWD_DIR . "/admin/models/MWDModelManage_lists.php";
		$model = new MWDModelManage_lists();
		$model->unsubscribe();	
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