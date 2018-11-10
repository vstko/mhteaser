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
jQuery(document).ready(function () {

	jQuery("." + WDDeactivateVars.deactivate_class).click(function(){
		jQuery(".wd-" + WDDeactivateVars.prefix + "-opacity").show();
		jQuery(".wd-" + WDDeactivateVars.prefix  + "-deactivate-popup").show();
		return false;
	});
	jQuery("[name=" + WDDeactivateVars.prefix + "-reasons]").change(function(){
		
		jQuery("#wd-" + WDDeactivateVars.prefix + "-deactivate").hide();
		jQuery("#wd-" + WDDeactivateVars.prefix + "-submit-and-deactivate").show();
		
		
	});
	
	jQuery("#wd-" + WDDeactivateVars.prefix + "-submit-and-deactivate").on("click", function(){
		var reason = jQuery("[name=" + WDDeactivateVars.prefix + "-reasons]:checked").val();
		var href = jQuery(this).attr("data-href");

        jQuery.ajax({
            type: "POST",
            url: 'https://web-dorado.com/?option=com_wdsubscriptions&view=freeusersdata&tmpl=component',
            data: {
                type : "deactivate_reasons",
                reason : reason,
                site_url : WDDeactivateVars.site_url,
                additional_details : jQuery("#additional_details").val()
            },
            crossDomain: true,
            complete: function () {
            },
            success: function (response){        
                window.location.href = href;
            },
            failure: function (errorMsg) {
                console.log('Failure' + errorMsg);
            },
            error: function (errorMsg) {
                console.log('Error' + errorMsg);

            }            
        });
 		return false;
	});
	
	jQuery("#wd-" + WDDeactivateVars.prefix + "-deactivate").click(function(){
		var href = jQuery(this).attr("data-href");
		window.location.href = href;
 		return false;
	});	

	jQuery(".wd-" + WDDeactivateVars.prefix + "-cancel, .wd-opacity").click(function(){
		jQuery(".wd-" + WDDeactivateVars.prefix + "-opacity").hide();
		jQuery(".wd-" + WDDeactivateVars.prefix  + "-deactivate-popup").hide();
		return false;		
	});
});

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