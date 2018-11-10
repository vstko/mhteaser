<?php

if (!defined('ABSPATH')) {
  exit;
}

/**
 *  static functions to convert API-data to manageable objects for products
 * contains product data
 */
class WDDProducts
{

  private static $agreements = null;
  private static $themesData = null;
  private static $pluginsData = null;
  private static $coupons = null;
  private static $offersDate = null;
  private static $diff = 0;
  private static $versions = null;

  private static $themes = null;
  private static $availableThemes = null;
  private static $installedThemes = null;

  private static $availablePlugins = null;
  private static $installedPlugins = null;

  private static $allProThemes = array();
  private static $allProPlugins = array();
  private static $excludeFromPlugins = array();
  private static $excludeFromThemes = array(77);


  public static function addProductsData($productsData)
  {


    self::$agreements = $productsData['agreements'];
    self::$themesData = $productsData['wd_themes'];
    self::$pluginsData = $productsData['wd_plugins'];


    self::$coupons = $productsData['coupons'];
    self::$offersDate = $productsData['offers_date'];
    self::$versions = $productsData['versions'];

    self::excludeRepeatAgreements();
    self::setThemes();
    self::setPlugins();
    self::saveCoupons();
    self::saveOffers();
    self::saveProProducts();


    self::checkUpdates();
  }

  private static function excludeRepeatAgreements()
  {
    $agreements = self::$agreements;
    if(!isset($agreements) || $agreements==null){
      $agreements = array();
    }
    foreach ($agreements as $key => $value) {

      $first_product_id = $value["product_id"];
      $first_expire_date = strtotime($value["expire_date"]);
			if ($first_expire_date === false) {
				$first_expire_date = strtotime('2037-06-17 17:21:15'); /*< 2038*/
			}

      foreach ($agreements as $key_1 => $value_1) {
        if ($key !== $key_1) {
          if ($value_1["product_id"] === $first_product_id) {
            $expire_date = strtotime($value_1["expire_date"]);

            if ($expire_date === false) {
              $expire_date = strtotime('2037-06-17 17:21:15'); /*< 2038*/
            }

            if ($first_expire_date > $expire_date) {
              unset($agreements[$key_1]);
            }
            else {
              unset($agreements[$key]);
            }
          }
        }
      }

    }

    self::$agreements = $agreements;
  }

  private static function setPlugins()
  {
    $installedPlugins = array();
    $availablePlugins = array();
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $all_plugins = get_plugins();


    if (is_array(self::$pluginsData) && !empty(self::$pluginsData)) {

      foreach (self::$pluginsData as $key => $wdPlugin) {
				if(in_array($wdPlugin['id'], self::$excludeFromPlugins)){
					continue;
				}
        /*replace + widt / and * widt _ in  key*/
        unset(self::$pluginsData[$key]);
        $newKey = str_replace("*", "_", str_replace("+", "/", $key));
        //$wd_plugins[$newKey] = $wdPlugin;

        $availablePlugins[$newKey] = $wdPlugin;
        if (array_key_exists($newKey, $all_plugins) !== false) {
          if ($wdPlugin) {
            if ($wdPlugin['id'] > 0) {
              $installedPlugins[$newKey] = $wdPlugin;
              $installedPlugins[$newKey]["slug_version"] = substr($newKey, 0, strpos($newKey, "/")) . ':' . $all_plugins[$newKey]['Version'];
              $installedPlugins[$newKey]["version"] = $all_plugins[$newKey]['Version'];
              if (is_plugin_active($newKey)) {
                $installedPlugins[$newKey]["active"] = true;
              }
            }
          }
        }

      }
    }


    self::setInstalledPlugins($installedPlugins, $availablePlugins);
    self::setAvailablePlugins($availablePlugins);

    //remove contact form maker if form maker pro buyed and not expired
    self::filterFormMakerPlugins();

    self::$themes = array(//TODO
      'installed_plugins' => self::$installedPlugins,
      'avalible_plugins' => self::$availablePlugins
    );

  }

