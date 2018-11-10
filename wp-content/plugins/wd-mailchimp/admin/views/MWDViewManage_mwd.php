<?php

class MWDViewManage_mwd {
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
		$cache_cleared = false;
		if(false === get_transient('mwd-list-info') && false === get_transient('mwd-profile-info') && false === get_transient('mwd-account-details') && false === get_transient('mwd-lists') && false === get_transient('mwd-subscribers-data') && false === get_transient('mwd-list-data') && false === get_transient('mwd-subscriber-data')) {
			$cache_cleared = true;
		}

		if(get_option('mwd_api_validation') == 'valid_apikey') {
			if(false === ($mwd_lists = get_transient('mwd-list-info'))) {
				$mwd_lists = $this->model->mwd_lists();
				set_transient( 'mwd-list-info', $mwd_lists, 1 * HOUR_IN_SECONDS );
			}
			if(false === ($mwd_profile_info = get_transient('mwd-profile-info'))) {
				$mwd_profile_info = $this->model->mwd_profile_info();
				set_transient( 'mwd-profile-info', $mwd_profile_info, 1 * HOUR_IN_SECONDS );
			}
			if(false === ($mwd_account_details = get_transient('mwd-account-details'))) {
				$mwd_account_details = $this->model->mwd_account_details();
				set_transient( 'mwd-account-details', $mwd_account_details, 1 * HOUR_IN_SECONDS );
			}
		} else {
			if(get_option('mwd_api_key')) {
				update_option('mwd_api_key', '');
				MWD_Library::mwd_redirect(add_query_arg(array('message' => '12'), admin_url('admin.php?page=manage_mwd')));
				die();
			}
		}

		$apikey = get_option('mwd_api_key', '');
		$is_api_valid = get_option('mwd_api_validation') == 'valid_apikey' ? 1 : 0;
		MWD_Library::mwd_upgrade_pro();
		?>

