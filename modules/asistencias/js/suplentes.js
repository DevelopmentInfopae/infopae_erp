jQuery.extend(jQuery.validator.messages, { 
	required: "Este campo es obligatorio.", 
	remote: "Por favor, rellena este campo.", 
	email: "Por favor, escribe una dirección de correo válida", 
	url: "Por favor, escribe una URL válida.", 
	date: "Por favor, escribe una fecha válida.", 
	dateISO: "Por favor, escribe una fecha (ISO) válida.", 
	number: "Por favor, escribe un número entero válido.", 
	digits: "Por favor, escribe sólo dígitos.", 
	creditcard: "Por favor, escribe un número de tarjeta válido.", 
	equalTo: "Por favor, escribe el mismo valor de nuevo.", 
	accept: "Por favor, escribe un valor con una extensión aceptada.", 
	maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), 
	minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), 
	rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), 
	range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
	max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), 
	min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

toastr.options = { 
	newestOnTop: true, 
	closeButton: false, 
	progressBar: true, 
	preventDuplicates: false, 
	showMethod: 'slideDown', 
	timeOut: 3500, };


var total = 0;
var faltan = 0;
var ausentes = [];
var repitentes = [];
var datatables = null;

for (x=0; x<=localStorage.length-1; x++)  {  
  clave = localStorage.key(x); 
  // console.log("La clave " + clave + " contiene el valor " + localStorage.getItem(clave));  
}

$(document).ready(function(){
	var d = new Date();
	var month = d.getMonth();
	var day = d.getDate();

	if(day != localStorage.getItem("wappsi_dia_actual") || month != localStorage.getItem("wappsi_mes_actual")){
		console.log("Se estaba trabajndo  "+localStorage.getItem("wappsi_dia_actual")+" de "+localStorage.getItem("wappsi_mes_actual"));
		console.log("Borrar almacenamiento local");
		localStorage.setItem("wappsi_dia_actual", day);
		localStorage.setItem("wappsi_mes_actual", month);
		localStorage.setItem("wappsi_institucion", "");
		localStorage.setItem("wappsi_sede", "");
		localStorage.removeItem("wappsi_total");
		localStorage.removeItem("wappsi_faltan");
		localStorage.removeItem("wappsi_ausentes");
		localStorage.removeItem("wappsi_repitentes");
		localStorage.removeItem("wappsi_no_consumieron");
		localStorage.removeItem("wappsi_no_repitieron");
	}

	$(document).on('ifChecked', '.checkbox-header', function () { 
		$('.checkbox'+ $(this).data('columna')).iCheck('check');
		faltan--;
		localStorage.setItem("wappsi_faltan", faltan);
		$(".asistenciaFaltantes").html(faltan);
		if (faltan == 0) {
			$( ".checkbox-header:not(:checked)").iCheck('disable');
		}		
	});

	$(document).on('ifUnchecked', '.checkbox-header', function(){
		$('.checkbox' + $(this).data('columna')).iCheck('uncheck');
		faltan++;
		localStorage.setItem("wappsi_falta", faltan);
		$(".asistenciaFaltantes").html(faltan);
		if (faltan > 0) {
			$(".checkbox-header:not(:checked)").iCheck('enable');
		}
	})


	$('#btnBuscar').click(function(){
		if($('#form_asistencia').valid()){
			validarSuplencia();
		}
	});

	$('.btnGuardar').click(function(){
		guardarSuplentes();
	});

});


