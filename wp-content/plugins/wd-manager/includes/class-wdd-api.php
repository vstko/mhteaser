<?php
if (!defined('ABSPATH')) {
  exit;
}

class WDDApi {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////

  public $userhash = array();
  private $reason_info = false;

  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct() {
    $this->userhash = get_site_option("wdd_user_hash");

  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  /**
   *  return array-decoded JSON or false or null in case of errors
   *
   **/

  public function get_remote_data($id) {

    if ($id == "") {
      $id = "0:0";
    }
    $remote_data_path = WDD_API_PLUGIN_DATA_PATH . '/' . $this->userhash;
    if($this->reason_info){
      $remote_data_path = $remote_data_path."?reason=".$this->reason_info;
    }
    $request_url = str_replace('_id_', $id, $remote_data_path);
    $request = wp_remote_get($request_url);
    if(isset($_GET['wdd_debug']) && $_GET['wdd_debug'] == '1'){
        WDD::set_notices(null, array($request_url), false);
    }

    if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
      set_site_transient('wdd_error_on_request', '0', 12 * 60 * 60);
			return json_decode($request['body'], true);
    }else{
      set_site_transient('wdd_error_on_request', '1', 12 * 60 * 60);
    }

    if(is_wp_error($request)){
      WDD::set_notices('wdd_api_resposne', $request->get_error_messages(), false);
    }

    return false;
  }

  /*deprecated */
  /*
          public function get_userhash(){
              $userhash = 'nohash';
              $user_id = isset( $_POST["user_id"] ) ? $_POST["user_id"] : 0;
              if($user_id){
                $request = wp_remote_get(  str_replace( '_user_', $user_id, WDD_API_UAER_HASH_PATH ) );

                if ( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200 ) {
                  $body = json_decode($request['body'], true);
                  $userhash = $body["body"];
                }
              }
              return $userhash;
          }*/

  private function check_existing_file($path) {
    if (file_exists(WP_CONTENT_DIR . "/" . $path . '/.keep')) {
      return true;
    }
    return false;
  }