		<div class="mwd-mailchimp mwd-main container-fluid">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<span class="mwd-logo"></span>
					<h1>MailChimp WD</h1>
				</div>
				<div class="col-md-8 col-sm-12 col-xs-12">
					<?php if(!$is_api_valid): ?>
					<div class="mwd-mailchimp-quick-connect panel panel-default">
						<div class="panel-heading mwd-mailchimp-progress-bar">
							<div class="mwd-mailchimp-loaded">
								<span>1/3</span>
							</div>
							<label>Connect to Mailchimp</label>
						</div>
						<div class="panel-body mwd-mailchimp-tabs">
							<div class="col-md-12 col-sm-12 col-xs-12 mwd-mailchimp-connect-tab" >
								<table class="table table-borderless">
									<tr>
										<td colspan=2>
											<button class="mwd-button mwd-api-label">
												MailChimp API Key
											</button>
										</td>
									</tr>
									<tr>
										<td colspan=2>
											<div class="mwd-arrow mwd-arrow-top left">
												Your MailChimp API Key can be found within this link: <a href="http://admin.mailchimp.com/account/api" target="_blank">http://admin.mailchimp.com/account/api</a>
											</div>	
											<br />
										</td>
									</tr>
									<tr>
										<td>
											<input type="text" id="mwd_mailchimp_apikey" name="mwd_mailchimp_apikey" value="" />
											<br />
											<div>After creating the API key, please paste it here.</div>
											<div class="mwd-load-mailchimp-message" ></div>
											<br />
										</td>
										<td>
											<div class="mwd-load-mailchimp-status" style="display:none;"></div>
										</td>
									</tr>
									<tr>
										<td colspan=2>
											<button class="mwd-button mwd-load-mailchimp large pull-right">
												Connect to Mailchimp
											</button>
										</td>
									</tr>
								</table>
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12 mwd-mailchimp-list-tab" style="display:none;">
								<table class="table table-borderless">
									<tr>
										<td>
											<button class="mwd-button mwd-api-label">
												Choose a list
											</button>
										</td>
									</tr>
									<tr>
										<td>
											<div class="mwd-arrow mwd-arrow-top left">
												Choose relevant list for MailChimp integration below:
											</div>	
											<br />
										</td>
									</tr>
									<tr>
										<td>
											<div>
												<select id="mwd_mailchimp_list" name="mwd_mailchimp_list">
												</select>
											</div>
											<br />
										</td>
									</tr>
									<tr>
										<td>
											<a class="pull-left skip-step" href="<?php echo esc_url(admin_url('admin.php?page=manage_mwd')); ?>">
												or skip this step
											</a>
											<button class="mwd-button mwd-load-mailchimp-list small pull-right">Save List</button>
										</td>
									</tr>
								</table>
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12 mwd-mailchimp-opt-in-form-tab" style="display:none;" >
								<table class="table table-borderless">
									<tr>
										<td>
											<button class="mwd-button mwd-api-label large">
												Choose display options
											</button>
										</td>
									</tr>
									<tr>
										<td>
											<div class="mwd-arrow mwd-arrow-top left">
												We have different types of opt-in forms that you can use to increase your email subscribers. Choose the one you like below:
											</div>	
											<br />
										</td>
									</tr>
									<tr>
										<td>
											<div class="mwd-row mwd-form-types">
												<label>
													<input type="radio" name="form_type" value="embedded" onclick="change_form_type('embedded');" checked="checked">
													<span class="mwd-embedded active"></span>
													<p>Embedded</p>
												</label>
												<label>
													<input type="radio" name="form_type" value="popover" onclick="change_form_type('popover');">
													<span class="mwd-popover"></span>
													<p>Popup</p>
												</label>
												<label>
													<input type="radio" name="form_type" value="topbar" onclick="change_form_type('topbar');">
													<span class="mwd-topbar"></span>
													<p>Topbar</p>
												</label>
												<label>
													<input type="radio" name="form_type" value="scrollbox" onclick="change_form_type('scrollbox');">
													<span class="mwd-scrollbox"></span>
													<p>Scrollbox</p>
												</label>
											</div>
											<br />
										</td>
									</tr>
									<tr>
										<td>
											<a class="pull-left skip-step" href="<?php echo esc_url(admin_url('admin.php?page=manage_mwd')); ?>">
												or skip this step
											</a>
											<button class="mwd-button mwd-create-form small pull-right">Create Form</button>
											<input type="hidden" id="mwd_list_id" value="" />
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>

					<?php else: ?>
					<div class="mwd-sidebar mwd-mailchimp-api">
						<table class="table table-borderless no-margin">
							<tr>
								<td>
									<label for="mwd_mailchimp_apikey">MailChimp API Key</label>
								</td>
								<td>
									<input type="text" id="mwd_mailchimp_apikey" name="mwd_mailchimp_apikey" value="<?php echo $apikey; ?>" onclick="jQuery(this).select(); return false;" disabled/>
									<div class="mwd-load-mailchimp-message"></div>
								</td>
								<td>
									<div class="mwd-load-mailchimp-status"></div>
								</td>
							</tr>
						</table>	
					</div>
					<br/>
					<div>
						<button class="mwd-button mwd-change-api extra-large">
							<span></span>
							Change MailChimp API
						</button>
						<button class="mwd-button mwd-renew-api extra-large" style="display:none;">
							<span></span>
							Connect to Mailchimp
						</button>
					</div>
					<?php endif; ?>
					
