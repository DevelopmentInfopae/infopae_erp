$(document).ready(function(){
	$('#btnBuscar').click(function(){
		buscarPreparacion();
	});	
});

function  buscarPreparacion(){
	if($('#formParametros').valid()){

		console.log("Buscar preparación");

		var formData = new FormData();

		formData.append('preparacion', $('#preparaciones').val());

		$.ajax({
			type: "post",
			url: "functions/fn_buscar_preparacion.php",
			//dataType: "json",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				//console.log(data);
				$(".boxPreparacion").html(data);
				$('#loader').fadeOut();
				inicializarFunciones();
			},
			error: function(data){
				console.log(data);
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
			}
		});
	}
}

function inicializarFunciones(){
	
	cargarOpcionesDeProducto();
   
	$('.productoFichaTecnicaDet select').select2({width : "100%"});

}

function cargarOpcionesDeProducto(){
	var valor = "";
	console.log("Cargar opciones de prodcuto");
	$( ".productoFichaTecnicaDetA select" ).each(function() {
  		valor = $( this ).val();
  		console.log(valor);
  		var data = '<option value="123">Hola</option>';
  		$( this ).append(data);
	});
}