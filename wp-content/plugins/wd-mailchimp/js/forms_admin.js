jQuery( document ).ready( function() {
	jQuery('.pp_display_on #pt0').click( function() {
		var isChecked = jQuery(this).prop('checked');
		jQuery('.pp_display_on input[type="checkbox"]').prop('checked', isChecked);
		if(isChecked){
			jQuery('.mwd-pp-show').show();
		}
		else{
			jQuery('.mwd-pp-show, .mwd-cat-show').hide();
		}
	});

	jQuery('.pp_display_on input[type="checkbox"]:not("#pt0")').click( function() {
		var isChecked = jQuery(this).prop('checked');
		var everythingChecked = jQuery('.pp_display_on #pt0').prop('checked');
		if(everythingChecked && !isChecked){
			jQuery('.pp_display_on #pt0').prop('checked', false);
		}
	});

	jQuery('.pp_display_on #pt5').click( function() {
		var isChecked = jQuery(this).prop('checked');
		if(isChecked){
			jQuery('.mwd-pp-show').show();

		} else{
			jQuery('.mwd-pp-show').hide();
		}
	});

	jQuery('.pp_display_on input:checkbox[class=catpost]').click( function() {
		var posts = [];
		jQuery("input:checkbox[class=catpost]:checked").each(function(){
			posts.push(jQuery(this).val());
		});

		if(posts.length){
			jQuery('.mwd-pp-show, .mwd-cat-show').show();

		} else{
			jQuery('.mwd-pp-show, .mwd-cat-show').hide();
		}
	});

	jQuery('body').on( 'focusin', '.pp_search_posts', function() {
		var this_input = jQuery( this );
		this_input.closest('ul').find( '.pp_live_search' ).removeClass('mwd-hide');
		if ( ! this_input.hasClass( 'already_triggered' ) ) {
			this_input.addClass( 'already_triggered' );

			pp_live_search( this_input, 0, true );
		}
	});

	jQuery( document ).click( function() {
		jQuery( '.pp_live_search' ).addClass('mwd-hide');
	});

	jQuery( 'body' ).on( 'click', '.pp_search_posts', function() {
		return false;
	});

	jQuery( 'body' ).on( 'input', '.pp_search_posts', function() {
		pp_live_search( jQuery( this ), 500, true );
	});

	jQuery( 'body' ).on( 'click', '.pp_search_results li', function() {
		var this_item = jQuery( this );

		if ( !this_item.hasClass('pp_no_res') ) {
			var text = this_item.text(),
				id = this_item.data('post_id'),
				main_container = this_item.closest('.mwd-pp'),
				display_box = main_container.find('.pp_selected'),
				value_field = main_container.find( '.pp_exclude' ),
				new_item = '<span data-post_id="' + id + '">' + text + '<span class="pp_selected_remove">x</span></span>';

			if (-1 === display_box.html().indexOf('data-post_id="' + id + '"')) {
				display_box.append( new_item );
				if ( '' === value_field.val() ) {
					value_field.val( id );
				} else {
					value_field.val( function( index, value ) {
						return value + "," + id;
					});
				}
			}
		}

		return false;
	});

	jQuery( 'body' ).on( 'click', '.pp_selected span.pp_selected_remove', function() {
		var this_item = jQuery( this ).parent(),
			value_field = this_item.closest('.mwd-pp').find('.pp_exclude'),
			value_string = value_field.val(),
			id = this_item.data('post_id');
		if (-1 !== value_string.indexOf(id)) {
			var str_toreplace = -1 !== value_string.indexOf( ',' + id ) ? ',' + id : id + ',',
				str_toreplace = -1 !== value_string.indexOf( ',' ) ? str_toreplace : id,
				new_value = value_string;

			new_value = value_string.replace(str_toreplace, '');
			value_field.val( new_value );
		}

		this_item.remove();
		return false;
	});
});

function mwd_apply_options(task) {
	mwd_set_input_value('task', task);
	document.getElementById('adminForm').submit();
}