					<?php if($is_api_valid): ?>
					<div>
						<br/>
						<h2>MailChimp Data</h2>
						<p>Your MailChimp list data can be found in container below. If you have made changes to your lists and want to renew data use the buttons below.</p>
						<div class="mwd-mailchimp-data">
							<?php echo '<p>A total of '.count($mwd_lists).' lists were found in your MailChimp account.</p>'; 
							if($mwd_lists):
								foreach($mwd_lists as $mwd_list): ?>
								<table class="mwd-list-table" cellspacing="0">
									<tbody>
										<tr>
											<td><a href="<?php echo add_query_arg(array('list_id' => $mwd_list->id), admin_url('admin.php?page=manage_lists&task=view')); ?>"><h3><?php echo $mwd_list->name; ?></h3></a></td>
											<td><strong>List ID: <?php echo $mwd_list->id; ?></strong></td>
											<td><strong>Count of subscribers: <?php echo $mwd_list->member_count; ?></strong></td>
										</tr>
										<tr>
											<th>Fields</th>
											<td colspan=2 style="padding: 0; border: 0;">
												<table class="mwd-merge-vars-table" cellspacing="0">
													<thead>
														<tr>
															<th>Name</th>
															<th>Tag</th>
															<th>Type</th>
														</tr>
													</thead>
													<tbody>
													<?php foreach($mwd_list->merge_vars as $merge_var): 
														$required = $merge_var->req ? '<span style="color:red;">*</span>' : '';
														$title = $merge_var->name.' ('.$merge_var->tag.') with field type '.$merge_var->field_type.'.'; 
														?>
														<tr title="<?php echo $title; ?>">
															<td><?php echo $merge_var->name.$required; ?></td>
															<td><code><?php echo $merge_var->tag; ?></code></td>
															<td><?php echo $merge_var->field_type ?></td>
														</tr>
													<?php endforeach; ?>	
													</tbody>
												</table>
											</td>
										</tr>
										<?php if(!empty($mwd_list->interest_groups)): ?>
										<tr>
											<th>Interest Groupings</th>
											<td colspan=2 style="padding: 0; border: 0;">
												<table class="mwd-merge-vars-table" cellspacing="0">
													<thead>
														<tr>
															<th>Name</th>
															<th>Groups</th>
														</tr>
													</thead>
													<tbody>
													<?php foreach($mwd_list->interest_groups as $interest_group): ?>
														<tr>
															<td><?php echo $interest_group['name']; ?></td>
															<td>
																<?php if(!empty($interest_group['groups'])) {?>
																	<ul>
																		<?php foreach($interest_group['groups'] as $group) { 
																			echo '<li>'.$group['name'].'</li>';
																		} ?>
																	</ul>
																<?php }	?> 
															</td>
														</tr>
													<?php endforeach; ?>	
													</tbody>
												</table>
											</td>
										</tr>
										<?php endif; ?>
									</tbody>
								</table>
								<br style="margin: 20px 0;">
							<?php endforeach; ?>	
							<?php endif; ?>
						</div>
						<br/>
						<div>
							<button class="mwd-button mwd-renew-list extra-large">
								<span></span>
								Renew MailChimp lists
							</button>
							<button class="mwd-button mwd-manage-lists medium">
								<span></span>
								Manage lists
							</button>
						</div>
					</div>
					<?php endif; ?>
				</div>
				<?php if($is_api_valid): ?>
				<div class="col-md-4 col-sm-12 col-xs-12">	
					<div class="mwd-sidebar mwd-account-info">
						<h2>Account Overview</h2>
						<hr class="mwd-section-seperator"/>
						<img src="<?php echo $mwd_profile_info['avatar']; ?>" />
						<div class="profile-general">
							<span class="mwd-profile-name"><?php echo $mwd_account_details['contact']['fname'].'<br /> '.$mwd_account_details['contact']['lname']; ?></span><?php echo ' ('.$mwd_profile_info['role'].')'; ?>
							<p><?php echo $mwd_profile_info['username']; ?></p>
							<p><?php echo $mwd_profile_info['email']; ?></p>
						</div>
						<div>
							<div class="mwd-row">
								<div class="mwd-key company">
									<span></span>Company
								</div>
								<div class="mwd-value">
									<?php echo $mwd_account_details['contact']['company'].'<br/>'.$mwd_account_details['contact']['city'].' '.$mwd_account_details['contact']['country']; ?>
								</div>
							</div>
							<div class="mwd-row">
								<div class="mwd-key industry">
									<span></span>Industry
								</div>
								<div class="mwd-value">
									<?php echo isset($mwd_account_details['industry']) ? $mwd_account_details['industry'] : ''; ?>
								</div>
							</div>
							<div class="mwd-row">
								<div class="mwd-key member_since">
									<span></span>Member Since
								</div>
								<div class="mwd-value">
									<?php echo isset($mwd_account_details['member_since']) ? $mwd_account_details['member_since'] : ''; ?>
								</div>
							</div>
							<div class="mwd-row">
								<div class="mwd-key plan_type">
									<span></span>Plan Type
								</div>
								<div class="mwd-value">
									<?php echo isset($mwd_account_details['plan_type']) ? $mwd_account_details['plan_type'] : ''; ?>
								</div>
							</div>
							<div class="mwd-row">
								<div class="mwd-key emails_left">
									<span></span>Emails Left
								</div>
								<div class="mwd-value">
									<?php echo isset($mwd_account_details['emails_left']) ? $mwd_account_details['emails_left'] : ''; ?>
								</div>
							</div>
							<div class="mwd-row">
								<div class="mwd-key affiliate_link">
									<span></span>Affiliate Link
								</div>
								<div class="mwd-value">
									<input type="text" readonly="" value="<?php echo isset($mwd_account_details['affiliate_link']) ? $mwd_account_details['affiliate_link'] : ''; ?>" onclick="jQuery(this).select(); return false;" >
								</div>
							</div>
						</div>
					</div>
					<br />
					<br />
					<div class="mwd-sidebar mwd-cache-settings">
						<h2>API Cache Settings</h2>
						<hr class="mwd-section-seperator"/>
						<form action="<?php echo add_query_arg(array('apikey' => $apikey, 'nonce_mwd' => wp_create_nonce('nonce_mwd')), admin_url('admin.php?page=manage_mwd&task=clear_mailchimp_api_cache')); ?>" method="post" id="clear-api-cache">
							<p>Remove MailChimp-related data from your site cache. The cache will be stored for an hour.</p>
							<?php
							if($cache_cleared) { ?>
								<button class="mwd-button mwd-clear-api-cache large" disabled="disabled" title="No MailChimp data found in temporary cache storage"><span></span>Clear API Cache</button>
							<?php } else { ?>
								<button class="mwd-button mwd-clear-api-cache large" onclick="jQuery('#clear-api-cache').submit(); return false;"><span></span>Clear API Cache</button>
							<?php } ?>
							<input type="hidden" name="message_id" id="message_id" value="11" />
						</form>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<script>
			var mwd_adminurl = "<?php echo admin_url('admin.php'); ?>";
			
