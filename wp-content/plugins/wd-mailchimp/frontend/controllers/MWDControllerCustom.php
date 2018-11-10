<?php

class MWDControllerCustom {
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
	public function execute($task) {
		if( ! class_exists( 'Mailchimp' ) ) {
			include_once( MWD_DIR . '/includes/Mailchimp.php' );
		}
		return $this->$task();
	}

	public function optin_confirmation() {
		$gid = (int)((isset($_GET['gid']) && esc_html($_GET['gid']) != '') ? esc_html($_GET['gid']) : 0);
		$form_id = (int)(isset($_GET['form_id']) ? esc_html($_GET['form_id']) : 0);
		$hashInfo = ((isset($_GET['h']) && esc_html($_GET['h']) != '') ? esc_html($_GET['h']) : 0);
		$hashInfo = explode("@", $hashInfo);
		
		$md5 = $hashInfo[0];
		$recipiend = isset($hashInfo[1]) ? $hashInfo[1] : '';	

		if($gid <= 0  or strlen($md5) <= 0 or strlen($recipiend) <= 0)
			return;

		require_once MWD_DIR . "/frontend/models/MWDModelCustom.php";
		$model = new MWDModelCustom();
		$confirmation_message = $model->setConfirmation($gid, $md5, $recipiend, $form_id);
		if(!empty($confirmation_message)) {	
			require_once MWD_DIR . "/frontend/views/MWDViewCustom.php";
			$view = new MWDViewCustom($model);
			$view->display($confirmation_message);
		}
	}
	
	public function unsubscribe() {
		$gid = (int)((isset($_GET['gid']) && esc_html($_GET['gid']) != '') ? esc_html($_GET['gid']) : 0);
		$form_id = (int)(isset($_GET['form_id']) ? esc_html($_GET['form_id']) : 0);
		$list_ids = isset($_GET['list_ids']) ? esc_html($_GET['list_ids']) : '';
		$email = isset($_GET['email']) ? esc_html($_GET['email']) : '';
		$md5 = isset($_GET['u']) ? esc_html($_GET['u']) : '';

		if($gid <= 0 or strlen($md5) <= 0 or strlen($email) <= 0)
			return;

		require_once MWD_DIR . "/frontend/models/MWDModelCustom.php";
		$model = new MWDModelCustom();
		$unsubscribe_message = $model->setUnsubscribe($gid, $md5, $email, $form_id, $list_ids);
		if(!empty($unsubscribe_message)) {	
			require_once MWD_DIR . "/frontend/views/MWDViewCustom.php";
			$view = new MWDViewCustom($model);
			$view->display($unsubscribe_message);
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