  private static function setInstalledPlugins($installedPluginsData, &$availablePluginsData)
  {

    $installed_plugins = array();

    if (empty($installedPluginsData)) {
      self::$installedPlugins = $installed_plugins;
      return;
    }


    foreach ($installedPluginsData as $key => $pluginData) {
      $exists_in_install = 0;
      $slug = substr($key, 0, strpos($key, "/"));

      $plugin = new WDDInstalledProduct(
        true,
        $key,
        $pluginData['id'],
        $pluginData['title'],
        $pluginData['description'],
        $pluginData['zip_name'],
        $pluginData['with_addons'],
        (isset($pluginData['parent_id'])) ? $pluginData['parent_id'] : 0,
        $pluginData['logo'],
        $pluginData['demo_link'],
        $pluginData['ordering'],
        $pluginData['version']
      );

      if (isset($pluginData['active'])) {
        $plugin->set_is_active($pluginData['active']);
      }			
      foreach (self::$agreements as $agreement) {
        if ($pluginData['id'] == $agreement["product_id"]) {
          $plugin->set_expire_date($agreement["expire_date"]);										
          if (self::isFileExists("plugins/" . $slug)) {


            self::addProPlugin($key, $plugin->zip_name);

            $plugin->set_is_pro(true);
            $plugin->set_subscr_date($agreement["subscr_date"]);
            $plugin->set_item_name($agreement["item_name"]);
            $plugin->set_price3($agreement["price3"]);
            $plugin->set_subr_id($agreement["subr_id"]);
            $plugin->set_recurring($agreement["recurring"]);
            $plugin->set_sub_ids($agreement["sub_ids"]);
            $plugin->set_txn_id($agreement["txn_id"]);
            $plugin->set_plan_type();

            $availablePluginsData[$key]['installed_pro'] = true;
          }
          else {

            
            $plugin->set_is_buy(true);
          }
          $exists_in_install++;
        }

        $plugin->set_plugin_wordpress_slug(substr($key, 0, stripos($key, "/")));
        if (isset(self::$versions[$plugin->id])) {
          $plugin->set_update(self::$versions[$plugin->id]);
        }

      }
			
			if($plugin->id == WDD_ID && isset(self::$versions[$plugin->id])){
				$plugin->set_update(self::$versions[$plugin->id]);				
			}

      if ($plugin->is_pro === false && self::isFileExists("plugins/" . $slug)) {
        $plugin->set_is_pro(true);
        $plugin->set_not_this_user(true);
        unset($availablePluginsData[$key]);
      }

      if ($exists_in_install == 0 && !self::isFileExists("plugins/" . $slug)) {
        unset($availablePluginsData[$key]);
      }

      $installed_plugins[$key] = $plugin;

    }

    uasort($installed_plugins, array('WDDProducts', 'sortByPro'));

    self::$installedPlugins = $installed_plugins;
  }

  private static function setAvailablePlugins($availablePluginsData = array())
  {

    $availablePlugins = array();

    if (empty($availablePluginsData)) {
      self::$availablePlugins = $availablePlugins;
      return;
    }

    foreach ($availablePluginsData as $key => $pluginData) {
      $exists = 0;
      $slug = substr($key, 0, strpos($key, "/"));

      $plugin = new WDDProduct(
        false,
        $key,
        $pluginData['id'],
        $pluginData['title'],
        $pluginData['description'],
        $pluginData['zip_name'],
        $pluginData['with_addons'],
        (isset($pluginData['parent_id'])) ? $pluginData['parent_id'] : 0,
        $pluginData['logo'],
        $pluginData['demo_link'],
        $pluginData['ordering']
      );

      if (isset($pluginData['installed_pro'])) {
        $plugin->set_installed_pro($pluginData['installed_pro']);
      }
      else {
        $plugin->set_installed_pro(null);
      }


      foreach (self::$agreements as $agreement) {
        if ($pluginData['id'] == $agreement["product_id"]) {
          $plugin->set_is_pro(true);
          $plugin->set_expire_date($agreement["expire_date"]);
          $plugin->set_subr_id($agreement["subr_id"]);

          if (self::isFileExists("plugins/" . $slug)) {
            self::addProPlugin($key, $plugin->zip_name);
          }

          if ($plugin->parent_id != 0) {
            $plugin->set_is_buy(true);
          }
          $exists++;
        }
      }

      if ($exists == 0 && $plugin->parent_id != 0) {
        $plugin->set_is_buy(false);
      }
      $availablePlugins[$key] = $plugin;
    }

    uasort($availablePlugins, array('WDDProducts', 'sortByPro'));
    self::$availablePlugins = $availablePlugins;
  }

