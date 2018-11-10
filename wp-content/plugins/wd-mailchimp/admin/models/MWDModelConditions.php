<?php
class MWDModelConditions {
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

	public function get_params($form_id, $rule_field = false) {
		global $wpdb;
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "mwd_forms WHERE id= %d", $form_id));
		$ids = array();
		$types = array();
		$labels = array();
		$paramss = array();
		$all_ids = array();
		$all_labels = array();
	
		$fields = explode('*:*new_field*:*',$row->form_fields);
		$fields = array_slice($fields,0, count($fields)-1);   
		foreach($fields as $field)
		{
			$temp = explode('*:*id*:*',$field);
			array_push($ids, $temp[0]);
			array_push($all_ids, $temp[0]);
			$temp = explode('*:*type*:*',$temp[1]);
			array_push($types, $temp[0]);
			$temp = explode('*:*w_field_label*:*',$temp[1]);
			array_push($labels, $temp[0]);
			array_push($all_labels, $temp[0]);
			array_push($paramss, $temp[1]);
		}
		
		if($rule_field){
			$select_and_input = array("type_text", "type_password", "type_textarea", "type_name", "type_number", "type_phone", "type_submitter_mail", "type_address", "type_spinner", "type_country", "type_checkbox", "type_radio", "type_own_select", "type_paypal_price", "type_paypal_select", "type_paypal_checkbox", "type_paypal_radio", "type_paypal_shipping","type_action","type_list");
			
			foreach($types as $key=>$value) {
				if(!in_array($types[$key],$select_and_input)) {					
					unset($ids[$key]);						
					unset($labels[$key]);					
					unset($types[$key]);
					unset($paramss[$key]);					
				}
			}
			
			$ids = array_values($ids);
			$labels = array_values($labels);
			$types = array_values($types);
			$paramss = array_values($paramss);
		}

		$params = array();
		$params['ids'] = $ids;
		$params['all_ids'] = $all_ids;
		$params['types'] = $types;
		$params['labels'] = $labels;
		$params['all_labels'] = $all_labels;
		$params['paramss'] = $paramss;

		return $params;		
	}
	
	public function get_cond_params($form_id) {
		global $wpdb;
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "mwd_forms WHERE id= %d", $form_id));
		
		$show_hide = array();
		$field_label = array();
		$all_any = array();
		$condition_params = array();

		$count_of_conditions=0;	
		if($row->condition == ""){
			return array();
		}	
	
		$conditions = explode('*:*new_condition*:*',$row->condition);
		$conditions = array_slice($conditions,0, count($conditions)-1); 
		$count_of_conditions = count($conditions);					
		foreach($conditions as $condition)
		{
			$temp = explode('*:*show_hide*:*',$condition);
			array_push($show_hide, $temp[0]);
			$temp = explode('*:*field_label*:*',$temp[1]);
			array_push($field_label, $temp[0]);
			$temp = explode('*:*all_any*:*',$temp[1]);
			array_push($all_any, $temp[0]);
			array_push($condition_params, $temp[1]);
		}
		
		$params = array();
		$params['count_of_conditions'] = $count_of_conditions;
		$params['show_hide'] = $show_hide;
		$params['field_label'] = $field_label;
		$params['all_any'] = $all_any;
		$params['condition_params'] = $condition_params;

		return $params;		
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