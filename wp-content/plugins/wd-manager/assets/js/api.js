
jQuery(document).ready(function () {
	if(! WDAPIVars.user_hash && (WDAdminVars.is_multisite && WDAPIVars.network_admin || !WDAdminVars.is_multisite)){
		checkIfLoggedIn();
	}
	if(jQuery("#wd_login").length){
		jQuery(document).keypress(function(e){
			if(e.which == 13){
				wdLogin(); 
				return false;
			}
		});
	}
});

function checkIfLoggedIn(){
    var data = {
		"type" : "user_id"
	}
    jQuery.ajax({
		type: "POST",
		dataType: 'json',
		url: WDAPIVars.user_data_url,
		data: data,
		xhrFields: {
			withCredentials: true
		},
		crossDomain: true,
		complete: function () {
		},
		success: function (response){ 
			//response = JSON.parse(response);
			if( response["user_id"] ){
				saveHash(response);
			}
			else{
				jQuery(".wd_spinner").hide();
				jQuery(".wd-login-sub").show();
			}
		},
		failure: function (errorMsg) {
			console.log('Failure' + errorMsg);
		},
		error: function (error) {
			console.log(error);
		}            
	}); 	
}

function saveHash(response){
	var data_ = {
		"user_hash" : response["user_id"],
		"user_full_name" : response["user_full_name"],
		"user_activation" : response["user_activation"],
		"action" : "get_user_hash",
		"nonce_WDD" : WDAPIVars.nonce
	};
	
	jQuery.post(WDAPIVars.ajax_url, data_, function (response_){
 		if(response_ != 'nohash'){
			window.location.href = 'admin.php?page=WDD_plugins&wdd_logged_in=1';
		}
		else{
			window.location.href = 'admin.php?page=WDD_plugins&err=1';
		} 
	}); 
}

function wdLogin(){
	var data = {};
	data.username = jQuery("#username").val();
	data.password = jQuery("#password").val();
	data.type = 'login_user';
	if(data.username == '' || data.password == ''){
		jQuery("#invalid_password, #activate_account").hide();
		jQuery("#required_fields").show();
		return false;
	}
	
  jQuery("#wd_login span.spinner").css({"visibility":"visible","display":"inline-block"});
  jQuery("#wd_login").addClass("disable");
    jQuery.ajax({
		type: "POST",
		dataType: 'json',
		url: WDAPIVars.user_data_url,
		data: data,
		xhrFields: {
			withCredentials: true
		},
		crossDomain: true,
		complete: function () {
		},
		success: function (response){
			if(response["user_id"]){
				saveHash(response);
				jQuery("#required_fields").hide();
			} else{
				if( response["user_activation"] == "" ){
					jQuery("#invalid_password").show();
				}
				else jQuery("#activate_account").show();
				jQuery("#required_fields").hide();
				jQuery("#wd_login span.spinner").css({"visibility":"hidden","display":"none"});
				jQuery("#wd_login").removeClass("disable");
			}
		},
		failure: function (errorMsg) {
			console.log('Failure' + errorMsg);
		},
		error: function (error) {
			console.log(error);
		}            
	});	
}

function wdLogout(obj){
	var data = {};
	data.type = 'logout_user';
    jQuery.ajax({
		type: "POST",
		dataType: 'json',
		url: WDAPIVars.user_data_url,
		data: data,
		xhrFields: {
			withCredentials: true
		},
		crossDomain: true,
		complete: function () {
		},
		success: function (response){		
			//obj.form.submit();
		},
		failure: function (errorMsg) {
			console.log('Failure' + errorMsg);
		},
		error: function (error) {
			console.log(error);
		}            
	});		
}



