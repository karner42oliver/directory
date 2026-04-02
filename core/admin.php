<?php
/**
* Directory_Core_Admin
*
* @uses Directory_Core
* @copyright PSOURCE 2011-2024 {@link https://github.com/cp-psource}
* @author DerN3rd {@link https://github.com/cp-psource}
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

if(!class_exists('Directory_Core_Admin')):

class Directory_Core_Admin extends Directory_Core {

	/**
	* Constructor.
	*/
	function __construct(){

		parent::__construct();

		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_menu', array( &$this, 'reorder_menu' ), 999 );
		add_action( 'restrict_manage_posts', array($this,'on_restrict_manage_posts') );
		add_action( 'add_meta_boxes', array( &$this, 'register_listing_meta_boxes' ) );
		add_action( 'save_post_directory_listing', array( &$this, 'save_listing_meta_boxes' ), 10, 2 );
		add_action( 'admin_notices', array( &$this, 'render_listing_profile_validation_notice' ) );

		add_action( 'admin_print_scripts', array( &$this, 'js_print_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'on_enqueue_scripts' ) );

		add_action( 'admin_init', array( &$this, 'welcome_first_time_user' ) );
		add_action( 'admin_init', array( &$this, 'handle_getting_started_redirects') );

		add_action( 'wp_ajax_dr_get_caps', array( &$this, 'ajax_get_caps' ) );
		add_action( 'wp_ajax_dr_save', array( &$this, 'ajax_save' ) );

		add_action( 'wp_ajax_nopriv_check_login', array( &$this, 'ajax_check_login' ) );
		add_action( 'wp_ajax_check_login', array( &$this, 'ajax_check_login' ) );

		//IPN script for Paypal
		add_action( 'wp_ajax_nopriv_directory_ipn', array( &$this, 'ajax_directory_ipn' ) );
		add_action( 'wp_ajax_directory_ipn', array( &$this, 'ajax_directory_ipn' ) );

		//Silent Post script for Authorizenet
		add_action( 'wp_ajax_nopriv_directory_sp', array( &$this, 'ajax_directory_silent_post' ) );
		add_action( 'wp_ajax_directory_sp', array( &$this, 'ajax_directory_silent_post' ) );

		// Render admin via action hook. Used mainly by modules.
		add_action( 'render_admin', array( &$this, 'render_admin' ), 10, 2 );

	}

	/**
	* Register all admin menues.
	*
	* @return void
	*/
	function admin_menu() {
		$opts = get_option( $this->options_name );

		if ( ! current_user_can('unfiltered_html') ) {
			remove_submenu_page('edit.php?post_type=directory_listing', 'post-new.php?post_type=directory_listing' );
			add_submenu_page( 
			'edit.php?post_type=directory_listing', 
			__( 'Neuer Eintrag', $this->text_domain ), 
			__( 'Neuer Eintrag', $this->text_domain ), 
			'create_listings', 'listings_add', 
			array( &$this, 'redirect_add' ) );
		}

		if ( ( isset( $opts['general']['show_getting_started'] ) && '1' == $opts['general']['show_getting_started'] ) || !$this->_getting_started_complete() ) {
			
			$menu_page = add_submenu_page( 
			'edit.php?post_type=directory_listing', 
			__( 'Erste Schritte', $this->text_domain ), 
			__( 'Erste Schritte', $this->text_domain ), 
			'manage_options', 
			'dr-get_started', 
			array( $this, 'create_getting_started_page' ) );
			
			// Hook styles
			//add_action( 'admin_print_styles-' .  $menu_page, array( &$this, 'enqueue_styles' ) );
		}
		
		$settings_page = add_submenu_page( 
		'edit.php?post_type=directory_listing', 
		__( 'Verzeichnis Einstellungen', $this->text_domain ), 
		__( 'Einstellungen', $this->text_domain ), 
		'create_users', //create_users so on multisite you can turn on and off Settings with the Admin add users switch
		'directory_settings', array( &$this, 
		'handle_settings_page_requests' ) );

		add_action( 'admin_print_scripts-' .  $settings_page, array( &$this, 'on_enqueue_scripts' ) );
		//@todo striaghten out style and script loads.

		if($this->use_credits	&& (current_user_can('manage_options') || $this->use_paypal || $this->use_authorizenet ) ){
			add_submenu_page( 
			'edit.php?post_type=directory_listing', 
			__( 'Verzeichnis Guthaben', $this->text_domain ), 
			__( 'Guthaben', $this->text_domain ), 
			'read', 'directory_credits' , 
			array( &$this, 'handle_credits_page_requests' ) );
		}
	}

	function redirect_add(){
		echo '<script>window.location = "' . get_permalink($this->add_listing_page_id) . '";</script>';
	}

	/**
	* Quick hack to reorder CPT menu items
	* so that the welcome page come first.
	*/
	function reorder_menu () {
		$opts = get_option( $this->options_name );
		if ( ( !isset( $opts['general']['show_getting_started'] ) || '0' == $opts['general']['show_getting_started'] ) && $this->_getting_started_complete() )
		return;

		global $submenu;

		$mkey = 'edit.php?post_type=directory_listing';

		$submenus = (empty($submenu[$mkey]) ) ? array() : $submenu[$mkey];

		foreach ( $submenus as $idx => $item ) {
			if ( ! in_array('dr-get_started', $item) ) continue;
			$tmp = $submenus[$idx];
			unset( $submenus[$idx] );
			array_unshift( $submenus, $tmp );
			$submenu[$mkey] = $submenus;
		}
	}

	/**
	* Inject welcome page markup.
	*/
	function create_getting_started_page () {
		global $current_user;
		$dr_tutorial = get_user_meta ( $current_user->ID, 'dr_tutorial', true );
		$dr_tutorial = $dr_tutorial ? $dr_tutorial : array();
		include( $this->plugin_dir . 'ui-admin/getting-started.php' );
	}

	/**
	* Redirect to Getting started page on first load.
	*/
	function welcome_first_time_user () {
		if ( is_network_admin() ) return false; // Not applicable on network pages.
		if ( $this->_getting_started_complete() ) return false; // User already saw this.

		$opts = maybe_unserialize(get_option( $this->options_name ) );
		if(empty($opts['general']['welcome_redirect']) ) return false; // Not a first time user, move on.

		//if old version < 2
		if ( get_option( 'dp_options' ) && ! isset( $_POST['install_dir2'] ) )
		return false;

		$opts['general']['welcome_redirect'] = false;
		update_option( $this->options_name, $opts );
		wp_redirect( admin_url( 'admin.php?page=dr-get_started' ) );
		die;
	}

	/**
	* Handle calls from welcome page and record progress.
	*/
	function handle_getting_started_redirects () {
		global $current_user;
		$dr_tutorial = get_user_meta( $current_user->ID, 'dr_tutorial', true );
		$dr_tutorial = $dr_tutorial ? $dr_tutorial : array();

		$dr_intent = isset( $_GET['dr_intent'] ) ? $_GET['dr_intent'] : false ;
		switch ( $dr_intent ) {
			case "settings":
			$dr_tutorial['settings'] = 1;
			update_user_meta( $current_user->ID, 'dr_tutorial', $dr_tutorial );
			wp_redirect( admin_url( 'edit.php?post_type=directory_listing&page=directory_settings' ) );
			exit;
			case "category":
			$dr_tutorial['category'] = 1;
			update_user_meta( $current_user->ID, 'dr_tutorial', $dr_tutorial );
			wp_redirect( admin_url( 'edit-tags.php?taxonomy=listing_category&post_type=directory_listing' ) );
			exit;
			case "listing":
			$dr_tutorial['listing'] = 1;
			update_user_meta( $current_user->ID, 'dr_tutorial', $dr_tutorial );
			wp_redirect( admin_url( 'edit.php?post_type=directory_listing' ) );
			exit;
		}
	}

	/**
	* Quick "are we done yet" check for welcome page.
	*/
	private function _getting_started_complete () {
		global $current_user;
		$dr_tutorial = get_user_meta( $current_user->ID, 'dr_tutorial', true );
		$dr_tutorial = $dr_tutorial ? $dr_tutorial : array();

		if ( isset( $dr_tutorial['settings'] ) && isset( $dr_tutorial['category'] ) && isset( $dr_tutorial['listing'] ) )
		return true;
		else
		return false;
	}

	/**
	* Load scripts on plugin specific admin pages only.
	*
	* @return void
	*/
	function on_enqueue_scripts() {
		$screen = function_exists('get_current_screen') ? get_current_screen() : null;
		$is_listing_screen = ($screen && isset($screen->post_type) && 'directory_listing' === $screen->post_type);

		wp_enqueue_style( 'dr-admin-styles', $this->plugin_url . 'ui-admin/css/ui-styles.css');

		//including JS scripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-form' );

		if ( $is_listing_screen || ( isset( $_GET['post_type'] ) &&  'directory_listing' == $_GET['post_type'] ) ) {
			wp_enqueue_script( 'dr_editor', $this->plugin_url . 'ui-admin/js/admin.js', array('jquery') );
			wp_localize_script('dr_editor', 'drListingAdmin', array(
				'badUrlText' => __( 'Bitte gib eine gueltige URL ein (mit http:// oder https://).', $this->text_domain ),
				'verifiedText' => __( 'URL geprueft. Die Seite wurde in einem neuen Tab geoeffnet.', $this->text_domain ),
			));
		}

		wp_enqueue_script( 'dr-admin-scripts', $this->plugin_url . 'ui-admin/js/ui-scripts.js', array( 'jquery' ) );
	}

	/**
	* Inject basic javascript dataset, for consistency.
	*/
	function js_print_scripts () {
		printf(
		'<script type="text/javascript">
		var _dr_data = {
		"root_url": "%s",
		};
		</script>',
		$this->plugin_url
		);
	}

	/**
	 * Save plugin options.
	 *
	 * @param  array $params The $_POST array
	 * @return void
	 */
	function save_admin_options( $params ) {
		check_admin_referer('verify');

		// Format costs to .00
		if ( 'payments' ==  $params['key'] ) {
			if ( isset( $params['recurring_cost'] ) )
				$params['recurring_cost'] = sprintf( "%01.2f", $params['recurring_cost'] );
			if ( isset( $params['one_time_cost'] ) )
				$params['one_time_cost'] = sprintf( "%01.2f", $params['one_time_cost'] );
			if ( isset( $params['cost_credit'] ) )
				$params['cost_credit'] = sprintf( "%01.2f", $params['cost_credit'] );
		}

		/* Remove unwanted parameters */
		unset( $params['_wpnonce'],
			$params['_wp_http_referer'],
			$params['save'],
			$params['add_role'],
			$params['delete_role'],
			$params['new_role']
		);

		/* Update options by merging the old ones */
		$options = $this->get_options();
		$options = array_merge( $options, array( $params['key'] => $params ) );
		update_option( $this->options_name, $options );

		// Add settings success message
		add_settings_error(
			'directory_core_admin_settings',
			'settings_saved',
			__( 'Einstellungen gespeichert.', $this->text_domain ),
			'updated'
		);
	}

	/**
	* Handles $_GET and $_POST requests for the settings page.
	*
	* @return void
	*/
	function handle_settings_page_requests() {
		$valid_tabs = array(
		'general',
		'capabilities',
		'ads',
		'payments',
		'payment-types',
		'affiliate',
		'shortcodes',
		);

		$params = stripslashes_deep($_POST);
		
		$page = (empty($_GET['page'])) ? '' : $_GET['page'] ;
		$tab = (empty($_GET['tab'])) ? 'general' : $_GET['tab']; //default tab

		if($page == 'directory_settings' && in_array($tab, $valid_tabs) ) {

			if ( isset( $params['add_role'] ) ) {
				check_admin_referer('verify');
				$name = sanitize_file_name($params['new_role']);
				$slug = sanitize_key(preg_replace('/\W+/','_',$name) );
				$result = add_role($slug, $name, array('read' => true) );
				if (empty($result) ) $this->message = __('ROLLE BESTEHT BEREITS' , $this->text_domain);
				else $this->message = sprintf(__('Neue Rolle "%s" hinzugefügt' , $this->text_domain), $name);
			}

			if ( isset( $params['remove_role'] ) ) {
				check_admin_referer('verify');
				$name = $params['delete_role'];
				remove_role($name);
				$this->message = sprintf(__('Rolle %s entfernt' , $this->text_domain), $name);
			}

			if(isset($params['save']) ) $this->save_admin_options( $params );
		}

		$this->render_admin( "settings-{$tab}" );

		do_action( 'dr_handle_settings_page_requests' );
	}

	/**
	* Handles $_GET and $_POST requests for the credits page.
	*
	* @return void
	*/
	function handle_credits_page_requests(){
		$valid_tabs = array(
		'my-credits',
		'send-credits',
		);

		$params = stripslashes_deep($_POST);
		
		$page = (empty($_GET['page'])) ? '' : $_GET['page'] ;
		$tab = (empty($_GET['tab'])) ? 'my-credits' : $_GET['tab']; //default tab

		if($page == 'directory_credits' && in_array($tab, $valid_tabs) ) {
			if ( $tab == 'send-credits' ) {
				if(!empty($params)) check_admin_referer('verify');
				$send_to = ( empty($params['manage_credits'])) ? '' : $params['manage_credits'];
				$send_to_user = ( empty($params['manage_credits_user'])) ? '' : $params['manage_credits_user'];
				$send_to_count = ( empty($params['manage_credits_count'])) ? '' : $params['manage_credits_count'];

				$credits = (is_numeric($send_to_count)) ? (intval($send_to_count)) : 0;

				if(is_multisite()) $blog_id = get_current_blog_id();

				if ($send_to == 'send_single'){
					$user = get_user_by('login', $send_to_user);
					if($user){
						$transaction = new DR_Transactions($user->ID, $blog_id);
						$transaction->credits += $credits;
						unset($transaction);
						$this->message = sprintf(__('Benutzer "%s" hat %s Credits für das Verzeichniskonto des Mitglieds erhalten',$this->text_domain), $send_to_user, $credits);

					} else {
						$this->message = sprintf(__('Benutzer "%s" nicht gefunden oder kein Verzeichnis-Mitglied',$this->text_domain), $send_to_user);
					}
				}

				if ($send_to == 'send_all'){
					$search = array();
					if(is_multisite()) $search['blog_id'] = get_current_blog_id();
					$users = get_users($search);
					foreach($users as $user){
						$transaction = new DR_Transactions($user->ID, $blog_id);
						$transaction->credits += $credits;
						unset($transaction);
					}
					$this->message = sprintf(__('Allen Benutzern wurden "%s" Credits zu ihren Konten hinzugefügt.',$this->text_domain), $credits);

				}
			} else {
				if ( isset( $params['purchase'] ) ) {
                //var_dump($this->checkout_page_id);die;
					$this->js_redirect( get_permalink($this->signup_page_id) );
				}
			}
		}

		$this->render_admin( "credits-{$tab}" );

		do_action( 'dr_handle_credits_page_requests' );
	}

	/**
	* Ajax callback which gets the post types associated with each page.
	*
	* @return JSON Encoded string
	*/
	function ajax_get_caps() {
		if ( !current_user_can( 'manage_options' ) ) die(-1);
		if(empty($_POST['role'])) die(-1);

		global $wp_roles;

		$role = $_POST['role'];

		if ( !$wp_roles->is_role( $role ) )
		die(-1);

		$role_obj = $wp_roles->get_role( $role );

		$response = array_intersect( array_keys( $role_obj->capabilities ), array_keys( $this->capability_map ) );
		$response = array_flip( $response );

		// response output
		header( "Content-Type: application/json" );
		echo json_encode( $response );
		die();
	}

	/**
	* Save admin options.
	*
	* @return void die() if _wpnonce is not verified
	*/
	function ajax_save() {

		check_admin_referer( 'verify' );

		if ( !current_user_can( 'manage_options' ) )
		die(-1);

		// add/remove capabilities
		global $wp_roles;

		$role = $_POST['roles'];

		$all_caps = array_keys( $this->capability_map );
		$to_add = array_keys( (array)$_POST['capabilities'] );
		$to_remove = array_diff( $all_caps, $to_add );

		foreach ( $to_remove as $capability ) {
			$wp_roles->remove_cap( $role, $capability );
		}

		foreach ( $to_add as $capability ) {
			$wp_roles->add_cap( $role, $capability );
		}

		die(1);
	}

	/**
	* Checking login name for register new user.
	*
	* @return
	*/
	function ajax_check_login() {

		if ( "login" == $_REQUEST['type'] ) {
			if ( username_exists( $_REQUEST['login'] ) )
			die('yes');
			else
			die('no');
		} elseif ( "email" == $_REQUEST['type'] ) {
			if ( email_exists( $_REQUEST['email'] ) )
			die('yes');
			else
			die('no');
		}
		die("yes");
	}

	function write_to_log($error, $log = 'error') {

		//create filename for each month
		$filename = $this->plugin_dir . "{$log}_" . date('Y_m') . '.log';

		//add timestamp to error
		$message = gmdate('[Y-m-d H:i:s] ') . $error;

		//write to file
		file_put_contents($filename, $message . "\n", FILE_APPEND);
	}

	/**
	* IPN script for change user role when Recurring Payment changed status
	*
	* @return void
	*/
	function ajax_directory_ipn() {
		// debug mode for IPN script (please open plugin dir (directory) for writing)
		$debug_ipn = 0;
		if ( 1 == $debug_ipn ) {
			$this->write_to_log(
			' - 01 -' . " POST\r\n" .
			print_r( $_SERVER, true ) . "\r\n" .
			print_r( $_POST, true ),
			'debug_ipn' );
		}

		$postdata = http_build_query($_POST);
		$postdata .= "&cmd=_notify-validate";

		$options = $this->get_options( 'payment_types' );
		$options = $options['paypal'];

		if ( 'live' == $options['api_url'] )
		$url = "https://www.paypal.com/cgi-bin/webscr";
		else
		$url = "https://www.sandbox.paypal.com/cgi-bin/webscr";

		$args =  array(
		'timeout' => 90,
		'sslverify' => false
		);

		$response = wp_remote_get( $url . "?" . $postdata, $args );

		if( is_wp_error( $response ) ) {
			if ( 1 == $debug_ipn ) {
				$this->write_to_log(
				' - 02 -' . " error with send post\r\n" .
				print_r( "url: " . $url . "\r\n", true ) .
				print_r( $response, true ),
				'debug_ipn' );
			}
			die('error with send post');
		} else {
			$response = $response["body"];
		}

		if ( $response != "VERIFIED" ) {
			if ( 1 == $debug_ipn ) {
				$this->write_to_log(
				' - 03 -' . " not VERIFIED\r\n" .
				print_r( $response, true ),
				'debug_ipn' );
			}
			die( 'not VERIFIED' );
		}

		if ( $_POST['subscr_id'] ) {

			if( is_numeric($_POST['custom']) ) { //old style
				$user_id = $_POST['custom'];
			} else {
				parse_str($_POST['custom'], $custom);
				$user_id = $custom['uid'];
				$blogid = $custom['bid'];
			}

			$transactions = new DR_Transactions($user_id, $blogid);

			if ( "subscr_payment" == $_POST['txn_type'] ) {

				$key = md5( $_POST['mc_currency'] . "directory_123" . $_POST['mc_gross'] );

				//checking hash keys
				if ( $key != $transactions->paypal['key']) {
					if ( 1 == $debug_ipn ) {
						$this->write_to_log(
						' - 04 -' . " Conflict Keys:\r\n" .
						print_r( " key from site: " . $transactions->paypal['key'], true ) . "\r\n" .
						print_r( "key from Paypal: " . $key, true ) . "\r\n" .
						print_r($transactions->paypal, true),
						'debug_ipn' );
					}
					die("conflict key");
				}

				//write subscr_id (profile_id) to user meta
				$transactions->paypal = $_POST;

				if ( 1 == $debug_ipn ) {
					$this->write_to_log(
					' - 05 -' . " subscr_payment OK\r\n" .
					print_r($transactions, true) . "\r\n",
					'debug_ipn' );
				}

			} elseif( in_array( $_POST['txn_type'], array("subscr_cancel", "subscr_failed", "subscr_eot") ) ) {

				if ( 1 == $debug_ipn ) {
					$this->write_to_log(
					' - 05 -' . " subscr_payment OK\r\n" .
					print_r($transactions, true) . "\r\n",
					'debug_ipn' );
				}
				$transactions->paypal = $_POST;
			}
		}
		die("ok");
	}

	/**
	* Script for change user role when Authorizenet Recurring Payment changed status
	* @return void
	*/
	function ajax_directory_silent_post() {

		// debug mode for Silent Post script (please open plugin dir (classifieds) for writing)
		$debug_sp = 0;
		if ( 1 == $debug_sp ) {
			$this->write_to_log(
			print_r( date( "H:i:s m.d.y" ) . ' - 01 -' . " POST\r\n", true ) .
			print_r( $_POST, true ),
			'debug_sp' );
		}

		//silent doesn't do any handshaking
		if ( ! empty($_POST['x_invoice_num']) ) {
			$blogid = explode('-', $_POST['x_invoice_num']); //Format CLS-4-87sd8si222ldff
			if($blogid[0] == 'CLS' && is_numeric($blogid[1])){
				$blogid = intval($blogid[1]);
				$user_id = $_POST['x_cust_id'];

				$transactions = new DR_Transactions($user_id, $blogid);

				$this->write_to_log(print_r($transactions->transactions, true), 'debug_sp');

				if(! empty($_POST['x_subscription_id'])
				&& $_POST['x_subscription_id'] == $transactions->authorizenet['profile_id'] ){

					$transactions->authorizenet = $_POST;
				} else {

					if ( 1 == $debug_sp ) $this->write_to_log('Subscription ID mismatch Post: ' . $_POST['x_subscription_id'] . ' Key: ' . $transactions->authorizenet['profile_id'] , 'debug_sp');

				}
			} else{
				if ( 1 == $debug_sp ) $this->write_to_log('Bad x-invoice_num Post: ' . $_POST['x_subscription_id'] . ' Key: ' . $transactions->authorizenet['key'], 'debug_sp' );
			}
		}
		die("ok");
	}

	/**
	* Renders an admin section of display code.
	*
	* @param  string $name Name of the admin file(without extension)
	* @param  string $vars Array of variable name=>value that is available to the display code(optional)
	* @return void
	*/
	function render_admin( $name, $vars = array() ) {
		foreach ( $vars as $key => $val )
		$$key = $val;
		if ( file_exists( "{$this->plugin_dir}ui-admin/{$name}.php" ) )
		include "{$this->plugin_dir}ui-admin/{$name}.php";
		else
		echo "<p>Rendering of admin template {$this->plugin_dir}ui-admin/{$name}.php failed</p>";
	}

	function on_restrict_manage_posts() {
		global $typenow;
		$taxonomy = 'listing_category';
		if( $typenow == "directory_listing" ){

			$filters = array($taxonomy);
			foreach ($filters as $tax_slug) {
				$tax_obj = get_taxonomy($tax_slug);
				if ( empty($tax_obj) ) {
					continue;
				}
				$tax_name = $tax_obj->labels->name;
				$terms = get_terms(array('taxonomy' => $tax_slug, 'hide_empty' => false));
				if (is_wp_error($terms)) {
					continue;
				}
				$current = isset($_GET[$tax_slug]) ? sanitize_text_field(wp_unslash($_GET[$tax_slug])) : '';
				echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
				echo "<option value=''>{$tax_obj->labels->all_items}&nbsp;</option>";
				foreach ($terms as $term) { echo '<option value='. $term->slug, $current == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>'; }
				echo "</select>";
			}
		}
	}

	/**
	* Register modern listing profile meta boxes.
	*
	* @return void
	*/
	function register_listing_meta_boxes() {
		add_meta_box(
			'dr_listing_business_profile',
			__( 'Gelbe Seiten Profil', $this->text_domain ),
			array( &$this, 'render_listing_business_profile_meta_box' ),
			'directory_listing',
			'normal',
			'high'
		);
	}

	/**
	* Render modern listing profile fields.
	*
	* @param WP_Post $post Post object.
	* @return void
	*/
	function render_listing_business_profile_meta_box( $post ) {
		$defaults = array(
			'company' => '',
			'contact_name' => '',
			'phone' => '',
			'mobile' => '',
			'email' => '',
			'website' => '',
			'opening_hours' => '',
			'street' => '',
			'postal_code' => '',
			'city' => '',
			'country' => '',
			'lat' => '',
			'lng' => '',
			'website_verified' => 0,
			'website_verified_at' => '',
		);

		$profile = get_post_meta( $post->ID, '_dr_business_profile', true );
		$profile = is_array($profile) ? array_merge($defaults, $profile) : $defaults;

		wp_nonce_field( 'dr_listing_business_profile', 'dr_listing_business_profile_nonce' );
		?>
		<div class="dr-modern-meta-box">
			<p><?php _e('Pflege hier die Kerndaten fuer Dein Branchenprofil. Die Werte werden als eigenes Gelbe-Seiten-Schema gespeichert.', $this->text_domain); ?></p>

			<div class="dr-modern-grid">
				<div class="dr-modern-field">
					<label for="dr_business_company"><?php _e('Firma', $this->text_domain); ?></label>
					<input type="text" id="dr_business_company" name="dr_business[company]" value="<?php echo esc_attr($profile['company']); ?>" class="regular-text" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_contact_name"><?php _e('Ansprechpartner', $this->text_domain); ?></label>
					<input type="text" id="dr_business_contact_name" name="dr_business[contact_name]" value="<?php echo esc_attr($profile['contact_name']); ?>" class="regular-text" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_phone"><?php _e('Telefon', $this->text_domain); ?></label>
					<input type="text" id="dr_business_phone" name="dr_business[phone]" value="<?php echo esc_attr($profile['phone']); ?>" class="regular-text dr-phone-mask" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_mobile"><?php _e('Mobil', $this->text_domain); ?></label>
					<input type="text" id="dr_business_mobile" name="dr_business[mobile]" value="<?php echo esc_attr($profile['mobile']); ?>" class="regular-text dr-phone-mask" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_email"><?php _e('E-Mail', $this->text_domain); ?></label>
					<input type="email" id="dr_business_email" name="dr_business[email]" value="<?php echo esc_attr($profile['email']); ?>" class="regular-text" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_website"><?php _e('Webseite', $this->text_domain); ?></label>
					<div class="dr-inline-controls">
						<input type="url" id="dr_business_website" name="dr_business[website]" value="<?php echo esc_attr($profile['website']); ?>" class="regular-text" placeholder="https://beispiel.de" />
						<button type="button" class="button" id="dr-verify-website"><?php _e('Webseite pruefen', $this->text_domain); ?></button>
					</div>
					<input type="hidden" id="dr_business_website_verified" name="dr_business[website_verified]" value="<?php echo !empty($profile['website_verified']) ? '1' : '0'; ?>" />
					<p class="description" id="dr-website-verify-state">
						<?php if (!empty($profile['website_verified']) && !empty($profile['website_verified_at'])): ?>
							<?php echo esc_html(sprintf(__('Zuletzt geprueft am %s', $this->text_domain), $profile['website_verified_at'])); ?>
						<?php else: ?>
							<?php _e('Noch nicht geprueft.', $this->text_domain); ?>
						<?php endif; ?>
					</p>
				</div>
			</div>

			<div class="dr-modern-field">
				<label for="dr_business_opening_hours"><?php _e('Oeffnungszeiten', $this->text_domain); ?></label>
				<textarea id="dr_business_opening_hours" name="dr_business[opening_hours]" rows="4" class="large-text" placeholder="Mo-Fr 09:00-18:00&#10;Sa 10:00-14:00"><?php echo esc_textarea($profile['opening_hours']); ?></textarea>
			</div>

			<div class="dr-modern-grid">
				<div class="dr-modern-field">
					<label for="dr_business_street"><?php _e('Strasse und Hausnummer', $this->text_domain); ?></label>
					<input type="text" id="dr_business_street" name="dr_business[street]" value="<?php echo esc_attr($profile['street']); ?>" class="regular-text" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_postal_code"><?php _e('PLZ', $this->text_domain); ?></label>
					<input type="text" id="dr_business_postal_code" name="dr_business[postal_code]" value="<?php echo esc_attr($profile['postal_code']); ?>" class="regular-text" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_city"><?php _e('Stadt', $this->text_domain); ?></label>
					<input type="text" id="dr_business_city" name="dr_business[city]" value="<?php echo esc_attr($profile['city']); ?>" class="regular-text" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_country"><?php _e('Land', $this->text_domain); ?></label>
					<input type="text" id="dr_business_country" name="dr_business[country]" value="<?php echo esc_attr($profile['country']); ?>" class="regular-text" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_lat"><?php _e('Breitengrad (lat)', $this->text_domain); ?></label>
					<input type="text" id="dr_business_lat" name="dr_business[lat]" value="<?php echo esc_attr($profile['lat']); ?>" class="regular-text" placeholder="z. B. 52.520008" />
				</div>
				<div class="dr-modern-field">
					<label for="dr_business_lng"><?php _e('Laengengrad (lng)', $this->text_domain); ?></label>
					<div class="dr-inline-controls">
						<input type="text" id="dr_business_lng" name="dr_business[lng]" value="<?php echo esc_attr($profile['lng']); ?>" class="regular-text" placeholder="z. B. 13.404954" />
						<button type="button" class="button" id="dr-open-map-preview"><?php _e('Standort pruefen', $this->text_domain); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	* Save modern listing profile data.
	*
	* @param int     $post_id Post ID.
	* @param WP_Post $post Post object.
	* @return void
	*/
	function save_listing_meta_boxes( $post_id, $post ) {
		if ( empty($post) || 'directory_listing' !== $post->post_type ) {
			return;
		}

		if ( !isset($_POST['dr_listing_business_profile_nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['dr_listing_business_profile_nonce']) ), 'dr_listing_business_profile' ) ) {
			return;
		}

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return;
		}

		if ( !current_user_can('edit_post', $post_id) ) {
			return;
		}

		$submitted = isset($_POST['dr_business']) ? wp_unslash($_POST['dr_business']) : array();
		$submitted = is_array($submitted) ? $submitted : array();

		$profile = array(
			'company' => isset($submitted['company']) ? sanitize_text_field($submitted['company']) : '',
			'contact_name' => isset($submitted['contact_name']) ? sanitize_text_field($submitted['contact_name']) : '',
			'phone' => isset($submitted['phone']) ? preg_replace('/[^0-9+\-()\s\/]/', '', (string)$submitted['phone']) : '',
			'mobile' => isset($submitted['mobile']) ? preg_replace('/[^0-9+\-()\s\/]/', '', (string)$submitted['mobile']) : '',
			'email' => isset($submitted['email']) ? sanitize_email($submitted['email']) : '',
			'website' => isset($submitted['website']) ? esc_url_raw($submitted['website']) : '',
			'opening_hours' => isset($submitted['opening_hours']) ? sanitize_textarea_field($submitted['opening_hours']) : '',
			'street' => isset($submitted['street']) ? sanitize_text_field($submitted['street']) : '',
			'postal_code' => isset($submitted['postal_code']) ? sanitize_text_field($submitted['postal_code']) : '',
			'city' => isset($submitted['city']) ? sanitize_text_field($submitted['city']) : '',
			'country' => isset($submitted['country']) ? sanitize_text_field($submitted['country']) : '',
			'lat' => isset($submitted['lat']) ? sanitize_text_field($submitted['lat']) : '',
			'lng' => isset($submitted['lng']) ? sanitize_text_field($submitted['lng']) : '',
			'website_verified' => (!empty($submitted['website_verified']) ? 1 : 0),
			'website_verified_at' => '',
		);

		$old = get_post_meta($post_id, '_dr_business_profile', true);
		$old = is_array($old) ? $old : array();

		if (!empty($profile['website_verified'])) {
			$profile['website_verified_at'] = current_time('mysql');
		} elseif (!empty($old['website_verified_at'])) {
			$profile['website_verified_at'] = $old['website_verified_at'];
		}

		$category_slugs = wp_get_post_terms($post_id, 'listing_category', array('fields' => 'slugs'));
		$category_slugs = is_wp_error($category_slugs) ? array() : $category_slugs;

		$errors = $this->validate_listing_business_profile($profile, $category_slugs);
		if (!empty($errors)) {
			update_post_meta($post_id, '_dr_business_profile_errors', $errors);

			if ('publish' === get_post_status($post_id)) {
				remove_action( 'save_post_directory_listing', array( &$this, 'save_listing_meta_boxes' ), 10 );
				wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));
				add_action( 'save_post_directory_listing', array( &$this, 'save_listing_meta_boxes' ), 10, 2 );
			}
		} else {
			delete_post_meta($post_id, '_dr_business_profile_errors');
		}

		update_post_meta($post_id, '_dr_business_profile', $profile);
	}

	/**
	* Validation rules for listing profiles.
	*
	* @param array $category_slugs Category slugs.
	* @return array
	*/
	function get_listing_business_profile_rules($category_slugs = array()) {
		$rules = array(
			'company' => __('Bitte gib die Firma an.', $this->text_domain),
			'phone' => __('Bitte gib eine Telefonnummer an.', $this->text_domain),
			'city' => __('Bitte gib eine Stadt an.', $this->text_domain),
		);

		$category_slugs = is_array($category_slugs) ? $category_slugs : array();
		$category_slugs = array_map('sanitize_title', $category_slugs);

		$category_requirements = array(
			'gastronomie' => array(
				'opening_hours' => __('Bitte hinterlege fuer Gastronomie die Oeffnungszeiten.', $this->text_domain),
			),
			'restaurant' => array(
				'opening_hours' => __('Bitte hinterlege fuer Restaurants die Oeffnungszeiten.', $this->text_domain),
			),
			'arzt' => array(
				'street' => __('Bitte gib fuer Arztprofile die Strasse an.', $this->text_domain),
				'postal_code' => __('Bitte gib fuer Arztprofile die PLZ an.', $this->text_domain),
			),
			'aerzte' => array(
				'street' => __('Bitte gib fuer Aerzte die Strasse an.', $this->text_domain),
				'postal_code' => __('Bitte gib fuer Aerzte die PLZ an.', $this->text_domain),
			),
		);

		foreach ($category_requirements as $slug => $fields) {
			if (in_array($slug, $category_slugs, true)) {
				$rules = array_merge($rules, $fields);
			}
		}

		return $rules;
	}

	/**
	* Validates listing profile data.
	*
	* @param array $profile Profile values.
	* @param array $category_slugs Category slugs.
	* @return array
	*/
	function validate_listing_business_profile($profile, $category_slugs = array()) {
		$profile = is_array($profile) ? $profile : array();
		$rules = $this->get_listing_business_profile_rules($category_slugs);
		$errors = array();

		foreach ($rules as $field => $message) {
			$value = isset($profile[$field]) ? trim((string)$profile[$field]) : '';
			if ($value === '') {
				$errors[] = $message;
			}
		}

		if (!empty($profile['email']) && !is_email($profile['email'])) {
			$errors[] = __('Bitte gib eine gueltige E-Mail-Adresse an.', $this->text_domain);
		}

		if (!empty($profile['website']) && !wp_http_validate_url($profile['website'])) {
			$errors[] = __('Bitte gib eine gueltige Webseite mit http:// oder https:// an.', $this->text_domain);
		}

		return array_values(array_unique($errors));
	}

	/**
	* Shows validation notice on listing edit screen.
	*
	* @return void
	*/
	function render_listing_profile_validation_notice() {
		$screen = function_exists('get_current_screen') ? get_current_screen() : null;
		if (empty($screen) || !isset($screen->post_type) || 'directory_listing' !== $screen->post_type) {
			return;
		}

		$post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
		if ($post_id <= 0) {
			return;
		}

		$errors = get_post_meta($post_id, '_dr_business_profile_errors', true);
		if (empty($errors) || !is_array($errors)) {
			return;
		}

		echo '<div class="notice notice-error"><p><strong>' . esc_html__('Bitte vervollstaendige Dein Branchenprofil:', $this->text_domain) . '</strong></p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . esc_html($error) . '</li>';
		}
		echo '</ul><p>' . esc_html__('Solange Pflichtfelder fehlen, bleibt der Eintrag als Entwurf gespeichert.', $this->text_domain) . '</p></div>';
	}
}

/* Initiate Admin */
if(is_admin()){
	global $Directory_Core;
	$Directory_Core = new Directory_Core_Admin();
}

endif;



