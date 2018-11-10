<?php

class MWDModelCustom {
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
	function setConfirmation($gid, $md5, $email, $form_id) {
		global $wpdb;
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);	
		$confirmation_message = array();
		$label_order_original = array();
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."mwd_forms WHERE id=%d", $form_id));
		$label_all = explode('#****#', $row->label_order);    
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			$label_id = $label_id_each[0];
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			$label_order_original[$label_id] = $label_order_each[0];
		}
		
		$optin_confirmation = get_option('mwd_confirmation-'.$form_id, array());
		if(!empty($optin_confirmation)) {
			if(isset($optin_confirmation[$gid]) && isset($optin_confirmation[$gid][$email])) {
				if($optin_confirmation[$gid][$email] == $md5) {
					//subscribe
					$merge_vars = $optin_confirmation[$gid]['merge_vars'];
					$all_form_variables = get_transient('mwd-custom-vars'.$form_id.'-'.$gid);
					$list = $all_form_variables['all'];
					$custom_fields = $all_form_variables['custom_fields'];
					$special_fields = $all_form_variables['special_fields'];
					
					$update_existing = $row->update_subscriber;
					$replace_interests = $row->replace_interest_groups;
					$email_type = $row->mail_mode_user == 1 ? 'html' : 'text';
					
					$_params = array( 
						'api_key' => $apikey,
						'id' => $row->list_id,
						'email' => array( 'email' => sanitize_email($merge_vars['EMAIL'])),
						'merge_vars' => $merge_vars,
						'email_type' => $email_type,
						'double_optin' => 0,
						'update_existing' => $update_existing,
						'send_welcome' => 0,
						'replace_interests' => $replace_interests
					);
				
					try {
						$response_data = $api->call('/lists/subscribe', $_params );
						$confirmation_message = array('msg' => '', 'type' => 'success');
					
						$fromname_user = $row->mail_from_name_user;  
						$subject_user = $row->mail_subject_user_final ? $row->mail_subject_user_final : $row->title;
						$replyto_user = $row->reply_to_user ? $row->reply_to_user : '';
						$email_content = $row->final_welcome_email;
						if ($row->mail_mode_user) {
							$content_type = "text/html";
							$mode = 1;
							$list_user = wordwrap($list, 70, "\n", true);
							$new_script = wpautop($email_content);
						}	
						else {
							$content_type = "text/plain";
							$mode = 0; 
							$list_user = wordwrap($list, 1000, "\n", true);
							$new_script = str_replace(array('<p>','</p>'), '', $email_content);
						}

						$special_fields['all'] = $list_user;
						$special_fields['user_mailchimp_id'] = $response_data['euid'];
						foreach($special_fields as $key => $special_field) {
							if(strpos($new_script, "%".$key."%") > -1)
								$new_script = str_replace("%".$key."%", $special_field, $new_script);

							if(strpos($fromname_user, "%".$key."%") > -1)
								$fromname_user = str_replace("%".$key."%", $special_field, $fromname_user);
							
							if(strpos($subject_user, "%".$key."%") > -1)
								$subject_user = str_replace("%".$key."%", $special_field, $subject_user);
						}	
	
						foreach($label_order_original as $key => $label_each) {
							if(strpos($new_script, "%".$label_each."%") > -1) {
								$new_script = str_replace("%".$label_each."%", $custom_fields[$key], $new_script);
							}
							
							if(strpos($fromname_user, "%".$label_each."%")>-1) {	
								$new_value = str_replace('<br>', ', ', $custom_fields[$key]);		
								$new_value = substr($new_value, -2) == ', ' ? substr($new_value, 0, -2) : $new_value;
								$fromname_user = str_replace("%".$label_each."%", $new_value, $fromname_user);							
							}	
								
							if(strpos($subject_user, "%".$label_each."%")>-1) {	
								$new_value = str_replace('<br>', ', ', $custom_fields[$key]);		
								$new_value = substr($new_value, -2) == ', ' ? substr($new_value, 0, -2) : $new_value;
								$subject_user = str_replace("%".$label_each."%", $new_value, $subject_user);							
							}
						}
						
						$unsubscribe_post_id = (int)$wpdb->get_var($wpdb->prepare('SELECT unsubscribe_post_id FROM ' . $wpdb->prefix . 'mwd_forms WHERE id="%d"', $form_id));
						$unsubscribe_link = get_post( $unsubscribe_post_id );
						if(strpos($new_script, "%unsubscribe_link%") > -1 && $unsubscribe_link !== NULL) {
							$unsub_link = add_query_arg(array('gid' => $gid, 'u' => $md5 , 'email' => $merge_vars['EMAIL'], 'list_ids' => $row->list_id, 'form_id' => $form_id), get_post_permalink($unsubscribe_post_id));
							
							$new_script = str_replace("%unsubscribe_link%", $unsub_link, $new_script);
						}
							
						$body = $new_script;
						$cca = $row->mail_cc_user;
						$bcc = $row->mail_bcc_user;
						$from = '';
						if ($row->mail_from_user ) {
							if ($fromname_user != '') {
								$from = "From: '" . $fromname_user . "' <" . str_replace("%list_from_email%", $special_fields['list_from_email'], $row->mail_from_user) . ">" . "\r\n";
							}	
							else {
								$from = "From: '' <" .str_replace("%list_from_email%", $special_fields['list_from_email'], $row->mail_from_user) . ">" . "\r\n";
							}
						}

						$headers =  $from . " Content-Type: " . $content_type . "; charset=\"" . get_option('blog_charset') . "\"\n";
						if ($replyto_user) {
							$headers .= "Reply-To: <" .  str_replace("%list_from_email%", $special_fields['list_from_email'], $replyto_user) . ">\r\n";
						}
						if ($cca) {
							$headers .= "Cc: <" . $cca . ">\r\n";          
						}
						if ($bcc) {
							$headers .= "Bcc: <" . $bcc . ">\r\n";          
						}

						if($merge_vars['EMAIL']) {
							$send = wp_mail(str_replace(' ', '', $merge_vars['EMAIL']), $subject_user, stripslashes($body), $headers);
						} 
						
						delete_transient('mwd-custom-vars'.$form_id.'-'.$gid);
						$optin_confirmation[$gid] = array();
						$optin_confirmation[$gid][$email] = 'confirmed';
						update_option('mwd_confirmation-'.$form_id, $optin_confirmation);
					} catch ( Exception $error ) { 
						$error_response = $error->getMessage();
						if($row->gen_error_message) {
							$confirmation_message = array('msg' => $row->gen_error_message, 'type' => 'error');
						}
						
						$this->remove($gid);
					}
				} else{
					if($optin_confirmation[$gid][$email] == 'confirmed'){
						$confirmation_message = array('msg' => $row->already_subscribed_message, 'type' => 'error');
					}
				}
			} else {
				if($row->gen_error_message) {
					$confirmation_message = array('msg' => $row->gen_error_message, 'type' => 'error');
				} else {
					$confirmation_message = array('msg' => __( "Confirmation link is invalid." , 'mwd-text' ), 'type' => 'error');
				}
				$this->remove($gid);
			}
		}

		return $confirmation_message;
	}
	
	public function setUnsubscribe($gid, $md5, $email, $form_id, $list_ids){
		global $wpdb;
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);	
		$list_ids = explode(',',$list_ids);
		$unsubscribe_message = array();
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."mwd_forms WHERE id=%d", $form_id));
		
		$unsubscribe = get_option('mwd_unsubscribe', array());
		if(!empty($unsubscribe)) {
			if(isset($unsubscribe[$form_id]) && isset($unsubscribe[$form_id][$gid])) {
				if($unsubscribe[$form_id][$gid] == $md5) {
					//unsubscribe
					foreach($list_ids as $list_id){
						$_params = array( 
							'api_key' => $apikey,
							'id' => $list_id,
							'email' => array('email' => sanitize_email($email)),
							'delete_member' => 0,
							'send_goodbye' => 0
						);
						
						try {
							$response_data = $api->call('/lists/unsubscribe', $_params );
							$unsubscribe_message = array('msg' => '', 'type' => 'success');

							unset($unsubscribe[$form_id][$gid]);
							update_option('mwd_unsubscribe', $unsubscribe);
						} catch ( Exception $error ) { 
							$error_response = $error->getMessage();
							if (strpos( $error_response, 'no record of the email') !== false) {
								if($row->invalid_email_message) {
									$unsubscribe_message = array('msg' => $row->invalid_email_message, 'type' => 'error');
								}
							} else if ( strpos( $error_response, 'is not subscribed' ) !== false ) {
								if($row->not_subscribed_message) {
									$unsubscribe_message = array('msg' => $row->not_subscribed_message, 'type' => 'error');
								}			
							}
							else { 
								if($row->gen_error_message) {
									$unsubscribe_message = array('msg' => $row->gen_error_message, 'type' => 'error');
								} 
							}
						}
					}	
				}	
			} else {
				if($row->gen_error_message) {
					$unsubscribe_message = array('msg' => $row->gen_error_message, 'type' => 'error');
				} else {
					$unsubscribe_message = array('msg' => __( "Unsubscribe link is invalid." , 'mwd-text' ), 'type' => 'error');
				}
			
			}
		}

		return $unsubscribe_message;
	}
	
	
	public function remove($group_id) {
		global $wpdb;
		$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_submits WHERE group_id= %d', $group_id));
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