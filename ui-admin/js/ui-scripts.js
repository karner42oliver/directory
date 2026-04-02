jQuery(function($) {

	function populate_checkboxes() {
		if ($('#roles').length) {
			$('#ajax-loader').show();
			// clear checked fields
			$('#capabilities input').prop('checked', false);
			// set data
			var data = {
				action: 'dr_get_caps',
				role: $('#roles').val(),
				nonce: (typeof drAdmin !== 'undefined') ? drAdmin.nonce : ''
			};
			// make the post request and process the response
			$.post(ajaxurl, data)
				.done(function(response) {
					$('#ajax-loader').hide();
					$.each(response, function(index, value) {
						$('input[name="capabilities[' + index + ']"]').prop('checked', value);
					});
				})
				.fail(function() {
					console.error('Failed to retrieve capabilities.');
				});
		}
	}

	populate_checkboxes();

	$('#roles').on('change', populate_checkboxes);

	$('.dr-general').on('submit', function(e) {
		e.preventDefault();
		$('#ajax-loader').show();
		var data = $(this).serialize();
		$.post(ajaxurl, data)
			.done(function() {
				$('#ajax-loader').hide();
			})
			.fail(function() {
				console.error('Failed to submit form.');
			});
	});

});


