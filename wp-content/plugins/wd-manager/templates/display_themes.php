<?php
/*
*$wdd_options
*$wd_themes
*$bundle
*$user_hash
*$site_url
*/

/*Self update notice*/
if ($self_update) {
  WDD::message("message");
}


?>

<div id="wd_overview" class="themes_page" data-origin="<?php echo $site_url; ?>" data-user="<?php echo $user_hash; ?>">
  <?php
  require_once(WDD_DIR_TEMPLATES . '/display_login_form.php');
  ?>
  <div id="wd_container" class="wd_clear">
    <div id="wd_sidebar" class="wd_tabs">
      <div id="installed_plugins">
        <h3><?php _e("Installed Themes", WDD_LANG); ?><span
            class="wdd_tooltop"><span><?php _e("Manage the themes you have installed on your WordPress website. Edit, update, delete, deactivate or activate them with one click.", WDD_LANG); ?></span></span>
        </h3>
      </div>
      <div id="purchased_plugins">
        <h3><?php _e("Purchased Themes", WDD_LANG); ?> <span
            class="wdd_tooltop"><span><?php _e("Below is the list of the paid themes available. Click on the install button and activate the themes to start using them.", WDD_LANG); ?></span></span>
        </h3>
      </div>
      <?php if (count($freeThemes)) : ?>
        <div id="free_plugins">
          <h3><?php _e("Free Themes", WDD_LANG); ?><span
              class="wdd_tooltop"><span><?php _e("Here are free themes available for you to use. Click on the install button  to start using the themes you want on your WordPress website.", WDD_LANG); ?></span></span>
          </h3>
        </div>
      <?php endif; ?>

      <?php
      /*Get all themes banner*/
      if ($bundle == 0) : ?>
        <div id="wd_baner" data-prod_id="184">
          <a><img src="<?php echo WDD_URL_IMG; ?>/themes-banner.png"></a>
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
            <?php

            if ($installed_count != 0): ?>
              <a href="#" id="all"><?php _e("All", WDD_LANG); ?> (<?php echo $installed_count; ?>)</a>
            <?php endif;
            if ($update_all_products_count['themes'] != 0): ?>
              <a href="#" id="update_available"><?php _e("Update available", WDD_LANG); ?>
                (<?php echo $update_all_products_count['themes']; ?>)</a>
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
        <?php if (count($installedThemes) && isset($installedThemes)) : ?>
          <div class="wd_plugins wd_clear">
            <?php foreach ($installedThemes as $key => $value):
              $count_days = 0;
              if (isset($value->subscr_date)) {
                $diff = abs(strtotime($value->subscr_date) - time());
                $count_days = ceil($diff / (60 * 60 * 24));
              }

              $plan_type = WDDProducts::getSingleProductPlanClass( $value );
              $type = ($value->is_active()) ? "active" : "deactivated";
              $class = array();
              $class[] = $key;
              $class[] = $type;
              $class[] = ($value->is_pro ? "pro" : "free");
              $class[] = strtolower(str_replace(array(" ", "/"), "", $value->title));
              $class = implode(" ", $class);
              ?>
              <div class="wd_plugin_container wd_plugin  <?php echo $class; ?>"
                   data-id="<?php echo $value->id; ?>"
                   data-version="<?php echo $value->version; ?>"
                   data-plugin="<?php echo $key; ?>"
                   data-slug="<?php echo $value->slug; ?>"
                   data-ordering="<?php echo $value->ordering; ?>"
                   data-active="<?php echo ($value->is_active()) ? 1 : 0; ?>"
                   data-wpnonce="<?php echo wp_create_nonce('install-theme_' . $key); ?>">
                <div class="for_check"></div>
                <div class="plugin_header_container">
                  <div class="plugin_header">
                    <?php if ($value->is_pro) : ?>
                      <div class="plugin_label"></div>
                    <?php endif; ?>
                    <div class="more_details"><span><?php _e("More Details", WDD_LANG); ?></span></div>
                    <h3><?php echo $value->title; ?></h3>
                    <div class="logo"
                         style="background-image: url('<?php echo WDD_SITE_LINK . $value->logo; ?>');"></div>
                  </div>
                </div>
                <div class="plugin_body">
                  <?php if ($value->is_pro && !$value->not_this_user) : ?>
                    <div class="wd_clear uppercase">
                      <div class="name"><?php _e("Product plan", WDD_LANG); ?></div>
                      <div class="value"><?php echo $plan_type; ?></div>
                    </div>
                    <?php if($value->expire_date !== null): ?>
                      <div class="wd_clear uppercase">
                        <div class="name"><?php _e("Expiration date", WDD_LANG); ?></div>
                        <div
                          class="value"><?php echo ($value->is_expired()) ? "<span class='expired'>" . __("Expired", WDD_LANG) . "</span>" : date("M. j Y", strtotime($value->expire_date)); ?></div>
                      </div>
                    <?php endif; ?>
                  <?php endif; ?>
                  <div class="wd_clear">
                    <div class="name"><?php _e("Version", WDD_LANG); ?></div>
                    <div class="value">
                      <?php if (!($value->is_expired()) && isset($value->available_update) && is_array($value->available_update) && count($value->available_update)) : ?>
                        <span class="view_change_log"><?php echo $value->version; ?>
                          <div class="action_tooltip"><?php _e("View change log", WDD_LANG); ?></div></span>
                      <?php else: ?>
                        <?php echo $value->version; ?>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="action_buttons">
                    <?php if (isset($value->available_update)) :
                      /*free update*/
                      if ($value->available_update == 1) : ?>
                        <div class="wd-more update-button">
                          <a
                            href="<?php echo esc_url(wp_nonce_url(self_admin_url('update.php?action=upgrade-theme&theme=' . $key . '&from=' . $wdd_options->prefix . '_themes'), 'upgrade-theme_' . $key)); ?>"
                            class="update-link"><?php _e("Update", WDD_LANG); ?><span class="spinner"></span></a>
                        </div>
                      <?php /*pro update*/
                      elseif (isset($value->available_update) && count($value->available_update) && $value->available_update) :
                        if ($value->is_expired() ) : ?>
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
                              <span class="spinner"></span>
                            </a>
                          </div>
                        <?php endif;
                      endif;
                    endif;
                    if (!$value->is_active()) : ?>
                      <div class="wd-more manage"><a
                          class="wdd_activate_product_button"
                          data-theme="<?php echo $key; ?>"
                          data-action="activate"
                          data-nonce="<?php echo wp_create_nonce('switch-theme_' . $key); ?>"><?php _e("Activate", WDD_LANG); ?>
                          <span class="spinner"></span></a>
                      </div>
                    <?php endif;
                    if ($value->is_pro && !$value->not_this_user && $plan_type != "" && $value->recurring == 0 && $count_days <= 15 && isset($value->sub_ids["Business"])&& isset($value->sub_ids["Developer"])) : ?>
                      <?php if (($bundle == 0 && $plan_type != "Developer")) : ?>
                        <div class="wd-more wd-upgrade">
                          <a href=""><?php _e("Upgrade to Higher Plan", WDD_LANG); ?></a>
                          <div class="wd_plans options_tooltip">
                            <ul>
                              <?php if ($plan_type == "Personal" && isset($value->sub_ids["Business"])) :
                                /*Upsell for Business*/ ?>
                                <li><a
                                    href="<?php echo WDD_WP_UPSALE_PATH; ?>&id=<?php echo $value->subr_id; ?>&tmpl=component&upsell_id=<?php echo $value->sub_ids["Business"]; ?>&subscr_id=<?php echo $value->txn_id; ?>&origin=<?php echo $site_url; ?>&p_id=<?php echo $value->id; ?>&u=<?php echo $user_hash; ?>"
                                    target="_blank"><?php _e("Business", WDD_LANG); ?></a></li>
                              <?php endif;
															if(isset($value->sub_ids["Developer"])) :
																/*Upsell for Developer*/ ?>
																<li><a
																		href="<?php echo WDD_WP_UPSALE_PATH; ?>&id=<?php echo $value->subr_id; ?>&tmpl=component&upsell_id=<?php echo $value->sub_ids["Developer"]; ?>&subscr_id=<?php echo $value->txn_id; ?>&origin=<?php echo $site_url; ?>&p_id=<?php echo $value->id; ?>&u=<?php echo $user_hash; ?>"
																		target="_blank"><?php _e("Developer", WDD_LANG); ?></a></li>
															<?php endif; ?>
                            </ul>
                          </div>
                        </div>
                      <?php endif;
                    endif;
                    /*Upgrade for free*/
                    if (!$value->is_pro && !$value->not_this_user && !$value->is_buy
                      || ($value->not_this_user && $value->is_pro)
                    ) : ?>
                      <div class="wd-more wd-upgrade-free">
                        <a><?php _e("Get Paid Version", WDD_LANG); ?></a>
                      </div>
                    <?php elseif ($value->is_buy && !$value->is_expired()): ?>
                      <div class="wd-more">
                        <a href="#" class="install-now wdd_install_pro"><?php _e("Install Paid Version", WDD_LANG); ?>
                          <span class="spinner"></span>
                        </a>
                      </div>
                    <?php endif; ?>
                    <?php if ($value->is_expired() && !$value->not_this_user) : ?>
                      <div class="wd-more wd-upgrade-free">
                        <a href="#"><?php _e("Renew Product Plan", WDD_LANG); ?></a>
                      </div>
                    <?php endif; ?>
                  </div>
                  <?php if (!$value->is_active()) : ?>
                    <div class="delete_product">
                      <a
                        class="wdd_delete_product_button"
                        data-theme="<?php echo $key; ?>"
                        data-action="delete"
                        data-nonce="<?php echo wp_create_nonce('delete-theme_' . $key); ?>"><?php _e("Delete", WDD_LANG); ?></a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else : ?>
          <p class="no_purchased"><?php _e("You don't have installed themes yet", WDD_LANG); ?></p>
        <?php endif; ?>
      </div>
      <!--PRO Available-->
      <div id="purchased_plugins_container" class="wd_tab_container">
        <div class="wd_plugins wd_clear">
          <?php if (count($proThemes) && isset($proThemes)) :
            foreach ($proThemes as $key => $value):
						
              $class = array();
              $class[] = $key;
              $class[] = strtolower(str_replace(array(" ", "/"), "", $value->title));
              $class = implode(" ", $class); ?>
              <div class="wd_plugin_container wd_plugin pro <?php echo $class; ?> "
                   data-id="<?php echo $value->id; ?>"
                   data-plugin="<?php echo $key; ?>"
                   data-slug="<?php echo $value->slug; ?>"
                   data-ordering="<?php echo $value->ordering; ?>"
                   data-wpnonce="<?php echo wp_create_nonce('install-theme_' . $key); ?>">
                <div class="plugin_header_container">
                  <div class="plugin_header">
                    <div class="plugin_label"></div>
                    <div class="more_details"><span><?php _e("More Details", WDD_LANG); ?></span></div>
                    <div class="logo"
                         style="background-image: url('<?php echo WDD_SITE_LINK . $value->logo; ?>');"></div>
                  </div>
                </div>
                <div class="plugin_body wd_clear">
                  <h3><?php echo $value->title; ?></h3>
                  <?php if ($value->installed_pro) : ?>
                    <div class="wd-more">
                      <a href="#" class="install-now installed"><?php _e("Installed", WDD_LANG); ?></a>
                    </div>
                  <?php else :
                    if ($value->is_expired()) : ?>
                      <div class="wd-more update-button">
                        <a href="#" class="update-link-expired"><?php _e("Install", WDD_LANG); ?><span
                            class="action_tooltip"><?php _e("You should renew product plan to get access to install", WDD_LANG); ?></span></a>
                      </div>
                    <?php else: ?>
                      <div class="wd-more">
                        <a href="#" class="install-now"><?php _e("Install", WDD_LANG); ?>
                          <span class="spinner"></span>
                        </a>
                      </div>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <p class="no_purchased"><?php _e("You don't have any purchased themes to be installed.", WDD_LANG); ?></p>
          <?php endif; ?>
        </div>
      </div>
      <!--FREE Available-->
      <?php if (count($freeThemes) && isset($freeThemes)) : ?>
        <div id="free_plugins_container" class="wd_tab_container">
          <div class="wd_plugins wd_clear">
            <?php foreach ($freeThemes as $key => $value):
              if ($value->id != 77) : /*Mottomag*/ ?>
                <div
                  class="wd_plugin_container wd_plugin free <?php echo $key; ?>  <?php echo strtolower(str_replace(array(" ", "/"), "", $value->title)); ?>"
                  data-plugin="<?php echo $key; ?>"
                  data-id="<?php echo $value->id; ?>"
                  data-ordering="<?php echo $value->ordering; ?>"
                  data-wpnonce="<?php echo wp_create_nonce('install-theme_' . $key); ?>">
                  <div class="plugin_header_container">
                    <div class="plugin_header">
                      <div class="view_demo"><span>
																	<a href="<?php echo $value->demo_link; ?>"
                                     target="_blank"><?php _e("View Demo", WDD_LANG); ?></a></span></div>
                      <div class="logo"
                           style="background-image: url('<?php echo WDD_SITE_LINK . $value->logo; ?>');"></div>
                    </div>
                  </div>
                  <div class="plugin_body wd_clear">
                    <h3><?php echo $value->title; ?></h3>
                    <div class="wd-more more_details">
                      <span class="more_details_span"><?php _e("Get Paid Version", WDD_LANG); ?></span>
                    </div>
                    <div class="wd-more">
                      <a
                        class="install-now"
                        href="<?php echo esc_url(wp_nonce_url(self_admin_url('update.php?action=install-theme&theme=' . $key . '&from=' . $wdd_options->prefix . '_themes'), 'install-theme_' . $key)); ?>"><?php _e("Install Free Version", WDD_LANG); ?>
                        <span class="spinner"></span></a>
                    </div>
                  </div>
                </div>
              <?php endif;
            endforeach; ?>
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