function validarSuplencia(){
	var datos = $('#form_asistencia').serialize();
	$.ajax({
		type: "post",
		url: "functions/fn_validar_suplencia.php",
		dataType: "json",
		data: datos,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			// console.log(data);
			if(data.estado == 1){
				Command:toastr.warning(data.mensaje,"Atención",{onHidden:function(){$('#loader').fadeOut(); location.reload();}});
			}
			else{
				$('#loader').fadeOut();
				cargarSuplentes();
			}		
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}


function cargarSuplentes(){
	var dibujado = 0;
	if($('#mes').val() != "" && $('#mes').val() != null ){ var mes = $('#mes').val(); }else{ var mes = ""; }
	if($('#mes').val() != "" && $('#mes').val() != null ){ var dia = $('#dia').val(); }else{ var dia = ""; }
	if($('#semana').val() != "" && $('#semana').val() != null ){ var semanaActual = $('#semana').val();}else{ var semanaActual = $('#semanaActual').val();}

	var sede = $('#sede').val();
	var complemento = $('#complemento').val();
	var nivel = $('#nivel').val();
	var grado = $('#grado').val();
	var grupo = $('#grupo').val();
	actualizarMarcadoresSuplencia(0);

	if ( $.fn.DataTable.isDataTable( '.dataTablesSedes' ) ) {datatables.destroy(); }
	datatables = $('.dataTablesSedes').DataTable({
	ajax: {
		method: 'POST',
		url: 'functions/fn_buscar_suplentes.php',
		data:{ 
			mes: mes,
			dia: dia,
			semanaActual: semanaActual,
			sede: sede,
			complemento: complemento,
			nivel: nivel,
			grado: grado,
			grupo: grupo,
		}
	},
	
	columns:[
		{
			sortable: false,
			className: "textoCentrado",
			"render": function ( data, type, full, meta ) {
				// console.log(full);
				banderaRegistros = meta.settings.json.banderaRegistros;	
				var tipoDocumento = full.tipo_doc;
				var documento = full.num_doc;	
				var suplencia = full.D; 
				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header\" ";				
				if(suplencia == '1'){
					opciones = opciones + " checked "; 
				}
				opciones = opciones + " data-columna=\"1\" value=\""+documento+"\" tipoDocumento = \""+tipoDocumento+"\"/> </div> ";
				return opciones;
			}
		},
		{ data: 'num_doc'},
		{ data: 'nombre'},
		{ data: 'grado'},
		{ data: 'grupo',className: "text-center"}
	],
	bSort: false,
	bPaginate: false,
	buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } } ],
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
			$('#loader').fadeIn();
		}

	}
	}).on("draw", function(){ 
		if(dibujado == 0){
			$('#loader').fadeOut();
			$("#banderaRegistros").val(banderaRegistros);
			dibujado++;	
			if(banderaRegistros == 1){
				$(".editando").fadeIn();
				$(".btnGuardar").html("<span class='fa fa-check'></span> Actualizar");				
			}else{
				$(".editando").fadeOut();
				$(".btnGuardar").html("<span class='fa fa-check'></span> Guardar");
			}
		}
		if (faltan <= 0) {
        	$( ".checkbox-header:not(:checked)").iCheck('disable');
        }
		$('.i-checks').iCheck({
			checkboxClass: 'icheckbox_square-green',
			radioClass: 'iradio_square-green',
		});
		//iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass   : 'iradio_minimal-blue'
        });
        //Red color scheme for iCheck
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
        checkboxClass: 'icheckbox_minimal-red',
        radioClass   : 'iradio_minimal-red'
        });
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
        });

	});	
}

function guardarSuplentes(){
	if($('#mes').val() != "" && $('#mes').val() != null){ var mes = $('#mes').val(); }
	if($('#dia').val() != "" && $('#dia').val() != null){ var dia = $('#dia').val(); }	
	if($('#semana').val() != "" && $('#semana').val() != null){ var semana = $('#semana').val(); }else{ var semana = $('#semanaActual').val(); }

	var formData = new FormData();
	formData.append('mes', mes);
	formData.append('dia', dia);
	formData.append('semana', semana);
	formData.append('sede', $('#sede').val());
	formData.append('complemento', $('#complemento').val());

	var cantidadSuplentes = 0;
	$( ".checkbox-header:checked").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('suplente['+documento+'][documento]', documento);
		formData.append('suplente['+documento+'][tipoDocumento]', tipoDocumento);
		formData.append('suplente['+documento+'][consume]', 1);
		cantidadSuplentes++;
	});

	$( ".checkbox-header:not(:checked)").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('suplente['+documento+'][documento]', documento);
		formData.append('suplente['+documento+'][tipoDocumento]', tipoDocumento);
		formData.append('suplente['+documento+'][consume]', 0);
		cantidadSuplentes++;
	});

	if(cantidadSuplentes <= 0){
		bandera++;
		Command:toastr.warning("Debe seleccionar al menos un estudiante para que repita.","Alerta!",{onHidden:function(){$('#loader').fadeOut();}});
	}else {
		$.ajax({
			url: 'functions/fn_guardar_suplentes.php',
			type: 'POST',
			dataType: 'json',
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $("#loader").fadeIn(); }
		})
		.done(function(data) {
			if (data.estado == 1) {
				Command : toastr.success( data.mensaje, "Registro Exitoso", { onHidden : function(){ $('#loader').fadeOut();
				location.reload();
				}});
			}else if (data.estado == 0) {
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
			}
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			 $("#loader").fadeOut();
			// console.log("complete");
		});
		
	}
}

function actualizarMarcadoresSuplencia(flagConsumo){
	totalEstudiantesSede();
	reg_faltan = 0;
	reg_ausentes = [];
	reg_repitentes = [];
	reg_consumieron = [];
	reg_repitieron =[];
	var formData = new FormData();
	if($('#dia').val() != "" && $('#dia').val() != null){ formData.append('dia', $('#dia').val()); }
	if($('#mes').val() != "" && $('#mes').val() != null){ formData.append('mes', $('#mes').val()); }
	if($('#semana').val() != "" && $('#semana').val() != null){ formData.append('semanaActual', $('#semana').val()); }else{ formData.append('semanaActual', $('#semanaActual').val());}
	formData.append('sede', $('#sede').val());
	formData.append('complemento', $('#complemento').val());
	$.ajax({
		type: "post",
		url: "functions/fn_cargar_asistencia_marcadores_suplencia.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){},
		success: function(data){
			// console.log(data);
			if(data.estado == 1){
				faltan = data.suplencia;
				$(".asistenciaFaltantes").html(faltan);
				localStorage.setItem("wappsi_faltan", reg_faltan);

			}else if (data.estado == 0) {
				faltan = 0;
				$(".asistenciaFaltantes").html(faltan);
			}
			if (faltan == 0) {
				// console.log('paso'); // pasa a pruebas funcionamiento en servidor discordancias en local
				// $( ".checkbox-header:not(:checked)").iCheck('disable');
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}
