<?php
if(!defined('ABSPATH')) {
  exit;
}

class WDDplugins {
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
  public function __construct(){
    $this->download_url = WDD_WP_UPDATES_PATH . "/" . $this->get_userhash() . "/";
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function display_plugins_page(){
    global $wdd_options, $self_update;
    $proPlugins = array();
    $freePlugins = array();
    $user_hash = $this->get_userhash();
    $site_url = urlencode(get_site_url());

    $bundle = WDDProducts::getActiveBundlePlan('plugins');

    $update_all_products_count = WDDInstalledProduct::getUpdatesCount();
    /*with addons*/
    $installedPlugins = WDDProducts::getInstalledPlugins();
    $availablePlugins = WDDProducts::getAvailablePlugins();

    /*only plugins, exclude manager*/
    $installed_count = WDDProducts::getInstalledPluginsCount(false, 0, true);
    $inactive_count = WDDProducts::getInstalledPluginsCount(false, 0);
    $active_count = WDDProducts::getInstalledPluginsCount(true, 0);
    if(!isset($availablePlugins) || $availablePlugins == null) {
      $availablePlugins = array();
    }
    foreach($availablePlugins as $slug => $plugin) {
      if(!$plugin->is_addon()) {
        if($plugin->is_pro) {
          $proPlugins[$slug] = $plugin;
          if($plugin->is_expired()) {
            $freePlugins[$slug] = $plugin;
          }
        } else {
          $freePlugins[$slug] = $plugin;
        }
      }
    }

    uasort($proPlugins, array($this, 'sortByOrdering'));
    uasort($freePlugins, array($this, 'sortByOrdering'));

    /*Install plugin*/
    $plugin_name = isset($_REQUEST["plugin_name"]) ? $_REQUEST["plugin_name"] : "";
    $zip_name = isset($_REQUEST["zip_name"]) ? $_REQUEST["zip_name"] : "";


    //TODO remove $all_plugins=false;
    $all_plugins = false;
    require_once(WDD_DIR_TEMPLATES . "/display_plugins.php");
  }

  private function sortByOrdering($a, $b){
    return $a->ordering - $b->ordering;
  }

  private function wdd_rmdir($del_file){
    static $input_file = null;
    if($input_file == null) {
      $input_file = $del_file;
    }

    if(is_writeable($del_file) === false) {
      $this->plugin_delete_error_msg($input_file);
      return;
    }

    if(is_dir($del_file)) {
      $del_folder = scandir($del_file);
      foreach($del_folder as $file) {
        if($file != '.' && $file != '..') {
          $this->wdd_rmdir($del_file . '/' . $file);
        }
      }
      if(is_writeable($del_file) === false) {
        $this->plugin_delete_error_msg($input_file);
        return;
      } else {
        rmdir($del_file);
      }
    } else {
      if(is_writeable($del_file) === false) {
        $this->plugin_delete_error_msg($input_file);
        return;
      } else {
        unlink($del_file);
      }
    }
  }

  private function plugin_delete_error_msg($plugin_dir){
    $plugin_dir_basename = basename($plugin_dir);

    $active_plugins = get_option('active_plugins'); // per site option
    if(!isset($active_plugins) || !$active_plugins) {
      $active_plugins = array();
    }
    foreach($active_plugins as $plugin) {
      $plugin_id_components = explode('/', $plugin);
      if(count($plugin_id_components) == 2 && $plugin_id_components[0] == $plugin_dir_basename) {
        deactivate_plugins($plugin);
      }
    }

    $message = array(
      sprintf('Permission error. Cannot delete %s folder or its content. Please delete it manually.', $plugin_dir)
    );
    WDD::set_notices(uniqid(), $message, false);
  }

  public function upgrade_plugin($product, $is_pro){

    require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
    require_once(ABSPATH . 'wp-admin/includes/plugin-install.php');

    //array( WP_CONTENT_DIR, $options['destination'] )

//    require_once(ABSPATH . 'wp-admin/includes/file.php');
//
//    $e = WP_Filesystem();
//    var_dump($e);die;




    $skin = new Plugin_Installer_Skin(compact('title', 'url', 'nonce', 'plugin', 'api'));
    $credentials = $skin->request_filesystem_credentials(false, WP_CONTENT_DIR, false);
    if($credentials == false) {
      //notice add define('FS_METHOD', 'direct'); on config.php
      //check wp-content/upgrade folder permission
      //check wp-content/plugins folder permission
      //check wp-content/plugins/plugin-slug folder permission if it exists(check for delete on update action)
    }
    $upgrader = new Plugin_Upgrader(new Plugin_Installer_Skin(compact('title', 'url', 'nonce', 'plugin', 'api')));

    $fs_options = apply_filters('upgrader_package_options', array('destination' => WP_PLUGIN_DIR));
    if($upgrader->fs_connect($fs_options) !== true) {
      WDD::set_notices('wdd_fs_issue', array(__('File system error. Invalid file permissions or FTP credentials.', WDD_LANG)), false);
      return false;
    }

    /*Delete dir if exists*/
    $plugin_folder = dirname($product->slug);

    $plugin_folder = str_replace(array('/', "\\"), array('', ''), $plugin_folder);
    if(!empty($plugin_folder) && $plugin_folder != '.' && file_exists(WP_PLUGIN_DIR . "/" . $plugin_folder)) {
      $this->wdd_rmdir(WP_PLUGIN_DIR . "/" . $plugin_folder);
    }

    if($product->id == 177) { // manager
      $result = $upgrader->install(WDD_WP_UPDATES_PATH . '/free/WD-manager.zip');
    } elseif($is_pro === true) {
      $result = $upgrader->install($this->download_url . $product->zip_name);
    } else {

      $plugin_slug_on_wp = $slug = substr($product->slug, 0, strpos($product->slug, "/"));//TODO CHECK
      $api = plugins_api('plugin_information', array(
        'slug' => sanitize_key(wp_unslash($plugin_slug_on_wp)),
        'fields' => array(
          'sections' => false,
        ),
      ));

      if(is_wp_error($api)) {
        return false;
      }

      $download_link = (is_array($api)) ? $api['download_link'] : $api->download_link;
      $result = $upgrader->install($download_link);

    }
    return ($result === true);
  }

  public function get_userhash(){
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