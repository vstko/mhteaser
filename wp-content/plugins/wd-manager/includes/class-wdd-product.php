<?php

if (!defined('ABSPATH')) {
  exit;
}

class WDDProduct
{

  protected $is_plugin = true;//true if plugin, false if theme
  protected $is_expired = null;
  protected $is_purchased = null;

  public $id = 0;
  public $slug = '';
  public $title = "";
  public $description = "";
  public $zip_name = "";
  public $with_addons = 0;
  public $parent_id = 0;
  public $logo = '';
  public $demo_link = '';
  public $ordering = 0;
  public $is_pro = false;
  public $subr_id = 0;
  public $expire_date = null;
  public $is_buy = false;
  public $installed_pro = false;
  public $available_update = false;
  public $has_pro = true;//has paid version

  private static $server_time_diff = null;

  public function __construct($is_plugin, $slug, $id, $title, $description, $zip_name, $with_addons,
                              $parent_id, $logo, $demo_link, $ordering)
  {

    $this->is_plugin = $is_plugin;
    $this->slug = $slug;
    $this->id = $id;
    $this->title = $title;
    $this->description = $description;
    $this->zip_name = $zip_name;
    $this->with_addons = $with_addons;
    $this->parent_id = $parent_id;
    $this->logo = $logo;
    $this->demo_link = $demo_link;
    $this->ordering = $ordering;
  }

  public function is_expired()
  {



    if ($this->is_expired !== null ) {
      return $this->is_expired;
    }


    if ($this->expire_date !== null) {

      if (self::$server_time_diff === null) {
        self::$server_time_diff = intval(get_site_option('wdd_server_time_diff'));
      }

      $diff = strtotime($this->expire_date) - (time() + self::$server_time_diff);


      $expired = ($diff < 0);
      //$expired = (ceil($diff / (60 * 60 * 24)) < 0);
    }
    else {

      $expired = false;
    }

    $this->is_expired = $expired;

    return $this->is_expired;
  }

  public function is_purchased()
  {
    if ($this->is_purchased !== null) {
      return $this->is_purchased;
    }

    if ($this->is_pro === true || $this->installed_pro === true) {
      $this->is_purchased = true;
    }
    else {
      $this->is_purchased = false;
    }

    return $this->is_purchased;
  }

  //do not work for addons
  public function is_pro()
  {
    if ($this->is_pro !== null) {
      return $this->is_pro;
    }


    if ($this->is_purchased() === false && $this->parent_id == 0) {
      $this->is_pro = false;
    }
    else {
      $this->is_pro = true;
    }

    return $this->is_pro;
  }

  public function is_pro_installed()
  {
    return $this->installed_pro;
  }


  public function is_addon()
  {
    return ($this->parent_id !== 0);
  }

  public function is_plugin()
  {
    return $this->is_plugin;
  }

  public function set_is_pro($is_pro)
  {
    $this->is_pro = ($is_pro === true) ? true : false;
  }

  public function set_subr_id($subr_id)
  {
    $this->subr_id = $subr_id;
  }

  public function set_expire_date($expire_date)
  {
    $this->expire_date = $expire_date;
  }

  public function set_is_buy($is_buy)
  {
    $this->is_buy = $is_buy;
  }

  public function set_installed_pro($installed_pro)
  {
    $this->installed_pro = $installed_pro;
  }

  public function set_has_pro($has_pro)
  {
    $this->has_pro = $has_pro;
  }


  public function getPlan($installed_pro)
  {
    return "";
  }

}