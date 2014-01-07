$(document).ready(function () {
	$('#cpf_cnpj').keypress(verificaNumero);
	$('#cep').mask('99999-999');
	$('#numero').keypress(verificaNumero);
	$('#cnae').mask('9999/9-99');
	
});

function verificaNumero(e) {
	if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
	    return false;
	
}