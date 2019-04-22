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

	if(day != localStorage.getItem("wappsi_dia_actual") && month != localStorage.getItem("wappsi_mes_actual")){
		localStorage.setItem("wappsi_dia_actual", day);
		localStorage.setItem("wappsi_mes_actual", month);
		localStorage.setItem("wappsi_sede", "");
		localStorage.removeItem("wappsi_total");
		localStorage.removeItem("wappsi_faltan");
		localStorage.removeItem("wappsi_ausentes");
		localStorage.removeItem("wappsi_repitentes")
		localStorage.removeItem("wappsi_no_consumieron")
		localStorage.removeItem("wappsi_no_repitieron")
		console.log("Borrar almacenamiento local");
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

	$(".asistenciaFaltantes").html(faltan);
	$(".asistenciaTotal").html(total);

	if(faltan > 0){
		$(".flagFaltantes").slideDown();
	}else{
		$(".flagFaltantes").slideUp();
	}

	console.log("Total: "+total);
	console.log("Faltan: "+faltan);

	cargarEstudiantes();

	$('.btnGuardar').click(function(){
		guardarEntregas();
	});	

		// Check a cada item de la columna Consumió
	$(document).on('ifChecked', '.checkbox-header-consume', function () { 
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

			if(faltan > 0){
				$(".flagFaltantes").slideDown();
			}else{
				$(".flagFaltantes").slideUp();
			}

			if(faltan <= 0){
				$( ".checkbox-header-repite:not(:checked)").iCheck('disable'); 
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
			$(".flagFaltantes").slideDown();
		}else{
			$(".flagFaltantes").slideUp();
		}

		if(faltan > 0){
			$( ".checkbox-header-repite:not(:checked)").iCheck('enable'); 
		}
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

			if(faltan > 0){
				$(".flagFaltantes").slideDown();
			}else{
				$(".flagFaltantes").slideUp();
			}

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
			$(".flagFaltantes").slideDown();
		}else{
			$(".flagFaltantes").slideUp();
		}

		if(faltan > 0){
			$( ".checkbox-header-repite:not(:checked)").iCheck('enable'); 
		}
	});

});


function cargarEstudiantes(){
	var semanaActual = $('#semanaActual').val();
	var sede = localStorage.getItem("wappsi_sede");

	var auxNoConsumieron = JSON.parse(localStorage.getItem("wappsi_no_consumieron"));
	var auxNoRepitieron = JSON.parse(localStorage.getItem("wappsi_no_repitieron"));
	var auxRepitentes = JSON.parse(localStorage.getItem("wappsi_repitentes"));


	if ( $.fn.DataTable.isDataTable( '.dataTablesSedes' ) ) {datatables.destroy(); }
	
	datatables = $('.dataTablesSedes').DataTable({
	ajax: {
		method: 'POST',
		url: 'functions/fn_buscar_consumidores.php',
		data:{
			semanaActual: semanaActual,
			sede: sede
		}
	},
	columns:[
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

				
				var index = auxNoConsumieron.indexOf(documento);
				
				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header checkbox-header-consume "+documento+"\" ";
				
				if (index > -1) {}else{opciones = opciones + " checked ";}

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

				
				var index = auxRepitentes.indexOf(documento);
				var index2 = auxNoRepitieron.indexOf(documento);

				
				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header checkbox-header-repite "+documento+"\" ";
				
				if (index > -1 && index2 <= -1) { opciones = opciones + " checked "; }else{ }

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
		$('#loader').fadeIn();
	}
	}).on("draw", function(){ 
		$('#loader').fadeOut();
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

function totalEstudiantesSede(){
	var formData = new FormData();
	formData.append('semanaActual', $('#semanaActual').val());
	formData.append('sede', $('#sede').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_total_estudiantes.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ 
			//$('#loader').fadeIn();
			 },
		success: function(data){
			if(data.estado == 1){
				total = data.total;
				localStorage.setItem("wappsi_total", total);
				$(".asistenciaTotal").html(total);
				//$('#loader').fadeOut();
			}		
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarMunicipios(){
	//formData.append('municipio', $('#municipio').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_municipios.php",
		dataType: "json",
		contentType: false,
		processData: false,
		//data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#municipio').html(data.opciones);
				$('#loader').fadeOut();
				cargarInstituciones();
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarInstituciones(){
	var formData = new FormData();
	formData.append('municipio', $('#municipio').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_instituciones.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#institucion').html(data.opciones);
				$('#loader').fadeOut();

			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarSedes(){
	var formData = new FormData();
	formData.append('institucion', $('#institucion').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_sede.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#sede').html(data.opciones);
				$('#loader').fadeOut();

			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarGrados(){
	var formData = new FormData();
	formData.append('semanaActual', $('#semanaActual').val());
	formData.append('sede', $('#sede').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_grados.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#grado').html(data.opciones);
				$('#loader').fadeOut();

			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarGrupos(){
	var formData = new FormData();
	formData.append('semanaActual', $('#semanaActual').val());
	formData.append('grado', $('#grado').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_grupos.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#grupo').html(data.opciones);
				$('#loader').fadeOut();
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}