<?php
class MWDViewManage_forms {
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
		if(get_option('mwd_api_validation') != 'valid_apikey') {
			echo MWD_Library::message("You need to connect to MailChimp before you can start creating forms. Head over to the <a href='".add_query_arg(array('page' => 'manage_mwd'), admin_url('admin.php'))."'>MailChimp WD</a> and enter your API key.", 'error');
			die();
		}
		
		$rows_data = $this->model->get_rows_data();
		$page_nav = $this->model->page_nav();
		$search_value = ((isset($_POST['search_value'])) ? esc_html($_POST['search_value']) : '');
		$search_type = ((isset($_POST['search_select_value_type'])) ?  $_POST['search_select_value_type'] : '0');
		$search_list = ((isset($_POST['search_select_value_list'])) ?  $_POST['search_select_value_list'] : '0');
		$asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$order_by_array = array('id', 'title', 'mail');
		$order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'id';
		$order_class = 'manage-column column-title sorted ' . $asc_or_desc;
		$ids_string = '';
		$form_types = array('0' => 'Select Type', 'embedded' => 'embedded', 'popover' => 'popover', 'topbar' => 'topbar', 'scrollbox' => 'scrollbox');
		$lists = $this->model->get_lists();
		
		if(!$lists['total']){
			echo MWD_Library::message("Currently you don't have any lists in your MailChimp account. <a href='http://admin.mailchimp.com/lists' target='_blank'>Click here</a> to go to MailChimp and configure a list.", 'error');
		}
		
		$lists = array_combine( array_map(function($k){ return $k['id']; }, $lists['data']), array_map(function($v){ return $v['name']; }, $lists['data']));
		$list_names = array_merge(array('0' => 'Select List'), $lists);

		$upload_dir = wp_upload_dir();
		$file_path = $upload_dir['baseurl'] . '/wd-mailchimp';
		MWD_Library::mwd_upgrade_pro(); 
		?>
		