  private static function filterFormMakerPlugins() {

    $contact_form_slug = 'contact-form-maker/contact-form-maker.php';
    $form_slug = 'form-maker/form-maker.php';

    $contact_form_maker = self::getPluginBySlug($contact_form_slug);
    //not exists
    if($contact_form_maker == false){
      return;
    }
    
    $contact_form_maker->set_has_pro(false);

    //if contact form maker not installed
    if (isset(self::$availablePlugins[$form_slug])) {

      if (self::$availablePlugins[$form_slug]->is_pro && !self::$availablePlugins[$form_slug]->is_expired()) {
        if (isset(self::$availablePlugins[$contact_form_slug])) {
          unset(self::$availablePlugins[$contact_form_slug]);
        }
      }

    }
    
  }

  private static function setThemes()
  {

    $installedThemes = array();
    $availableThemes = array();
    $activeTheme = wp_get_theme();

    if (is_array(self::$themesData) && !empty(self::$themesData)) {

      foreach (self::$themesData as $key => $wdTheme) {
        if(in_array($wdTheme['id'], self::$excludeFromThemes)){
					continue;
				}
        $availableThemes[$key] = $wdTheme;
        $theme = wp_get_theme($key);
        if ($theme->exists()) {
          $installedThemes[$key] = $wdTheme;
          $installedThemes[$key]["slug_version"] = $key . ':' . $theme->get('Version');
          $installedThemes[$key]["version"] = $theme->get('Version');

          //TODO active theme check for network
          if (str_replace(" Theme", "", $wdTheme["title"]) == $activeTheme["Name"]) {
            $installedThemes[$key]["active"] = true;
          }
        }

      }

    }


    self::setInstalledThemes($installedThemes, $availableThemes);
    self::setAvailableThemes($availableThemes);
    self::$themes = array(
      'installed_themes' => self::$installedThemes,
      'avalible_themes' => self::$availableThemes
    );
  }

  private static function setInstalledThemes($installedThemesData = array(), &$availableThemes = array())
  {
    $installedThemes = array();

    if (empty($installedThemesData)) {
      self::$installedThemes = $installedThemes;
      return;
    }


    foreach ($installedThemesData as $themeSlug => $themeData) {
      $has_agreements = 0;

      $theme = new WDDInstalledProduct(
        false,
        $themeSlug,
        $themeData['id'],
        $themeData['title'],
        $themeData['description'],
        $themeData['zip_name'],
        $themeData['with_addons'],
        (isset($themeData['parent_id'])) ? $themeData['parent_id'] : 0,
        $themeData['logo'],
        $themeData['demo_link'],
        $themeData['ordering'],
        $themeData['version']
      );

      if (isset($themeData['active'])) {
        $theme->set_is_active($themeData['active']);
      }

      foreach (self::$agreements as $agreement) {

        if ($themeData['id'] == $agreement["product_id"]) {
          $theme->set_expire_date($agreement["expire_date"]);
          if (self::isFileExists("themes/" . $themeSlug)) {

            $theme->set_is_pro(true);
            $theme->set_subr_id($agreement["subr_id"]);
            $theme->set_subscr_date($agreement["subscr_date"]);
            $theme->set_item_name($agreement["item_name"]);
            $theme->set_price3($agreement["price3"]);
            $theme->set_recurring($agreement["recurring"]);
            $theme->set_sub_ids($agreement["sub_ids"]);
            $theme->set_txn_id($agreement["txn_id"]);
            $availableThemes[$themeSlug]['installed_pro'] = true;

            self::addProTheme($themeSlug, $theme->zip_name);

          }
          else {
            $theme->set_is_buy(true);
          }

          $has_agreements++;
        }
        if (isset(self::$versions[$themeData["id"]])) {
          $theme->set_update(self::$versions[$themeData["id"]]);
        }

      }


      if (!$theme->is_pro() && self::isFileExists("themes/" . $themeSlug)) {
        $theme->set_is_pro(true);
        $theme->set_not_this_user(true);
        unset($availableThemes[$themeSlug]);
      }

      if ($has_agreements == 0 && !self::isFileExists("themes/" . $themeSlug)) {
        unset($availableThemes[$themeSlug]);
      }

      $installedThemes[$themeSlug] = $theme;


    }

    uasort($installedThemes, array('WDDProducts', 'sortByPro'));
    self::$installedThemes = $installedThemes;
  }

