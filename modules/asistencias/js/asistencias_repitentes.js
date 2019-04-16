jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
var total = 0;
var faltan = 0;
var ausentes = [];
var repitentes = [];

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

	ausentes = JSON.parse(localStorage.getItem("wappsi_ausentes"));
	repitentes = JSON.parse(localStorage.getItem("wappsi_repitentes"));

	console.log("Repitentes");
	console.log(repitentes);	
	console.log("Ausentes");
	console.log(ausentes);






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


	cargarRepitentes();

	// Check a cada item
	$(document).on('ifChecked', '.checkbox-header', function () { 
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
				$( ".checkbox-header:not(:checked)").iCheck('disable'); 
			}




		}
	});






	
	// unCheck a cada item
	$(document).on('ifUnchecked', '.checkbox-header', function () { 
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
		$(".flagFaltantes").slideDown();



			if(faltan > 0){
				$( ".checkbox-header:not(:checked)").iCheck('enable'); 
			}

	});








	// ************************************************************
	// ************************************************************
	// ************************************************************
	// ************************************************************
	// ************************************************************
	// ************************************************************
	// ************************************************************













	$( "#sede" ).change(function() {
		localStorage.setItem("wappsi_sede", $("#sede").val());
		cargarGrados();
	});

	$( "#grado" ).change(function() {
		cargarGrupos();
	});

	$('#btnBuscar').click(function(){
		if($('#form_asistencia').valid()){
			cargarEstudiantes()
		}
	});

	$('#btnGuardar').click(function(){
		guardarRepitentes();
	});		



	$('#btnRestablecerContadores').click(function(){
		restablecerContadores();
	});		
});


