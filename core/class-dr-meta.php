<?php

class DR_Meta{

	protected $_meta = null;

	public $post_id = 0;
	public $text_domain = DR_TEXT_DOMAIN;
	public $options_name = DR_OPTIONS_NAME;
	public $plugin_dir = DR_PLUGIN_DIR;
	public $plugin_url = DR_PLUGIN_URL;
	public $meta_name = '_dr_meta';

	private $struc = array(
	'status' => 'unpaid',
	'expires' => 0,
	'business_profile' => array(
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
	),

	);

	function __construct($id = 0){
		global $post;

		$this->post_id = intval($id);
		if (empty($this->post_id) && isset($post) && $post instanceof WP_Post) {
			$this->post_id = intval($post->ID);
		}

		if (empty($this->post_id)) {
			return;
		}

		$this->_meta = get_post_meta( $this->post_id, $this->meta_name, true);

		if( empty($this->_meta) ) {
			$this->_meta = $this->struc;
			update_post_meta($this->post_id, $this->meta_name, $this->_meta);
		} elseif (is_array($this->_meta)) {
			$this->_meta = array_merge($this->struc, $this->_meta);
			if (!isset($this->_meta['business_profile']) || !is_array($this->_meta['business_profile'])) {
				$this->_meta['business_profile'] = $this->struc['business_profile'];
			} else {
				$this->_meta['business_profile'] = array_merge($this->struc['business_profile'], $this->_meta['business_profile']);
			}
			update_post_meta($this->post_id, $this->meta_name, $this->_meta);
		}
	}

	function __get(  $property = '' ){
		if (empty($this->post_id)) {
			return null;
		}

		$this->_meta = get_post_meta( $this->post_id, $this->meta_name, true);
		$this->_meta = is_array($this->_meta) ? $this->_meta : $this->struc;

		switch( $property ) {
			case 'meta' : return $this->_meta; break;
			case 'status' : return isset($this->_meta['status']) ? $this->_meta['status'] : $this->struc['status']; break;
			case 'expires' : return isset($this->_meta['expires']) ? $this->_meta['expires'] : $this->struc['expires']; break;
			case 'business_profile' :
				$profile = isset($this->_meta['business_profile']) && is_array($this->_meta['business_profile']) ? $this->_meta['business_profile'] : array();
				return array_merge($this->struc['business_profile'], $profile);
			break;
		}

	}

	function __set($property, $value){
		if (empty($this->post_id)) {
			return;
		}

		$this->_meta = get_post_meta( $this->post_id, $this->meta_name, true);
		$this->_meta = is_array($this->_meta) ? array_merge($this->struc, $this->_meta) : $this->struc;

		switch( $property ) {
			case 'meta' : $this->_meta = $value; break;
			case 'status' : $this->_meta['status'] = $value; break;
			case 'expires' : $this->_meta['expires'] = $value; break;
			case 'business_profile' :
				$value = is_array($value) ? $value : array();
				$this->_meta['business_profile'] = array_merge($this->struc['business_profile'], $value);
			break;
		}

		update_post_meta($this->post_id, $this->meta_name, $this->_meta);
	}

	function __isset($property){
		return ( in_array($property, array(
		'status',
		'expires',
		'business_profile',
		)
		) );
	}

}
