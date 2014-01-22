$(document).ready(function() {
	$('#pesquisar_contribuinte').click(function(){
		$.ajax({
			type: 'post',
			url: 'contribuinte/listagem/ajax/1',
			success: function(retorno) {
				bootbox.dialog(
						retorno,
						[{
							"label" : "Voltar",
							"class" : "btn btn-primary",
							"callback": function() {
							}
						}]
					);		
			}
		});
		return false;
	});
});