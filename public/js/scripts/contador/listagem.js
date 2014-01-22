function excluir(intId, strNomeRazaoSocial) {
	bootbox.confirm("Deseja realmente excluir o contador: " + strNomeRazaoSocial + " ?", function(result) {
		if(result == true)
			document.location.href = 'contador/excluir/id/' + intId;
	});
}