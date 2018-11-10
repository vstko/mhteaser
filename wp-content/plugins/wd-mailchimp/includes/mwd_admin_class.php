<?php

class MWD_Admin {
	public static $instance = null;
	protected $version = '1.1.0';
	public $update_path = 'http://api.web-dorado.com/v1/_id_/allversions';
	public $updates = array();
	public $mwd_plugins = array();
	public $prefix = "mwd_";
	protected $notices = null;
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'check_for_update' ), 25 );
	}

	public function get_plugin_data( $name ) {
		$mwd_plugins = array(
			'wd-mailchimp/wd-mailchimp.php' => array(
				'id'          => 164,
				'url'         => 'https://web-dorado.com/products/wordpress-mailchimp-wd.html',
				'description' => 'MailChimp WD is a functional plugin developed to create MailChimp subscribe/unsubscribe forms and manage lists from your WordPress site.',
				'icon'        => '',
				'image'       => plugins_url( '../assets/wd-mailchimp.png', __FILE__ )
			)
		);
	
		return $mwd_plugins[ $name ];
	}
	
	public function get_remote_version( $id ) {
		$userhash = 'nohash';
		if(file_exists(MWD_DIR.'/.keep') && is_readable(MWD_DIR.'/.keep')){
			$f = fopen(MWD_DIR.'/.keep', 'r');
			$userhash = fgets($f);
			fclose($f);
		}
		
		$this->update_path .= '/'.$userhash;
		$request = wp_remote_get( ( str_replace( '_id_', $id, $this->update_path ) ) );
		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			return json_decode( $request['body'], true );
		}

		return false;
	}


	public function check_for_update() {
		global $menu;
		$mwd_plugins  = array();
		$request_ids   = array();

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		foreach ( $all_plugins as $name => $plugin ) {
			if ( strpos( $name, "mwd_" ) !== false or  $name == "wd-mailchimp/wd-mailchimp.php" ) {

				$data = $this->get_plugin_data( $name );
				if ( $data['id'] > 0 ) {
					$request_ids[] = $data['id'].':'.$plugin['Version'];
					$mwd_plugins[ $data['id'] ] = $plugin;
					$mwd_plugins[ $data['id'] ]['mwd_data'] = $data;
				}
			}
		}

		$this->mwd_plugins = $mwd_plugins;
		if ( false === $updates_available = get_transient( 'mwd_update_check' ) ) {
			$updates_available = array();
			if ( count( $request_ids ) > 0 ) {
				$remote_version    = $this->get_remote_version( implode( '_', $request_ids ) );
				if ( isset( $remote_version['body'] ) ) {
					foreach ( $remote_version['body'] as $id=>$updated_plugin ) {
						if ( isset( $updated_plugin[0]['version'] ) && version_compare( $mwd_plugins[$id]['Version'], $updated_plugin[0]['version'], '<' ) ) {
							$updates_available [ $id ] = $updated_plugin;
						}
					}
				}
			}
			set_transient( 'mwd_update_check', $updates_available, 12 * 60 * 60 );
		}
		$this->updates = $updates_available;
		$updates_count = is_array( $updates_available ) ? count( $updates_available ) : 0;
		add_submenu_page('manage_mwd', 'Updates', 'Updates' . ' ' . '<span class="update-plugins count-' . $updates_count . '" title="title"><span class="update-count">' . $updates_count . '</span></span>', 'manage_options', 'updates_mwd',	'updates_mwd');
		
		if ( $updates_count > 0 ) {
			foreach ( $menu as $key => $value ) {

				if ( $menu[ $key ][2] == 'manage_mwd' || $menu[ $key ][2] == 'updates_mwd' ) {
					$menu[ $key ][0] .= ' ' . '<span class="update-plugins count-' . $updates_count . '" title="title">
                                                    <span class="update-count">' . $updates_count . '</span></span>';

					return;
				}

			}
		}

	}
	
	public function plugin_updated() {
		delete_transient( 'mwd_update_check' );
	}
	

}

?>