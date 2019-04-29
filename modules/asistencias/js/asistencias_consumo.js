jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

var total = 0;
var faltan = 0;
var ausentes = [];
var repitentes = [];
var noConsumieron = [];
var noRepitieron =[];

$(document).ready(function(){

	var d = new Date();
	var month = d.getMonth();
	var day = d.getDate();
	console.log("Hoy es "+day+" de "+month);

	$('.checkbox-header-consumio-all').iCheck({checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', });	
	$('.checkbox-header-repitio-all').iCheck({checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', });	

	if(day != localStorage.getItem("wappsi_dia_actual") || month != localStorage.getItem("wappsi_mes_actual")){
		console.log("Se estaba trabajndo  "+localStorage.getItem("wappsi_dia_actual")+" de "+localStorage.getItem("wappsi_mes_actual"));
		console.log("Borrar almacenamiento local");
		// localStorage.setItem("wappsi_dia_actual", day);
		// localStorage.setItem("wappsi_mes_actual", month);
		// localStorage.setItem("wappsi_institucion", "");
		// localStorage.setItem("wappsi_sede", "");
		// localStorage.removeItem("wappsi_total");
		// localStorage.removeItem("wappsi_faltan");
		// localStorage.removeItem("wappsi_ausentes");
		// localStorage.removeItem("wappsi_repitentes");
		// localStorage.removeItem("wappsi_no_consumieron");
		// localStorage.removeItem("wappsi_no_repitieron");
	}

	total = localStorage.getItem("wappsi_total");
	faltan = localStorage.getItem("wappsi_faltan");


	if ( JSON.parse(localStorage.getItem("wappsi_ausentes")) === null) {
		localStorage.setItem("wappsi_ausentes", JSON.stringify(ausentes));
	}

	if ( JSON.parse(localStorage.getItem("wappsi_repitentes")) === null) {
		localStorage.setItem("wappsi_repitentes", JSON.stringify(repitentes));
	}	

	if ( JSON.parse(localStorage.getItem("wappsi_no_consumieron")) === null) {
		localStorage.setItem("wappsi_no_consumieron", JSON.stringify(noConsumieron));
	}	

	if ( JSON.parse(localStorage.getItem("wappsi_no_repitieron")) === null) {
		localStorage.setItem("wappsi_no_repitieron", JSON.stringify(noRepitieron));
	}

	ausentes = JSON.parse(localStorage.getItem("wappsi_ausentes"));
	repitentes = JSON.parse(localStorage.getItem("wappsi_repitentes"));
	noConsumieron = JSON.parse(localStorage.getItem("wappsi_no_consumieron"));
	noRepitieron = JSON.parse(localStorage.getItem("wappsi_no_repitieron"));

	console.log("Repitentes");
	console.log(repitentes);	
	console.log("Ausentes");
	console.log(ausentes);
	console.log("No Consumierón");
	console.log(noConsumieron);	
	console.log("No Repitierón");
	console.log(noRepitieron);

	$("#sede").val(localStorage.getItem("wappsi_sede"));

	// $(".asistenciaFaltantes").html(faltan);
	// $(".asistenciaTotal").html(total);


	$(".asistenciaFaltantes").html(0);
	$(".asistenciaTotal").html(0);

	

	console.log("Total: "+total);
	console.log("Faltan: "+faltan);







	$('#btnBuscar').click(function(){
		if($('#form_asistencia').valid()){
			cargarEstudiantes()
		}
	});

	$('#ventanaConfirmar .btnNo').click(function(){
		var aux = $("#asistenteTramite").val();
		aux = ".checkbox-header-asistencia."+aux;
		
		//console.log(aux);
		// Cambiar de estado un check recien cambiado.
		$(aux).iCheck('destroy');
		if( $(aux).prop('checked') ) {
			$(aux).prop( "checked", false );
		}else{
			$(aux).prop( "checked", true );
		}
		$(aux).parent().iCheck({checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', });	

	});

	$('#ventanaConfirmar .btnSi').click(function(){
		actualizarAsistencia();
	});














	$('.btnGuardar').click(function(){
		guardarEntregas();
	});	





	// Check todos consumieron
	$(document).on('ifChecked', '.checkbox-header-consumio-all', function () { 
		console.log("Check todos consumieron.");
		$('.checkbox-header-consume:enabled').iCheck('check');
	});	

	// unCheck todos consumieron
	$(document).on('ifUnchecked', '.checkbox-header-consumio-all', function () { 
		console.log("Check todos consumieron.");
		$('.checkbox-header-consume').iCheck('uncheck');
		$( ".checkbox-header-repite").iCheck('uncheck'); 
		$( ".checkbox-header-repite").iCheck('disable'); 
	});

	// Check todos repitio
	$(document).on('ifChecked', '.checkbox-header-repitio-all', function () { 
		console.log("Check todos repitieron.");
		// $('.checkbox-header-repite:enabled').iCheck('check');
		$( ".checkbox-header-repite:enabled" ).each(function( index ) {
			aux = $(this).parent();
			aux = $(aux).parent();
			aux = $(aux).parent();
			aux = $(aux).prev();
			aux = $(aux).text();
			//console.log(aux);
			if(aux == "Si"){
				$(this).iCheck('check');
			}
  			//console.log( index + ": " + $( this ).text() );
		});
	});	

	// unCheck todos repitio
	$(document).on('ifUnchecked', '.checkbox-header-repitio-all', function () { 
		console.log("Check todos repitieron.");
		$('.checkbox-header-repite').iCheck('uncheck');
	});


		// $('#asistenteTramite').val($(this).val());
		// $('#tipoDocumentoAsistenteTramite').val($(this).attr('tipoDocumento'));
		// $('#valorActualizacion').val(1);
		// $('#ventanaConfirmar .modal-body p').html('¿Esta seguro de <strong>que desea hacer cambios en los registros de la asistencia</strong> para este estudiante? ');
  		// 		$('#ventanaConfirmar').modal();	
	// checkbox-header-repitio-all








	// Check a cada item de la columna Consumió
	$(document).on('ifChecked', '.checkbox-header-consume', function () { 
		console.log("Check en un elemento de consumio.");
		if( (faltan) > 0 ){
			$('.checkbox'+ $(this).data('columna')).iCheck('check'); 
			console.log("S");
			faltan--;
			localStorage.setItem("wappsi_faltan", faltan);
			$(".asistenciaFaltantes").html(faltan);

			// Quitando del array de los que no consumieron.	
			var aux = JSON.parse(localStorage.getItem("wappsi_no_consumieron"));
			var index = aux.indexOf($(this).val());
			if (index > -1) {
				aux.splice(index, 1);
			}		
			console.log(aux);
			localStorage.setItem("wappsi_no_consumieron", JSON.stringify(aux));	



			if(faltan <= 0){
				$( ".checkbox-header-repite:not(:checked)").iCheck('disable'); 
			}else{
				aux = $(this).val();
				$( ".checkbox-header-repite."+aux).iCheck('enable'); 	
			}
		}
	});
	
	// unCheck a cada item de la columna Consumió
	$(document).on('ifUnchecked', '.checkbox-header-consume', function () { 
		$(".checkbox-header-repite."+$(this).val()).iCheck('uncheck');


		console.log("Faltan: "+faltan);
		$('.checkbox'+ $(this).data('columna')).iCheck('uncheck'); 
		console.log("N");
		faltan++;
		localStorage.setItem("wappsi_faltan", faltan);
		$(".asistenciaFaltantes").html(faltan);

		var aux = JSON.parse(localStorage.getItem("wappsi_no_consumieron"));
		aux.push($(this).val());
		console.log(aux);
		localStorage.setItem("wappsi_no_consumieron", JSON.stringify(aux));

		// Si este estudiante que no consume esta entre los repitentes, hay que quitarlo de la lista de repitentes.

		var aux = JSON.parse(localStorage.getItem("wappsi_repitentes"));
		var index = aux.indexOf($(this).val());
		if (index > -1) {
			aux.splice(index, 1);
		}		
		console.log(aux);
		localStorage.setItem("wappsi_repitentes", JSON.stringify(aux));




		if(faltan > 0){
			$( ".checkbox-header-repite:not(:checked)").iCheck('enable'); 
		}


		aux = $(this).val();
		$( ".checkbox-header-repite."+aux).iCheck('uncheck'); 
		$( ".checkbox-header-repite."+aux).iCheck('disable'); 
	});

	// Check a cada item de la columna Repitió
	$(document).on('ifChecked', '.checkbox-header-repite', function () { 
		if( (faltan) > 0 ){
			$('.checkbox'+ $(this).data('columna')).iCheck('check'); 
			console.log("S");
			faltan--;
			localStorage.setItem("wappsi_faltan", faltan);
			$(".asistenciaFaltantes").html(faltan);
			
			var aux = JSON.parse(localStorage.getItem("wappsi_repitentes"));
			aux.push($(this).val());
			console.log(aux);
			localStorage.setItem("wappsi_repitentes", JSON.stringify(aux));

	

			if(faltan <= 0){
				$( ".checkbox-header-repite:not(:checked)").iCheck('disable'); 
			}
		}
	});
	
	// unCheck a cada item de la columna Repitió
	$(document).on('ifUnchecked', '.checkbox-header-repite', function () { 
		console.log("Faltan: "+faltan);
		$('.checkbox'+ $(this).data('columna')).iCheck('uncheck'); 
		console.log("N");
		faltan++;
		localStorage.setItem("wappsi_faltan", faltan);
		$(".asistenciaFaltantes").html(faltan);


		var aux = JSON.parse(localStorage.getItem("wappsi_repitentes"));
		var index = aux.indexOf($(this).val());
		if (index > -1) {
			aux.splice(index, 1);
		}		
		localStorage.setItem("wappsi_repitentes", JSON.stringify(aux));

		

		if(faltan > 0){
			$( ".checkbox-header-repite:not(:checked)").iCheck('enable'); 
		}
	});

	// Check a cada item de la columna Asistencia
	$(document).on('ifChecked', '.checkbox-header-asistencia', function () { 
		console.log("Check en un elemento de asistencia.");
		$('#asistenteTramite').val($(this).val());
		$('#tipoDocumentoAsistenteTramite').val($(this).attr('tipoDocumento'));
		$('#valorActualizacion').val(1);
		$('#ventanaConfirmar .modal-body p').html('¿Esta seguro de <strong>que desea hacer cambios en los registros de la asistencia</strong> para este estudiante? ');
  		$('#ventanaConfirmar').modal();
	});
	
	// unCheck a cada item de la columna Asistencia
	$(document).on('ifUnchecked', '.checkbox-header-asistencia', function () { 
		console.log("unCheck en un elemento de asistencia.");
		$('#asistenteTramite').val($(this).val());
		$('#tipoDocumentoAsistenteTramite').val($(this).attr('tipoDocumento'));
		$('#valorActualizacion').val(0);
		$('#ventanaConfirmar .modal-body p').html('¿Esta seguro de <strong>que desea hacer cambios en los registros de la asistencia</strong> para este estudiante? ');
  		$('#ventanaConfirmar').modal();
	});





















});


function cargarEstudiantes(){





	var dibujado = 0;
	var sede = localStorage.getItem("wappsi_sede");

	var auxNoConsumieron = JSON.parse(localStorage.getItem("wappsi_no_consumieron"));
	var auxNoRepitieron = JSON.parse(localStorage.getItem("wappsi_no_repitieron"));
	var auxRepitentes = JSON.parse(localStorage.getItem("wappsi_repitentes"));
	var auxConsumieron = JSON.parse(localStorage.getItem("wappsi_consumieron"));
	var auxRepitieron = JSON.parse(localStorage.getItem("wappsi_repitieron"));



	var aux = JSON.parse(localStorage.getItem("wappsi_ausentes"));

	var semanaActual = $('#semanaActual').val();
	var sede = $('#sede').val();
	var nivel = $('#nivel').val();
	var grado = $('#grado').val();
	var grupo = $('#grupo').val();

	if ( $.fn.DataTable.isDataTable( '.dataTablesSedes' ) ) {datatables.destroy(); }
	
	datatables = $('.dataTablesSedes').DataTable({
	ajax: {
		method: 'POST',
		url: 'functions/fn_buscar_consumidores.php',
		data:{
			semanaActual: semanaActual,
			sede: sede,
			nivel: nivel,
			grado: grado,
			grupo: grupo
		}
	},
	columns:[
			{
			sortable: false,
			className: "textoCentrado",
			"render": function ( data, type, full, meta ) {
				var tipoDocumento = full.tipo_doc;
				var documento = full.num_doc;
				var asistencia = full.asistencia; 

				

				
				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header checkbox-header-asistencia "+documento+"\" ";
				
				if (asistencia == 1) { opciones = opciones + " checked "; }
				// else { opciones = opciones + " disabled "; }

				opciones = opciones + " data-columna=\"1\" value=\""+documento+"\" tipoDocumento = \""+tipoDocumento+"\"/> </div> ";

				return opciones;
			}
		},	
		{ data: 'num_doc'},
		{ data: 'nombre'},
		{ data: 'grado'},
		{ data: 'grupo',className: "text-center"},
		{
			sortable: false,
			className: "textoCentrado",
			"render": function ( data, type, full, meta ) {
				var tipoDocumento = full.tipo_doc;
				var documento = full.num_doc;
				var asistencia = full.asistencia; 
				var consumio = full.consumio; 

				
				var index = auxConsumieron.indexOf(documento);
				
				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header checkbox-header-consume "+documento+"\" ";
				
				if (asistencia != 1) { opciones = opciones + " disabled "; }else{
					if (index > -1) {opciones = opciones + " checked ";}
				}
				if (consumio == 1) { opciones = opciones + " checked "; }


				opciones = opciones + " data-columna=\"1\" value=\""+documento+"\" tipoDocumento = \""+tipoDocumento+"\"/> </div> ";

				return opciones;
			}
		},	
		{
			sortable: false,
			className: "textoCentrado",
			"render": function ( data, type, full, meta ) {
				var tipoDocumento = full.tipo_doc;
				var documento = full.num_doc;
				var asistencia = full.asistencia; 
				var opciones = ""; 
				var index = auxRepitentes.indexOf(documento);
				if (index > -1) {opciones = "Si"; }else{opciones = "No"; }
				return opciones;
			}
		},		
		{
			sortable: false,
			className: "textoCentrado",
			"render": function ( data, type, full, meta ) {
				var tipoDocumento = full.tipo_doc;
				var documento = full.num_doc;
				var repitio = full.repitio;				
				var index = auxRepitieron.indexOf(documento);				
				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header checkbox-header-repite "+documento+"\" ";				
				if (repitio == 1) { opciones = opciones + " checked "; }		
				else{opciones = opciones + " disabled "; }				
				opciones = opciones + " data-columna=\"1\" value=\""+documento+"\" tipoDocumento = \""+tipoDocumento+"\"/> </div> ";
				return opciones;
			}
		}
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
		$('.registroConsumo').slideDown();
		if(dibujado == 0){
			$('#loader').fadeOut();
			dibujado++;
			actualizarMarcadores(1);

		}
		totalEstudiantesSede(); 
		$('.estadoSede').bootstrapToggle(); 




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


        if(faltan <= 0){$( ".checkbox-header-repite:not(:checked)").iCheck('disable'); }


	});	
}

function guardarEntregas(){
	console.log("Guardar asistencia.");
	var bandera = 0;	
	var repitente = [];
	var documento = "";
	var tipoDocumento = "";
	var semana = $('#semanaActual').val();	
	var formData = new FormData();
	formData.append('semana', semana);
	formData.append('sede', $('#sede').val());

	$( ".checkbox-header-consume:checked").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('consumieron['+documento+'][documento]', documento);
		formData.append('consumieron['+documento+'][tipoDocumento]', tipoDocumento);
	});	

	$( ".checkbox-header-repite:checked").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('repitieron['+documento+'][documento]', documento);
		formData.append('repitieron['+documento+'][tipoDocumento]', tipoDocumento);
	});	

	if(bandera == 0){
		console.log("Guardar");
		$.ajax({
			type: "post",
			url: "functions/fn_guardar_consumos.php",
			dataType: "json",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $("#loader").fadeIn(); },
			success: function(data){
				if(data.state == 1){
					Command : toastr.success( data.message, "Registro Exitoso", { onHidden : function(){ $('#loader').fadeOut();
					// location.href="URL para redireccionar";
					location.reload();
					}});
				}else{
					Command:toastr.error(data.message,"Error al hacer el registro.",{onHidden:function(){ $('#loader').fadeOut(); }});
				}
			},
			error: function(data){
				console.log(data);
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){ $('#loader').fadeOut(); }});
			}
		});
	}
}

