<?php

class MWDViewBlocked_ips {
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
		$rows_data = $this->model->get_rows_data();
		$page_nav = $this->model->page_nav();
		$search_value = ((isset($_POST['search_value'])) ? esc_html(stripslashes($_POST['search_value'])) : '');	
		$asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$order_by_array = array('id', 'ip');
		$order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'id';
		$order_class = 'manage-column column-title sorted ' . $asc_or_desc;
		$ids_string = '';
		MWD_Library::mwd_upgrade_pro();
		?>
		<div id="mwd_blocked_ips_message" style="width: 99%; display: none;"></div>

		<div class="mwd-mailchimp">
		<form onkeypress="mwd_doNothing(event)" id="blocked_ips" method="post" action="admin.php?page=blocked_ips" style="width:99%;">
			<?php wp_nonce_field('nonce_mwd', 'nonce_mwd'); ?>
			<div class="mwd-page-banner">
				<div class="mwd-logo">
				</div>
				<h1>Blocked IPs</h1>
				
				<div class="mwd-page-actions">
					<div>
						<button class="mwd-button save-button small" onclick="mwd_set_input_value('task', 'save_all');">
							<span></span>
							Save
						</button>
						<button class="mwd-button delete-button small" onclick="if (confirm('Do you want to unblock selected IPs?')) { mwd_set_input_value('task', 'delete_all'); } else { return false; }">
							<span></span>
							Delete
						</button>
					</div>	
				</div>
			</div>	 
				 
			<div class="mwd-clear"></div>
			<div class="tablenav top">
				<?php
					MWD_Library::search('IP', $search_value, 'blocked_ips');
					MWD_Library::html_page_nav($page_nav['total'], $page_nav['limit'], 'blocked_ips');
				?>
			</div>
			<table class="widefat fixed pages mwd-block-ip">
				<thead>
					<tr>
						<th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin: 0;" /></th>
						<th class="table_small_col <?php if ($order_by == 'id') {echo $order_class;} ?>">
							<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'id'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'id' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'blocked_ips')" href="">
								<span>ID</span><span class="sorting-indicator"></span></th>
							</a>
						<th class="<?php if ($order_by == 'ip') {echo $order_class;} ?>">
							<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'ip'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'ip' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'blocked_ips')" href="">
							<span>IP</span><span class="sorting-indicator"></span>
							</a>
						</th>
						<th class="table_small_col">Edit</th>
						<th class="table_small_col">Delete</th>
					</tr>		  
					<tr id="tr" style="background-color: #f9f9f9;">
						<th></th>
						<th></th>
						<th>
							<input type="text" class="input_th" id="ip" name="ip" onkeypress="return mwd_check_isnum(event)">
							<button class="mwd-button add-button small" onclick="if (mwd_check_required('ip', 'IP')) {return false;} mwd_set_input_value('task', 'save'); mwd_set_input_value('current_id', ''); mwd_save_ip('blocked_ips');">
								Add IP
								<span></span>
							</button>
						</th>
						<th>
							
						</th>
						<th></th>
					</tr>
				</thead>
			<tbody id="tbody_arr">
			<?php
				if ($rows_data) {
					foreach ($rows_data as $row_data) {
						$alternate = (!isset($alternate) || $alternate == 'class="alternate"') ? '' : 'class="alternate"';
						?>
						<tr id="tr_<?php echo $row_data->id; ?>" <?php echo $alternate; ?>>
						<td class="table_small_col check-column" id="td_check_<?php echo $row_data->id; ?>" >
								<input id="check_<?php echo $row_data->id; ?>" name="check_<?php echo $row_data->id; ?>" type="checkbox" />
							</td>
							<td class="table_small_col" id="td_id_<?php echo $row_data->id; ?>" ><?php echo $row_data->id; ?></td>
							<td id="td_ip_<?php echo $row_data->id; ?>" >
								<a class="pointer" id="ip<?php echo $row_data->id; ?>"
							 onclick="mwd_edit_ip(<?php echo $row_data->id; ?>)" 
							 title="Edit"><?php echo $row_data->ip; ?></a>
							</td>
							<td class="table_small_col" id="td_edit_<?php echo $row_data->id; ?>">
								<button class="mwd-icon edit-icon" onclick="mwd_edit_ip(<?php echo $row_data->id; ?>);">
									<span></span>
								</button>
							</td>
							<td class="table_small_col" id="td_delete_<?php echo $row_data->id; ?>">
								<button class="mwd-icon delete-icon" onclick="if (confirm('Do you want to unblock selected IP?')) { mwd_set_input_value('task', 'delete'); mwd_set_input_value('current_id', <?php echo $row_data->id; ?>); mwd_form_submit(event, 'blocked_ips'); } else {return false;}">
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
		<input id="task" name="task" type="hidden" value="" />
		<input id="current_id" name="current_id" type="hidden" value="" />
		<input id="ids_string" name="ids_string" type="hidden" value="<?php echo $ids_string; ?>" />
		<input id="asc_or_desc" name="asc_or_desc" type="hidden" value="<?php echo $asc_or_desc; ?>" />
		<input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>" />
    </form>
		</div>
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