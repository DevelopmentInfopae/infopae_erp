$(document).ready(function(){
	$('.dataTables-sedes tbody tr').click(function(){
		var aux = $(this).attr('numDoc');
		$('#verTitular #numDoc').val(aux);
		aux = $(this).attr('tipoDoc');
		$('#verTitular #tipoDoc').val(aux);
		$('#verTitular').submit();
	});
});
