////////////////////////////////////////////////////////////////////////////////////////
// Events                                                                             //
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
// Constants                                                                          //
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
// Variables                                                                          //
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
// Constructor & Destructor                                                           //
////////////////////////////////////////////////////////////////////////////////////////	

jQuery(document).ready(function ()
{
  /*Addon active tab*/
  if (typeof(Storage) !== "undefined" && typeof(localStorage.addontab) === "undefined") {
    if (jQuery(".wd_parent_plugins > div.active").length) {
      localStorage.addontab = jQuery(".wd_parent_plugins > div.active").attr("id");
    } else
      if (jQuery(".wd_parent_plugins > div").eq(0).length) {
        localStorage.addontab = jQuery(".wd_parent_plugins > div").eq(0).attr("id");
      }
  }
  /*COPY COUPONE CODE*/
  jQuery(".copy_code").click(function ()
  {
    var id = jQuery(this).prev("span").attr("id");
    WDDCopyToClipboard(id);
  });

  /* Delete, actevate, deactivate */
  // jQuery(".manage a, .delete_product a").click(function(){
  //  var action = jQuery(this).data("action");
  //  var nonce = jQuery(this).data("nonce");
  //  if(jQuery(this).closest("#wd_overview").hasClass("themes_page")){
  // 	 var theme = jQuery(this).data("theme");
  // 	 var select = "&plugin=" + plugin;
  // 	 if(WDAdminVars.is_multisite == "0"){
  // 		 window.location = WDD_options.themes_url + "?action=" + action + "&stylesheet=" + theme + "&from=WDD_themes&_wpnonce=" + nonce;
  // 	 }else{
  // 		 //if(action == "activate"){
  // 			 window.location = WDD_options.themes_url + "?action=" + action + "&stylesheet=" + theme + "&from=WDD_themes&_wpnonce=" + nonce;
  // 		 //}
  // 	 }
  //  } else {
  // 	 var plugin = jQuery(this).data("plugin");
  // 	 var select = "&plugin=" + plugin;
  // 	 if(action == "delete-selected"){
  // 			select = "&checked[0]=" + plugin;
  // 	 }
  // 	 if(jQuery(this).closest("#wd_overview").hasClass("plugins_page")){
  // 		 window.location = WDD_options.plugins_url + "?action=" + action + select + "&plugin_status=all&paged=1&from=WDD_plugins&_wpnonce=" + nonce;
  // 	 } else if(jQuery(this).closest("#wd_overview").hasClass("addons_page")){
  // 		 window.location = WDD_options.plugins_url + "?action=" + action + select + "&plugin_status=all&paged=1&from=WDD_addons&_wpnonce=" + nonce;
  // 	 }
  //  }
  // });


  jQuery(".wd-more a").click(function ()
  {
    if (jQuery(this).parent().find("span.spinner").length && false) {//TODO check false
      jQuery(this).parent().find("span.spinner").css({"visibility": "visible", "display": "inline-block"});
      jQuery(this).addClass("disable");
    }
  });

  /* Show update available products and get installed product tab */
  jQuery("#update_available").click(function ()
  {
    WDDchangeFilter("update_available");
    return false;
  });

  /* Filters */
  jQuery("#filter_content a:not(#update_available)").click(function ()
  {
    var value = jQuery(this).attr("id");
    WDDchangeFilter(value);
    return false;
  });

  /* Popup close*/
  jQuery(".wd_close").click(function (e)
  {
    jQuery(this).closest(".wd_popup").hide();
    jQuery("#" + jQuery(this).closest(".wd_popup").attr("id") + "_content").html('<span class="spinner"></span>');
  });
  jQuery(".wd_popup").click(function (e)
  {
    jQuery(this).hide();
    jQuery("#" + jQuery(this).attr("id") + "_content").html('<span class="spinner"></span>');
  });
  jQuery(document).keyup(function (e)
  {
    if (e.keyCode == 27) {
      jQuery(".wd_popup").hide();
      if (jQuery("#wd_iframe_content").length) {
        jQuery("#wd_iframe_content").html('<span class="spinner"></span>');
      }
      return false;
    }
  });

  /*IFRAME*/
  jQuery("body").on("click", ".wd-upgrade-free, .wd_tab_container:not(#free_plugins_container) .more_details span, .wd-more.more_details, .more-overview a, #wd_overview.themes_page #wd_baner, #wd_overview.plugins_page #wd_baner, #special_offers_iframe", function (e)
  {
    if (typeof jQuery(this).data("prod_id") != "undefined") {
      var prod_id = jQuery(this).data("prod_id");
      var installed = 0;
      var origin = jQuery(this).closest("#wd_overview").data("origin");
      var user_id = jQuery(this).closest("#wd_overview").data("user");
      var wpnonce = "";
      var plugin = jQuery("#wd_overview").hasClass("themes_page") ? 0 : 1;
    } else {
      var prod_id = jQuery(this).closest(".wd_plugin_container").length ? jQuery(this).closest(".wd_plugin_container").data("id") : jQuery(this).data("id");
      var installed = ((jQuery(this).closest("#installed_plugins_container").length && !jQuery(this).closest(".no_installed").length) || jQuery(this).closest(".wd_plugin_container").find(".install-now.installed").length) ? 1 : 0;
      var origin = jQuery(this).closest("#wd_overview").data("origin");
      var user_id = jQuery(this).closest("#wd_overview").data("user");
      var wpnonce = jQuery(this).closest(".wd_plugin_container").length ? jQuery(this).closest(".wd_plugin_container").data("wpnonce") : jQuery(this).data("wpnonce");
      var plugin = jQuery("#wd_overview").hasClass("themes_page") ? 0 : 1;
    }
    var from = WDDgetQueryVar("page");
    jQuery("#wd_iframe_content").html('<iframe src="' + WDD_options.product_url + '&product_id=' + prod_id + '&installed=' + installed + '&origin=' + origin + '&u=' + user_id + '&wpnonce=' + wpnonce + '&plugin=' + plugin + '&from=' + from + '"></iframe>');
    jQuery("#wd_iframe").fadeIn();
  });

  /*CHANGE LOG*/
  jQuery(".view_change_log").click(function (e)
  {
    var _this = jQuery(this);
    jQuery("#change_log_content").html('');
    var page = "WDD_plugins";
    if (jQuery(this).closest("#wd_overview").hasClass("themes_page")) {
      page = "WDD_themes";
    } else
      if (jQuery(this).closest("#wd_overview").hasClass("addons_page")) {
        page = "WDD_addons";
      }
    var prod_slug = jQuery(this).closest(".wd_plugin_container").data("plugin");
    jQuery.post(
      WDD_options.ajax_url + "?action=WDD_change_log&prod_id=" + prod_slug + "&page=" + page,
      function (data)
      {
        jQuery("#change_log_content").html(data);
        updateFromChangeLogPopup();
      }
    );
    jQuery("#change_log").fadeIn();
  });

  /*Tooltips*/
  jQuery(".wd_plugin .plugin_body .value.options,.wd-upgrade").click(function (e)
  {
    jQuery(this).find(".options_tooltip").toggle();
    jQuery(this).toggleClass("active");
    e.preventDefault();
  });
  jQuery("body").click(function ()
  {
    jQuery(".options_tooltip").hide();
    jQuery(".plugin_body .value.options,.wd-upgrade").removeClass("active");
  });
  jQuery(".plugin_body .value.options,.wd-upgrade,#wd_iframe_container,#change_log_container,.options_tooltip").click(function (e)
  {
    e.stopPropagation();
  });
  jQuery(".active_sort").click(function ()
  {
    if (jQuery(this).parent().hasClass("active")) {
      jQuery(this).parent().removeClass("active");
      jQuery(this).next().hide();
    } else {
      jQuery(this).parent().addClass("active");
      jQuery(this).next().show();
    }
  });
  jQuery(".sort_tooltip li").click(function ()
  {
    var loc_this = this;
    jQuery(".sort_tooltip li").removeClass("active");
    jQuery(loc_this).addClass("active");
    WDDsortUnorderedList(jQuery(loc_this).data("type"));
    jQuery(loc_this).closest(".sort_by_select").removeClass("active");
    jQuery(loc_this).closest(".sort_tooltip").hide();
    jQuery(".active_sort").text(jQuery(loc_this).text());
    if (typeof(Storage) !== "undefined") {
      localStorage.sortBy = jQuery(loc_this).data("type");
    }

  });

  /* Search */
  if (jQuery('.wd_plugin_container').length != 0) {
    jQuery.extend(jQuery.expr[':'], {
      'containsi': function (elem, i, match, array)
      {
        return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
      }
    });
    jQuery(document).on('keyup', '#search_prod', function ()
    {
      searched = jQuery(this).val();
      if (searched != '') {
        jQuery('.wd_plugin_container').each(function ()
        {
          if (jQuery(this).find("h3:containsi(" + searched + ")").length > 0) {
            jQuery(this).removeClass(' hidden');
          }
          else
            jQuery(this).addClass(' hidden');
        });
      }
      else {
        jQuery('.wd_plugin_container').removeClass(' hidden');
      }
      jQuery("#avalible_plugins .wd_plugins").each(function (index, el)
      {
        jQuery("#avalible_plugins .av_count").eq(index).html(jQuery(this).find("h3:containsi(" + searched + ")").length);
      });
    });
  }

  /* Tabs */

  jQuery(".wd_tabs > div:not(#wd_baner):not(.addons-banner)").click(function ()
  {
    var id = jQuery(this).attr("id");
    WDDchangeLeftMenu(id);

  });

  /*Get pro add-ons*/
  jQuery(".get_pro_addons").click(function ()
  {
    jQuery(".wd_tabs > div").removeClass("active");
    jQuery("#pro_plugins").addClass("active");
    jQuery(".addons:visible").find(".wd_tab_container").hide();
    jQuery(".addons:visible").find("#pro_plugins_container").show();
    return false;
  });

  /*ADD-ONS tabs*/
  var parent = WDDgetQueryVar("parent");

  if (parent) {
    WDDcurrentAddonTab(parent);
  } else
    if (typeof(Storage) !== "undefined" && typeof(localStorage.addontab) !== "undefined" && localStorage.addontab != "") {
      WDDcurrentAddonTab(localStorage.addontab);
    }
  if (jQuery(".wd_parent_plugins > div.active").length) {
    jQuery("#" + jQuery(".wd_parent_plugins > div.active").attr("id") + "_filter").show();
  }

  jQuery(".wd_parent_plugins .plugin_header").click(function (e)
  {
    var sidebar_tab = jQuery("#wd_sidebar").find("div.active").attr("id");
    var id = jQuery(this).attr("id");
    /*Show addons banner*/
    if (jQuery("#wd_overview").hasClass("addons_page")) {
      if (id == "form-maker") {
        jQuery(".addons-banner").hide();
        jQuery("#wd_baner_form").show();
      } else
        if (id == "event-calendar-wd") {
          jQuery(".addons-banner").hide();
          jQuery("#wd_baner_calendar").show();
        } else {
          jQuery(".addons-banner").hide();
        }
    }
    jQuery(".wd_parent_plugins .plugin_header").removeClass("active");
    jQuery(this).addClass("active");
    jQuery("#wd_addons .addons,.addons_page #filter_content > div").hide();
    jQuery("#" + id + "_content,.addons_page #" + id + "_filter").show();
    if (jQuery(this).closest("#wd_overview").hasClass("addons_page")) {
      jQuery("#" + id + "_content").find(".wd_tab_container").hide();
      jQuery("#" + id + "_content").find("#" + sidebar_tab + "_container").show();
    }
    /*Hide Pro Add-ons tab if empty*/
    if (!jQuery("#" + id + "_content #pro_plugins_container .wd_plugin_container").length) {
      jQuery("#pro_plugins").hide();
      if (jQuery("#pro_plugins").hasClass("active")) {
        jQuery("#wd_sidebar > div").removeClass("active");
        jQuery("#installed_plugins").addClass("active");
        jQuery("#wd_addons .addons:visible").find("#installed_plugins_container").show();
      }
    } else {
      jQuery("#pro_plugins").show();
    }
    /*Sort while changing tab*/
    if (jQuery(".sort_by .sort_tooltip li.active").length) {
      WDDsortUnorderedList(jQuery(".sort_by .sort_tooltip li.active").data("type"));
    }
    /*Addon active tab*/
    if (typeof(Storage) !== "undefined") {
      localStorage.addontab = jQuery(this).attr("id");
    }
  });

  /*for buttons*/
  jQuery("#installed_plugins_container .wd_plugin_container").each(function ()
  {
    var button_count = jQuery(this).find(".action_buttons > div").length;
    jQuery(this).addClass("button_count_" + button_count);
  });

  /* Select product for update*/
  jQuery("body").on("click", ".checked_for_updates .for_check", function (e)
  {
    if (jQuery(this).hasClass("active"))
      jQuery(this).removeClass("active");
    else
      jQuery(this).addClass("active");
    var count = 0;
    if (jQuery(".checked_for_updates .for_check.active").length)
      count = jQuery(".checked_for_updates .for_check.active").length;
    jQuery("#update_count").text(" (" + count + ")");
    //count()
    if (count == jQuery(".checked_for_updates .for_check:visible").length) {
      jQuery(".update_checkbox").addClass("deselect");
    } else {
      jQuery(".update_checkbox").removeClass("deselect");
      jQuery(".update_checkbox input[type='checkbox']").prop('checked', false);
    }
    // for bulk update
    //jQuery("#update_selected").val();
  });

  /*Select all, Deselect all*/
  jQuery(".update_checkbox input[type='checkbox']").click(function ()
  {
    if (jQuery(this).prop('checked')) {
      jQuery(this).parent().addClass("deselect");
      jQuery(".checked_for_updates .for_check:visible").addClass("active");
    } else {
      jQuery(this).parent().removeClass("deselect");
      jQuery(".checked_for_updates .for_check:visible").removeClass("active");
    }
    var count = 0;
    if (jQuery(".checked_for_updates .for_check.active:visible").length)
      count = jQuery(".checked_for_updates .for_check.active:visible").length;
    jQuery("#update_count").text(" (" + count + ")");
  });

  /*Sort by popular*/
  if (typeof(Storage) !== "undefined") {

    var WDD_page_data = JSON.parse(localStorage.getItem("WDD_page_data"));
    var WDD_manager_page = localStorage.getItem("WDD_manager_page");
    var WDD_current_page = WDDgetUrlData();
    if (WDD_page_data != null && WDD_manager_page === WDD_current_page) {
      if (WDD_page_data.WDD_left_menu_id != undefined) {
        WDDchangeLeftMenu(WDD_page_data.WDD_left_menu_id);
      } else {
        jQuery("#installed_plugins").addClass("active");
      }
      if (WDD_page_data.WDD_filter_menu_id != undefined) {
        WDDchangeFilter(WDD_page_data.WDD_filter_menu_id);
      } else {
        jQuery("#all").addClass("active");
      }
    } else {
      localStorage.removeItem("WDD_manager_page");
      localStorage.removeItem("WDD_page_data");
      if (!jQuery(".wd_tabs > div").hasClass("active")) {
        jQuery("#installed_plugins").addClass("active");
        jQuery("#all").addClass("active");
      }
    }

    if (typeof(localStorage.sortBy) === "undefined" && jQuery(".sort_tooltip ul li").eq(0).length) {
      localStorage.sortBy = jQuery(".sort_tooltip ul li").eq(0).data("type");
    }
    WDDsortUnorderedList(localStorage.sortBy);
    if (jQuery(".sort_tooltip ul li[data-type='" + localStorage.sortBy + "']").length) {
      jQuery(".sort_tooltip ul li").removeClass("active");
      jQuery(".sort_tooltip ul li[data-type='" + localStorage.sortBy + "']").addClass("active");
      jQuery(".active_sort").text(localStorage.sortBy);
    }

    localStorage.setItem("WDD_manager_page", WDDgetUrlData());
  } else {
    jQuery("#installed_plugins").addClass("active");
  }

  var wddInitBtns = new WDDAJAX();
});

