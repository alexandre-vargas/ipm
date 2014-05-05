$(document).ready(function() {
	var updateBoxPosition = function() {
		$('.signin-container').css({
			'margin-top': ($(window).height() - $('.signin-container').height() - 120) / 2
		});
	};
	$(window).resize(updateBoxPosition);
	setTimeout(updateBoxPosition, 50);

	$('#usuario').keypress(verificaNumero);

});
