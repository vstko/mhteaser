function mwd_check_page_load() {
  if (!document.getElementById('araqel') || (document.getElementById('araqel').value == '0')) {
    alert('Please wait while page is loading.');
    return false;
  }
  else {
    return true;
  }
}

function remove_whitespace(node) {
  var ttt;
  for (ttt = 0; ttt < node.childNodes.length; ttt++) {
    if (node.childNodes[ttt] && node.childNodes[ttt].nodeType == '3' && !/\S/.test(node.childNodes[ttt].nodeValue)) {
      node.removeChild(node.childNodes[ttt]);
      ttt--;
    }
    else {
      if (node.childNodes[ttt].childNodes.length) {
        remove_whitespace(node.childNodes[ttt]);
      }
    }
  }
  return;
}

function remove_empty_columns() {
	jQuery('.wdform_section').each(function() {
		if(jQuery(this).find('.wdform_column').last().prev().html()=='') {
			if(jQuery(this).children().length>2) {
				jQuery(this).find('.wdform_column').last().prev().remove();
				remove_empty_columns();
			}
		}	
	});
}


function sortable_columns() {
  jQuery( ".wdform_column" ).sortable({
		connectWith: ".wdform_column",
		cursor: 'move',
		placeholder: "highlight",
		start: function(e,ui){
			jQuery('.wdform_column').each(function() {
				if(jQuery(this).html()) {
					jQuery(this).append(jQuery('<div class="wdform_empty_row" style="height:80px;"></div>'));
					jQuery( ".wdform_column" ).sortable( "refresh" );
				}
			});			
		},
		update: function(event, ui) {
			jQuery('.wdform_section .wdform_column:last-child').each(function() {
				if(jQuery(this).html()) {
					jQuery(this).parent().append(jQuery('<div></div>').addClass("wdform_column"));	
					sortable_columns();
				}		
			});
		},
		stop: function(event, ui) {
			jQuery('.wdform_empty_row').remove();	
			remove_empty_columns();	
		}
  });
}

function all_sortable_events()
{
	jQuery(document).on( "click", ".wdform_row, .wdform_tr_section_break", function() {
		var this2=this; 
		setTimeout( function(){		
			if(jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).attr("class")=="wdform_arrows_show") {
				jQuery("#wdform_field"+jQuery(this2).attr("wdid")).css({"background-color":"#fff", "border":"none", "margin-top":""});
				jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).removeClass("wdform_arrows_show");
				jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).addClass("wdform_arrows");
				jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).hide();
			}
		else {
			jQuery(".wdform_arrows_show").addClass("wdform_arrows");
			jQuery(".wdform_arrows").hide();
			jQuery(".wdform_arrows_show").removeClass("wdform_arrows_show");
			jQuery(".wdform_field, .wdform_field_section_break").css("background-color","#fff");
			jQuery(".wdform_field").css("margin-top","");
			
			if(jQuery("#wdform_field"+jQuery(this2).attr("wdid")).attr("type")=='type_editor')
				jQuery("#wdform_field"+jQuery(this2).attr("wdid")).css("margin-top","-5px");
			
			jQuery("#wdform_field"+jQuery(this2).attr("wdid")).css({"background-color":"#fff"});
			jQuery("#wdform_field"+jQuery(this2).attr("wdid")).css({"border":"none"});
			jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).removeClass("wdform_arrows");
			jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).addClass("wdform_arrows_show");
			jQuery("#wdform_arrows"+jQuery(this2).attr("wdid")).show();
		}

	},300)});

	jQuery(document).on( "hover", ".wdform_tr_section_break", function() {
		jQuery("#wdform_field"+jQuery(this).attr("wdid")).css({"background-color":"#F5F5F5"});
	});

	jQuery(document).on( "hover", ".wdform_row", function() {
		jQuery("#wdform_field"+jQuery(this).attr("wdid")).css({"cursor":"move","background-color":"#F5F5F5"});
	});

	jQuery(document).on( "mouseleave", ".wdform_row, .wdform_tr_section_break", function() {
		jQuery("#wdform_field"+jQuery(this).attr("wdid")).css({"background-color":"#fff", "border":"none"});
		if(jQuery("#wdform_arrows"+jQuery(this).attr("wdid")).attr("class")!="wdform_arrows_show") {
			jQuery("#wdform_arrows"+jQuery(this).attr("wdid")).addClass("wdform_arrows");
		}
	});
}

