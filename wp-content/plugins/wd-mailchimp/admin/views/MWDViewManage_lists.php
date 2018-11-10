<?php
class MWDViewManage_lists {
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
		if(get_option('mwd_api_validation') == 'valid_apikey' ) {
			if(false === ($mwd_lists = get_transient('mwd-lists'))) {
				$mwd_lists = $this->model->get_lists();
				set_transient( 'mwd-lists', $mwd_lists, 1 * HOUR_IN_SECONDS );
			}
		} else {
			echo MWD_Library::message_id('12');
			die();
		}
		
		$mwd_lists = $this->model->get_lists_search();
		
		$page_nav = $this->model->page_nav('lists');
		$search_value = ((isset($_POST['search_value'])) ? esc_html($_POST['search_value']) : '');
		$asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$order_by_array = array('name', 'member', 'created');
		$order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'name';
		$order_class = 'manage-column column-title sorted ' . $asc_or_desc;
		MWD_Library::mwd_upgrade_pro();
		?>
		
		<div class="mwd-mailchimp mwd-wrap">
			<form onkeypress="mwd_doNothing(event)" id="manage_lists" method="post" action="admin.php?page=manage_lists">
				<?php wp_nonce_field('nonce_mwd', 'nonce_mwd'); ?>
				<div class="mwd-page-banner">
					<span class="mwd-logo"></span>
					<h1>Lists</h1>
				</div>
				<div class="tablenav top">
					<?php
						MWD_Library::search('Name', $search_value, 'manage_lists');
						MWD_Library::html_page_nav($page_nav['total'], $page_nav['limit'], 'manage_lists');
					?>
				</div>
				<div class="mwd-clear"></div>
				<table class="widefat fixed pages">
					<thead>
						<th class="table_small_col">#</th>
						<th class="<?php if ($order_by == 'name') { echo $order_class; } ?>">
							<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'name'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'name' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'manage_lists')" href="">
							<span>List Name</span><span class="sorting-indicator"></span></a>
						</th>
						<th class="<?php if ($order_by == 'member') { echo $order_class; } ?>">
							<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'member'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'member' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'manage_lists')" href="">
							<span>Subscribers</span><span class="sorting-indicator"></span></a>
						</th>
						<th class="<?php if ($order_by == 'created') { echo $order_class; } ?>">
							<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'created'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'created' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'manage_lists')" href="">
							<span>Date Created</span><span class="sorting-indicator"></span></a>
						</th>
						<th width="10%">View</th>
					</thead>
					<tbody id="tbody_arr">
						<?php
						if ($mwd_lists['total']) {
							foreach ($mwd_lists['data'] as $index => $mwd_list) {
								$alternate = (!isset($alternate) || $alternate == '') ? 'class="alternate"' : '';
								?>
								<tr id="tr_<?php echo 20 * ($page_nav['limit'] - 1) + $index + 1; ?>" <?php echo $alternate; ?>>
									<td class="table_small_col"><?php echo 20 * ($page_nav['limit'] - 1) + $index + 1; ?></td>
									<td>
										<a href="<?php echo add_query_arg(array('list_id' => $mwd_list['id']), admin_url('admin.php?page=manage_lists&task=view')); ?>"><?php echo $mwd_list['name']; ?></a>
									</td>
									<td>
										<?php echo $mwd_list['stats']['member_count_since_send'] > 0 ? $mwd_list['stats']['member_count_since_send'] : $mwd_list['stats']['member_count']; ?>
									</td>
									<td>
										<?php echo $mwd_list['date_created']; ?>
									</td>
									<td width="10%">
										<a href="<?php echo add_query_arg(array('list_id' => $mwd_list['id']), admin_url('admin.php?page=manage_lists&task=view')); ?>">
											<span class="mwd-view"></span>
										</a>
									</td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
				<input id="task" name="task" type="hidden" value=""/>
				<input id="list_id" name="list_id" type="hidden" value=""/>
				<input id="asc_or_desc" name="asc_or_desc" type="hidden" value="asc"/>
				<input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>"/>
			</form>
		</div>
		
		<?php
	}

	public function view($list_id) {
		if(get_option('mwd_api_validation') == 'valid_apikey' ) {
			if(false === ($subscribers_list = get_transient('mwd-subscribers-data'))) {
				$subscribers_list = $this->model->get_subscribers_list( $list_id );
				set_transient( 'mwd-subscribers-data', $subscribers_list, 1 * HOUR_IN_SECONDS );
			}
			if(false === ($list_data = get_transient('mwd-list-data'))) {
				$list_data = $this->model->get_list_data( $list_id );
				set_transient( 'mwd-list-data', $list_data, 1 * HOUR_IN_SECONDS );
			}
		} else {
			MWD_Library::mwd_redirect(add_query_arg(array('list_id' => $list_id, 'message' => '12'), admin_url('admin.php?page=manage_lists&task=view')));
		}
		
		$subscribers_list = $this->model->get_subscribers_list_search( $list_id );
		$list = $list_data['list'];
		$merge_vars = $list_data['merge_vars'];
		$interest_groups = $list_data['interest_groups'];
		$segments = $list_data['segments'];
		$search_value = ((isset($_POST['search_value'])) ? esc_html($_POST['search_value']) : '');
		$page_nav = $this->model->page_nav( 'subscribers', $list_id );
		
		$asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$order_by_array = array('email', 'last_update_time');
		$order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'email';
		$order_class = 'manage-column column-title sorted ' . $asc_or_desc;
		$users_count = $subscribers_list['total'];
		
		MWD_Library::mwd_upgrade_pro('view');
		?>
		
		<div class="mwd-mailchimp mwd-main container-fluid">
			<div class="row">
				<div class="col-md-12">
					<span class="mwd-logo"></span>
					<h1>List: <?php echo $list['name']; ?></h1>
					<div class="mwd-page-actions">
						<button class="mwd-button export-button medium" onclick="mwd_export_users()">
							<span></span>
							Export CSV
						</button>
				</div>
				</div>
				<div class="col-md-8 col-sm-6">
					<form id="mwd_subscribers" method="post">
						<div class="tablenav top">
						<?php
							MWD_Library::search('User Email', $search_value, 'mwd_subscribers');
							MWD_Library::html_page_nav($page_nav['total'], $page_nav['limit'], 'mwd_subscribers');
						?>
						</div>
						<div class="mwd-clear"></div>
						<table class="widefat fixed pages">
							<thead>
								<th width="8%" class="text-center">#</th>
								<th class="<?php if ($order_by == 'email') { echo $order_class; } ?>">
									<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'email'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'email' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'mwd_subscribers')" href="">
									<span>User Email</span><span class="sorting-indicator"></span></a>
								</th>
								<th>Email Client</th>
								<th class="<?php if ($order_by == 'last_update_time') { echo $order_class; } ?>">
									<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'last_update_time'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'last_update_time' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'mwd_subscribers')" href="">
									<span>Last Changed</span><span class="sorting-indicator"></span></a>
								</th>	
								<th width="15%">Unsubscribe</th>
							</thead>
							<tbody id="tbody_arr">
								<?php
								if ($subscribers_list['total']) {
									foreach ($subscribers_list['data'] as $index => $subscriber) {
										$alternate = (!isset($alternate) || $alternate == '') ? 'class="alternate"' : '';
										if(!empty($subscriber['clients'])) {
											$client_name = $subscriber['clients']['name'];
											$client_icon = "<img src='".$subscriber['clients']['icon_url']."' alt=".$client_name." title=".$client_name.">";
										} else {
											$client_name = '';
											$client_icon = "<span title='not set'>N/A</span>";
										}
										?>
										<tr id="tr_<?php echo 20 * ($page_nav['limit'] - 1) + $index + 1; ?>" <?php echo $alternate; ?>>
											<td class="table_small_col"><?php echo 20 * ($page_nav['limit'] - 1) + $index + 1; ?></td>
											<td>
												<a href="<?php echo add_query_arg(array('list_id' => $list['id'], 'sub_id' => $subscriber['leid']), admin_url('admin.php?page=manage_lists&task=subscriber_info')); ?>"><?php echo $subscriber['email']; ?></a>
											</td>
											<td>
												<?php echo $client_name. $client_icon; ?>
											</td>
											<td>
												<?php echo $subscriber['info_changed']; ?>
											</td>
											<td class="text-center">
												<a href="<?php echo add_query_arg( array('sub_id' => $subscriber['leid'], 'list_id' => $list['id'], 'nonce_mwd' => wp_create_nonce('nonce_mwd')), admin_url('admin.php?page=manage_lists&task=unsubscribe') ) ?>" style="display:inline-block;">
													<span class="mwd-icon unsubscribe-icon"></span>
												</a>	
											</td>
										</tr>
										<?php
									}
								}
								else {
									?>
									<tr>
										<td></td>
										<td colspan="3">No subscribers</td>
									<?php
								}
								?>
							</tbody>
						</table>
						<input id="asc_or_desc" name="asc_or_desc" type="hidden" value="asc"/>
						<input id="task" name="task" type="hidden" value=""/>
						<input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>"/>
					</form>
					<br /><br />
					<div class="mwd-sidebar mwd-fields-info">
						<h2>Form Fields</h2>
						<hr class="mwd-section-seperator"/>
						<div class="panel">
							<div class="panel-heading text-center">
								<?php echo intval($list['stats']['merge_var_count']+1); ?> Fields
							</div>
							<div class="mwd-arrow mwd-arrow-top">
								<?php if(count($merge_vars) > 0) { ?>
									<table class="table table-bordered" style="border-top:none;">
										<thead>
											<tr>
												<th>Name</th>
												<th>Tag</th>
												<th>Type</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												foreach($merge_vars as $merge_var) { ?>
													<tr>
														<td><?php echo $merge_var['name']; ?></td>
														<td><?php echo $merge_var['tag']; ?></td>
														<td><?php echo $merge_var['field_type']; ?></td>
													</tr>
													<?php
												}	
											?>
										</tbody>
									</table>
								<?php } ?>
							</div>	
						</div>
					</div>
				</div>
				<div class="col-md-4 col-sm-6">
					<div class="mwd-sidebar mwd-list-info">
						<h2>List Overview</h2>
						<hr class="mwd-section-seperator"/>
						<div class="list-general">
							<div>
								<div class="mwd-key">
									<label>List ID</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<?php echo $list['id']; ?>
								</div>
							</div>
							<div>
								<div class="mwd-key">
									<label>List Rating</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<?php 
									$list_rating = $list['list_rating'];
									$list_rating = $list_rating ? $list_rating : 'n/a';
									echo $list_rating; ?>
								</div>
							</div>
							<div>
								<div class="mwd-key">
									<label>Average Subscribers</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<?php echo $list['stats']['avg_sub_rate'].'/ month'; ?>
								</div>
							</div>
							<div>
								<div class="mwd-key">
									<label>Subscriber Count</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<?php echo intval($list['stats']['member_count']); ?>
								</div>
							</div>
							<div>
								<div class="mwd-key">
									<label>New Since Last Campaign</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<?php echo intval($list['stats']['member_count_since_send']); ?>
								</div>
							</div>
							<div>
								<div class="mwd-key">
									<label>Created</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<?php echo date(get_option('date_format'), strtotime( $list['date_created'])); ?>
								</div>
							</div>
							<div>
								<div class="mwd-key">
									<label>Default From Email</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<input type="text" value="<?php echo sanitize_email($list['default_from_email']); ?>" readonly onclick="jQuery(this).select();" />
								</div>
							</div>
							<div>
								<div class="mwd-key">
									<label>Default From Name</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<?php echo $list['default_from_name']; ?>
								</div>
							</div>
							<div>
								<div class="mwd-key">
									<label>List Fields</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<?php echo intval($list['stats']['merge_var_count']+1); ?>
								</div>
							</div>
							<div>
								<div class="mwd-key">
									<label>Short Signup URL</label>
								</div>
								<div class="mwd-value mwd-arrow mwd-arrow-right">
									<input type="text" readonly="" value="<?php echo esc_url_raw($list['subscribe_url_short']); ?>" onclick="jQuery(this).select(); return false;" >
								</div>
							</div>
						</div>
					</div>
					<br /><br />
					<div class="mwd-sidebar mwd-interest-groups-info">
						<h2>Interest Groups Overview</h2>
						<hr class="mwd-section-seperator"/>
						<div class="panel">
							<div class="panel-heading text-center">
								<?php 
									if(is_array($interest_groups)) { 
										echo intval(count( $interest_groups)).' Merge Variables';
									} 
								?>
							</div>
							<div class="panel-body mwd-arrow mwd-arrow-top">
								<?php if(is_array($interest_groups)) { ?>
									<ul>
										<?php foreach($interest_groups as $interest_group) { ?>
											<li>
												<span><?php echo $interest_group['name']; ?></span><span>&nbsp;&nbsp;(<?php echo $interest_group['groups'][0]['subscribers']; ?>)</span>
											</li>
										<?php }	?>
									</ul>
								<?php } else {
									echo $interest_groups;
								} ?></div>
						</div>
					</div>
					<br /><br />
					<div class="mwd-sidebar mwd-segments-info">
						<h2>Segments Overview</h2>
						<hr class="mwd-section-seperator"/>
						<div class="panel">
							<div class="panel-heading text-center">
								<?php
									if(is_array($segments) && isset( $segments['saved'] ) && count( $segments['saved'] ) > 0) { 
										echo intval(count($segments['saved'])).' Segment'; 
									}	
								?>
							</div>
							<div class="panel-body mwd-arrow mwd-arrow-top">
								<?php if(is_array($segments)) {
									if(isset( $segments['saved'] ) && count( $segments['saved'] ) > 0) { ?>
									<ul>
										<?php 
										foreach($segments['saved'] as $segment) { 
											$k = 1;
											?>
											<li>
												<span><?php echo $segment['name']; ?></span><a href="" onclick="mwd_toggle(this); return false;"><span class="mwd-show-icon"></span></a>
											</li>
											<div class="mwd-segment-conditions hide">
												<ul>
													<?php
													foreach( $segment['segment_opts']['conditions'] as $condition ) {
														$val = (is_array($condition['value'])) ? implode(',', $condition['value'] ) : $condition['value'];
														echo '<li><span>'.intval($k). '. 
														If '.$condition['field'].' '.$condition['op'].' '. $val .'</span></li>';
														$k++;
													}
													?>
												</ul>
											</div>
										<?php }	?>
									</ul>
									<?php } 
									else{
										echo 'This list does not have segments.';
									}
								}
								else {
									echo $segments;
								} ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
		function mwd_export_users() {
			jQuery.ajax({
				type: "POST",  
				url:"<?php echo add_query_arg(array('action' => 'ListsGenerete_csv', 'list_id' => $list['id'], 'send_header' => 0), admin_url('admin-ajax.php')); ?>",
				success: function(data) {
					window.location = "<?php echo add_query_arg(array('action' => 'ListsGenerete_csv', 'list_id' => $list['id'], 'send_header' => 1), admin_url('admin-ajax.php')); ?>";
				}
			});
		}	
		</script>
		<?php
	}

	public function subscriber_info($sub_id, $list_id) {
		$apikey = get_option( 'mwd_api_key', '' );
		$api = new Mailchimp($apikey);
		if(get_option('mwd_api_validation') == 'valid_apikey' ) {
			if(false === ($profile_data = get_transient('mwd-subscriber-data'))) {
				$profile_data = $this->model->get_user_data( $sub_id, $list_id );
				set_transient( 'mwd-subscriber-data', $profile_data, 1 * HOUR_IN_SECONDS );
			}
		} else {
			MWD_Library::mwd_redirect(add_query_arg(array('list_id' => $list_id, 'sub_id' => $sub_id, 'message' => '12'), admin_url('admin.php?page=manage_lists&task=subscriber_info')));
		}
		if(isset($profile_data['data'][0])) {
			$user_email = sanitize_email($profile_data['data'][0]['email']);
			$list_name = $profile_data['data'][0]['list_name'];
		} else {
			$list_name = '';
			$user_email = '';
		}
		MWD_Library::mwd_upgrade_pro('subscriber_info');
		?>
		
		<div class="mwd-mailchimp container-fluid">
			<div class="row">
				<div class="col-md-12">
					<span class="mwd-logo"></span>
					<h1>Subscriber Details</h1>
				</div>
				<div class="col-md-8">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=manage_lists' ) ); ?>" title="<?php _e( 'View List', 'form-maker' ); ?>">
						Lists
					</a>
					&nbsp;&#187;&nbsp;
					<a href="<?php echo esc_url(admin_url('admin.php?page=manage_lists&task=view&list_id='.$list_id)); ?>" title="<?php echo $list_name; ?>">
						<?php echo $list_name; ?>
					</a>
					&nbsp;&#187;&nbsp;
					<span title="<?php echo $user_email; ?>">
						<?php echo $user_email; ?>
					</span>
				</div>
				<?php
				if(isset($profile_data['data'][0])) {
					$profile_data = $profile_data['data'][0];
					$avatar_image = get_avatar($user_email, 120);
					$other_lists = (isset($profile_data['lists']) && ! empty($profile_data['lists'])) ? $profile_data['lists'] : array();
					$merge_variables_user = ($profile_data['merges'] && ! empty( $profile_data['merges'])) ? $profile_data['merges'] : array();

					$additional_lists = array();
					if( isset( $other_lists ) && count( $other_lists ) >= 1 ) {
						foreach( $other_lists as $list ) {
							if( $list['status'] == 'subscribed' ) {	
								$list_data = $api->call( 'lists/list' , array( 'apikey' => $apikey, 'filters' => array( 'list_id' => $list['id'] ) ) );
								if( $list_data && isset( $list_data['data'][0] ) ) {
									$additional_lists[$list_data['data'][0]['id']] = $list_data['data'][0]['name'];
								}
							}
						}	
					}

					if( isset( $merge_variables_user ) && count( $merge_variables_user ) >= 1 ) {
						$merge_variables = $api->call( 'lists/merge-vars' , array( 'apikey' => $apikey, 'id' => array( $list_id ) ) );
																													

						$merge_variable_fields = array();
						if( $merge_variables ) {
							foreach( $merge_variables['data'][0]['merge_vars'] as $merge_variable ) {
								$merge_variable_fields[$merge_variable['name']] = ( isset( $merge_variables_user[$merge_variable['tag']] ) ) ? $merge_variables_user[$merge_variable['tag']] : '';
							}
						}

					}
					
					$member_rating = (!empty( $profile_data['member_rating'])) ? (int)$profile_data['member_rating'] : 0;
					$member_rating_stars = '';
					if( $member_rating > 0 ) {
						$x = 1;
						while( $x <= 5 ) {
							if( $x <= $member_rating ) {
								$member_rating_stars .= '<span class="dashicons dashicons-star-filled"></span>';
							} else {
								$member_rating_stars .= '<span class="dashicons dashicons-star-empty"></span>';
							}
							$x++;
						}
					} else {
						$y = 1;
						while( $y <= 5 ) {
							$member_rating_stars .= '<span class="dashicons dashicons-star-empty"></span>';
							$y++;
						}
					}
					$last_changed = strtotime( $profile_data['info_changed'] );
					$user_language = ( $profile_data['language'] && $profile_data['language'] != '' ) ? $profile_data['language'] : '';
					
					$unsubscribe_url = esc_url_raw(add_query_arg( array('sub_id' => $profile_data['leid'], 'list_id' => $list_id, 'nonce_mwd' => wp_create_nonce('nonce_mwd')), admin_url('admin.php?page=manage_lists&task=unsubscribe')));
					?>
					<div class="col-md-8 col-sm-10 col-xs-12">
						<div class="mwd-subscriber-details col-md-12">
							<div class="col-md-3 col-xs-4">
								<?php echo $avatar_image; ?>
							</div>
							<div class="col-md-9 col-xs-8">	
								<h2><?php echo $user_email; ?></h2>
								<hr class="mwd-section-seperator"/>
								<?php echo '<p class="member-star-rating-container" title="'.'Member Rating: '.$member_rating.' star(s)">' . $member_rating_stars . '</p>'; ?>
								<p>Subscribed: <?php echo get_date_from_gmt( $profile_data['info_changed'], 'F jS, Y h:i a' ); ?>
								</p>
								<?php if(isset( $profile_data['geo']) && ! empty( $profile_data['geo'])) { ?>
									<?php if(isset($profile_data['geo']['latitude']) && isset($profile_data['geo']['longitude'])){ ?>
										<p>
											Location: <?php echo $this->mwd_get_geocode( $profile_data['geo']['latitude'], $profile_data['geo']['longitude'] ); ?>
										</p>
									<?php } else { ?>
										<p>
											Location: <?php echo $profile_data['geo']['region'] . ', ' . $profile_data['geo']['cc']; ?>
										</p>
									<?php 
									}
								} 
								?>
							</div>
							<div class="col-md-12">
								<div class="mwd-subscriber-header">
									<button class="mwd-button merge-variables smail active-button" onclick="change_tab('merge-variables'); return false;"><span></span></button>
									<button class="mwd-button additional-lists smail" onclick="change_tab('additional-lists'); return false;"><span></span></button>
									<button class="mwd-button delete-subscriber smail" onclick="change_tab('delete-subscriber'); return false;"><span></span></button>
								</div>
								<div class="mwd-subscriber-content merge-variables-tab row" style="margin: -3px -1px 0 !important;">
									<div class="col-md-12">
										<h2>Fields:</h2>
										<hr class="mwd-section-seperator"/>
										<?php 
										if(!empty( $merge_variable_fields)) {
											foreach( $merge_variable_fields as $field_name => $value ) { ?>
												<div class="mwd-row">
													<div class="mwd-key merge-var-label">
														<?php echo $field_name; ?>
													</div>
													<?php if($value) { ?>												
													<div class="mwd-value merge-var-value">
														<span><em><?php echo $value; ?></em></span>
													</div>
													<?php } ?>
												</div>
											<?php } 
											} else { ?>
													<div class="mwd-no-data">No Subscriber Data Found</div>
												<?php
											}
										if(isset( $profile_data['id'] ) && $profile_data['id'] != '') { ?>
											<div class="mwd-row">
												<div class="mwd-key merge-var-label">
													User ID
												</div>
												<div class="mwd-value merge-var-value">
													<span><em><?php echo $profile_data['id']; ?></em></span>
												</div>
											</div>
											<?php
										}
										if(isset( $profile_data['ip_signup'] ) && $profile_data['ip_signup'] != '') { ?>
											<div class="mwd-row">
												<div class="mwd-key merge-var-label">
													Signup IP
												</div>
												<div class="mwd-value merge-var-value">
													<span><em><?php echo $profile_data['ip_signup']; ?></em></span>
												</div>
											</div>
											<?php
										}
										?>
									</div>			
								</div>	
								<div class="mwd-subscriber-content additional-lists-tab" style="display:none;">
									<div class="col-md-12">
										<h2>Other Subscriptions:</h2>
										<hr class="mwd-section-seperator"/>
										<?php
										if( !empty( $additional_lists ) ) {	
											unset( $additional_lists[$list_id] );
											if( ! empty( $additional_lists ) ) {
												?>	
												<ul>
												<?php foreach( $additional_lists as $listid => $name ) {
													$user_redirect_url = esc_url_raw( admin_url( 'admin.php?page=manage_lists&task=view&list_id='.$listid ) );
													?>
													<li>
														<a href="<?php echo $user_redirect_url; ?>"><?php echo $name; ?></a>
													</li>
													<?php
												} ?>
												</ul>
												<?php
											}
										} else { ?>
											<div class="mwd-no-data">No Other Subscriptions Found.</div>
											<?php
										}
										?>
									</div>
								</div>		
								<div class="mwd-subscriber-content delete-subscriber-tab" style="display:none;">
									<div class="col-md-12">
										<h2>Delete subscriber:</h2>
										<hr class="mwd-section-seperator"/>
										<form id="delete_subscriber" method="POST" action="<?php echo $unsubscribe_url; ?>">
											<p class="description">
												<?php echo 'Deleting this subscriber will completely remove <strong>'.$user_email.'</strong> from the "<strong>'.$list_name.'</strong>" MailChimp list.'; ?>
											</p>
											<br />
											<label>
												<input type="checkbox" name="confirm_delete_subscriber" value="1" onclick="toggle_delete(jQuery(this));">
												<?php echo 'Are you sure you want to delete "<strong>'.$user_email.'</strong>" from "<strong>'.$list_name.'</strong>?"'; ?>
											</label>
										</form>
									</div>
									<div style="text-align: right;">
										<button class="mwd-button mwd-delete-subscriber large" onclick="jQuery('#delete_subscriber').submit();" disabled="disabled">
											<span></span>
											Delete Subscriber
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	public function mwd_get_geocode($latitude, $longitude) {
		$geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude .','.$longitude;
		$geocode_response = wp_remote_get($geocode_url);
		if(is_wp_error($geocode_response)) {
			return;
		}
		$geocode_response_body = json_decode(wp_remote_retrieve_body( $geocode_response ), true);
		if(is_wp_error($geocode_response_body)) {
			return;
		}
		$city = isset($geocode_response_body['results'][0]['address_components'][2]) ? $geocode_response_body['results'][0]['address_components'][2]['short_name'] : '';
		$state = isset($geocode_response_body['results'][0]['address_components'][5]) ? $geocode_response_body['results'][0]['address_components'][5]['short_name'] : '';
		$country = isset($geocode_response_body['results'][0]['address_components'][6]) ? $geocode_response_body['results'][0]['address_components'][6]['short_name'] : '';
		$link = '<a href="http://maps.google.com/maps?q=' . $latitude . ',' . $longitude . '" target="_blank" title="' . __( 'View Google Map', 'mwd-text' ) . '">'. $city . ', ' . $state . ', ' . $country .'</a>&nbsp;<span class="flag-icon flag-icon-'. strtolower($country) .'"></span>';
		return $link;
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