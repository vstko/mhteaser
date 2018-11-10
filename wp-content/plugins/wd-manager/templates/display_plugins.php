<?php
/*
*$wdd_options
*$user_hash
*$bundle
*$site_url
*/


/*Self update notice*/
if ($self_update) {
  WDD::message("message");
}

?>

<div id="wd_overview" class="plugins_page" data-origin="<?php echo $site_url; ?>" data-user="<?php echo $user_hash; ?>">
  <?php
  require_once(WDD_DIR_TEMPLATES . '/display_login_form.php');
  ?>
  <div id="wd_container" class="wd_clear">
    <div id="wd_sidebar" class="wd_tabs">
      <div id="installed_plugins">
        <h3><?php _e("Installed Plugins", WDD_LANG); ?><span
            class="wdd_tooltop"><span><?php _e("These are paid and free Web-Dorado plugins you’ve installed on your WordPress website. Get main info, update, upgrade and manage your installed plugins with one click.", WDD_LANG); ?></span></span>
        </h3>
      </div>
      <div id="purchased_plugins">
        <h3><?php _e("Purchased Plugins", WDD_LANG); ?> <span
            class="wdd_tooltop"><span><?php _e("These are the paid plugins you’ve purchased. Install and activate the plugins to start using them on your WordPress website.  ", WDD_LANG); ?></span></span>
        </h3>
      </div>
      <?php if (count($freePlugins)) : ?>
        <div id="free_plugins">
          <h3><?php _e("Free Plugins", WDD_LANG); ?> <span
              class="wdd_tooltop"><span><?php _e("Choose from the wide variety of Web-Dorado free plugins available. Install and activate to use the plugins on your website.", WDD_LANG); ?></span></span>
          </h3>
        </div>
      <?php endif; ?>
      <?php
      /*Get all plugins banner*/
      if ($bundle == 0) : ?>
        <div id="wd_baner" data-prod_id="180">
          <img src="<?php echo WDD_URL_IMG; ?>/plugins-banner.png">
        </div>
      <?php endif; ?>
    </div>
    <div id="wd_plugins">
      <div class="wd_header_container wd_clear">
        <div id="refresh_container">
          <form id="wd_plugins_form" action="<?php echo WDD_CURRENT_PGE; ?>" method="post">
            <input type="submit" name="wdd_refresh_button" id="wdd_refresh_button" value="Reload list">
          </form>
        </div>
        <div id="search_container">
          <div id="filter_content" class="wd_clear">
            <?php if ($installed_count != 0): ?>
              <a href="#" id="all"><?php _e("All", WDD_LANG); ?> (<?php echo $installed_count; ?>)</a>
            <?php endif;
            if ($update_all_products_count['plugins'] != 0): ?>
              <a href="#" id="update_available"><?php _e("Update available", WDD_LANG); ?>
                (<?php echo $update_all_products_count['plugins']; ?>)</a>
            <?php endif;
            if ($active_count != 0) : ?>
              <a href="#" id="activeted"><?php _e("Active", WDD_LANG); ?> (<?php echo $active_count; ?>)</a>
            <?php endif;
            if ($inactive_count != 0) : ?>
              <a href="#" id="inactive"><?php _e("Inactive", WDD_LANG); ?> (<?php echo $inactive_count; ?>)</a>
            <?php endif; ?>
          </div>
          <div id="search_content" class="wd_clear">
            <input type="text" name="search_prod" id="search_prod" placeholder="Search">
            <div class="sort_by wd_clear">
              <div class="sort_by_select" id="sort_by_free">
                <div class="active_sort">  <?php _e("Popular", WDD_LANG); ?>  </div>
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
      <div id="installed_plugins_container" class="wd_tab_container" style="display:block;">
        <?php if (count($installedPlugins) && isset($installedPlugins)) : ?>
          <div class="wd_plugins wd_clear">
            <?php foreach ($installedPlugins as $key => $value):
              if ($value->parent_id == 0 && $value->id != WDD_ID) :
                $slug = substr($key, 0, strpos($key, "/"));
                $count_days = 0;
                if (isset($value->subscr_date)) {
                  $diff = abs(strtotime($value->subscr_date) - time());
                  $count_days = ceil($diff / (60 * 60 * 24));
                }
								
                $plan_type = WDDProducts::getSingleProductPlanClass( $value );
                $type = ($value->is_active()) ? "active" : "deactivated";
                $class = array();
                $class[] = strtolower(str_replace(array(" ", "/"), "", $value->title));
                $class[] = $type;
                $class[] = $slug;
                $class[] = ($value->is_pro ? "pro" : "free");
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
                    <div class="plugin_logo"
                         style="background-image: url('<?php echo WDD_SITE_LINK . $value->logo; ?>');">
                    </div>
                    <div class="plugin_header">
                      <?php if ($value->is_pro): ?>
                        <div class="plugin_label"></div>
                      <?php endif; ?>
                      <h3><?php echo $value->title; ?></h3>
                      <div class="more_details"><span><?php _e("More Details", WDD_LANG); ?></span></div>
                    </div>
                  </div>
                  <div class="plugin_body">
                    <?php if ($value->is_pro && !$value->not_this_user) : ?>
                      <div class="wd_clear">
                        <div class="name"><?php _e("Product plan", WDD_LANG); ?></div>
                        <div class="value"><?php echo $plan_type; ?></div>
                      </div>
                      <?php if($value->expire_date !== null): ?>
                        <div class="wd_clear">
                          <div class="name"><?php _e("Expiration date", WDD_LANG); ?></div>
                          <div
                            class="value"><?php echo ($value->is_expired()) ? "<span class='expired'>" . __("Expired", WDD_LANG) . "</span>" : date("M. j Y", strtotime($value->expire_date)); ?></div>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                    <div class="wd_clear">
                      <div class="name"><?php _e("Version", WDD_LANG); ?></div>
                      <div class="value">
                        <?php

                        if (!($value->is_expired()) && isset($value->available_update) && is_array($value->available_update) && count($value->available_update)) :
                          ?>
                          <span class="view_change_log"><?php echo $value->version; ?>
                            <div class="action_tooltip"><?php _e("View change log", WDD_LANG); ?></div></span>
                        <?php else: ?>
                          <?php echo $value->version; ?>
                        <?php endif; ?>
                      </div>
                    </div>

                    <?php if ($value->with_addons === 1) : ?>
                      <div class="plugin_addons_link">
                        <a
                          href="<?php echo esc_url(add_query_arg(array('page' => $wdd_options->prefix . '_addons', 'parent' => $slug), network_admin_url('admin.php'))); ?>"> <?php _e("Plugin Add-ons", WDD_LANG); ?></a>
                      </div>
                    <?php else: ?>
                      <div class="plugin_addons_link">
                      </div>
                    <?php endif; ?>
                    <div class="action_buttons">
                      <?php

                      if ($value->available_update) :
                        /*free update*/

                        if ($value->available_update == 1) : ?>
                          <div class="wd-more update-button">
                            <a
                              href="<?php echo esc_url(wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . $key . '&from=' . $wdd_options->prefix . '_plugins'), 'upgrade-plugin_' . $key)); ?>"
                              class="update-link"><?php _e("Update", WDD_LANG); ?><span class="spinner"></span></a>
                          </div>
                        <?php /*pro update*/
                        elseif (isset($value->available_update) && count($value->available_update)) :
                          if ($value->is_expired()) : ?>
                            <div class="wd-more update-button">
                              <a href="#" class="update-link-expired update-link"><?php _e("Update", WDD_LANG); ?><span
                                  class="action_tooltip"><?php _e("You should renew product plan to get access to updates", WDD_LANG); ?></span></a>
                            </div>
                          <?php elseif ($value->not_this_user) : ?>
                            <div class="wd-more update-button">
                              <a href="#" class="update-link-expired update-link"><?php _e("Update", WDD_LANG); ?><span
                                  class="action_tooltip"><?php _e("You do not have a subscription for updates", WDD_LANG); ?></span></a>
                            </div>
                          <?php else : ?>
                            <div class="wd-more update-button">
                              <a href="#" class="update-link"><?php _e("Update", WDD_LANG); ?>
                                <span class="spinner"></span></a>
                            </div>
                          <?php endif;
                        endif;
                      endif;
                      if ($type == "active") :
                        $deactivation_link = esc_url(wp_nonce_url(self_admin_url('plugins.php?action=deactivate&plugin=' . $key), 'deactivate-plugin_' . $key));
                        ?>
                        <div class="wd-more manage">
                          <a class="wdd_deactivate_product_button"
                             href="<?php echo $deactivation_link; ?>"
                             data-plugin="<?php echo $key; ?>"
                             data-action="deactivate"
                             data-nonce="<?php echo wp_create_nonce('deactivate-plugin_' . $key); ?>">
                            <?php _e("Deactivate", WDD_LANG); ?>
                            <span class="spinner"></span>
                          </a>
                        </div>
                      <?php else :
                        $activation_link = esc_url(wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=' . $key), 'activate-plugin_' . $key));
                        ?>
                        <div class="wd-more manage">
                          <a class="wdd_activate_product_button"
                             href="<?php echo $activation_link; ?>"
                             data-plugin="<?php echo $key; ?>"
                             data-action="activate"
                             data-nonce="<?php echo wp_create_nonce('activate-plugin_' . $key); ?>">
                            <?php _e("Activate", WDD_LANG); ?>
                            <span class="spinner"></span>
                          </a>
                        </div>
                      <?php endif;
                      if ($value->is_pro && !$value->not_this_user && $plan_type != "" && $value->recurring == 0 && $count_days <= 15) : ?>
                        <?php if ($bundle == 0 && $plan_type != "Developer") : ?>
                          <div class="wd-more wd-upgrade">
                            <a href=""><?php _e("Upgrade to Higher Plan", WDD_LANG); ?></a>
                            <div class="wd_plans options_tooltip">
                              <ul>
                                <?php if ($plan_type == "Personal") :
                                  /*Upsell for Business*/ ?>
                                  <li><a
                                      href="<?php echo WDD_WP_UPSALE_PATH; ?>&id=<?php echo $value->subr_id; ?>&tmpl=component&upsell_id=<?php echo $value->sub_ids["Business"]; ?>&subscr_id=<?php echo $value->txn_id; ?>&origin=<?php echo $site_url; ?>&p_id=<?php echo $value->id; ?>&u=<?php echo $user_hash; ?>"
                                      target="_blank"><?php _e("Business", WDD_LANG); ?></a></li>
                                <?php endif;
                                /*Upsell for Developer*/ ?>
                                <li><a
                                    href="<?php echo WDD_WP_UPSALE_PATH; ?>&id=<?php echo $value->subr_id; ?>&tmpl=component&upsell_id=<?php echo $value->sub_ids["Developer"]; ?>&subscr_id=<?php echo $value->txn_id; ?>&origin=<?php echo $site_url; ?>&p_id=<?php echo $value->id; ?>&u=<?php echo $user_hash; ?>"
                                    target="_blank"><?php _e("Developer", WDD_LANG); ?></a></li>
                              </ul>
                            </div>
                          </div>
                        <?php endif;
                      endif;
                      /*Upgrade for free*/
                      /*if free not purchased , or not this user pro*/
                      if ((!$value->is_pro && !$value->not_this_user && !$value->is_buy && $value->has_pro)
                        || ($value->not_this_user && $value->is_pro && $value->has_pro)
                      ) : ?>
                        <div class="wd-more wd-upgrade-free">
                          <a><?php _e("Get Paid Version", WDD_LANG); ?></a>
                        </div>
                      <?php

                      elseif ($value->is_buy && !$value->is_expired()): ?>
                        <div class="wd-more">
                          <a href="#" class="install-now wdd_install_pro"><?php _e("Install Paid Version", WDD_LANG); ?>
                            <span class="spinner"></span></a>
                        </div>
                      <?php endif; ?>
                      <?php  if ($value->is_expired()  && !$value->not_this_user) : ?>
                        <div class="wd-more wd-upgrade-free">
                          <a href="#"><?php _e("Renew Product Plan", WDD_LANG); ?></a>
                        </div>
                      <?php endif; ?>
                    </div>
                    <?php if (!($type == "active")) : ?>
                      <div class="delete_product">
                        <a
                          class="wdd_delete_product_button"
                          data-plugin="<?php echo $key; ?>"
                          data-action="delete-selected"
                          data-nonce="<?php echo wp_create_nonce('bulk-plugins'); ?>"><?php _e("Delete", WDD_LANG); ?></a>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php else : ?>
          <p class="no_purchased"><?php _e("You don't have installed plugins yet", WDD_LANG); ?></p>
        <?php endif; ?>
      </div>
      <!--PRO Available-->
      <div id="purchased_plugins_container" class="wd_tab_container">
        <div class="wd_plugins wd_clear">
          <?php
          $plugin_cout = 0;
          if (count($proPlugins) && isset($proPlugins)) :
            foreach ($proPlugins as $key => $value):
              if ($value->parent_id == 0 && $value->id != WDD_ID) :

                $plugin_cout++;
                $slug = substr($key, 0, strpos($key, "/"));
                $class = array();
                $class[] = $slug;
                $class[] = strtolower(str_replace(array(" ", "/"), "", $value->title));
                $class = implode(" ", $class);
                ?>
                <div class="wd_plugin_container wd_plugin pro <?php echo $class; ?>"
                     data-id="<?php echo $value->id; ?>"
                     data-ordering="<?php echo $value->ordering; ?>"
                     data-slug="<?php echo $slug; ?>"
                     data-plugin="<?php echo $key; ?>"
                     data-wpnonce="<?php echo wp_create_nonce('install-plugin_' . $slug); ?>">
                  <div class="plugin_header_container wd_clear">
                    <div class="plugin_logo"
                         style="background-image: url('<?php echo WDD_SITE_LINK . $value->logo; ?>');">
                    </div>
                    <div class="plugin_header">
                      <div class="plugin_label"></div>
                      <h3><?php echo $value->title; ?></h3>
                      <div class="more_details"><span><?php _e("More Details", WDD_LANG); ?></span></div>
                    </div>
                  </div>
                  <div class="plugin_body">
                    <div class="plugin_desc"><?php echo $value->description; ?></div>
                    <?php if (!is_null($value->installed_pro) && $value->installed_pro == true) : ?>
                      <div class="wd-more">
                        <a href="#" class="install-now installed"><?php _e("Installed", WDD_LANG); ?></a>
                      </div>
                    <?php else :
                      if ($value->is_expired() ) : ?>
                        <div class="wd-more update-button">
                          <a href="#" class="update-link-expired"><?php (!is_multisite()) ? _e("Install and Activate", WDD_LANG) : _e("Install", WDD_LANG); ?><span
                              class="action_tooltip"><?php _e("You should renew product plan to get access to install", WDD_LANG); ?></span></a>
                        </div>

                        <?php /*if(!$value->not_this_user && !$value->is_buy) :	?>
																				<div class="wd-more wd-upgrade-free">
																					<a href="#"><?php _e("Renew Product Plan", WDD_LANG); ?></a>
																				</div>	
																			<?php endif;*/ ?>
                      <?php else /*if( !$value->not_this_user ) ddd*/
                        : ?>
                        <div class="wd-more">
                          <a href="#" class="install-now"><?php (!is_multisite()) ? _e("Install and Activate", WDD_LANG) : _e("Install", WDD_LANG); ?>
                            <span class="spinner"></span>
                          </a>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif;
          if ($plugin_cout == 0) : ?>
            <p class="no_purchased"><?php _e("You don't have any purchased plugins to be installed.", WDD_LANG); ?></p>
          <?php endif; ?>
        </div>
      </div>
      <!--FREE Available-->
      <?php if (count($freePlugins) && isset($freePlugins)) : ?>
        <div id="free_plugins_container" class="wd_tab_container">
          <div class="wd_plugins wd_clear">
            <?php foreach ($freePlugins as $key => $value):
              if ($value->parent_id == 0 && $value->id != WDD_ID) :
                $slug = substr($key, 0, strpos($key, "/"));
                ?>
                <div
                  class="wd_plugin_container <?php echo $slug; ?>  <?php echo strtolower(str_replace(array(" ", "/"), "", $value->title)); ?> wd_plugin free"
                  data-id="<?php echo $value->id; ?>"
                  data-ordering="<?php echo $value->ordering; ?>"
                  data-slug="<?php echo $slug; ?>"
                  data-plugin="<?php echo $key; ?>"
                  data-wpnonce="<?php echo wp_create_nonce('install-plugin_' . $slug) ?>">
                  <div class="plugin_header_container wd_clear">
                    <div class="plugin_logo"
                         style="background-image: url('<?php echo WDD_SITE_LINK . $value->logo; ?>');">
                    </div>
                    <div class="plugin_header">
                      <h3 <?php echo $value->demo_link ? '' : 'class="no_demo"'; ?>><?php echo $value->title; ?></h3>
					  <?php if( $value->demo_link ): ?>
                      <div class="view_demo"><span><a href="<?php echo $value->demo_link; ?>"
                                                      target="_blank"><?php _e("View Demo", WDD_LANG); ?></a></span>
                      </div>
					  <?php endif; ?>
                    </div>
                  </div>
                  <div class="plugin_body">
                    <div class="plugin_desc"><?php echo $value->description; ?></div>
                    <div class="wd-more more_details">
                      <span class="more_details_span"><?php _e("Get Paid Version", WDD_LANG); ?></span>
                    </div>
                    <div class="wd-more">
                      <a
                        href="<?php echo esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $slug . '&from=' . $wdd_options->prefix . '_plugins'), 'install-plugin_' . $slug)); ?>"
                        class="install-now"><?php _e("Install Free Version", WDD_LANG); ?><span class="spinner"></span></a>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
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