function restablecerContadores(){
	localStorage.removeItem("wappsi_total");
	localStorage.removeItem("wappsi_faltan");
	localStorage.removeItem("wappsi_ausentes");
	console.log("Borrar almacenamiento local");
	Command : toastr.success( "Exito!", "Se ha borrado con éxito el almacenamiento local.", { onHidden : function(){}});
}

function actualizarAsistencia(){
	console.log("Actualizar asistencia de estudiante.");
	var formData = new FormData();
	formData.append('semana', $('#semanaActual').val());
	formData.append('sede', $('#sede').val());
	formData.append('documento', $('#asistenteTramite').val());
	formData.append('tipoDocumento', $('#tipoDocumentoAsistenteTramite').val());
	formData.append('valor', $('#valorActualizacion').val());
	$.ajax({
		type: "post",
		url: "functions/fn_actualizar_asistencia_estudiante.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $("#loader").fadeIn(); },
		success: function(data){
			if(data.state == 1){
				Command : toastr.success( data.message, "Actualización del registro exitosa", { onHidden : function(){ $('#loader').fadeOut();
				// location.href="URL para redireccionar";
				aux = $('#asistenteTramite').val();
				if($('#valorActualizacion').val() == 0){
					$( ".checkbox-header-consume."+aux).iCheck('uncheck'); 
					$( ".checkbox-header-consume."+aux).iCheck('disable'); 
					$( ".checkbox-header-repite."+aux).iCheck('uncheck'); 
					$( ".checkbox-header-repite."+aux).iCheck('disable'); 



				}else{
					$( ".checkbox-header-consume."+aux).iCheck('enable'); 
				}
				}});
			}else{
				Command:toastr.error(data.message,"Error al actualizar el registro.",{onHidden:function(){ $('#loader').fadeOut(); }});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){ $('#loader').fadeOut(); }});
		}
	});
}



	



