<?php
$user_hash = get_site_option("wdd_user_hash");
$username = get_site_option("wdd_user_full_name");
$class = "loged_in";
if( !$user_hash || $user_hash == 'nohash'  ){
	$class = "loged_out";
}

?>
<div class="wd-login-form <?php echo $class; ?>">
	<?php 
	if( isset( $_GET["err"] ) && $_GET["err"] == 1 ){
	  echo "<p class='login_error'>";
		echo sprintf(
			__( "An error has occurred. Sorry for the inconvenience. Please contact us at %s to find out the problem. Thanks for understanding us.", WDD_LANG ),
			'<a href="mailto:support@web-dorado.com">support@web-dorado.com</a>'
			) ;
    echo "</p>";
	}	

	?>
	<div id="wd-login-form" class="wd_clear">
		<?php 	
		if( !$user_hash || $user_hash == 'nohash'  ){ ?>
	    <h2><?php _e("Log In", WDD_LANG); ?></h2>
			<p id="invalid_password"><?php _e("Invalid username or password.", WDD_LANG); ?></p>
			<p id="required_fields"><?php _e("Please fill required fields.", WDD_LANG); ?></p>
			<p id="activate_account"><?php _e("Please activate your account.", WDD_LANG); ?></p>
			<div class="wd_spinner">
			<?php _e( "User identification...", "WDD" ); ?>
			<img src="<?php echo WDD_URL_IMG; ?>/spinner_light.gif" style="width: 20px;margin: 0 0 -5px 7px;">
			</div>
				<div class="wd-login-sub" style="display:none;">
			    <p><?php _e( "Please log in to your Web-Dorado account to manage your free and paid Web-Dorado plugins, themes and add-ons.", "WDD" ); ?></p>
					<?php wp_nonce_field( 'nonce_WDD', 'nonce_WDD' ); ?>
					<div>
					  <div class="styled-input">
							<input type="text" name="username" id="username" placeholder="<?php _e( "Username", WDD_LANG ); ?>">
							<span class="styled_bar"></span>
						</div>
						<p><a href="<?php echo WDD_SITE_LINK; ?>/username-reminder-request.html" target="_blank"><?php _e( "Forgot Your Username?", WDD_LANG ); ?></a></p>
					</div>
					<div>
					  <div class="styled-input">
							<input type="password" name="password" id="password"  placeholder="<?php _e( "Password", WDD_LANG ); ?>">
							<span class="styled_bar"></span>
						</div>
						<p><a href="<?php echo WDD_SITE_LINK; ?>/password-reset.html" target="_blank"><?php _e( "Forgot Password?", WDD_LANG ); ?></a></p>
					</div>
					<button onclick="wdLogin(); return false;" class="wd-login" id="wd_login"><?php _e( "Log In", WDD_LANG ); ?><span class="spinner"></span></button>
					<div id="create_account"> <a href="<?php echo WDD_SITE_LINK; ?>/registration.html" target="_blank"><?php _e("Create an account", WDD_LANG);?></a></div>
				</div>			
			<?php
		}
		else{
		  $page = isset($_GET["page"]) ? $_GET["page"] : "";
			$special_offers = get_site_option("wdd_special_offers") !== false ? get_site_option("wdd_special_offers") : 0;
			?>
			  <div class="user_name"> <?php echo __("Hi", WDD_LANG) . ", " . $username; ?></div>
				<form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="wd-logout-form">
					<?php wp_nonce_field( 'nonce_WDD', 'nonce_WDD' ); ?>
					<button onclick="wdLogout(this);" class="wd-logout"><?php _e( "Log Out", WDD_LANG ); ?></button>
					<input type="hidden" name="action" id="action" value="wd_logout">
				</form>
			  <div class="wdd_menu">
					<ul class="wd_clear">
						<li id="wdd_plugins" <?php echo ($page == $wdd_options->prefix . '_plugins') ? "class='active'" : ""; ?>><a href="<?php echo esc_url(add_query_arg( array( 'page' => $wdd_options->prefix . '_plugins'), network_admin_url( 'admin.php' )) ); ?>"><?php _e("Plugins", WDD_LANG); ?></a></li>
						<li id="wdd_themes" <?php echo ($page == $wdd_options->prefix . '_themes') ? "class='active'" : ""; ?>><a href="<?php echo esc_url(add_query_arg( array( 'page' => $wdd_options->prefix . '_themes'), network_admin_url( 'admin.php' )) ); ?>"><?php _e("Themes", WDD_LANG); ?></a></li>
						<li id="wdd_addons" <?php echo ($page == $wdd_options->prefix . '_addons') ? "class='active'" : ""; ?>><a href="<?php echo esc_url(add_query_arg( array( 'page' => $wdd_options->prefix . '_addons'), network_admin_url( 'admin.php' )) ); ?>"><?php _e("Add-ons", WDD_LANG); ?></a></li>
						<?php if($special_offers) : ?>
						<li id="wdd_special_offers" <?php echo ($page == $wdd_options->prefix . '_special_offers') ? "class='active'" : ""; ?>><a href="<?php echo esc_url(add_query_arg( array( 'page' => $wdd_options->prefix . '_special_offers'), network_admin_url( 'admin.php' )) ); ?>"><?php _e("Special Offers", WDD_LANG); ?></a></li>
						<?php endif; ?>
						<li id="support_forum"><a href="<?php echo WDD_SITE_LINK; ?>/support/contact-us.html" target="_blank"><?php _e("Support", WDD_LANG); ?></a></li>

					</ul>
				</div>
			<?php
		}

		?>	
	</div>
	
</div>