jQuery(document).on( "dblclick", ".wdform_row, .wdform_tr_section_break", function() {
	edit(jQuery(this).attr("wdid"));
});
	
	
function mwd_change_radio(elem) {	
	if(jQuery( elem ).hasClass( "mwd-yes" )) {
		jQuery( elem ).val('0');
		jQuery( elem ).next().val('0');
		jQuery( elem ).removeClass('mwd-yes').addClass('mwd-no');
		jQuery(elem).find("span").animate({
			right: parseInt(jQuery( elem ).css( "width")) - 14 + 'px'
		}, 400, function() {
		}); 
	}	
	else {
		jQuery( elem ).val('1');
		jQuery( elem ).next().val('1');
		jQuery(elem).find("span").animate({
			right: 0
		}, 400, function() {
			jQuery( elem ).removeClass('mwd-no').addClass('mwd-yes');
		}); 
	}	
}
		
function enable_drag(elem) {
	if(jQuery('#enable_sortable').val() != 1) {
		jQuery('.wdform_column').sortable( "enable" );
		jQuery( ".wdform_arrows" ).slideUp(700);
		all_sortable_events();
	}
	else {
		jQuery('.wdform_column').sortable( "disable" );	
		jQuery(".wdform_column").css("border","none");		
		jQuery( ".wdform_row, .wdform_tr_section_break" ).die("click");
		jQuery( ".wdform_row" ).die("hover");
		jQuery( ".wdform_tr_section_break" ).die("hover");
		jQuery( ".wdform_field" ).css("cursor","default");
		jQuery( ".wdform_field, .wdform_field_section_break" ).css("background-color","#fff");
		jQuery( ".wdform_field, .wdform_field_section_break" ).css("border","none");
		jQuery( ".wdform_arrows_show" ).hide();
		jQuery( ".wdform_arrows_show" ).addClass("wdform_arrows");
		jQuery( ".wdform_arrows_show" ).removeClass("wdform_arrows_show");
		jQuery( ".wdform_arrows" ).slideDown(600);	
	}
	
	mwd_change_radio(elem);
}

function refresh_() {
	document.getElementById('counter').value = gen;
	for (i = 1; i <= form_view_max; i++) {
		if (document.getElementById('form_id_tempform_view' + i)) {
			if (document.getElementById('page_next_' + i)) {
				document.getElementById('page_next_' + i).removeAttribute('src');
      }
			if (document.getElementById('page_previous_' + i)) {
				document.getElementById('page_previous_' + i).removeAttribute('src');
      }
			document.getElementById('form_id_tempform_view' + i).parentNode.removeChild(document.getElementById('form_id_tempform_view_img' + i));
			document.getElementById('form_id_tempform_view' + i).removeAttribute('style');
		}
  }
	document.getElementById('form_front').value = document.getElementById('take').innerHTML;
}

function cmwd_create_input(toAdd_id, value_id, parent_id, cmwd_url) {
  var value = jQuery("#" + value_id).val();
  if (value) {
    jQuery("#" + value_id).attr("style", "width: 250px;");
    var mail_div = jQuery("<div>").attr("class", "mwd_mail_div").prependTo("#" + parent_id).text(value);
    jQuery("<img>").attr("src", cmwd_url + "/images/delete.png").attr("class", "mwd_delete_img").attr("onclick", "mwd_delete_mail(this, '" + value + "')").attr("title", "Delete Email").appendTo(mail_div);
    jQuery("#" + value_id).val("");
    jQuery("#" + toAdd_id).val(jQuery("#" + toAdd_id).val() + value + ",");
  }
}

function mwd_delete_mail(img, value) {
  jQuery(img).parent().remove();
  jQuery("#mail").val(jQuery("#mail").val().replace(value + ',', ''));
}

