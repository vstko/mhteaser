<?php
if (!defined('ABSPATH')) {
  exit;
}

class WDDthemes
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
  public function display_themes_page()
  {
    global $wdd_options, $self_update, $update_themes_count;
		$proThemes = array();
		$freeThemes = array();
    $user_hash = $this->get_userhash();
    $site_url = urlencode(get_site_url());

    $availableThemes = WDDProducts::getAvailableThemes();


		$installedThemes = WDDProducts::getInstalledThemes();
    $bundle = WDDProducts::getActiveBundlePlan('themes');

		$update_all_products_count = WDDInstalledProduct::getUpdatesCount();
		$installed_count = WDDProducts::getInstalledThemesCount(false, true);
		$inactive_count = WDDProducts::getInstalledThemesCount(false);
		$active_count = WDDProducts::getInstalledThemesCount(true);
    if(!isset($availableThemes) || $availableThemes == null){
      $availableThemes = array();
    }
		foreach ($availableThemes as $slug => $theme) {
			if ($theme->is_pro) {
				$proThemes[$slug] = $theme;
				if ($theme->is_expired()) {
					$freeThemes[$slug] = $theme;
				}
			}
			else {
				$freeThemes[$slug] = $theme;
			}
		}
    uasort($proThemes, array($this, 'sortByOrdering'));
    uasort($freeThemes, array($this, 'sortByOrdering'));



    /*Install theme*/
    $theme_name = isset($_REQUEST["theme_name"]) ? $_REQUEST["theme_name"] : "";
    $zip_name = isset($_REQUEST["zip_name"]) ? $_REQUEST["zip_name"] : "";




    $all_themes = false;
    require_once(WDD_DIR_TEMPLATES . "/display_themes.php");
  }

  public function upgrade_theme($product, $is_pro){

    require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
    require_once(ABSPATH . 'wp-admin/includes/theme.php');
    //require_once(ABSPATH .'wp-admin/includes/class-plugin-upgrader.php');
    $upgrader = new Theme_Upgrader(new Theme_Installer_Skin(compact('title', 'url', 'nonce', 'plugin', 'api')));

    $fs_options = apply_filters('upgrader_package_options', array('destination' => WP_PLUGIN_DIR));
    if($upgrader->fs_connect($fs_options) !== true) {
      WDD::set_notices('wdd_fs_issue', array(__('File system error. Invalid file permissions or FTP credentials.', WDD_LANG)), false);
      return false;
    }

    /*Delete dir if exists*/

    $theme_folder = str_replace(array('/', "\\"), array('', ''), $product->slug);

    if (!empty($theme_folder) && $theme_folder != '.' && file_exists(WP_CONTENT_DIR . "/themes/" . $theme_folder)) {
      $this->wdd_rmdir(WP_CONTENT_DIR . "/themes/" . $theme_folder);
    }

    if($is_pro == true){
      $result = $upgrader->install($this->download_url . $product->zip_name);
    }else{
      $api = themes_api(
        'theme_information',
        array(
          'slug' => $product->slug,
          'fields' => array('sections' => false, 'tags' => false)
        )
      );

      if (is_wp_error($api)) {
        return false;
      }

      $download_link = (is_array($api)) ? $api['download_link'] : $api->download_link;
      $result = $upgrader->install($download_link);
    }

    return ($result === true);
  }

  private function wdd_rmdir($del_file)
  {
    static $input_file = null;
    if ($input_file == null) {
      $input_file = $del_file;
    }

    if (is_writeable($del_file) === false) {
      $this->theme_delete_error_msg($input_file);
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
        $this->theme_delete_error_msg($input_file);
        return;
      }
      else {
        rmdir($del_file);
      }
    }
    else {
      if (is_writeable($del_file) === false) {
        $this->theme_delete_error_msg($input_file);
        return;
      }
      else {
        unlink($del_file);
      }
    }
  }

  private function theme_delete_error_msg($plugin_dir)
  {
    $theme_dir_basename = basename($plugin_dir);

    $themes = wp_get_themes();

    foreach ($themes as $theme) {

      if ($theme !== $theme_dir_basename) {
        switch_theme($theme);
      }

    }

    $message = array(
      sprintf('Permission error. Cannot delete %s folder or its content. Please delete it manually.', $plugin_dir)
    );
    WDD::set_notices(uniqid(), $message, false);
  }

	private function sortByOrdering($a, $b)
  {
    return $a->ordering - $b->ordering;
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