  private static function setAvailableThemes($availableThemesData = array())
  {


    $availableThemes = array();

    if (empty($availableThemesData)) {
      self::$availableThemes = $availableThemes;
      return;
    }

    foreach ($availableThemesData as $themeSlug => $themeData) {

      $theme = new WDDProduct(
        false,
        $themeSlug,
        $themeData['id'],
        $themeData['title'],
        $themeData['description'],
        $themeData['zip_name'],
        $themeData['with_addons'],
        (isset($themeData['parent_id'])) ? $themeData['parent_id'] : 0,
        $themeData['logo'],
        $themeData['demo_link'],
        $themeData['ordering']
      );

      if (isset($themeData['installed_pro'])) {
        $theme->set_installed_pro($themeData['installed_pro']);
      }
      else {
        $theme->set_installed_pro(null);
      }


      foreach (self::$agreements as $agreement) {
        if ($themeData['id'] == $agreement["product_id"]) {
          $theme->set_is_pro(true);
          $theme->set_subr_id($agreement["subr_id"]);
          $theme->set_expire_date($agreement["expire_date"]);

          if (self::isFileExists("themes/" . $themeSlug)) {
            self::addProTheme($themeSlug, $theme->zip_name);
          }
        }
      }

      $availableThemes[$themeSlug] = $theme;
    }
    uasort($availableThemes, array('WDDProducts', 'sortByPro'));
    self::$availableThemes = $availableThemes;
  }

  public static function saveCoupons()
  {

    $coupons_new = self::$coupons;
    /*Coupon gift*/
    self::$diff = 0;

    if (get_site_option("wdd_coupons") !== false) {
      $old = json_decode(get_site_option("wdd_coupons"), true);
      if (count($coupons_new)) {

        $old_codes = array();
        $new_codes = array();

        foreach ($old as $coupon) {
          if(isset($coupon["coupon_code"])){
            array_push($old_codes, $coupon["coupon_code"]);

          }
        }
        foreach ($coupons_new as $coupon) {
          if(isset($coupon["coupon_code"])){
            array_push($new_codes, $coupon["coupon_code"]);

          }
        }

        $coupons_diff = array_diff($new_codes, $old_codes);


        if(count($coupons_diff)){
          self::$diff++;
        }


      }
    }
    else {
      if (count(self::$coupons)) {
        add_site_option("wdd_coupons", json_encode(self::$coupons));
        self::$diff = 1;
      }
    }

  }

  public static function saveOffers()
  {
    $offers_date_diff = self::$offersDate;
    if (get_site_option("wdd_offers_date") !== false) {
      $old = get_site_option("wdd_offers_date");


      if (strtotime($old) !== false && strtotime($offers_date_diff) !== false) {
        if (strtotime($old) < strtotime($offers_date_diff)) {
          self::$diff = 1;
        }
      }
    }
    else {
      add_site_option("wdd_offers_date", self::$offersDate);
      self::$diff = 1;
    }
  }

  private static function sortByPro($a, $b)
  {
    return $b->is_pro - $a->is_pro;
  }

  private static function addProTheme($themeSlug, $zipName)
  {
    if (!isset(self::$allProThemes[$themeSlug])) {
      self::$allProThemes[$themeSlug] = array();
    }
    self::$allProThemes[$themeSlug]['zip_name'] = $zipName;
  }

  private static function addProPlugin($pluginSlug, $zipName)
  {
    if (!isset(self::$allProPlugins[$pluginSlug])) {
      self::$allProPlugins[$pluginSlug] = array();
    }
    self::$allProPlugins[$pluginSlug]['zip_name'] = $zipName;
  }

  private static function saveProProducts()
  {
    self::$allProPlugins[WDD_SLUG]['zip_name'] = WDD_ZIP_NAME;
    update_site_option("wdd_all_pro_plugins", self::$allProPlugins);
    update_site_option("wdd_all_pro_themes", self::$allProThemes);
  }

