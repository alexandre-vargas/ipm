$(document).ready(function() { 
	
	$('#armazenar').click(function(){
		$.ajax({
			type: 'post',
			data: 'inscricao_estadual=' + $('#inscricao_estadual').val(),
			url: 'cadastro/recuperar-contribuinte-ajax',
			success: function(retorno) {
				$( '#conteudo' ).html(retorno);
			}
		})
		return false;
	})

});