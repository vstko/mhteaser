<?php

class MWDViewConditions {
	////////////////////////////////////////////////////////////////////////////////////////
	// Events                                                                             //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constants                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Variables                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	private $model;


	////////////////////////////////////////////////////////////////////////////////////////
	// Constructor & Destructor                                                           //
	////////////////////////////////////////////////////////////////////////////////////////
	public function __construct($model) {
		$this->model = $model;
	}
	////////////////////////////////////////////////////////////////////////////////////////
	// Public Methods                                                                     //
	////////////////////////////////////////////////////////////////////////////////////////
	public function display() {
		$form_id = $_REQUEST['form_id'];
		$params = $this->model->get_params($form_id, true);
		$ids = $params['ids'];
		$types = $params['types'];
		$labels = $params['labels'];
		$paramss = $params['paramss'];
		$all_ids = $params['all_ids'];
		$all_labels = $params['all_labels'];
	
		$cond_params = $this->model->get_cond_params($form_id);
		if(!$cond_params) {
			die();
		}

		$count_of_conditions = $cond_params['count_of_conditions'];
		$show_hide = $cond_params['show_hide'];
		$field_label = $cond_params['field_label'];
		$all_any = $cond_params['all_any'];
		$condition_params = $cond_params['condition_params'];

		$select_type_fields = array("type_country", "type_address", "type_checkbox", "type_radio", "type_own_select", "type_paypal_select", "type_paypal_checkbox", "type_paypal_radio", "type_paypal_shipping");

		for($k=0; $k<$count_of_conditions; $k++) {				
			if(in_array($field_label[$k],$all_ids)) : ?>
				<div id="condition<?php echo $k; ?>" class="mwd-condition">
					<div id="conditional_fileds<?php echo $k; ?>">
					<select id="show_hide<?php echo $k; ?>" name="show_hide<?php echo $k; ?>" class="show_hide" >
						<option value="1" <?php if($show_hide[$k]==1) echo 'selected="selected"'; ?>>show</option>
						<option value="0" <?php if($show_hide[$k]==0) echo 'selected="selected"'; ?>>hide</option>
					</select> 			
					<select id="fields<?php echo $k; ?>" name="fields<?php echo $k; ?>" class="select_field" >
						<?php 
							foreach($all_labels as $key => $value) { 	
								$selected = ($field_label[$k]==$all_ids[$key] ? 'selected="selected"' : '');
								echo '<option value="'.$all_ids[$key].'" '.$selected.'>'.$value.'</option>';
							}
						?>
					</select> 
					<span>if</span>			
					<select id="all_any<?php echo $k; ?>" name="all_any<?php echo $k; ?>" class="all_any" >
						<option value="and" <?php if($all_any[$k]=="and") echo 'selected="selected"'; ?>>all</option>
						<option value="or" <?php if($all_any[$k]=="or") echo 'selected="selected"'; ?>>any</option>
					</select> 
					<span>of the following match:</span>
					<img src="<?php echo MWD_URL . '/images/add.png?ver='. get_option("mwd_version").''; ?>" title="add" onclick="add_condition_fields(<?php echo $k; ?>)" class="add_condition_fields" style="cursor: pointer; vertical-align: middle;">
					<img src="<?php echo MWD_URL . '/images/page_delete.png?ver='. get_option("mwd_version").''; ?>" onclick="delete_condition(<?php echo $k; ?>)" class="delete_img" style="cursor: pointer; vertical-align: middle;">		
				</div>
				<?php 
				if($condition_params[$k]) {
					$_params = explode('*:*next_condition*:*',$condition_params[$k]);
					$_params = array_slice($_params,0, count($_params)-1); 
					foreach($_params as $key=>$_param)
					{
						$key_select_or_input = '';
						$param_values = explode('***',$_param);
						$multiselect = explode('@@@',$param_values[2]);

						if(in_array($param_values[0],$ids)): ?>
						<div id="condition_div<?php echo $k; ?>_<?php echo $key; ?>"  class="cond_fields">
							<select id="field_labels<?php echo $k; ?>_<?php echo $key; ?>" onchange="change_choices('<?php echo $k; ?>_<?php echo $key; ?>',this.value)" class="select_field">
								<?php 
									foreach($labels as $key1 => $value) 		
									{ 
										$selected = '';
										if($param_values[0]==$ids[$key1])
										{
											$selected = 'selected="selected"';
											$multiple = (($types[$key1]=="type_checkbox" || $types[$key1]=="type_paypal_checkbox") ? 'multiple="multiple"' : '');
											$key_select_or_input = $key1;
										}	
										echo $ids[$key1].' ';
										if($field_label[$k]!=$ids[$key1])
											echo '<option value="'.$ids[$key1].'" '.$selected.'>'.$value.'</option>';
									}
								?>	
							</select>
							<select id="is_select<?php echo $k; ?>_<?php echo $key; ?>" class="is_select">
								<option value="==" <?php if($param_values[1]=="==") echo 'selected="selected"'; ?>>is</option>
								<option value="!=" <?php if($param_values[1]=="!=") echo 'selected="selected"'; ?>>is not</option>
								<option value="%" <?php if($param_values[1]=="%") echo 'selected="selected"'; ?>>like</option>
								<option value="!%" <?php if($param_values[1]=="!%") echo 'selected="selected"'; ?>>not like</option>
								<option value="=" <?php if($param_values[1]=="=") echo 'selected="selected"'; ?>>empty</option>
								<option value="!" <?php if($param_values[1]=="!") echo 'selected="selected"'; ?>>not empty</option>
							</select>
							<div id="field_choices<?php echo $k; ?>_<?php echo $key; ?>" class="field_choices">
								<?php
									switch($types[$key_select_or_input])
									{
										case "type_text":
										case "type_password":
										case "type_textarea":
										case "type_name":
										case "type_number":
										case "type_phone":
										case "type_submitter_mail":
										case "type_paypal_price":
										case "type_spinner":
											$keypress_function ='';
											if($types[$key_select_or_input] == "type_number" || $types[$key_select_or_input] == "type_phone")
												$keypress_function = "return check_isnum_space(event)";
											else
												if($types[$key_select_or_input]=="type_paypal_price")
													$keypress_function = "return check_isnum_point(event)";
													
											echo '<input id="field_value'.$k.'_'.$key.'" type="text" value="'. $param_values[2].'" onkeypress="'.$keypress_function.'" class="field_value_input" >';
										break;
										case "type_address":
											$w_countries = array("","Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");

											echo '<select id="field_value'.$k.'_'.$key.'" class="field_value_select">';		
												foreach($w_countries as $choise)
												{
													$selected =(in_array($choise, $multiselect) ? 'selected="selected"' : '');									
													echo '<option value="'.$choise.'" '.$selected.'>'.$choise.'</option>';	
												}	
											echo '</select>';	
										break;
										case "type_country":
											$temp = $paramss[$key_select_or_input];
											$temp = explode('*:*w_size*:*',$temp);
											$temp = explode('*:*w_countries*:*',$temp[1]);
											$w_countries =  explode('***',$temp[0]);
											echo '<select id="field_value'.$k.'_'.$key.'" class="field_value_select">';		
												foreach($w_countries as $choise)
												{
													$selected =(in_array($choise, $multiselect) ? 'selected="selected"' : '');	
													echo '<option value="'.$choise.'" '.$selected.'>'.$choise.'</option>';
												}	
											echo '</select>';	
										break;
										case "type_radio":
										case "type_own_select":
											$temp = $paramss[$key_select_or_input];
											$exp_par = $types[$key_select_or_input]== 'type_radio' ? '*:*w_flow*:*' : '*:*w_size*:*';
											$temp = explode($exp_par, $temp);
											$temp = explode('*:*w_choices*:*',$temp[1]);
											$param['w_choices'] = $temp[0];
											$param['w_choices']	= explode('***',$param['w_choices']);
											
											if(strpos($temp[1], 'w_value_disabled') > -1) {
												$temp = explode('*:*w_value_disabled*:*',$temp[1]);
												$temp = explode('*:*w_choices_value*:*',$temp[1]);
												$param['w_choices_value'] = $temp[0];
											}
										
											if(isset($param['w_choices_value']))
												$param['w_choices_value'] = explode('***',$param['w_choices_value']);
											else
												$param['w_choices_value'] = $param['w_choices'];
											echo '<select id="field_value'.$k.'_'.$key.'" class="field_value_select">';
											foreach($param['w_choices'] as $key1=>$choise_label){
												$selected = (in_array($param['w_choices_value'][$key1], $multiselect) ? 'selected="selected"' : '');
												echo '<option value="'.$param['w_choices_value'][$key1].'" '.$selected.'>'.$choise_label.'</option>';
											}	
											echo '</select>';	
										break;
										
										case "type_checkbox":
											$temp = $paramss[$key_select_or_input];
											$temp = explode('*:*w_flow*:*',$temp);
											$temp = explode('*:*w_choices*:*',$temp[1]);
											$param['w_choices'] = $temp[0];
											$param['w_choices']	= explode('***',$param['w_choices']);	
										
											if(strpos($temp[1], 'w_value_disabled') > -1) {
												$temp= explode('*:*w_value_disabled*:*',$temp[1]);
												$temp = explode('*:*w_choices_value*:*',$temp[1]);
												$param['w_choices_value'] = $temp[0];
											}
										
											if(isset($param['w_choices_value']))
												$param['w_choices_value'] = explode('***',$param['w_choices_value']);
											else
												$param['w_choices_value'] = $param['w_choices'];

											echo '<select id="field_value'.$k.'_'.$key.'" class="field_value_select" multiple="multiple">';											
											foreach($param['w_choices'] as $key1=>$choise_label){
												$selected = (in_array($param['w_choices_value'][$key1], $multiselect) ? 'selected="selected"' : '');
												echo '<option value="'.$param['w_choices_value'][$key1].'" '.$selected.'>'.$choise_label.'</option>';	
											}	
											echo '</select>';				
										break;
										case "type_action":
										case "type_list":
											$temp = $paramss[$key_select_or_input];
											$temp = explode('*:*w_flow*:*',$temp);
											$temp = explode('*:*w_choices*:*',$temp[1]);
											
											$param['w_choices'] = $temp[0];
											$param['w_choices']	= explode('***',$param['w_choices']);	
										
											$temp = explode('*:*w_choices_checked*:*',$temp[1]);
											$temp = explode('*:*w_choices_value*:*',$temp[1]);
											$param['w_choices_value'] = $temp[0];
											$param['w_choices_value'] = explode('***',$param['w_choices_value']);
											
											$temp = explode('*:*w_type*:*', $temp[1]);
											$param['w_type'] = $temp[0];

											$multiple = $param['w_type'] == 'checkbox' ? 'multiple="multiple"' : '';	
											echo '<select id="field_value'.$k.'_'.$key.'" class="field_value_select" '.$multiple.'>';		
												foreach($param['w_choices'] as $key1=>$choise_label) {
													$selected = (in_array($param['w_choices_value'][$key1], $multiselect) ? 'selected="selected"' : '');
													
													echo '<option value="'.$param['w_choices_value'][$key1].'" '.$selected.' >'.$choise_label.'</option>';	
												}	
											echo '</select>';				
										break;
										
										case "type_paypal_select":
											$temp = $paramss[$key_select_or_input];
											$temp = explode('*:*w_size*:*',$temp);
											$temp = explode('*:*w_choices*:*',$temp[1]);
											$param['w_choices'] = $temp[0];
											$param['w_choices']	= explode('***',$param['w_choices']);	
										
											$temp = explode('*:*w_choices_price*:*',$temp[1]);
											$param['w_choices_value'] = $temp[0];
											$param['w_choices_value'] = explode('***',$param['w_choices_value']);
										
											echo '<select id="field_value'.$k.'_'.$key.'" class="field_value_select">';	
												foreach($param['w_choices'] as $key1=>$choise_label) {
													$selected = (in_array($param['w_choices_value'][$key1], $multiselect) ? 'selected="selected"' : '');
													echo '<option value="'.$param['w_choices_value'][$key1].'" '.$selected.'>'.$choise_label.'</option>';	
												}	
											echo '</select>';				
										break;
										
										case "type_paypal_checkbox":
										case "type_paypal_radio":
										case "type_paypal_shipping":
											$temp = $paramss[$key_select_or_input];
											$temp = explode('*:*w_flow*:*',$temp);
											$temp = explode('*:*w_choices*:*',$temp[1]);
											$param['w_choices'] = $temp[0];
											$temp = explode('*:*w_choices_price*:*',$temp[1]);
											$param['w_choices_price'] = $temp[0];
											$param['w_choices']	= explode('***',$param['w_choices']);
											$param['w_choices_price'] = explode('***',$param['w_choices_price']);

											$multiple = ($types[$key_select_or_input]=='type_paypal_checkbox' ? 'multiple="multiple"' : '');	
											echo '<select id="field_value'.$k.'_'.$key.'" class="field_value_select" '.$multiple.'>';	
											
											foreach($param['w_choices'] as $key1=>$choise_label)
											{
												$choise_value = ($types[$key_select_or_input]=='type_paypal_checkbox' ? $choise_label.'*:*value*:*'.$param['w_choices_price'][$key1] : $param['w_choices_price'][$key1]);
												$selected = (in_array($choise_value, $multiselect) ? 'selected="selected"' : '');

												if(strpos($choise_label, '[') === false && strpos($choise_label, ']') === false && strpos($choise_label, ':') === false)
													echo '<option value="'.$choise_value.'" '.$selected.'>'.$choise_label.'</option>';	
											}	
											echo '</select>';			
										break;			
									}
								?>
							</div>
							<img src="<?php echo MWD_URL . '/images/delete.png?ver='. get_option("mwd_version").''; ?>" id="delete_condition<?php echo $k.'_'.$key; ?>" onclick="delete_field_condition(&quot;<?php echo $k.'_'.$key; ?>&quot;)" class="delete_condition" style="vertical-align: middle; cursor: pointer;">
						</div>
						<?php endif;
					}
				}
				?>
			</div>
			<?php endif;
		}
		die();
	}
	