function pp_live_search( input, delay, full_content ) {
	var this_input = input,
		search_value = this_input.val(),
		post_type = this_input.data('post_type');

	setTimeout( function(){
		if ( search_value === this_input.val() ) {
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action : 'helper',
					task : 'mwd_live_search',
					nonce_mwd : nonce_mwd,
					pp_live_search : search_value,
					pp_post_type : post_type,
					pp_full_content : full_content
				},
				beforeSend: function( data ) {
					this_input.css('width','95%');
					this_input.parent().find('.mwd-loading').css('display','inline-block');
				},
				success: function( data ) {
					this_input.css('width','100%');
					this_input.parent().find('.mwd-loading').css('display', 'none');
					/* if ( true === full_content ) { */
						this_input.closest('.mwd-pp').find('.pp_search_results').replaceWith(data);
					/* } else {
						this_input.closest('.mwd-pp').find('.pp_search_results').append(data);
					} */
				},
				error: function( err ) {
					console.log(err);
				}
			});
		}
	}, delay );
}

function mwd_toggle(elem) {
	jQuery(elem).parent().next().toggleClass('hide');
}

function change_tab(elem) {
	jQuery('.mwd-subscriber-header .mwd-button').removeClass('active-button');
	jQuery('.mwd-subscriber-header .'+elem).addClass('active-button');
	jQuery('.mwd-subscriber-content').hide();
	jQuery('.'+elem+'-tab').show();
}

function change_form_type(type){
	jQuery('.mwd-form-types span').removeClass('active');
	jQuery('.mwd-form-types').find('.mwd-'+type).addClass('active');
	jQuery('#type_settings_fieldset tr').removeClass('mwd-show').addClass('mwd-hide');
}

function toggle_delete(elem){
	if(elem.prop("checked"))
		jQuery(".mwd-delete-subscriber").removeAttr("disabled");
	else
	jQuery(".mwd-delete-subscriber").attr("disabled","disabled");
}


function mwd_select_value(obj) {
	event.stopPropagation();
	obj.focus();
	obj.select();
}

function mwd_change_radio_checkbox_text(elem) {
	var labels_array = [];
		labels_array['paypal_mode'] = ['Off', 'On'];
		labels_array['checkout_mode'] = ['Testmode', 'Production'];
		labels_array['mail_mode'] = ['Text', 'HTML'];
		labels_array['mail_mode_user'] = ['Text', 'HTML'];
		labels_array['popover_show_on'] = ['Page Exit', 'Page Load'];
		labels_array['topbar_position'] = ['Bottom', 'Top'];
		labels_array['scrollbox_position'] = ['Left', 'Right'];
		labels_array['value'] = ['1', '0'];

	jQuery(elem).val(labels_array['value'][jQuery(elem).val()]);
	jQuery(elem).next().val(jQuery(elem).val());

	var clicked_element = labels_array[jQuery(elem).attr('name')];
	jQuery(elem).find('label').html(clicked_element[jQuery(elem).val()]);
	if(jQuery( elem ).hasClass( "mwd-text-yes" )) {
		jQuery( elem ).removeClass('mwd-text-yes').addClass('mwd-text-no');
		jQuery(elem).find("span").animate({
			right: parseInt(jQuery( elem ).css( "width")) - 14 + 'px'
		}, 400, function() {
		});
	}
	else {
		jQuery( elem ).removeClass('mwd-text-no').addClass('mwd-text-yes');
		jQuery(elem).find("span").animate({
			right: 0
		}, 400, function() {
		});
	}
}

