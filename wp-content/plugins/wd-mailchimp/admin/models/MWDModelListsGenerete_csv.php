<?php

class MWDModelListsGenerete_csv {
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
	public function get_data($list_id) {
		$list_data = $this->get_list_data( $list_id );
		$list_name = $list_data['list_name'];	
		$merge_vars = $list_data['merge_vars'];	
		$interest_groups = $list_data['interest_groups'];	
		$user_data = $list_data['user_data'];	

		$additional_keys = array('email_type' => 'EMAIL_TYPE', 'member_rating' => 'MEMBER_RATING','timestamp_opt' => 'OPTIN_TIME','ip_opt' => 'OPTIN_IP','timestamp_signup' => 'CONFIRM_TIME','ip_signup' => 'CONFIRM_IP','latitude' => 'LATITUDE','longitude' => 'LONGITUDE','gmtoff' => 'GMTOFF','dstoff' => 'DSTOFF','timezone' => 'TIMEZONE','cc' => 'CC','region' => 'REGION','info_changed' => 'LAST_CHANGED','leid' => 'LEID','euid' => 'EUID','notes' => 'NOTES');
		$additional_values = array('email_type', 'member_rating', 'timestamp_opt', 'ip_opt', 'timestamp_signup', 'ip_signup', 
		'custom', 'info_changed', 'leid', 'euid', 'notes');
	
		if(count($list_data['merge_vars'])){
			$data_keys = array();
			foreach($merge_vars as $merge_key => $merge_var) {
				$data_keys[$merge_var['tag']] = $merge_var['name'];
			}

			if(is_array($interest_groups)) {
				foreach($interest_groups as $interest_group_key => $interest_group) {
					$data_keys[$interest_group['id']] = $interest_group['name'];
				}
			}
		}
		
		
		$data_keys = $data_keys + $additional_keys;

		$data = array();
		$GROUPINGS = array();
		if(isset($user_data) && $user_data['total']){
			foreach($user_data['data'] as $user_key => $user) {
				if(isset($user['merges']['GROUPINGS'])){
					$GROUPINGS = $user['merges']['GROUPINGS'];
					unset($user['merges']['GROUPINGS']);
				}
				if($GROUPINGS) {
					foreach($GROUPINGS as $group){
						$user['merges'][$group['id']] =  $group['name'];
					}
				}
				
				foreach($additional_values as $additional_value){
					if($additional_value == 'custom'){
						$user['merges']['latitude'] = isset( $user['geo']) && isset($user['geo']['latitude']) ? $user['geo']['latitude'] : '';
						$user['merges']['longitude'] = isset( $user['geo']) && isset($user['geo']['longitude']) ? $user['geo']['longitude'] : '';
						$user['merges']['gmtoff'] = isset( $user['geo']) && isset($user['geo']['gmtoff']) ? $user['geo']['gmtoff'] : '';
						$user['merges']['dstoff'] = isset( $user['geo']) && isset($user['geo']['dstoff']) ? $user['geo']['dstoff'] : '';
						$user['merges']['timezone'] = isset( $user['geo']) && isset($user['geo']['timezone']) ? $user['geo']['timezone'] : '';
						$user['merges']['cc'] =  isset( $user['geo']) && isset($user['geo']['cc']) ? $user['geo']['cc'] : '';	
						$user['merges']['region'] =  isset( $user['geo']) && isset($user['geo']['region']) ? $user['geo']['region'] : '';
					} else{
						if($additional_value == 'notes'){
							if($user['notes']){
								$user['notes'] = array_map(function($n) { return $n['created'] .' '. $n['note']; }, $user['notes']);
								$user['notes'] = implode(' ', $user['notes']);
							}	
							else{
								$user['notes'] = '';
							}
						}
						$user['merges'][$additional_value] = $user[$additional_value];
					}
				}

				$data[] = $user['merges'];
			}
		}
	
		return array($data_keys, $data, $list_name);	
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
		
		$sort_dir = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		/*$start = isset($_POST['page_number']) && $_POST['page_number'] ? ((int) $_POST['page_number']-1) * 20 : 0; */
		
		$mwd_user_data = $api->call('lists/members', array( 'id' =>	$list_id, 'opts' =>	array(/* 'start' => $start, 'limit'=> 20,  */'sort_field' => 'email', 'sort_dir' => $sort_dir )));
		
		$all_data['list_name'] = $mwd_list['name'];
		$all_data['merge_vars'] = $mwd_merge_vars;
		$all_data['interest_groups'] = $mwd_interest_groups;
		$all_data['user_data'] = $mwd_user_data;
		return $all_data;
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