	public function add_condition() {
		$form_id = $_REQUEST['form_id'];
		$cond_index = $_REQUEST['cond_index'];
		$params = $this->model->get_params($form_id);
		$ids = $params['ids'];
		$types = $params['types'];
		$labels = $params['labels'];
		?>
		<div id="conditional_fileds<?php echo $cond_index; ?>">
			<select id="show_hide<?php echo $cond_index; ?>" name="show_hide<?php echo $cond_index; ?>" class="show_hide">
				<option value="1">show</option>
				<option value="0">hide</option>
			</select>
			<select id="fields<?php echo $cond_index; ?>" name="fields<?php echo $cond_index; ?>" class="select_field">
				<?php 
					foreach($ids as $key => $field_id){
						echo '<option value="'.$field_id.'">'.$labels[$key].'</option>';
					}
				?>
			</select>
			<span>if</span>        
			<select id="all_any<?php echo $cond_index; ?>" name="all_any<?php echo $cond_index; ?>" class="all_any">
				<option value="and">all</option>
				<option value="or">any</option>
			</select>
			<span>of the following match:</span> 
			<img src="<?php echo MWD_URL . '/images/add.png?ver='. get_option("mwd_version").''; ?>" title="add" onclick="add_condition_fields(<?php echo $cond_index; ?>)" class="add_condition_fields" style="cursor: pointer; vertical-align: middle;">
			<img src="<?php echo MWD_URL . '/images/page_delete.png?ver='. get_option("mwd_version").''; ?>" onclick="delete_condition(<?php echo $cond_index; ?>)" class="delete_img" style="cursor: pointer; vertical-align: middle;">
		</div>
		<?php
		die();
	}
	
