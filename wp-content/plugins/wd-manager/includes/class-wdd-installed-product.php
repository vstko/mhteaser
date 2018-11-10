<?php

if (!defined('ABSPATH')) {
  exit;
}

class WDDInstalledProduct extends WDDProduct {

  public $subscr_date = "";
  public $item_name = "";
  public $price3 = "";
  public $recurring = 0;
  public $sub_ids = array();
  public $txn_id = '';
  public $update = null;
  public $not_this_user = false;
  public $plugin_wordpress_slug = null;//only for plugins
  public $is_active = false;
  public $version = null;
  public $plan_type = null;

  private $has_update = null;
  private $changelog_data = null;

  private static $pluginsToUpdate = null;
  private static $themesToUpdate = null;

  private static $addonsUpdates = array();
  private static $addonsUpdatesAvailable = array();
  private static $pluginsUpdatesAvailable = array();
  private static $themesUpdatesAvailable = array();
  private static $plans = array(
    /* single */
    'Personal', 'Developer', 'Business',
    /* bundles */
    'Starter', 'Advanced', 'Premium', 'VIP',
  );


  public function __construct($is_plugin, $slug, $id, $title, $description, $zip_name, $with_addons,
                              $parent_id, $logo, $demo_link, $ordering, $version) {

    parent::__construct(
      $is_plugin,
      $slug,
      $id,
      $title,
      $description,
      $zip_name,
      $with_addons,
      $parent_id,
      $logo,
      $demo_link,
      $ordering
    );
    $this->version = $version;

  }

 

  public function get_changelog_data() {
    return $this->changelog_data;
  }


  public function has_update() {
    if ($this->has_update != null) {
      return $this->has_update;
    }

    if ($this->is_plugin()) {
      $this->has_plugin_update();
    } else {
      $this->has_theme_update();
    }

    if ($this->has_update == null) {
      $this->has_update = false;
    }

    return $this->has_update;
  }

  private function has_plugin_update() {
    if (self::$pluginsToUpdate === null || self::$pluginsToUpdate == false) {
      self::$pluginsToUpdate = get_site_transient('update_plugins');

    }

    $updates = array();

    $min_version_for_update = array(
      array(
        'slug' => 'form-maker/form-maker.php',
        'version' => '2.7.0'
      )
    );

    if ($this->is_pro === true || $this->id == WDD_ID) {

      foreach($min_version_for_update as $item) {
        if($item['slug'] === $this->slug){
          if(version_compare($this->version, $item['version'], '<')){
            $this->update = null;
            $this->has_update = false;
            return;
          }
        }
      }

      if (!empty($this->update) && is_array($this->update)) {
        foreach ($this->update as $index => $update) {

          if (empty($update)) {
            continue;
          }

          if (version_compare($this->version, $update['version'], '<')) {
            $updates[] = $update;
            $this->available_update = $this->update;
          }

        }

        $this->update = null;

        if (!empty($updates)) {
          if ($this->parent_id != 0) {

            self::$addonsUpdatesAvailable[$this->slug] = $updates;
            if (isset(self::$addonsUpdates[$this->parent_id])) {
              self::$addonsUpdates[$this->parent_id]++;
            } else {
              self::$addonsUpdates[$this->parent_id] = 1;
            }
          } else {
            self::$pluginsUpdatesAvailable[$this->slug] = $updates;
          }
          $this->changelog_data = $updates;
          $this->has_update = true;
        }
      }

    } else {

      if (isset(self::$pluginsToUpdate->response) && is_array(self::$pluginsToUpdate->response)) {
        if (array_key_exists($this->slug, self::$pluginsToUpdate->response) !== false) {
          $this->available_update = true;
          $this->has_update = true;

          if ($this->parent_id == 0) {
            self::$pluginsUpdatesAvailable[$this->slug] = 1;
          }

        }
      }

    }
  }

  private function has_theme_update() {
    if (self::$themesToUpdate === null) {
      self::$themesToUpdate = get_site_transient('update_themes');
    }

    $updates = array();

    if ($this->is_pro === true) {

      if (!empty($this->update) && is_array($this->update)) {
        foreach ($this->update as $index => $update) {

          if (empty($update)) {
            continue;
          }

          if (version_compare($this->version, $update['version'], '<')) {
            $updates[] = $update;
            $this->available_update = $this->update;
          }

        }

        $this->update = null;

        if (!empty($updates)) {
          self::$themesUpdatesAvailable[$this->slug] = $updates;
          $this->has_update = true;
          $this->changelog_data = $updates;
        }
      }


    } else {

      if (isset(self::$themesToUpdate->response) && is_array(self::$themesToUpdate->response)) {
        if (array_key_exists($this->slug, self::$themesToUpdate->response) !== false) {
          $this->available_update = true;

          if ($this->parent_id == 0) {//TODO theme addon?
            self::$themesUpdatesAvailable[$this->slug] = 1;
            $this->has_update = true;
            $this->changelog_data = $updates;
          }

        }
      }

    }
  }

  public function get_updates() {
    ////todo  $pluginsUpdatesAvailable[$this->id] bug here with index, is update an object
    return array();
  }

  public function set_subscr_date($subscr_date) {
    $this->subscr_date = $subscr_date;
  }

  public function set_item_name($item_name) {
    $this->item_name = $item_name;
  }

  public function set_price3($price3) {
    $this->price3 = $price3;
  }

  public function set_recurring($recurring) {
    $this->recurring = $recurring;
  }

  public function set_sub_ids($sub_ids) {
    $this->sub_ids = $sub_ids;
  }

  public function set_txn_id($txn_id) {
    $this->txn_id = $txn_id;
  }

  public function set_update($update) {
    $this->update = $update;
  }

  public function set_not_this_user($not_this_user) {
    $this->not_this_user = $not_this_user;
  }

  public function set_plugin_wordpress_slug($plugin_wordpress_slug) {
    $this->plugin_wordpress_slug = $plugin_wordpress_slug;
  }

  public function set_is_active($is_active) {
    $this->is_active = ($is_active == true) ? true : false;
  }

  public function set_plan_type() {

    $plan_class = '';

    if (isset($this->item_name)) {
      foreach (self::$plans as $plan_group) {
        if (stripos($this->item_name, $plan_group) !== false) {
          $plan_class = $plan_group;
          break;
        }
      }
    }

    $this->plan_type = $plan_class;
  }

  public function is_active() {
    return $this->is_active;
  }


  public static function getUpdatesCount() {

    $manager_has_update = array_key_exists('wd-manager/wd-manager.php', self::$pluginsUpdatesAvailable) ? 1 : 0;
    return array(
      'plugins' => sizeof(self::$pluginsUpdatesAvailable) - $manager_has_update,
      'addons' => self::$addonsUpdates,
      'themes' => sizeof(self::$themesUpdatesAvailable),
      'manager' => $manager_has_update
    );

  }

  public static function getAddonsUpdatesAvailable() {
    return self::$addonsUpdatesAvailable;
  }

  public static function getPluginsUpdatesAvailable() {
    return self::$pluginsUpdatesAvailable;
  }

  public static function getThemesUpdatesAvailable() {
    return self::$themesUpdatesAvailable;
  }

}