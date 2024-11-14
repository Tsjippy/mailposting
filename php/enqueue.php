<?php
namespace SIM\MAILPOSTING;
use SIM;

//load js and css
add_action( 'admin_enqueue_scripts', __NAMESPACE__.'\enqueueScripts');
function enqueueScripts($hook) {
	//Only load on sim settings pages
	if(!str_contains($hook, 'sim-settings_page_sim_mailposting')) {
		return;
	}

	wp_enqueue_script('sim_posting_admin', SIM\pathToUrl(MODULE_PATH.'js/admin.min.js'), array() , MODULE_VERSION, true);
}