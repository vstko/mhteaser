<?php
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Directories
 */

define('WDD_DIRR', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
define('WDD_URLR', plugins_url(plugin_basename(dirname(__FILE__))));
define('WDD_DIR', dirname(__FILE__));
define('WDD_DIR_INCLUDES', WDD_DIR . '/includes');
define('WDD_DIR_TEMPLATES', WDD_DIR . '/templates');
define('WDD_DIR_ASSETS', WDD_DIR . '/assets');
define('WDD_URL_CSS', plugins_url(plugin_basename(dirname(__FILE__))) . '/assets/css');
define('WDD_URL_JS', plugins_url(plugin_basename(dirname(__FILE__))) . '/assets/js');
define('WDD_URL_IMG', plugins_url(plugin_basename(dirname(__FILE__))) . '/assets/img');
define('WDD_SITE_LINK', "https://web-dorado.com");
define('WDD_LANG', "wd-manager");
define('WDD_ID', 177);
define('WDD_SLUG', "wd-manager/wd-manager.php");
define('WDD_ZIP_NAME', "WD-manager.zip");

/**
 * Domain / URL / Address
 */

define('WDD_API_PLUGIN_DATA_PATH', 'https://api.web-dorado.com/v2.1/_id_/plugindata');
define('WDD_API_UAER_HASH_PATH', 'https://api.web-dorado.com/v2.1/_user_/userhash/0');
define('WDD_WP_UPDATES_PATH', 'https://web-dorado.com/wpupdates');
define('WDD_WP_UPSALE_PATH', 'https://web-dorado.com/index.php?option=com_wdsubscriptions&view=checkout');
define('WDD_WP_PRODUCT_PATH', 'https://web-dorado.com/index.php?option=com_wdproducts');
define('WDD_USER_DATA', 'https://web-dorado.com/index.php?option=com_wdsubscriptions&view=data&tmpl=component');
define('WDD_SPECIAL_OFFERS', 'https://web-dorado.com/index.php?option=com_content&view=article&id=1449');
define('WDD_UPDATE_PATH', 'https://web-dorado.com/?option=com_wdsubscriptions&view=updatedownload&format=row');



// global options
global $wdd_options;
$wdd_options = new StdClass();
$wdd_options->prefix = null;
$wdd_options->plugin_main_file = null;
$wdd_options->plugin_menu_parent_slug = null;
$wdd_options->after_activate = null;
