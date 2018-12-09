<?php

/* ---------------------------------------------------------------------------
 * Child Theme URI | DO NOT CHANGE
 * --------------------------------------------------------------------------- */
define( 'CHILD_THEME_URI', get_stylesheet_directory_uri() );


/* ---------------------------------------------------------------------------
 * Define | YOU CAN CHANGE THESE
 * --------------------------------------------------------------------------- */

// White Label --------------------------------------------
define( 'WHITE_LABEL', false );

// Static CSS is placed in Child Theme directory ----------
define( 'STATIC_IN_CHILD', false );


/* ---------------------------------------------------------------------------
 * Enqueue Style
 * --------------------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', 'mfnch_enqueue_styles', 101 );
function mfnch_enqueue_styles() {

	// Enqueue the parent stylesheet
// 	wp_enqueue_style( 'parent-style', get_template_directory_uri() .'/style.css' );		//we don't need this if it's empty

	// Enqueue the parent rtl stylesheet
	if ( is_rtl() ) {
		wp_enqueue_style( 'mfn-rtl', get_template_directory_uri() . '/rtl.css' );
	}

	// Enqueue the child stylesheet
	wp_dequeue_style( 'style' );
	wp_enqueue_style( 'style', get_stylesheet_directory_uri() .'/style.css' );

}

/* ---------------------------------------------------------------------------
 * Load Textdomain
 * --------------------------------------------------------------------------- */
add_action( 'after_setup_theme', 'mfnch_textdomain' );
function mfnch_textdomain() {
    load_child_theme_textdomain( 'betheme',  get_stylesheet_directory() . '/languages' );
    load_child_theme_textdomain( 'mfn-opts', get_stylesheet_directory() . '/languages' );
}

/*-- Load JS --*/
function my_js() {
        wp_enqueue_script( 'jquery.scrollify', get_stylesheet_directory_uri() . '/vendors/scrollify/jquery.scrollify.js', array( 'jquery'), '', true );
		wp_enqueue_script( 'scripts', get_stylesheet_directory_uri() . '/app.min.js', array( 'jquery'), '', true );
}
add_action( 'wp_enqueue_scripts', 'my_js' );


/* ----------------------------------------------------------------------------
 * Register new api endpoint for sequent
 ------------------------------------------------------------------------------ */

require_once(__DIR__.'/sequent/Prospect.php'); // imports Prospect class
require_once(__DIR__.'/sequent/Utils.php'); // imports Utils class

function createProspect($data) {

    /** @var WP_REST_Request $data */
    $prospectObj = new Prospect();
    $dataToAddProspect = $prospectObj->getDataToAddProspect($data->get_body_params());
    $utils = new Utils();
    $options = get_option('plugin_options');
    $baseUrl = $options['sequen_api_base_url'];
    $apiKey = $options['x_api_key'];
    $sequentKey = $options['x_sequent_key'];

    $sequentRes = $utils::addProspect($dataToAddProspect,$baseUrl, $apiKey, $sequentKey);

    $response = new WP_REST_Response(json_decode($sequentRes, true));

    return $response;
}

require_once(TEMPLATEPATH . '-child/functions/admin-menu.php');

add_action( 'rest_api_init', function () {
    register_rest_route( 'betheme-child/v1', '/sequent/addprospect', array(
        'methods' => 'POST',
        'callback' => 'createProspect',
    ) );
} );
