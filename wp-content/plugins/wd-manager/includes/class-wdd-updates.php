<?php
if (!defined('ABSPATH')) {
  exit;
}

class WDDUpdate
{
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////

  protected $updates = array();
  protected $plugins = array();
  protected $userhash;
  protected $download_url;


  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct()
  {
    //require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
    global $wdd_options;
    add_filter('site_transient_update_plugins', array($this, 'inject_update'));
    add_filter('site_transient_update_plugins', array($this, 'remove_plugin_from_no_update'));
    add_filter('site_transient_update_themes', array($this, 'inject_theme_update'));
    add_filter('site_transient_update_themes', array($this, 'remove_theme_from_no_update'));
    add_action('upgrader_process_complete', array($this, "after_update"));
    $this->userhash = $this->get_userhash();
    $this->download_url = WDD_WP_UPDATES_PATH . "/" . $this->userhash . "/";
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////

  public function inject_theme_update($update_themes)
  {
    $all_pro_themes = get_site_option("wdd_all_pro_themes");
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    if ((isset($_GET["page"]) && $_GET["page"] == "WDD_themes") || $action == "upgrade-theme") {
      if (!is_object($update_themes))
        return $update_themes;
      if (!isset($update_themes->response) || !is_array($update_themes->response))
        $update_themes->response = array();
      if ($all_pro_themes) {
        foreach ($all_pro_themes as $key => $theme) {
          if ($this->check_existing_file("themes/" . $key)) {
            $update_themes->response[$key] = array(
              'theme' => $key,
              'new_version' => '',
              'url' => 'https://wordpress.org/themes/' . $key . '/',
              'package' => $this->download_url . $theme["zip_name"]
            );
          }
        }
      }
    }
    return $update_themes;
  }



  public function inject_update($update_plugins)
  {

    global $wdd_options;
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    if ((isset($_GET["page"]) && ($_GET["page"] == "WDD_plugins" || $_GET["page"] == "WDD_addons")) || $action == "upgrade-plugin") {
      if (!is_object($update_plugins))
        return $update_plugins;
      if (!isset($update_plugins->response) || !is_array($update_plugins->response))
        $update_plugins->response = array();

      $all_pro_plugins = get_site_option("wdd_all_pro_plugins");
      if ($all_pro_plugins) {
        foreach ($all_pro_plugins as $key => $product) {
          $plugin_name = substr($key, 0, strpos($key, "/"));
          if ($this->check_existing_file("plugins/" . $plugin_name) || $key == WDD_SLUG) {
            $update_plugins->response[$key] = (object)array(
              'slug' => $plugin_name,
              'new_version' => '', // The newest version
              'url' => 'https://wordpress.org/plugins/' . $plugin_name, // Informational
              'package' => $this->download_url . $product["zip_name"] // Where WordPress should pull the ZIP from.
            );
          }
        }
      }
    }

    return $update_plugins;
  }

  public function remove_theme_from_no_update($value)
  {


    if (isset($value) && is_object($value)) {
      $all_pro_themes = get_site_option("wdd_all_pro_themes");
      if ($all_pro_themes) {
        foreach ($all_pro_themes as $key => $product) {
          if ($this->check_existing_file("themes/" . $key)) {
            unset($value->no_update[$key]);
          }
        }
      }
    }

    return $value;
  }

  public function remove_plugin_from_no_update($value)
  {


    if (isset($value) && is_object($value)) {
      $all_pro_plugins = get_site_option("wdd_all_pro_plugins");


      if ($all_pro_plugins) {
        foreach ($all_pro_plugins as $key => $product) {

          $plugin_name = substr($key, 0, strpos($key, "/"));
          if ($this->check_existing_file("plugins/" . $plugin_name)) {

            /*remove update notifications from plugins menu when user is on WDD page*/
            if ((isset($_GET["page"]) && ($_GET["page"] == "WDD_plugins" || $_GET["page"] == "WDD_addons")) ) {
              unset($value->response[$key]);
            }
            unset($value->no_update[$key]);
          }
        }
      }
    }

    return $value;
  }

  public function after_update()
  {
    global $wdd_options;
    $this->plugin_updated();

    $all_pro_plugins = get_site_option("wdd_all_pro_plugins");
    if ($all_pro_plugins) {
      foreach ($all_pro_plugins as $key => $product) {
        $plugin_name = substr($key, 0, strpos($key, "/"));
        $ufter_update = add_query_arg(
          array(
            'plugin_name' => $plugin_name,
            'user_id' => $this->userhash,
          ),
          WDD_UPDATE_PATH);
        wp_remote_get($ufter_update);
      }
    }
  }

  public function refresh_updates($plugins)
  {
    
    $this->plugin_updated();
    global $wdd_options;
    $addons_updates = array();
    $addons_updates_available = array();
    $plugins_updates_available = array();
    $themes_updates_available = array();
    $agreements = array();
    //$remote_data = get_option( $wdd_options->prefix . '_remote_data' );
    /*plugins*/




    $data_plugins = array("updates_available" => $plugins_updates_available, "addons_updates_available" => $addons_updates_available);

    $data_themes = array("updates_available" => $themes_updates_available);

    $result = array_merge($plugins_updates_available, $themes_updates_available, $addons_updates_available);
    $remote_data = array("updates_available" => $result, "agreements" => $agreements);

    //update_option( $wdd_options->prefix.'_remote_data', $remote_data, 12 * 60 * 60 );
    $updates_available = $remote_data["updates_available"];

    $this->updates = $updates_available;

    $updates_count = is_array($updates_available) ? count($updates_available) : 0;
    $updates_plugins = is_array($data_plugins["updates_available"]) ? count($data_plugins["updates_available"]) : 0;
    $updates_addons = is_array($data_plugins["addons_updates_available"]) ? count($data_plugins["addons_updates_available"]) : 0;
    $updates_themes = is_array($data_themes["updates_available"]) ? count($data_themes["updates_available"]) : 0;

    $return_array = array("plugins" => $this->installed_plugins, "themes" => $this->installed_themes, "plugins_count" => $updates_plugins, "themes_count" => $updates_themes, "addons_count" => $updates_addons, "addons_updates" => $addons_updates);
    return $return_array;
  }


  public function plugin_updated()
  {
    global $wdd_options;
    delete_site_transient('wdd_remote_data');
  }


  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////


  private function get_userhash()
  {
    $api = new WDDApi();
    return $api->userhash;
  }

  private function check_existing_file($path)
  {
    if (file_exists(WP_CONTENT_DIR . "/" . $path . '/.keep')) {
      return true;
    }
    return false;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////

}