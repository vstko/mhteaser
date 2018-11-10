<?php
/*
*$wdd_options
*$wd_plugins
*$user_hash
*$all_plugins
*$site_url
*/

/*Self update notice*/
if($self_update){
   WDD::message("message");
}

 ?>

<div id="wd_overview"  class="wd_clear wd_overview" data-origin="<?php echo $site_url; ?>" data-user="<?php echo $user_hash; ?>">
	<?php		
	 require_once ( WDD_DIR_TEMPLATES . '/display_login_form.php' );
	 if(is_array($wd_coupons) && count($wd_coupons)) :
      $wd_coupons = array_reverse($wd_coupons);	 ?>
			<div id="wd_coupons">
          <h2><?php _e("Available Coupons", WDD_LANG); ?></h2>
					<table>
					  <tr>
							<th><?php _e("Product", WDD_LANG); ?></th>
							<th><?php _e("Discount", WDD_LANG); ?></th>
							<th><?php _e("Valid through", WDD_LANG); ?></th>
							<th><?php _e("Coupon code", WDD_LANG); ?></th>
						</tr>
						<?php $i = 0;
						foreach($wd_coupons as $wd_coupon) : ?>
							<tr>
								<td>
									<?php 
										foreach( $wd_coupon["subscribtions"] as $subscribtion ) {
											echo '<p><a href="' . WDD_SITE_LINK . $subscribtion["url"] .'" target="_blank">' .  $subscribtion["name"] . '</a></p>';	
										} 
									?>
								</td>
								<td><?php echo $wd_coupon["discount"]; ?></td>
								<td><?php echo date("d.m. Y", strtotime($wd_coupon["end_date"])); ?></td>
								<td><span id="coupon_code_<?php echo $i; ?>"><?php echo $wd_coupon["coupon_code"]; ?></span><span class="copy_code"><?php _e("Copy", WDD_LANG); ?></span></td>
							</tr>
						<?php
            $i++;
						endforeach; ?>
					</table>
			</div>
		<?php endif; ?>
	  <div id="wd_baner" class="special_offers">
			<div id="special_offers_iframe" data-prod_id="180"> </div>
			<iframe src="<?php echo WDD_SPECIAL_OFFERS; ?>" scrolling="no"></iframe>
    </div>
</div>
<div id="wd_iframe" class="wd_popup">
  <div id="wd_iframe_container">
    <div class="wd_close"></div>
    <div id="wd_iframe_content">
      <span class="spinner"></span>
    </div>
  </div>
</div>
