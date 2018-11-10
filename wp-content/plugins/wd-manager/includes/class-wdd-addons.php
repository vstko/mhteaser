<?php
if (!defined('ABSPATH')) {
  exit;
}

class WDDaddons
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
  private $download_url;
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct()
  {
    $this->download_url = WDD_WP_UPDATES_PATH . "/" . $this->get_userhash() . "/";
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function display_addons_page()
  {
    global $wdd_options,  $self_update, $addons_updates;

    $purchasedPlugins = array();
    $proPlugins = array();
    $user_hash = $this->get_userhash();
    $site_url = urlencode(get_site_url());

    $availablePlugins = WDDProducts::getAvailablePlugins();
		$installedPlugins = WDDProducts::getInstalledPlugins();
		$update_all_products_count = WDDInstalledProduct::getUpdatesCount();
    if(!isset($availablePlugins) || $availablePlugins == null){
      $availablePlugins = array();
    }
    foreach($availablePlugins as $key => $value){
			if( $value->is_addon() ){
				if($value->is_buy){
					$purchasedPlugins[$key] = $value;
					if ($value->is_expired()) {
						$proPlugins[$key] = $value;
					}
				}else{
					$proPlugins[$key] = $value;
				}
			}
		}
    /*Install plugin*/
    $plugin_name = isset($_REQUEST["plugin_name"]) ? $_REQUEST["plugin_name"] : "";
    $zip_name = isset($_REQUEST["zip_name"]) ? $_REQUEST["zip_name"] : "";
    if ($plugin_name != "") {
      /*Delete dir if exists*/
      if (file_exists(WP_PLUGIN_DIR . "/" . $plugin_name)) {
        $this->wdd_rmdir(WP_PLUGIN_DIR . "/" . $plugin_name);
      }

      require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
      //require_once(ABSPATH .'wp-admin/includes/class-plugin-upgrader.php');
      $upgrader = new Plugin_Upgrader(new Plugin_Installer_Skin(compact('title', 'url', 'nonce', 'plugin', 'api')));
      $upgrader->install($this->download_url . $zip_name);

      exit;
    }

    /*For bundles banner*/
    $bundle["event_calendar_wd"] = WDDProducts::getActiveBundlePlan('addons', 86);
    $bundle["form_maker"] = WDDProducts::getActiveBundlePlan('addons', 31);
    require_once(WDD_DIR_TEMPLATES . "/display_addons.php");
  }


  private function wdd_rmdir($del_file)
  {
    static $input_file = null;
    if ($input_file == null) {
      $input_file = $del_file;
    }

    if (is_writeable($del_file) === false) {
      $this->plugin_delete_error_msg($input_file);
      return;
    }

    if (is_dir($del_file)) {
      $del_folder = scandir($del_file);
      foreach ($del_folder as $file) {
        if ($file != '.' && $file != '..') {
          $this->wdd_rmdir($del_file . '/' . $file);
        }
      }
      if (is_writeable($del_file) === false) {
        $this->plugin_delete_error_msg($input_file);
        return;
      }
      else {
        rmdir($del_file);
      }
    }
    else {
      if (is_writeable($del_file) === false) {
        $this->plugin_delete_error_msg($input_file);
        return;
      }
      else {
        unlink($del_file);
      }
    }
  }

  private function plugin_delete_error_msg($plugin_dir)
  {
    $plugin_dir_basename = basename($plugin_dir);

    $active_plugins = get_option('active_plugins', array()); // per site opt
    if(!isset($active_plugins) || !$active_plugins){
      $active_plugins = array();
    }
    foreach ($active_plugins as $plugin) {
      $plugin_id_components = explode('/', $plugin);
      if (count($plugin_id_components) == 2 && $plugin_id_components[0] == $plugin_dir_basename) {
        deactivate_plugins($plugin);
      }
    }

    $html = '';
    $html .= '<div id="message" class="wdm_message error updated notice is-dismissible wdd-message">';
    $html .= '<p> ' . sprintf('Permission error. Cannot delete %s folder or its content. Please delete it manually.', $plugin_dir) . '</p>';
    $html .= '</div>';

    echo $html;
  }

  public function get_userhash()
  {
    $api = new WDDApi();
    return $api->userhash;
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