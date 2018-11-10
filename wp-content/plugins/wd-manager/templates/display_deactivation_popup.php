	<div class="wd-opacity wd-<?php echo $wdd_options->prefix; ?>-opacity"></div>
	<div class="wd-deactivate-popup wd-<?php echo $wdd_options->prefix; ?>-deactivate-popup">
		<div class="wd-deactivate-popup-header">
			<?php _e( "If you have a moment, please let us know why you are deactivating", $wdd_options->prefix ); ?>:
		</div>
		
		<div class="wd-deactivate-popup-body">
			<?php foreach( $deactivate_reasons as $deactivate_reason_slug => $deactivate_reason ) { ?>
				<div class="wd-<?php echo $wdd_options->prefix; ?>-reasons">
					<input type="radio" value="<?php echo $deactivate_reason["id"];?>" id="<?php echo $deactivate_reason_slug; ?>" name="<?php echo $wdd_options->prefix; ?>-reasons" >
					<label for="<?php echo $deactivate_reason_slug; ?>"><?php echo $deactivate_reason["text"];?></label>
				</div>
			<?php } ?>
			<div class="additional_details_wrap">
				<label for="additional_details"><?php echo __( "Additional details", $wdd_options->prefix );?></label><br>
				<textarea id="additional_details" cols="70" rows="4"></textarea>
			</div>
		</div>		
		<div class="wd-btns">
			<a href="#" class="button button-secondary  wd-<?php echo $wdd_options->prefix; ?>-cancel"><?php _e( "Cancel" , $wdd_options->prefix ); ?></a>
			<a href="#" data-href="<?php echo add_query_arg( $wdd_options->prefix . "_submit_and_deactivate" , 1 , network_admin_url( "plugins.php" ) ); ?>" class="button button-primary button-close" id="wd-<?php echo $wdd_options->prefix; ?>-deactivate"><?php _e( "Deactivate" , $wdd_options->prefix ); ?></a>
			<a href="#" data-href="<?php echo add_query_arg( $wdd_options->prefix . "_submit_and_deactivate" , 1 , network_admin_url( "plugins.php" ) ); ?>" class="button button-primary button-close" id="wd-<?php echo $wdd_options->prefix; ?>-submit-and-deactivate" style="display:none;"><?php _e( "Submit and deactivate" , $wdd_options->prefix ); ?></a>			
		</div>	
	</div>