var WDDAJAX = function ()
{
  var _this = this;

  this.page = '';

  var param_name = 'page';
  if (param_name = (new RegExp('[?&]' + encodeURIComponent(param_name) + '=([^&]*)')).exec(location.search))
    this.page = decodeURIComponent(param_name[1]);


  if (this.page == 'WDD_plugins' || this.page == 'WDD_themes' || this.page == 'WDD_addons') {
    this.init();
  }


};

/*
 actionQueue[
 {
 action:'',//install,activate,deactivate,delete,update
 url:'',//ajax url
 method:'',//POST/GET
 data:{},//ajax request body
 }
 ]
 */

WDDAJAX.prototype = {

  actionQueue: new Array(),
  currentActionIndex: 0,
  inProcess: false,
  filesystemCredentialsModal: {
    init: false,
    modalExists: false,
    modalFilled: false,
    $modal: null,
    serializedData: "",
    isOpen: false,
    actionQueue: {
      $button: null//for triggering
    }
  },
  successInstalled: false,
  constructor: WDDAJAX,

  init: function ()
  {

    var _this = this;

    _this.beforeUnload();

    this.filesystemCredentialsModal.$modal = jQuery('#request-filesystem-credentials-dialog');
    if (this.filesystemCredentialsModal.$modal.length == 1) {
      this.filesystemCredentialsModal.modalExists = true;
    }

    var page = WDDgetUrlData();

    if (page == "WDD_plugins") {
      _this.setPluginsPageEvents();
    } else
      if (page == "WDD_addons") {
        _this.setAddonsPageEvents();
      } else
        if (page == "WDD_themes") {
          _this.setThemesPageEvents();
        }

    if(jQuery('.wdd_self_update_modal').length > 0){
      _this.setSelfUpdatePageEvents();
    }

  },
  setAddonsPageEvents: function ()
  {
    var _this = this;


    jQuery('.installed_addons.no_installed').each(function ()
    {

      var $pluginContainer = jQuery(this);
      var paid = $pluginContainer.data("pro") == "pro" ? true : false;
      $pluginContainer.find('.wd-more .install-now').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'install',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForInstall($pluginContainer, paid),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);
        _this.addOverlay($pluginContainer);


        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

    });
    jQuery('.installed_addons .wd_plugin_container').each(function ()
    {

      var $pluginContainer = jQuery(this);

      $pluginContainer.find('.wd-more .wdd_activate_product_button').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'activate',
          'url': WDD_options.ajax_url,
          'method': 'GET',
          'data': _this.getRequestDataForActivate($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);
        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      $pluginContainer.find('.wd-more .wdd_deactivate_product_button').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'deactivate',
          'url': WDD_options.ajax_url,
          'method': 'GET',
          'data': _this.getRequestDataForDeactivate($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      $pluginContainer.find('.delete_product .wdd_delete_product_button').on('click', function (e)
      {
        e.preventDefault();

        if(!_this.filesystemCredentialsModal.isOpen) {
          if (confirm('Are you sure you want to delete this plugin?') === false) {
            return false;
          }
        }

        var actionData = {
          'action': 'delete',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForDelete($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      $pluginContainer.find('.wd-more .update-link').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'update',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForUpdate($pluginContainer, true),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      //install paid version
      $pluginContainer.find('.wd-more .wdd_install_pro').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'install',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForInstall($pluginContainer, true),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

    });

    jQuery('.avalable_addons .wd_plugin_container').each(function ()
    {

      var $pluginContainer = jQuery(this);

      $pluginContainer.find('.wd-more .install-now').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'install',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForInstall($pluginContainer, true),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

    });

  },
  setPluginsPageEvents: function ()
  {
    var _this = this;
    jQuery('#free_plugins_container .wd_plugin_container').each(function ()
    {

      var $pluginContainer = jQuery(this);

      $pluginContainer.find('.wd-more .install-now').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'install',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForInstall($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

    });

    jQuery('#installed_plugins_container .wd_plugin_container').each(function ()
    {

      var $pluginContainer = jQuery(this);

      $pluginContainer.find('.wd-more .wdd_activate_product_button').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'activate',
          'url': WDD_options.ajax_url,
          'method': 'GET',
          'data': _this.getRequestDataForActivate($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      $pluginContainer.find('.wd-more .wdd_deactivate_product_button').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'deactivate',
          'url': WDD_options.ajax_url,
          'method': 'GET',
          'data': _this.getRequestDataForDeactivate($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      $pluginContainer.find('.delete_product .wdd_delete_product_button').on('click', function (e)
      {
        e.preventDefault();

        if(!_this.filesystemCredentialsModal.isOpen){
          if (confirm('Are you sure you want to delete this plugin?') === false) {
            return false;
          }
        }

        var actionData = {
          'action': 'delete',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForDelete($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      $pluginContainer.find('.wd-more .update-link').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'update',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForUpdate($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      //install paid version
      $pluginContainer.find('.wd-more .wdd_install_pro').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'install',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForInstall($pluginContainer, true),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

    });

    jQuery('#purchased_plugins_container .wd_plugin_container').each(function ()
    {

      var $pluginContainer = jQuery(this);

      $pluginContainer.find('.wd-more .install-now').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'install',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForInstall($pluginContainer, true),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) == false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

    });
  },
  setThemesPageEvents: function ()
  {

    var _this = this;
    jQuery('#free_plugins_container .wd_plugin_container').each(function ()
    {

      var $pluginContainer = jQuery(this);

      $pluginContainer.find('.wd-more .install-now').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'install',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForInstall($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

    });

    jQuery('#installed_plugins_container .wd_plugin_container').each(function ()
    {

      var $pluginContainer = jQuery(this);

      $pluginContainer.find('.wd-more .wdd_activate_product_button').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'activate',
          'url': WDD_options.ajax_url,
          'method': 'GET',
          'data': _this.getRequestDataForActivate($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      $pluginContainer.find('.delete_product .wdd_delete_product_button').on('click', function (e)
      {
        e.preventDefault();

        if(!_this.filesystemCredentialsModal.isOpen) {
          if (confirm('Are you sure you want to delete this theme?') === false) {
            return false;
          }
        }

        var actionData = {
          'action': 'delete',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForDelete($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      $pluginContainer.find('.wd-more .update-link').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'update',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForUpdate($pluginContainer),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

      //install paid version
      $pluginContainer.find('.wd-more .wdd_install_pro').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'install',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForInstall($pluginContainer, true),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

    });

    jQuery('#purchased_plugins_container .wd_plugin_container').each(function ()
    {

      var $pluginContainer = jQuery(this);

      $pluginContainer.find('.wd-more .install-now').on('click', function (e)
      {
        e.preventDefault();

        var actionData = {
          'action': 'install',
          'url': WDD_options.ajax_url,
          'method': 'POST',
          'data': _this.getRequestDataForInstall($pluginContainer, true),
          'productContainer': $pluginContainer
        };

        if (_this.doAction(actionData, jQuery(this)) === false) {
          return false;
        }

        _this.actionQueue.push(actionData);

        _this.addOverlay($pluginContainer);

        if (_this.actionQueue.length == 1) {
          _this.nextAction();
        }

        return false;

      });

    });

  },
  setSelfUpdatePageEvents:function () {
    var _this = this;
    jQuery('.wdm_update .self_update').on('click', function (e)
    {
      e.preventDefault();

      jQuery(this).find("span.spinner").css({"visibility": "visible", "display": "inline-block"});

      var actionData = {
        'action': 'install',
        'url': WDD_options.ajax_url,
        'method': 'POST',
        'data': {
          'action': 'wdd_plugins_action',
          'wdd_action': 'install',
          'slug': 'wd-manager/wd-manager.php',
          'pro': 1,
          'activate': 1,
          'wdd_nonce': WDD_options.product_update_nonce
        }
      };

      if (_this.doAction(actionData, jQuery(this)) === false) {
        return false;
      }

      _this.actionQueue.push(actionData);

      if (_this.actionQueue.length == 1) {
        _this.nextAction();
      }

      return false;
    });
  },
  nextAction: function (response)
  {
    //debugger;
    if (typeof response == 'undefined' && this.inProcess == true) {
      return;
    }

    if (typeof response != 'undefined' && response.success == true) {
      this.successInstalled = true;
    }


    this.inProcess = true;

    if (typeof this.actionQueue[this.currentActionIndex] === 'undefined') {


      if (this.successInstalled == true) {
        var page_data = {"WDD_left_menu_id": "installed_plugins"};
        WDDStorageSetData(page_data, "WDD_page_data", true);
      }
      
      if (this.filesystemCredentialsModal.actionQueue.$button == null) {
        window.location.reload();
      }

 
      return;
    }

    var action = this.actionQueue[this.currentActionIndex];

    this.ajaxReq(action.url, action.method, action.data);
  },
  doAction: function (actionData, $button)
  {
    
    if (actionData.action == "install" || actionData.action == "update" || actionData.action == "delete") {

      //check filesystem Credentials
      if (this.filesystemCredentialsModal.modalExists == true && this.filesystemCredentialsModal.modalFilled == false) {
        this.filesystemCredentialsModal.actionQueue.$button = $button;
        this.displayFilesystemCredentialsModal();


        return false;
      }


    }

    return true;
  },
  displayFilesystemCredentialsModal: function ()
  {

    var modalData = this.filesystemCredentialsModal;
    modalData.$modal.show();
    modalData.isOpen = true;

    var _this = this;
    if (modalData.init == false) {

      modalData.$modal.find('form').on('submit', function (e)
      {
        e.preventDefault();

        if ((modalData.$modal.find('#password').val()).trim() !== ""
        && (modalData.$modal.find('#username').val()).trim() !== ""
        && (modalData.$modal.find('#hostname').val()).trim() !== ""
        ) {
          modalData.serializedData = jQuery(this).serialize();
          modalData.modalFilled = true;

          if (modalData.actionQueue.$button !== null) {
            var $btn = modalData.actionQueue.$button;
            modalData.actionQueue.$button = null;
            $btn.trigger('click');

            if (_this.actionQueue.length > 1) {
              _this.nextAction({});
            }
          }

        }

        modalData.$modal.hide();
        modalData.isOpen = false;

        return false;
      });

      modalData.$modal.find('.cancel-button').on('click', function (e)
      {
        e.preventDefault();

        modalData.actionQueue.$button = null;
        modalData.$modal.hide();
        modalData.isOpen = false;


        if (_this.actionQueue.length > 0 && typeof _this.actionQueue[_this.currentActionIndex] === 'undefined') {
          window.location.reload();
        }


        return false;
      });

    }

  },
  getRequestDataForInstall: function ($productContainer, isPro)
  {
    if (typeof isPro == 'undefined') {
      isPro = false;
    }

    var data;
    if (isPro === true) {

      data = {
        'action': 'wdd_plugins_action',
        'wdd_action': 'install',
        'slug': $productContainer.data('plugin'),
        'pro': 1,
        'activate': 1
      };

      if ($productContainer.closest('#installed_plugins_container').length > 0 && $productContainer.hasClass('deactivated')) {
        data.activate = 0;
      }

    } else {

      data = {
        'action': 'wdd_plugins_action',
        'wdd_action': 'install',
        'slug': $productContainer.data('plugin'),
        'pro': 0,
        'activate': 1
      };

    }

    if (WDDgetUrlData() == 'WDD_themes') {
      data.activate = 0;
    }

    data.update = 0;

    return data;
  },
  getRequestDataForActivate: function ($productContainer)
  {
    var data = {
      'action': 'wdd_plugins_action',
      'wdd_action': 'activate',
      'slug': $productContainer.data('plugin'),
      //'_wpnonce': $productContainer.find('.wdd_activate_product_button').data('nonce')
    };

    return data;
  },
  getRequestDataForDeactivate: function ($productContainer)
  {
    var data = {
      'action': 'wdd_plugins_action',
      'wdd_action': 'deactivate',
      'slug': $productContainer.data('plugin'),
      //'_wpnonce': $productContainer.find('.wdd_deactivate_product_button').data('nonce')
    };

    return data;
  },
  getRequestDataForDelete: function ($productContainer)
  {

    //var action = (WDDgetUrlData() == "WDD_themes") ? "delete-theme" : "delete-plugin";

    var data = {
      'action': 'wdd_plugins_action',
      'wdd_action': 'delete',
      'slug': $productContainer.data('plugin'),
    };


    return data;
  },
  getRequestDataForUpdate: function ($productContainer, isAddon)
  {
    var data = {};
    var activate = 1;

    if ($productContainer.closest('#installed_plugins_container').length > 0 && $productContainer.hasClass('deactivated')) {
      activate = 0;
    }

    var isAddon;
    if (typeof isAddon == 'undefined') {
      isAddon = false;
    }

    if ($productContainer.hasClass('pro') || isAddon) {

      data = {
        'action': 'wdd_plugins_action',
        'wdd_action': 'install',
        'slug': $productContainer.data('plugin'),
        'pro': 1,
        'activate': activate,
      };

    } else {

      data = {
        'action': 'wdd_plugins_action',
        'wdd_action': 'install',
        'slug': $productContainer.data('plugin'),
        'pro': 0,
        'activate': activate
      };
    }

    if (WDDgetUrlData() == 'WDD_themes') {
      data.activate = 0;
    }

    data.update = 1;

    return data;
  },
  addActionToQueue: function (data)
  {

    if (data.success !== true) {
      this.currentActionIndex++;
      return;
    }

    this.currentActionIndex++;


  },
  ajaxReq: function (urlAJAX, method, data)
  {

    if (typeof  method == 'undefined') {
      method = 'GET';
    }

    if (typeof  data == 'undefined') {
      data = {};
    }

    data.wdd_nonce = WDD_options.product_update_nonce;

    data.is_theme = (WDDgetUrlData() == "WDD_themes") ? 1 : 0;

    if (this.filesystemCredentialsModal.modalExists == true) {
      data.filesystem_credentials = this.filesystemCredentialsModal.serializedData;
    }


    var _this = this;

    jQuery.ajax({
      url: urlAJAX,
      method: method,
      data: data
    })
      .done(function (data, textStatus, jqXHR)
      {
        data = _this.getResponseObject(data);
        _this.changeLoadingView(data);
        _this.currentActionIndex++;
        _this.nextAction(data);
        //debugger;
      })
      .fail(function (jqXHR, textStatus, errorThrown)
      {
        _this.changeLoadingView(data);
        _this.currentActionIndex++;
        _this.nextAction({success: false});
        //debugger;
      })

  },
  getResponseObject: function (data)
  {
    var response = {
      success: false,
      message: "",
      data: []
    };

    if (typeof data == "object") {
      return data;
    }

    var newData = data.split('wdd_ajax_response_delimiter');

    if (newData.length !== 2) {
      return response;
    }

    var tempResponse = JSON.parse(newData[1]);
    if (typeof tempResponse.success !== 'undefined') {
      response = tempResponse;
    }

    return response;
  },
  beforeUnload: function ()
  {
    var _this = this;
    window.addEventListener("beforeunload", function (e)
    {

      if (typeof _this.actionQueue[_this.currentActionIndex] === 'undefined') {
        return undefined;
      }

      var confirmationMessage = 'Do you want to leave this page? Your changes will not be saved.';

      if (typeof e == "undefined") {
        e = window.event;
      }

      if (e) {
        e.returnValue = confirmationMessage;
      }

      return confirmationMessage;
    });
  },
  addOverlay: function ($container)
  {
    var html =
      '<div class="wdd_ajax_overlay">' +
      '<div class="wdd_ajax_status">' +
      '<div class="wdd_ajax_overlay_loading"><img src="' + WDD_options.img_path + '/spinner.gif" /></div>' +
      '<div class="wdd_ajax_overlay_success"><img src="' + WDD_options.img_path + '/success.png" /></div>' +
      '<div class="wdd_ajax_overlay_fail"><img src="' + WDD_options.img_path + '/fail.png" /></div>' +
      '</div>' +
      '</div>';
    $container.append(jQuery(html));
  },
  changeLoadingView: function (data)
  {
    var actionData = this.actionQueue[this.currentActionIndex];
    if (typeof  actionData.productContainer == 'undefined') {
      return;
    }

    actionData.productContainer.find('.wdd_ajax_overlay .wdd_ajax_status .wdd_ajax_overlay_loading').hide();
//debugger;
    if (data.success == true) {
      actionData.productContainer.find('.wdd_ajax_overlay .wdd_ajax_status .wdd_ajax_overlay_success').show();
    } else {
      actionData.productContainer.find('.wdd_ajax_overlay .wdd_ajax_status .wdd_ajax_overlay_fail').show();
    }

  },
  removeOverlay: function ()
  {
    jQuery('.wdd_ajax_overlay').remove();
  }

};


function WDDchangeFilter(id)
{
  var this_active_tab = jQuery("#" + id);
  if (this_active_tab.length === 0) {
    id = "all";
  }
  if (id === "update_available") {
    jQuery("#installed_plugins_container .wd_plugin_container").show();
    if (!jQuery("#" + id).hasClass("active")) {
      jQuery("#installed_plugins_container .wd_plugin_container").each(function ()
      {
        if (jQuery(this).find(".update-link").length == 0) {
          jQuery(this).hide();
        }
      });
      jQuery("#filter_content a").removeClass("active");
      jQuery("#" + id).addClass("active");
      jQuery("#wd_container").addClass("checked_for_updates");
      jQuery("#installed_plugins_container .wd_plugin_container .wd-more:not(.update-button)").hide();
      jQuery("#installed_plugins_container .wd_plugin_container").addClass("button_count_1js");
    } else {
      jQuery("#" + id).removeClass("active");
      jQuery("#wd_container").removeClass("checked_for_updates");
      jQuery("#installed_plugins_container .wd_plugin_container .wd-more:not(.update-button)").show();
      jQuery("#installed_plugins_container .wd_plugin_container").removeClass("button_count_1js");
    }
  } else {
    if (!jQuery("#" + id).hasClass("active")) {
      jQuery(".wd_plugin_container .wd-more:not(.update-button),#installed_plugins_container .wd_plugin_container").show();
      jQuery(".wd_plugin_container").removeClass("button_count_1js");

      if (id == "activeted") {
        jQuery("#installed_plugins_container .wd_plugin_container.deactivated").hide();
      } else
        if (id == "inactive") {
          jQuery("#installed_plugins_container .wd_plugin_container:not(.deactivated)").hide();
        }
      jQuery("#filter_content a").removeClass("active");
      jQuery("#" + id).addClass("active");
    } else {
      jQuery("#installed_plugins_container .wd_plugin_container").show();
      jQuery("#" + id).removeClass("active");
    }
  }
  var page_data = {
    "WDD_filter_menu_id": id
  };
  WDDStorageSetData(page_data, "WDD_page_data", true);
}
function WDDchangeLeftMenu(id)
{

  if (jQuery("#" + id).hasClass("WDD_deactivated_tab")) {
    id = "installed_plugins";
  }

  jQuery(".wd_tabs > div").removeClass("active");
  jQuery("#" + id).addClass("active");
  if (jQuery("#" + id).closest("#wd_overview").hasClass("addons_page")) {
    jQuery(".addons:visible").find(".wd_tab_container").hide();
    jQuery(".addons:visible").find("#" + id + "_container").show();
  } else {
    jQuery(".wd_tab_container").hide();
    jQuery("#" + id + "_container").show();
  }
  if (id != "installed_plugins") {
    jQuery("#filter_content").hide();
  } else {
    jQuery("#filter_content").show();
  }
  /*Sort while changing tab*/
  if (jQuery(".sort_by .sort_tooltip li.active").length) {
    WDDsortUnorderedList(jQuery(".sort_by .sort_tooltip li.active").data("type"));
  }
  var page_data = {
    "WDD_left_menu_id": id
  };
  WDDStorageSetData(page_data, "WDD_page_data", true);
}

function WDDcurrentAddonTab(parent)
{
  /*Show addons banner*/
  if (parent == "form-maker") {
    jQuery(".addons-banner").hide();
    jQuery("#wd_baner_form").show();
  } else
    if (parent == "event-calendar-wd") {
      jQuery(".addons-banner").hide();
      jQuery("#wd_baner_calendar").show();
    } else {
      jQuery(".addons-banner").hide();
    }
  jQuery(".wd_parent_plugins .plugin_header").removeClass("active");
  jQuery("#" + parent).addClass("active");
  jQuery("#wd_addons .addons,#filter_content > div").hide();
  jQuery("#" + parent + "_filter").show();
  var parent_div = jQuery("#" + parent + "_content");
  parent_div.show();
  if (parent_div.find("#installed_plugins_container .no_purchased").length) {
    jQuery(".wd_tabs > div").removeClass("active");
    jQuery(".addons:visible").find(".wd_tab_container").hide();
    if (parent_div.find("#purchased_plugins_container .no_purchased").length) {
      jQuery("#pro_plugins").addClass("active");
      jQuery(".addons:visible").find("#pro_plugins_container").show();
    } else {
      jQuery("#purchased_plugins").addClass("active");
      jQuery(".addons:visible").find("#purchased_plugins_container").show();
    }
  }
  /*Hide Pro Add-ons tab if empty*/
  if (!jQuery("#" + parent + "_content #pro_plugins_container .wd_plugin_container").length) {
    jQuery("#pro_plugins").hide();
    jQuery("#pro_plugins").addClass("WDD_deactivated_tab");
  } else {
    jQuery("#pro_plugins").show();
    jQuery("#pro_plugins").removeClass("WDD_deactivated_tab");
  }
}

function WDDMoreUpdates(el)
{
  if (!jQuery(el).hasClass("open")) {
    jQuery(el).removeClass("closed").addClass("open").html("Less updates");
    jQuery(el).prev("table").find("tr").fadeIn();
  } else {
    jQuery(el).removeClass("open").addClass("closed").html("More updates");
    jQuery(el).prev("table").find("tr:nth-child(n+6)").fadeOut();
  }
}

function WDDsortUnorderedList(type)
{

  if (jQuery("#wd_overview").hasClass("addons_page")) {
    var element = jQuery("#wd_plugins").find(".addons:visible .wd_tab_container:visible").find(".wd_plugin_container");
    var parent = jQuery("#wd_plugins").find(".addons:visible .wd_tab_container:visible").find(".wd_plugins");
  } else {
    var element = jQuery("#wd_plugins").find(".wd_tab_container:visible").find(".wd_plugin_container");
    var parent = jQuery("#wd_plugins").find(".wd_tab_container:visible").find(".wd_plugins");
  }

  var vals = [];
  if (type == "Alphabetical") {
    for (var i = 0; i < element.length; i++) {
      vals.push(element.eq(i).find("h3").text().replace(/ /g, "").replace("/", "").toLowerCase());
    }
    vals.sort();
    jQuery(vals).each(function (index, el)
    {
      jQuery(parent).append(jQuery(parent).find(jQuery(".wd_plugin_container." + el)));
    });
  } else
    if (type == "Popular") {
      for (var i = 0; i < element.length; i++) {
        vals.push(element.eq(i).data("ordering"));
      }
      vals.sort(function (a, b)
      {
        return a - b;
      });
      jQuery(vals).each(function (index, el)
      {
        jQuery(parent).append(jQuery(parent).find(jQuery(".wd_plugin_container[data-ordering='" + el + "']")));
      });
    }
}

function WDDCopyToClipboard(containerid)
{
  var text = document.getElementById(containerid);
  var selection = window.getSelection();
  var range = document.createRange();
  range.selectNodeContents(text);
  selection.removeAllRanges();
  selection.addRange(range);
  document.execCommand('copy');
}

function WDDgetQueryVar(parent)
{
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    if (pair[0] == parent) {
      return pair[1];
    }
  }
  return (false);
}
function WDDgetUrlData()
{
  var param_name = 'page';

  if (param_name = (new RegExp('[?&]' + encodeURIComponent(param_name) + '=([^&]*)')).exec(location.search))
    return decodeURIComponent(param_name[1]);
  else
    return '';
//	var manager_page = url.searchParams.get("page");
//	return manager_page;
}


function WDDStorageSetData(data, storage_name, type)
{
  if (typeof Object.assign != 'function') {
	  Object.assign = function(target) {
		'use strict';
		if (target == null) {
		  throw new TypeError('Cannot convert undefined or null to object');
		}

		target = Object(target);
		for (var index = 1; index < arguments.length; index++) {
		  var source = arguments[index];
		  if (source != null) {
			for (var key in source) {
			  if (Object.prototype.hasOwnProperty.call(source, key)) {
				target[key] = source[key];
			  }
			}
		  }
		}
		return target;
	  };
  }
  
  if (type) {
    var storage_data = localStorage.getItem(storage_name);
    if (storage_data != null) {
      storage_data = JSON.parse(storage_data);
      Object.assign(storage_data, data);
      localStorage.setItem(storage_name, JSON.stringify(storage_data));
    } else {
      localStorage.setItem(storage_name, JSON.stringify(data));
    }
  } else {

  }
}


function updateFromChangeLogPopup()
{

  jQuery('#change_log_content .update_now').on('click', function (e)
  {
    e.preventDefault();
    jQuery('#change_log').css('display', 'none');

    var product_id = jQuery(this).data('id');

    var page = WDDgetUrlData();

    var $product_container = null;
    if (page == 'WDD_plugins') {
      $product_container = jQuery('#installed_plugins_container').find('.wd_plugin_container[data-id="' + product_id + '"]')
    } else
      if (page == 'WDD_addons') {
        $product_container = jQuery('.wd_tab_container.installed_addons').find('.wd_plugin_container[data-id="' + product_id + '"]')
      } else
        if (page == 'WDD_themes') {
          $product_container = jQuery('#installed_plugins_container').find('.wd_plugin_container[data-id="' + product_id + '"]')
        }

    if (page !== null) {
      $product_container.find('.wd-more.update-button .update-link').trigger('click');
    }

    return false;
  });

}
////////////////////////////////////////////////////////////////////////////////////////
// Public Methods                                                                     //
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
// Getters & Setters                                                                  //
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
// Private Methods                                                                    //
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
// Listeners                                                                          //
////////////////////////////////////////////////////////////////////////////////////////
