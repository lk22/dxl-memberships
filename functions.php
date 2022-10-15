<?php 


/**
 * implementing a login redirect if member is not logged in
 */
add_filter('login_redirect', 'manager_redirect', 1, 3);

if( !function_exists('manager_logout_redirect') ) {
	function manager_logout_redirect() {
		wp_redirect(home_url());
		exit();
	}
}