function form_maker_options_tabs(id) {
	if (mwd_check_email('mailToAdd') || mwd_check_email('paypal_email')) {
		return false;
	}
	jQuery("#fieldset_id").val(id);
	jQuery(".mwd_fieldset_active").removeClass("mwd_fieldset_active").addClass("mwd_fieldset_deactive");
	jQuery("#" + id + "_fieldset").removeClass("mwd_fieldset_deactive").addClass("mwd_fieldset_active");
	jQuery(".mwd_fieldset_tab").removeClass("active");
	jQuery("#" + id).addClass("active");
	return false;
}

function codemirror_for_javascript() {
  var editor = CodeMirror.fromTextArea(document.getElementById("form_javascript"), {
  lineNumbers: true,
  lineWrapping: true,
  mode: "javascript"
  });
  
  CodeMirror.commands["selectAll"](editor);
  editor.autoFormatRange(editor.getCursor(true), editor.getCursor(false));
  editor.scrollTo(0,0);
}

function set_type(type) {
	switch(type) {
		case 'post':
			document.getElementById('post').removeAttribute('style');
			document.getElementById('page').setAttribute('style','display:none');
			document.getElementById('url').setAttribute('style','display:none');
		break;
		case 'page':
			document.getElementById('page').removeAttribute('style');
			document.getElementById('post').setAttribute('style','display:none');
			document.getElementById('url').setAttribute('style','display:none');
		break;
		case 'url':
			document.getElementById('page').setAttribute('style','display:none');
			document.getElementById('post').setAttribute('style','display:none');
			document.getElementById('url').removeAttribute('style');
		break;
		case 'none':
			document.getElementById('page').setAttribute('style','display:none');
			document.getElementById('post').setAttribute('style','display:none');
			document.getElementById('url').setAttribute('style','display:none');
		break;
		case 'hide_form':
			document.getElementById('page').setAttribute('style','display:none');
			document.getElementById('post').setAttribute('style','display:none');
			document.getElementById('url').setAttribute('style','display:none');
		break;
	}
}

function insertAtCursor(myField, myValue) {
  if (myField.style.display == "none") {
    tinyMCE.execCommand('mceInsertContent', false, "%" + myValue + "%");
    return;
  }
  if (document.selection) {
    myField.focus();
    sel = document.selection.createRange();
    sel.text = myValue;
  }
  else if (myField.selectionStart || myField.selectionStart == '0') {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos)
      + "%" + myValue + "%"
      + myField.value.substring(endPos, myField.value.length);
  }
  else {
    myField.value += "%" + myValue + "%";
  }
}

function check_isnum(e) {
  var chCode1 = e.which || e.keyCode;
  if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57)) {
    return false;
  }
  return true;
}

function mwd_check_email(id) {
	if (document.getElementById(id) && jQuery('#' + id).val() != '') {
		var email_array = jQuery('#' + id).val().split(',');
		for (var email_id = 0; email_id < email_array.length; email_id++) {
			var email = email_array[email_id].replace(/^\s+|\s+$/g, '');
			if (email.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1) {
				alert('This is not a valid email address.');
				jQuery('#' + id).css('border', '1px solid #FF0000');
				jQuery('#' + id).focus();
				jQuery('html, body').animate({
					scrollTop:jQuery('#' + id).offset().top - 200
				}, 500);
				return true;
			}
		}
	}
	return false;
}

function mwd_check_req(is_email){
	if(!is_email)
		return false;
		
	var req_filled = false;
	jQuery('.merge-variables.admintable .mwd_options_value select').each(function(){
		if(jQuery(this).data('req') == true && jQuery(this).val() == 0){
			req_filled = true;
			alert("Please fill all mailchimp required field in 'MailChimp Options -> Correspondence of the fields'.");
			return false;
		}
	});

	return req_filled;
}

function mwd_edit_ip(id) {
	var ip = jQuery("#ip" + id).html();
	jQuery("#td_ip_" + id).html('<input id="ip' + id + '" class="input_th' + id + '" type="text" onkeypress="return mwd_check_isnum(event)" value="' + ip + '" name="ip' + id + '" />');
	jQuery("#td_edit_" + id).html('<button class="mwd-icon add-block-ip-icon" onclick="if (mwd_check_required(\'ip' + id + '\', \'IP\')) {return false;} mwd_set_input_value(\'task\', \'save\'); mwd_set_input_value(\'current_id\', ' + id + '); mwd_save_ip(' + id + '); return false;"></button>');
}

