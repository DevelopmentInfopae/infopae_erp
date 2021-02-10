jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });



var total = 0;
var faltan = 0;
var ausentes = [];
var repitentes = [];
var datatables = null;



for (x=0; x<=localStorage.length-1; x++)  {  
  clave = localStorage.key(x); 
  //document.write("La clave " + clave + "contiene el valor " + localStorage.getItem(clave) + "<br />");  
  console.log("La clave " + clave + " contiene el valor " + localStorage.getItem(clave));  
}

console.log(localStorage.getItem("wappsi_sede"));

$(document).ready(function(){

	var d = new Date();
	var month = d.getMonth();
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

	// $(".asistenciaFaltantes").html(faltan);
	// $(".asistenciaTotal").html(total);


	console.log("Total: "+total);
	console.log("Faltan: "+faltan);



	$('#btnBuscar').click(function(){
		if($('#form_asistencia').valid()){
			validarAsistenciaSellada();			
		}
	});

























	// Check a cada item
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




			



			// if(faltan <= 0){$( ".checkbox-header:not(:checked)").iCheck('disable'); }



		}
	});






	
	// unCheck a cada item
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












	$( "#grado" ).change(function() {
		cargarGrupos();
	});



	$('.btnGuardar').click(function(){
		datatables.search('').draw();
		guardarRepitentes();
	});		



	$('#btnRestablecerContadores').click(function(){
		restablecerContadores();
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
				Command:toastr.warning(data.mensaje,"Atención",{onHidden:function(){$('#loader').fadeOut(); location.reload();}});

			}
			else{
				$('#loader').fadeOut();
				cargarRepitentes();
			}		
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function cargarRepitentes(){
	var dibujado = 0;

	if($('#mes').val() != "" && $('#mes').val() != null ){
		var mes = $('#mes').val();
	}else{
		var mes = "";
	}

	if($('#mes').val() != "" && $('#mes').val() != null ){
		var dia = $('#dia').val();
	}else{
		var dia = "";
	}



	if($('#semana').val() != "" && $('#semana').val() != null ){
		var semanaActual = $('#semana').val();
	}else{
		var semanaActual = $('#semanaActual').val();
	}


	var sede = $('#sede').val();
	var complemento = $('#complemento').val();
	var nivel = $('#nivel').val();
	var grado = $('#grado').val();
	var grupo = $('#grupo').val();




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
				var tipoDocumento = full.tipo_doc;
				var documento = full.num_doc;
				var repite = full.repite;

				
				var index = aux.indexOf(documento);
				
				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header checkbox-header-repite\" ";
				
				//if (index > -1) { opciones = opciones + " checked "; }else{ }
				
				if(repite == 1){opciones = opciones + " checked "; }
				
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
				var repite = full.repite;
				var favorito = full.favorito;

				
				var index = aux.indexOf(documento);
				
				var opciones = " <div class=\"i-checks text-center\"> <input type=\"checkbox\" class=\"checkbox-header checkbox-header-favorito\" ";
				
				//if (index > -1) { opciones = opciones + " checked "; }else{ }
				
				if(favorito != null){
					opciones = opciones + " checked favorito=\"1\" "; 
				}else{
					opciones = opciones + " favorito=\"0\" ";	
				}






				
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
		if(dibujado == 0){
			$('#loader').fadeOut();
			actualizarMarcadores(0);
			dibujado++;
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


        //if(faltan <= 0){$( ".checkbox-header:not(:checked)").iCheck('disable'); }


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



	var mes = "";
	var dia = "";

	if($('#mes').val() != "" && $('#mes').val() != null){
		var mes = $('#mes').val();	
	}

	if($('#dia').val() != "" && $('#dia').val() != null){
		var dia = $('#dia').val();	
	}













	

	if($('#semana').val() != "" && $('#semana').val() != null){
		var semana = $('#semana').val();	
	}else{
		var semana = $('#semanaActual').val();	
	}


	var formData = new FormData();
	formData.append('mes', mes);
	formData.append('dia', dia);
	formData.append('semana', semana);
	formData.append('sede', $('#sede').val());
	formData.append('complemento', $('#complemento').val());

	var cantidadRepitentes = 0;
	$( ".checkbox-header-repite:checked").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('repitente['+documento+'][documento]', documento);
		formData.append('repitente['+documento+'][tipoDocumento]', tipoDocumento);
		formData.append('repitente['+documento+'][repite]', 1);
		cantidadRepitentes++;
	});	

	$( ".checkbox-header-repite:not(:checked)").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('repitente['+documento+'][documento]', documento);
		formData.append('repitente['+documento+'][tipoDocumento]', tipoDocumento);
		formData.append('repitente['+documento+'][repite]', 0);
	});

	// Favoritos
	// Nuevos favoritos
	console.log("Los Favoritos:");
	$(".checkbox-header-favorito[favorito='0']:checked").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('repitente['+documento+'][favorito]', 1);
		//console.log(documento+" Favorito: "+1);
	});
	// Dejan de ser favoritos
	$(".checkbox-header-favorito[favorito='1']:not(:checked)").each(function(){
		documento = $(this).val();
		tipoDocumento = $( this ).attr('tipoDocumento');
		formData.append('repitente['+documento+'][favorito]', 0);
		//console.log(documento+" Favorito: "+0);
	});
	//bandera++;







	if(cantidadRepitentes <= 0){
		bandera++;
		Command:toastr.warning("Debe seleccionar al menos un estudiante para que repita.","Alerta!",{onHidden:function(){$('#loader').fadeOut();}});
	}

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