jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

var datatables = null;

$(document).ready(function(){
	var d = new Date();
	var month = d.getMonth();
	var day = d.getDate();
	console.log("Hoy es "+day+" de "+month);	

	$('#btnBuscar').click(function(){
		$('#loader').fadeIn();
	

			if($('#form_asistencia').valid()){
				cargarEstudiantes();
			}


	});
});

function validarAsistenciaSellada(){
	console.log("Validación de sistencia Sellada");
	var formData = new FormData();
	formData.append('semanaActual', $('#semanaActual').val());
	formData.append('sede', $('#sede').val());
	$.ajax({
		type: "post",
		url: "functions/fn_validar_asistencia_no_sellada.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			console.log(data);
			if(data.estado == 1){
				//Command:toastr.warning(data.mensaje,"Atención",{onHidden:function(){$('#loader').fadeOut(); location.reload();}});
				//$('#loader').fadeOut();
				cargarEstudiantes();

			}
			else{
				//$('#loader').fadeOut();
				cargarEstudiantes();
			}		
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarEstudiantes(){
    $('#loader').fadeIn();
	$('.registroConsumo').slideUp();
	var dibujado = 0;
	var mes = "";
	var semanaActual = "";
	var dia = "";

	if($('#dia').val() != "" && $('#dia').val() != null){
		dia = $("#dia").val();	
	}

	if($('#semana').val() != "" && $('#semana').val() != null){
		semanaActual = $("#semana").val();	
	}else{
		semanaActual = $('#semanaActual').val();
	}

	if($('#mes').val() != "" && $('#mes').val() != null){
		mes = $("#mes").val();	
	}	
	
	var sede = $('#sede').val();
	var nivel = $('#nivel').val();
	var grado = $('#grado').val();
	var grupo = $('#grupo').val();

	if ( $.fn.DataTable.isDataTable( '.dataTablesSedes' ) ) {datatables.destroy(); }
	
	datatables = $('.dataTablesSedes').DataTable({
	ajax: {
		method: 'POST',
		url: 'functions/fn_registro_biometrico.php',
		data:{
			dia: dia,
			semanaActual: semanaActual,
			mes: mes,
			sede: sede,
			nivel: nivel,
			grado: grado,
			grupo: grupo
		}
	},
	columns:[
		{ data: 'num_doc'},
		{ data: 'nombre'},
		{ data: 'grado'},
		{ data: 'grupo',className: "text-center"},			
		{ data: 'fecha'},
	],
	bSort: false,
	bPaginate: false,
	buttons: [ {extend: 'excel', title: 'Asistencia', className: 'btnExportarExcel', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } } ],
	dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"html5buttons"B>',
	oLanguage: {
	sLengthMenu: 'Mostrando _MENU_ registros',
	sZeroRecords: 'No se encontraron registros',
	sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
	sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
	sInfoFiltered: '(Filtrado desde _MAX_ registros)',
	sSearch:         'Buscar: ',
	oPaginate:{
		sFirst:    'Primero',
		sLast:     'Último',
		sNext:     'Siguiente',
		sPrevious: 'Anterior'
	}
	},
	//pageLength: 10,
	responsive: true,
	"preDrawCallback": function( settings ) {
		if(dibujado == 0){
			//$('#loader').fadeIn();
		}
	}
	}).on("draw", function(){ 
		$('.registroConsumo').slideDown();
		$('#loader').fadeOut();
	});	
	  
	var botonAcciones = '<div class="dropdown pull-right">'+ '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+ 'Acciones <span class="caret"></span>'+ '</button>'+ '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+ '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>' + '</ul>'+ '</div>';
  	$('.containerBtn').html(botonAcciones);
}

