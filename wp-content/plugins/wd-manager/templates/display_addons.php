<?php
/*
*$wdd_options
*$user_hash
*$bundle
*$site_url
*$addons_updates
*/

$installed_plugins = array();

/*Self update notice*/
if($self_update){
   WDD::message("message");
}

$felters = array();
 ?>

<div id="wd_overview" class="addons_page" data-origin="<?php echo $site_url; ?>" data-user="<?php echo $user_hash; ?>">
	<?php		
		require_once ( WDD_DIR_TEMPLATES . '/display_login_form.php' );
	?>
	  <div id="wd_container" class="wd_clear">
		  <div id="wd_sidebar" class="wd_tabs">
					<div id="installed_plugins">
						<h3><?php _e("Installed Add-Ons", WDD_LANG); ?><span class="wdd_tooltop"><span><?php _e("Below you can see all the add-ons that you have installed for the plugin. Please note that you need to have the paid version of the plugin for the add-ons to work.", WDD_LANG); ?></span></span></h3>
					</div>
					<div id="purchased_plugins">
						<h3><?php _e("Purchased Add-Ons", WDD_LANG); ?><span class="wdd_tooltop"><span><?php _e("These are the add-ons you’ve purchased. Install and activate the add-ons to start using them on your WordPress website.", WDD_LANG); ?></span></span></h3>
					</div>
					<?php if(count($proPlugins)) : ?>
						<div id="pro_plugins">
							<h3><?php _e("Paid Add-Ons", WDD_LANG); ?> <span class="wdd_tooltop"><span><?php _e("Below you can see all the add-ons available to purchase for the plugin. Click the “Buy Now” button to get and start using the add-on.", WDD_LANG); ?></span></span></h3>
						</div>
					<?php endif;
					/*Banner*/
					if($bundle["event_calendar_wd"] == 0): ?>
					<div id="wd_baner_calendar" class="addons-banner">
							<a href="<?php echo WDD_SITE_LINK; ?>/index.php?option=com_wdsubscriptions&view=checkout&id=247&tmpl=component&origin=<?php echo $site_url; ?>&u=<?php echo $user_hash; ?>" target="_blank"><img src="<?php echo WDD_URL_IMG; ?>/event-banner.png"></a>
					</div>
					<?php endif;
					if($bundle["form_maker"] == 0): ?>
					<div id="wd_baner_form" class="addons-banner">
							<a href="<?php echo WDD_SITE_LINK; ?>/index.php?option=com_wdsubscriptions&view=checkout&id=249&tmpl=component&origin=<?php echo $site_url; ?>&u=<?php echo $user_hash; ?>" target="_blank"><img src="<?php echo WDD_URL_IMG; ?>/form-maker-banner.png"></a>
					</div>
					<?php endif; ?>
			</div>
    <div id="wd_plugins">
			<div id="installed_plugins_width_addons">
					<div class="wd_parent_plugins wd_clear">
						<?php if(count($installedPlugins) && isset($installedPlugins)) :
							foreach($installedPlugins as $key => $value):
								if($value->with_addons === 1) {	
									$slug = substr($key,0,strpos($key,"/"));
									$installed_plugins[$value->id]["slug"] = $slug;
									$installed_plugins[$value->id]["subr_id"]= isset($value->subr_id) ? $value->subr_id : 0;
									$installed_plugins[$value->id]["is_pro"]= $value->is_pro; ?>
									
									<div id="<?php echo $slug; ?>" class="wd_parent_plugin plugin_header " data-plugin="<?php echo $key; ?>">
											<h3><?php echo $value->title; ?></h3>
									</div>
									<?php
									/*fiter content*/
									$installed_count = WDDProducts::getInstalledPluginsCount(false, $value->id, true);
									$active = WDDProducts::getInstalledPluginsCount(true, $value->id, false);
									$inactive = WDDProducts::getInstalledPluginsCount(false, $value->id, false);

									if($installed_count != 0){
										$felters[$slug]["installed"] = '<a href="#" id="all">' .  __("All", WDD_LANG) . ' (' . $installed_count . ')</a>';
									}
									if(isset($update_all_products_count["addons"][$value->id]) && $update_all_products_count["addons"][$value->id] != 0){
										$felters[$slug]["update"] = '<a href="#" id="update_available">' . __("Update available", WDD_LANG) . ' (' . $update_all_products_count["addons"][$value->id] . ')</a>';
									}
									if($active != 0){
										$felters[$slug]["active"] = '<a href="#" id="activeted">' .  __("Active", WDD_LANG) . ' (' .   $active . ')</a>';
									}
									if($inactive != 0){
										$felters[$slug]["inactive"] = '<a href="#" id="inactive">' .  __("Inactive", WDD_LANG) . ' (' .   $inactive . ')</a>';
									}
								} else {
									if($value->parent_id == 0){
										unset($installedPlugins[$key]);
									}
								} ?>
						<?php endforeach;
					  endif;
						/*Avalible plugins width addons*/
						if(count($availablePlugins) && isset($availablePlugins)) :
						  $avalible_plugins = array();
							foreach($availablePlugins as $key => $value):
								if($value->with_addons === 1 && !array_key_exists($key, $installedPlugins)) :	
								  $avalible_plugins[$key] = $value;
									$slug = substr($key,0,strpos($key,"/"));  ?>
									<div id="<?php echo $slug; ?>" class="wd_parent_plugin plugin_header" data-plugin="<?php echo $key; ?>">
											<h3><?php echo $value->title; ?></h3>
									</div>
								<?php endif;
						   endforeach;
					  endif; ?>
					</div>
				</div>
			  <div class="wd_header_container wd_clear">
						<div id="refresh_container">
							<form id="wd_plugins_form" action="<?php echo WDD_CURRENT_PGE;?>" method="post">
								<input type="submit" name="wdd_refresh_button" id="wdd_refresh_button" value="Reload list">
							</form>	
						</div>
						<div id="search_container">
							<div id="filter_content" class="wd_clear">
							  <?php if(count($felters) && isset($felters)):
									foreach($felters as $key => $felter): ?>
										<div id="<?php echo $key; ?>_filter">
											<?php foreach($felter as $value):
												echo $value;
											endforeach; ?>
										</div>
									<?php endforeach;
								endif; ?>
							</div>
							<div id="search_content" class="wd_clear">
								<input type="text" name="search_prod" id="search_prod" placeholder="Search">
								<div class="sort_by wd_clear">
										<div class="sort_by_select" id="sort_by_free"><div class="active_sort">	<?php _e("Popular", WDD_LANG); ?>	</div>	
												<div class="sort_tooltip">
													<ul>
														<li class="popular" data-type="Popular"><?php _e("Popular", WDD_LANG); ?></li>
														<li class="alphabetical" data-type="Alphabetical"><?php _e("Alphabetical", WDD_LANG); ?></li>
													</ul>
												</div>
										</div>				
										<label for="sort_by_free"><?php _e("Sort by", WDD_LANG); ?></label>
								</div>
							</div>
							<div id="update_content" class="wd_clear">
								<div class="wd-more">
									<a href="#"><?php _e("Update Selected", WDD_LANG); ?><span id="update_count"></span></a>									
								</div>
							  <div class="update_checkbox">
									<input type="checkbox" name="select_all" id="select_all">
									<label id="select_all_label"><?php _e("Select All", WDD_LANG); ?></label>
									<label id="deselect_all_label"><?php _e("Deselect All", WDD_LANG); ?></label>
								</div>
								<input type="hidden" name="update_selected" id="update_selected" value="">
							</div>
						</div>
					</div>
				<?php //if(count($installed_plugins)) : ?>
				<div id="wd_addons">
				  <?php $i = 0;
					if(count($installed_plugins) && isset($installed_plugins)):
						foreach($installed_plugins as $key1 => $installed_plugin):
					$installed_addons = array(); ?>
						<div id="<?php echo $installed_plugin["slug"]; ?>_content" class="addons" <?php echo $i == 0? "style='display:block;'" : ""; ?>>
								<div id="installed_plugins_container" style="display:block;" class="wd_tab_container installed_addons">
								  <div class="wd_plugins wd_clear">
								  <?php



									if(count($installedPlugins) && isset($installedPlugins)) :
										foreach($installedPlugins as $key => $value){
											if($value->parent_id == $key1){
												$installed_addons[$key] = $value;
											}	
										}
										if(count($installed_addons) && isset($installed_addons)) : ?>
										<?php foreach($installed_addons as $key => $value):
												$slug = substr($key,0,strpos($key,"/"));
												$count_days = 0;
												if(isset($value->subscr_date)) {
													$diff = abs(strtotime($value->subscr_date) - time());
													$count_days = ceil($diff / (60 * 60 * 24));
												}
												
											  $plan_type = WDDProducts::getSingleProductPlanClass( $value );

										    $type = ($value->is_active()) ? "active" : "deactivated";
												$class = array();
												$class[] = str_replace(".", "-", $slug);
												$class[] = strtolower(str_replace(array(" ", "/"), "", $value->title));
												$class[] = $type;
												$class = implode(" ", $class);
											?>
													<div class="wd_plugin_container wd_plugin <?php echo $class; ?>" 
													data-plugin="<?php echo $key; ?>"  
													data-slug="<?php echo $slug; ?>"
													data-id="<?php echo $value->id; ?>"
													data-version="<?php echo $value->version; ?>" 
													data-ordering="<?php echo $value->ordering; ?>" 
													data-active="<?php echo ($type == "active") ? 1 : 0; ?>" 
													data-wpnonce="<?php echo wp_create_nonce('install-plugin_' . $slug); ?>">
													  <div class="for_check"></div>
														<div class="plugin_header_container wd_clear">
															<div class="plugin_logo" style="background-image: url('<?php echo WDD_SITE_LINK . $value->logo; ?>');">
															</div>
															<div class="plugin_header">
																	<div class="plugin_label"></div>
																	<h3><?php echo $value->title; ?></h3>
																  <div class="more_details"><span><?php _e("More Details", WDD_LANG); ?></span></div>
															</div>
														</div>
														<div class="plugin_body">
															<?php if($value->is_pro && !$value->not_this_user) : ?>
																<div class="wd_clear uppercase">
																		<div class="name"><?php _e("Product plan", WDD_LANG); ?></div>
																		<div class="value"><?php echo $plan_type; ?> </div>
																</div>
																<?php if($value->expire_date !== null): ?>
																	<div class="wd_clear uppercase">
																			<div class="name"><?php _e("Expiration date", WDD_LANG); ?></div>
																			<div class="value"><?php echo ( $value->is_expired() ) ? "<span class='expired'>" . __("Expired", WDD_LANG) . "</span>" : date("M. j Y",strtotime($value->expire_date)); ?></div>
																	</div>
																<?php endif; ?>
															<?php endif; ?>
															  <div class="wd_clear">
																		<div class="name"><?php _e("Version", WDD_LANG); ?></div>
																		<div class="value">
																			<?php if(!$value->is_expired() && isset($value->available_update) && is_array($value->available_update) && count($value->available_update)) : ?>
																					<span class="view_change_log"><?php echo $value->version; ?><div class="action_tooltip"><?php _e("View change log", WDD_LANG); ?></div></span>
																			<?php else: ?>
																				<?php echo $value->version; ?>
																			<?php endif; ?>
																		</div>
																</div>
																<div class="action_buttons">
																		<?php if(isset($value->available_update) && is_array($value->available_update) && count($value->available_update) ) :
																			 if($value->is_expired()) :	?>
																				<div class="wd-more update-button">
																					<a href="#" class="update-link-expired update-link"><?php _e("Update", WDD_LANG); ?><span class="action_tooltip"><?php _e("You should renew product plan to get access to updates", WDD_LANG); ?></span></a>
																				</div>
																			<?php elseif( $value->not_this_user ) : ?>
																				<div class="wd-more update-button">
																					<a href="#" class="update-link-expired update-link"><?php _e("Update", WDD_LANG); ?><span class="action_tooltip"><?php _e("You do not have a subscription for updates", WDD_LANG); ?></span></a>
																				</div>
																			<?php else : ?>
																				<div class="wd-more update-button">
																					<a href="#" class="update-link"><?php _e("Update", WDD_LANG); ?>
																						<span class="spinner"></span>
																					</a>
																				</div>
																			<?php endif;
																		endif;
																		if($type == "active") : ?>
																			<div class="wd-more manage"><a
																					class="wdd_deactivate_product_button"
																					data-plugin="<?php echo $key; ?>"
																					data-action="deactivate"
																					data-nonce="<?php echo wp_create_nonce('deactivate-plugin_' . $key); ?>" ><?php _e("Deactivate", WDD_LANG); ?><span class="spinner"></span></a>
																			</div>
																		<?php else : ?>
																			<div class="wd-more manage"><a
																					class="wdd_activate_product_button"
																					data-plugin="<?php echo $key; ?>"
																					data-action="activate"
																					data-nonce="<?php echo wp_create_nonce('activate-plugin_' . $key); ?>"><?php _e("Activate", WDD_LANG); ?><span class="spinner"></span></a>
																			</div>
																		<?php endif;
																		if($value->is_pro && !$value->not_this_user && $plan_type != "" && $count_days <= 15) :  ?>
																		<?php if( !(($key1 == 31 && isset($bundle["form_maker"]) && $bundle["form_maker"] != 0) || ($key1 == 86 && isset($bundle["event_calendar_wd"]) && $bundle["event_calendar_wd"] != 0)) && $plan_type != "Developer") : ?>
																			<div class="wd-more wd-upgrade">
																					<a href=""><?php _e("Upgrade to Higher Plan", WDD_LANG); ?></a>
																					<div class="wd_plans options_tooltip">
																						<ul>
																							<?php	if( $plan_type == "Personal" ) :
																									/*Upsell for Business*/ ?>
																									<li><a href="<?php echo WDD_WP_UPSALE_PATH; ?>&id=<?php echo $value->subr_id; ?>&tmpl=component&upsell_id=<?php echo $value->sub_ids["Business"]; ?>&subscr_id=<?php echo $value->txn_id; ?>&origin=<?php echo $site_url; ?>&p_id=<?php echo $value->id; ?>&u=<?php echo $user_hash; ?>" target="_blank"><?php _e("Business", WDD_LANG); ?></a></li>
																								<?php endif;
																								/*Upsell for Developer*/  ?>
																								<li><a href="<?php echo WDD_WP_UPSALE_PATH; ?>&id=<?php echo $value->subr_id; ?>&tmpl=component&upsell_id=<?php echo $value->sub_ids["Developer"]; ?>&subscr_id=<?php echo $value->txn_id; ?>&origin=<?php echo $site_url; ?>&p_id=<?php echo $value->id; ?>&u=<?php echo $user_hash; ?>" target="_blank"><?php _e("Developer", WDD_LANG); ?></a></li>
																						</ul>
																					</div>
																			</div>
																		<?php endif;
																		endif;
																		if($value->is_expired() &&  !$value->not_this_user && !$value->is_buy) :	?>
																			<div class="wd-more wd-upgrade-free">
																				<a href="#"><?php _e("Renew Product Plan", WDD_LANG); ?></a>
																			</div>	
																		<?php endif;
																		if($value->not_this_user && $value->is_pro) : ?>
																			<div class="wd-more wd-upgrade-free" data-id="<?php echo $value->id; ?>" data-wpnonce="<?php echo wp_create_nonce('install-plugin_' . $slug); ?>">
																					<a><?php _e("Get Paid Version", WDD_LANG); ?></a>
																			</div>
																		<?php endif; ?>
																</div>
															  <?php if(!($type == "active")) : ?>
																	<div class="delete_product">
																		<a
																			class="wdd_delete_product_button"
																			data-plugin="<?php echo $key; ?>"
																			data-action="delete-selected"
																			data-nonce="<?php echo wp_create_nonce( 'bulk-plugins'); ?>"><?php _e("Delete", WDD_LANG); ?></a>
																	</div>
																<?php endif; ?>

														</div>
											</div>
										<?php endforeach; ?>
									<?php else : ?>	
										<p class="no_purchased"><?php _e("You don't have installed add-ons yet.", WDD_LANG); ?> <a href="" class="get_pro_addons"><?php _e("Check our paid add-ons here", WDD_LANG); ?></a></p>
									<?php endif; ?>
									</div>
									<?php endif; ?>
								</div>
								<!--BUY Available-->
								<div id="purchased_plugins_container" class="wd_tab_container avalable_addons">
										<?php if(count($availablePlugins) && isset($availablePlugins)) :
											$avalable_addons = array();
											foreach($purchasedPlugins as $key=>$value){ /*ddd*/
												if($value->parent_id == $key1){
													$avalable_addons[$key] = $value;
												}	
											}  ?>
										<div class="wd_plugins wd_clear">
										 <?php if(count($avalable_addons) && isset($avalable_addons)) :
											
											foreach($avalable_addons as $key => $value):
													$slug = substr($key,0,strpos($key,"/"));
													
													$class = array();
													$class[] = str_replace(".","-",$slug);
													$class[] = strtolower(str_replace(array(" ","/"), "", $value->title));
													$class = implode(" ", $class);
													?>
													
													<div class="wd_plugin_container wd_plugin <?php echo $class; ?>" 
													data-plugin="<?php echo $key; ?>" 
													data-id="<?php echo $value->id; ?>" 
													data-ordering="<?php echo $value->ordering; ?>" 
													data-wpnonce="<?php echo wp_create_nonce('install-plugin_' . $slug); ?>">
														<div class="plugin_header_container wd_clear">
															<div class="plugin_logo" style="background-image: url('<?php echo WDD_SITE_LINK . $value->logo; ?>');">
															</div>
															<div class="plugin_header">
																	<div class="plugin_label"></div>
																	<h3><?php echo $value->title; ?></h3>
																  <div class="more_details"><span><?php _e("More Details", WDD_LANG); ?></span></div>
															</div>
														</div>
														<div class="plugin_body">
															<div class="plugin_desc"><?php echo $value->description; ?></div>
																<?php if(isset($value->installed_pro) && $value->installed_pro == true) : ?>
																 <div class="wd-more">
																	<a href="#"  class="install-now installed"><?php _e("Installed", WDD_LANG); ?></a>
																 </div>	
																<?php else :	
																	if( $value->is_expired() ) :	?>
																			<div class="wd-more update-button">
																				<a href="#" class="update-link-expired"><?php (!is_multisite()) ? _e("Install and Activate", WDD_LANG) : _e("Install", WDD_LANG); ?><span class="action_tooltip"><?php _e("You should renew product plan to get access to install", WDD_LANG); ?></span></a>
																			</div>
																	<?php else : ?>
																		<div class="wd-more">
																			<a href="#" class="install-now"><?php (!is_multisite()) ? _e("Install and Activate", WDD_LANG) : _e("Install", WDD_LANG); ?>
																				<span class="spinner"></span>
																			</a>
																		</div>
																	<?php endif; ?>	
																<?php endif; ?>	
														</div>
											</div>
											<?php endforeach;
												else : ?>
													<p class="no_purchased"><?php _e("You don't have any purchased add-ons to be installed.", WDD_LANG); ?> <a href="" class="get_pro_addons"><?php _e("Check our paid add-ons here", WDD_LANG); ?></a></p>
											<?php endif; ?>
											</div>
										<?php endif; ?>
								</div>
								<!--PRO Available-->
								<?php if(count($proPlugins) && isset($proPlugins)) : ?>
									<div id="pro_plugins_container" class="wd_tab_container avalable_addons">
										<?php if(count($availablePlugins) && isset($availablePlugins)) :
											$avalable_addons = array();
											foreach($proPlugins as $key=>$value){
												if($value->parent_id == $key1){
													$avalable_addons[$key] = $value;
												}	
											}  ?>
											<div class="wd_plugins wd_clear">
											<?php if(count($avalable_addons) && isset($avalable_addons)) :
											/*uasort($avalable_addons, function($a, $b) {
													return $b['buy'] - $a['buy'];
											});*/
											
											foreach($avalable_addons as $key => $value):
													$slug = substr($key,0,strpos($key,"/")); 
													$class = array();
													$class[] = str_replace(".","-",$slug);
													$class[] = strtolower(str_replace(array(" ","/"), "", $value->title));
													$class = implode(" ", $class);
													?>
													
													<div class="wd_plugin_container wd_plugin not_purchased <?php echo $class; ?>" 
													data-plugin="<?php echo $key; ?>" 
													data-id="<?php echo $value->id; ?>" 
													data-ordering="<?php echo $value->ordering; ?>" 
													data-wpnonce="<?php echo wp_create_nonce('install-plugin_' . $slug); ?>">
														<div class="plugin_header_container wd_clear">
															<div class="plugin_logo" style="background-image: url('<?php echo WDD_SITE_LINK . $value->logo; ?>');">
															</div>
															<div class="plugin_header">
																	<div class="plugin_label"></div>
																	<h3><?php echo $value->title; ?></h3>
																  <div class="more_details"><span><?php _e("More Details", WDD_LANG); ?></span></div>
															</div>
														</div>
														<div class="plugin_body">
																<div class="plugin_desc"><?php echo $value->description; ?></div>
																<div class="wd-more">
																		<a class="wd-upgrade-free"><?php _e("Buy now", WDD_LANG); ?></a>	
																</div>
														</div>
											   </div>
											<?php endforeach; ?>
											<?php endif; ?>
										 </div>	
										<?php endif; ?>
									</div>
								<?php endif; ?>
						</div>
					<?php $i++;
					endforeach; 
					/*Avalible plugins width addons*/
					endif;
					$i = 0;
					if(!isset($avalible_plugins) || $avalible_plugins==null){
						$avalible_plugins = array();
					}
					foreach($avalible_plugins as $key1 => $avalible_plugin):
						$slug = substr($key1,0,strpos($key1,"/")); ?>
						<div id="<?php echo $slug; ?>_content" class="addons" <?php echo $i == 0? "style='display:block;'" : ""; ?>>
								<div id="installed_plugins_container" style="display:block;" class="wd_tab_container installed_addons no_installed" data-plugin="<?php echo $key1; ?>" data-pro="<?php echo ($avalible_plugin->is_pro) ? "pro" : "free" ; ?>">
								  <div class="wd_plugins wd_clear">
								    <?php  if ($avalible_plugin->id == 157 || $avalible_plugin->id == 31): ?>
										  <p><?php echo $avalible_plugin->title .  __(" addons require at least free version of the plugin installed",WDD_LANG); ?></p>
											<?php  if ($avalible_plugin->is_pro): ?>
												<div class="wd-more">
													<a href="#" class="install-now"><?php _e("Install Paid Version", WDD_LANG); ?>
														<span class="spinner"></span>
													</a>
												</div>
											<?php else : ?>
												<div class="wd-more">																
												 <a href="<?php echo esc_url( wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $slug . '&from=' . $wdd_options->prefix . '_addons'), 'install-plugin_' . $slug) ); ?>"  class="install-now"><?php _e("Install Free Version", WDD_LANG); ?><span class="spinner"></span></a>
												</div>
											<?php endif; ?>
										<?php else : ?>
										  <p><?php echo $avalible_plugin->title .  __(" addons require paid version installed",WDD_LANG); ?></p>
											<?php  if ($avalible_plugin->is_pro):
												if($avalible_plugin->is_expired()) : ?>
													<div class="wd-more update-button">
														<a href="#" class="update-link-expired update-link"><?php _e("Install Paid Version", WDD_LANG); ?><span class="action_tooltip"><?php _e("You should renew product plan to get access to updates", WDD_LANG); ?></span></a>
													</div>
												<?php else : ?>
													<div class="wd-more">
														<a href="#" class="install-now"><?php _e("Install Paid Version", WDD_LANG); ?>
															<span class="spinner"></span>
														</a>
													</div>
												<?php endif; ?>
										<?php else : ?>
											<div class="wd-more wd-upgrade-free" data-id="<?php echo $avalible_plugin->id; ?>" data-wpnonce="<?php echo wp_create_nonce('install-plugin_' . $slug); ?>">
													<a><?php _e("Get Paid Version", WDD_LANG); ?></a>
											</div><br>
											<div class="wd-more">																
											 <a href="<?php echo esc_url( wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $slug . '&from=' . $wdd_options->prefix . '_addons'), 'install-plugin_' . $slug) ); ?>"  class="install-now"><?php _e("Install Free Version", WDD_LANG); ?><span class="spinner"></span></a>
											</div>
										<?php endif; ?>
										<?php endif; ?>
									</div>
								</div>
								<!--BUY Available-->
								<div id="purchased_plugins_container" class="wd_tab_container avalable_addons">
								</div>
						</div>
					<?php $i++;
					endforeach; ?>
				</div>
				<?php //endif; ?>
    </div>
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
<div id="change_log" class="wd_popup">
	<div class="change_log">
		<div class="wd_close"></div>
		<div id="change_log_container">
			<div id="change_log_content">
				<span class="spinner"></span>
			</div>
		</div>
	</div>
</div>

