<?php

class MWDModelManage_mwd {
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
	public function mwd_lists() {
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
    require_once MWD_DIR . "/admin/models/MWDModelHelper.php";
    $model = new MWDModelHelper();
    $model->mwd_validate_api($apikey);
    if (get_option('mwd_api_validation') == 'invalid_apikey') {
      $mchlists = null;
      return;
    }
		$mchlists = $api->call( 'lists/list' , array( 'apikey' => $apikey) );
	
		$lists = array();
		if ( is_array($mchlists) && $mchlists["total"]) {
			foreach ( $mchlists['data'] as $list ) {
				$lists["{$list['id']}"] = (object) array(
					'id' => $list['id'],
					'name' => $list['name'],
					'member_count' => $list['stats']['member_count'],
					'merge_vars' => array(),
					'interest_groups' => array()
				);
				
				try {
					$merge_variables = $api->call( 'lists/merge-vars' , array( 'apikey' => $apikey , 'id' => array($list['id'])));
				} catch (Exception $e) {
					return;
				}
				$merge_vars["{$list['id']}"] = $merge_variables['data'][0]['merge_vars'];
				
				$interest_groups = array();
				try {
					$interest_groups = $api->call( 'lists/interest-groupings' , array( 'apikey' => $apikey , 'id' => $list['id'] , 'counts' => true ) );
				} catch( Exception $e ) {
				}	
				
				$lists["{$list['id']}"]->interest_groups = $interest_groups;
			}

			if($merge_vars)
				foreach ($merge_vars as $list_id => $merge_var) {
					$lists["{$list_id}"]->merge_vars = array_map( array( $this, 'merge_vars' ), $merge_var );
				}
		}

		return $lists;
	}
	
	public function merge_vars( $merge_var ) {
		$array = array(
			'name' => $merge_var['name'],
			'field_type' => $merge_var['field_type'],
			'req' => $merge_var['req'],
			'tag' => $merge_var['tag'],
		);

		if ( isset( $merge_var->choices ) ) {
			$array['choices'] = $merge_var['choices'];
		}

		return (object) $array;
	}	
	
	public function mwd_profile_info() {
		$apikey = get_option( 'mwd_api_key', '' );
		$api = new Mailchimp( $apikey );
		try {
			$profile_info = $api->call( '/users/profile' , array( 'apikey' => $apikey ) );
		} catch (Exception $e) {
			//echo '<h4>Error: '. $e->getMessage().'</h4>';
			return;
		}

		return $profile_info; 
	}
	
	public function mwd_account_details() {
		$apikey = get_option( 'mwd_api_key', '' );
		$api = new Mailchimp( $apikey );
		try {
			$account_details = $api->call( '/helper/account-details' , array( 'apikey' => $apikey ) );
		} catch (Exception $e) {
			//echo '<h4>Error: '. $e->getMessage().'</h4>';
			return;
		}
		
		return $account_details; 
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