(function ($) {
$(function () {

/**
 * Handler tutorial resets.
 */
$(".dr-restart_tutorial").on('click', function () {
	var $me = $(this);
	// Change UI
	$me.after(
		'<img src="' + _dr_data.root_url + 'ui-admin/images/ajax-loader.gif" />'
	).remove();
	// Do call
	$.post(ajaxurl, {
		"action": "dr_restart_tutorial",
		"tutorial": $me.attr("data-dr_tutorial"),
	}, function () {
		window.location.reload();
	});
	return false;
});

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

$('#dr-verify-website').on('click', function () {
	var $website = $('#dr_business_website');
	var $state = $('#dr-website-verify-state');
	var normalized = normalizeUrl($website.val());
	if (!normalized) {
		alert((window.drListingAdmin && drListingAdmin.badUrlText) || 'Bitte gib eine gueltige URL ein.');
		$website.focus();
		return;
	}

	try {
		var parsed = new URL(normalized);
		if (!parsed.hostname) {
			throw new Error('invalid');
		}
		$website.val(parsed.toString());
		$('#dr_business_website_verified').val('1');
		window.open(parsed.toString(), '_blank', 'noopener,noreferrer');
		$state.text((window.drListingAdmin && drListingAdmin.verifiedText) || 'URL geprueft.');
	} catch (e) {
		$('#dr_business_website_verified').val('0');
		alert((window.drListingAdmin && drListingAdmin.badUrlText) || 'Bitte gib eine gueltige URL ein.');
		$website.focus();
	}
});

$('#dr-open-map-preview').on('click', function () {
	var lat = $.trim($('#dr_business_lat').val());
	var lng = $.trim($('#dr_business_lng').val());
	var street = $.trim($('#dr_business_street').val());
	var postal = $.trim($('#dr_business_postal_code').val());
	var city = $.trim($('#dr_business_city').val());
	var country = $.trim($('#dr_business_country').val());
	var url = '';

	if (lat && lng) {
		url = 'https://www.openstreetmap.org/?mlat=' + encodeURIComponent(lat) + '&mlon=' + encodeURIComponent(lng) + '#map=17/' + encodeURIComponent(lat) + '/' + encodeURIComponent(lng);
	} else {
		var query = [street, postal, city, country].filter(Boolean).join(', ');
		if (!query) {
			alert('Bitte gib zuerst eine Adresse oder Koordinaten ein.');
			return;
		}
		url = 'https://www.openstreetmap.org/search?query=' + encodeURIComponent(query);
	}

	window.open(url, '_blank', 'noopener,noreferrer');
});

});
})(jQuery);