function set_condition() {
	field_condition = '';
	jQuery('.mwd-condition').each(function() {
		conditions = '';
		cond_id = jQuery(this)[0].id.replace('condition','');

		field_condition += jQuery("#show_hide"+cond_id).val()+"*:*show_hide*:*";
		field_condition += jQuery("#fields"+cond_id).val()+"*:*field_label*:*";
		field_condition += jQuery("#all_any"+cond_id).val()+"*:*all_any*:*";

		this2 = this;
		jQuery(this2).find(jQuery('.cond_fields')).each(function() {
			cond_fieldid = jQuery(this)[0].id.replace('condition_div'+cond_id+'_','');
			conditions += jQuery("#field_labels"+cond_id+"_"+cond_fieldid).val()+"***";
			conditions += jQuery("#is_select"+cond_id+"_"+cond_fieldid).val()+"***";

			if(jQuery("#field_value"+cond_id+"_"+cond_fieldid).prop("tagName")=="SELECT" && jQuery("#field_value"+cond_id+"_"+cond_fieldid).attr('multiple'))
			{
				sel = jQuery("#field_value"+cond_id+"_"+cond_fieldid)[0];
				selValues = '';
				for(m=0; m < sel.length; m++)
				{
					if(sel.options[m].selected)
						selValues += sel.options[m].value+"@@@";
				}
				conditions+=selValues;
			}
			else
				conditions+=jQuery("#field_value"+cond_id+"_"+cond_fieldid).val();
				conditions+="*:*next_condition*:*";
		});

		field_condition+=conditions;
		field_condition+="*:*new_condition*:*";
	});

	document.getElementById('condition').value = field_condition;
}
function show_mixed_fields(){
	var tagsInForm = jQuery('#tagsInForm').val().split(',');
	var groupsInForm = jQuery('#groupsInForm').val().split(',');

	var data = JSON.parse(lists);
	var tags = [], gids = [];
	var mergeNameExist = [], groupNameExist = [];
	mergeParams = [];
	groupParams = {};

	jQuery('.list-fields').empty();
	var groupToAddAfter = '';
	jQuery('.mwd_lists input[type="checkbox"]').each(function(ind) {
		var listId = jQuery(this).val();
		var listData = data[listId];
		var listName = listData['name'];
		if(jQuery(this).prop('checked') == true){
			var merge_vars = listData['merge_vars'];
			if( merge_vars ){
				if( jQuery('.list-fields').find('.listfields').length == 0 )
					jQuery('.list-fields').append('<div class="mini-label listfields">List fields</div>');
				
				var n = merge_vars.length;
				for(var l=0; l<n; l++){
					if(!inArray(merge_vars[l]['tag'], tags)){
						if(inArray(merge_vars[l]['name'], mergeNameExist))
							merge_vars[l]['name'] += '(1)';

						if(merge_vars[l]['choices']){
							mergeParams[merge_vars[l]['tag']] = merge_vars[l]['choices'];
						}

						var reqclass = merge_vars[l]['req'] == true ? 'isRequired' : '';
						var informclass = inArray(merge_vars[l]['tag'], tagsInForm) ? 'inForm' : 'noInForm';
						jQuery('.list-fields').append('<button data-fieldType="'+merge_vars[l]['field_type']+'" data-tag="'+merge_vars[l]['tag']+'" data-req="'+merge_vars[l]['req']+'" class="'+informclass + ' ' + reqclass+'" onclick="add_mailchimp_field(this); return false;">'+merge_vars[l]['name']+'</button>');
					}

					tags.push(merge_vars[l]['tag']);
					groupNameExist.push(merge_vars[l]['name']);
				}
			}

			var interestGroups = listData['interest_groups'];
			if( interestGroups ){
				var m = interestGroups.length;
				for(var k=0; k<m; k++){
					if(inArray(interestGroups[k]['name'], groupNameExist))
						interestGroups[k]['name'] += '(1)';

					groupParams[interestGroups[k]['id']] = interestGroups[k]['groups'].map(function(el){ return el['name'];});
					if(!inArray(interestGroups[k]['id'], gids)) {
						if( groupToAddAfter == '' )
							groupToAddAfter += '<br /><br /><div class="mini-label intgroup">Interest groups</div>';
						
						var informclass = inArray(interestGroups[k]['id'], groupsInForm) ? 'inForm' : 'noInForm';
						groupToAddAfter += '<button data-fieldtype="'+interestGroups[k]['form_field']+'" data-id="'+interestGroups[k]['id']+'" class="'+informclass +'" onclick="add_mailchimp_group(this); return false;">'+interestGroups[k]['name']+'</button>';

						gids.push(interestGroups[k]['id']);
						groupNameExist.push(interestGroups[k]['name']);
					}
				}
			}
		}
	});
	
	jQuery('.list-fields').append(groupToAddAfter);
}

