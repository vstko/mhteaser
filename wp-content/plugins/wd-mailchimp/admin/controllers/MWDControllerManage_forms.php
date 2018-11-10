<?php

class MWDControllerManage_forms {
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
    $apikey = get_option('mwd_api_key', '');
    require_once MWD_DIR . "/admin/models/MWDModelHelper.php";
    $model = new MWDModelHelper();
    $model->mwd_validate_api($apikey);
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
	
	public function undo() {
		global $wpdb;	
		require_once MWD_DIR . "/admin/models/MWDModelManage_forms.php";
		$model = new MWDModelManage_forms();

		require_once MWD_DIR . "/admin/views/MWDViewManage_forms.php";
		$backup_id = (int)MWD_Library::get('backup_id');
		$id = (int)MWD_Library::get('id');
		$query = "SELECT backup_id FROM ".$wpdb->prefix."mwd_forms_backup WHERE backup_id < $backup_id AND id = $id ORDER BY backup_id DESC LIMIT 0 , 1 ";
		$backup_id = $wpdb->get_var($query);
		
		$view = new MWDViewManage_forms($model);
		$view->edit($backup_id);

	}
	
	public function redo() {
		global $wpdb;	
		require_once MWD_DIR . "/admin/models/MWDModelManage_forms.php";
		$model = new MWDModelManage_forms();

		require_once MWD_DIR . "/admin/views/MWDViewManage_forms.php";
		$backup_id = (int)MWD_Library::get('backup_id');
		$id = (int)MWD_Library::get('id');
	
		$query = "SELECT backup_id FROM ".$wpdb->prefix."mwd_forms_backup WHERE backup_id > $backup_id AND id = $id ORDER BY backup_id ASC LIMIT 0 , 1 ";
		$backup_id = $wpdb->get_var($query);
 
		$view = new MWDViewManage_forms($model);
		$view->edit($backup_id);
	}

	public function display() {
		require_once MWD_DIR . "/admin/models/MWDModelManage_forms.php";
		$model = new MWDModelManage_forms();

		require_once MWD_DIR . "/admin/views/MWDViewManage_forms.php";
		$view = new MWDViewManage_forms($model);
		$view->display();
	}

	public function add() {
		require_once MWD_DIR . "/admin/models/MWDModelManage_forms.php";
		$model = new MWDModelManage_forms();

		require_once MWD_DIR . "/admin/views/MWDViewManage_forms.php";
		$view = new MWDViewManage_forms($model);
		$view->edit(0);
	}
	
	public function edit() {
		global $wpdb;			
		require_once MWD_DIR . "/admin/models/MWDModelManage_forms.php";
		$model = new MWDModelManage_forms();

		require_once MWD_DIR . "/admin/views/MWDViewManage_forms.php";
		$view = new MWDViewManage_forms($model);
		$id = (int)MWD_Library::get('current_id', 0);
		
		$query = "SELECT backup_id FROM ".$wpdb->prefix."mwd_forms_backup WHERE cur=1 and id=".$id;
		$backup_id = $wpdb->get_var($query);
	
		if(!$backup_id) {
			$query = "SELECT max(backup_id) FROM ".$wpdb->prefix."mwd_forms_backup";
			$backup_id = $wpdb->get_var($query);
			if($backup_id)
				$backup_id++;
			else
				$backup_id = 1;

			$query = "INSERT INTO ".$wpdb->prefix."mwd_forms_backup SELECT ".$backup_id." AS backup_id, 1 AS cur, mwdformsbkup.id, mwdformsbkup.title, mwdformsbkup.mail, mwdformsbkup.form_front, mwdformsbkup.theme, mwdformsbkup.list_id, mwdformsbkup.type, mwdformsbkup.counter, mwdformsbkup.label_order, mwdformsbkup.label_order_current, mwdformsbkup.pagination, mwdformsbkup.show_title, mwdformsbkup.show_numbers,  mwdformsbkup.form_fields,  mwdformsbkup.sortable, mwdformsbkup.published, mwdformsbkup.savedb, mwdformsbkup.requiredmark, mwdformsbkup.double_optin, mwdformsbkup.welcome_email, mwdformsbkup.hide_after_signup, mwdformsbkup.update_subscriber, mwdformsbkup.replace_interest_groups, mwdformsbkup.delete_member, mwdformsbkup.send_goodbye, mwdformsbkup.send_notify, mwdformsbkup.subscribe_action, mwdformsbkup.merge_variables, mwdformsbkup.use_mailchimp_email, mwdformsbkup.from_mail, mwdformsbkup.from_name, mwdformsbkup.reply_to, mwdformsbkup.mail_cc, mwdformsbkup.mail_bcc, mwdformsbkup.mail_subject, mwdformsbkup.mail_mode, mwdformsbkup.mail_attachment, mwdformsbkup.mail_emptyfields, mwdformsbkup.script_mail, mwdformsbkup.mail_from_user, mwdformsbkup.mail_from_name_user, mwdformsbkup.reply_to_user, mwdformsbkup.mail_subject_user, mwdformsbkup.mail_subject_user_final, mwdformsbkup.mail_cc_user, mwdformsbkup.mail_bcc_user, mwdformsbkup.mail_mode_user, mwdformsbkup.mail_attachment_user, mwdformsbkup.optin_confirmation_post_id, mwdformsbkup.optin_confirmation_email, mwdformsbkup.final_welcome_email, mwdformsbkup.goodbye_email, mwdformsbkup.success_message, mwdformsbkup.unsubscribe_message, mwdformsbkup.gen_error_message, mwdformsbkup.invalid_email_message, mwdformsbkup.empty_submit_message, mwdformsbkup.already_subscribed_message, mwdformsbkup.not_subscribed_message, mwdformsbkup.submit_text_type, mwdformsbkup.article_id, mwdformsbkup.url, mwdformsbkup.paypal_mode, mwdformsbkup.checkout_mode, mwdformsbkup.paypal_email, mwdformsbkup.payment_currency, mwdformsbkup.tax, mwdformsbkup.javascript ,mwdformsbkup.condition, mwdformsbkup.header_title, mwdformsbkup.header_description, mwdformsbkup.header_image_url, mwdformsbkup.header_image_animation, mwdformsbkup.header_hide_image, mwdformsbkup.required_message, mwdformsbkup.groups, mwdformsbkup.unsubscribe_post_id, mwdformsbkup.hide_labels, mwdformsbkup.update_message, mwdformsbkup.mail_subject_unsubscribe FROM ".$wpdb->prefix."mwd_forms as mwdformsbkup WHERE id=".$id;
			$wpdb->query($query);
		}		
		$view->edit($backup_id);
	}

