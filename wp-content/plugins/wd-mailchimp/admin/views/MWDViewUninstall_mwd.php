<?php

class MWDViewUninstall_mwd{
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
		global $wpdb;
		$prefix = $wpdb->prefix;
		?>
		<div class="mwd-mailchimp">
			<form method="post" action="admin.php?page=uninstall_mwd" style="width:99%;">
				<?php wp_nonce_field('nonce_mwd', 'nonce_mwd'); ?>
				<div class="wrap">
					<div class="mwd-page-banner">
						<div class="mwd-logo">
						</div>
						<h1>Uninstall MailChimp WD</h1>
					</div>
					<br />
					<div class="goodbye-text">
					Before uninstalling the plugin, please Contact our <a href="https://web-dorado.com/support/contact-us.html" target= '_blank'>support team</a>. We'll do our best to help you out with your issue. We value each and every user and value what’s right for our users in everything we do.<br>
					However, if anyway you have made a decision to uninstall the plugin, please take a minute to <a href="https://web-dorado.com/support/contact-us.html" target= '_blank'>Contact us</a> and tell what you didn't like for our plugins further improvement and development. Thank you !!!
					</div>	
					<br />		
					<p>
						Deactivating MailChimp WD plugin does not remove any data that may have been created, such as the Forms and the Submissions. To completely remove this plugin, you can uninstall it here.
					</p>
					<p style="color: red;">
						<strong>WARNING:</strong>
						Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.
					</p>
					<p style="color: red">
						<strong>The following WordPress Options/Tables will be DELETED:</strong>
					</p>
					<table class="widefat">
						<thead>
							<tr>
								<th>Database Tables</th>
							</tr>
						</thead>
						<tr>
							<td valign="top">
								<ol>
									<li><?php echo $prefix; ?>mwd_forms</li>
									<li><?php echo $prefix; ?>mwd_forms_backup</li>
									<li><?php echo $prefix; ?>mwd_forms_blocked</li>
									<li><?php echo $prefix; ?>mwd_forms_submits</li>
									<li><?php echo $prefix; ?>mwd_forms_views</li>
									<li><?php echo $prefix; ?>mwd_display_options</li>
									<li><?php echo $prefix; ?>mwd_themes</li>
									<li><?php echo $prefix; ?>mwd_forms_sessions</li>
								</ol>
							</td>
						</tr>
					</table>
					<p style="text-align: center;">
						Do you really want to uninstall MaliChimp WD?
					</p>
					<p style="text-align: center;">
						<input type="checkbox" name="MailChimpWD" id="check_yes" value="yes" />&nbsp;<label for="check_yes">Yes</label>
					</p>
					<p style="text-align: center;">
						<input type="submit" value="UNINSTALL" class="button-primary" onclick="if (check_yes.checked) {  if (confirm('You are About to Uninstall MailChimp WD.\nThis Action Is Not Reversible.')) { mwd_set_input_value('task', 'mwd_uninstall'); } else { return false; } } else { return false; }" />
					</p>
				</div>
				<input id="task" name="task" type="hidden" value="" />
			</form>
		</div>
		<?php
	}

	public function mwd_uninstall() {
		global $wpdb;
		$this->model->delete_db_tables();
		$prefix = $wpdb->prefix;
		$deactivate_url = add_query_arg(array('action' => 'deactivate', 'plugin' => 'wd-mailchimp/wd-mailchimp.php'), admin_url('plugins.php'));
		$deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_wd-mailchimp/wd-mailchimp.php');
		?>
		<div id="message" class="updated">
			<p>The following Database Tables succesfully deleted:</p>
			<p><?php echo $prefix; ?>mwd_forms,</p>
			<p><?php echo $prefix; ?>mwd_forms_backup,</p>
			<p><?php echo $prefix; ?>mwd_forms_blocked,</p>
			<p><?php echo $prefix; ?>mwd_forms_submits,</p>
			<p><?php echo $prefix; ?>mwd_forms_views,</p>
			<p><?php echo $prefix; ?>mwd_display_options,</p>
			<p><?php echo $prefix; ?>mwd_themes,</p>
			<p><?php echo $prefix; ?>mwd_forms_sessions.</p>
		</div>
		<div class="wrap">
			<h2>Uninstall MailChimp WD</h2>
			<p><strong><a href="<?php echo $deactivate_url; ?>">Click Here</a> To Finish the Uninstallation and MailChimp WD will be Deactivated Automatically.</strong></p>
			<input id="task" name="task" type="hidden" value="" />
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