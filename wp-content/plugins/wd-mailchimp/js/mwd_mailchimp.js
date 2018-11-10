function renew_lists(data){
	jQuery('.mwd-mailchimp-data').html('');
	var mailchimp_data = '';
		mailchimp_data += '<p>A total of '+ Object.keys(data).length +' lists were found in your MailChimp account.</p>';
console.log(data);
	for (var key in data) {
		if (data.hasOwnProperty(key)) {
			mailchimp_data	+= 
				'<table class="mwd-list-table" cellspacing="0">\
					<tbody>\
						<tr>\
							<td><a href="'+mwd_adminurl+'?page=manage_lists&task=view&list_id='+data[key]["id"]+'"><h3>'+data[key]["name"]+'</h3></a></td>\
							<td><strong>List ID: '+data[key]["id"]+'<strong></td>\
							<td><strong>Count of subscribers: '+data[key]["member_count"]+'<strong></td>\
						</tr>\
						<tr>\
							<th>Fields</th>\
							<td colspan=2 style="padding: 0; border: 0;">\
								<table class="mwd-merge-vars-table" cellspacing="0">\
									<thead>\
										<tr>\
											<th>Name</th>\
											<th>Tag</th>\
											<th>Type</th>\
										</tr>\
									</thead>\
									<tbody>';
				for (var merge_key in data[key]["merge_vars"]) {
					if (data[key]["merge_vars"].hasOwnProperty(merge_key)) {
						var merge_vars_params = data[key]["merge_vars"][merge_key];
						var title = merge_vars_params['name']+' ('+merge_vars_params['tag']+') with field type '+merge_vars_params['field_type']+'.';
						var required = merge_vars_params['req'] ? '<span style="color:red;">*</span>' : '';
						mailchimp_data +=  '<tr title="'+title+'">\
											<td>'+merge_vars_params['name']+required+'</td>\
											<td><code>'+merge_vars_params['tag']+'</code></td>\
											<td>'+merge_vars_params['field_type']+'</td>\
										</tr>';
					}					
				}						
				mailchimp_data += '</tbody>\
								</table>\
							</td>\
						</tr>';
					
				if(data[key]["interest_groups"].length > 0) {
					mailchimp_data += '<tr>\
							<th>Interest Groupings</th>\
							<td colspan=2 style="padding: 0; border: 0;">\
								<table class="mwd-merge-vars-table" cellspacing="0">\
									<thead>\
										<tr>\
											<th>Name</th>\
											<th>Groups</th>\
										</tr>\
									</thead>\
									<tbody>';
									for (var interest_group_key in data[key]["interest_groups"]) {
										var interest_group = data[key]["interest_groups"][interest_group_key];
										if(typeof interest_group === 'object'){
											mailchimp_data += '<tr>\
												<td>'+interest_group['name']+'</td>\
												<td>';
												if(interest_group["groups"].length > 0) {
													mailchimp_data += '<ul>';
													for (var group_key in interest_group["groups"]) {
														var group = interest_group["groups"][group_key];
														mailchimp_data += '\
															<li>'+group['name']+'</li>';
													} 
													mailchimp_data += '</ul>';
												}
												mailchimp_data	+= '</td>\
											</tr>';
										}
									}
									mailchimp_data	+= '</tbody>\
								</table>\
							</td>\
						</tr>';
				}
					mailchimp_data	+= '</tbody>\
				</table>\
				<br style="margin: 20px 0;">';
		}		
	}	
	jQuery('.mwd-mailchimp-data').html(mailchimp_data);
	jQuery('<div class="updated"><p><strong>MailChimp List was successfully renewed.</strong></p></div><br />').insertBefore('.mwd-mailchimp-data');
}

function renew_account_data(account, profile){
	jQuery('.mwd-account-info').html('');
	var account_info = '';
		account_info += '<img src="'+profile['avatar']+'" />\
			<div class="profile-general">\
				<span class="mwd-profile-name">'+account['contact']['fname']+'<br /> '+account['contact']['lname']+'</span> ('+profile['role']+') \
				<p>'+profile['username']+'</p>\
				<p>'+profile['email']+'</p>\
			</div>\
			<div class="mwd-mailchimp-connect-tab">\
				<div class="mwd-row">\
					<div class="mwd-key company">\
						<span></span>Company\
					</div>\
					<div class="mwd-value">\
						'+account['contact']['company']+'<br/>'+account['contact']['city']+' '+account['contact']['country']+'\
					</div>\
				</div>\
				<div class="mwd-row">\
					<div class="mwd-key industry">\
						<span></span>Industry\
					</div>\
					<div class="mwd-value">\
						'+account['industry']+'\
					</div>\
				</div>\
				<div class="mwd-row">\
					<div class="mwd-key member_since">\
						<span></span>Member Since\
					</div>\
					<div class="mwd-value">\
						'+account['member_since']+'\
					</div>\
				</div>\
				<div class="mwd-row">\
					<div class="mwd-key plan_type">\
						<span></span>Plan Type\
					</div>\
					<div class="mwd-value">\
						'+account['plan_type']+'\
					</div>\
				</div>\
				<div class="mwd-row">\
					<div class="mwd-key emails_left">\
						<span></span>Emails Left\
					</div>\
					<div class="mwd-value">\
						'+account['emails_left']+'\
					</div>\
				</div>\
				<div class="mwd-row">\
					<div class="mwd-key affiliate_link">\
						<span></span>Affiliate Link\
					</div>\
					<div class="mwd-value">\
						<input type="text" readonly="" value="'+account['affiliate_link']+'" onclick="jQuery(this).select(); return false;" >\
					</div>\
				</div>\
			</div>';
	
	jQuery('.mwd-account-info').html(account_info);
}