			function mwd_connect_to_mailchimp() {
				if(jQuery('#mwd_mailchimp_apikey').val()) {
					if(!jQuery('#mwd_mailchimp_apikey').attr('disabled'))
						jQuery('.mwd-load-mailchimp-status').css('background', 'url(<?php echo MWD_URL . '/images/load.gif'; ?>) no-repeat 0% 50%').show();
						return jQuery.ajax({
						url:'<?php echo add_query_arg(array('action' => 'helper', 'task' => 'mwd_params', 'nonce_mwd' => wp_create_nonce('nonce_mwd')), admin_url('admin-ajax.php')); ?>'+'&apikey='+jQuery('#mwd_mailchimp_apikey').val(),
						dataType: "json",
						success:function(data) {
							var lists = data['lists'];
							jQuery('.mwd-load-mailchimp-status').css('background', 'url(<?php echo MWD_URL . '/images/icons.png'; ?>) no-repeat 90% 25%');
							
							jQuery('.mwd-mailchimp-loaded').html('2/3');
							jQuery('.mwd-mailchimp-loaded').css('width','33.33%');
							jQuery('.mwd-mailchimp-progress-bar').find('label').html('Choose Mailchimp List');
							jQuery('.mwd-mailchimp-connect-tab').hide();
							jQuery('.mwd-mailchimp-list-tab').show();
						
							for (var key in lists) {
								jQuery('#mwd_mailchimp_list').append('<option value="'+lists[key]["id"]+'">'+ lists[key]["name"]+'</option>');
							}
													
							renew_lists(lists);
							renew_account_data(data['account_details'], data['profile_info']);
						},
						error:function(err){
							jQuery('.mwd-load-mailchimp-status').css('background', 'none');
							jQuery('.mwd-load-mailchimp-message').html(err.responseText);
							jQuery('.mwd-mailchimp-data').html('');
							jQuery('.mwd-account-info').html('');
						}
					});
				}
			}
			