function mwd_save_ip(id) {
	var ip = jQuery("#ip" + id).val();
	var post_data = {};
	post_data["ip"] = ip;
	post_data["current_id"] = id;
	post_data["task"] = "save";

	jQuery.post(jQuery("#blocked_ips").attr("action"), post_data, function (data) {
			jQuery("#td_ip_" + id).html('<a id="ip' + id + '" class="pointer" title="Edit" onclick="mwd_edit_ip(' + id + ')">' + ip + '</a>');
			jQuery("#td_edit_" + id).html('<button class="mwd-icon edit-icon" onclick="mwd_edit_ip(' + id + ');"></button>');
		}	
	).success(function (data, textStatus, errorThrown) {
		jQuery(".update, .error").hide();
		jQuery("#mwd_blocked_ips_message").html("<div class='updated'><strong><p>Items Succesfully Saved.</p></strong></div>");
		jQuery("#mwd_blocked_ips_message").show();
	});
}

function wdhide(id) {
	document.getElementById(id).style.display = "none";
}
function wdshow(id) {
	document.getElementById(id).style.display = "block";
}
function delete_field_condition(id) {
	var cond_id = id.split("_");
	document.getElementById("condition"+cond_id[0]).removeChild(document.getElementById("condition_div"+id));
}

function change_choices(id, field_id) {
	jQuery.ajax({
		url: 'admin-ajax.php?action=conditions&task=change_choices&form_id='+jQuery('#current_id').val()+'&field_id='+field_id+'&num='+id,
		method: "POST",
		dataType: "html",
		success:function(data){
			console.log(data);
			jQuery("#field_choices"+id).html(data);
			jQuery("#condition_div"+id).show();
		},
		error:function(err){
		}
	});	
}

function add_condition_fields(cond_index) {
	var max_index = 0;
	jQuery('#condition'+cond_index).find(jQuery('.cond_fields')).each(function() {
		var value = parseInt(jQuery(this)[0].id.replace('condition_div'+cond_index+'_',''));
		max_index = (value >= max_index) ? value+1 : max_index;
	});

	jQuery.ajax({
		url: 'admin-ajax.php?action=conditions&task=add_condition_fields&form_id='+jQuery('#current_id').val()+'&cond_index='+cond_index+'&cond_fieldindex='+max_index+'&cond_fieldid='+jQuery('#fields'+cond_index).val(),
		method: "POST",
		dataType: "html",
		success:function(data){
			var condition_field = jQuery('<div id="condition_div'+cond_index+'_'+max_index+'" class="cond_fields" style="display:none;">').append(data);
			jQuery("#condition"+cond_index).append(condition_field);
		},
		error:function(err){
		}
	});	
}

function add_condition() {
	var max_id = 0;
	jQuery('.mwd-condition').each(function() {
		var value = parseInt(jQuery(this)[0].id.replace('condition',''));
		max_id = (value >= max_id) ? value+1 : max_id;
	});
	
	jQuery.ajax({
		url: 'admin-ajax.php?action=conditions&task=add_condition&form_id='+jQuery('#current_id').val()+'&cond_index='+max_id,
		method: "POST",
		dataType: "html",
		success:function(data){
			var condition = jQuery('<div id="condition'+max_id+'" class="mwd-condition">').append(data);
			jQuery("#mwd_conditions").append(condition);
		},
		error:function(err){
			console.log(err);
		}
	});	
}

function delete_condition(num) {
	jQuery('#mwd_conditions').find(jQuery('#condition'+num)).remove();
}

function acces_level(length) {
	var value='';
	for(i=0; i<=parseInt(length); i++) {
    if (document.getElementById('user_'+i).checked) {
      value=value+document.getElementById('user_'+i).value+',';			
    }	
  }
	document.getElementById('user_id_wd').value=value;
}

function check_isnum_space(e) {
	var chCode1 = e.which || e.keyCode;	
	if (chCode1 ==32) {
		return true;
  }
  if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57)) {
		return false;
  }
	return true;
}

function check_isnum_point(e) {
  var chCode1 = e.which || e.keyCode;	
	if (chCode1 ==46) {
		return true;
	}
	if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57)) {
    return false;
  }
	return true;
}