  public function get_products_data() {

    $installed_themes = $this->get_installed_themes_slugs();
    $themes_ids = array();
    $plugins_ids = array();

    foreach ($installed_themes as $installed_theme) {
      $themes_ids[] = $installed_theme["slug_version"];
    }

    $installed_plugins = $this->get_installed_plugins_slugs();
    foreach ($installed_plugins as $installed_plugin) {
      $plugins_ids[] = $installed_plugin["slug_version"];
    }

    /*Get remote data*/
    $versions = array();
    $k = 0;
    while (get_site_transient($k . "wdd_remote_versions_data")) {
      $data = get_site_transient($k . "wdd_remote_versions_data");
      $versions = $versions + json_decode($data, true);
      $k++;
    }

    $agreements = get_site_transient('wdd_remote_agreements_data');
    $wd_themes = get_site_transient('wdd_remote_themes_data');
    $wd_plugins = get_site_transient('wdd_remote_plugins_data');
    $coupons = get_site_transient('wdd_remote_coupons_data');
    $offers_date = get_site_transient('wdd_remote_offers_date');

    $agreements = $agreements ? $agreements : array();
    $wd_themes = $wd_themes ? $wd_themes : array();
    $wd_plugins = $wd_plugins ? $wd_plugins : array();
    $coupons = $coupons ? $coupons : array();
    $offers_date = $offers_date ? $offers_date : '';

    $wdd_notices = get_site_option('wdd_notices');
    $get_data_form_api = (
      (
        false === $agreements ||
        false === $wd_themes ||
        false === $wd_plugins ||
        false === $coupons ||
        count($versions) === 0
      ) &&
      get_site_transient('wdd_error_on_request') != '1'
    );

    /* if there is a reason to reload a list*/
    if ((isset($_REQUEST["wdd_logged_in"]) && $_REQUEST["wdd_logged_in"] == "1")
      || (!empty($wdd_notices)) /// after action
      || isset($_POST["wdd_refresh_button"])
      || $get_data_form_api
    ) {
      $wdd_logged_in = false;
      if ((isset($_REQUEST["wdd_logged_in"]) && $_REQUEST["wdd_logged_in"] == "1")){
        $wdd_logged_in = true;
      }

      $reason_data = array(
        "agreement"=>false === $agreements,
        "themes"=>false === $wd_themes,
        "plugins"=>false === $wd_plugins,
        "coupons" =>false === $coupons,
        "count_versions" => count($versions) === 0,
        "logged_in" => $wdd_logged_in,
        "notices" => !empty($wdd_notices),
        "refresh_button" => isset($_POST["wdd_refresh_button"]),
        "data_form_api" => $get_data_form_api,
      );
      $this->reason_info = $this->add_reason($reason_data);
      $ids = array_merge($themes_ids, $plugins_ids);
      $installed_str = implode("_", $ids);
      $user_data = $this->get_remote_data($installed_str);

      $user_data_array = is_array($user_data);
      /*wrong data should not reset the list of products*/
      if ($user_data_array && !empty($user_data)) {
        $agreements = array();
        $versions = array();
        $wd_themes = array();
        $wd_plugins = array();
        $coupons = array();
        $offers_date = '';

        /* exclude gallery-ecommerce */
        unset($user_data['body']['products']['plugins']['gallery-ecommerce+gallery-ecommerce.php']);

        if (isset($user_data['body']) && count($user_data['body'])) {
          $agreements = $user_data['body']['agreements'];
          $versions = $user_data['body']['versions'];
          $wd_themes = $user_data['body']['products']['themes'];
          $wd_plugins = $user_data['body']['products']['plugins'];
          $coupons = $user_data['body']['coupons'];
          $offers_date = $user_data['body']['offers_date'];
        } elseif (isset($user_data) && count($user_data)) {
          $agreements = $user_data['agreements'];
          $versions = $user_data['versions'];
          $wd_themes = $user_data['products']['themes'];
          $wd_plugins = $user_data['products']['plugins'];
          $coupons = $user_data['coupons'];
          $offers_date = $user_data['offers_date'];
        }
        $options_count = ceil(count($versions) / 10);
        for ($i = 0; $i < $options_count; $i++) {
          $_user_data = array_slice($versions, $i * 10, 10, true);
          set_site_transient($i . "wdd_remote_versions_data", json_encode($_user_data), 12 * 60 * 60);
        }
        set_site_transient('wdd_remote_agreements_data', $agreements, 12 * 60 * 60);
        set_site_transient('wdd_remote_themes_data', $wd_themes, 12 * 60 * 60);
        set_site_transient('wdd_remote_plugins_data', $wd_plugins, 12 * 60 * 60);
        set_site_transient('wdd_remote_coupons_data', $coupons, 12 * 60 * 60);
        set_site_transient('wdd_remote_offers_date', $offers_date, 12 * 60 * 60);

        $server_time = isset($user_data['server_time']) ? $user_data['server_time'] : date('Y-m-d H:i:s');
        update_site_option('wdd_server_time_diff', (strtotime($server_time) - strtotime(date('Y-m-d H:i:s'))));
      }

    }
		

    $result = array(
      'agreements' => $agreements,
      'wd_themes' => $wd_themes,
      'wd_plugins' => $wd_plugins,
      'coupons' => $coupons,
      'offers_date' => $offers_date,
      'versions' => $versions
    );

    return $result;
  }
  private function add_reason($data){
    $reason = "";
    foreach ($data as $key => $value){
      if($value){
        $reason .= $key.",";
      }
    }
    return $reason;
  }
  private function get_installed_themes_slugs() {
    $installed_themes = array();
    $themes = wp_get_themes();
    foreach ($themes as $key => $theme) {
      if ((strpos($theme->Author, "web-dorado.com") !== false || strpos($theme->Author, "10web") !== false || strpos($theme->Author, "WebDorado") !== false) && $this->check_existing_file("themes/" . $key)) {
        $installed_themes[$key]["slug_version"] = $key . ':' . $theme->get('Version');
      }
    }
    return $installed_themes;
  }

  private function get_installed_plugins_slugs() {
    $installed_plugins = array();
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $all_plugins = get_plugins();
    foreach ($all_plugins as $key => $all_plugin) {
      $slug = substr($key, 0, strpos($key, "/"));
      $valid_webdorado_URI = strpos($all_plugin["AuthorURI"], "web-dorado.com");
      //$valid_codecanyonURI = strpos($all_plugin["AuthorURI"], "https://codecanyon.net/item/wp-grid-gallery-i-wordpress-gallery-plugin/19334904");
      if (($valid_webdorado_URI !== false || strpos($all_plugin["AuthorURI"], "10web") !== false) && ($this->check_existing_file("plugins/" . $slug) || $key == WDD_SLUG)) {
        $installed_plugins[$key]["slug_version"] = str_replace("_", "*", str_replace("/", "+", $key)) . ':' . $all_plugins[$key]['Version'];
      }
    }
    return $installed_plugins;
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