function inArray(needle, haystack) {
	var length = haystack.length;
	for(var i = 0; i < length; i++) {
		if(haystack[i] == needle) return true;
	}
	return false;
}


function change_hide_show(className){
	jQuery('.'+className+'.mwd-hide').removeClass('mwd-hide').addClass('mwd-temporary');
	jQuery('.'+className+'.mwd-show').removeClass('mwd-show').addClass('mwd-hide');
	jQuery('.'+className+'.mwd-show-table').removeClass('mwd-show-table').addClass('mwd-hide');
	jQuery('.'+className+'.mwd-temporary').removeClass('mwd-temporary').addClass('mwd-show');
}


function mwd_show_hide(class_name){
	if(jQuery('.'+class_name).hasClass('mwd-hide'))
		jQuery('.'+class_name).removeClass('mwd-hide').addClass('mwd-show');
	else
		jQuery('.'+class_name).removeClass('mwd-show').addClass('mwd-hide');

}

function mwd_doNothing(event) {
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13) {
		if (event.preventDefault) {
			event.preventDefault();
		}
		else {
			event.returnValue = false;
		}
	}
}

function mwd_ajax_save(form_id) {
  var search_value = jQuery("#search_value").val();
  var current_id = jQuery("#current_id").val();
  var page_number = jQuery("#page_number").val();
  var search_or_not = jQuery("#search_or_not").val();
  var ids_string = jQuery("#ids_string").val();
  var image_order_by = jQuery("#image_order_by").val();
  var asc_or_desc = jQuery("#asc_or_desc").val();
  var ajax_task = jQuery("#ajax_task").val();
  var image_current_id = jQuery("#image_current_id").val();
  ids_array = ids_string.split(",");

  var post_data = {};
  post_data["search_value"] = search_value;
  post_data["current_id"] = current_id;
  post_data["page_number"] = page_number;
  post_data["image_order_by"] = image_order_by;
  post_data["asc_or_desc"] = asc_or_desc;
  post_data["ids_string"] = ids_string;
  post_data["task"] = "ajax_search";
  post_data["ajax_task"] = ajax_task;
  post_data["image_current_id"] = image_current_id;

  jQuery.post(
    jQuery('#' + form_id).action,
    post_data,

    function (data) {
      var str = jQuery(data).find('#images_table').html();
      jQuery('#images_table').html(str);
      var str = jQuery(data).find('#tablenav-pages').html();
      jQuery('#tablenav-pages').html(str);
      jQuery("#show_hide_weights").val("Hide order column");
      mwd_show_hide_weights();
      mwd_run_checkbox();
    }
  ).success(function (jqXHR, textStatus, errorThrown) {
  });
  return false;
}

function mwd_run_checkbox() {
  jQuery("tbody").children().children(".check-column").find(":checkbox").click(function (l) {
    if ("undefined" == l.shiftKey) {
      return true
    }
    if (l.shiftKey) {
      if (!i) {
        return true
      }
      d = jQuery(i).closest("form").find(":checkbox");
      f = d.index(i);
      j = d.index(this);
      h = jQuery(this).prop("checked");
      if (0 < f && 0 < j && f != j) {
        d.slice(f, j).prop("checked", function () {
          if (jQuery(this).closest("tr").is(":visible")) {
            return h
          }
          return false
        })
      }
    }
    i = this;
    var k = jQuery(this).closest("tbody").find(":checkbox").filter(":visible").not(":checked");
    jQuery(this).closest("table").children("thead, tfoot").find(":checkbox").prop("checked", function () {
      return(0 == k.length)
    });
    return true
  });
  jQuery("thead, tfoot").find(".check-column :checkbox").click(function (m) {
    var n = jQuery(this).prop("checked"), l = "undefined" == typeof toggleWithKeyboard ? false : toggleWithKeyboard, k = m.shiftKey || l;
    jQuery(this).closest("table").children("tbody").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked", function () {
      if (jQuery(this).is(":hidden")) {
        return false
      }
      if (k) {
        return jQuery(this).prop("checked")
      } else {
        if (n) {
          return true
        }
      }
      return false
    });
    jQuery(this).closest("table").children("thead,  tfoot").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked", function () {
      if (k) {
        return false
      } else {
        if (n) {
          return true
        }
      }
      return false
    })
  });
}