		<form onkeypress="mwd_doNothing(event)" class="wrap" id="manage_form" method="post" action="admin.php?page=manage_forms">
			<?php wp_nonce_field('nonce_mwd', 'nonce_mwd'); ?>
			<div class="mwd-page-banner">
				<div class="mwd-logo">
				</div>
				<div class="mwd-logo-title">Forms</div>
				<button class="mwd-button add-button medium" onclick="mwd_set_input_value('task', 'add'); mwd_form_submit(event, 'manage_form')">
					<span></span>
					Add New
				</button>
				<div class="mwd-page-actions">
					<button class="mwd-button delete-button small" onclick="if (confirm('Do you want to delete selected item(s)?')) { mwd_set_input_value('task', 'delete_all'); mwd_form_submit(event, 'manage_form'); } else { return false; }">
						<span></span>
						Delete
					</button>
				</div>
			</div>
			<div class="tablenav top">
			<?php
				MWD_Library::search('Title', $search_value, 'manage_form');
				MWD_Library::search_select('Form Type', $search_type, $form_types, 'manage_form', 'type');
				MWD_Library::search_select('List Name', $search_list, $list_names, 'manage_form', 'list');
				MWD_Library::html_page_nav($page_nav['total'], $page_nav['limit'], 'manage_form');
			?>
			</div>
			<div class="mwd-clear"></div>
			<table class="wp-list-table widefat fixed pages">
				<thead>
					<th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;"/></th>
					<th class="table_small_col <?php if ($order_by == 'id') { echo $order_class; } ?>">
						<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'id'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'id' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'manage_form')" href="">
						<span>ID</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="<?php if ($order_by == 'title') { echo $order_class; } ?>">
						<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'title'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'title' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'manage_form')" href="">
						<span>Title</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="<?php if ($order_by == 'type') { echo $order_class; } ?>">
						<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'type'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'type' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'manage_form')" href="">
						<span>Type</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="<?php if ($order_by == 'list') { echo $order_class; } ?>">
						<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'list'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'list' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'manage_form')" href="">
						<span>List</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="table_big_col">Shortcode</th>
					<th class="table_large_col">PHP function</th>
					<th class="table_small_col">Edit</th>
					<th class="table_small_col">Delete</th>
				</thead>
				<tbody id="tbody_arr">
					<?php
					if ($rows_data) {
						foreach ($rows_data as $row_data) {
							$alternate = (!isset($alternate) || $alternate == '') ? 'class="alternate"' : '';
							?>
							<tr id="tr_<?php echo $row_data->id; ?>" <?php echo $alternate; ?>>
								<td class="table_small_col check-column">
									<input id="check_<?php echo $row_data->id; ?>" name="check_<?php echo $row_data->id; ?>" type="checkbox"/>
								</td>
								<td class="table_small_col"><?php echo $row_data->id; ?></td>
								<td>
									<a onclick="mwd_set_input_value('task', 'edit'); mwd_set_input_value('current_id', '<?php echo $row_data->id; ?>'); mwd_form_submit(event, 'manage_form')" href=""><div class="title"><?php echo $row_data->title; ?></a>
								</td>
								<td>
									<?php echo $row_data->type; ?>
								</td>
								<td>
									<?php $list_name = array();
									$list_ids = explode(',', $row_data->list_id);
									foreach($list_ids as $list_id){
										if(isset($list_names[$list_id])){
											$list_name[] = $list_names[$list_id];
										}
									}
									echo implode(', ', $list_name); ?>
								</td>
								<td class="table_big_col" style="padding-left: 0; padding-right: 0;">
									<?php if($row_data->type == 'embedded'){ ?>
										<input type="text" value='[mwd-mailchimp id="<?php echo $row_data->id; ?>"]' onclick="mwd_select_value(this)"  readonly="readonly" />
									<?php } else { ?>
										<a href="<?php echo add_query_arg(array('current_id' => $row_data->id, 'nonce_mwd' => wp_create_nonce('nonce_mwd')), admin_url('admin.php?page=manage_forms&task=display_options')); ?>">Set Display Options</a>
									<?php } ?>
								</td>
								<td class="table_large_col" style="padding-left: 0; padding-right: 0;">
									<?php if($row_data->type == 'embedded'){ ?>
										<input type="text" value='&#60;?php MWD_load_forms(array("id" => <?php echo $row_data->id; ?>), "embedded"); ?&#62;' onclick="mwd_select_value(this)"  readonly="readonly" />
									<?php } else { ?>
										<input type="text" value='&#60;?php MWD_load_forms(array("id" => <?php echo $row_data->id; ?>)); ?&#62;' onclick="mwd_select_value(this)"  readonly="readonly" />
									<?php } ?>
								</td>
								<td class="table_small_col">
									<button class="mwd-icon edit-icon" onclick="mwd_set_input_value('task', 'edit'); mwd_set_input_value('current_id', '<?php echo $row_data->id; ?>'); mwd_form_submit(event, 'manage_form');">
										<span></span>
									</button>
								</td>
								<td class="table_small_col">
									<button class="mwd-icon delete-icon" onclick="if (confirm('Do you want to delete selected item(s)?')) { mwd_set_input_value('task', 'delete'); mwd_set_input_value('current_id', '<?php echo $row_data->id; ?>'); mwd_form_submit(event, 'manage_form'); } else {return false;}">
										<span></span>
									</button>
								</td>
							</tr>
							<?php
							$ids_string .= $row_data->id . ',';
						}
					}
					?>
				</tbody>
			</table>
			<input id="task" name="task" type="hidden" value=""/>
			<input id="current_id" name="current_id" type="hidden" value=""/>
			<input id="ids_string" name="ids_string" type="hidden" value="<?php echo $ids_string; ?>"/>
			<input id="asc_or_desc" name="asc_or_desc" type="hidden" value="asc"/>
			<input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>"/>
		</form>
		<?php
	}

	public function edit($id) {
		global $wpdb;
		if(get_option('mwd_api_validation') != 'valid_apikey' && !get_option('mwd_api_key')) {
			echo MWD_Library::message("You need to connect to MailChimp before you can start creating forms. Head over to the <a href='".add_query_arg(array('page' => 'manage_mwd'), admin_url('admin.php'))."'>MailChimp WD</a> and enter your API key.", 'error');
			die();
		}

		?> <img src="<?php echo MWD_URL . '/images/icons.png'; ?>" style="display:none;"/> <?php

		$row = $this->model->get_row_data_new($id);
		$themes = $this->model->get_theme_rows_data();
		$list_ids = isset($_GET['list_id']) ? explode(',', $_GET['list_id']) : ($row->list_id ? explode(',', $row->list_id) : array());
		$lists = $this->model->mwd_lists();
		if(!$lists){
			echo MWD_Library::message("Currently you don't have any lists in your MailChimp account. <a href='http://admin.mailchimp.com/lists' target='_blank'>Click here</a> to go to MailChimp and configure a list.", 'error');
		}

		$animation_effects = array(
			'none' => 'None',
			'bounce' => 'Bounce',
			'tada' => 'Tada',
			'bounceInDown' => 'BounceInDown',
			'fadeInLeft' => 'FadeInLeft',
			'flash' => 'Flash',
			'pulse' => 'Pulse',
			'rubberBand' => 'RubberBand',
			'shake' => 'Shake',
			'swing' => 'Swing',
			'wobble' => 'Wobble',
			'hinge' => 'Hinge',
			'lightSpeedIn' => 'LightSpeedIn',
			'rollIn' => 'RollIn',
			'bounceIn' => 'BounceIn',
			'bounceInLeft' => 'BounceInLeft',
			'bounceInRight' => 'BounceInRight',
			'bounceInUp' => 'BounceInUp',
			'fadeIn' => 'FadeIn',
			'fadeInDown' => 'FadeInDown',
			'fadeInDownBig' => 'FadeInDownBig',
			'fadeInLeftBig' => 'FadeInLeftBig',
			'fadeInRight' => 'FadeInRight',
			'fadeInRightBig' => 'FadeInRightBig',
			'fadeInUp' => 'FadeInUp',
			'fadeInUpBig' => 'FadeInUpBig',
			'flip' => 'Flip',
			'flipInX' => 'FlipInX',
			'flipInY' => 'FlipInY',
			'rotateIn' => 'RotateIn',
			'rotateInDownLeft' => 'RotateInDownLeft',
			'rotateInDownRight' => 'RotateInDownRight',
			'rotateInUpLeft' => 'RotateInUpLeft',
			'rotateInUpRight' => 'RotateInUpRight',
			'zoomIn' => 'ZoomIn',
			'zoomInDown' => 'ZoomInDown',
			'zoomInLeft' => 'ZoomInLeft',
			'zoomInRight' => 'ZoomInRight',
			'zoomInUp' => 'ZoomInUp',
		);

		$labels = array();
		$label_id = array();
		$label_order_original = array();
		$label_type = array();
		$label_all = explode('#****#', $row->label_order);
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			array_push($label_id, $label_id_each[0]);
			$label_oder_each = explode('#**label**#', $label_id_each[1]);
			array_push($label_order_original, addslashes($label_oder_each[0]));
			array_push($label_type, $label_oder_each[1]);
		}

		$labels['id'] = '"' . implode('","', $label_id) . '"';
		$labels['label'] = '"' . implode('","', $label_order_original) . '"';
		$labels['type'] = '"' . implode('","', $label_type) . '"';
		$form_type = isset($_GET['form_type']) ? $_GET['form_type'] : $row->type;
		$mergeParams = array();
		$groupParams = array();
		$tagsInForm = array_keys(json_decode(html_entity_decode($row->merge_variables), true));
		$groupsInForm = array_keys(json_decode(html_entity_decode($row->groups), true));
		$tags = array();
		$mergeNameExist = array();
		$groupNameExist = array();
		$default_theme = $wpdb->get_var('SELECT id FROM ' . $wpdb->prefix . 'mwd_themes where `default`=1');
		
		MWD_Library::mwd_upgrade_pro('edit'); 
		?>
		<script src="<?php echo MWD_URL . '/js/mwd_forms.js'; ?>?ver=<?php echo get_option("mwd_version"); ?>" type="text/javascript"></script>
		<script type="text/javascript">
			var plugin_url = "<?php echo MWD_URL; ?>";
			form_view = 1;
			form_view_count = 1;
			form_view_max = 1;
			function previewImg(task){
				jQuery('#saving').html('<div class="mwd-loading-container"><div class="mwd-loading-content"></div></div>');
				mwd_set_input_value('task', task);
				document.getElementById('manage_form').submit();
			}

			function submitbutton() {
				<?php if ($id) { ?>
				if (!document.getElementById('araqel') || (document.getElementById('araqel').value == '0')) {
					alert('Please wait while page loading.');
					return false;
				}
				<?php } ?>
				/* var isReq = false;
				jQuery('.list-fields .noInForm').each(function(){
					if(jQuery(this).data('req') == true ){
						alert(jQuery(this).html() + ' is required.');
						isReq = true;
						return false;
					}
				});
				if(isReq)
					return false; */

				tox = '';
				form_fields = '';
				document.getElementById('take').style.display = "none";
				document.getElementById('page_bar').style.display = "none";
				jQuery('.wdform_section').each(function() {
					var this2 = this;
					jQuery(this2).find('.wdform_column').each(function() {
						if(!jQuery(this).html() && jQuery(this2).children().length>1)
							jQuery(this).remove();
					});
				});
				remove_whitespace(document.getElementById('take'));
				l_id_array = [<?php echo $labels['id']?>];
				l_label_array = [<?php echo $labels['label']?>];
				l_type_array = [<?php echo $labels['type']?>];
				l_id_removed = [];
				for (x = 0; x < l_id_array.length; x++) {
					l_id_removed[l_id_array[x]] = true;
				}
				for (t = 1; t <= form_view_max; t++) {
					if (document.getElementById('form_id_tempform_view' + t)) {
						wdform_page = document.getElementById('form_id_tempform_view' + t);
						remove_whitespace(wdform_page);
						n = wdform_page.childNodes.length - 2;
						for (q = 0; q <= n; q++) {
							if (!wdform_page.childNodes[q].getAttribute("wdid")) {
								wdform_section = wdform_page.childNodes[q];
								for (x = 0; x < wdform_section.childNodes.length; x++) {
									wdform_column = wdform_section.childNodes[x];
									if (wdform_column.firstChild) {
										for (y=0; y < wdform_column.childNodes.length; y++) {
											is_in_old = false;
											wdform_row = wdform_column.childNodes[y];
											if (wdform_row.nodeType == 3) {
												continue;
											}
											wdid = wdform_row.getAttribute("wdid");
											if (!wdid) {
												continue;
											}
											l_id = wdid;
											l_label = document.getElementById(wdid + '_element_labelform_id_temp').innerHTML;
											l_label = l_label.replace(/(\r\n|\n|\r)/gm," ");
											wdtype = wdform_row.firstChild.getAttribute('type');

											for (var z = 0; z < l_id_array.length; z++) {
												if (l_type_array[z] == "type_address") {
													if (document.getElementById(l_id + "_mini_label_street1") || document.getElementById(l_id + "_mini_label_street2") || document.getElementById(l_id + "_mini_label_city") || document.getElementById(l_id + "_mini_label_state") || document.getElementById(l_id+"_mini_label_postal") || document.getElementById(l_id+"_mini_label_country")) {
														l_id_removed[l_id_array[z]] = false;
													}
												}
												else {
													if (l_id_array[z] == wdid) {
														l_id_removed[l_id] = false;
													}
												}
											}

											if (wdtype == "type_address") {
												addr_id = parseInt(wdid);
												id_for_country = addr_id;
												if (document.getElementById(id_for_country + "_mini_label_street1"))
													tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_street1").innerHTML + '#**label**#type_address#****#';
												addr_id++;
												if (document.getElementById(id_for_country + "_mini_label_street2"))
													tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_street2").innerHTML + '#**label**#type_address#****#';
												addr_id++;
												if (document.getElementById(id_for_country+"_mini_label_city"))
													tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_city").innerHTML + '#**label**#type_address#****#';
												addr_id++;
												if (document.getElementById(id_for_country + "_mini_label_state"))
													tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_state").innerHTML + '#**label**#type_address#****#';
												addr_id++;
												if (document.getElementById(id_for_country + "_mini_label_postal"))
													tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_postal").innerHTML + '#**label**#type_address#****#';
												addr_id++;
												if (document.getElementById(id_for_country+"_mini_label_country")) {
													tox=tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_country").innerHTML + '#**label**#type_address#****#';
												}

											}
											else {
												tox = tox + wdid + '#**id**#' + l_label + '#**label**#' + wdtype + '#****#';
											}

											id = wdid;
											form_fields += wdid + "*:*id*:*";
											form_fields += wdtype + "*:*type*:*";
											w_choices = new Array();
											w_choices_value=new Array();
											w_choices_checked = new Array();
											w_choices_disabled = new Array();
											w_allow_other_num = 0;
											w_property = new Array();
											w_property_type = new Array();
											w_property_values = new Array();
											w_choices_price = new Array();
											if (document.getElementById(id+'_element_labelform_id_temp').innerHTML) {
												w_field_label = document.getElementById(id + '_element_labelform_id_temp').innerHTML.replace(/(\r\n|\n|\r)/gm," ");
											}
											else {
												w_field_label = " ";
											}
											if (document.getElementById(id + '_label_sectionform_id_temp')) {
												if (document.getElementById(id + '_label_sectionform_id_temp').style.display == "block") {
													w_field_label_pos = "top";
												}
												else {
													w_field_label_pos = "left";
												}
											}
											if (document.getElementById(id + "_elementform_id_temp")) {
												s = document.getElementById(id + "_elementform_id_temp").style.width;
												w_size=s.substring(0,s.length - 2);
											}
											if (document.getElementById(id + "_label_sectionform_id_temp")) {
												s = document.getElementById(id + "_label_sectionform_id_temp").style.width;
												w_field_label_size = s.substring(0, s.length - 2);
											}
											if (document.getElementById(id + "_requiredform_id_temp")) {
													w_required = document.getElementById(id + "_requiredform_id_temp").value;
											}
											if (document.getElementById(id + "_uniqueform_id_temp")) {
												w_unique = document.getElementById(id + "_uniqueform_id_temp").value;
											}
											if (document.getElementById(id + '_label_sectionform_id_temp')) {
												w_class = document.getElementById(id + '_label_sectionform_id_temp').getAttribute("class");
												if (!w_class) {
													w_class = "";
												}
											}
											gen_form_fields();
											wdform_row.innerHTML = "%" + id + " - " + l_label + "%";
										}
									}
								}
							}
							else {
								id = wdform_page.childNodes[q].getAttribute("wdid");
								w_editor = document.getElementById(id + "_element_sectionform_id_temp").innerHTML;
								form_fields += id + "*:*id*:*";
								form_fields += "type_section_break" + "*:*type*:*";
								form_fields += "custom_" + id + "*:*w_field_label*:*";
								form_fields += w_editor + "*:*w_editor*:*";
								form_fields += "*:*new_field*:*";
								wdform_page.childNodes[q].innerHTML = "%" + id + " - " + "custom_" + id + "%";
							}
						}
					}
				}
				document.getElementById('label_order_current').value = tox;

				for (x = 0; x < l_id_array.length; x++) {
					if (l_id_removed[l_id_array[x]]) {
						tox = tox + l_id_array[x] + '#**id**#' + l_label_array[x] + '#**label**#' + l_type_array[x] + '#****#';
					}
				}

				document.getElementById('label_order').value = tox;
				document.getElementById('form_fields').value = form_fields;

				refresh_();
				document.getElementById('pagination').value=document.getElementById('pages').getAttribute("type");
				document.getElementById('show_title').value=document.getElementById('pages').getAttribute("show_title");
				document.getElementById('show_numbers').value=document.getElementById('pages').getAttribute("show_numbers");

				return true;
			}

			gen = <?php echo (($id != 0) ? $row->counter : 3); ?>;

			function enable() {
				alltypes = Array('customHTML', 'text', 'checkbox', 'radio', 'time_and_date', 'select', 'file_upload', 'button', 'page_break', 'section_break', 'paypal', 'survey');
				for (x = 0; x < 12; x++) {
					document.getElementById('img_' + alltypes[x]).src = "<?php echo MWD_URL . '/images/'; ?>" + alltypes[x] + ".png?ver=<?php echo get_option("mwd_version"); ?>";
				}
				if (document.getElementById('formsDiv').style.display == 'block') {
					jQuery('#formsDiv').slideToggle(200);
				}
				else {
					jQuery('#formsDiv').slideToggle(400);
				}
				if (document.getElementById('formsDiv').offsetWidth) {
					document.getElementById('formsDiv1').style.width = (document.getElementById('formsDiv').offsetWidth - 60) + 'px';
				}
				if (document.getElementById('formsDiv1').style.display == 'block') {
					jQuery('#formsDiv1').slideToggle(200);
				}
				else {
					jQuery('#formsDiv1').slideToggle(400);
				}
				document.getElementById('when_edit').style.display = 'none';
			}

			function enable2() {
				alltypes = Array('customHTML', 'text', 'checkbox', 'radio', 'time_and_date', 'select', 'file_upload',  'button', 'page_break', 'section_break', 'paypal', 'survey');
				for (x = 0; x < 12; x++) {
					document.getElementById('img_' + alltypes[x]).src = "<?php echo MWD_URL . '/images/'; ?>" + alltypes[x] + ".png?ver=<?php echo get_option("mwd_version"); ?>";
				}
				if (document.getElementById('formsDiv').style.display == 'block') {
					jQuery('#formsDiv').slideToggle(200);
				}
				else {
					jQuery('#formsDiv').slideToggle(400);
				}
				if (document.getElementById('formsDiv').offsetWidth) {
					document.getElementById('formsDiv1').style.width = (document.getElementById('formsDiv').offsetWidth - 60) + 'px';
				}
				if (document.getElementById('formsDiv1').style.display == 'block') {
					jQuery('#formsDiv1').slideToggle(200);
				}
				else {
					jQuery('#formsDiv1').slideToggle(400);
				}
				document.getElementById('when_edit').style.display = 'block';
				if (document.getElementById('field_types').offsetWidth) {
					document.getElementById('when_edit').style.width = document.getElementById('field_types').offsetWidth + 'px';
				}
				if (document.getElementById('field_types').offsetHeight) {
					document.getElementById('when_edit').style.height = document.getElementById('field_types').offsetHeight + 'px';
				}
			}
		</script>
		
		<form class="wrap" id="manage_form" method="post" action="admin.php?page=manage_forms" style="width:99%;" enctype="multipart/form-data">
		<?php wp_nonce_field('nonce_mwd', 'nonce_mwd'); ?>
			<h2 class="mwd-h2-message"></h2>
			<div class="mwd-page-header">
				<div class="mwd-logo">
				</div>
				<div class="mwd-page-actions">
					<button class="mwd-button display-options-button large" onclick="if (!mwd_check_required('title', 'Form title') && submitbutton()) { previewImg('display_options'); } return false;">
						<span></span>
						Display Options
					</button>
					<button class="mwd-button form-options-button medium" onclick="if (!mwd_check_required('title', 'Form title') && submitbutton()) { previewImg('form_options'); } return false;">
						<span></span>
						Form Options
					</button>
					<?php
					if(isset($row->backup_id) )
						if($row->backup_id!="") {
							global $wpdb;
							$query = "SELECT backup_id FROM " . $wpdb->prefix . "mwd_forms_backup WHERE backup_id > ".$row->backup_id." AND id = ".$row->id." ORDER BY backup_id ASC LIMIT 0 , 1 ";
							$backup_id = $wpdb->get_var($query);
							if($backup_id) { ?>
								<button class="mwd-button redo-button small" onclick="if (!mwd_check_required('title', 'Form title') && submitbutton()) { jQuery('#saving_text').html('Redo'); previewImg('redo'); } return false;">
									<span></span>
									Redo
								</button>
								<?php
							}
							$query = "SELECT backup_id FROM " . $wpdb->prefix . "mwd_forms_backup WHERE backup_id < ".$row->backup_id." AND id = ".$row->id." ORDER BY backup_id DESC LIMIT 0 , 1 ";
							$backup_id = $wpdb->get_var($query);

							if($backup_id) { ?>
								<button class="mwd-button undo-button small" onclick="if (!mwd_check_required('title', 'Form title') && submitbutton()) { jQuery('#saving_text').html('Undo'); previewImg('undo'); } return false;">
									<span></span>
									Undo
								</button>
								<?php
							}
						}

						if ($id) { ?>
							<button class="mwd-button save-as-copy-button medium" onclick="if (!mwd_check_required('title', 'Form title') && submitbutton()) { previewImg('save_as_copy'); } return false;">
								<span></span>
								Save as Copy
							</button>
						<?php } ?>
					<button class="mwd-button save-button small" onclick="if (!mwd_check_required('title', 'Form title') && submitbutton()) { previewImg('save'); } return false;">
						<span></span>
						Save
					</button>
					<button class="mwd-button apply-button small" onclick="if (!mwd_check_required('title', 'Form title') && submitbutton()) { previewImg('apply'); } return false;">
						<span></span>
						Apply
					</button>
					<button class="mwd-button cancel-button small" onclick="mwd_set_input_value('task', 'cancel');">
						<span></span>
						Cancel
					</button>

				</div>
				<div class="mwd-clear"></div>
			</div>

			<div class="container-fluid">
				<div class="row">
					<div class="col-md-9">
						<div class="mwd-row">
							<label>Form title: </label>
							<input type="text" id="title" name="title" value="<?php echo $row->title; ?>"/>
						</div>
						<div class="mwd-row">
							<label>Theme: </label>
							<select id="theme" name="theme" onChange="set_preview()">
								<option value="0" <?php echo $row->theme && $row->theme == 0 ? 'selected' : '' ?>>Inherit From Website Theme</option>
								<?php
								foreach ($themes as $theme) {
									?>
									<option value="<?php echo $theme->id; ?>" <?php echo (($theme->id == $row->theme) ? 'selected' : ''); ?>><?php echo $theme->title; ?></option>
									<?php
								}
								?>

							</select>
							<?php if ($id) { ?>
							<button id="preview_form" class="mwd-button preview-button small" onclick="tb_show('', '<?php echo add_query_arg(array('action' => 'FormsPreview', 'form_id' => $row->id, 'test_theme' => $row->theme,  'form_preview' => 1, 'width' => '1000', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>'); return false;">
								<span></span>
								Preview
							</button>
							<?php } ?>
							<button id="edit_css" class="mwd-button options-edit-button small" onclick="window.open('<?php echo add_query_arg(array('current_id' => ($row->theme ? $row->theme : $default_theme), 'nonce_mwd' => wp_create_nonce('nonce_mwd')), admin_url('admin.php?page=themes&task=edit')); ?>'); return false;">
								<span></span>
								Edit
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="mwd-clear"></div>
			<div id="formsDiv" onclick="close_window()"></div>
			<div id="formsDiv1">
				<table class="formsDiv1_table" border="0" width="100%" cellpadding="0" cellspacing="0" height="100%">
					<tr>
						<td style="padding:0px">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
								<tr valign="top">
									<td width="20%" height="100%" id="field_types">
										<div id="when_edit" style="display: none;"></div>
										<table border="0" cellpadding="0" cellspacing="3" width="100%" style="border-collapse: separate; border-spacing: 3px;">
											<tbody>
												<tr>
													<td align="center" onclick="addRow('customHTML')" class="field_buttons" id="table_editor">
														<img src="<?php echo MWD_URL; ?>/images/customHTML.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_customHTML">
														<div>Custom HTML</div>
													</td>
													<td align="center" onclick="addRow('text')" class="field_buttons" id="table_text">
														<img src="<?php echo MWD_URL; ?>/images/text.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_text">
														<div>Text input</div>
													</td>
												</tr>
												<tr>
													<td align="center" onclick="addRow('checkbox')" class="field_buttons" id="table_checkbox">
														<img src="<?php echo MWD_URL; ?>/images/checkbox.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_checkbox">
														<div>Multiple Choice</div>
													</td>
													<td align="center" onclick="addRow('radio')" class="field_buttons" id="table_radio">
														<img src="<?php echo MWD_URL; ?>/images/radio.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_radio">
														<div>Single Choice</div>
													</td>
												</tr>
												<tr>
													<td align="center" onclick="addRow('survey')" class="field_buttons" id="table_survey">
														<img src="<?php echo MWD_URL; ?>/images/survey.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_survey">
														<div>Survey Tools</div>
													</td>
													<td align="center" onclick="addRow('time_and_date')" class="field_buttons" id="table_time_and_date">
														<img src="<?php echo MWD_URL; ?>/images/time_and_date.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_time_and_date">
														<div>Time and Date</div>
													</td>
											   </tr>
												<tr>
													<td align="center" onclick="addRow('select')" class="field_buttons" id="table_select">
														<img src="<?php echo MWD_URL; ?>/images/select.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_select">
														<div>Select Box</div>
													</td>
													<td align="center" onclick="addRow('file_upload')" class="field_buttons" id="table_file_upload">
														<img src="<?php echo MWD_URL; ?>/images/file_upload.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_file_upload">
														<div>File Upload</div>
													</td>
												</tr>
												<tr>
													<td align="center" onclick="addRow('section_break')" class="field_buttons" id="table_section_break">
														<img src="<?php echo MWD_URL; ?>/images/section_break.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_section_break">
														<div>Section Break</div>
													</td>
													<td align="center" onclick="addRow('page_break')" class="field_buttons" id="table_page_break">
														<img src="<?php echo MWD_URL; ?>/images/page_break.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_page_break">
														<div>Page Break</div>
													</td>
												</tr>
												<tr>
													<td align="center" onclick="addRow('paypal')" style="" id="table_paypal" class="field_buttons">
														<img src="<?php echo MWD_URL; ?>/images/paypal.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_paypal">
														<div>PayPal</div>
												</td>
												<td align="center" onclick="addRow('button')" id="table_button" class="field_buttons">
													<img src="<?php echo MWD_URL; ?>/images/button.png?ver=<?php echo get_option("mwd_version"); ?>" style="margin:5px" id="img_button">
													<div>Button</div>
												</td>
											   </tr>

											</tbody>
										</table>
									</td>
									<td width="40%" height="100%" align="left">
										<div id="edit_table"></div>
									</td>
									<td align="center" valign="top" style="background: url("<?php echo MWD_URL . '/images/border2.png'; ?>") repeat-y;">&nbsp;</td>
									<td style="padding:15px;">
										<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
											<tr>
												<td align="right">
													<input type="radio" value="end" name="el_pos" checked="checked" id="pos_end" onclick="Disable()"/>
													At The End
													<input type="radio" value="begin" name="el_pos" id="pos_begin" onclick="Disable()"/>
													At The Beginning
													<input type="radio" value="before" name="el_pos" id="pos_before" onclick="Enable();"/>
													Before
													<select style="width: 100px; margin-left: 5px;" id="sel_el_pos" onclick="change_before()" disabled="disabled"></select>
													<br>
													<button class="mwd-button field-save-button small" onclick="add(0, false); return false;">
														Save
														<span></span>
													</button>
													<button class="mwd-button cancel-button small" onclick="close_window(); return false;">
														Cancel
														<span></span>
													</button>
													<hr style="margin-bottom:10px" />
												</td>
											</tr>
											<tr height="100%" valign="top">
												<td id="show_table"></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<input type="hidden" id="old" />
				<input type="hidden" id="old_selected" />
				<input type="hidden" id="element_type" />
				<input type="hidden" id="editing_id" />
				<input type="hidden" value="<?php echo MWD_URL; ?>" id="form_plugins_url" />
				<div id="main_editor" style="position: fixed; display: none; z-index: 140;">
					<?php if (user_can_richedit()) {
						wp_editor('', 'form_maker_editor', array('teeny' => FALSE, 'textarea_name' => 'form_maker_editor', 'media_buttons' => FALSE, 'textarea_rows' => 5));
					}
					else { ?>
						<textarea name="form_maker_editor" id="form_maker_editor" cols="40" rows="5" style="width: 440px; height: 350px;" class="mce_editable" aria-hidden="true"></textarea>
						<?php
					}
					?>
				</div>
			</div>
			<?php if (!function_exists('the_editor')) { ?>
				<iframe id="tinymce" style="display: none;"></iframe>
			<?php } ?>
			<br />
			<div id="saving">
			</div>
			<div class="panel form-header">
				<div class="panel-heading">
					<span class="mwd-header-bg"></span>
					Header
					<span class="mwd-expcol pull-right" onclick="jQuery(this).toggleClass('mwd-expanded'); mwd_toggle(this);"></span>
				</div>
				<div class="panel-content hide">
					<div class="col-md-7 col-xs-12">
						<div class="mwd-row">
							<label>Title: </label>
							<input type="text" id="header_title" name="header_title" value="<?php echo $row->header_title; ?>"/>
						</div>
						<div class="mwd-row">
							<label>Description: </label>
							<div id="description_editor" style="width:470px; display: inline-block; vertical-align: middle;">
								<?php if (user_can_richedit()) {
									wp_editor($row->header_description, 'header_description', array('teeny' => FALSE, 'textarea_name' => 'header_description', 'media_buttons' => FALSE, 'textarea_rows' => 5));
								}
								else { ?>
									<textarea name="header_description" id="header_description" cols="40" rows="5" style="width: 440px; height: 350px;" class="mce_editable" aria-hidden="true"></textarea>
									<?php
								}
								?>
							</div>
						</div>
					</div>
					<div class="col-md-5 col-xs-12">
						<div class="mwd-row">
							<label>Image: </label>
							<input type="text" id="header_image_url" name="header_image_url" value="<?php echo $row->header_image_url; ?>"/>
							<button class="mwd-button add-button small" onclick="mwdOpenMediaUploader(event); return false;">Add Image</button>
							<?php $header_bg = $row->header_hide_image ? 'background-image: url('.$row->header_image_url.'); background-position: center;' : ''; ?>
							<div id="header_image" class="header_img" style="<?php echo $header_bg; ?>"></div>
						</div>
						<div class="mwd-row">
							<label>Image Animation: </label>
							<select name="header_image_animation">
								<?php
									foreach($animation_effects as $anim_key => $animation_effect){
										$selected = $row->header_image_animation == $anim_key ? 'selected="selected"' : '';
										echo '<option value="'.$anim_key.'" '.$selected.'>'.$animation_effect.'</option>';
									}
								?>
							</select>
						</div>
						<div class="mwd-row">
							<label for="header_hide_image">Hide Image on Mobile: </label>
							<input type="checkbox" id="header_hide_image" name="header_hide_image" value="1" <?php echo $row->header_hide_image == '1' ? 'checked="checked"' : '' ?> />
						</div>
					</div>
					<div class="mwd-clear"></div>
				</div>
			</div>
			<div class="panel form-content">
				<div class="panel-heading">
					<span class="mwd-header-bg"></span>
					Form Fields
					<span class="mwd-expanded mwd-expcol pull-right" onclick="jQuery(this).toggleClass('mwd-expanded'); mwd_toggle(this);"></span>
				</div>
				<div class="panel-content">
					<div class="mwd-2col">
						<div class="mwd-sidebar pull-left">
							<div class="mwd-mini-heading mwd-border">Select the Mailchimp lists to connect to this form.</div>
							<?php if($lists) { ?>
							<div class="mwd-row">
									<label>List: </label>
									<div class="mwd_lists" style="display: inline-block; vertical-align: middle;">
									<?php $index = 0;
									foreach ($lists as $list_key => $list) {
										$checked = '';
										if(in_array($list_key, $list_ids)){
											$checked = 'checked="checked"';
										}
										if(!$id && $index == 0 && !$list_ids){
											$checked = 'checked="checked"';
											$list_ids[] = $list_key;
										}
										$index++;
										?>
										<input type="checkbox" name="list[]" id="<?php echo $list_key; ?>" value="<?php echo $list_key; ?>" <?php echo $checked; ?> onclick="show_mixed_fields()" />
										<label for="<?php echo $list_key; ?>"><?php echo $list->name; ?></label>
										<br/>
									<?php } ?>
									</div>
							</div>
							<br />
							<div class="mwd-mini-heading mwd-border">Click on the field name to add it to the form.</div>
							<div class="mwd-row list-fields mailchimp-field">
								<?php if($list_ids) {
								$groupToAddAfter = '';
								foreach( $list_ids as $lkey => $listID ){
									$listData = $lists[$listID];
									$merges = $listData->merge_vars;
									if( $merges ){
										echo $lkey == 0 ? '<div class="mini-label listfields">List fields</div>' : '';
										$n = count($merges);
										for($l=0; $l<$n; $l++){
											if(!in_array($merges[$l]->tag, $tags)){
												if(in_array($merges[$l]->name, $mergeNameExist))
													$merges[$l]->name .= '(1)';

												if(isset($merges[$l]->choices)){
													$mergeParams[$merges[$l]->tag] = $merges[$l]->choices;
												}
												$reqclass = $merges[$l]->req == true ? 'isRequired' : '';
												if(in_array($merges[$l]->tag, $tagsInForm)){
													$informclass = 'inForm';
												} else{
													$informclass = 'noInForm';
												}

												echo '<button data-fieldType="'.$merges[$l]->field_type.'" data-tag="'.$merges[$l]->tag.'" data-req="'.$merges[$l]->req.'" class="'.$informclass .' ' . $reqclass.'" onclick="add_mailchimp_field(this); return false;">'.$merges[$l]->name.'</button>';

												$mergeNameExist[] = $merges[$l]->name;
											}

											$tags[] = $merges[$l]->tag;
										}
									}
									
									$intGroups = $listData->interest_groups;
									if( $intGroups ){
										$groupToAddAfter .=  $lkey == 0 ? '<br /><br /><div class="mini-label intgroup">Interest groups</div>' : '';
										$m = count($intGroups);
										for($k=0; $k<$m; $k++){
											if(in_array($intGroups[$k]['name'], $groupNameExist))
												$merges[$l]->name .= '(1)';
											$allGroups = $intGroups[$k]['groups'];
											$groupParams[$intGroups[$k]['id']] = array_map(function($el){ return $el['name'];}, $allGroups);

											if(in_array($intGroups[$k]['id'], $groupsInForm)){
												$informclass = 'inForm';
											} else{
												$informclass = 'noInForm';
											}
											$groupToAddAfter .= '<button data-fieldType="'.$intGroups[$k]['form_field'].'" data-id="'.$intGroups[$k]['id'].'" class="'.$informclass.'" onclick="add_mailchimp_group(this); return false;">'.$intGroups[$k]['name'].'</button>';

											$groupNameExist[] = $intGroups[$k]['name'];
										}
									}
								}
								echo $groupToAddAfter;
							}
							?>
							</div>
							<div class="pull-right need-more">
								<span></span><a onclick="tb_show('', '<?php echo add_query_arg(array('action' => 'helper', 'task' => 'add_more', 'nonce_mwd' => wp_create_nonce('nonce_mwd'), 'width' => '650', 'height' => '380'), admin_url('admin-ajax.php')); ?>&list_id='+ jQuery('.mwd_lists input:checked').map(function() {return this.value;}).get().join(','))+'&TB_iframe=1';">Need more fields?</a>
							</div>
							<div class="mwd-clear"></div>
							<?php } else{
								echo MWD_Library::message("Currently you don't have any lists in your MailChimp account. <a href='http://admin.mailchimp.com/lists' target='_blank'>Click here</a> to go to MailChimp and configure a list.", 'error');
							} ?>	
						</div>
						<div class="pull-right">
							<div class="mwd-sidebar">
								<div class="mwd-mini-heading mwd-border">Select the form action.</div>
								<div class="mwd-row">
									<label title="Defines the action - either subscribe or unsubscribe the user">Action: </label>
									<select id="subscribe_action" name="subscribe_action" >
									<option value="1" <?php echo $row->subscribe_action == 1 ? 'selected="selected"' : ''?>>Subscribe</option>
									<option value="0" <?php echo $row->subscribe_action == 0 ? 'selected="selected"' : ''?>>Unsubscribe</option>
									</select>
									<div class="mwd-hide">You have already chosen the action of the form.</div>
									<div class="need-more text-right" style="margin-top:5px;">
										<span></span><a onclick="if (!mwd_check_required('title', 'Form title') && submitbutton()) { document.getElementById('fieldset_id').value='mailchimp'; previewImg('form_options'); } return false;">More MailChimp options ?</a>
									</div>
								</div>
							</div>
							<div class="mwd-sidebar">
								<div class="mwd-mini-heading mwd-border">Allow the user to choose form action (Subscribe/Unsubscribe) and the MailChimp list.</div>
								<div class="mailchimp-field custom-button">
									<button class="noInForm" data-action="1" onclick="el_action(this); return false;"><span></span>Form Action</button>
									<button class="noInForm" data-list="1" onclick="el_list(this); return false;"><span></span>List Choice</button>
								</div>
							</div>
							<div class="mwd-sidebar">
								<div class="mwd-mini-heading mwd-border">Here you can add custom fields, not connected with mailchimp. These fields entries will be saved in submissions</div>
								<div class="mailchimp-field custom-button">
									<button onclick="el_captcha(this); return false;"><span></span>Add Captcha</button>
									<button onclick="enable(); Enable(); return false;"><span></span>Custom Fields</button>
								</div>
							</div>
						</div>	
						<div class="mwd-clear"></div>
					</div>
					<div class="mwd-sidebar">
						<div class="mwd-mini-heading">
							<label for="enable_sortable">Enable Drag & Drop</label>
							<button name="sortable" id="enable_sortable" class="mwd-checkbox-radio-button <?php echo $row->sortable == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="enable_drag(this); return false;" value="<?php echo $row->sortable; ?>">
								<span></span>
							</button>
							<input type="hidden" name="sortable" id="sortable_hidden" value="<?php echo $row->sortable; ?>"/>
						</div>
						<div class="mwd-italic mwd-border">
							You can use drag and drop to move the fields up/down for the change of the order and left/right for creating columns within the form.
						</div>
						<div class="mwd-clear"></div>
					</div>
					<fieldset>
						<?php if ($id) { ?>
							<div style="margin: 8px; display: table; width: 100%;" id="page_bar">
								<div id="page_navigation" style="display: table-row;">
									<div align="center" id="pages" show_title="<?php echo $row->show_title; ?>" show_numbers="<?php echo $row->show_numbers; ?>" type="<?php echo $row->pagination; ?>" style="display: table-cell;  width:90%;"></div>
									<div align="left" id="edit_page_navigation" style="display: table-cell; vertical-align: middle;"></div>
								</div>
							</div>
							<div id="take" class="main">
								<?php echo $row->form_front; ?>
							</div>
						<?php } else { ?>
							<div style="margin:8px; display:table; width:100%"  id="page_bar">
								<div id="page_navigation" style="display:table-row">
									<div align="center" id="pages" show_title="false" show_numbers="true" type="none" style="display:table-cell;  width:90%"></div>
									<div align="left" id="edit_page_navigation" style="display:table-cell; vertical-align: middle;"></div>
								</div>
							</div>
							<div id="take" class="main">
								<div class="wdform-page-and-images" style="display:table; border-top:0px solid black;">
									<div id="form_id_tempform_view1" class="wdform_page" page_title="Untitled page" next_title="Next" next_type="text" next_class="wdform-page-button" next_checkable="false" previous_title="Previous" previous_type="text" previous_class="wdform-page-button" previous_checkable="false">
										<div class="wdform_section">
											<div class="wdform_column">
												<div wdid="1" class="wdform_row">
													<div id="wdform_field1" type="type_submitter_mail" class="wdform_field" style="display: table-cell; cursor: move; border: none; background-color: rgb(255, 255, 255);">
														<div id="wdform_arrows1" class="wdform_arrows" style="display: none;">
															<div id="X_1" valign="middle" align="right" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/delete_el.png" title="Remove the field" onclick="remove_row(&quot;1&quot;)" onmouseover="chnage_icons_src(this,&quot;delete_el&quot;)" onmouseout="chnage_icons_src(this,&quot;delete_el&quot;)">
															</div>
															<div id="left_1" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/left.png" title="Move the field to the left" onclick="left_row(&quot;1&quot;)" onmouseover="chnage_icons_src(this,&quot;left&quot;)" onmouseout="chnage_icons_src(this,&quot;left&quot;)">
															</div>
															<div id="up_1" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/up.png" title="Move the field up" onclick="up_row(&quot;1&quot;)" onmouseover="chnage_icons_src(this,&quot;up&quot;)" onmouseout="chnage_icons_src(this,&quot;up&quot;)">
															</div>
															<div id="down_1" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/down.png" title="Move the field down" onclick="down_row(&quot;1&quot;)" onmouseover="chnage_icons_src(this,&quot;down&quot;)" onmouseout="chnage_icons_src(this,&quot;down&quot;)">
															</div>
															<div id="right_1" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/right.png" title="Move the field to the right" onclick="right_row(&quot;1&quot;)" onmouseover="chnage_icons_src(this,&quot;right&quot;)" onmouseout="chnage_icons_src(this,&quot;right&quot;)">
															</div>
															<div id="edit_1" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/edit.png" title="Edit the field" onclick="edit(&quot;1&quot;)" onmouseover="chnage_icons_src(this,&quot;edit&quot;)" onmouseout="chnage_icons_src(this,&quot;edit&quot;)">
															</div>
															<div id="duplicate_1" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/duplicate.png" title="Duplicate the field" onclick="duplicate(&quot;1&quot;)" onmouseover="chnage_icons_src(this,&quot;duplicate&quot;)" onmouseout="chnage_icons_src(this,&quot;duplicate&quot;)"></div><div id="page_up_1" valign="middle" class="element_toolbar"><img src="<?php echo MWD_URL; ?>/images/page_up.png" title="Move the field to the upper page" onclick="page_up(&quot;1&quot;)" onmouseover="chnage_icons_src(this,&quot;page_up&quot;)" onmouseout="chnage_icons_src(this,&quot;page_up&quot;)">
															</div>
															<div id="page_down_1" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/page_down.png" title="Move the field to the lower page" onclick="page_down(&quot;1&quot;)" onmouseover="chnage_icons_src(this,&quot;page_down&quot;)" onmouseout="chnage_icons_src(this,&quot;page_down&quot;)">
															</div>
														</div>
														<div align="left" id="1_label_sectionform_id_temp" class="" style="display: table-cell; width: 100px;">
															<span id="1_element_labelform_id_temp" class="label" style="vertical-align: top;">E-mail:</span>
															<span id="1_required_elementform_id_temp" class="required" style="vertical-align: top;"> *</span>
														</div>
														<div align="left" id="1_element_sectionform_id_temp" class="" style="display: table-cell;">
															<input type="hidden" value="type_submitter_mail" name="1_typeform_id_temp" id="1_typeform_id_temp">
															<input type="hidden" value="yes" name="1_requiredform_id_temp" id="1_requiredform_id_temp">
															<input type="hidden" value="" name="1_uniqueform_id_temp" id="1_uniqueform_id_temp">
															<input type="text" class="input_deactive" id="1_elementform_id_temp" name="1_elementform_id_temp" value="" title="" onfocus="delete_value('1_elementform_id_temp')" onblur="return_value('1_elementform_id_temp')" onchange="change_value('1_elementform_id_temp')" style="width: 200px;" disabled="">
														</div>
													</div>
												</div>
												<div wdid="2" class="wdform_row ui-sortable-handle">
													<div id="wdform_field2" type="type_submit_reset" class="wdform_field" style="display: table-cell; cursor: move; border: none; background-color: rgb(255, 255, 255);">
														<div id="wdform_arrows2" class="wdform_arrows" style="display: none;">
															<div id="X_2" valign="middle" align="right" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/delete_el.png" title="Remove the field" onclick="remove_row(&quot;2&quot;)" onmouseover="chnage_icons_src(this,&quot;delete_el&quot;)" onmouseout="chnage_icons_src(this,&quot;delete_el&quot;)">
															</div>
															<div id="left_2" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/left.png" title="Move the field to the left" onclick="left_row(&quot;2&quot;)" onmouseover="chnage_icons_src(this,&quot;left&quot;)" onmouseout="chnage_icons_src(this,&quot;left&quot;)">
															</div>
															<div id="up_2" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/up.png" title="Move the field up" onclick="up_row(&quot;2&quot;)" onmouseover="chnage_icons_src(this,&quot;up&quot;)" onmouseout="chnage_icons_src(this,&quot;up&quot;)">
															</div>
															<div id="down_2" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/down.png" title="Move the field down" onclick="down_row(&quot;2&quot;)" onmouseover="chnage_icons_src(this,&quot;down&quot;)" onmouseout="chnage_icons_src(this,&quot;down&quot;)">
															</div>
															<div id="right_2" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/right.png" title="Move the field to the right" onclick="right_row(&quot;2&quot;)" onmouseover="chnage_icons_src(this,&quot;right&quot;)" onmouseout="chnage_icons_src(this,&quot;right&quot;)">
															</div>
															<div id="edit_2" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/edit.png" title="Edit the field" onclick="edit(&quot;2&quot;)" onmouseover="chnage_icons_src(this,&quot;edit&quot;)" onmouseout="chnage_icons_src(this,&quot;edit&quot;)">
															</div>
															<div id="duplicate_2" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/duplicate.png" title="Duplicate the field" onclick="duplicate(&quot;2&quot;)" onmouseover="chnage_icons_src(this,&quot;duplicate&quot;)" onmouseout="chnage_icons_src(this,&quot;duplicate&quot;)">
															</div>
															<div id="page_up_2" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/page_up.png" title="Move the field to the upper page" onclick="page_up(&quot;2&quot;)" onmouseover="chnage_icons_src(this,&quot;page_up&quot;)" onmouseout="chnage_icons_src(this,&quot;page_up&quot;)">
															</div>
															<div id="page_down_2" valign="middle" class="element_toolbar">
																<img src="<?php echo MWD_URL; ?>/images/page_down.png" title="Move the field to the lower page" onclick="page_down(&quot;2&quot;)" onmouseover="chnage_icons_src(this,&quot;page_down&quot;)" onmouseout="chnage_icons_src(this,&quot;page_down&quot;)">
															</div>
														</div>
														<div align="left" id="2_label_sectionform_id_temp" class="" style="display: table-cell;">
															<span id="2_element_labelform_id_temp" style="display: none;">type_submit_reset_2</span>
														</div>
														<div align="left" id="2_element_sectionform_id_temp" class="" style="display: table-cell;">
															<input type="hidden" value="type_submit_reset" name="2_typeform_id_temp" id="2_typeform_id_temp">
															<button type="button" class="button-submit" id="2_element_submitform_id_temp" value="Submit" onclick="check_required('submit', 'form_id_temp');">Submit</button>
															<button type="button" class="button-reset" id="2_element_resetform_id_temp" value="Reset" onclick="check_required('reset');" style="display: none;">Reset</button>
														</div>
													</div>
												</div>
											</div>
											<div class="wdform_column"></div>
										</div>
										<div class="wdform_footer">
											<div style="width: 100%;">
												<div style="width: 100%; display: table;">
													<div style="display: table-row-group;">
														<div id="mwd_form_id_temppage_nav1" style="display: table-row;"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="form_id_tempform_view_img1" style="float: right;">
										<div>
											<img src="<?php echo MWD_URL . '/images/minus.png?ver='. get_option("mwd_version"); ?>" title="Show or hide the page" class="page_toolbar" onClick="show_or_hide('1')" onMouseOver="chnage_icons_src(this,'minus')" onmouseout="chnage_icons_src(this,'minus')" id="show_page_img_1"/>
											<img src="<?php echo MWD_URL . '/images/page_delete.png?ver='. get_option("mwd_version"); ?>" title="Delete the page" class="page_toolbar" onClick="remove_page('1')" onMouseOver="chnage_icons_src(this,'page_delete')" onmouseout="chnage_icons_src(this,'page_delete')"/>
											<img src="<?php echo MWD_URL . '/images/page_delete_all.png?ver='. get_option("mwd_version"); ?>" title="Delete the page with fields" class="page_toolbar" onClick="remove_page_all('1')" onMouseOver="chnage_icons_src(this,'page_delete_all')" onmouseout="chnage_icons_src(this,'page_delete_all')"/>
											<img src="<?php echo MWD_URL . '/images/page_edit.png?ver='. get_option("mwd_version"); ?>" title="Edit the page" class="page_toolbar" onClick="edit_page_break('1')" onMouseOver="chnage_icons_src(this,'page_edit')"  onmouseout="chnage_icons_src(this,'page_edit')"/>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</fieldset>
				</div>
			</div>
			<input type="hidden" name="form_front" id="form_front" />
			<input type="hidden" name="form_fields" id="form_fields" />
			<input type="hidden" name="pagination" id="pagination" />
			<input type="hidden" name="show_title" id="show_title" />
			<input type="hidden" name="show_numbers" id="show_numbers" />
			<input type="hidden" name="public_key" id="public_key" />
			<input type="hidden" name="private_key" id="private_key" />
			<input type="hidden" id="label_order" name="label_order" value="<?php echo $row->label_order; ?>" />
			<input type="hidden" id="label_order_current" name="label_order_current" value="<?php echo $row->label_order_current; ?>" />
			<input type="hidden" name="counter" id="counter" value="<?php echo $row->counter; ?>" />
			<input type="hidden" id="araqel" value="0" />
			<input type="hidden" name="backup_id" id="backup_id" value="<?php echo $row->backup_id;?>">
			<input type="hidden" id="merge_variables" name="merge_variables" value='<?php echo html_entity_decode($row->merge_variables); ?>'/>
			<input type="hidden" id="groups" name="groups" value='<?php echo html_entity_decode($row->groups); ?>'/>
			<input type="hidden" id="form_type" name="form_type" value="<?php echo $form_type; ?>"/>
			<input type="hidden" id="tagsInForm" name="tagsInForm" value="<?php echo implode(',', $tagsInForm); ?>"/>
			<input type="hidden" id="groupsInForm" name="groupsInForm" value="<?php echo implode(',', $groupsInForm); ?>"/>
			<input type="hidden" id="action_wdid" name="action_wdid" value=""/>
			<input type="hidden" id="list_choice_wdid" name="list_choice_wdid" value=""/>
			<input type="hidden" name="fieldset_id" id="fieldset_id" value="general" />
			<?php if ($id) { ?>
			<script type="text/javascript">
				lists = '<?php echo addcslashes(json_encode($lists), "'"); ?>' || [];
				mergeParams = JSON.parse('<?php echo addcslashes(json_encode($mergeParams, true), "'"); ?>') || [];
				groupParams = JSON.parse('<?php echo addcslashes(json_encode($groupParams, true), "'"); ?>') || [];
				function set_preview() {
					jQuery("#preview_form").attr("onclick", "tb_show('', '<?php echo add_query_arg(array('action' => 'FormsPreview', 'form_id' => $row->id,  'form_preview' => 1), admin_url('admin-ajax.php')); ?>&test_theme=" + jQuery('#theme').val() + "&width=1000&height=500&TB_iframe=1'); return false;");
				}
				function formOnload() {
					for (t = 0; t < <?php echo $row->counter; ?>; t++) {
						if (document.getElementById(t + "_typeform_id_temp")) {
							if (document.getElementById(t + "_typeform_id_temp").value == "type_spinner") {
								var spinner_value = document.getElementById(t + "_elementform_id_temp").value;
								var spinner_min_value = document.getElementById(t + "_min_valueform_id_temp").value;
								var spinner_max_value = document.getElementById(t + "_max_valueform_id_temp").value;
								var spinner_step = document.getElementById(t + "_stepform_id_temp").value;
								jQuery("#" + t + "_elementform_id_temp")[0].spin = null;
								spinner = jQuery("#" + t + "_elementform_id_temp").spinner();
								spinner.spinner("value", spinner_value);
								jQuery("#" + t + "_elementform_id_temp").spinner({ min:spinner_min_value});
								jQuery("#" + t + "_elementform_id_temp").spinner({ max:spinner_max_value});
								jQuery("#" + t + "_elementform_id_temp").spinner({ step:spinner_step});
							}
							else if (document.getElementById(t + "_typeform_id_temp").value == "type_slider") {
								var slider_value = document.getElementById(t + "_slider_valueform_id_temp").value;
								var slider_min_value = document.getElementById(t + "_slider_min_valueform_id_temp").value;
								var slider_max_value = document.getElementById(t + "_slider_max_valueform_id_temp").value;
								var slider_element_value = document.getElementById(t + "_element_valueform_id_temp");
								var slider_value_save = document.getElementById(t + "_slider_valueform_id_temp");
								jQuery("#" + t + "_elementform_id_temp")[0].slide = null;
								jQuery(function () {
									jQuery("#" + t + "_elementform_id_temp").slider({
										range:"min",
										value:eval(slider_value),
										min:eval(slider_min_value),
										max:eval(slider_max_value),
										slide:function (event, ui) {
											slider_element_value.innerHTML = "" + ui.value;
											slider_value_save.value = "" + ui.value;
										}
									});
								});
							}
							else if (document.getElementById(t + "_typeform_id_temp").value == "type_range") {
								var spinner_value0 = document.getElementById(t + "_elementform_id_temp0").value;
								var spinner_step = document.getElementById(t + "_range_stepform_id_temp").value;
								jQuery("#" + t + "_elementform_id_temp0")[0].spin = null;
								jQuery("#" + t + "_elementform_id_temp1")[0].spin = null;
								spinner0 = jQuery("#" + t + "_elementform_id_temp0").spinner();
								spinner0.spinner("value", spinner_value0);
								jQuery("#" + t + "_elementform_id_temp0").spinner({ step:spinner_step});
								var spinner_value1 = document.getElementById(t + "_elementform_id_temp1").value;
								spinner1 = jQuery("#" + t + "_elementform_id_temp1").spinner();
								spinner1.spinner("value", spinner_value1);
								jQuery("#" + t + "_elementform_id_temp1").spinner({ step:spinner_step});
								var myu = t;
								jQuery(document).ready(function () {
									jQuery("#" + myu + "_mini_label_from").click(function () {
										if (jQuery(this).children('input').length == 0) {
											var from = "<input type='text' id='from' class='from' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\"" + jQuery(this).text() + "\">";
											jQuery(this).html(from);
											jQuery("input.from").focus();
											jQuery("input.from").blur(function () {
												var id_for_blur = document.getElementById('from').parentNode.id.split('_');
												var value = jQuery(this).val();
												jQuery("#" + id_for_blur[0] + "_mini_label_from").text(value);
											});
										}
									});
									jQuery("label#" + myu + "_mini_label_to").click(function () {
										if (jQuery(this).children('input').length == 0) {
											var to = "<input type='text' id='to' class='to' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\"" + jQuery(this).text() + "\">";
											jQuery(this).html(to);
											jQuery("input.to").focus();
											jQuery("input.to").blur(function () {
												var id_for_blur = document.getElementById('to').parentNode.id.split('_');
												var value = jQuery(this).val();
												jQuery("#" + id_for_blur[0] + "_mini_label_to").text(value);
											});
										}
									});
								});
							}
							else if (document.getElementById(t + "_typeform_id_temp").value == "type_action") {
								jQuery('#action_wdid').val(t);
								jQuery('.mailchimp-field button[data-action="1"]').removeClass('noInForm').addClass('inForm');
								jQuery('#subscribe_action').attr('disabled', 'disabled');
								jQuery('#subscribe_action').next().removeClass('mwd-hide').addClass('mwd-show');
							}else if (document.getElementById(t + "_typeform_id_temp").value == "type_list") {
								jQuery('#list_choice_wdid').val(t);
								jQuery('.mailchimp-field button[data-list="1"]').removeClass('noInForm').addClass('inForm');
							}
						}
					}

					remove_whitespace(document.getElementById('take'));
					form_view = 1;
					form_view_count = 0;

					for (i = 1; i <= 30; i++) {
						if (document.getElementById('form_id_tempform_view' + i)) {
							form_view_count++;
							form_view_max = i;
							tbody_img = document.createElement('div');
							tbody_img.setAttribute('id', 'form_id_tempform_view_img' + i);
							tbody_img.style.cssText = "float:right";
							tr_img = document.createElement('div');
							var img = document.createElement('img');
								img.setAttribute('src', '<?php echo MWD_URL; ?>/images/minus.png?ver=<?php echo get_option("mwd_version"); ?>');
								img.setAttribute('title', 'Show or hide the page');
								img.setAttribute("class", "page_toolbar");
								img.setAttribute('id', 'show_page_img_' + i);
								img.setAttribute('onClick', 'show_or_hide("' + i + '")');
								img.setAttribute("onmouseover", 'chnage_icons_src(this,"minus")');
								img.setAttribute("onmouseout", 'chnage_icons_src(this,"minus")');
							var img_X = document.createElement("img");
								img_X.setAttribute("src", "<?php echo MWD_URL; ?>/images/page_delete.png?ver=<?php echo get_option("mwd_version"); ?>");
								img_X.setAttribute('title', 'Delete the page');
								img_X.setAttribute("class", "page_toolbar");
								img_X.setAttribute("onclick", 'remove_page("' + i + '")');
								img_X.setAttribute("onmouseover", 'chnage_icons_src(this,"page_delete")');
								img_X.setAttribute("onmouseout", 'chnage_icons_src(this,"page_delete")');
							var img_X_all = document.createElement("img");
								img_X_all.setAttribute("src", "<?php echo MWD_URL; ?>/images/page_delete_all.png?ver=<?php echo get_option("mwd_version"); ?>");
								img_X_all.setAttribute('title', 'Delete the page with fields');
								img_X_all.setAttribute("class", "page_toolbar");
								img_X_all.setAttribute("onclick", 'remove_page_all("' + i + '")');
								img_X_all.setAttribute("onmouseover", 'chnage_icons_src(this,"page_delete_all")');
								img_X_all.setAttribute("onmouseout", 'chnage_icons_src(this,"page_delete_all")');
							var img_EDIT = document.createElement("img");
								img_EDIT.setAttribute("src", "<?php echo MWD_URL; ?>/images/page_edit.png?ver=<?php echo get_option("mwd_version"); ?>");
								img_EDIT.setAttribute('title', 'Edit the page');
								img_EDIT.setAttribute("class", "page_toolbar");
								img_EDIT.setAttribute("onclick", 'edit_page_break("' + i + '")');
								img_EDIT.setAttribute("onmouseover", 'chnage_icons_src(this,"page_edit")');
								img_EDIT.setAttribute("onmouseout", 'chnage_icons_src(this,"page_edit")');
							tr_img.appendChild(img);
							tr_img.appendChild(img_X);
							tr_img.appendChild(img_X_all);
							tr_img.appendChild(img_EDIT);
							tbody_img.appendChild(tr_img);
							document.getElementById('form_id_tempform_view' + i).parentNode.appendChild(tbody_img);
						}
					}

					if (form_view_count > 1) {
						for (i = 1; i <= form_view_max; i++) {
							if (document.getElementById('form_id_tempform_view' + i)) {
								first_form_view = i;
								break;
							}
						}
						form_view = form_view_max;
						need_enable = false;
						generate_page_nav(first_form_view);
						var img_EDIT = document.createElement("img");
							img_EDIT.setAttribute("src", "<?php echo MWD_URL . '/images/edit.png?ver='.get_option("mwd_version"); ?>");
							img_EDIT.style.cssText = "margin-left:40px; cursor:pointer";
							img_EDIT.setAttribute("onclick", 'el_page_navigation()');
						var td_EDIT = document.getElementById("edit_page_navigation");
							td_EDIT.appendChild(img_EDIT);
						document.getElementById('page_navigation').appendChild(td_EDIT);
					}
					document.getElementById('araqel').value = 1;

				}
				jQuery(window).load(function () {
					formOnload();
				});
				jQuery(function() {
					jQuery('.wdform_section .wdform_column:last-child').each(function() {
						jQuery(this).parent().append(jQuery('<div></div>').addClass("wdform_column"));
					});

					sortable_columns();
					if(<?php echo $row->sortable ?>==1) {
						jQuery( ".wdform_arrows" ).hide();
						all_sortable_events();
					}
					else
						jQuery('.wdform_column').sortable( "disable" );

				});
			</script>
			<?php
			} else { ?>
				<script type="text/javascript">
					lists = '<?php echo addcslashes(json_encode($lists), "'"); ?>';
					mergeParams = JSON.parse('<?php echo addcslashes(json_encode($mergeParams, true), "'"); ?>');
					groupParams = JSON.parse('<?php echo addcslashes(json_encode($groupParams, true), "'"); ?>');
					jQuery(function() {
						jQuery('.wdform_section .wdform_column:last-child').each(function() {
							jQuery(this).parent().append(jQuery('<div></div>').addClass("wdform_column"));
						});
						sortable_columns();
						all_sortable_events();
					});
				</script>
			<?php } ?>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
			<input type="hidden" id="task" name="task" value=""/>
			<input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>" />
		</form>
		<?php
	}

	public function get_labels($row) {
		$label_id = array();
		$label_label = array();
		$label_type = array();
		$label_all = explode('#****#', $row->label_order_current);
		$label_all = array_slice($label_all, 0, count($label_all) - 1);

		$email_select = '';
		$reg_select='<option value="0">Select a field</option>';

		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			array_push($label_id, $label_id_each[0]);
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			array_push($label_label, $label_order_each[0]);
			array_push($label_type, $label_order_each[1]);

			if($label_order_each[1]=='type_submitter_mail') {
				$email_select.='<option value="'.$label_id_each[0].'">'.addslashes($label_order_each[0]).'</option>';
			}
		}

		return array($label_id, $label_label, $label_type, $email_select);
	}

	public function form_options($id) {
		global $wpdb;
		$row = $this->model->get_row_data($id);
		$themes = $this->model->get_theme_rows_data();
		$page_title = 'Options: '.$row->title;

		$allParamsLabels = $this->get_labels($row);

		$label_id = $allParamsLabels[0];
		$label_label = $allParamsLabels[1];
		$label_type = $allParamsLabels[2];
		$email_select = $allParamsLabels[3];

		$fields = explode('*:*id*:*type_submitter_mail*:*type*:*', $row->form_fields);
		$fields_count = count($fields);

		$is_email = $email_select ? 1 : 0;
		$default_theme = $wpdb->get_var('SELECT id FROM ' . $wpdb->prefix . 'mwd_themes where `default`=1');
		
		MWD_Library::mwd_upgrade_pro('form_options'); 
		?>
		<script>
			gen = "<?php echo $row->counter; ?>";
			form_view_max = 20;
			function set_preview() {
				jQuery("#preview_form").attr("onclick", "tb_show('', '<?php echo add_query_arg(array('action' => 'FormsPreview', 'form_id' => $row->id,  'form_preview' => 1), admin_url('admin-ajax.php')); ?>&test_theme=" + jQuery('#theme').val() + "&width=1000&height=500&TB_iframe=1'); return false;");
			}
		</script>
		
		<form class="wrap" method="post" action="admin.php?page=manage_forms" style="width:99%;" name="adminForm" id="adminForm">
			<?php wp_nonce_field('nonce_mwd', 'nonce_mwd'); ?>
			<div class="mwd-page-header">
				<div class="mwd-logo">
				</div>
				<div class="mwd-page-title"><?php echo $page_title; ?></div>
				<div class="mwd-page-actions">
					<button class="mwd-button save-button small" onclick="if (mwd_check_email('mailToAdd') || mwd_check_email('paypal_email') || mwd_check_req(<?php echo $is_email; ?>)) {return false;}; set_condition(); mwd_apply_options('save_options');">
						<span></span>
						Save
					</button>
					<button class="mwd-button apply-button small" onclick="if (mwd_check_email('mailToAdd') ||  mwd_check_email('from_mail') || mwd_check_email('paypal_email') || mwd_check_req(<?php echo $is_email; ?>)) {return false;}; set_condition(); mwd_apply_options('apply_options');">
						<span></span>
						Apply
					</button>
					<button class="mwd-button cancel-button small" onclick="mwd_set_input_value('task', 'cancel_options');">
						<span></span>
						Cancel
					</button>
				</div>
				<div class="mwd-clear"></div>
			</div>
			<div class="mwd-form-options">
				<div class="submenu-box">
					<div class="submenu-pad">
						<ul id="submenu" class="configuration">
							<li>
								<a id="general" class="mwd_fieldset_tab" onclick="form_maker_options_tabs('general')" href="#">General Options</a>
							</li>
							<li>
								<a id="mailchimp" class="mwd_fieldset_tab" onclick="form_maker_options_tabs('mailchimp')" href="#">MailChimp Options</a>
							</li>
							<li>
								<a id="custom" class="mwd_fieldset_tab" onclick="form_maker_options_tabs('custom')" href="#">Email Options</a>
							</li>
							<li>
								<a id="messages" class="mwd_fieldset_tab" onclick="form_maker_options_tabs('messages')" href="#">Custom Messages</a>
							</li>
							<li>
								<a id="actions" class="mwd_fieldset_tab" onclick="form_maker_options_tabs('actions')" href="#">Actions after Submission</a>
							</li>
							<li>
								<a id="payment" class="mwd_fieldset_tab" onclick="form_maker_options_tabs('payment')" href="#">Paypal Options</a>
							</li>
							<li>
								<a id="javascript" class="mwd_fieldset_tab" onclick="form_maker_options_tabs('javascript'); codemirror_for_javascript();" href="#">JavaScript</a>
							</li>
							<li>
								<a id="conditions" class="mwd_fieldset_tab" onclick="form_maker_options_tabs('conditions')" href="#">Conditional Fields</a>
							</li>
						</ul>
					</div>
				</div>
				<fieldset id="general_fieldset" class="adminform mwd_fieldset_deactive">
					<legend>General Options</legend>
						<table class="admintable" style="float: left;">
							<tr valign="top">
								<td class="mwd_options_label">
									<label>Published</label>
								</td>
								<td class="mwd_options_value">
									<button class="mwd-checkbox-radio-button <?php echo $row->published == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->published; ?>">
										<span></span>
									</button>
									<input type="hidden" name="published" value="<?php echo $row->published; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label>Save data(to database)</label>
								</td>
								<td class="mwd_options_value">
									<button class="mwd-checkbox-radio-button <?php echo $row->savedb == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->savedb; ?>">
										<span></span>
									</button>
									<input type="hidden" name="savedb" value="<?php echo $row->savedb; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="theme">Theme</label>
								</td>
								<td class="mwd_options_value">
									<select id="theme" name="theme" onChange="set_preview()">
										<option value="0" <?php echo $row->theme && $row->theme == 0 ? 'selected' : '' ?>>Inherit From Website Theme</option>
										<?php
										foreach ($themes as $theme) {
											?>
											<option value="<?php echo $theme->id; ?>" <?php echo (($theme->id == $row->theme) ? 'selected' : ''); ?>><?php echo $theme->title; ?></option>
											<?php
										}
										?>
									</select>
									<button id="preview_form" class="mwd-button preview-button small" onclick="tb_show('', '<?php echo add_query_arg(array('action' => 'FormsPreview', 'form_id' => $row->id, 'test_theme' => $row->theme, 'form_preview' => 1, 'width' => '1000', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>'); return false;">
										<span></span>
										Preview
									</button>
									<button id="edit_css" class="mwd-button options-edit-button small" onclick="window.open('<?php echo add_query_arg(array('current_id' => ($row->theme ? $row->theme : $default_theme), 'nonce_mwd' => wp_create_nonce('nonce_mwd')), admin_url('admin.php?page=themes&task=edit')); ?>'); return false;">
										<span></span>
										Edit
									</button>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="requiredmark">Hide labels</label>
								</td>
								<td class="mwd_options_value">
									<button class="mwd-checkbox-radio-button <?php echo $row->hide_labels == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->hide_labels; ?>">
										<span></span>
									</button>
									<input type="hidden" name="hide_labels" value="<?php echo $row->hide_labels; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="requiredmark">Required fields mark</label>
								</td>
								<td class="mwd_options_value">
									<input type="text" id="requiredmark" name="requiredmark" value="<?php echo $row->requiredmark; ?>" style="width:250px;" />
								</td>
							</tr>
						</table>
				</fieldset>

				<fieldset id="mailchimp_fieldset" class="adminform mwd_fieldset_deactive">
					<?php
						$display = '';
						if(!$is_email) {
							echo MWD_Library::message('Please add email field to your form to use this option.', 'error');
							$display = 'style="display: none;"';
						}
					?>
					<div <?php echo $display; ?>>
					<legend>List Options</legend>
					<table class="admintable" style="float: left;">
						<tr valign="top">
							<td  class="mwd_options_label">
								<label title="Defines the action - either subscribe or unsubscribe the user">Action: </label>
							</td>
							<td class="mwd_options_value" >
								<select id="subscribe_action" name="subscribe_action" onchange="change_hide_show('mwd-action')" <?php echo (in_array('type_action', $label_type) ? 'disabled="disabled"' : '')?>>
									<option value="1" <?php echo $row->subscribe_action == 1 ? 'selected="selected"' : ''?>>Subscribe</option>
									<option value="0" <?php echo $row->subscribe_action == 0 ? 'selected="selected"' : ''?>>Unsubscribe</option>
								</select>
								<div class="<?php echo (in_array('type_action', $label_type) ? 'mwd-show' : 'mwd-hide')?>">You have already chosen the action of the form.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label" style="vertical-align: middle;">
								<label> Email Type: </label>
							</td>
							<td class="mwd_options_value">
								<button name="mail_mode_user"class="mwd-checkbox-radio-button <?php echo $row->mail_mode_user == 1 ? 'mwd-text-yes' : 'mwd-text-no' ?> medium" onclick="mwd_change_radio_checkbox_text(this); return false;" value="<?php echo $row->mail_mode_user  ?>">
									<label><?php echo $row->mail_mode_user == 1 ? 'HTML' : 'Text' ?></label>
									<span></span>
								</button>
								<input type="hidden" name="mail_mode_user" value="<?php echo $row->mail_mode_user; ?>"/>
							</td>
						</tr>
					</table>

					<legend>MailChimp Options</legend>
					<table class="admintable mwd-action <?php echo $row->subscribe_action == 1 || in_array('type_action', $label_type) ? 'mwd-show-table' : 'mwd-hide' ?>" style="float: left;">
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Double Opt-in?</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->double_optin == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); mwd_show_hide('welcome_email'); return false;" value="<?php echo $row->double_optin; ?>">
									<span></span>
								</button>
								<input type="hidden" name="double_optin" value="<?php echo $row->double_optin; ?>"/>
								<div>Double opt-in requires confirmation from user in advance to being added to a list.  (recommended)</div>
							</td>
						</tr>
						<tr valign="top" class="welcome_email <?php echo $row->double_optin == 1 ? 'mwd-hide' : 'mwd-show' ?> ">
							<td class="mwd_options_label">
								<label>Send Welcome Email?</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->welcome_email == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->welcome_email; ?>">
									<span></span>
								</button>
								<input type="hidden" name="welcome_email" value="<?php echo $row->welcome_email; ?>"/>
								<div>If double opt-in is not enabled you can instead send a "Welcome Email" to your list subscribers by enabling this option.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Update existing subscribers?</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->update_subscriber == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); mwd_show_hide('replace_interest_groups'); return false;" value="<?php echo $row->update_subscriber; ?>">
									<span></span>
								</button>
								<input type="hidden" name="update_subscriber" value="<?php echo $row->update_subscriber; ?>"/>
								<div>Enable the option to update (rewrite) previously submitted user data for existing subscribers (instead of showing the "already subscribed" message).</div>
							</td>
						</tr>
						<tr valign="top" class="replace_interest_groups <?php echo $row->update_subscriber == 0 ? 'mwd-hide' : 'mwd-show' ?>">
							<td class="mwd_options_label">
								<label>Replace interest groups?</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->replace_interest_groups == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->replace_interest_groups; ?>">
									<span></span>
								</button>
								<input type="hidden" name="replace_interest_groups" value="<?php echo $row->replace_interest_groups; ?>"/>
								<div>Enable the option to update(rewrite) interest groups of existing subscribers instead of expanding the current group list(only when updating a subscriber).</div>
							</td>
						</tr>
					</table>
					<table class="admintable mwd-action <?php echo  in_array('type_action', $label_type) || $row->subscribe_action != 1 ? 'mwd-show-table' : 'mwd-hide' ?>" style="float: left;">
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Delete Subscriber</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->delete_member == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->delete_member; ?>">
									<span></span>
								</button>
								<input type="hidden" name="delete_member" value="<?php echo $row->delete_member; ?>"/>
								<div>Enable the option if you want to delete the subscriber instead of unsubscribing.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Send Unsubscribe</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->send_goodbye == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->send_goodbye; ?>">
									<span></span>
								</button>
								<input type="hidden" name="send_goodbye" value="<?php echo $row->send_goodbye; ?>"/>
								<div>Enable the option to send a "Unsubscribe Email" to the unsubscribed/deleted user.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Send Notification</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->send_notify == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->send_notify; ?>">
									<span></span>
								</button>
								<input type="hidden" name="send_notify" value="<?php echo $row->send_notify; ?>"/>
								<div>Select "yes" if you want to send the unsubscribe notification email to the address defined in the list email notification settings.</div>
							</td>
						</tr>
					</table>
					</div>
				</fieldset>

				<fieldset id="custom_fieldset" class="adminform mwd_fieldset_deactive">
					<?php
						$diasbled = '';
						if(!$email_select) {
							$row->use_mailchimp_email = 0;
							$diasbled = 'disabled="disabled"';
						}
					?>
					<legend>Mailchimp Email Options</legend>
					<table class="admintable">
						<tr valign="top">
							<td style="width: 150px; vertical-align: middle;">
								<label>Use MailChimp Globals</label>
							</td>
							<td style="padding: 15px;" >
								<button class="mwd-checkbox-radio-button <?php echo $row->use_mailchimp_email == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); mwd_show_hide('mwd_mail_options'); return false;" value="<?php echo $row->use_mailchimp_email; ?>" <?php echo $diasbled; ?>>
									<span></span>
								</button>
								<input type="hidden" name="use_mailchimp_email" value="<?php echo $row->use_mailchimp_email; ?>"/>
							</td>
							<?php if(!$email_select) {
								echo '<td>'.MWD_Library::message('Please add email field to your form to use this option.', 'error').'</td>';
							} ?>
						</tr>
					</table>
					<fieldset class="mwd_mail_options <?php echo $row->use_mailchimp_email == 1 ? 'mwd-hide' : 'mwd-show' ?>">
						<legend style="color: #46ACC3;">Email Options templates correspond to the Action (Subscribe/Unsubscribe) set in MailChimp Options.</legend>
						<legend>Email to User</legend>
						<table class="admintable">
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="mail_from_user">Email From</label>
								</td>
								<td class="mwd_options_value">
									<?php $choise = "document.getElementById('mail_from_user')"; ?>
									<input type="text" id="mail_from_user" name="mail_from_user" value="<?php echo $row->mail_from_user; ?>" style="width: 250px; display: block;" />
									<input style="border: 1px solid silver; font-size: 10px;" type="button" value="List:From Email" onClick="insertAtCursor(<?php echo $choise; ?>,'list_from_email')" />
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="mail_from_name_user">From Name</label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_list('mail_from_name_user', $row, 'name_from'); ?>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="reply_to_user">Reply to<br />(if different from "Email Form")</label>
								</td>
								<td class="mwd_options_value">
									<?php $choise = "document.getElementById('reply_to_user')"; ?>
									<input type="text" id="reply_to_user" name="reply_to_user" value="<?php echo $row->reply_to_user; ?>" style="width:250px; display:block;"/>
									<input style="border: 1px solid silver; font-size: 10px;" type="button" value="List:From Email" onClick="insertAtCursor(<?php echo $choise; ?>,'list_from_email')" />
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label>CC:</label>
								</td>
								<td class="mwd_options_value">
									<input type="text" id="mail_cc_user" name="mail_cc_user" value="<?php echo $row->mail_cc_user ?>" style="width:250px;" />
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label>BCC:</label>
								</td>
								<td class="mwd_options_value">
									<input type="text" id="mail_bcc_user" name="mail_bcc_user" value="<?php echo $row->mail_bcc_user ?>" style="width:250px;" />
								</td>
							</tr>
							<tr class="mwd-action <?php echo in_array('type_action', $label_type) || $row->subscribe_action == 1 ? 'mwd-show' : 'mwd-hide' ?>">
								<td class="mwd_options_label">
									<label>Subject (Opt-in Confirmation):</label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_list('mail_subject_user', $row, 'subject'); ?>
								</td>
							</tr>
							<tr class="mwd-action <?php echo in_array('type_action', $label_type) || $row->subscribe_action == 1 ? 'mwd-show' : 'mwd-hide' ?>">
								<td class="mwd_options_label">
									<label>Subject (Final Welcome):</label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_list('mail_subject_user_final', $row, 'subject'); ?>
								</td>
							</tr>
							<tr class="mwd-action <?php echo !in_array('type_action', $label_type) && $row->subscribe_action == 1 ? 'mwd-hide' : 'mwd-show' ?>">
								<td class="mwd_options_label">
									<label>Subject (Unsubscribe):</label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_list('mail_subject_unsubscribe', $row, 'subject'); ?>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label" style="vertical-align: middle;">
									<label>Send attachments: </label>
								</td>
								<td class="mwd_options_value">
									<button name="mail_attachment_user" class="mwd-checkbox-radio-button <?php echo $row->mail_attachment_user == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->mail_attachment_user; ?>">
										<span></span>
									</button>
									<input type="hidden" name="mail_attachment_user" value="<?php echo $row->mail_attachment_user; ?>"/>
								</td>
							</tr>
							<tr class="mwd-action <?php echo in_array('type_action', $label_type) || $row->subscribe_action == 1 ? 'mwd-show' : 'mwd-hide' ?>">
								<td class="mwd_options_label" valign="top">
									<label>Opt-in confirmation email</label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_buttons('optin_confirmation_email', $row, 'confirmation'); ?>
								</td>
							</tr>
							<tr class="mwd-action <?php echo in_array('type_action', $label_type) || $row->subscribe_action == 1 ? 'mwd-show' : 'mwd-hide' ?>">
								<td class="mwd_options_label" valign="top">
									<label>Final "welcome" email</label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_buttons('final_welcome_email', $row, 'unsubscribe'); ?>
								</td>
							</tr>
							<tr class="mwd-action <?php echo !in_array('type_action', $label_type) && $row->subscribe_action == 1 ? 'mwd-hide' : 'mwd-show' ?>">
								<td class="mwd_options_label" valign="top">
									<label>Unsubscribe email</label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_buttons('goodbye_email', $row, 'goodbye'); ?>
								</td>
							</tr>
						</table>
					</fieldset>

					<fieldset class="adminform mwd_mail_options <?php echo $row->use_mailchimp_email == 1 ? 'mwd-hide' : 'mwd-show' ?>">
						<legend>Email to Administrator</legend>
						<table class="admintable">
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="mailToAdd">Email to send submissions to</label>
								</td>
								<td class="mwd_options_value">
									<input type="text" id="mailToAdd" name="mailToAdd" style="width: 250px;" />
									<input type="hidden" id="mail" name="mail" value="<?php echo $row->mail . ($row->mail && (substr($row->mail, -1) != ',') ? ',' : ''); ?>" />
									<img src="<?php echo MWD_URL . '/images/add.png?ver='. get_option("mwd_version"); ?>" style="vertical-align: middle; cursor: pointer;" title="Add more emails" onclick="if (mwd_check_email('mailToAdd')) {return false;};cmwd_create_input('mail', 'mailToAdd', 'cmwd_mail_div', '<?php echo MWD_URL; ?>')" />
									<div id="cmwd_mail_div">
										<?php
										$mail_array = explode(',', $row->mail);
										foreach ($mail_array as $mail) {
											if ($mail && $mail != ',') {
												?>
												<div class="mwd_mail_input">
													<?php echo $mail; ?>
													<img src="<?php echo MWD_URL; ?>/images/delete.png?ver=<?php echo get_option("mwd_version"); ?>" class="mwd_delete_img" onclick="mwd_delete_mail(this, '<?php echo $mail; ?>')" title="Delete Email" />
												</div>
												<?php
											}
										}
										?>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="from_mail">Email From</label>
								</td>
								<td class="mwd_options_value">
									<?php
									$choise = "document.getElementById('mail_from_other')";
									$is_other = TRUE;
									for ($i = 0; $i < $fields_count - 1; $i++) {
										?>
										<div>
											 <input type="radio" name="from_mail" id="from_mail<?php echo $i; ?>" value="<?php echo (strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]); ?>"  <?php echo ((strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]) == $row->from_mail ? 'checked="checked"' : '' ); ?> onclick="wdhide('mail_from_other')" />
											<label for="from_mail<?php echo $i; ?>"><?php echo substr($fields[$i + 1], 0, strpos($fields[$i + 1], '*:*w_field_label*:*')); ?></label>
										</div>
										<?php
										if(strlen($fields[$i])!=1) {
											if (substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*') + 15, strlen($fields[$i])) == $row->from_mail) {
												$is_other = FALSE;
											}
										}
										else {
											if($fields[$i] == $row->from_mail)
												$is_other=false;
										}
									}
									?>
									<div style="<?php echo ($fields_count == 1) ? 'display:none;' : ''; ?>">
										<input type="radio" id="other" name="from_mail" value="other" <?php echo ($is_other) ? 'checked="checked"' : ''; ?> onclick="wdshow('mail_from_other')" />
										<label for="other">Other</label>
									</div>
									<input type="text" style="width: <?php echo ($fields_count == 1) ? '250px' : '235px; margin-left: 15px' ?>; display: <?php echo ($is_other) ? 'block;' : 'none;'; ?>" id="mail_from_other" name="mail_from_other" value="<?php echo ($is_other) ? $row->from_mail : ''; ?>" />
									<input style="border: 1px solid silver; font-size: 10px;" type="button" value="List:From Email" onClick="insertAtCursor(<?php echo $choise; ?>,'list_from_email')" />
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="from_name">From Name</label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_list('from_name', $row, 'name_from'); ?>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label for="reply_to">Reply to<br/>(if different from "Email From") </label>
								</td>
								<td class="mwd_options_value">
									<?php
									$choise = "document.getElementById('reply_to_other')";
									$is_other = TRUE;
									for ($i = 0; $i < $fields_count - 1; $i++) {
										?>
										<div>
											<input type="radio" name="reply_to" id="reply_to<?php echo $i; ?>" value="<?php echo (strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]); ?>"  <?php echo ((strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]) == $row->reply_to ? 'checked="checked"' : '' ); ?> onclick="wdhide('reply_to_other')" />
											<label for="reply_to<?php echo $i; ?>"><?php echo substr($fields[$i + 1], 0, strpos($fields[$i + 1], '*:*w_field_label*:*')); ?></label>
										</div>
										<?php
										if(strlen($fields[$i])!=1) {
											if (substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*') + 15, strlen($fields[$i])) == $row->reply_to) {
												$is_other = FALSE;
											}
										}
										else {
											if($fields[$i] == $row->reply_to)
												$is_other=false;
										}
									}
									?>
									<div style="<?php echo ($fields_count == 1) ? 'display: none;' : ''; ?>">
										<input type="radio" id="other1" name="reply_to" value="other" <?php echo ($is_other) ? 'checked="checked"' : ''; ?> onclick="wdshow('reply_to_other')" />
										<label for="other1">Other</label>
									</div>
									<input type="text" style="width: <?php echo ($fields_count == 1) ? '250px' : '235px; margin-left: 15px'; ?>; display: <?php echo ($is_other) ? 'block;' : 'none;'; ?>" id="reply_to_other" name="reply_to_other" value="<?php echo ($is_other && $row->reply_to) ? $row->reply_to : ''; ?>" />
									<input style="border: 1px solid silver; font-size: 10px;" type="button" value="List:From Email" onClick="insertAtCursor(<?php echo $choise; ?>,'list_from_email')" />
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label> CC: </label>
								</td>
								<td class="mwd_options_value">
									<input  type="text" id="mail_cc" name="mail_cc" value="<?php echo $row->mail_cc ?>" style="width:250px;" />
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label> BCC: </label>
								</td>
								<td class="mwd_options_value">
									<input type="text" id="mail_bcc" name="mail_bcc" value="<?php echo $row->mail_bcc ?>" style="width:250px;" />
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label">
									<label> Subject: </label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_list('mail_subject', $row, 'subject'); ?>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label" style="vertical-align: middle;">
									<label> Mode: </label>
								</td>
								<td class="mwd_options_value">
									<button name="mail_mode" class="mwd-checkbox-radio-button <?php echo $row->mail_mode == 1 ? 'mwd-text-yes' : 'mwd-text-no' ?> medium" onclick="mwd_change_radio_checkbox_text(this); return false;" value="<?php echo $row->mail_mode  ?>">
										<label><?php echo $row->mail_mode == 1 ? 'HTML' : 'Text' ?></label>
										<span></span>
									</button>
									<input type="hidden" name="mail_mode" value="<?php echo $row->mail_mode; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label" style="vertical-align: middle;">
									<label>Send attachments: </label>
								</td>
								<td class="mwd_options_value">
									<button class="mwd-checkbox-radio-button <?php echo $row->mail_attachment == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->mail_attachment; ?>">
										<span></span>
									</button>
									<input type="hidden" name="mail_attachment" value="<?php echo $row->mail_attachment; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="mwd_options_label" style="vertical-align: middle;">
									<label>Exclude empty fields: </label>
								</td>
								<td class="mwd_options_value">
									<button class="mwd-checkbox-radio-button <?php echo $row->mail_emptyfields == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->mail_emptyfields; ?>">
										<span></span>
									</button>
									<input type="hidden" name="mail_emptyfields" value="<?php echo $row->mail_emptyfields; ?>"/>
								</td>
							</tr>
							<tr>
								<td class="mwd_options_label" valign="top">
									<label>Custom Text in Email For Administrator</label>
								</td>
								<td class="mwd_options_value">
									<?php echo $this->get_fields_buttons('script_mail', $row, ''); ?>
								</td>
							</tr>
						</table>
					</fieldset>
				</fieldset>

				<fieldset id="messages_fieldset" class="adminform mwd_fieldset_deactive">
					<legend>Custom Messages</legend>
					<table class="admintable">
						<tr valign="top">
							<td colspan="2">
								<div style="margin-bottom: 10px;">
									Enter your custom messages for this form below. Leave the field blank to use the default global error message.
								</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Subscribe Message</label>
							</td>
							<td class="mwd_options_value">
								<?php echo $this->get_fields_buttons('success_message', $row, ''); ?>
								<div>The message displayed to the user after they have submitted the form and the data has been successfully sent to MailChimp.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Unsubscribe Message</label>
							</td>
							<td class="mwd_options_value">
								<?php echo $this->get_fields_buttons('unsubscribe_message', $row, 'goodbye'); ?>
								<div>The message displayed to the user after they have submitted the form and the data has been successfully sent to MailChimp.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Update Message</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" id="update_message" name="update_message" style="width: 250px;" value="<?php echo $row->update_message; ?>"/>
								<div>The message displayed to the user after they have submitted the form and the data has been successfully modified in MailChimp list.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>General Error Message</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" id="gen_error_message" name="gen_error_message" style="width: 250px;" value="<?php echo $row->gen_error_message; ?>"/>
								<div>The message displayed to the user after a generic error has occurred.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Invalid Email</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" id="invalid_email_message" name="invalid_email_message" style="width: 250px;" value="<?php echo $row->invalid_email_message; ?>"/>
								<div>The message displayed to the user after they have entered a non-valid email address.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Already Subscribed Email</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" id="already_subscribed_message" name="already_subscribed_message" style="width: 250px;" value="<?php echo $row->already_subscribed_message; ?>"/>
								<div>The message displayed to the user after they attempt to sign up for a mailing list using an email address that is already subscribed.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Required Message</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" id="required_message" name="required_message" style="width: 250px;" value="<?php echo $row->required_message; ?>"/>
								<div>The message displayed to the user after they attempt to sign up for a mailing list without filling a required field (required either by form or MailChimp list), e.g. Name field is required.</div>
							</td>
						</tr>

						<tr valign="top">
							<td class="mwd_options_label">
								<label>Not subscribed</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" id="not_subscribed_message" name="not_subscribed_message" style="width: 250px;" value="<?php echo $row->not_subscribed_message; ?>"/>
								<div>When using the unsubscribe method, this is the text that shows when the given email address is not on the selected list(s).</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Empty Submit</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" id="empty_submit_message" name="empty_submit_message" style="width: 250px;" value="<?php echo $row->empty_submit_message; ?>"/>
								<div>The message displayed to the user when form doesn't contain any input fields to be submitted.</div>
							</td>
						</tr>
					</table>
				</fieldset>

				<fieldset id="actions_fieldset" class="adminform mwd_fieldset_deactive">
					<legend>Actions after submission</legend>
					<table class="admintable">
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Action type</label>
							</td>
							<td class="mwd_options_value">
								<div>
									<input type="radio" name="submit_text_type" id="text_type_hide_form" onclick="set_type('hide_form')" value="2" <?php echo ($row->submit_text_type != 1 && $row->submit_text_type != 3 && $row->submit_text_type != 4 && $row->submit_text_type != 5) ? "checked" : ""; ?> />
									<label for="text_type_hide_form">Hide Form</label>
								</div>
								<div>
									<input type="radio" name="submit_text_type" id="text_type_none" onclick="set_type('none')" value="1" <?php echo $row->submit_text_type == 1  ? "checked" : ""; ?> />
									<label for="text_type_none">Stay on Form</label>
								</div>

								<div>
									<input type="radio" name="submit_text_type" id="text_type_post" onclick="set_type('post')" value="3" <?php echo ($row->submit_text_type == 3) ? "checked" : ""; ?> />
									<label for="text_type_post">Post</label>
								</div>
								<div>
									<input type="radio" name="submit_text_type" id="text_type_page" onclick="set_type('page')" value="4" <?php echo ($row->submit_text_type == 4) ? "checked" : ""; ?> />
									<label for="text_type_page">Page</label>
								</div>
								<div>
									<input type="radio" name="submit_text_type" id="text_type_url" onclick="set_type('url')" value="5" <?php echo ($row->submit_text_type == 5) ? "checked" : ""; ?> />
									<label for="text_type_url">URL</label>
								</div>
							</td>
						</tr>
						<tr id="post" <?php echo (($row->submit_text_type != 3) ? 'style="display:none"' : ''); ?>>
							<td class="mwd_options_label">
								<label for="post_name">Post</label>
							</td>
							<td class="mwd_options_value">
								<select id="post_name" name="post_name">
									<option value="0">- Select Post -</option>
									<?php
									$args = array('posts_per_page'  => 10000);
									query_posts($args);
									while (have_posts()) : the_post(); ?>
									<option value="<?php $x = get_permalink(get_the_ID()); echo $x; ?>" <?php echo (($row->article_id == $x) ? 'selected="selected"' : ''); ?>><?php the_title(); ?></option>
									<?php
									endwhile;
									wp_reset_query();
									?>
								</select>
							</td>
						</tr>
						<tr id="page" <?php echo (($row->submit_text_type != 4) ? 'style="display:none"' : ''); ?>>
							<td class="mwd_options_label">
								<label for="page_name">Page</label>
							</td>
							<td class="mwd_options_value">
								<select id="page_name" name="page_name">
									<option value="0">- Select Page -</option>
									<?php
									$pages = get_pages();
									foreach ($pages as $page) {
										$page_id = get_page_link($page->ID);
										?>
										<option value="<?php echo $page_id; ?>" <?php echo (($row->article_id == $page_id) ? 'selected="selected"' : ''); ?>><?php echo $page->post_title; ?></option>
										<?php
									}
									wp_reset_query();
									?>
								</select>
							</td>
						</tr>

						<tr id="url" <?php echo (($row->submit_text_type != 5 ) ? 'style="display:none"' : ''); ?>>
							<td class="mwd_options_label">
								<label for="url">URL</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" id="url" name="url" style="width:300px" value="<?php echo $row->url; ?>" />
							</td>
						</tr>
					</table>
				</fieldset>

				<fieldset id="payment_fieldset" class="adminform mwd_fieldset_deactive">
					<legend>Payment Options</legend>
					<table class="admintable">
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Turn Paypal On</label>
							</td>
							<td class="mwd_options_value">
								<button name="paypal_mode" class="mwd-checkbox-radio-button <?php echo $row->paypal_mode == 1 ? 'mwd-text-yes' : 'mwd-text-no' ?> small" onclick="mwd_change_radio_checkbox_text(this); return false;" value="<?php echo $row->paypal_mode == 1 ? '1' : '0' ?>">
									<label><?php echo $row->paypal_mode == 1 ? 'On' : 'Off' ?></label>
									<span></span>
								</button>
								<input type="hidden" name="paypal_mode" value="<?php echo $row->paypal_mode; ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label>Checkout Mode</label>
							</td>
							<td class="mwd_options_value">
								<button name="checkout_mode" class="mwd-checkbox-radio-button <?php echo $row->checkout_mode == 1 ? 'mwd-text-yes' : 'mwd-text-no' ?> large" onclick="mwd_change_radio_checkbox_text(this); return false;" value="<?php echo $row->checkout_mode == 1 ? '1' : '0' ?>">
									<label><?php echo $row->checkout_mode == 1 ? 'Production' : 'Testmode' ?></label>
									<span></span>
								</button>
								<input type="hidden" name="checkout_mode" value="<?php echo $row->checkout_mode; ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label for="paypal_email">Paypal email</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" name="paypal_email" id="paypal_email" value="<?php echo $row->paypal_email; ?>" class="text_area" style="width:250px">
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label for="payment_currency">Payment Currency</label>
							</td>
							<td class="mwd_options_value">
								<select id="payment_currency" name="payment_currency">
									<option value="USD" <?php echo (($row->payment_currency == 'USD') ? 'selected' : ''); ?>>$ &#8226; U.S. Dollar</option>
									<option value="EUR" <?php echo (($row->payment_currency == 'EUR') ? 'selected' : ''); ?>>&#8364; &#8226; Euro</option>
									<option value="GBP" <?php echo (($row->payment_currency == 'GBP') ? 'selected' : ''); ?>>&#163; &#8226; Pound Sterling</option>
									<option value="JPY" <?php echo (($row->payment_currency == 'JPY') ? 'selected' : ''); ?>>&#165; &#8226; Japanese Yen</option>
									<option value="CAD" <?php echo (($row->payment_currency == 'CAD') ? 'selected' : ''); ?>>C$ &#8226; Canadian Dollar</option>
									<option value="MXN" <?php echo (($row->payment_currency == 'MXN') ? 'selected' : ''); ?>>Mex$ &#8226; Mexican Peso</option>
									<option value="HKD" <?php echo (($row->payment_currency == 'HKD') ? 'selected' : ''); ?>>HK$ &#8226; Hong Kong Dollar</option>
									<option value="HUF" <?php echo (($row->payment_currency == 'HUF') ? 'selected' : ''); ?>>Ft &#8226; Hungarian Forint</option>
									<option value="NOK" <?php echo (($row->payment_currency == 'NOK') ? 'selected' : ''); ?>>kr &#8226; Norwegian Kroner</option>
									<option value="NZD" <?php echo (($row->payment_currency == 'NZD') ? 'selected' : ''); ?>>NZ$ &#8226; New Zealand Dollar</option>
									<option value="SGD" <?php echo (($row->payment_currency == 'SGD') ? 'selected' : ''); ?>>S$ &#8226; Singapore Dollar</option>
									<option value="SEK" <?php echo (($row->payment_currency == 'SEK') ? 'selected' : ''); ?>>kr &#8226; Swedish Kronor</option>
									<option value="PLN" <?php echo (($row->payment_currency == 'PLN') ? 'selected' : ''); ?>>zl &#8226; Polish Zloty</option>
									<option value="AUD" <?php echo (($row->payment_currency == 'AUD') ? 'selected' : ''); ?>>A$ &#8226; Australian Dollar</option>
									<option value="DKK" <?php echo (($row->payment_currency == 'DKK') ? 'selected' : ''); ?>>kr &#8226; Danish Kroner</option>
									<option value="CHF" <?php echo (($row->payment_currency == 'CHF') ? 'selected' : ''); ?>>CHF &#8226; Swiss Francs</option>
									<option value="CZK" <?php echo (($row->payment_currency == 'CZK') ? 'selected' : ''); ?>>Kc &#8226; Czech Koruny</option>
									<option value="ILS" <?php echo (($row->payment_currency == 'ILS') ? 'selected' : ''); ?>>&#8362; &#8226; Israeli Sheqel</option>
									<option value="BRL" <?php echo (($row->payment_currency == 'BRL') ? 'selected' : ''); ?>>R$ &#8226; Brazilian Real</option>
									<option value="TWD" <?php echo (($row->payment_currency == 'TWD') ? 'selected' : ''); ?>>NT$ &#8226; Taiwan New Dollars</option>
									<option value="MYR" <?php echo (($row->payment_currency == 'MYR') ? 'selected' : ''); ?>>RM &#8226; Malaysian Ringgit</option>
									<option value="PHP" <?php echo (($row->payment_currency == 'PHP') ? 'selected' : ''); ?>>&#8369; &#8226; Philippine Peso</option>
									<option value="THB" <?php echo (($row->payment_currency == 'THB') ? 'selected' : ''); ?>>&#xe3f; &#8226; Thai Bahtv</option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<td class="mwd_options_label">
								<label for="tax">Tax</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" name="tax" id="tax" value="<?php echo $row->tax; ?>" class="text_area" style="width: 40px;" onKeyPress="return check_isnum_point(event)"> %
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset id="javascript_fieldset" class="adminform mwd_fieldset_deactive">
					<legend>JavaScript</legend>
					<table class="admintable">
						<tr valign="top">
							<td class="mwd_options_label">
								<label for="javascript">Javascript</label>
							</td>
							<td class="mwd_options_value" style="width:650px;">
								<textarea style="margin: 0px; height: 400px; width: 600px;" cols="60" rows="30" name="javascript" id="form_javascript"><?php echo $row->javascript; ?></textarea>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset id="conditions_fieldset" class="adminform mwd_fieldset_deactive">
					<div>
						<button class="mwd-button add-button large" onclick="add_condition(); return false;">
							Add Condition
							<span></span>
						</button>
					</div>
					<div id="mwd_conditions">
						<span></span>
					</div>
					<script>
						jQuery.ajax({
							url: 'admin-ajax.php?action=conditions&form_id=<?php echo $row->id; ?>',
							method: "POST",
							dataType: "html",
							success:function(data){
								jQuery("#mwd_conditions").html(data);
							},
							error:function(err){
							}
						});
					</script>
					<input type="hidden" id="condition" name="condition" value="<?php echo $row->condition; ?>" />
				</fieldset>

				</div>
				<input type="hidden" name="boxchecked" value="0">
				<input type="hidden" name="fieldset_id" id="fieldset_id" value="<?php echo MWD_Library::get('fieldset_id', 'general'); ?>" />
				<input type="hidden" id="task" name="task" value=""/>
				<input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>" />
			</form>
		<script>
		jQuery(window).load(function () {
			form_maker_options_tabs(jQuery("#fieldset_id").val());
			function hide_email_labels(event) {
				var e = event.toElement || event.relatedTarget;
				if (e.parentNode == this || e == this) {
					return;
				}
				this.style.display="none";
			}

			var allTypes = ['mail_from_labels', 'mail_subject_labels', 'mail_from_name_user_labels', 'mail_subject_user_labels', 'mail_subject_user_final_labels'];
			var m = allTypes.length;
			for(var l=0; l< m; l++){
				if(document.getElementById(allTypes[l])) {
					document.getElementById(allTypes[l]).addEventListener('mouseout',hide_email_labels,true);
				}
			}
		});

		</script>
		<?php
	}

	public function get_fields_list($field_id, $row, $type){
		$allParamsLabels = $this->get_labels($row);
		$label_id = $allParamsLabels[0];
		$label_label = $allParamsLabels[1];
		$label_type = $allParamsLabels[2];


		$allTypes = array(
			'subject' => array(
				'list_subject' => 'Subject',
				'list_id' => 'ID',
				'list_name' => 'Name'
			),
			'name_from' => array(
				'list_from_name' => 'From Name',
				'list_name' => 'Name'
			)
		);

		echo '<input type="text" id="'.$field_id.'" name="'.$field_id.'" value="'.$row->$field_id.'" style="width:250px;" />
		<img src="'. MWD_URL . '/images/add.png?ver='. get_option("mwd_version").'" onclick="document.getElementById(\''.$field_id.'_labels\').style.display=\'block\';" style="vertical-align: middle; cursor: pointer; display:inline-block; margin:0px; float:none;">';

		$choise = "document.getElementById('".$field_id."')";
		echo '<div style="position:relative; top:-3px;"><div id="'.$field_id.'_labels" class="email_labels" style="display:none;">';
		for($i=0; $i<count($label_label); $i++) {
			if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" ||  $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy")
			continue;

			$param = htmlspecialchars(addslashes($label_label[$i]));

			$fld_label = $param;
			if(strlen($fld_label)>30)
			{
				$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);
				$fld_label = explode("\n", $fld_label);
				$fld_label = $fld_label[0] . ' ...';
			}

			echo "<a onClick=\"insertAtCursor(".$choise.",'".$param."'); document.getElementById('".$field_id."_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">".$fld_label."</a>";

		}

		echo "<a onClick=\"insertAtCursor(".$choise.",'subid'); document.getElementById('".$field_id."_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Submission ID</a>";

		echo "<a onClick=\"insertAtCursor(".$choise.",'username'); document.getElementById('".$field_id."_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";

		if(isset($allTypes[$type])){
			foreach($allTypes[$type] as $key => $value){
				echo "<a onClick=\"insertAtCursor(".$choise.", '".$key."'); document.getElementById('".$field_id."_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">List: ".$value."</a>";
			}
		}


		echo '</div></div>';

		if(isset($allTypes[$type])){
			foreach($allTypes[$type] as $key => $value){
				echo '<input style="border: 1px solid silver; font-size: 10px;" type="button" value="List:'.$value.'" onClick="insertAtCursor('.$choise.', \''.$key.'\')" /> ';
			}
		}

	}

	public function get_fields_buttons($field_id, $row, $type){
		$allParamsLabels = $this->get_labels($row);
		$label_id = $allParamsLabels[0];
		$label_label = $allParamsLabels[1];
		$label_type = $allParamsLabels[2];
		$list_variables = array('list_id' => 'List:ID', 'list_name' => 'List:Name', 'list_owner_email' => 'List:Owner Email Address');
		$custom_variables = array('all' => 'All fields list', 'subid' => 'Submission ID', 'ip' => 'Ip', 'username' => 'Username', 'useremail' => 'User Email');

		$allTypes = array(
			'confirmation' => array(
				'confirmation_link' => 'Confirmation Link'
			),
			'unsubscribe' => array(
				'unsubscribe_link' => 'Unsubscribe Link'
			)
		);

		echo '<div style="margin-bottom:5px">';
		$choise = "document.getElementById('".$field_id."')";
		for ($i = 0; $i < count($label_label); $i++) {
			if ($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" ||  $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button"  || $label_type[$i]=="type_send_copy" || $type == 'goodbye')
			continue;

			$param = htmlspecialchars(addslashes($label_label[$i]));
			$fld_label = $param;
			if(strlen($fld_label)>30) {
				$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);
				$fld_label = explode("\n", $fld_label);
				$fld_label = $fld_label[0] . ' ...';
			}

			if($label_type[$i]=="type_file_upload")
				$fld_label .= '(as image)';

			echo '<input style="border: 1px solid silver; font-size: 10px;" type="button" value="'.$fld_label.'" onClick="insertAtCursor('.$choise.', \''.$param.'\')" /> ';

		}
		echo '<hr />';
		foreach($custom_variables as $custom_key => $custom_variable) {
			if($type != 'goodbye')
				echo '<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="'.$custom_variable.'" onClick="insertAtCursor('.$choise.', \''.$custom_key.'\')" />';
		}

		foreach($list_variables as $list_key => $list_variable) {
			echo '<input style="border: 1px solid silver; font-size: 10px; margin:3px;" type="button" value="'.$list_variable.'" onClick="insertAtCursor('.$choise.', \''.$list_key.'\')" />';
		}

	 	if(isset($allTypes[$type])){
			echo '<hr />';
			foreach($allTypes[$type] as $key => $value){
				echo '<input style="border: 1px solid silver; font-size: 10px;" type="button" value="'.$value.'" onClick="insertAtCursor('.$choise.', \''.$key.'\')" /> ';
			}
		}

		echo '</div>';
		if (user_can_richedit()) {
			wp_editor($row->$field_id, $field_id, array('teeny' => FALSE, 'textarea_name' => $field_id, 'media_buttons' => FALSE, 'textarea_rows' => 5, 'editor_height' => '200px'));
		}
		else {
			echo '<textarea name="'.$field_id.'" id="'.$field_id.'" cols="20" rows="10" style="width:300px; height:450px;">'.$row->$field_id.'</textarea>';
		}

	}

	public function display_options($id) {
		$row_form = $this->model->get_row_data($id);
		$row = $this->model->get_display_options($id);

		$page_title = 'Display Options: '.$row_form->title;
		$animation_effects = array(
			'none' => 'None',
			'bounce' => 'Bounce',
			'tada' => 'Tada',
			'bounceInDown' => 'BounceInDown',
			'fadeInLeft' => 'FadeInLeft',
			'flash' => 'Flash',
			'pulse' => 'Pulse',
			'rubberBand' => 'RubberBand',
			'shake' => 'Shake',
			'swing' => 'Swing',
			'wobble' => 'Wobble',
			'hinge' => 'Hinge',
			'lightSpeedIn' => 'LightSpeedIn',
			'rollIn' => 'RollIn',
			'bounceIn' => 'BounceIn',
			'bounceInLeft' => 'BounceInLeft',
			'bounceInRight' => 'BounceInRight',
			'bounceInUp' => 'BounceInUp',
			'fadeIn' => 'FadeIn',
			'fadeInDown' => 'FadeInDown',
			'fadeInDownBig' => 'FadeInDownBig',
			'fadeInLeftBig' => 'FadeInLeftBig',
			'fadeInRight' => 'FadeInRight',
			'fadeInRightBig' => 'FadeInRightBig',
			'fadeInUp' => 'FadeInUp',
			'fadeInUpBig' => 'FadeInUpBig',
			'flip' => 'Flip',
			'flipInX' => 'FlipInX',
			'flipInY' => 'FlipInY',
			'rotateIn' => 'RotateIn',
			'rotateInDownLeft' => 'RotateInDownLeft',
			'rotateInDownRight' => 'RotateInDownRight',
			'rotateInUpLeft' => 'RotateInUpLeft',
			'rotateInUpRight' => 'RotateInUpRight',
			'zoomIn' => 'ZoomIn',
			'zoomInDown' => 'ZoomInDown',
			'zoomInLeft' => 'ZoomInLeft',
			'zoomInRight' => 'ZoomInRight',
			'zoomInUp' => 'ZoomInUp',
		);

		MWD_Library::mwd_upgrade_pro('display_options'); 
		?>
		
		<form class="wrap" method="post" action="admin.php?page=manage_forms" style="width:99%;" name="adminForm" id="adminForm">
			<?php wp_nonce_field('nonce_mwd', 'nonce_mwd'); ?>
			<div class="mwd-page-header">
				<div class="mwd-logo">
				</div>
				<div class="mwd-page-title"><?php echo $page_title; ?></div>
				<div class="mwd-page-actions">
					<button class="mwd-button save-button small" onclick="mwd_apply_options('save_display_options');">
						<span></span>
						Save
					</button>
					<button class="mwd-button apply-button small" onclick="mwd_apply_options('apply_display_options');">
						<span></span>
						Apply
					</button>
					<button class="mwd-button cancel-button small" onclick="mwd_set_input_value('task', 'cancel_options');">
						<span></span>
						Cancel
					</button>
				</div>
				<div class="mwd-clear"></div>
			</div>
			<div class="mwd-form-options">
				<fieldset id="type_settings_fieldset" class="adminform">
					<div class="mwd-row mwd-form-types">
						<label style="font-size:18px; width: 170px !important; ">Form Type</label>
						<label>
							<input type="radio" name="form_type" value="embedded" onclick="change_form_type('embedded'); change_hide_show('mwd-embedded');"
							<?php echo $row->type == 'embedded' ? 'checked="checked"' : '' ?>>
							<span class="mwd-embedded <?php echo $row->type == 'embedded' ? ' active' : '' ?>"></span>
							<p>Embedded</p>
						</label>
						<label>
							<input type="radio" name="form_type" value="popover" onclick="change_form_type('popover'); change_hide_show('mwd-popover');"
							<?php echo $row->type == 'popover' ? 'checked="checked"' : '' ?>>
							<span class="mwd-popover <?php echo $row->type == 'popover' ? ' active' : '' ?>"></span>
							<p>Popup</p>
						</label>
						<label>
							<input type="radio" name="form_type" value="topbar" onclick="change_form_type('topbar'); change_hide_show('mwd-topbar');"
							<?php echo $row->type == 'topbar' ? 'checked="checked"' : '' ?>>
							<span class="mwd-topbar <?php echo $row->type == 'topbar' ? ' active' : '' ?>"></span>
							<p>Topbar</p>
						</label>
						<label>
							<input type="radio" name="form_type" value="scrollbox" onclick="change_form_type('scrollbox'); change_hide_show('mwd-scrollbox');"<?php echo $row->type == 'scrollbox' ? 'checked="checked"' : '' ?>>
							<span class="mwd-scrollbox <?php echo $row->type == 'scrollbox' ? ' active' : '' ?>"></span>
							<p>Scrollbox</p>
						</label>
					</div>
					<br /><br />
					<table class="admintable">
						<tr class="mwd-embedded <?php echo $row->type == 'embedded' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Form Placement</label>
							</td>
							<td class="mwd_options_value">
								Use <input type="text" value='[mwd-mailchimp id="<?php echo $row->form_id; ?>"]' onclick="mwd_select_value(this)"  readonly="readonly" style="width:155px !important;"/> shortcode to display the form.
							</td>
						</tr>
						<tr class="mwd-popover <?php echo $row->type == 'popover' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Animation Effect</label>
							</td>
							<td class="mwd_options_value">
								<select id="popover_animate_effect" name="popover_animate_effect">
								<?php
									foreach($animation_effects as $anim_key => $animation_effect){
										$selected = $row->popover_animate_effect == $anim_key ? 'selected="selected"' : '';
										echo '<option value="'.$anim_key.'" '.$selected.'>'.$animation_effect.'</option>';
									}
								?>
								</select>
							</td>
						</tr>

						<tr class="mwd-popover <?php echo $row->type != 'popover' ? 'mwd-hide' : 'mwd-show'; ?>">
							<td class="mwd_options_label">
								<label>Loading Delay</label>
							</td>
							<td class="mwd_options_value">
								<input type="number" name="popover_loading_delay" value="<?php echo $row->popover_loading_delay; ?>" /> seconds
								<div>Define time before displaying the popup (default to 0 for no delay).
								</div>
							</td>
						</tr>
						<tr class="mwd-popover <?php echo $row->type == 'popover' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Frequency</label>
							</td>
							<td class="mwd_options_value">
								<input type="number" name="popover_frequency" value="<?php echo $row->popover_frequency; ?>" /> days
								<div>Display the popup to the same visitor (who has closed popup/already subscribed to the list) after the mentioned period. Leave the default value(0) for permanent display.
								</div>
							</td>
						</tr>
						<tr class="mwd-topbar <?php echo $row->type == 'topbar' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Position</label>
							</td>
							<td class="mwd_options_value">
								<button name="topbar_position" class="mwd-checkbox-radio-button <?php echo $row->topbar_position == 1 ? 'mwd-text-yes' : 'mwd-text-no' ?> medium" onclick="mwd_change_radio_checkbox_text(this); mwd_show_hide('topbar_remain_top'); return false;" value="<?php echo $row->topbar_position == 1 ? '1' : '0' ?>">
									<label><?php echo $row->topbar_position == 1 ? 'Top' : 'Bottom' ?></label>
									<span></span>
								</button>
								<input type="hidden" name="topbar_position" value="<?php echo $row->topbar_position; ?>"/>
							</td>
						</tr>
						<tr class="mwd-topbar topbar_remain_top <?php echo $row->type != 'topbar' ? 'mwd-hide' : ($row->topbar_position == 1 ? 'mwd-show' : 'mwd-hide') ?>">
							<td class="mwd_options_label">
								<label>Remain at top when scrolling</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->topbar_remain_top == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->topbar_remain_top; ?>">
									<span></span>
								</button>
								<input type="hidden" name="topbar_remain_top" value="<?php echo $row->topbar_remain_top; ?>"/>
							</td>
						</tr>
						<tr class="mwd-topbar <?php echo $row->type == 'topbar' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Allow Closing the bar</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->topbar_closing == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this);  return false;" value="<?php echo $row->topbar_closing; ?>">
									<span></span>
								</button>
								<input type="hidden" name="topbar_closing" value="<?php echo $row->topbar_closing; ?>"/>
							</td>
						</tr>
						<tr class="mwd-topbar topbar_hide_duration <?php echo $row->type != 'topbar' ? 'mwd-hide' : 'mwd-show' ?>">
							<td class="mwd_options_label">
								<label>Frequency</label>
							</td>
							<td class="mwd_options_value">
								<input type="number" name="topbar_hide_duration" value="<?php echo $row->topbar_hide_duration; ?>"/>days
								<div>Display the popup to the same visitor (who has closed popup/already subscribed to the list) after the mentioned period. Leave the default value(0) for permanent display.
							</div>
							</td>
						</tr>

						<tr class="mwd-scrollbox <?php echo $row->type == 'scrollbox' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Position</label>
							</td>
							<td class="mwd_options_value">
								<button name="scrollbox_position" class="mwd-checkbox-radio-button <?php echo $row->scrollbox_position == 1 ? 'mwd-text-yes' : 'mwd-text-no' ?> medium" onclick="mwd_change_radio_checkbox_text(this); return false;" value="<?php echo $row->scrollbox_position == 1 ? '1' : '0' ?>">
									<label><?php echo $row->scrollbox_position == 1 ? 'Right' : 'Left' ?></label>
									<span></span>
								</button>
								<input type="hidden" name="scrollbox_position" value="<?php echo $row->scrollbox_position; ?>"/>
							</td>
						</tr>
						<tr class="mwd-scrollbox <?php echo $row->type != 'scrollbox' ? 'mwd-hide' : 'mwd-show'; ?>">
							<td class="mwd_options_label">
								<label>Loading Delay</label>
							</td>
							<td class="mwd_options_value">
								<input type="number" name="scrollbox_loading_delay" value="<?php echo $row->scrollbox_loading_delay; ?>" /> seconds
								<div>This is how long the page should wait before showing the popup (defaults to 0 seconds for no delay).
								</div>
							</td>
						</tr>
						<tr class="mwd-scrollbox <?php echo $row->type == 'scrollbox' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Frequency</label>
							</td>
							<td class="mwd_options_value">
								<input type="number" name="scrollbox_hide_duration" value="<?php echo $row->scrollbox_hide_duration; ?>"/>days
								<div>Display the popup to the same visitor (who has closed popup/already subscribed to the list) after the mentioned period. Leave the default value(0) for permanent display.
								</div>
							</td>
						</tr>

						<tr class="mwd-popover mwd-topbar mwd-scrollbox <?php echo $row->type != 'embedded' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Always show for administrator</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->show_for_admin == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->show_for_admin; ?>">
									<span></span>
								</button>
								<input type="hidden" name="show_for_admin" value="<?php echo $row->show_for_admin; ?>"/>
								<div>Permanent form display for administrator.</div>
							</td>
						</tr>

						<tr class="mwd-scrollbox <?php echo $row->type == 'scrollbox' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Trigger Point</label>
							</td>
							<td class="mwd_options_value">
								<input type="number" name="scrollbox_trigger_point" value="<?php echo $row->scrollbox_trigger_point; ?>"/>%
								<div>Show when a user has scrolled selected percent (%) of your page.</div>
							</td>
						</tr>
						<tr class="mwd-scrollbox <?php echo $row->type == 'scrollbox' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Allow Closing the bar</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->scrollbox_closing == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->scrollbox_closing; ?>">
									<span></span>
								</button>
								<input type="hidden" name="scrollbox_closing" value="<?php echo $row->scrollbox_closing; ?>"/>
							</td>
						</tr>
						<tr class="mwd-scrollbox <?php echo $row->type == 'scrollbox' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Allow Minimize</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->scrollbox_minimize == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); mwd_show_hide('minimize_text'); return false;" value="<?php echo $row->scrollbox_minimize; ?>">
									<span></span>
								</button>
								<input type="hidden" name="scrollbox_minimize" value="<?php echo $row->scrollbox_minimize; ?>"/>
							</td>
						</tr>
						<tr class="mwd-scrollbox minimize_text <?php echo $row->type == 'scrollbox' && $row->scrollbox_minimize == 1 ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Minimize Text</label>
							</td>
							<td class="mwd_options_value">
								<input type="text" name="scrollbox_minimize_text" value="<?php echo $row->scrollbox_minimize_text; ?>"/>
							</td>
						</tr>

						<tr class="mwd-scrollbox <?php echo $row->type == 'scrollbox' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Auto Hide</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->scrollbox_auto_hide == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->scrollbox_auto_hide; ?>">
									<span></span>
								</button>
								<input type="hidden" name="scrollbox_auto_hide" value="<?php echo $row->scrollbox_auto_hide; ?>"/>
								<div>Hide box again when visitor scrolls back up.</div>
							</td>
						</tr>
						<tr class="mwd-popover mwd-topbar mwd-scrollbox <?php echo $row->type != 'embedded' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Display on</label>
							</td>
							<td class="mwd_options_value">
								<ul class="pp_display pp_display_on"><?php
								$posts_and_pages = $this->model->mwd_posts_query();
								$stat_types = array('everything' => 'All', 'home' => 'Homepage', 'archive' => 'Archives');

								$def_post_types = array('category' => 'Categories', 'post' => 'Post', 'page' => 'Page');
								$custom_post_types = get_post_types( array(
									'public'   => true,
									'_builtin' => false,
								) );

								$post_types = array_merge($def_post_types, $custom_post_types);
								$all_types = $stat_types + $post_types;
								$selected_types = explode(',', $row->display_on);
								$show_cats = array_intersect(array_keys($post_types), $selected_types) && !in_array('everything', $selected_types) ? true : false;
								$m = 0;
								foreach($all_types as $post_key => $post_type){
									$checked = in_array('everything', $selected_types) || in_array($post_key, $selected_types) ? 'checked="checked"' : '';
									$postclass = $post_key != 'page' && in_array($post_key, array_keys($post_types)) ? 'class="catpost"' : '';
									echo '<li><input id="pt'.$m.'" type="checkbox" name="display_on[]" value="'.$post_key.'" '.$checked.' '.$postclass.'/><label for="pt'.$m.'">'.$post_type.'</label></li>';
									$m++;
								}
								?>
								</ul>
							</td>
						</tr>
						<tr class="mwd-popover mwd-topbar mwd-scrollbox mwd-cat-show <?php echo $row->type != 'embedded' && $show_cats ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Display on these categories</label>
							</td>
							<td class="mwd_options_value">
								<ul class="pp_display pp_display_on_categories"><?php
								$categories = $this->model->mwd_categories_query();
								$selected_categories = explode(',', $row->display_on_categories);
								$current_categories_array = explode(',', $row->current_categories);
								$m = 0;
								foreach($categories as $cat_key => $category){
									$checked = ((!$row->current_categories && !$row->display_on_categories) || in_array($cat_key, $selected_categories) || (in_array('auto_check_new', $selected_categories) && !in_array($cat_key, $current_categories_array))) ? 'checked="checked"' : '';

									echo '<li><input id="cat'.$m.'" type="checkbox" name="display_on_categories[]" value="'.$cat_key.'" '.$checked.'/><label for="cat'.$m.'">'.$category.'</label></li>';
									$m++;
								}
								$auto_check = (!$row->current_categories && !$row->display_on_categories) || in_array('auto_check_new', $selected_categories) ? 'checked="checked"' : '';
								echo '<li><br/><input id="cat'.$m.'" type="checkbox" name="display_on_categories[]" value="auto_check_new" '.$auto_check.'/><label for="cat'.$m.'">Automatically check new categories</label></li>';
								$current_categories = !$row->current_categories && !$row->display_on_categories ? implode(',', array_keys($categories)) : $row->current_categories;
								?>
								</ul>
								<input type="hidden" name="current_categories" value="<?php echo $current_categories; ?>"/>
							</td>
						</tr>
						<tr class="mwd-popover mwd-topbar mwd-scrollbox mwd-pp-show <?php echo (in_array('everything', $selected_types) || in_array('page', $selected_types) || in_array('post', $selected_types)) && $row->type != 'embedded' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Display on these posts</label>
							</td>
							<td class="mwd_options_value">
								<div class="mwd-mini-heading">Click on input area to view the list of posts. If left empty the form will appear on all posts.</div>
								<p>Posts defined below will override all settings above</p>
								<ul class="mwd-pp">
									<li class="pp_selected"><?php if($row->posts_include){
										$posts_include = explode(',', $row->posts_include);
										foreach($posts_include as $post_exclude){
											if(isset($posts_and_pages[$post_exclude])){
												$ptitle = $posts_and_pages[$post_exclude]['title'];
												$ptype = $posts_and_pages[$post_exclude]['post_type'];
												echo '<span data-post_id="'.$post_exclude.'">['.$ptype.'] - '.$ptitle.'<span class="pp_selected_remove">x</span></span>';
											}
										}
									} ?></li>
									<li>
										<input type="text" class="pp_search_posts" value="" data-post_type="only_posts" style="width: 100% !important;" />
										<input type="hidden" class="pp_exclude" name="posts_include" value="<?php echo $row->posts_include; ?>" />
										<span class="mwd-loading"></span>
									</li>
									<li class="pp_live_search mwd-hide">
										<ul class="pp_search_results">

										</ul>
									</li>
								</ul>
							</td>
						</tr>
						<tr class="mwd-popover mwd-topbar mwd-scrollbox mwd-pp-show <?php echo (in_array('everything', $selected_types) || in_array('page', $selected_types) || in_array('post', $selected_types)) && $row->type != 'embedded' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Display on these pages</label>
							</td>
							<td class="mwd_options_value">
								<div class="mwd-mini-heading">Click on input area to view the list of pages. If left empty the form will appear on all pages.</div>
								<p>Pages defined below will override all settings above</p>
								<ul class="mwd-pp">
									<li class="pp_selected"><?php if($row->pages_include){
										$pages_include = explode(',', $row->pages_include);
										foreach($pages_include as $page_exclude){
											if(isset($posts_and_pages[$page_exclude])){
												$ptitle = $posts_and_pages[$page_exclude]['title'];
												$ptype = $posts_and_pages[$page_exclude]['post_type'];
												echo '<span data-post_id="'.$page_exclude.'">['.$ptype.'] - '.$ptitle.'<span class="pp_selected_remove">x</span></span>';
											}
										}
									} ?></li>
									<li>
										<input type="text" class="pp_search_posts" value="" data-post_type="only_pages" style="width: 100% !important;" />
										<input type="hidden" class="pp_exclude" name="pages_include" value="<?php echo $row->pages_include; ?>" />
										<span class="mwd-loading"></span>
									</li>
									<li class="pp_live_search mwd-hide">
										<ul class="pp_search_results">
										</ul>
									</li>
								</ul>
							</td>
						</tr>
						<tr class="mwd-popover mwd-topbar mwd-scrollbox <?php echo $row->type != 'embedded' ? 'mwd-show' : 'mwd-hide' ?>">
							<td class="mwd_options_label">
								<label>Hide on Mobile</label>
							</td>
							<td class="mwd_options_value">
								<button class="mwd-checkbox-radio-button <?php echo $row->hide_mobile == 1 ? 'mwd-yes' : 'mwd-no' ?>" onclick="mwd_change_radio(this); return false;" value="<?php echo $row->hide_mobile; ?>">
									<span></span>
								</button>
								<input type="hidden" name="hide_mobile" value="<?php echo $row->hide_mobile; ?>"/>
							</td>
						</tr>

				</table>
				</fieldset>
			</div>
			<input type="hidden" id="task" name="task" value=""/>
			<input type="hidden" id="current_id" name="current_id" value="<?php echo $row->form_id; ?>" />
		</form>
		<?php
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