	public function form_options() {
		if (!isset($_GET['task'])) {
			$this->save_db();
		}
		require_once MWD_DIR . "/admin/models/MWDModelManage_forms.php";
		$model = new MWDModelManage_forms();

		require_once MWD_DIR . "/admin/views/MWDViewManage_forms.php";
		$view = new MWDViewManage_forms($model);
   
		global $wpdb;
		$id = (int)MWD_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "mwd_forms"));
		$view->form_options($id);
	}
	
	public function display_options() {
		if (!isset($_GET['task'])) {
			$this->save_db();
		}
		require_once MWD_DIR . "/admin/models/MWDModelManage_forms.php";
		$model = new MWDModelManage_forms();

		require_once MWD_DIR . "/admin/views/MWDViewManage_forms.php";
		$view = new MWDViewManage_forms($model);
   
		global $wpdb;
		$id = (int)MWD_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "mwd_forms"));
		$view->display_options($id);
	}

	public function save_options() {
		$message = $this->save_db_options();
		$page = MWD_Library::get('page');
		$current_id = (int)MWD_Library::get('current_id', 0);
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
	}
	
	public function save_display_options() {
		$message = $this->save_dis_options();
		$page = MWD_Library::get('page');
		$current_id = (int)MWD_Library::get('current_id', 0);
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
	}

	public function apply_options() {
		$message = $this->save_db_options();
		require_once MWD_DIR . "/admin/models/MWDModelManage_forms.php";
		$model = new MWDModelManage_forms();

		require_once MWD_DIR . "/admin/views/MWDViewManage_forms.php";
		$view = new MWDViewManage_forms($model);

		$page = MWD_Library::get('page');
		$current_id = (int)MWD_Library::get('current_id', 0);
		$fieldset_id = MWD_Library::get('fieldset_id', 'general');
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'form_options', 'current_id' => $current_id, 'message' => $message, 'fieldset_id' => $fieldset_id), admin_url('admin.php')));
	}

	public function apply_display_options() {
		$message = $this->save_dis_options();
		require_once MWD_DIR . "/admin/models/MWDModelManage_forms.php";
		$model = new MWDModelManage_forms();

		require_once MWD_DIR . "/admin/views/MWDViewManage_forms.php";
		$view = new MWDViewManage_forms($model);

		$page = MWD_Library::get('page');
		$current_id = (int)MWD_Library::get('current_id', 0);
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display_options', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
	}


	public function cancel_options() {
		$this->edit();
	}

	public function save_db_options() {
		global $wpdb;
		 $javascript = "// Occurs before the form is loaded
function before_load() {
  
}	
// Occurs just before submitting  the form
function before_submit() {
	// IMPORTANT! If you want to interrupt (stop) the submitting of the form, this function should return true. You don't need to return any value if you don't want to stop the submission.
}	
// Occurs just before resetting the form
function before_reset() {
  
}";
	
		$id = (int)MWD_Library::get('current_id', 0);
		$max_form_id = (int)$wpdb->get_var('SELECT max(id) FROM ' . $wpdb->prefix . 'mwd_forms') + 1;
		$published = (isset($_POST['published']) ? esc_html(stripslashes($_POST['published'])) : 1);
		$savedb = (isset($_POST['savedb']) ? esc_html(stripslashes($_POST['savedb'])) : 1);
		$theme = (int)(isset($_POST['theme']) ? esc_html(stripslashes($_POST['theme'])) : $wpdb->get_var("SELECT id FROM " . $wpdb->prefix . "mwd_themes where `default`=1"));
		$requiredmark = (isset($_POST['requiredmark']) ? esc_html(stripslashes($_POST['requiredmark'])) : '*');
		$double_optin = (isset($_POST['double_optin']) ? esc_html(stripslashes($_POST['double_optin'])) : 1);
		$welcome_email = (isset($_POST['welcome_email']) ? esc_html(stripslashes($_POST['welcome_email'])) : 1);
		$hide_after_signup = (isset($_POST['hide_after_signup']) ? esc_html(stripslashes($_POST['hide_after_signup'])) : 0);
		$update_subscriber = (isset($_POST['update_subscriber']) ? esc_html(stripslashes($_POST['update_subscriber'])) : 1);
		$replace_interest_groups = (isset($_POST['replace_interest_groups']) ? esc_html(stripslashes($_POST['replace_interest_groups'])) : 1);
		$delete_member = (isset($_POST['delete_member']) ? esc_html(stripslashes($_POST['delete_member'])) : 0);
		$send_goodbye = (isset($_POST['send_goodbye']) ? esc_html(stripslashes($_POST['send_goodbye'])) : 1);
		$send_notify = (isset($_POST['send_notify']) ? esc_html(stripslashes($_POST['send_notify'])) : 1);
		$subscribe_action = (isset($_POST['subscribe_action']) ? esc_html(stripslashes($_POST['subscribe_action'])) : 1);
		$use_mailchimp_email = (isset($_POST['use_mailchimp_email']) ? esc_html(stripslashes($_POST['use_mailchimp_email'])) : 1);
		$mail = (isset($_POST['mail']) ? esc_html(stripslashes($_POST['mail'])) : '');
		if (isset($_POST['mailToAdd']) && esc_html(stripslashes($_POST['mailToAdd'])) != '') {
			$mail .= esc_html(stripslashes($_POST['mailToAdd'])) . ',';
		}
		$from_mail = (isset($_POST['from_mail']) ? esc_html(stripslashes($_POST['from_mail'])) : '');
		if ($from_mail == "other") {
			$from_mail = (isset($_POST['mail_from_other']) ? esc_html(stripslashes($_POST['mail_from_other'])) : '');
		}
		$from_name = (isset($_POST['from_name']) ? esc_html(stripslashes($_POST['from_name'])) : '');
		$reply_to = (isset($_POST['reply_to']) ? esc_html(stripslashes($_POST['reply_to'])) : '');
		if ($reply_to == "other") {
			$reply_to = (isset($_POST['reply_to_other']) ? esc_html(stripslashes($_POST['reply_to_other'])) : '');
		}
		$mail_cc = (isset($_POST['mail_cc']) ? esc_html(stripslashes($_POST['mail_cc'])) : '');
		$mail_bcc = (isset($_POST['mail_bcc']) ? esc_html(stripslashes($_POST['mail_bcc'])) : '');
		$mail_subject = (isset($_POST['mail_subject']) ? esc_html(stripslashes($_POST['mail_subject'])) : '');
		$mail_mode = (isset($_POST['mail_mode']) ? esc_html(stripslashes($_POST['mail_mode'])) : 1);
		$mail_attachment = (isset($_POST['mail_attachment']) ? esc_html(stripslashes($_POST['mail_attachment'])) : 1);
		$mail_emptyfields = (isset($_POST['mail_emptyfields']) ? esc_html(stripslashes($_POST['mail_emptyfields'])) : 0);
		$script_mail = (isset($_POST['script_mail']) ? stripslashes($_POST['script_mail']) : '');
		$mail_from_user = (isset($_POST['mail_from_user']) ? esc_html(stripslashes($_POST['mail_from_user'])) : '');
		$mail_from_name_user = (isset($_POST['mail_from_name_user']) ? esc_html(stripslashes($_POST['mail_from_name_user'])) : '');
		$reply_to_user = (isset($_POST['reply_to_user']) ? esc_html(stripslashes($_POST['reply_to_user'])) : '');
		$mail_subject_user = (isset($_POST['mail_subject_user']) ? esc_html(stripslashes($_POST['mail_subject_user'])) : '');
		$mail_subject_user_final = (isset($_POST['mail_subject_user_final']) ? esc_html(stripslashes($_POST['mail_subject_user_final'])) : '');
		$mail_cc_user = (isset($_POST['mail_cc_user']) ? esc_html(stripslashes($_POST['mail_cc_user'])) : '');
		$mail_bcc_user = (isset($_POST['mail_bcc_user']) ? esc_html(stripslashes($_POST['mail_bcc_user'])) : '');
		$mail_mode_user = (isset($_POST['mail_mode_user']) ? esc_html(stripslashes($_POST['mail_mode_user'])) : 1);
		$mail_attachment_user = (isset($_POST['mail_attachment_user']) ? esc_html(stripslashes($_POST['mail_attachment_user'])) : 1);

		$optin_confirmation_post_id = (int)$wpdb->get_var('SELECT optin_confirmation_post_id FROM ' . $wpdb->prefix . 'mwd_forms WHERE optin_confirmation_post_id!=0');
		$optin_confirmation_post = array(
			'post_title'    => 'Opt-In Confirmation',
			'post_content'  => '<div class="mwd-confirm-error">
<h3>Subscription Confirmed</h3>
Your subscription to our list has been confirmed.

Thank you for subscribing!

</div>
[mwd_optin_confirmation]',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type'   => 'mwd_optin_conf',
		);

		if(!$optin_confirmation_post_id || get_post( $optin_confirmation_post_id )===NULL)
			$optin_confirmation_post_id = wp_insert_post( $optin_confirmation_post );
		
		$unsubscribe_post_id = (int)$wpdb->get_var('SELECT unsubscribe_post_id FROM ' . $wpdb->prefix . 'mwd_forms WHERE unsubscribe_post_id!=0');
		$unsubscribe_post = array(
			'post_title'    => 'Unsubscribe',
			'post_content'  => '<div class="mwd-confirm-error">
<h3>Unsubscribe Successful</h3>
You have been removed from MailChimp list.

</div>
[mwd_unsubscribe]',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type'   => 'mwd_optin_conf',
		);

		if(!$unsubscribe_post_id || get_post( $unsubscribe_post_id )===NULL)
			$unsubscribe_post_id = wp_insert_post( $unsubscribe_post );
		
		$optin_confirmation_email = (isset($_POST['optin_confirmation_email']) ? stripslashes($_POST['optin_confirmation_email']) : '');
		$final_welcome_email = (isset($_POST['final_welcome_email']) ? stripslashes($_POST['final_welcome_email']) : '');
		$goodbye_email = (isset($_POST['goodbye_email']) ? stripslashes($_POST['goodbye_email']) : '');

		$success_message = (isset($_POST['success_message']) ? htmlspecialchars_decode(esc_html(stripslashes($_POST['success_message']))) : '');
		$update_message = (isset($_POST['update_message']) ? htmlspecialchars_decode(esc_html(stripslashes($_POST['update_message']))) : '');
		$unsubscribe_message = (isset($_POST['unsubscribe_message']) ? htmlspecialchars_decode(esc_html(stripslashes($_POST['unsubscribe_message']))) : '');
		$gen_error_message = (isset($_POST['gen_error_message']) ? esc_html(stripslashes($_POST['gen_error_message'])) : '');
		$invalid_email_message = (isset($_POST['invalid_email_message']) ? esc_html(stripslashes($_POST['invalid_email_message'])) : '');
		$already_subscribed_message = (isset($_POST['already_subscribed_message']) ? esc_html(stripslashes($_POST['already_subscribed_message'])) : '');
		$not_subscribed_message = (isset($_POST['not_subscribed_message']) ? esc_html(stripslashes($_POST['not_subscribed_message'])) : '');
		$empty_submit_message = (isset($_POST['empty_submit_message']) ? esc_html(stripslashes($_POST['empty_submit_message'])) : '');
		$required_message = (isset($_POST['required_message']) ? esc_html(stripslashes($_POST['required_message'])) : '');
		if (isset($_POST['submit_text_type'])) {
			$submit_text_type = esc_html(stripslashes($_POST['submit_text_type']));
			if ($submit_text_type == 5) {
				$article_id = (isset($_POST['page_name']) ? esc_html(stripslashes($_POST['page_name'])) : 0);
			}
			else {
				$article_id = (isset($_POST['post_name']) ? esc_html(stripslashes($_POST['post_name'])) : 0);
			}
		}
		else {
			$submit_text_type = 0;
			$article_id = 0;
		}
		$url = (isset($_POST['url']) ? esc_html(stripslashes($_POST['url'])) : '');
		$paypal_mode = (isset($_POST['paypal_mode']) ? esc_html(stripslashes($_POST['paypal_mode'])) : 0);
		$checkout_mode = (isset($_POST['checkout_mode']) ? esc_html(stripslashes($_POST['checkout_mode'])) : 0);
		$paypal_email = (isset($_POST['paypal_email']) ? esc_html(stripslashes($_POST['paypal_email'])) : '');
		$payment_currency = (isset($_POST['payment_currency']) ? stripslashes($_POST['payment_currency']) : '');
		$tax = (isset($_POST['tax']) ? esc_html(stripslashes($_POST['tax'])) : 0);
		$javascript = (isset($_POST['javascript']) ? stripslashes($_POST['javascript']) : $javascript);
		$condition = (isset($_POST['condition']) ? esc_html(stripslashes($_POST['condition'])) : '');
		$hide_labels = isset($_POST['hide_labels']) ? esc_html(stripslashes($_POST['hide_labels'])) : 0;
		$update_message = isset($_POST['update_message']) ? esc_html(stripslashes($_POST['update_message'])) : '';
		$mail_subject_unsubscribe = isset($_POST['mail_subject_unsubscribe']) ? esc_html(stripslashes($_POST['mail_subject_unsubscribe'])) : '';

		$save = $wpdb->update($wpdb->prefix . 'mwd_forms', array(
			'published' => $published,
			'savedb' => $savedb,
			'theme' => $theme,
			'requiredmark' => $requiredmark,
			'double_optin' => $double_optin,
			'welcome_email' => $welcome_email,
			'hide_after_signup' => $hide_after_signup,
			'update_subscriber' => $update_subscriber,
			'replace_interest_groups' => $replace_interest_groups,
			'delete_member' => $delete_member,
			'send_goodbye' => $send_goodbye,
			'send_notify' => $send_notify,
			'subscribe_action' => $subscribe_action,
			'use_mailchimp_email' => $use_mailchimp_email,
			'mail' => $mail,
			'from_mail' => $from_mail,
			'from_name' => $from_name,
			'reply_to' => $reply_to,
			'mail_cc' => $mail_cc,
			'mail_bcc' => $mail_bcc,
			'mail_subject' => $mail_subject,
			'mail_mode' => $mail_mode,
			'mail_attachment' => $mail_attachment,
			'mail_emptyfields' => $mail_emptyfields,
			'script_mail' => $script_mail,
			'mail_from_user' => $mail_from_user,
			'mail_from_name_user' => $mail_from_name_user,
			'reply_to_user' => $reply_to_user,
			'mail_subject_user' => $mail_subject_user,
			'mail_subject_user_final' => $mail_subject_user_final,
			'mail_cc_user' => $mail_cc_user,
			'mail_bcc_user' => $mail_bcc_user,
			'mail_mode_user' => $mail_mode_user,
			'mail_attachment_user' => $mail_attachment_user,
			'optin_confirmation_post_id' => $optin_confirmation_post_id,
			'optin_confirmation_email' => $optin_confirmation_email,
			'final_welcome_email' => $final_welcome_email,
			'goodbye_email' => $goodbye_email,
			'success_message' => $success_message,
			'unsubscribe_message' => $unsubscribe_message,
			'gen_error_message' => $gen_error_message,
			'invalid_email_message' => $invalid_email_message,
			'already_subscribed_message' => $already_subscribed_message,
			'not_subscribed_message' => $not_subscribed_message,
			'empty_submit_message' => $empty_submit_message,
			'submit_text_type' => $submit_text_type,
			'article_id' => $article_id,
			'url' => $url,
			'paypal_mode' => $paypal_mode,
			'checkout_mode' => $checkout_mode,
			'paypal_email' => $paypal_email,
			'payment_currency' => $payment_currency,
			'tax' => $tax,
			'javascript' => $javascript,
			'condition' => $condition,
			'required_message' => $required_message,
			'unsubscribe_post_id' => $unsubscribe_post_id,
			'hide_labels' => $hide_labels,
			'update_message' => $update_message,
			'mail_subject_unsubscribe' => $mail_subject_unsubscribe,
		), array('id' => $id));
		
		if ($save !== FALSE) {
			$save_in_backup = $wpdb->update($wpdb->prefix . 'mwd_forms_backup', array(
			'theme' => $theme
		), array('id' => $id));
			return 8;
		}
		else {
			return 2;
		}
	}

	public function save_dis_options() {
		global $wpdb;
		$id = (int)MWD_Library::get('current_id', 0);
		$scrollbox_loading_delay = (isset($_POST['scrollbox_loading_delay']) ? esc_html(stripslashes($_POST['scrollbox_loading_delay'])) : 0);
		$popover_animate_effect = (isset($_POST['popover_animate_effect']) ? esc_html(stripslashes($_POST['popover_animate_effect'])) : '');
		$popover_loading_delay = (isset($_POST['popover_loading_delay']) ? esc_html(stripslashes($_POST['popover_loading_delay'])) : 0);
		$popover_frequency = (isset($_POST['popover_frequency']) ? esc_html(stripslashes($_POST['popover_frequency'])) : 0);
		$topbar_position = (isset($_POST['topbar_position']) ? esc_html(stripslashes($_POST['topbar_position'])) : 1);
		$topbar_remain_top = (isset($_POST['topbar_remain_top']) ? esc_html(stripslashes($_POST['topbar_remain_top'])) : 1);
		$topbar_closing = (isset($_POST['topbar_closing']) ? esc_html(stripslashes($_POST['topbar_closing'])) : 1);
		$topbar_hide_duration = (isset($_POST['topbar_hide_duration']) ? esc_html(stripslashes($_POST['topbar_hide_duration'])) : 0);
		$scrollbox_position = (isset($_POST['scrollbox_position']) ? esc_html(stripslashes($_POST['scrollbox_position'])) : 1);
		$scrollbox_trigger_point = (isset($_POST['scrollbox_trigger_point']) ? esc_html(stripslashes($_POST['scrollbox_trigger_point'])) : 20);
		$scrollbox_hide_duration = (isset($_POST['scrollbox_hide_duration']) ? esc_html(stripslashes($_POST['scrollbox_hide_duration'])) : 0);
		$scrollbox_auto_hide = (isset($_POST['scrollbox_auto_hide']) ? esc_html(stripslashes($_POST['scrollbox_auto_hide'])) :1);
		$hide_mobile = (isset($_POST['hide_mobile']) ? esc_html(stripslashes($_POST['hide_mobile'])) : 0);
		$scrollbox_closing = (isset($_POST['scrollbox_closing']) ? esc_html(stripslashes($_POST['scrollbox_closing'])) : 1);
		$scrollbox_minimize = (isset($_POST['scrollbox_minimize']) ? esc_html(stripslashes($_POST['scrollbox_minimize'])) : 1);
		$scrollbox_minimize_text = (isset($_POST['scrollbox_minimize_text']) ? esc_html(stripslashes($_POST['scrollbox_minimize_text'])) : '');
		
		$type = (isset($_POST['form_type']) ? esc_html(stripslashes($_POST['form_type'])) : 'embadded');
		$display_on = (isset($_POST['display_on']) ? esc_html(implode(',', $_POST['display_on'])) : '');
		$posts_include = (isset($_POST['posts_include']) ? esc_html(stripslashes($_POST['posts_include'])) : '');
		$pages_include = (isset($_POST['pages_include']) ? esc_html(stripslashes($_POST['pages_include'])) : '');
		$display_on_categories = (isset($_POST['display_on_categories']) ? esc_html(implode(',', $_POST['display_on_categories'])) : '');
		$current_categories = (isset($_POST['current_categories']) ? esc_html(stripslashes($_POST['current_categories'])) : '');
		$show_for_admin = (isset($_POST['show_for_admin']) ? esc_html(stripslashes($_POST['show_for_admin'])) : 0);

		$save = $wpdb->update($wpdb->prefix . 'mwd_display_options', array(
			'type' => $type,
			'scrollbox_loading_delay' => $scrollbox_loading_delay,
			'popover_animate_effect' => $popover_animate_effect,
			'popover_loading_delay' => $popover_loading_delay,
			'popover_frequency' => $popover_frequency,
			'topbar_position' => $topbar_position,
			'topbar_remain_top' => $topbar_remain_top,
			'topbar_closing' => $topbar_closing,
			'topbar_hide_duration' => $topbar_hide_duration,
			'scrollbox_position' => $scrollbox_position,
			'scrollbox_trigger_point' => $scrollbox_trigger_point,
			'scrollbox_hide_duration' => $scrollbox_hide_duration,
			'scrollbox_auto_hide' => $scrollbox_auto_hide,
			'hide_mobile' => $hide_mobile,
			'scrollbox_closing' => $scrollbox_closing,
			'scrollbox_minimize' => $scrollbox_minimize,
			'scrollbox_minimize_text' => $scrollbox_minimize_text,
			'display_on' => $display_on,
			'posts_include' => $posts_include,
			'pages_include' => $pages_include,
			'display_on_categories' => $display_on_categories,
			'current_categories' => $current_categories,
			'show_for_admin' => $show_for_admin,
		), array('form_id' => $id));
		
		if ($save !== FALSE) {
			$save_in_backup = $wpdb->update($wpdb->prefix . 'mwd_forms_backup', array(
				'type' => $type
			), array('id' => $id));
			
			$save_in_form = $wpdb->update($wpdb->prefix . 'mwd_forms', array(
				'type' => $type
			), array('id' => $id));
			return 8;
		}
		else {
			return 2;
		}	
	}
	
	public function save_as_copy() {
		$message = $this->save_db_as_copy();
		$page = MWD_Library::get('page');
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
	}

	public function save() {
		$message = $this->save_db();
		$page = MWD_Library::get('page');
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
	}

	public function apply() {
		$message = $this->save_db();
		global $wpdb;
		$id = (int) $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "mwd_forms");
		$current_id = (int)MWD_Library::get('current_id', $id);
		$page = MWD_Library::get('page');
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
	}

	public function save_db() {
		global $wpdb;
	 $javascript = "// Occurs before the form is loaded
function before_load() {
  
}	
// Occurs just before submitting  the form
function before_submit() {
	// IMPORTANT! If you want to interrupt (stop) the submitting of the form, this function should return true. You don't need to return any value if you don't want to stop the submission.
}	
// Occurs just before resetting the form
function before_reset() {
  
}";
		$id = (int)MWD_Library::get('current_id', 0);
		$title = (isset($_POST['title']) ? esc_html(stripslashes($_POST['title'])) : '');
		$theme = (isset($_POST['theme']) ? stripslashes($_POST['theme']) : '');
		$list_id = isset($_POST['list']) ? implode(',',$_POST['list']) : '';
	
		$form_front = (isset($_POST['form_front']) ? stripslashes($_POST['form_front']) : '');
		$sortable = (isset($_POST['sortable']) ? stripslashes($_POST['sortable']) : 1);
		$counter = (isset($_POST['counter']) ? esc_html(stripslashes($_POST['counter'])) : 0);
		$label_order = (isset($_POST['label_order']) ? esc_html(stripslashes($_POST['label_order'])) : '');
		$pagination = (isset($_POST['pagination']) ? esc_html(stripslashes($_POST['pagination'])) : '');
		$show_title = (isset($_POST['show_title']) ? esc_html(stripslashes($_POST['show_title'])) : '');
		$show_numbers = (isset($_POST['show_numbers']) ? esc_html(stripslashes($_POST['show_numbers'])) : '');
		$label_order_current = (isset($_POST['label_order_current']) ? esc_html(stripslashes($_POST['label_order_current'])) : '');
		$form_fields = (isset($_POST['form_fields']) ? stripslashes($_POST['form_fields']) : '');
		$header_title = (isset($_POST['header_title']) ? esc_html(stripslashes($_POST['header_title'])) : '');
		$header_description = (isset($_POST['header_description']) ? htmlspecialchars_decode(esc_html(stripslashes($_POST['header_description']))) : '');

		$header_image_url = '';
		$files = isset($_FILES['header_image']) ? $_FILES['header_image'] : NULL;
		if(isset($files) && $files['name']){
			$destination = '/wp-content/plugins/wd-mailchimp/images/uploads';
			$fileName = $files['name'];
			$fileTemp = $files['tmp_name'];
			if(move_uploaded_file($fileTemp, ABSPATH  . $destination. '/' . $fileName)) {
				$fileTemp = $destination . '/' . $fileName;				
				$check = getimagesize( ABSPATH . $fileTemp);
				if($check !== false) {
					$header_image_url = site_url() . $destination. '/' . $fileName;
				}
			}
		} 
		else{
			$header_image_url = (isset($_POST['header_image_url']) ? esc_html(stripslashes($_POST['header_image_url'])) : '');
		}
		
		$header_image_animation = (isset($_POST['header_image_animation']) ? esc_html(stripslashes($_POST['header_image_animation'])) : '');
		$header_hide_image = (isset($_POST['header_hide_image']) ? esc_html(stripslashes($_POST['header_hide_image'])) : '');
		$merge_variables = (isset($_POST['merge_variables']) ? esc_html(stripslashes($_POST['merge_variables'])) : '');
		$groups = (isset($_POST['groups']) ? esc_html(stripslashes($_POST['groups'])) : '');
		
		$type = (isset($_POST['form_type']) ? esc_html(stripslashes($_POST['form_type'])) : 'embedded');
		$scrollbox_minimize_text = $header_title ? $header_title : 'The form is minimized.';
		$subscribe_action = (isset($_POST['subscribe_action']) ? esc_html(stripslashes($_POST['subscribe_action'])) : 1);
		
		if ($id != 0) {
			$save = $wpdb->update($wpdb->prefix . 'mwd_forms', array(
				'title' => $title,
				'theme' => $theme,
				'list_id' => $list_id,
				'form_front' => $form_front,
				'sortable' => $sortable,
				'counter' => $counter,
				'label_order' => $label_order,
				'label_order_current' => $label_order_current,
				'pagination' => $pagination,
				'show_title' => $show_title,
				'show_numbers' => $show_numbers,
				'form_fields' => $form_fields,
				'merge_variables' => $merge_variables,
				'header_title' => $header_title,
				'header_description' => $header_description,
				'header_image_url' => $header_image_url,
				'header_image_animation' => $header_image_animation,
				'header_hide_image' => $header_hide_image,
				'groups' => $groups,
				'subscribe_action' => $subscribe_action,
			  ), array('id' => $id));
		}
		else {
			$save = $wpdb->insert($wpdb->prefix . 'mwd_forms', array(
				'title' => $title,
				'mail' => '',
				'form_front' => $form_front,
				'theme' => $theme,
				'list_id' => $list_id,
				'type' => $type,
				'counter' => $counter,
				'label_order' => $label_order,
				'label_order_current' => $label_order_current,
				'pagination' => $pagination,
				'show_title' => $show_title,
				'show_numbers' => $show_numbers,
				'form_fields' => $form_fields,
				'sortable' => $sortable,
				'published' => 1,
				'savedb' => 1,
				'requiredmark' => '*',

				'double_optin' => 1,
				'welcome_email' => 1,
				'hide_after_signup' => 0,
				'update_subscriber' => 1,
				'replace_interest_groups' => 1,
				'delete_member' => 0,
				'send_goodbye' => 1,
				'send_notify' => 1,
				'subscribe_action' => $subscribe_action,
				'merge_variables' => $merge_variables,	
				'use_mailchimp_email' => 1,
				'from_mail' => '',
				'from_name' => '',
				'reply_to' => '',
				'mail_cc' => '',
				'mail_bcc' => '',
				'mail_subject' => '',
				'mail_mode' => 1,
				'mail_attachment' => 1,
				'mail_emptyfields' => 0,
				'script_mail' => '%all%',
				'mail_from_user' => '%list_from_email%',
				'mail_from_name_user' => '%list_from_name%',
				'reply_to_user' => '%list_from_email%',
				'mail_subject_user' => '%list_name%: Please Confirm Subscription',
				'mail_subject_user_final' => '%list_name%: Subscription Confirmed',
				'mail_cc_user' => '',
				'mail_bcc_user' => '',
				'mail_mode_user' => 1,
				'mail_attachment_user' => 1,
				'optin_confirmation_post_id' => 0,
				'optin_confirmation_email' => '<h2>%list_name%</h2>
<h2>Please Confirm Subscription</h2>
<a href="%confirmation_link%">Yes, subscribe me to this list.</a>

If you received this email by mistake, simply delete it. You won\'t be subscribed if you don\'t click the confirmation link above.

For questions about this list, please contact:
%list_owner_email%',
				'final_welcome_email' => '<h2>%list_name%</h2>
Your subscription to our list has been confirmed.

For your records, here is a copy of the information you submitted to us...

%all%

&nbsp;

If at any time you wish to stop receiving our emails, you can:
<a href="%unsubscribe_link%">Unsubscribe here.</a>

You may also contact us at:
%list_owner_email%',
				'goodbye_email' => '<h2>%list_name%</h2>
<h2>We have removed your email address from our list.</h2>
We\'re sorry to see you go.

For questions or comments, please contact us at:
%list_owner_email%',
				'success_message' => '<h2>Thank you for subscribing!</h2>
				
Check your email for the confirmation message.',
				'unsubscribe_message' => 'You have successfully unsubscribed from %list_name%.',
				'gen_error_message' => 'Whoops, something went wrong! Please try again.',
				'invalid_email_message' => 'Please enter a valid email address.',
				'empty_submit_message' => 'No data is submitted.',
				'already_subscribed_message' => 'It looks like you\'re already subscribed to this list.',
				'not_subscribed_message' => 'Your email is not included in MailChimp list. Please try again.',
				'submit_text_type' => 0,
				'article_id' => 0,
				'url' => '',
				'paypal_mode' => 0,
				'checkout_mode' => 0,
				'paypal_email' => '',
				'payment_currency' => '',
				'tax' => 0,
				'javascript' => $javascript,
				'condition' => '',
				'header_title' => $header_title,
				'header_description' => $header_description,
				'header_image_url' => $header_image_url,
				'header_image_animation' => $header_image_animation,
				'header_hide_image' => $header_hide_image,
				'required_message' => '%s field is required.',
				'groups' => $groups,
				'unsubscribe_post_id' => 0,
				'hide_labels' => 0,
				'update_message' => 'Thank you for subscribing!',
				'mail_subject_unsubscribe' => '%list_name%: Unsubcribe Confirmed',
			), array(
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',

				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
			));
			
			$id = (int)$wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "mwd_forms");
			
			$save_display_options = $wpdb->insert($wpdb->prefix . 'mwd_display_options', array(	
				'form_id' => $id,
				'type' => $type,
				'scrollbox_loading_delay' => 0,
				'popover_animate_effect' => '',
				'popover_loading_delay' => 0,
				'popover_frequency' => 0,
				'topbar_position' => 1,
				'topbar_remain_top' => 1,
				'topbar_closing' => 1,
				'topbar_hide_duration' => 0,
				'scrollbox_position' => 1,
				'scrollbox_trigger_point' => 20,
				'scrollbox_hide_duration' => 0,
				'scrollbox_auto_hide' => 1,
				'hide_mobile' => 0,
				'scrollbox_closing' => 1,
				'scrollbox_minimize' => 1,
				'scrollbox_minimize_text' => $scrollbox_minimize_text,
				'display_on' => 'home,post,page',
				'posts_include' => '',
				'pages_include' => '',
				'display_on_categories' => '',
				'current_categories' => '',
				'show_for_admin' => 0,
			), array(
				'%d',
				'%s',
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
			));	
			
			$wpdb->insert($wpdb->prefix . 'mwd_forms_views', array(
				'form_id' => $id,
				'views' => 0
			), array(
				'%d',
				'%d'
			));
		}
	
		$backup_id = (isset($_POST['backup_id']) ? (int)esc_html(stripslashes($_POST['backup_id'])) : '');
		if($backup_id) {
			$query = "SELECT backup_id FROM ".$wpdb->prefix."mwd_forms_backup WHERE backup_id > ".$backup_id." AND id = ".$id." ORDER BY backup_id ASC LIMIT 0 , 1 ";
			if($wpdb->get_var($query)) {
				$query = "DELETE FROM ".$wpdb->prefix."mwd_forms_backup WHERE backup_id > ".$backup_id." AND id = ".$id;
				$wpdb->query($query);
			}

			$row = $wpdb->get_row($wpdb->prepare("SELECT form_fields, form_front FROM ".$wpdb->prefix."mwd_forms_backup WHERE backup_id = '%d'", $backup_id));
			if($row->form_fields==$form_fields and $row->form_front==$form_front) {
				$save = $wpdb->update($wpdb->prefix . 'mwd_forms_backup', array(
					'cur' => 1,
					'title' => $title,
					'theme' => $theme,
					'type' => $type,
					'list_id' => $list_id,
					'form_front' => $form_front,
					'sortable' => $sortable,
					'counter' => $counter,
					'label_order' => $label_order,
					'label_order_current' => $label_order_current,
					'pagination' => $pagination,
					'show_title' => $show_title,
					'show_numbers' => $show_numbers,
					'form_fields' => $form_fields,
					'merge_variables' => $merge_variables,
					'header_title' => $header_title,
					'header_description' => $header_description,
					'header_image_url' => $header_image_url,
					'header_image_animation' => $header_image_animation,
					'header_hide_image' => $header_hide_image,
					'groups' => $groups,
					'subscribe_action' => $subscribe_action,
				), array('backup_id' => $backup_id));

				if ($save !== FALSE) {
					return 1;
				}
				else {
					return 2;
				}
			}
		}
	
		$wpdb->query("UPDATE ".$wpdb->prefix."mwd_forms_backup SET cur=0 WHERE id=".$id ); 
		$save = $wpdb->insert($wpdb->prefix . 'mwd_forms_backup', array(
			'cur' => 1,
			'id' => $id,
			'title' => $title,
			'mail' => '',
			'form_front' => $form_front,
			'theme' => $theme,
			'list_id' => $list_id,
			'type' => $type,
			'counter' => $counter,
			'label_order' => $label_order,
			'label_order_current' => $label_order_current,
			'pagination' => $pagination,
			'show_title' => $show_title,
			'show_numbers' => $show_numbers,
			'form_fields' => $form_fields,
			'sortable' => $sortable,
			'published' => 1,
			'savedb' => 1,
			'requiredmark' => '*',

			'double_optin' => 1,
			'welcome_email' => 1,
			'hide_after_signup' => 0,
			'update_subscriber' => 1,
			'replace_interest_groups' => 1,
			'delete_member' => 0,
			'send_goodbye' => 1,
			'send_notify' => 1,
			'subscribe_action' => $subscribe_action,
			'merge_variables' => $merge_variables,	
			'use_mailchimp_email' => 1,
			'from_mail' => '',
			'from_name' => '',
			'reply_to' => '',
			'mail_cc' => '',
			'mail_bcc' => '',
			'mail_subject' => '',
			'mail_mode' => 1,
			'mail_attachment' => 1,
			'mail_emptyfields' => 0,
			'script_mail' => '%all%',
			'mail_from_user' => '%list_from_email%',
			'mail_from_name_user' => '%list_from_name%',
			'reply_to_user' => '%list_from_email%',
			'mail_subject_user' => '%list_name%: Please Confirm Subscription',
			'mail_subject_user_final' => '%list_name%: Subscription Confirmed',
			'mail_cc_user' => '',
			'mail_bcc_user' => '',
			'mail_mode_user' => 1,
			'mail_attachment_user' => 1,
			'optin_confirmation_post_id' => 0,
			'optin_confirmation_email' => '<h2>%list_name%</h2>
<h2>Please Confirm Subscription</h2>
<a href="%confirmation_link%">Yes, subscribe me to this list.</a>

If you received this email by mistake, simply delete it. You won\'t be subscribed if you don\'t click the confirmation link above.

For questions about this list, please contact:
%list_owner_email%',
			'final_welcome_email' => '<h2>%list_name%</h2>
Your subscription to our list has been confirmed.

For your records, here is a copy of the information you submitted to us...

%all%

&nbsp;

If at any time you wish to stop receiving our emails, you can:
<a href="%unsubscribe_link%">Unsubscribe here.</a>

You may also contact us at:
%list_owner_email%',
			'goodbye_email' => '<h2>%list_name%</h2>
<h2>We have removed your email address from our list.</h2>
We\'re sorry to see you go.

For questions or comments, please contact us at:
%list_owner_email%',
			'success_message' => '<h2>Thank you for subscribing!</h2>
			
Check your email for the confirmation message.',
			'unsubscribe_message' => '<h2>You have successfully unsubscribed from %list_name%.',
			'gen_error_message' => 'Whoops, something went wrong! Please try again.',
			'invalid_email_message' => 'Please enter a valid email address.',
			'empty_submit_message' => 'No data is submitted.',
			'already_subscribed_message' => 'It looks like you\'re already subscribed to this list.',
			'not_subscribed_message' => 'Your email is not included in MailChimp list. Please try again.',
			'submit_text_type' => 0,
			'article_id' => 0,
			'url' => '',
			'paypal_mode' => 0,
			'checkout_mode' => 0,
			'paypal_email' => '',
			'payment_currency' => '',
			'tax' => 0,
			'javascript' => $javascript,
			'condition' => '',
			'header_title' => $header_title,
			'header_description' => $header_description,
			'header_image_url' => $header_image_url,
			'header_image_animation' => $header_image_animation,
			'header_hide_image' => $header_hide_image,
			'required_message' => '%s field is required.',
			'groups' => $groups,
			'unsubscribe_post_id' => 0,
			'hide_labels' => 0,
			'update_message' => 'Thank you for subscribing!',
			'mail_subject_unsubscribe' => '%list_name%: Unsubcribe Confirmed',
		), array(
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',

			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s',
			'%s',
		));
  
		$query = "SELECT count(backup_id) FROM ".$wpdb->prefix."mwd_forms_backup WHERE id = ".$id;
		$wpdb->get_var($query);
		if($wpdb->get_var($query)>10) {
			$query = "DELETE FROM ".$wpdb->prefix."mwd_forms_backup WHERE id = ".$id." ORDER BY backup_id ASC LIMIT 1 ";
			$wpdb->query($query);
		}
	
		if ($save !== FALSE) {
			return 1;
		}
		else {
			return 2;
		}
	}
	
	public function save_db_as_copy() {
		global $wpdb;
		$id = (int)MWD_Library::get('current_id', 0);
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'mwd_forms WHERE id="%d"', $id));
		$row_display = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'mwd_display_options WHERE form_id="%d"', $id));
		$title = (isset($_POST['title']) ? esc_html(stripslashes($_POST['title'])) : '');
		$theme = (isset($_POST['theme']) ? esc_html(stripslashes($_POST['theme'])) : '');
		$list_id = isset($_POST['list']) ? implode(',',$_POST['list']) : '';
		$form_front = (isset($_POST['form_front']) ? stripslashes($_POST['form_front']) : '');
		$sortable = (isset($_POST['sortable']) ? stripslashes($_POST['sortable']) : 1);
		$counter = (isset($_POST['counter']) ? esc_html(stripslashes($_POST['counter'])) : 0);
		$label_order = (isset($_POST['label_order']) ? esc_html(stripslashes($_POST['label_order'])) : '');
		$label_order_current = (isset($_POST['label_order_current']) ? esc_html(stripslashes($_POST['label_order_current'])) : '');
		$pagination = (isset($_POST['pagination']) ? esc_html(stripslashes($_POST['pagination'])) : '');
		$show_title = (isset($_POST['show_title']) ? esc_html(stripslashes($_POST['show_title'])) : '');
		$show_numbers = (isset($_POST['show_numbers']) ? esc_html(stripslashes($_POST['show_numbers'])) : '');
		$form_fields = (isset($_POST['form_fields']) ? stripslashes($_POST['form_fields']) : '');
		$header_title = (isset($_POST['header_title']) ? esc_html(stripslashes($_POST['header_title'])) : '');
		$header_description = (isset($_POST['header_description']) ? htmlspecialchars_decode(esc_html(stripslashes($_POST['header_description']))) : '');

		$header_image_url = '';
		$files = isset($_FILES['header_image']) ? $_FILES['header_image'] : NULL;
		if(isset($files) && $files['name']){
			$destination = '/wp-content/plugins/wd-mailchimp/images/uploads';
			$fileName = $files['name'];
			$fileTemp = $files['tmp_name'];
			if(move_uploaded_file($fileTemp, ABSPATH  . $destination. '/' . $fileName)) {
				$fileTemp = $destination . '/' . $fileName;				
				$check = getimagesize( ABSPATH . $fileTemp);
				if($check !== false) {
					$header_image_url = site_url() . $destination. '/' . $fileName;
				}
			}
		} 
		else{
			$header_image_url = (isset($_POST['header_image_url']) ? esc_html(stripslashes($_POST['header_image_url'])) : '');
		}
		
		$header_image_animation = (isset($_POST['header_image_animation']) ? esc_html(stripslashes($_POST['header_image_animation'])) : '');
		$header_hide_image = (isset($_POST['header_hide_image']) ? esc_html(stripslashes($_POST['header_hide_image'])) : '');
		
		$merge_variables = (isset($_POST['merge_variables']) ? esc_html(stripslashes($_POST['merge_variables'])) : '');
		$groups = (isset($_POST['groups']) ? esc_html(stripslashes($_POST['groups'])) : '');
		$subscribe_action = (isset($_POST['subscribe_action']) ? esc_html(stripslashes($_POST['subscribe_action'])) : 1);
		
		$save = $wpdb->insert($wpdb->prefix . 'mwd_forms', array(
			'title' => $title,
			'mail' => $row->mail,
			'form_front' => $form_front,
			'theme' => $theme,
			'list_id' => $list_id,
			'type' => $row->type,
			'counter' => $counter,
			'label_order' => $label_order,
			'label_order_current' => $label_order_current,
			'pagination' => $pagination,
			'show_title' => $show_title,
			'show_numbers' => $show_numbers,
			'form_fields' => $form_fields,
			'sortable' => $sortable,
			'published' => $row->published,
			'savedb' => $row->savedb,
			'requiredmark' => $row->requiredmark,
			'double_optin' => $row->double_optin,
			'welcome_email' => $row->welcome_email,
			'hide_after_signup' => $row->hide_after_signup,
			'update_subscriber' => $row->update_subscriber,
			'replace_interest_groups' => $row->replace_interest_groups,
			'delete_member' => $row->delete_member,
			'send_goodbye' => $row->send_goodbye,
			'send_notify' => $row->send_notify,
			'subscribe_action' => $subscribe_action,
			'merge_variables' => $merge_variables,
			'use_mailchimp_email' => $row->use_mailchimp_email,
			'from_mail' => $row->from_mail,
			'from_name' => $row->from_name,
			'reply_to' => $row->reply_to,
			'mail_cc' => $row->mail_cc,
			'mail_bcc' => $row->mail_bcc,
			'mail_subject' => $row->mail_subject,
			'mail_mode' => $row->mail_mode,
			'mail_attachment' => $row->mail_attachment,
			'mail_emptyfields' => $row->mail_emptyfields,
			'script_mail' => $row->script_mail,
			'mail_from_user' => $row->mail_from_user,
			'mail_from_name_user' => $row->mail_from_name_user,
			'reply_to_user' => $row->reply_to_user,
			'mail_subject_user' => $row->mail_subject_user,
			'mail_subject_user_final' => $row->mail_subject_user_final,
			'mail_cc_user' => $row->mail_cc_user,
			'mail_bcc_user' => $row->mail_bcc_user,
			'mail_mode_user' => $row->mail_mode_user,
			'mail_attachment_user' => $row->mail_attachment_user,
			'optin_confirmation_post_id' => $row->optin_confirmation_post_id,
			'optin_confirmation_email' => $row->optin_confirmation_email,
			'final_welcome_email' => $row->final_welcome_email,
			'goodbye_email' => $row->goodbye_email,
			'success_message' => $row->success_message,
			'unsubscribe_message' => $row->unsubscribe_message,
			'gen_error_message' => $row->gen_error_message,
			'invalid_email_message' => $row->invalid_email_message,
			'empty_submit_message' => $row->empty_submit_message,
			'already_subscribed_message' => $row->already_subscribed_message,
			'not_subscribed_message' => $row->not_subscribed_message,
			'submit_text_type' => $row->submit_text_type,
			'article_id' => $row->article_id,
			'url' => $row->url,
			'paypal_mode' => $row->paypal_mode,
			'checkout_mode' => $row->checkout_mode,
			'paypal_email' => $row->paypal_email,
			'payment_currency' => $row->payment_currency,
			'tax' => $row->tax,
			'javascript' => $row->javascript,
			'condition' => $row->condition,
			'header_title' => $header_title,
			'header_description' => $header_description,
			'header_image_url' => $header_image_url,
			'header_image_animation' => $header_image_animation,
			'header_hide_image' => $header_hide_image,
			'required_message' => $row->required_message,
			'groups' => $groups,
			'unsubscribe_post_id' => $row->unsubscribe_post_id,
			'hide_labels' => $row->hide_labels,
			'update_message' => $row->update_message,
			'mail_subject_unsubscribe' => $row->mail_subject_unsubscribe,
		), array(
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',

			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s',
			'%s',
		));
		$new_id = (int)$wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "mwd_forms");
		
		$save = $wpdb->insert($wpdb->prefix . 'mwd_display_options', array(
			'form_id' => $new_id,
			'type' => $row_display->type,
			'scrollbox_loading_delay' => $row_display->scrollbox_loading_delay,
			'popover_animate_effect' => $row_display->popover_animate_effect,
			'popover_loading_delay' => $row_display->popover_loading_delay,
			'popover_frequency' => $row_display->popover_frequency,
			'topbar_position' => $row_display->topbar_position,
			'topbar_remain_top' => $row_display->topbar_remain_top,
			'topbar_closing' => $row_display->topbar_closing,
			'topbar_hide_duration' => $row_display->topbar_hide_duration,
			'scrollbox_position' => $row_display->scrollbox_position,
			'scrollbox_trigger_point' => $row_display->scrollbox_trigger_point,
			'scrollbox_hide_duration' => $row_display->scrollbox_hide_duration,
			'scrollbox_auto_hide' => $row_display->scrollbox_auto_hide,
			'hide_mobile' => $row_display->hide_mobile,
			'scrollbox_closing' => $row_display->scrollbox_closing,
			'scrollbox_minimize' => $row_display->scrollbox_minimize,
			'scrollbox_minimize_text' => $row_display->scrollbox_minimize_text,
			'display_on' => $row_display->display_on,
			'posts_include' => $row_display->posts_include,
			'pages_include' => $row_display->pages_include,
			'display_on_categories' => $row_display->display_on_categories,
			'current_categories' => $row_display->current_categories,
			'show_for_admin' => $row_display->show_for_admin,
		), array(
			'%d',
			'%s',
			'%d',
			'%s',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d'
		));
		
		$wpdb->insert($wpdb->prefix . 'mwd_forms_views', array(
			'form_id' => $new_id,
			'views' => 0
		), array(
			'%d',
			'%d'
		));

		if ($save !== FALSE) {
			copy(MWD_DIR.'/css/frontend/mwd-style-'.$id.'.css', MWD_DIR.'/css/frontend/mwd-style-'.$new_id.'.css');
			return 1;
		}
		else {
			return 2;
		}
	}

	public function delete($id) {
		global $wpdb;	
		$query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms WHERE id="%d"', $id);
		if ($wpdb->query($query)) {
			$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_views WHERE form_id="%d"', $id));
			$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_submits WHERE form_id="%d"', $id));
			$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_sessions WHERE form_id="%d"', $id));
			$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_backup WHERE id="%d"', $id));
			$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_display_options WHERE form_id="%d"', $id));
		
			delete_option('mwd_confirmation-'.$id);
			if (file_exists(ABSPATH."wp-content/plugins/wd-mailchimp/css/frontend/mwd-style-".$id.".css")) {
				unlink(ABSPATH."wp-content/plugins/wd-mailchimp/css/frontend/mwd-style-".$id.".css");
			}
			
			$message = 3;
		}
		else {
			$message = 2;
		}

		$page = MWD_Library::get('page');
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
	}
  
	public function delete_all() {
		global $wpdb;
		$flag = FALSE;
		$isDefault = FALSE;
		$form_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'mwd_forms');
		foreach ($form_ids_col as $form_id) {
			if (isset($_POST['check_' . $form_id])) {
				$flag = TRUE;
				$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms WHERE id="%d"', $form_id));
				$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_views WHERE form_id="%d"', $form_id));
				$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_submits WHERE form_id="%d"', $form_id));
				$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_backup WHERE id="%d"', $form_id));
				$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_display_options WHERE form_id="%d"', $form_id));
				
				delete_option('mwd_confirmation-'.$form_id);
			}
		}
		if ($flag) {
			$message = 5;
		}
		else {
			$message = 6;
		}

		$page = MWD_Library::get('page');
		MWD_Library::mwd_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
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