	public function add_condition_fields() {
		$form_id = $_REQUEST['form_id'];
		$cond_index = $_REQUEST['cond_index'];
		$cond_fieldindex = $_REQUEST['cond_fieldindex'];
		$cond_fieldid = $_REQUEST['cond_fieldid'];
		$params = $this->model->get_params($form_id, true);

		$ids = $params['ids'];
		$types = $params['types'];
		$labels = $params['labels'];
		
		$first_id = (!empty($ids) ? ($cond_fieldid != $ids[0] ? $ids[0] : (isset($ids[1]) ? $ids[1] : -1)) : -1);
		?>
		<script>
		if(<?php echo $first_id; ?> > 0){
			change_choices('<?php echo $cond_index.'_'.$cond_fieldindex; ?>', <?php echo $first_id; ?>);
		}
		</script>
		<?php
		if(!empty($ids)){ ?>
			<select id="field_labels<?php echo $cond_index.'_'.$cond_fieldindex; ?>" onchange="change_choices('<?php echo $cond_index.'_'.$cond_fieldindex; ?>',this.value)" class="select_field">
			<?php 
				foreach($ids as $key => $field_id){
					if($field_id != $cond_fieldid){
						echo '<option value="'.$field_id.'">'.$labels[$key].'</option>';
					}
				}
			?>		
			</select>
			<select id="is_select<?php echo $cond_index.'_'.$cond_fieldindex; ?>" class="is_select" >
				<option value="==">is</option>
				<option value="!=">is not</option>
				<option value="%">like</option>
				<option value="!%">not like</option>
				<option value="=">empty</option>
				<option value="!">not empty</option>
			</select>
			<div id="field_choices<?php echo $cond_index.'_'.$cond_fieldindex; ?>" class="field_choices">
			</div>	
			<img src="<?php echo MWD_URL . '/images/delete.png?ver='. get_option("mwd_version").''; ?>" id="delete_condition<?php echo $cond_index.'_'.$cond_fieldindex; ?>" onclick="delete_field_condition(&quot;<?php echo $cond_index.'_'.$cond_fieldindex; ?>&quot;)" class="delete_condition" style="vertical-align: middle; cursor: pointer;">
		<?php }
		die();
	}
	
