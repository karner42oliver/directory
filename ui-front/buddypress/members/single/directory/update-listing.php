<?php

/**
* The template for displaying the Add/edit listing page.
* You can override this file in your active theme.
*
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

global $post, $post_ID;
$listing_data   = '';
$selected_cats  = '';
$error = isset( $dr_error ) ? $dr_error : ''; // get_query_var('dr_error');

$options = $this->get_options('general');

$post_statuses = get_post_statuses(); // get the wp post status list
$allowed_statuses = $this->get_options('general'); // Get the ones we allow
$allowed_statuses['moderation'] = (empty($allowed_statuses['moderation']) ) ? array('publish' => 1, 'draft'=> 1 ) : $allowed_statuses['moderation']; // Get the ones we allow
$allowed_statuses = array_reverse(array_intersect_key($post_statuses, $allowed_statuses['moderation']) ); //return the reduced list

//Are we adding a Listing?
if(! isset($_REQUEST['post_id']) ){
	//Make an auto-draft so we have a post id to connect attachemnts to. Set global $post_id so media editor can hook up.
	$post_ID = wp_insert_post( array( 'post_title' => __( 'Automatischer Entwurf' ), 'post_type' => $this->post_type, 'post_status' => 'auto-draft', 'comment_status' => 'closed', 'ping_status' => 'closed' ) );
	$listing_data = get_post($post_ID, ARRAY_A );
	$listing_data['post_title'] = ''; //Have to have a title to insert the auto-save but we don't want it as final.
	$editing = false;
}

//Or are we editing a listing?
if( isset($_REQUEST['post_id']) ){
	$listing_data = get_post( absint( $_REQUEST['post_id'] ), ARRAY_A );
	$post_ID = $listing_data['ID'];
	$editing = true;
}
$post = get_post($post_ID);

if ( isset( $_POST['listing_data'] ) ) $listing_data = $_POST['listing_data'];

require_once(ABSPATH . 'wp-admin/includes/template.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/post.php');

$editor_settings =   array(
'wpautop' => true, // use wpautop?
'media_buttons' => true, // show insert/upload button(s)
'textarea_name' => 'listing_data[post_content]', // set the textarea name to something different, square brackets [] can be used here
'textarea_rows' => 10, //get_option('default_post_edit_rows', 10), // rows="..."
'tabindex' => '',
'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
'editor_class' => '', // add extra class(es) to the editor textarea
'teeny' => false, // output the minimal editor config used in Press This
'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
);

$listing_content = (empty( $listing_data['post_content'] ) ) ? '' : $listing_data['post_content'];

$dr_profile_defaults = array(
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
);
$dr_profile = get_post_meta( isset($listing_data['ID']) ? intval($listing_data['ID']) : 0, '_dr_business_profile', true );
$dr_profile = is_array($dr_profile) ? array_merge($dr_profile_defaults, $dr_profile) : $dr_profile_defaults;
if (isset($_POST['dr_business']) && is_array($_POST['dr_business'])) {
	$dr_profile = array_merge($dr_profile, wp_unslash($_POST['dr_business']));
}
wp_enqueue_script('set-post-thumbnail');

?>
<script type="text/javascript" src="<?php echo $this->plugin_url . 'ui-front/js/jquery.tagsinput.min.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo $this->plugin_url . 'ui-front/js/media-post.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo $this->plugin_url . 'ui-front/js/ui-front.js'; ?>" >
</script>

<?php if ( !empty( $error ) ): ?>
<br /><div class="error"><?php echo $error . '<br />'; ?></div>
<?php endif; ?>

<div class="dr_update_form">

	<form class="standard-form base" method="post" action="#" enctype="multipart/form-data" id="dr_update_form" >
		<input type="hidden" id="post_ID" name="listing_data[ID]" value="<?php echo ( isset( $listing_data['ID'] ) ) ? $listing_data['ID'] : ''; ?>" />
		<input type="hidden" name="post_id" value="<?php echo ( isset( $listing_data['ID'] ) ) ? $listing_data['ID'] : ''; ?>" />

		<?php if(post_type_supports('directory_listing','title') ): ?>
		<div class="editfield">
			<label for="title"><?php _e( 'Titel', $this->text_domain ); ?></label><br />
			<input class="required" type="text" id="title" name="listing_data[post_title]" value="<?php echo ( isset( $listing_data['post_title'] ) ) ? $listing_data['post_title'] : ''; ?>" />
			<p class="description"><?php _e( 'Gib hier den Titel ein.', $this->text_domain ); ?></p>
		</div>
		<?php endif; ?>

		<?php if(post_type_supports('directory_listing','thumbnail') && current_theme_supports('post-thumbnails') ): ?>
		<div class="editfield">
			<?php if(empty($options['media_manager']) ): ?>

			<?php if(has_post_thumbnail()) the_post_thumbnail('thumbnail'); ?><br />
			<script type="text/javascript">js_translate.image_chosen = '<?php _e("Eintrags-Bild ausgewählt", $this->text_domain); ?>';</script>
			<span class="upload-button">

				<?php $class = ( empty($options['field_image_req']) && !has_post_thumbnail() ) ? 'required' : ''; ?>

				<input type="file" name="feature_image" size="1" id="image" class="<?php echo $class; ?>" />
				<button type="button" class="button"><?php _e('Eintrags-Bild einstellen', $this->text_domain); ?></button>
			</span>
			<br />

			<?php else: ?>

			<div id="postimagediv">
				<div class="inside">
					<?php $thumbnail_id = get_post_meta( $post_ID, '_thumbnail_id', true ); ?>
					<?php echo _wp_post_thumbnail_html($thumbnail_id, $post_ID); ?>
				</div>
			</div>
			<?php endif; ?>

		</div>
		<?php endif; ?>

		<?php if(post_type_supports('directory_listing','editor') ): ?>
		<label for="listingcontent"><?php _e( 'Inhalt', $this->text_domain ); ?></label><br />

		<?php if (version_compare(get_bloginfo('version'), '3.3', '>=')): ?>


		<?php wp_editor( $listing_content, 'listingcontent', $editor_settings); ?>

		<?php else: ?>

		<textarea id="listingcontent" name="listing_data[post_content]" cols="40" rows="5"><?php echo esc_textarea($listing_content); ?></textarea>

		<?php endif; ?>

		<p class="description"><?php _e( 'Der Inhalt Deines Eintrags.', $this->text_domain ); ?></p>
		<?php endif; ?>

		<?php if(post_type_supports('directory_listing','excerpt') ): ?>
		<div class="editfield alt">
			<label for="excerpt"><?php _e( 'Auszug', $this->text_domain ); ?></label><br />
			<textarea id="excerpt" name="listing_data[post_excerpt]" rows="2" ><?php echo (isset( $listing_data['post_excerpt'] ) ) ? esc_textarea($listing_data['post_excerpt']) : ''; ?></textarea>
			<p class="description"><?php _e( 'Ein kurzer Auszug Deines Eintrags.', $this->text_domain ); ?></p>
		</div>
		<?php endif; ?>

		<div class="dr-business-profile">
			<h3><?php _e('Branchenprofil', $this->text_domain); ?></h3>
			<div class="dr-modern-grid">
				<div class="editfield"><label for="dr_business_company"><?php _e('Firma', $this->text_domain); ?></label><input type="text" id="dr_business_company" name="dr_business[company]" value="<?php echo esc_attr($dr_profile['company']); ?>" /></div>
				<div class="editfield"><label for="dr_business_contact_name"><?php _e('Ansprechpartner', $this->text_domain); ?></label><input type="text" id="dr_business_contact_name" name="dr_business[contact_name]" value="<?php echo esc_attr($dr_profile['contact_name']); ?>" /></div>
				<div class="editfield"><label for="dr_business_phone"><?php _e('Telefon', $this->text_domain); ?></label><input type="text" class="dr-phone-mask" id="dr_business_phone" name="dr_business[phone]" value="<?php echo esc_attr($dr_profile['phone']); ?>" /></div>
				<div class="editfield"><label for="dr_business_mobile"><?php _e('Mobil', $this->text_domain); ?></label><input type="text" class="dr-phone-mask" id="dr_business_mobile" name="dr_business[mobile]" value="<?php echo esc_attr($dr_profile['mobile']); ?>" /></div>
				<div class="editfield"><label for="dr_business_email"><?php _e('E-Mail', $this->text_domain); ?></label><input type="email" id="dr_business_email" name="dr_business[email]" value="<?php echo esc_attr($dr_profile['email']); ?>" /></div>
				<div class="editfield"><label for="dr_business_website"><?php _e('Webseite', $this->text_domain); ?></label><div class="dr-inline-controls"><input type="url" class="dr-business-website" id="dr_business_website" name="dr_business[website]" value="<?php echo esc_attr($dr_profile['website']); ?>" placeholder="https://beispiel.de" /><button type="button" class="button dr-verify-website"><?php _e('Webseite pruefen', $this->text_domain); ?></button></div><p class="description dr-website-verify-state"><?php _e('Noch nicht geprueft.', $this->text_domain); ?></p></div>
			</div>
			<div class="editfield"><label for="dr_business_opening_hours"><?php _e('Oeffnungszeiten', $this->text_domain); ?></label><textarea id="dr_business_opening_hours" name="dr_business[opening_hours]" rows="3"><?php echo esc_textarea($dr_profile['opening_hours']); ?></textarea></div>
			<div class="dr-modern-grid">
				<div class="editfield"><label for="dr_business_street"><?php _e('Strasse und Hausnummer', $this->text_domain); ?></label><input type="text" id="dr_business_street" name="dr_business[street]" value="<?php echo esc_attr($dr_profile['street']); ?>" /></div>
				<div class="editfield"><label for="dr_business_postal_code"><?php _e('PLZ', $this->text_domain); ?></label><input type="text" id="dr_business_postal_code" name="dr_business[postal_code]" value="<?php echo esc_attr($dr_profile['postal_code']); ?>" /></div>
				<div class="editfield"><label for="dr_business_city"><?php _e('Stadt', $this->text_domain); ?></label><input type="text" id="dr_business_city" name="dr_business[city]" value="<?php echo esc_attr($dr_profile['city']); ?>" /></div>
				<div class="editfield"><label for="dr_business_country"><?php _e('Land', $this->text_domain); ?></label><input type="text" id="dr_business_country" name="dr_business[country]" value="<?php echo esc_attr($dr_profile['country']); ?>" /></div>
				<div class="editfield"><label for="dr_business_lat"><?php _e('Breitengrad (lat)', $this->text_domain); ?></label><input type="text" id="dr_business_lat" name="dr_business[lat]" value="<?php echo esc_attr($dr_profile['lat']); ?>" /></div>
				<div class="editfield"><label for="dr_business_lng"><?php _e('Laengengrad (lng)', $this->text_domain); ?></label><div class="dr-inline-controls"><input type="text" class="dr-business-lng" id="dr_business_lng" name="dr_business[lng]" value="<?php echo esc_attr($dr_profile['lng']); ?>" /><button type="button" class="button dr-open-map-preview"><?php _e('Standort pruefen', $this->text_domain); ?></button></div></div>
			</div>
			<input type="hidden" class="dr-business-website-verified" name="dr_business[website_verified]" value="<?php echo !empty($dr_profile['website_verified']) ? '1' : '0'; ?>" />
		</div>

		<?php
		//get related hierarchical taxonomies
		$taxonomies = get_object_taxonomies('directory_listing', 'objects');
		$taxonomies = empty($taxonomies) ? array() : $taxonomies;

		//Loop through the taxonomies that apply
		foreach($taxonomies as $taxonomy):
		if( ! $taxonomy->hierarchical) continue;
		$tax_name = $taxonomy->name;
		$labels = $taxonomy->labels;
		//Get this Taxonomies terms
		$selected_cats = array_values( wp_get_post_terms($listing_data['ID'], $tax_name, array('fields' => 'ids') ) );


		?>

		<div id="taxonomy-<?php echo $tax_name; ?>" class="dr_taxonomydiv">
			<label><?php echo $labels->all_items; ?></label>

			<div id="<?php echo $tax_name; ?>_all" class="dr_tax_panel">
				<?php
				$name = ( $tax_name == 'category' ) ? 'post_category' : 'tax_input[' . $tax_name . ']';
				echo "<input type='hidden' name='{$name}[]' value='0' />"; 		// Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
				?>
				<ul id="<?php echo $tax_name; ?>_checklist" class="list:<?php echo $labels->name; ?> categorychecklist form-no-clear">
					<?php wp_terms_checklist( 0, array( 'taxonomy' => $tax_name, 'selected_cats' => $selected_cats, 'checked_ontop' => false ) ) ?>
				</ul>
			</div>
			<span class="description"><?php echo $labels->add_or_remove_items; ?></span>
		</div>
		<?php endforeach; ?>

		<?php
		//get related non-hierarchical taxonomies

		//Loop through the taxonomies that apply
		foreach($taxonomies as $tag):
		if( $tag->hierarchical) continue;

		$tag_name = $tag->name;
		$labels = $tag->labels;

		//Get this Taxonomies terms
		$tag_list = strip_tags(get_the_term_list( $listing_data['ID'], $tag_name, '', ',', '' ));

		?>

		<div class="dr_taxonomydiv">
			<div id="<?php echo $tag_name; ?>-checklist" class="tagchecklist">
				<label><?php echo $labels->name; ?></label>
				<input id="tag_<?php echo $tag_name; ?>" name="tag_input[<?php echo $tag_name; ?>]" type="text" value="<?php echo $tag_list?>" />

			</div>
			<span class="description"><?php echo $labels->add_or_remove_items; ?></span>
		</div>
		<script type="text/javascript" > jQuery('#tag_<?php echo $tag_name; ?>').tagsInput({width:'auto', height:'150px'}); </script>
		<?php endforeach; ?>

		<div class="clear"><br /></div>

		<div class="editfield" >
			<label for="title"><?php _e( 'Status', $this->text_domain ); ?></label>
			<div id="status-box">
				<select name="listing_data[post_status]" id="listing_data[post_status]">
					<?php
					foreach($allowed_statuses as $key => $value): ?>

					<option value="<?php echo $key; ?>" <?php selected( ! empty($listing_data['post_status'] ) && $key == $listing_data['post_status'] ); ?> ><?php echo $value; ?></option>

					<?php endforeach; ?>
				</select>
			</div>
			<p class="description"><?php _e( 'Wähle einen Status für Deinen Eintrag.', $this->text_domain ); ?></p>
		</div>

		<?php if ( !empty( $error ) ): ?>
		<br /><div class="error"><?php echo $error . '<br />'; ?></div>
		<?php endif; ?>

		<div class="submit">
			<?php wp_nonce_field( 'verify' ); ?>
			<input type="submit" value="<?php _e( 'Änderungen speichern', $this->text_domain ); ?>" name="update_listing">

			<input type="button" value="<?php _e( 'Abbrechen', $this->text_domain ); ?>" onclick="location.href='<?php echo get_permalink($this->my_listings_page_id); ?>'">
		</div>

		<?php //echo do_shortcode('[ct_validate]') ; ?>
	</form>
</div>