			jQuery( document ).ready(function() {
				jQuery('.mwd-load-mailchimp').on('click', function() {
					mwd_connect_to_mailchimp();
				});	
				
				jQuery('.mwd-renew-list').on('click', function() {
					if(<?php echo $is_api_valid; ?> && jQuery('#mwd_mailchimp_apikey').val()) {
						jQuery.ajax({
							url:'<?php echo add_query_arg(array('action' => 'helper', 'task' => 'mwd_params', 'apikey' => $apikey, 'nonce_mwd' => wp_create_nonce('nonce_mwd')), admin_url('admin-ajax.php')); ?>',
							dataType: "json",
							success:function(data) {
								jQuery('.updated').next().remove();
								jQuery('.updated').remove();
								
								var lists = data['lists'];
								renew_lists(lists);
							},
							error:function(err){
								//console.log(err);
							}
						});
					}
				});	
			
				
				jQuery('.mwd-skip').on('click', function() {
					location.reload();
				});
			
				jQuery('.mwd-load-mailchimp-list').on('click', function() {
					jQuery('.mwd-mailchimp-loaded').html('3/3');
					jQuery('.mwd-mailchimp-loaded').css('width','66.66%');
					jQuery('.mwd-mailchimp-progress-bar').find('label').html('Create Form');
					jQuery('.mwd-mailchimp-list-tab').hide();
					jQuery('.mwd-mailchimp-opt-in-form-tab').show();
					jQuery('#mwd_list_id').val(jQuery('#mwd_mailchimp_list').val());
				});
				
				jQuery('.mwd-create-form').on('click', function() {
					window.location = '<?php echo add_query_arg(array('task' => 'add', 'nonce_mwd' => wp_create_nonce('nonce_mwd')), admin_url('admin.php?page=manage_forms')); ?>&list_id='+jQuery('#mwd_list_id').val()+'&form_type='+jQuery('input[name=form_type]:checked').val();
				});
				
				jQuery('.mwd-manage-lists').on('click', function() {
					window.location.href = '<?php echo esc_url(admin_url('admin.php?page=manage_lists')); ?>';
				});
				
				jQuery('.mwd-change-api').click(function() {
					jQuery('#mwd_mailchimp_apikey').removeAttr('disabled');
					jQuery('.mwd-load-mailchimp-status').css('background', 'none');
					jQuery(this).hide();
					jQuery('.mwd-renew-api').show();
				});
				
				jQuery('.mwd-renew-api').click(function() {
					var renew_this = jQuery(this);	
					jQuery('.updated').next().remove();
					jQuery('.updated').remove();
					mwd_connect_to_mailchimp().done(function() {
						jQuery('#mwd_mailchimp_apikey').attr('disabled', true);
						jQuery('.mwd-load-mailchimp-message').empty();
						jQuery(renew_this).hide();
						jQuery('.mwd-change-api').show();
					});
				});
				
				
			});
		
		</script>
		
		<?php
	}
	
	public function clear_mailchimp_api_cache($message_id) {
		delete_transient('mwd-list-info');
		delete_transient('mwd-profile-info');
		delete_transient('mwd-account-details');
		delete_transient('mwd-lists');
		delete_transient('mwd-subscribers-data');
		delete_transient('mwd-list-data');
		delete_transient('mwd-subscriber-data');

		MWD_Library::mwd_redirect(add_query_arg(array('message' => $message_id), admin_url('admin.php?page=manage_mwd')));
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