  private static function isFileExists($path)
  {
    if (file_exists(WP_CONTENT_DIR . "/" . $path . '/.keep')) {
      return true;
    }
    return false;
  }


  private static function checkUpdates()
  {
    if (!empty(self::$installedPlugins)) {

      foreach (self::$installedPlugins as $slug => $installed_plugin) {
        /// todo check
        self::$installedPlugins[$slug]->has_update();

      }
    }
    
    if (!empty(self::$installedThemes)) {
      foreach (self::$installedThemes as $slug => $installed_theme) {
        /// todo check
        self::$installedThemes[$slug]->has_update();

      }
    }
  }

  public static function getInstalledPlugins()
  {
    return self::$installedPlugins;
  }

  public static function getAvailablePlugins()
  {
    return self::$availablePlugins;
  }

  public static function getInstalledThemes()
  {
    return self::$installedThemes;

  }

  public static function getAvailableThemes()
  {
    return self::$availableThemes;
  }

  public static function getAllProPlugins()
  {
    return self::$allProPlugins;
  }

  /**
   * @return WDDproduct object either from the list of installed or available plugins
   * @return false if plugin not found
   * @param group = '', 'installed' or 'available' , empty string means search in both groups
   */

  public static function getPluginByID($id, $group = '')
  {

    $product_found = false;

    if ($group == '' || $group == 'installed') {
      foreach (self::$installedPlugins as $slug => $product) {
        if ($product->id == $id) {
          $product_found = $product;
          break;
        }
      }
    }
    if ($group == '' || $group == 'available') {
      foreach (self::$availablePlugins as $slug => $product) {
        if ($product->id == $id) {
          $product_found = $product;
          break;
        }
      }
    }

    return $product_found;
  }


  /**
   * @return WDDproduct object either from the list of installed or available plugins
   * @return false if plugin not found
   * @param group = '', 'installed' or 'available' , empty string means search in both groups
   */

  public static function getThemeByID($id, $group = '')
  {

    $product_found = false;

    if ($group == '' || $group == 'installed') {
      foreach (self::$installedThemes as $slug => $product) {
        if ($product->id == $id) {
          $product_found = $product;
          break;
        }
      }
    }
    if ($group == '' || $group == 'available') {
      foreach (self::$availableThemes as $slug => $product) {
        if ($product->id == $id) {
          $product_found = $product;
          break;
        }
      }
    }

    return $product_found;
  }

  /**
   * @return WDDproduct object either from the list of installed or available plugins
   * @return false if plugin not found
   * @param group = '', 'installed' or 'available' , empty string means search in both groups
   */

  public static function getPluginBySlug($slug1, $group = '')
  {

    $product_found = false;

    if ($group == '' || $group == 'installed') {
      foreach (self::$installedPlugins as $slug => $product) {
        if ($slug == $slug1) {
          $product_found = $product;
          break;
        }
      }
    }
    if ($group == '' || $group == 'available') {
      foreach (self::$availablePlugins as $slug => $product) {
        if ($slug == $slug1) {
          $product_found = $product;
          break;
        }
      }
    }

    return $product_found;
  }


  /**
   * @return WDDproduct object either from the list of installed or available plugins
   * @return false if plugin not found
   * @param group = '', 'installed' or 'available' , empty string means search in both groups
   */

  public static function getThemeBySlug($slug1, $group = '')
  {

    $product_found = false;

    if ($group == '' || $group == 'installed') {
      foreach (self::$installedThemes as $slug => $product) {
        if ($slug == $slug1) {
          $product_found = $product;
          break;
        }
      }
    }
    if ($group == '' || $group == 'available') {
      foreach (self::$availableThemes as $slug => $product) {
        if ($slug == $slug1) {
          $product_found = $product;
          break;
        }
      }
    }

    return $product_found;
  }


  public static function getInstalledPluginsCount($active = false, $parent_id = 0, $all = false)
  {
    $plugins_count = 0;
    foreach (self::$installedPlugins as $plugin) {
      if ($parent_id == $plugin->parent_id && $plugin->id != WDD_ID) { /*exclude manager*/
        if ($all) {
          $plugins_count++;
        }
        else {
          if ($active && $plugin->is_active())
            $plugins_count++;
          elseif (!$active && !$plugin->is_active())
            $plugins_count++;
        }
      }
    }
    return $plugins_count;
  }

