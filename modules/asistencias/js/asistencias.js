var banderaRegistros = "";
var total = 0;
var faltan = 0;
var ausentes = [];
var datatables = null;
// for (x=0; x<=localStorage.length-1; x++)  {  
//   clave = localStorage.key(x); 
//   //document.write("La clave " + clave + "contiene el valor " + localStorage.getItem(clave) + "<br />");  
//   console.log("La clave " + clave + " contiene el valor " + localStorage.getItem(clave));  
// }

$(document).ready(function(){
	var d = new Date();

	// console.log(d);

	var month = d.getMonth()+1;
	var day = d.getDate();
	console.log("Hoy es "+day+" de "+month);

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

	// var total = localStorage.getItem("wappsi_total");
	// var faltan = localStorage.getItem("wappsi_faltan");

	// $(".asistenciaFaltantes").html(faltan);
	// $(".asistenciaTotal").html(total);

	console.log("Total: "+total);
	console.log("Faltan: "+faltan);
	
	if (localStorage.getItem("wappsi_ausentes") === null) {
		localStorage.setItem("wappsi_ausentes", JSON.stringify(ausentes));
	}

	// $(".asistenciaFaltantes").html(faltan);
	// $(".asistenciaTotal").html(total);

	$('.i-checks').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'iradio_square-green',
	});

	$(document).on('ifChecked', '.checkbox-header0', function (){ 
		$('.checkbox-header').iCheck('check');
		$('.checkbox-header0').iCheck('check');
		console.log("S");
		faltan = 0;
		localStorage.setItem("wappsi_faltan", faltan);
		$(".asistenciaFaltantes").html(faltan);
	});




	
	$(document).on('ifUnchecked', '.checkbox-header0', function () { 
		$('.checkbox-header').iCheck('uncheck');
		$('.checkbox-header0').iCheck('uncheck');
		console.log("N");
		faltan = total;
		localStorage.setItem("wappsi_faltan", faltan);
		$(".asistenciaFaltantes").html(faltan);


	});	

	$(document).on('ifChecked', '.checkbox-header', function () { 
		$('.checkbox'+ $(this).data('columna')).iCheck('check');
		console.log("Check fataban: "+faltan);





		console.log("S");
		faltan--;
		localStorage.setItem("wappsi_faltan", faltan);
		$(".asistenciaFaltantes").html(faltan);

		var aux = JSON.parse(localStorage.getItem("wappsi_ausentes"));
		var index = aux.indexOf($(this).val());
		if (index > -1) {
			aux.splice(index, 1);
		}		
		localStorage.setItem("wappsi_ausentes", JSON.stringify(aux));



	});
	
	$(document).on('ifUnchecked', '.checkbox-header', function () { 
		$('.checkbox'+ $(this).data('columna')).iCheck('uncheck'); 
		console.log("N");
		faltan++;
		localStorage.setItem("wappsi_faltan", faltan);
		$(".asistenciaFaltantes").html(faltan);

		var aux = JSON.parse(localStorage.getItem("wappsi_ausentes"));
		aux.push($(this).val());
		localStorage.setItem("wappsi_ausentes", JSON.stringify(aux));




	});
	


	$('#btnBuscar').click(function(){
		if($('#form_asistencia').valid()){
			validarAsistenciaSellada();
		}
	});






















	$('.btnGuardar').click(function(){
		datatables.search('').draw();
		guardarAsistencia();
	});		



	$('#btnRestablecerContadores').click(function(){
		restablecerContadores();
	});		
});

function restablecerContadores(){
	localStorage.removeItem("wappsi_total");
	localStorage.removeItem("wappsi_faltan");
	localStorage.removeItem("wappsi_ausentes");
	console.log("Borrar almacenamiento local");
	Command : toastr.success( "Exito!", "Se ha borrado con éxito el almacenamiento local.", { onHidden : function(){}});
}