// Set value by id.
function mwd_set_input_value(input_id, input_value) {
	if (document.getElementById(input_id)) {
		document.getElementById(input_id).value = input_value;
	}
}

// Submit form by id.
function mwd_form_submit(event, form_id, task, id) {
  if (document.getElementById(form_id)) {
    document.getElementById(form_id).submit();
  }
  if (event.preventDefault) {
    event.preventDefault();
  }
  else {
    event.returnValue = false;
  }
}

// Check if required field is empty.
function mwd_check_required(id, name) {
  if (jQuery('#' + id).val() == '') {
    alert(name + '* field is required.');
    jQuery('#' + id).attr('style', 'border-color: #FF0000; border-style: solid; border-width: 1px;');
    jQuery('#' + id).focus();
    jQuery('html, body').animate({
      scrollTop:jQuery('#' + id).offset().top - 200
    }, 500);
    return true;
  }
  else {
    return false;
  }
}

// Show/hide order column and drag and drop column.
function mwd_show_hide_weights() {
  if (jQuery("#show_hide_weights").val() == 'Show order column') {
    jQuery(".connectedSortable").css("cursor", "default");
    jQuery("#tbody_arr").find(".handle").hide(0);
    jQuery("#th_order").show(0);
    jQuery("#tbody_arr").find(".mwd_order").show(0);
    jQuery("#show_hide_weights").val("Hide order column");
    if (jQuery("#tbody_arr").sortable()) {
      jQuery("#tbody_arr").sortable("disable");
    }
  }
  else {
    jQuery(".connectedSortable").css("cursor", "move");
    var page_number;
    if (jQuery("#page_number") && jQuery("#page_number").val() != '' && jQuery("#page_number").val() != 1) {
      page_number = (jQuery("#page_number").val() - 1) * 20 + 1;
    }
    else {
      page_number = 1;
    }
    jQuery("#tbody_arr").sortable({
      handle:".connectedSortable",
      connectWith:".connectedSortable",
      update:function (event, tr) {
        jQuery("#draganddrop").attr("style", "");
        jQuery("#draganddrop").html("<strong><p>Changes made in this table should be saved.</p></strong>");
        var i = page_number;
        jQuery('.mwd_order').each(function (e) {
          if (jQuery(this).find('input').val()) {
            jQuery(this).find('input').val(i++);
          }
        });
      }
    });//.disableSelection();
    jQuery("#tbody_arr").sortable("enable");
    jQuery("#tbody_arr").find(".handle").show(0);
    jQuery("#tbody_arr").find(".handle").attr('class', 'handle connectedSortable');
    jQuery("#th_order").hide(0);
    jQuery("#tbody_arr").find(".mwd_order").hide(0);
    jQuery("#show_hide_weights").val("Show order column");
  }
}

function mwd_check_isnum(e) {
	var chCode1 = e.which || e.paramlist_keyCode;
	if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57) && (chCode1 != 46) && (chCode1 != 45)) {
		return false;
	}
	return true;
}

function mwd_add_preview_image(files, image_for, slide_id, layer_id){

}

function mwdOpenMediaUploader(e, callback){
   if(typeof callback == "undefined"){
       callback = false;
   }
   e.preventDefault();
	var custom_uploader = wp.media({
       title: 'Upload',
       button: {
           text: 'Add Image'
       },
       multiple: false
   })
   .on('select', function() {
		var attachment = custom_uploader.state().get('selection').first().toJSON();
		jQuery('#header_image_url').val(attachment.url);
		jQuery("#header_image").css("background-image", 'url("' + attachment.url + '")');
		jQuery("#header_image").css("background-position", 'center');
	})
	.open();

	return false;
}