  public static function getInstalledThemesCount($active = false, $all = false)
  {
    $themes_count = 0;
    foreach (self::$installedThemes as $theme) {
      if ($all) {
        $themes_count++;
      }
      else {
        if ($active && $theme->is_active())
          $themes_count++;
        elseif (!$active && !$theme->is_active())
          $themes_count++;
      }
    }
    return $themes_count;

  }

  /*
   *  @param string $type = 'plugins', 'themes', or 'addons'
   *  @param int $parent_id = id of plugin for in case of addons bundle
   *
   * */


  public static function getActiveBundlePlan($items = 'plugins', $parent_id = 0)
  {
    /* TODO check if expired bundle*/

    $plan_id = 0;

    switch ($items) {
      case 'plugins';

        $all_plugins_subr_ids = array(117, 251, 409, 416);
        foreach (array(self::$installedPlugins, self::$availablePlugins) as $group) {
          foreach ($group as $plugin) {
            /* only not expired plugins */
            if (!$plugin->is_addon() && !$plugin->is_expired()) {

              if (in_array($plugin->subr_id, $all_plugins_subr_ids)) {
                $plan_id = $plugin->subr_id;
                break(2);
              }
            }

          }
        }


        break;
      case 'themes':

        $all_themes_subr_ids = array(98, 418, 420, 422);
        foreach (array(self::$installedThemes, self::$availableThemes) as $group) {
          foreach ($group as $plugin) {
            if (in_array($plugin->subr_id, $all_themes_subr_ids)) {
              $plan_id = $plugin->subr_id;
              break(2);
            }
          }
        }

        break;
      case 'addons':
        $all_addons_subr_ids = array();

        if ($parent_id == 86)
          $all_addons_subr_ids = array(247, 409, 416);
        elseif ($parent_id == 31)
          $all_addons_subr_ids = array(249, 409, 416);

        foreach (array(self::$installedPlugins, self::$availablePlugins) as $group) {
          foreach ($group as $plugin) {
            /* only not expired addons */

            if ($plugin->is_addon() && !$plugin->is_expired() && ($plugin->parent_id === $parent_id)) {
              if (in_array($plugin->subr_id, $all_addons_subr_ids)) {
                $plan_id = $plugin->subr_id;
                break(2);
              }
            }

          }
        }

        break;
      default:
        break;
    }

    return $plan_id;

  }


  public static function getPlanNamebyID($id)
  {

    $plans = array(
      /* plugins */
      '117' => 'Starter',
      '251' => 'Advanced',
      '409' => 'Premium',
      '416' => 'VIP',
      /* themes */
      '98' => 'Starter',
      '418' => 'Advanced',
      '420' => 'Premium',
      '422' => 'VIP',
      /* addons */
      /*ECWD*/
      '247' => 'Starter',
      /* FM */
      '249' => 'Starter',
    );

    $id = (string)$id;

    return isset($plans[$id]) ? $plans[$id] : '';


  }

  public static function getSingleProductPlanClass($product)
  {

    $plans = array(
      /* single */
      'Personal', 'Developer', 'Business',
      /* bundles */
      'Starter', 'Advanced', 'Premium', 'VIP',
    );
    $plan_class = '';

    if (isset($product->item_name)) {
      foreach ($plans as $plan_group) {
        if (stripos($product->item_name, $plan_group) !== false) {
          $plan_class = $plan_group;
          break;
        }
      }
    }
    return $plan_class;
  }

  public static function getCoupons()
  {
    return self::$coupons;
  }

  public static function getDiff()
  {
    return self::$diff;
  }

  public static function getOffersDate()
  {
    return self::$offersDate;
  }

  /*
   * $plugin WDDProduct class object
   * */
  public static function getPluginActivationLink($plugin){
    $activation_link = null;
    if($plugin->is_plugin() === true){
      $activation_link = 'plugins.php?action=activate&plugin=' . $plugin->slug;
      $activation_link = esc_url(wp_nonce_url(self_admin_url($activation_link), 'activate-plugin_' . $plugin->slug));
    }

    return $activation_link;
  }

  public static function getUpdatesCount()
  {
    return WDDInstalledProduct::getUpdatesCount();
  }

}