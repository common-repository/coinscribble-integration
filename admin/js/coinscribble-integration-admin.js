(function( $ ) {

	$(document).ready(function () {
		$('#coinscribbleCategorizationSettings select').each(function () {
			const id = $(this).data('value');
			if (id !== '') {
				const optionToSelect = $(this).find('option[value="'+ id +'"]')[0];
				$(optionToSelect).attr('selected', 'selected');
			}
		})
	})

	$(document).on('submit', '#coinscribbleTokenSettings', (function (e) {
		e.preventDefault();
		sendAjax('POST', 'coinscribbleTokenSettings', function (response) {
			['status_2', 'status_1', 'status_0'].forEach(function (item) {
				$('#coinscribbleTokenSettings #status').removeClass(item)
			})
			if(response.success) {
				$('#coinscribblePaymentMethods').show();
				$('#coinscribbleCategorizationSettings').show();
				alert(response.data.message);
				$.each( response.data.status, function( key, value ) {
					$('#coinscribbleTokenSettings #status').addClass( 'status_' + key).html(value)
				});
			} else {
				$('#coinscribblePaymentMethods').hide();
				$('#coinscribbleCategorizationSettings').hide();
				alert(response.data.error)
				$('#coinscribbleTokenSettings #status').addClass( 'status_2' ).html(coinscribbleJsObject.statuses.failed)
			}
		});
	}));

	$(document).on('submit', '#coinscribbleCategorizationSettings', (function (e) {
		e.preventDefault();
		sendAjax('POST', 'coinscribbleCategorizationSettings', function (response) {
			if (response.success) {
				alert(response.data.message);
				$('#coinscribbleCategorizationSettings #status').html(response.data.status);
			} else {
				alert(response.data.error)
			}
		});
	}));

	$(document).on('submit', '#coinscribbleUpdateTranactions', (function (e) {
		e.preventDefault();
		sendAjax('POST', 'coinscribbleUpdateTranactions', function () {
			window.location.reload();
		});
	}))

	$(document).on('submit', '#coinscribblePaymentMethods', (function (e) {
		e.preventDefault();
		sendAjax('POST', 'coinscribblePaymentMethods', function (response) {
			if (response.success == false) {
				alert(response.data.error);
			}
		});
	}))

	$(document).on('change', '#coinscribbleSelectPaymentMethod', function() {
		const placeholder = $($('#coinscribbleSelectPaymentMethod option[value="' + this.value + '"]')[0]).data('placeholder');
		$('#coinscribblePaymentAdditionalInfo').attr('placeholder', placeholder);
		$('#coinscribblePaymentAdditionalInfoLabel').html(placeholder);
	});


	function sendAjax( method, formId, success_calback)
	{
		const form = new FormData(document.getElementById(formId));
		form.append('action', $("#" + formId).data('ajax_action'));

		$.ajax({
			method: method,
			url: $("#" + formId).attr('action'),
			contentType: false,
			processData: false,
			data: form,
			beforeSend : function (){
				add_loader(formId);
			},
			success : function(response) {
				if (typeof success_calback === 'function') {
					success_calback(response);
				}
			},
			error : function(response) {
				alert(response.errorMessage);
			},
		}).always(function (response) {
			stop_loader(formId);
		});
	}

	function add_loader(formId) {
		$('#' + formId + ' .coinscribble-overlay').fadeIn(300);
		$('#' + formId + ' .coinscribble-loader').fadeIn(300);
	}

	function stop_loader(formId) {
		$('#' + formId + ' .coinscribble-overlay').fadeOut(300);
		$('#' + formId + ' .coinscribble-loader').fadeOut(300);
	}

})( jQuery );