function cargarRepitentes(){
	var semanaActual = $('#semanaActual').val();
	var sede = localStorage.getItem("wappsi_sede");
	var aux = JSON.parse(localStorage.getItem("wappsi_repitentes"));
	console.log(aux);
	//var aux = JSON.parse(localStorage.getItem("wappsi_ausentes"));
	//console.log(aux);
	if ( $.fn.DataTable.isDataTable( '.dataTablesSedes' ) ) {datatables.destroy(); }
	datatables = $('.dataTablesSedes').DataTable({
	ajax: {
		method: 'POST',
		url: 'functions/fn_buscar_repitentes.php',
		data:{
			semanaActual: semanaActual,
			sede: sede
		}
	},
	columns:[
		{
			sortable: false,
			className: "textoCentrado",
			"render": function ( data, type, full, meta ) {
				var tipoDocumento = full.tipo_doc;
				var documento = full.num_doc;

				
				var index = aux.indexOf(documento);
				
				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header\" ";
				
				if (index > -1) { opciones = opciones + " checked "; }else{ }

				opciones = opciones + " data-columna=\"1\" value=\""+documento+"\" tipoDocumento = \""+tipoDocumento+"\"/> </div> ";

				return opciones;
			}
		},
		{ data: 'num_doc'},
		{ data: 'nombre'},
		{ data: 'grado'},
		{ data: 'grupo'}
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


        if(faltan <= 0){$( ".checkbox-header:not(:checked)").iCheck('disable'); }


	});	
}

function restablecerContadores(){
	localStorage.removeItem("wappsi_total");
	localStorage.removeItem("wappsi_faltan");
	localStorage.removeItem("wappsi_ausentes");
	console.log("Borrar almacenamiento local");
	Command : toastr.success( "Exito!", "Se ha borrado con éxito el almacenamiento local.", { onHidden : function(){}});
}

function guardarRepitentes(){
	console.log("Guardar asistencia.");
	var bandera = 0;	
	var repitente = [];
	var documento = "";
	var tipoDocumento = "";
	var semana = $('#semanaActual').val();	
	var formData = new FormData();
	formData.append('semana', semana);
	formData.append('sede', $('#sede').val());

	$( ".checkbox-header:checked").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('repitente['+documento+'][documento]', documento);
		formData.append('repitente['+documento+'][tipoDocumento]', tipoDocumento);
	});	

	if(bandera == 0){
		console.log("Guardar");
		$.ajax({
			type: "post",
			url: "functions/fn_guardar_repitentes.php",
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

// semanaActual








	// { data: 'nombreSede'},
	// 		{ data: 'codigoInstitucion'},
	// 		{ data: 'nombreInstitucion'},
	// 		{ data: 'nombreCoordinador'},
	// 		{ data: 'nombreJornada'},
	// 		{ data: 'tipoValidacion'},
	// 		{defaultContent: '<div class="btn-group">'+ '<div class="dropdown pull-right">'+ '<button class="btn btn-primary btn-sm" type="button" id="dropDownMenu1" data-toggle="dropdown"  aria-haspopup="true">'+ 'Acciones <span class="caret"></span>'+ '</button>'+ '<ul class="dropdown-menu pull-right" aria-labelledby="dropDownMenu1">'+ '<li>'+ '<a href="#" class="editarSede"><i class="fa fa-pencil fa-lg"></i> Editar</a>'+ '</li>'+ '<li>'+ '<a href="#" class="verDispositivos"><i class="fa fa-eye fa-lg"></i> Ver dispositivos</a>'+ '</li>'+ '<li>'+ '<a href="#" class="verInfraestructura"><i class="fa fa-bank fa-lg"></i> Ver Infraestructura</a>'+ '</li>'+ '<li>'+ '<a href="#" class="verTitulares"><i class="fa fa-child fa-lg"></i> Ver Titulares</a>'+ '</li>'+ '<li class="divider"></li>'+ '<li>'+ '<a href="#">'+ 'Estado: &nbsp;'+ '<input type="checkbox" class="estadoSede" data-toggle="toggle" data-on="Activo" data-off="Inactivo" data-size="mini" data-width="70" data-height="24">'+ '</a>'+ '</li>'+ '</ul>'+ '</div>'+ '</div>'}











	// $(document).ready(function(){
	//   // Configuración para la tabla de sedes.
	//   datatables = $('.dataTablesSedes').DataTable({
	//     ajax: {
	//       method: 'POST',
	//       url: 'functions/fn_sedes_buscar_dataTables.php',
	//       data:{
	//         municipio: '<?= ((isset($_POST["municipio"]) && $_POST["municipio"] != "") ? $_POST["municipio"] : $municipio_defecto["CodMunicipio"]); ?>',
	//         institucion: '<?= (isset($_POST["institucion"]) ? $_POST["institucion"] : ""); ?>'
	//       }
	//     },
	//     columns:[
	//       { data: 'codigoSede'},
	//       { data: 'nombreSede'},
	//       { data: 'codigoInstitucion'},
	//       { data: 'nombreInstitucion'},
	//       { data: 'nombreCoordinador'},
	//       { data: 'nombreJornada'},
	//       { data: 'tipoValidacion'},
	//       <?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
	//       { defaultContent: '<div class="btn-group">'+
	//                                     '<div class="dropdown pull-right">'+
	//                                       '<button class="btn btn-primary btn-sm" type="button" id="dropDownMenu1" data-toggle="dropdown"  aria-haspopup="true">'+
	//                                         'Acciones <span class="caret"></span>'+
	//                                       '</button>'+
	//                                       '<ul class="dropdown-menu pull-right" aria-labelledby="dropDownMenu1">'+
	//                                         '<li>'+
	//                                           '<a href="#" class="editarSede"><i class="fa fa-pencil fa-lg"></i> Editar</a>'+
	//                                         '</li>'+
	//                                         '<li>'+
	//                                           '<a href="#" class="verDispositivos"><i class="fa fa-eye fa-lg"></i> Ver dispositivos</a>'+
	//                                         '</li>'+
	//                                         '<li>'+
	//                                           '<a href="#" class="verInfraestructura"><i class="fa fa-bank fa-lg"></i> Ver Infraestructura</a>'+
	//                                         '</li>'+
	//                                         '<li>'+
	//                                           '<a href="#" class="verTitulares"><i class="fa fa-child fa-lg"></i> Ver Titulares</a>'+
	//                                         '</li>'+
	//                                         '<li class="divider"></li>'+
	//                                         '<li>'+
	//                                           '<a href="#">'+
	//                                             'Estado: &nbsp;'+
	//                                             '<input type="checkbox" class="estadoSede" data-toggle="toggle" data-on="Activo" data-off="Inactivo" data-size="mini" data-width="70" data-height="24">'+
	//                                           '</a>'+
	//                                         '</li>'+
	//                                       '</ul>'+
	//                                     '</div>'+
	//                                   '</div>'}
	//       <?php } ?>
	//     ],
	//     buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel', exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } } ],
	//     dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"html5buttons"B>',
	//     oLanguage: {
	//       sLengthMenu: 'Mostrando _MENU_ registros',
	//       sZeroRecords: 'No se encontraron registros',
	//       sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
	//       sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
	//       sInfoFiltered: '(Filtrado desde _MAX_ registros)',
	//       sSearch:         'Buscar: ',
	//       oPaginate:{
	//         sFirst:    'Primero',
	//         sLast:     'Último',
	//         sNext:     'Siguiente',
	//         sPrevious: 'Anterior'
	//       }
	//     },
	//     pageLength: 10,
	//     responsive: true,
	//     "preDrawCallback": function( settings ) {
	//       $('#loader').fadeIn();
	//     }
	//   }).on("draw", function(){ $('#loader').fadeOut(); $('.estadoSede').bootstrapToggle(); });

	//   // Evento para editar sede
	//   $(document).on('click', '.dataTablesSedes tbody .editarSede', function(){
	//     var tr = $(this).closest('tr');
	//     var datos = datatables.row( tr ).data();
	//     editarSede(datos.codigoSede, datos.nombreSede);
	//   });
	//   // Evento para cambiar de estado a la sede
	//   $(document).on('change', '.dataTablesSedes tbody input[type=checkbox].estadoSede', function(){
	//     var tr = $(this).closest('tr');
	//     var datos = datatables.row( tr ).data();
	//     confirmarCambioEstado(datos.codigoSede, datos.estadoSede);
	//   });
	//   // Evento para ver la sede
	//   $(document).on('click', '.dataTablesSedes tbody td:nth-child(-n+7)', function(){
	//     var tr = $(this).closest('tr');
	//     var datos = datatables.row( tr ).data();
	//     $('#formVerSede #codSede').val(datos.codigoSede);
	//     $('#formVerSede #nomSede').val(datos.nombreSede);
	//     $('#formVerSede #nomInst').val(datos.nombreInstitucion);
	//     $('#formVerSede').submit();
	//   });

	//   // Evento para ver dispositivos de la sede
	//   $(document).on('click', '.dataTablesSedes tbody .verDispositivos', function(){
	//     var tr = $(this).closest('tr');
	//     var datos = datatables.row( tr ).data();
	//     verDispositivosSede(datos.codigoSede);
	//   });
	//   // Evento para ver dispositivos de la sede
	//   $(document).on('click', '.dataTablesSedes tbody .verInfraestructura', function(){
	//     var tr = $(this).closest('tr');
	//     var datos = datatables.row( tr ).data();
	//     verInfraestructurasSede(datos.codigoSede);
	//   });
	//   // Evento para ver dispositivos de la sede
	//   $(document).on('click', '.dataTablesSedes tbody .verTitulares', function(){
	//     var tr = $(this).closest('tr');
	//     var datos = datatables.row( tr ).data();
	//     verTitularesSede(datos.codigoSede);
	//   });

	//   // Evitar el burbujeo del DOM en el control dropbox
	//   $(document).on('click', '.dropdown li:nth-child(6)', function(e) { e.stopPropagation(); });

	//   // Configuración para la validación del formulario de búsqueda de sedes.
	//   jQuery.extend(jQuery.validator.messages, { required: "Campo obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

	//   <?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
	//   // Botón de acciones para la tabla.
	//   var botonAcciones = '<div class="dropdown pull-right">'+
	//                     '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
	//                       'Acciones <span class="caret"></span>'+
	//                     '</button>'+
	//                     '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
	//                       '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-pdf-o"></i> Exportar </a></li>'+
	//                       '<li class="divider"></li>'+
	//                       '<li>'+
	//                         '<a class="fileinput fileinput-new" data-provides="fileinput">'+
	//                           '<span class="btn-file">'+
	//                             '<i class="fa fa-upload"></i> '+
	//                             '<span class="fileinput-new">Importar sedes</span>'+
	//                             '<span class="fileinput-exists">Cambiar</span>'+
	//                             '<input type="file" name="archivoSede" id="archivoSede" onchange="if(!this.value.length) return false; cargarArchivo();" accept=".csv, .xlsx">'+
	//                           '</span> '+
	//                           '<span class="fileinput-filename center-block"></span>'+
	//                           '<span href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</span>'+
	//                         '</a>'+
	//                       '</li>'+
	//                       '<li><a href="#" id="importarPriorizacion"><i class="fa fa-upload"></i> Importar priorización</a></li>'+
	//                       '<li><a href="#" id="importarFocalizacion"><i class="fa fa-upload"></i> Importar focalización</a></li>'+
	//                       '<li class="divider"></li>'+
	//                       '<li><a href="'+ $('#inputBaseUrl').val() +'/download/sedes/Plantilla_Sedes.csv" dowload> <i class="fa fa-download"></i> Descarga plantilla sedes.CSV</a></li>'+
	//                       '<li><a href="'+ $('#inputBaseUrl').val() +'/download/sedes/Plantilla_Sedes.xlsx" dowload> <i class="fa fa-download"></i> Descarga plantilla sedes.XLSX </a></li>'+
	//                       '<li><a href="'+ $('#inputBaseUrl').val() +'/download/priorizacion/Plantilla_Priorizacion.csv" dowload> <i class="fa fa-download"></i> Descarga plantilla priorización .CSV</a></li>'+
	//                       '<li><a href="'+ $('#inputBaseUrl').val() +'/download/focalizacion/Plantilla_Focalizacion.csv" dowload> <i class="fa fa-download"></i> Descarga plantilla focalización .CSV</a></li>'+
	//                       '<ul>'+
	//                     '</ul>'+
	//                   '</div>';
	// $('.containerBtn').html(botonAcciones);
	// <?php } ?>
	// });
