<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	// configuration should be loaded first.
	require_once dirname( __FILE__ ) . '/config.php';
    
	// load other files
	require_once WDD_DIR . '/class-wdd.php';
    
    
	require_once WDD_DIR_INCLUDES . '/class-wdd-login.php';
  require_once WDD_DIR_INCLUDES . '/class-wdd-api.php';
  require_once WDD_DIR_INCLUDES . '/class-wdd-product.php';
  require_once WDD_DIR_INCLUDES . '/class-wdd-installed-product.php';
  require_once WDD_DIR_INCLUDES . '/class-wdd-products.php';


