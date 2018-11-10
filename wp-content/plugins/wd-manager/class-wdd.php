<?php
if (!defined('ABSPATH')) {
  exit;
}

class WDD {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  public static $instance;
  private $update = null;
  private $self_update = null;
  private $coupons = null;
  private $offers_date = null;

  private $menues = array();

  private static $wdd_notices = null;
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  protected function __construct() {


    add_filter('http_request_args', array($this, 'add_request_timeout'), 2, 2);

    $api = new WDDApi();

    WDDProducts::addProductsData($api->get_products_data());
    $wdd_manager_plugin = WDDProducts::getPluginByID(WDD_ID);
    
    $this->self_update = (method_exists($wdd_manager_plugin, "has_update")) ? $wdd_manager_plugin->has_update() : false;


    $this->coupons = WDDProducts::getCoupons();
    $this->diff = WDDProducts::getDiff();
    $this->offers_date = WDDProducts::getOffersDate();

    if (is_multisite() === true) {
      add_action('network_admin_menu', array($this, 'wd_overview_menu_page'), 24);
    } else {
      add_action('admin_menu', array($this, 'wd_overview_menu_page'), 24);
    }
    
    add_action('wp_ajax_wdd_plugins_action', array($this, 'wdd_plugins_action'));

    //Remove plugin update notification  from plugin list
    add_filter('site_transient_update_plugins', array($this, 'remove_plugin_from_updates'), 1);
    add_action('admin_post_wd_logout', array($this, 'wd_logout'));
    add_action('admin_post_nopriv_wd_logout', array($this, 'wd_logout'));

    add_action('admin_footer', array($this, 'print_request_filesystem_credentials_modal'));
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////

  public function wd_logout() {
    check_admin_referer('nonce_WDD', 'nonce_WDD');
    delete_site_option("wdd_user_hash");
    delete_site_option("wdd_user_full_name");
    delete_site_transient("wdd_remote_theme_plugin_data");
    wp_redirect(network_admin_url('admin.php?page=WDD_plugins'));
  }

  // Return an instance of this class.
  public static function get_instance() {
    if (null == self::$instance) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  // Init plugin data
  public function wdd_init($options) {
    global $wdd_options;
    if (!is_array($options)) {
      return false;
    }
    if (isset($options["prefix"])) {
      $wdd_options->prefix = $options["prefix"];
    }
    if (isset($options["plugin_main_file"])) {
      $wdd_options->plugin_main_file = $options["plugin_main_file"];
    }
    if (isset($options["plugin_menu_parent_slug"])) {
      $wdd_options->plugin_menu_parent_slug = $options["plugin_menu_parent_slug"];
    }
    if (isset($options["after_activate"])) {
      $wdd_options->after_activate = $options["after_activate"];
    }
    $this->wd_includes();
    $this->register_hooks();
    
    add_action( 'admin_enqueue_scripts', array($this, 'wdd_scripts'));
    
  }

  public function wd_overview_menu_page() {
    global $wdd_options;
    $special_offers = 0;
    $wdd_activate = (get_site_option("wdd_activate") !== false) ? get_site_option("wdd_activate") : 0;
    /*Show Special offers page after day */
    if ((time() - $wdd_activate) > (24 * 60 * 60)) {
      $special_offers = 1;
      if (get_site_option("wdd_first_gift") === false) {
        add_site_option("wdd_first_gift", 1);
      }
    }
    if (get_site_option("wdd_special_offers") === false) {
      add_site_option("wdd_special_offers", $special_offers);
    } else {
      update_site_option("wdd_special_offers", $special_offers);
    }
    $wdd_first_gift = (get_site_option("wdd_first_gift") !== false && get_site_option("wdd_first_gift") == 1) ? true : false;

    if ($this->update == null) {
      $update = new WDDUpdate();
    } else {
      $update = $this->update;
    }

    $this->updates_count = WDDProducts::getUpdatesCount();

    $self_update = 0;
    if ($this->self_update)
      $self_update = 1;
    $update_count = $this->updates_count['plugins'] + $this->updates_count['themes'] + array_sum($this->updates_count['addons']) + $self_update;
    add_menu_page(
      'Manager',
      'Manager <span class="update-plugins count-' . $update_count . '" title="title"><span class="update-count">' . $update_count . '</span></span>' . (($special_offers && ($this->diff || $wdd_first_gift)) ? '<img src="' . WDD_URL_IMG . '/gift.png" style="margin: 0 0 -2px 3px;">' : ''),
      'manage_options',
      $wdd_options->plugin_menu_parent_slug,
      array($this, 'display_plugins_page'),
      WDD_URL_IMG . '/wd_logo.png',
      2
    );

    /*Plugins*/
    $plugins_page = add_submenu_page($wdd_options->plugin_menu_parent_slug, __('Plugins', WDD_LANG), __('Plugins', WDD_LANG) . '<span class="update-plugins count-' . $this->updates_count['plugins'] . '" title="title"><span class="update-count">' . $this->updates_count['plugins'] . '</span></span>', 'manage_options', $wdd_options->plugin_menu_parent_slug, array($this, 'display_plugins_page'));
    /*Themes*/
    $themes_page = add_submenu_page($wdd_options->plugin_menu_parent_slug, __('Themes', WDD_LANG), __('Themes', WDD_LANG) . '<span class="update-plugins count-' . $this->updates_count['themes'] . '" title="title"><span class="update-count">' . $this->updates_count['themes'] . '</span></span>', 'manage_options', $wdd_options->prefix . '_themes', array($this, 'display_themes_page'));
    /*Add-ons*/
    $addons_page = add_submenu_page($wdd_options->plugin_menu_parent_slug, __('Add-ons', WDD_LANG), __('Add-ons', WDD_LANG) . '<span class="update-plugins count-' . array_sum($this->updates_count['addons']) . '" title="title"><span class="update-count">' . array_sum($this->updates_count['addons']) . '</span></span>', 'manage_options', $wdd_options->prefix . '_addons', array($this, 'display_addons_page'));

    $this->menues = array('plugins' => $plugins_page, 'themes' => $themes_page, 'addons' => $addons_page, 'special_offers' => $plugins_page);

    /*Special Offers*/
    if ($special_offers) {
      $special_offers_page = add_submenu_page($wdd_options->plugin_menu_parent_slug, __('Special Offers', $wdd_options->prefix), __('Special Offers', $wdd_options->prefix) . (($this->diff || $wdd_first_gift) ? '<img src="' . WDD_URL_IMG . '/gift.png" style="margin: 0 0 -2px 3px;">' : ''), 'manage_options', $wdd_options->prefix . '_special_offers', array($this, 'display_special_offers_page'));
      $this->menues['special_offers'] = $special_offers_page;
    }



  }

  public function wdd_styles() {
    global $wdd_options;

    wp_enqueue_style($wdd_options->prefix . '_plugins_css', WDD_URL_CSS . '/overview.css', array(), WDD_VERSION);
  }

  public function wdd_scripts($hook){

    if($hook == $this->menues['plugins'] || $hook == $this->menues['addons'] || $hook == $this->menues['special_offers']){
      $this->wdd_styles();

      wdd_common_scripts_styles();
      $this->plugins_scripts();
    }
    else if($hook == $this->menues['themes']){
      $this->wdd_styles();

      wdd_common_scripts_styles();
      $this->themes_scripts();
    }





  }


  public function plugins_scripts() {
    global $wdd_options;
    wp_enqueue_script($wdd_options->prefix . '_plugins_js', WDD_URL_JS . '/overview.js', array(), WDD_VERSION);
    wp_localize_script($wdd_options->prefix . '_plugins_js', $wdd_options->prefix . '_options', array(
      "product_url" => WDD_WP_PRODUCT_PATH,
      "plugins_url" => network_admin_url("plugins.php"),
      "ajax_url" => admin_url("admin-ajax.php"),
      'product_update_nonce' => wp_create_nonce('updates'),
      'plugins_page_url' => admin_url("plugins.php"),
      'update_page_url' => admin_url("update.php"),
      'img_path' => WDD_URL_IMG
    ));

  }

  public function themes_scripts() {
    global $wdd_options;
    wp_enqueue_script($wdd_options->prefix . '_themes_js', WDD_URL_JS . '/overview.js', array(), WDD_VERSION);
    wp_localize_script($wdd_options->prefix . '_themes_js', $wdd_options->prefix . '_options', array(
      "product_url" => WDD_WP_PRODUCT_PATH,
      "themes_url" => network_admin_url("themes.php"),
      "ajax_url" => admin_url("admin-ajax.php"),
      'product_update_nonce' => wp_create_nonce('updates'),
      'plugins_page_url' => admin_url("plugins.php"),
      'update_page_url' => admin_url("update.php"),
      'img_path' => WDD_URL_IMG
    ));
  }

  // Display plugins page
  public function display_plugins_page() {
    global $self_update;
    require_once(WDD_DIR_INCLUDES . "/class-wdd-plugins.php");
    $plugins_instance = new WDDplugins();

    $self_update = $this->self_update;
    $plugins_instance->display_plugins_page();
  }

  // Display themes page
  public function display_themes_page() {

    global $self_update;
    require_once(WDD_DIR_INCLUDES . "/class-wdd-themes.php");
    $themes_instance = new WDDthemes();

    $self_update = $this->self_update;
    $themes_instance->display_themes_page();
  }

  // Display addons page
  public function display_addons_page() {

    global $self_update;

    require_once(WDD_DIR_INCLUDES . "/class-wdd-addons.php");
    $addons_instance = new WDDaddons();

    $self_update = $this->self_update;
    $addons_instance->display_addons_page();
  }

  public function print_request_filesystem_credentials_modal() {

    $screen = get_current_screen();
    if(!in_array($screen->id, $this->menues)) {
      return;
    }

    ob_start();
    $credentials = request_filesystem_credentials(site_url());
    ob_end_clean();

    if ($credentials !== false) {
      return;
    }

    ?>

    <div id="request-filesystem-credentials-dialog"
         class="notification-dialog-wrap request-filesystem-credentials-dialog">
      <div class="notification-dialog-background"></div>
      <div class="notification-dialog" role="dialog" aria-labelledby="request-filesystem-credentials-title"
           tabindex="0">
        <div class="request-filesystem-credentials-dialog-content">
          <?php request_filesystem_credentials(site_url()); ?>
        </div>
      </div>
    </div>
    <?php

  }

  // Display addons page
  public function display_special_offers_page() {
    global $self_update, $wd_coupons;


    require_once(WDD_DIR_INCLUDES . "/class-wdd-special-offers.php");

    $special_offers_instance = new WDDspecial_offers();

    $self_update = $this->self_update;
    $wd_coupons = $this->coupons;
    $this->diff = 0;
    if (get_site_option("wdd_first_gift") !== false) {
      update_site_option("wdd_first_gift", 0);
    }
    update_site_option("wdd_coupons", json_encode($this->coupons));
    update_site_option("wdd_offers_date", $this->offers_date);
    $special_offers_instance->display_special_offers_page();
  }

  // Remove plugin update notification  from plugin list
  public function remove_plugin_from_updates($value) {
    global $wdd_options;
    if (isset($value) && is_object($value)) {
      // check not your plugin pages pages
      $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
      $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

      $all_pro_plugins = get_site_option("wdd_all_pro_plugins");
      if ($all_pro_plugins) {
        foreach ($all_pro_plugins as $key => $product) {
          if (strpos($page, $wdd_options->prefix) === false || $action != "upgrade-plugin") {
            unset($value->response[$key]);
          }
        }
      }
    }

    return $value;
  }


  // Includs
  public function wd_includes() {
    global $wdd_options;
    $current_url = $_SERVER['REQUEST_URI'];
    /*if(strpos( $current_url, "plugins.php" ) !== false ){
      require_once( WDD_DIR_INCLUDES . '/class-wdd-deactivate.php' );
      new WDDeactivate();
    }*/
    require_once(WDD_DIR_INCLUDES . "/class-wdd-updates.php");
  }

  public function register_hooks() {
    global $wdd_options;

    //add_filter( 'plugin_action_links_' . plugin_basename( $wdd_options->plugin_main_file ),  array( $this, 'change_deactivation_link' ) );
    add_action('wp_ajax_' . $wdd_options->prefix . '_change_log', array($this, 'change_log'));
  }


  public function change_log($links) {



    /// todo byid, by slug instead !!!
    global $wdd_options;
    $prod_id = isset($_REQUEST["prod_id"]) ? $_REQUEST["prod_id"] : "";
    $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";

    if ($page == "WDD_themes")
      $installed_product = WDDProducts::getThemeBySlug($prod_id , 'installed');
    else
      $installed_product = WDDProducts::getPluginBySlug($prod_id, 'installed');


    if ($installed_product) {

      $new_version = $installed_product->version;
      $content = "";
      $available_update = array_reverse($installed_product->get_changelog_data());

      $image = $installed_product->logo;
      $whats_new = "<table><tr><th colspan='2'>" . __("What's new", WDD_LANG) . "</th></tr>";
      $style = "";
      for ($i = 0; $i < count($available_update); $i++) {
        if ($i > 3)
          $style = "style='display:none'";
        $whats_new .= "<tr " . $style . "><td>" . $available_update[$i]["version"] . "</td><td>" . $available_update[$i]["note"] . "</td></tr>";
        if ($i == 0) {
          $new_version = $available_update[$i]["version"];
        }
      }
      $whats_new .= "</table>";
      $content .= "<div class='log_header' style='background-image:url(" . WDD_SITE_LINK . $image . ")'><h4>" . $installed_product->title . "</h4><p>" . __("Version", WDD_LANG) . " " . $installed_product->version . "</p></div>";
      $content .= "<div class='update_content'><p>" . __("There is a new", WDD_LANG) . " <b>" . $new_version . "</b> " . __("version", WDD_LANG) . "</p><p>";
      if (!$installed_product->not_this_user) {
        $content .= "<a href='' data-id='".$installed_product->id."' class='update_now'>" . __("Update now", WDD_LANG) . "<span class='spinner'></span></a>";
      } else {
        $content .= "<div class='wd-more'><a href='#' class='update-link-expired update-link'>" . __("Update now", WDD_LANG) . "<span class='action_tooltip'>" . __("You do not have a subscription for updates", WDD_LANG) . "</span></a></div>";
      }
      $content .= "</p></div>";
      $content .= $whats_new;
      if (count($available_update) > 3) {
        $content .= "<div id='see_more' onclick='WDDMoreUpdates(this);'>" . __("More updates", WDD_LANG) . "</div>";
      }

      echo $content;
    }
    exit;
  }

  function change_deactivation_link($links) {
    global $wdd_options;

    $links["deactivate"] = '<a href="#" class="' . $wdd_options->prefix . '_deactivate_link">Deactivate</a>';
    return $links;
  }

  public function wdd_plugins_action(){

    $response = array(
      'success' => false,
      'message' => '',
      'data' => array()
    );

    if (current_user_can('manage_options') === false) {
      $response['message'] = 'PERMISSION DENIED';
      die(json_encode($response));
    }


    if (!check_ajax_referer('updates', 'wdd_nonce', false)) {
      $response['message'] = 'WRONG NONCE';
      die(json_encode($response));
    }

    $product_slug = isset($_REQUEST["slug"]) ? $_REQUEST["slug"] : "";
    if($product_slug == ""){
      $response['message'] = 'PRODUCT SLUG IS EMPTY';
      die(json_encode($response));
    }

    $is_theme = (isset($_REQUEST['is_theme']) && $_REQUEST['is_theme'] == '1');

    if($is_theme == true){
      $product = WDDProducts::getThemeBySlug($product_slug);
    }else {
      $product = WDDProducts::getPluginBySlug($product_slug);
    }

    if($product === false){
      $response['message'] = 'NO PRODUCT WITH SLUG '.$product_slug;
      die(json_encode($response));
    }

    if(empty($_REQUEST['wdd_action'])){
      $response['message'] = 'WDD_ACTION NOT EXISTS';
      die(json_encode($response));
    }

    if(isset($_REQUEST['filesystem_credentials'])){
      $filesystem_credentials = wp_parse_args($_REQUEST['filesystem_credentials']);
      if(is_array($filesystem_credentials) && !empty($filesystem_credentials)){

        foreach ($filesystem_credentials as $key=>$value) {
          $_POST[$key] = $value;
        }

      }
    }


    switch ($_REQUEST['wdd_action']) {

      case 'activate':
        $multisite = is_multisite();
        if ($is_theme == true) {
          $response = $this->activate_theme($product, $multisite);
        } else {
          $response = $this->activate_plugin($product, $multisite);
        }
        break;
      case 'deactivate':
        $multisite = is_multisite();
        if ($is_theme == true) {

        } else {
          $response = $this->deactivate_plugin($product, $multisite);
        }
        break;
      case 'install':

        $userhash = get_site_option("wdd_user_hash");
        $update_path = add_query_arg(
          array(
            'product_id' => $product->id,
            'user_id' => $userhash,
          ),
          WDD_UPDATE_PATH);

        $is_pro = (isset($_REQUEST['pro']) && $_REQUEST['pro'] === "1");
        $activate = (isset($_REQUEST['activate']) && $_REQUEST['activate'] === '1');
        $update = (isset($_REQUEST['update']) && $_REQUEST['update'] === '1');

        if ($is_pro == true) {
          /* create file for current user and current product */
          $request = wp_remote_get($update_path);
          if (is_wp_error($request)) {
            $response['message'] = $request->get_error_messages();
            break;
          } else if (!isset($request['body']) || ($request['body'] != 1)) {
            $response['message'] = sprintf(__('"%s": Plugin file does not exist on server.', WDD_LANG), $product->title);
            break;
          }
        }

        if ($is_theme == true) {
          $response = $this->install_themes($product, $is_pro, $update);
        } else {
          $response = $this->install_plugins($product, $is_pro, $activate, $update);
        }
        break;
      case 'delete':

        if($is_theme == false){
          $response = $this->delete_plugin($product);
        }else{
          $response = $this->delete_theme($product);
        }

        break;
    };

    $messages = (!is_array($response['message'])) ? array($response['message']) : $response['message'];

    if (isset($response['data']['activate_response']['message'])) {
      array_push($messages, $response['data']['activate_response']['message']);
    }

    self::set_notices($product->slug, $messages, $response['success']);

    echo 'wdd_ajax_response_delimiter';//on install action generated html
    die(json_encode($response));
  }

  public function activate_plugin($product, $multisite) {
    $response = array('success' => false, 'message' => '', 'data' => array());
//Failed to install/activate/deactivate the plugin "Calendar".

    /* do not use silent activation until all plugins are geady for that */
    $result = activate_plugin($product->slug, '', $multisite, false);
    if (is_wp_error($result)) {
      $response['success'] = false;
      $response['message'] = sprintf(__('Failed to activate the plugin "%s".', WDD_LANG), $product->title);
      $response['data'] = $result;
    } else {
      $response['success'] = true;
      $response['message'] = sprintf(__('"%s" plugin activated.', WDD_LANG), $product->title);
    }
    return $response;
  }

  public function activate_theme($product, $multisite) {
    $response = array('success' => false, 'message' => '', 'data' => array());

    if($multisite === false){

      switch_theme($product->slug);
      $result = $this->is_theme_active($product, $multisite);
    }else{
      $result = false;
    }


    if ($result == false) {
      $response['success'] = false;
      $response['message'] = sprintf(__('Failed to activate the theme "%s".', WDD_LANG), $product->title);
    } else {
      $response['success'] = true;
      $response['message'] = sprintf(__('"%s" theme activated.', WDD_LANG), $product->title);
    }
    return $response;
  }

  public function deactivate_plugin($product, $multisite) {
    $response = array('success' => false, 'message' => '', 'data' => array());

    deactivate_plugins($product->slug, true, $multisite);

    if ($this->is_plugin_active($product->slug, $multisite) == true) {
      $response['success'] = false;
      $response['message'] = sprintf(__('Failed to deactivate the plugin "%s".', WDD_LANG), $product->title);
    } else {
      $response['success'] = true;
      $response['message'] = sprintf(__('"%s" plugin deactivated.', WDD_LANG), $product->title);
    }
    return $response;
  }

  public function install_plugins($product, $is_pro, $activate, $update) {
    $response = array('success' => false, 'message' => '', 'data' => array());

    require_once(WDD_DIR_INCLUDES . "/class-wdd-plugins.php");
    $plugins_instance = new WDDplugins();

    $installed = $plugins_instance->upgrade_plugin($product, $is_pro);

    if ($installed === true) {
      $response['success'] = true;

      if($update == true) {
        $response['message'] = sprintf(__('"%s" plugin updated.', WDD_LANG), $product->title);
      }else{
        $response['message'] = sprintf(__('"%s" plugin installed.', WDD_LANG), $product->title);
      }

      if (is_multisite() == false && $activate == true) {
        $response['data'] = array(
          'activate_response' => $this->activate_plugin($product, is_multisite())
        );
      }

    } else {

      if($update == false) {
        $response['message'] = sprintf(__('Failed to install the plugin "%s".', WDD_LANG), $product->title);
      }else{
        $response['message'] = sprintf(__('Failed to update the plugin "%s".', WDD_LANG), $product->title);
      }

    }

    return $response;

  }

  public function install_themes($product, $is_pro,$update) {
    $response = array('success' => false, 'message' => '', 'data' => array());

    require_once(WDD_DIR_INCLUDES . "/class-wdd-themes.php");
    $themes_instance = new WDDthemes();

    $installed = $themes_instance->upgrade_theme($product, $is_pro);

    if ($installed === true) {
      $response['success'] = true;

      if($update == true){
        $response['message'] = sprintf(__('"%s" theme updated.', WDD_LANG), $product->title);
      }else{
        $response['message'] = sprintf(__('"%s" theme installed.', WDD_LANG), $product->title);
      }

    } else {
      if($update == true) {
        $response['message'] = sprintf(__('Failed to update the theme "%s".', WDD_LANG), $product->title);
      }else{
        $response['message'] = sprintf(__('Failed to install the theme "%s".', WDD_LANG), $product->title);
      }
    }

    return $response;
  }

  public function delete_plugin($product){
    $response = array('success' => false, 'message' => '', 'data' => array());
    $delete_plugins = delete_plugins(array($product->slug));
    if ($delete_plugins == true) {
      $response['success'] = true;
      $response['message'] = sprintf(__('"%s" plugin deleted.', WDD_LANG), $product->title);
    } else {
      $response['message'] = sprintf(__('Failed to delete the plugin "%s".', WDD_LANG), $product->title);
      $response['data']['result'] = $delete_plugins;
    }

    return $response;
  }

  public function delete_theme($product){
    $response = array('success' => false, 'message' => '', 'data' => array());

    $delete_themes = delete_theme($product->slug);
    $theme_path = WP_CONTENT_DIR . "/themes/" . $product->slug;

    if (!file_exists($theme_path)) {
      $response['success'] = true;
      $response['message'] = sprintf(__('"%s" theme deleted.', WDD_LANG), $product->title);
    } else {
      $response['message'] = sprintf(__('Failed to delete the theme "%s".', WDD_LANG), $product->title);
      $response['data']['result'] = $delete_themes;
    }

    return $response;
  }

  public function is_plugin_active($slug, $multisite) {
    if ($multisite == true) {
      return is_plugin_active_for_network($slug);
    } else {
      return is_plugin_active($slug);
    }
  }
  
  public function is_theme_active($slug, $multisite) {

    $activeTheme = wp_get_theme();

    if($multisite == false) {
      if (str_replace(" Theme", "", $slug->title) == $activeTheme["Name"]) {
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }

  }


  static function message($type) {
    global $wdd_options;
    if ($type == "message") {
      $html = '<div id="wdm_overlay" class="wdd_self_update_modal"><div class="wdm_update"><p>' . __("New version of WD manager is available!", WDD_LANG) . '</p> <a class="self_update">' . __("Please update now", WDD_LANG) . '<span class="spinner"></span></a>';
      $html .= '</div></div>';
    } else {
      $html = "";
    }
    echo $html;

  }

  public static function print_notices() {

    $notices = get_site_option('wdd_notices');

    if (!is_array($notices) || empty($notices)) {
      return;
    }

    foreach ($notices as $slug => $notice) {

      if (empty($notice['messages'])) {
        continue;
      }

      $notice_class = ($notice['success'] == true) ? 'success' : 'error';
      echo '<div class="wdm_message ' . $notice_class . ' updated notice is-dismissible wdd-message">';
      foreach ($notice['messages'] as $message) {
        echo '<p>' . $message . '</p>';
      }
      echo '</div>';

    }
    update_site_option('wdd_notices', array());

//    if ($type == "1") {
//      /*no response body*/
//      $message = __("Sorry, something went wrong. We’re working on getting this fixed as soon as we can.", WDD_LANG);
//    } elseif ($type == "2") {
//      /*response with 404 or similar message*/
//      $message = __("Sorry, something went wrong. The resource you requested could not be found.", WDD_LANG);
//    } else {
//      /*other unknown error*/
//      $message = __("Sorry, something went wrong.", WDD_LANG);
//    }
  }

  public static function set_notices($slug = null, $messages = array(), $success = false) {
    if ($slug == null) {
      $slug = uniqid();
    }

    if (self::$wdd_notices == null) {

      $wdd_notices = get_site_option('wdd_notices');

      if (!is_array($wdd_notices)) {
        $wdd_notices = array();
      }

      self::$wdd_notices = $wdd_notices;
    }

    self::$wdd_notices[$slug] = array(
      'messages' => $messages,
      'success' => $success
    );
    update_site_option('wdd_notices', self::$wdd_notices);
  }


  public function add_request_timeout($args, $url){
      if(strpos($url,'api.web-dorado.com/v2.1') !== false){
        $args['timeout'] = 15;
      }
      return $args;
  }


  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////

  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////

}
