<?php

class MWDControllerHelper {
	////////////////////////////////////////////////////////////////////////////////////////
	// Events                                                                             //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constants                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Variables                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constructor & Destructor                                                           //
	////////////////////////////////////////////////////////////////////////////////////////
	public function __construct() {
	}
	////////////////////////////////////////////////////////////////////////////////////////
	// Public Methods                                                                     //
	////////////////////////////////////////////////////////////////////////////////////////
	public function execute() {
		if( ! class_exists( 'Mailchimp' ) ) {
			include_once( MWD_DIR . '/includes/Mailchimp.php' );
		}
	
		$nonce_mwd = MWD_Library::get('nonce_mwd');
		if(!wp_verify_nonce($nonce_mwd, 'nonce_mwd')) {
			MWD_Library::mwd_redirect(add_query_arg(array('message' => '10'), admin_url('admin.php?page=manage_mwd')));
		}	
		
		$task = MWD_Library::get('task');
		$list_id = MWD_Library::get('list_id', 0);

		if (method_exists($this, $task)) {
			$this->$task($list_id);
		}
	}

	public function mwd_params() {
		require_once MWD_DIR . "/admin/models/MWDModelHelper.php";
		$model = new MWDModelHelper();
		
		$apikey = MWD_Library::get('apikey');
		$isvalid_apikey = $model->mwd_validate_api($apikey);
		if(get_option('mwd_api_validation') == 'invalid_apikey') {
			echo "<b style='color:red'>".$isvalid_apikey."</b>\n";
			die();
		}
	
		$lists = $model->mwd_lists();
		$account_details = $model->mwd_account_details();
		$profile_info = $model->mwd_profile_info();
		
		$page_data = array();
		$page_data['lists'] = $lists;
		$page_data['account_details'] = $account_details;
		$page_data['profile_info'] = $profile_info;
		
		if(false !== get_transient('mwd-list-info') || false !== get_transient('mwd-profile-info') || false !== get_transient('mwd-account-details')) {
			delete_transient('mwd-list-info');
			delete_transient('mwd-profile-info');
			delete_transient('mwd-account-details');
		}
		
		echo json_encode($page_data);
		die();
	}	
	
	public function lists() {
		require_once MWD_DIR . "/admin/models/MWDModelHelper.php";
		$model = new MWDModelHelper();
		
		$apikey = get_option('mwd_api_key');
		$isvalid_apikey = $model->mwd_validate_api($apikey);
		if(get_option('mwd_api_validation') == 'invalid_apikey') {
			echo "<b style='color:red'>".$isvalid_apikey."</b>\n";
			die();
		}
	
		$lists = $model->mwd_lists();
		echo json_encode($lists);
		die();
	}
		
	public function merge_variables($list_id) {
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		$merge_variables_data = $api->call( 'lists/merge-vars' , array( 'apikey' => $apikey , 'id' => array($list_id)));
		$merge_variables = $merge_variables_data['data'][0]['merge_vars'];

		echo json_encode($merge_variables);
		die();	
	}

	public function mwd_live_search() {
		$search_string = ! empty( $_POST['pp_live_search'] ) ? sanitize_text_field( $_POST['pp_live_search'] ) : '';
		$post_type = ! empty( $_POST['pp_post_type'] ) ? sanitize_text_field( $_POST['pp_post_type'] ) : 'any';
		$full_content = ! empty( $_POST['pp_full_content'] ) ? sanitize_text_field( $_POST['pp_full_content'] ) : 'true';

		$args['s'] = $search_string;

		$results = $this->mwd_posts_query( $args, $post_type );
		/* if ('true' === $full_content) { */
			$output = '<ul class="pp_search_results">';
		/* } else {
			$output = '';
		} */

		if (empty($results)) {
			/* if ( 'true' === $full_content ) { */
				$output .= sprintf(
					'<li class="pp_no_res">%1$s</li>',
					esc_html__( 'No results found', 'mwd-text' )
				);
			/* } */
		} else {
			foreach( $results as $single_post ) {
				$output .= sprintf(
					'<li data-post_id="%2$s">[%3$s] - %1$s</li>',
					esc_html( $single_post['title'] ),
					esc_attr( $single_post['id'] ),
					esc_html( $single_post['post_type'] )
				);
			}
		}

		/* if ( 'true' === $full_content ) { */
			$output .= '</ul>';
		/* } */

		die( $output );
	}
	
	public function mwd_posts_query( $args = array(), $include_post_type = '' ) {
		if ( 'only_pages' === $include_post_type ) {
			$pt_names = array( 'page' );
		} elseif ( 'any' === $include_post_type || 'only_posts' === $include_post_type ) {
			$default_post_types = array( 'post', 'page' );
			$custom_post_types = get_post_types( array(
				'public'   => true,
				'_builtin' => false,
			) );

			$post_types = array_merge($default_post_types, $custom_post_types);
			$pt_names = array_values($post_types);

			if ( 'only_posts' === $include_post_type ) {
				unset($pt_names[1]);
			}
		} else {
			$pt_names = $include_post_type;
		}

		$query = array(
			'post_type' => $pt_names,
			'suppress_filters' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);

		if ( isset( $args['s'] ) ) {
			$query['s'] = $args['s'];
		}

		$get_posts = new WP_Query;
		$posts = $get_posts->query( $query );
		if ( ! $get_posts->post_count ) {
			return false;
		}

		$results = array();
		foreach ($posts as $post) {
			$results[] = array(
				'id' => (int) $post->ID,
				'title' => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
				'post_type' => $post->post_type,
			);
		}

		wp_reset_postdata();

		return $results;
	}
	
	public function add_more( $list_id ) {
		require_once MWD_DIR . "/admin/models/MWDModelHelper.php";
		$model = new MWDModelHelper();
		
		$list_ids = explode(',', $list_id);
		$lists = $model->mwd_lists();
		$apikey = get_option('mwd_api_key', '');
		?>
		<style>
			.add_more{
				color:#696969;
				padding: 0 25px;
				font-size: 14px;
			}
			
			.add_more p{
				padding: 0;
				margin: 0;
				font-size: 14px;
			}
			
			.add_more ul{
				list-style-type: circle;
				padding-left: 15px;
			}
			
			.add_more ol{
				margin-left: 15px;
			}
			
			.renew-button{
				text-align:right;
			}
		</style>
		<div class="add_more">
			<h2>Add more fields</h2>
			<span>To add more fields to your form, you will need to create those fields in MailChimp first.</span>
			<br /><br />
			<span><strong>Here's how:</strong></span>
			<ol>
				<li>
					<p>Log in to your MailChimp account. </p>
				</li>
				<li>
					<p>Add list fields to any of your selected lists. Clicking the following links will take you to the right screen. </p>
					<ul>
						<?php foreach( $list_ids as $list ) { ?>
							<li data-list-id="<?php echo $list; ?>">
								<a href="https://admin.mailchimp.com/lists/settings/merge-tags?id=<?php echo $lists[$list]->web_id; ?>" target="_blank">
									<?php echo $lists[$list]->name; ?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</li>
				<li>
					<p>Click the following button to have MailChimp for WordPress pick up on your changes.</p>
					<div class="renew-button"><button class="mwd-button mwd-renew-list extra-large">
						<span></span>
						Renew MailChimp lists
					</button></div>
				</li>
			</ol>
		</div>
		<script>
			jQuery('.mwd-renew-list').on('click', function() {
				if(!parseInt(jQuery('#current_id').val())){
					jQuery('#title').val('new form');
				}

				if (submitbutton()) { 
					previewImg('apply');
				} 
			});	
		</script>
		<?php
		die();
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