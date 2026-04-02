<?php
/**
* Load core DB data. Only loaded during Activation
* 
*/
if ( !class_exists('Directory_Core_Data') ):

class Directory_Core_Data {

	/**
	* Load initial Content Types Directory data for plugin
	*
	* @return void
	*/

	public function __construct() {
		//Load default data if object instatiated. Set to only happen during plugin Activate
		add_action( 'init', array( &$this, 'load_data' ) );
		add_action( 'init', array( &$this, 'load_payment_data' ) );
		add_action( 'init', array( &$this, 'load_mu_plugins' ) );
		add_action( 'init', array( &$this, 'rewrite_rules' ) );
	}

	/**
	* Load initial Content Types data for plugin
	*
	* @return void
	*/
	public function load_data() {
		flush_rewrite_rules();
	}

	public function load_payment_data() {

		$options = get_option( DR_OPTIONS_NAME );
		$options = ( is_array($options) ) ? $options : array();

		//General default
		if(empty($options['general']) ){
			$options['general'] = array(
			'member_role'             => 'subscriber',
			'moderation'              => array('publish' => 1, 'pending' => 1, 'draft' => 1 ),
			'custom_fields_structure' => 'table',
			'welcome_redirect'        => 'true',
			'key'                     => 'general'
			);
		}

		//Update from older version
		if (! empty($options['general_settings']) ) {
			$options['general'] = array_merge($options['general'], $options['general_settings']);
			unset($options['general_settings']);
		}

		//Default Payments settings
		if ( empty( $options['payments'] ) ) {
			$options['payments'] = array(
			'enable_recurring'    => '1',
			'recurring_cost'      => '9.99',
			'recurring_name'      => 'Subscription',
			'billing_period'      => 'Month',
			'billing_frequency'   => '1',
			'billing_agreement'   => 'Customer will be billed at “9.99 per month for 2 years”',
			'enable_one_time'     => '1',
			'one_time_cost'       => '99.99',
			'one_time_name'       => 'One Time Only',
			'enable_credits'      => '1',
			'cost_credit'         => '.99',
			'credits_per_listing' => 1,
			'signup_credits'      => 0,
			'credits_description' => '',
			'tos_content'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at sem libero. Pellentesque accumsan consequat porttitor. Curabitur ut lorem sed ipsum laoreet tempus at vel erat. In sed tempus arcu. Quisque ut luctus leo. Nulla facilisi. Sed sodales lectus ut tellus venenatis ac convallis metus suscipit. Vestibulum nec orci ut erat ultrices ullamcorper nec in lorem. Vivamus mauris velit, vulputate eget adipiscing elementum, mollis ac sem. Aliquam faucibus scelerisque orci, ut venenatis massa lacinia nec. Phasellus hendrerit lorem ornare orci congue elementum. Nam faucibus urna a purus hendrerit sit amet pulvinar sapien suscipit. Phasellus adipiscing molestie imperdiet. Mauris sit amet justo massa, in pellentesque nibh. Sed congue, dolor eleifend egestas egestas, erat ligula malesuada nulla, sit amet venenatis massa libero ac lacus. Vestibulum interdum vehicula leo et iaculis.',
			'key'               => 'payments'
			);
		}

		if (! empty($options['payment_settings']) ) {
			$options['payments'] = array_merge($options['payments'], $options['payment_settings'] );
			unset($options['payment_settings']);
		}

		if(empty($options['payment_types']) ) {
			$options['payment_types'] = array(
			'use_free'         => 1,
			'use_paypal'       => 0,
			'use_authorizenet' => 0,
			'paypal'           => array('api_url' => 'sandbox', 'api_username' => '', 'api_password' => '', 'api_signature' => '', 'currency' => 'USD'),
			'authorizenet'     => array('mode' => 'sandbox', 'delim_char' => ',', 'encap_char' => '', 'email_customer' => 'yes', 'header_email_receipt' => 'Thanks for your payment!', 'delim_data' => 'yes'),
			);
		}

		if ( ! empty($options['paypal']) ){
			$options['payment_types']['paypal'] = array_merge($options['payment_types']['paypal'], $options['paypal']);
			unset($options['paypal']);
		}
		update_option( DR_OPTIONS_NAME, $options );
	}
	
	public function rewrite_rules() {

		add_rewrite_rule("directory_listing/author/([^/]+)/page/?([2-9][0-9]*)",
		"index.php?post_type=directory_listing&author_name=\$matches[1]&paged=\$matches[2]", 'top');

		add_rewrite_rule("directory_listing/author/([^/]+)",
		"index.php?post_type=directory_listing&author_name=\$matches[1]", 'top');

		flush_rewrite_rules();
	}

	public function load_mu_plugins() {
        if (!is_dir(WPMU_PLUGIN_DIR . '/logs')) {
            mkdir(WPMU_PLUGIN_DIR . '/logs', 0755, true);
        }

        $mu_plugins = array(
            'gateway-relay.php' => 'gateway-relay.php',
            'wpmu-assist.php'  => 'wpmu-assist.php'
        );

        foreach ($mu_plugins as $plugin) {
            copy(DR_PLUGIN_DIR . "mu-plugins/$plugin", WPMU_PLUGIN_DIR . "/$plugin");
        }
    }	
}

endif;
