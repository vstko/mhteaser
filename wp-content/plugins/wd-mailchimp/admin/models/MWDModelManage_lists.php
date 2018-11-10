<?php

class MWDModelManage_lists {
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
	public function get_lists() {
		$this->clear_mailchimp_api_cache();
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		
		$sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$sort_field_array = array('id', 'name', 'member', 'created');
		$sort_field = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $sort_field_array) ? esc_html(stripslashes($_POST['order_by'])) : 'id';
		$start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1) : 0;
		
		$mwd_lists = $api->call( 'lists/list' , array( 'apikey' => $apikey, 'filters' => array(), 'start' => $start, 'limit' => 20, 'sort_field' => $sort_field, 'sort_dir' => $sort_dir) );
		
		return $mwd_lists;
	}
	
	public function get_lists_search() {
		$this->clear_mailchimp_api_cache();
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		
		$list_name_search = (isset($_POST['search_value']) && (esc_html($_POST['search_value']) != '')) ? esc_html($_POST['search_value']) : '';
		$sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$sort_field_array = array('id', 'name', 'member', 'created');
		$sort_field = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $sort_field_array) ? esc_html(stripslashes($_POST['order_by'])) : 'id';
		$start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1)  : 0;
		
		$mwd_lists = $api->call( 'lists/list' , array( 'apikey' => $apikey, 'filters' => array('list_name' => $list_name_search), 'start' => $start, 'sort_field' => $sort_field, 'sort_dir' => $sort_dir) );
		return $mwd_lists;
	}
	
	public function get_list_data( $list_id ) {
		$this->clear_mailchimp_api_cache();
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		
		$all_data = array();
		$mwd_list = $api->call( 'lists/list' , array( 'apikey' => $apikey, 'filters' => array('list_id' => $list_id)) );
		$mwd_list = $mwd_list['data'][0];
		
		$mwd_merge_vars = $api->call( 'lists/merge-vars' , array( 'apikey' => $apikey , 'id' => array( $list_id ) ) );
		$mwd_merge_vars = $mwd_merge_vars['data'][0]['merge_vars'];
		
		try {
			$mwd_interest_groups = $api->call( 'lists/interest-groupings' , array( 'apikey' => $apikey , 'id' => $list_id , 'counts' => true ) );
		} catch( Exception $e ) {
			$mwd_interest_groups = $e->getMessage();
		}
		
		try {
			$mwd_segments = $api->call( 'lists/segments' , array( 'apikey' => $apikey , 'id' => $list_id , 'type' => 'saved' ) );
		} catch( Exception $segment_error ) {
			$mwd_segments = $e->getMessage();
		}
		
		$all_data['list'] = $mwd_list;
		$all_data['merge_vars'] = $mwd_merge_vars;
		$all_data['interest_groups'] = $mwd_interest_groups;
		$all_data['segments'] = $mwd_segments;
		return $all_data;
	}
			
	public function get_subscribers_list( $list_id ) {
		$this->clear_mailchimp_api_cache();
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		
		$sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1) : 0;
		
		$subscribers_list = $api->call('lists/members', array( 'id'	=>	$list_id, 'opts' =>	array('start' => $start, 'limit'=> 20, 'sort_field' => 'email', 'sort_dir'	=> $sort_dir )));
		
		return $subscribers_list;
	}
	
	public function get_subscribers_list_search( $list_id ) {
		$this->clear_mailchimp_api_cache();
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		
		$email_search = (isset($_POST['search_value']) && (esc_html($_POST['search_value']) != '')) ? esc_html($_POST['search_value']) : '';
		$search_list = array();
		if( $email_search ) {
			$search_list = $api->call('helper/search-members', array( 'apikey'	=>	$apikey, 'query' =>	'email:'.$email_search, 'id' => $list_id));
			
			$subscribers_list = array();
			$search_data = $search_list['full_search'];
			$subscribers_list['total'] = $search_data['total'];
			$subscribers_list['data'] = $search_data['members'];
	
		} else {
			$sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
			$sort_field_array = array('email', 'last_update_time');
			$sort_field = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $sort_field_array) ? esc_html(stripslashes($_POST['order_by'])) : 'email';
			$start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1)  : 0;
			$subscribers_list = $api->call('lists/members', array( 'id'	=>	$list_id, 'opts' =>	array('start' => $start, 'limit'=> 20, 'sort_field' => $sort_field, 'sort_dir'	=> $sort_dir )));

			/*$subscribers_list_data = $subscribers_list['data'];
			 if ($subscribers_list['total'] && $email_search) {
				$emails_list = array_map(function($member) { return $member['email']; }, $subscribers_list['data']);
				$subscribers = array_filter( array_map( function($key, $value) use ($email_search, $subscribers_list_data) { return strpos($value, $email_search) > -1 ? $subscribers_list_data[$key] : ''; }, array_keys($emails_list), $emails_list ) );
		
				$subscribers_list['total'] = count($subscribers);
				$subscribers_list['data'] = $subscribers;
			} */
				
		}

		return $subscribers_list;
	}
	
	public function get_user_data( $sub_id, $list_id ) {
		$this->clear_mailchimp_api_cache();
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		$user_data = array();
		try {
			$user_data = $api->call( 'lists/member-info' , array( 'apikey' => $apikey, 'id' => $list_id, 'emails' => array( array( 'leid' => $sub_id ) ) ) );
		} catch (Exception $e) {
			echo '<h4>Error: '. $e->getMessage().'</h4>';
			return;
		}
		return $user_data;
	}
	
	public function unsubscribe() {
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		
		$list_id = MWD_Library::get('list_id', 0);
		$sub_id = MWD_Library::get('sub_id', 0);
		
		try {
			$unsubscribe_user = $api->call( 'lists/unsubscribe' , array( 'apikey' => $apikey, 'id' => $list_id, 'email' => array( 'leid' => $sub_id ), 'send_goodbye' => false, 'send_notify' => false ) );
			$this->clear_mailchimp_api_cache();
			MWD_Library::mwd_redirect(add_query_arg(array('list_id' => $list_id, 'message' => '9'), admin_url('admin.php?page=manage_lists&task=view')));
		} catch ( Exception $e ) {
			$error_response = $e->getMessage();
			if ( strpos( $error_response, 'is not subscribed' ) !== false ) {
				MWD_Library::mwd_redirect(add_query_arg(array('list_id' => $list_id, 'message' => '13'), admin_url('admin.php?page=manage_lists&task=view')));
			}	
			else{
				MWD_Library::mwd_redirect(add_query_arg(array('list_id' => $list_id, 'message' => '12'), admin_url('admin.php?page=manage_lists&task=view')));
			}
		}	
		
	}
	
	public function clear_mailchimp_api_cache() {
		delete_transient('mwd-list-info');
		delete_transient('mwd-profile-info');
		delete_transient('mwd-account-details');
		delete_transient('mwd-lists');
		delete_transient('mwd-subscribers-data');
		delete_transient('mwd-list-data');
		delete_transient('mwd-subscriber-data');
	}
	
	public function page_nav( $call_name, $list_id = 0 ) {
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		
		$list_name_search = (isset($_POST['search_value']) && (esc_html($_POST['search_value']) != '')) ? esc_html($_POST['search_value']) : '';
		try {
			switch($call_name) {
				case 'lists':
					$data = $this->get_lists_search();
				break;
				case 'subscribers':
					$data = $this->get_subscribers_list_search( $list_id );
				break;
			}
			
		} catch (Exception $e) {
			//echo '<h4>Error: '. $e->getMessage().'</h4>';
			return;
		}

		$page_nav['total'] = $data['total'];
		if (isset($_POST['page_number']) && $_POST['page_number']) {
			$limit = ((int) $_POST['page_number'] - 1) * 20;
		}
		else {
			$limit = 0;
		}
		$page_nav['limit'] = (int) ($limit / 20 + 1);
		return $page_nav;
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