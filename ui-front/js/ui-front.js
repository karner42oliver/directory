jQuery(document).ready(function($) {
	$('form.confirm-form').hide();
	$('form.dr-contact-form').hide();
});

var dr_listings = {
	edit: function( key ) {
		jQuery( '#action-form' ).attr( 'action', dr_edit );
		jQuery( '#action-form input[name="action"]' ).val( 'edit_listing' );
		jQuery( '#action-form input[name="post_id"]' ).val( key );
		jQuery( '#action-form' ).trigger('submit');
	},
	toggle_delete: function( key ) {
		jQuery( '#delete-confirm-' + key ).parent().find( 'span' ).hide();
		jQuery( '#delete-confirm-' + key ).show();
	},
	toggle_delete_yes: function( key ) {
		jQuery( '#action-form input[name="action"]' ).val( 'delete_listing' );
		jQuery( '#action-form input[name="post_id"]' ).val( key );
		jQuery( '#action-form' ).trigger('submit');

	},
	toggle_delete_no: function( key ) {
		jQuery( '#delete-confirm-' + key ).parent().find( 'span' ).show();
		jQuery( '#delete-confirm-' + key ).hide();

	},
	toggle_contact_form: function() {
		jQuery('.dr-ad-info').hide();
		jQuery('#action-form').hide();
		jQuery('#confirm-form').show();
	},
	cancel_contact_form: function() {
		jQuery('#confirm-form').hide();
		jQuery('.dr-ad-info').show();
		jQuery('#action-form').show();
	},
	cancel: function(key) {
		jQuery('#confirm-form-'+key).hide();
		jQuery('#action-form-'+key).show();
	}
	
};

var js_translate = js_translate || {};
js_translate.image_chosen = 'Image Chosen';

(function($){

	jQuery(document).ready(function($) {
		$('.upload-button input:file').on('change focus click', fileInputs );
	});

	fileInputs = function() {
		var $this = $(this),
		$val = $this.val(),
		valArray = $val.split('\\'),
		newVal = valArray[valArray.length-1],
		$button = $this.siblings('.button'),
		$fakeFile = $this.siblings('.file-holder');
		if(newVal !== '') {
			$button.text(js_translate.image_chosen);
			if($fakeFile.length === 0) {
				$button.after('<span class="file-holder">' + newVal + '</span>');
			} else {
				$fakeFile.text(newVal);
			}
		}
	};

	function normalizeUrl(url) {
		if (!url) {
			return '';
		}
		var trimmed = String(url).trim();
		if (!/^https?:\/\//i.test(trimmed)) {
			trimmed = 'https://' + trimmed;
		}
		return trimmed;
	}

	function sanitizePhoneInput(value) {
		return String(value || '').replace(/[^0-9+\-()\s/]/g, '');
	}

	$(document).on('input', '.dr-phone-mask', function () {
		$(this).val(sanitizePhoneInput($(this).val()));
	});

	$(document).on('click', '.dr-verify-website', function () {
		var $button = $(this);
		var $container = $button.closest('.editfield');
		var $form = $button.closest('form');
		var $website = $container.find('.dr-business-website');
		var $state = $container.find('.dr-website-verify-state');
		var $verified = $form.find('.dr-business-website-verified');
		var normalized = normalizeUrl($website.val());

		if (!normalized) {
			$verified.val('0');
			$state.removeClass('is-ok').addClass('is-error').text('Bitte gib eine gueltige Webseite an.');
			$website.focus();
			return;
		}

		try {
			var parsed = new URL(normalized);
			if (!parsed.hostname) {
				throw new Error('invalid');
			}
			$website.val(parsed.toString());
			$verified.val('1');
			$state.removeClass('is-error').addClass('is-ok').text('Webseite geprueft. Ich habe sie in einem neuen Tab geoeffnet.');
			window.open(parsed.toString(), '_blank', 'noopener,noreferrer');
		} catch (e) {
			$verified.val('0');
			$state.removeClass('is-ok').addClass('is-error').text('Bitte gib eine gueltige Webseite mit http:// oder https:// an.');
			$website.focus();
		}
	});

	$(document).on('click', '.dr-open-map-preview', function () {
		var $button = $(this);
		var $form = $button.closest('form');
		var lat = $.trim($form.find('#dr_business_lat').val());
		var lng = $.trim($form.find('.dr-business-lng').val());
		var street = $.trim($form.find('#dr_business_street').val());
		var postal = $.trim($form.find('#dr_business_postal_code').val());
		var city = $.trim($form.find('#dr_business_city').val());
		var country = $.trim($form.find('#dr_business_country').val());
		var url = '';

		if (lat && lng) {
			url = 'https://www.openstreetmap.org/?mlat=' + encodeURIComponent(lat) + '&mlon=' + encodeURIComponent(lng) + '#map=17/' + encodeURIComponent(lat) + '/' + encodeURIComponent(lng);
		} else {
			var query = [street, postal, city, country].filter(Boolean).join(', ');
			if (!query) {
				return;
			}
			url = 'https://www.openstreetmap.org/search?query=' + encodeURIComponent(query);
		}

		window.open(url, '_blank', 'noopener,noreferrer');
	});

})(jQuery);