	public function change_choices() {
		$form_id = $_REQUEST['form_id'];
		$num = $_REQUEST['num'];
		$field_id = $_REQUEST['field_id'];
		$params = $this->model->get_params($form_id);
		
		$ids = $params['ids'];
		$types = $params['types'];
		$labels = $params['labels'];
		$paramss = $params['paramss'];

		$key = array_search($field_id, $ids);		
		switch($types[$key])
		{
			case "type_text":
			case "type_password":
			case "type_textarea":
			case "type_name":
			case "type_number":
			case "type_phone":
			case "type_submitter_mail":
			case "type_paypal_price":
			case "type_spinner":
				$keypress_function ='';
				if($types[$key] == "type_number" || $types[$key] == "type_phone")
					$keypress_function = "return check_isnum_space(event)";
				else
					if($types[$key] == "type_paypal_price")
						$keypress_function = "return check_isnum_point(event)";

				echo '<input id="field_value'.$num.'" type="text" value="" onkeypress="'.$keypress_function.'" class="field_value_input">';
			break;
			
			case "type_address":
				$w_countries = array("","Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");
				echo '<select id="field_value'.$num.'" class="field_value_select">';		
					foreach($w_countries as $choise)
						echo '<option value="'.$choise.'" >'.$choise.'</option>';	
				echo '</select>';		
			break;
			case "type_country":
				$temp = $paramss[$key];
				$temp = explode('*:*w_size*:*',$temp);
				$temp = explode('*:*w_countries*:*',$temp[1]);
				$w_countries =  explode('***',$temp[0]);
				echo '<select id="field_value'.$num.'" class="field_value_select">';		
					foreach($w_countries as $choise)
						echo '<option value="'.$choise.'" >'.$choise.'</option>';	
				echo '</select>';	
			break;
			case "type_radio":
			case "type_own_select":
				$temp = $paramss[$key];
				
				$exp_par = $types[$key] == 'type_radio' ? '*:*w_flow*:*' : '*:*w_size*:*';
				$temp = explode($exp_par, $temp);
				$temp = explode('*:*w_choices*:*',$temp[1]);
				$param['w_choices'] = $temp[0];
				$param['w_choices']	= explode('***',$param['w_choices']);
				
				if(strpos($temp[1], 'w_value_disabled') > -1) {
					$temp = explode('*:*w_value_disabled*:*',$temp[1]);
					$temp = explode('*:*w_choices_value*:*',$temp[1]);
					$param['w_choices_value'] = $temp[0];
				}
			
				if(isset($param['w_choices_value']))
					$param['w_choices_value'] = explode('***',$param['w_choices_value']);
				else
					$param['w_choices_value'] = $param['w_choices'];
			
				echo '<select id="field_value'.$k.'_'.$key.'" class="field_value_select">';
				foreach($param['w_choices'] as $key1=>$choise_label) {
					echo '<option value="'.$param['w_choices_value'][$key1].'">'.$choise_label.'</option>';
				}	
				echo '</select>';	
			break;
			
			case "type_checkbox":
				$temp = $paramss[$key];
				$temp = explode('*:*w_flow*:*',$temp);
				$temp = explode('*:*w_choices*:*',$temp[1]);
				$param['w_choices'] = $temp[0];
				$param['w_choices']	= explode('***',$param['w_choices']);	
			
				if(strpos($temp[1], 'w_value_disabled') > -1) {
					$temp= explode('*:*w_value_disabled*:*',$temp[1]);
					$temp = explode('*:*w_choices_value*:*',$temp[1]);
					$param['w_choices_value'] = $temp[0];
				}
			
				if(isset($param['w_choices_value']))
					$param['w_choices_value'] = explode('***',$param['w_choices_value']);
				else
					$param['w_choices_value'] = $param['w_choices'];
				
			
				$multiple = 'multiple="multiple"';	
				echo '<select id="field_value'.$num.'" class="field_value_select" '.$multiple.'>';		
					foreach($param['w_choices'] as $key1=>$choise_label) {
						echo '<option value="'.$param['w_choices_value'][$key1].'" >'.$choise_label.'</option>';	
					}	
				echo '</select>';				
			break;
			
			case "type_action":
			case "type_list":
				$temp = $paramss[$key];
				$temp = explode('*:*w_flow*:*',$temp);
				$temp = explode('*:*w_choices*:*',$temp[1]);
				
				$param['w_choices'] = $temp[0];
				$param['w_choices']	= explode('***',$param['w_choices']);	
			
				$temp = explode('*:*w_choices_checked*:*',$temp[1]);
				$temp = explode('*:*w_choices_value*:*',$temp[1]);
				$param['w_choices_value'] = $temp[0];
				$param['w_choices_value'] = explode('***',$param['w_choices_value']);
				
				$temp = explode('*:*w_type*:*', $temp[1]);
				$param['w_type'] = $temp[0];

				$multiple = $param['w_type'] == 'checkbox' ? 'multiple="multiple"' : '';	
				echo '<select id="field_value'.$num.'" class="field_value_select" '.$multiple.'>';		
					foreach($param['w_choices'] as $key1=>$choise_label) {
						echo '<option value="'.$param['w_choices_value'][$key1].'" >'.$choise_label.'</option>';	
					}	
				echo '</select>';				
			break;
			
			case "type_paypal_select":
				$temp = $paramss[$key];
				$temp = explode('*:*w_size*:*',$temp);
				$temp = explode('*:*w_choices*:*',$temp[1]);
				$param['w_choices'] = $temp[0];
				$param['w_choices']	= explode('***',$param['w_choices']);	
			
				$temp = explode('*:*w_choices_price*:*',$temp[1]);
				$param['w_choices_value'] = $temp[0];
				$param['w_choices_value'] = explode('***',$param['w_choices_value']);
			
				echo '<select id="field_value'.$num.'" class="field_value_select">';		
					foreach($param['w_choices'] as $key1=>$choise_label) {
						echo '<option value="'.$param['w_choices_value'][$key1].'" >'.$choise_label.'</option>';	
					}	
				echo '</select>';				
			break;
			case "type_paypal_checkbox":
			case "type_paypal_radio":
			case "type_paypal_shipping":
				$temp = $paramss[$key];
				$temp = explode('*:*w_flow*:*',$temp);
				$temp = explode('*:*w_choices*:*',$temp[1]);
				$param['w_choices'] = $temp[0];
				$temp= explode('*:*w_choices_price*:*',$temp[1]);
				$param['w_choices_price'] = $temp[0];

				$param['w_choices']	= explode('***',$param['w_choices']);
				$param['w_choices_price'] = explode('***',$param['w_choices_price']);

				$multiple = ($types[$key]=='type_paypal_checkbox' ? 'multiple="multiple"' : '');	
				echo '<select id="field_value'.$num.'" class="field_value_select" '.$multiple.'>';		
					foreach($param['w_choices'] as $key1=>$choise_label)
					{
						$choise_value = ($types[$key]=='type_paypal_checkbox' ? $choise_label.'*:*value*:*'.$param['w_choices_price'][$key1] : $param['w_choices_price'][$key1]);		
						echo '<option value="'.$choise_value.'" >'.$choise_label.'</option>';	
					}	
				echo '</select>';			
			break;			
		}
		die();	
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