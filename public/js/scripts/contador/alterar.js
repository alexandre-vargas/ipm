$(document).ready(function () {
	$(function(){
		window.prettyPrint && prettyPrint();
		$('#data_crc').datepicker({
			format: 'dd/mm/yyyy'
		});
	});

	$('input[name="data_crc"]').mask('99/99/9999');
	
});