function guardarAsistencia(){
	console.log("Guardar asistencia.");

	var mes = "";
	var semana = "";
	var dia = "";


	var bandera = 0;	
	var asistencia = [];
	var documento = "";
	var tipoDocumento = "";

	if($('#mes').val() != "" && $('#mes').val() != null){
		mes = $('#mes').val();
	}	

	if($('#semana').val() != "" && $('#semana').val() != null){
		semana = $('#semana').val();
	}else{
		semana = $('#semanaActual').val();
	}

	if($('#dia').val() != "" && $('#dia').val() != null){
		dia = $('#dia').val();
	}	


	
	var formData = new FormData();
	
	formData.append('mes', mes);
	formData.append('semana', semana);
	formData.append('dia', dia);

	formData.append('sede', $('#sede').val());
	formData.append('grado', $('#grado').val());
	formData.append('grupo', $('#grupo').val());
	formData.append('banderaRegistros', $('#banderaRegistros').val());

	var cantidadAsistentes = 0;

	$( ".checkbox-header:checked").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('asistencia['+documento+'][documento]', documento);
		formData.append('asistencia['+documento+'][tipoDocumento]', tipoDocumento);
		formData.append('asistencia['+documento+'][asistencia]', 1);
		cantidadAsistentes++;
	});	

	$( ".checkbox-header:not(:checked)").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('asistencia['+documento+'][documento]', documento);
		formData.append('asistencia['+documento+'][tipoDocumento]', tipoDocumento);
		formData.append('asistencia['+documento+'][asistencia]', 0);
	});




	if(cantidadAsistentes <= 0){
		// bandera++;
		// Command:toastr.warning("Debe asistir almenos un estudiante.","Alerta!",{onHidden:function(){$('#loader').fadeOut();}});
	}



	if(bandera == 0){
		console.log("Guardar");
		$.ajax({
			type: "post",
			url: "functions/fn_guardar_asistencia.php",
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
				Command:toastr.warning(data.mensaje,"Atención",{onHidden:function(){$('#loader').fadeOut(); location.reload();}});

			}
			else{
				$('#loader').fadeOut();
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
	var dibujado = 0;


	var mes = $('#mes').val();
	var semana = $('#semana').val();
	var dia = $('#dia').val();


	var semanaActual = $('#semanaActual').val();
	var sede = $('#sede').val();
	var nivel = $('#nivel').val();
	var grado = $('#grado').val();
	var grupo = $('#grupo').val();
	var aux = JSON.parse(localStorage.getItem("wappsi_ausentes"));
	console.log(aux);




	if ( $.fn.DataTable.isDataTable( '.dataTablesSedes' ) ) {
		datatables.destroy();

	}



	datatables = $('.dataTablesSedes').DataTable({
	ajax: {
		method: 'POST',
		url: 'functions/fn_buscar_estudiantes.php',
		data:{
			mes: mes,
			semana: semana,
			dia: dia,
			semanaActual: semanaActual,
			sede: sede,
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
				// console.log("Dentro de las filas: "+meta.settings.json.banderaRegistros);
				banderaRegistros = meta.settings.json.banderaRegistros;	
				var tipoDocumento = full.tipo_doc;
				var documento = full.num_doc;	

				var asistencia = full.asistencia;	
				var repite = full.repite;	
				var consumio = full.consumio;	
				var repitio = full.repitio;	

				//console.log(aux);
				var index = aux.indexOf(documento);	


				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header\" ";
				
				if(banderaRegistros == 1){
					if(asistencia == 1){
						opciones = opciones + " checked "; 
					}
				}else{
					if (index > -1) {}else{
						opciones = opciones + " checked "; 
					}
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
			console.log("Bandera Registros: "+banderaRegistros);
			$("#banderaRegistros").val(banderaRegistros);
			actualizarMarcadores(0);
			dibujado++;	
			if(banderaRegistros == 1){
				$(".editando").fadeIn();
				$(".btnGuardar").html("Actualizar");				
			}else{
				$(".editando").fadeOut();
				$(".btnGuardar").html("Guardar");
			}
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



	});	
}