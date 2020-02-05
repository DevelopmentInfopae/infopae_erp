var municipioBusqueda = "";
var institucionBusqueda = "";
var sedeBusqueda = "";
var ultimoRegistro = 0;

$(function() {

	var container = $("#flot-line-chart-moving");

	// Determine how many data points to keep based on the placeholder's initial size;
	// this gives us a nice high-res plot while avoiding more than one point per pixel.

	var maximum = container.outerWidth() / 2 || 300;

	//

	var data = [];

	function getRandomData() {

		if (data.length) {
			data = data.slice(1);
		}

		while (data.length < maximum) {
			var previous = data.length ? data[data.length - 1] : 50;
			var y = previous + Math.random() * 10 - 5;
			data.push(y < 0 ? 0 : y > 100 ? 100 : y);
		}

		// zip the generated y values with the x values

		var res = [];
		for (var i = 0; i < data.length; ++i) {
			res.push([i, data[i]])
		}

		return res;
	}

	series = [{
		data: getRandomData(),
		lines: {
			fill: true
		}
	}];


	var plot = $.plot(container, series, {
		grid: {

			color: "#999999",
			tickColor: "#D4D4D4",
			borderWidth:0,
			minBorderMargin: 20,
			labelMargin: 10,
			backgroundColor: {
				colors: ["#ffffff", "#ffffff"]
			},
			margin: {
				top: 8,
				bottom: 20,
				left: 20
			},
			markings: function(axes) {
				var markings = [];
				var xaxis = axes.xaxis;
				for (var x = Math.floor(xaxis.min); x < xaxis.max; x += xaxis.tickSize * 2) {
					markings.push({
						xaxis: {
							from: x,
							to: x + xaxis.tickSize
						},
						color: "#fff"
					});
				}
				return markings;
			}
		},
		colors: ["#1ab394"],
		xaxis: {
			tickFormatter: function() {
				return "";
			}
		},
		yaxis: {
			min: 0,
			max: 110
		},
		legend: {
			show: true
		}
	});

	// Update the random dataset at 25FPS for a smoothly-animating chart

	setInterval(function updateRandom() {
		series[0].data = getRandomData();
		plot.setData(series);
		plot.draw();
	}, 40);

});

$(document).ready(function(){
	mueveReloj();
	fechaActual();
	cargarMunicipios();
	
	$( "#municipio" ).change(function() {
		localStorage.setItem("wappsi_municipio", $("#municipio").val());
		cargarInstituciones();
	});
	
	$( "#institucion" ).change(function() {
		localStorage.setItem("wappsi_institucion", $("#institucion").val());
		cargarSedes();
	});
	
	$( "#sede" ).change(function() {
		localStorage.setItem("wappsi_sede", $("#sede").val());
	});
	
	$( "#btnFiltro" ).click(function() {
		console.log('Click en aplicar Filtro');
	});

	

	

	// cargarMunicipios();

	// if(localStorage.getItem("wappsi_mes") != null){
	// 	$( "#mes" ).val(localStorage.getItem("wappsi_mes"));
	// 	cargarSemanas();	
	// }
});

function fechaActual(){
	let date = new Date();
	let day = date.getDate();
	let month = date.getMonth() + 1;
	let year = date.getFullYear();
	
	if(month < 10){
	  console.log(`${day}/0${month}/${year}`);
	  fechaActual = `${day}/0${month}/${year}`;
	}else{
	  console.log(`${day}/${month}/${year}`);
	  fechaActual = `${day}/${month}/${year}`;
	}
	$('.fecha-actual').html(fechaActual);
}

function mueveReloj(){ 
	momentoActual = new Date();
	hora = momentoActual.getHours();
	minuto = momentoActual.getMinutes();
	segundo = momentoActual.getSeconds();
	
	var ampm = hora >= 12 ? 'pm' : 'am';
	hora = hora % 12;
	hora = hora ? hora : 12; // the hour '0' should be '12'
	hora = hora < 10 ? '0'+hora : hora;
	minuto = minuto < 10 ? '0'+minuto : minuto;
	segundo = segundo < 10 ? '0'+segundo : segundo;

	//horaImprimible = hora + ":" + minuto + ":" + segundo + '' + ampm;
	horaImprimible = hora + ":" + minuto + '' + ampm;

	//document.form_reloj.reloj.value = horaImprimible 
	$('.hora-actual').html(horaImprimible);

	//La función se tendrá que llamar así misma para que sea dinámica, 
	//de esta forma:

	setTimeout(mueveReloj,1000);
}

function cargarMunicipios(){
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
				var aux = $('#municipio').val();
				$('#municipio').html(data.opciones);
				$('#loader').fadeOut();
				cargarInstituciones();
				municipioBusqueda = $('#municipio').val();
				console.log('Municipio para hacer la busqueda: '+municipioBusqueda);
				/* Si no habia un valor de munipio anterior es necesario buscar lo totales de las sedes .*/
				if(aux == null){
					buscarTotalesSedes();
				}
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
	console.log("Cargar Instituciones.");
	var formData = new FormData();
	formData.append('municipio', $('#municipio').val());
	if($('#validacion').val() != null){
		console.log($('#validacion').val());
		formData.append('validacion', $('#validacion').val());
	}

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
				
				$('#institucion').val(localStorage.getItem("wappsi_institucion"));
				localStorage.setItem("wappsi_institucion", $("#institucion").val());
				if($('#institucion').val() != ""){
					cargarSedes()
				}
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
	console.log("Cargar Sedes.");
	var formData = new FormData();
	formData.append('institucion', $('#institucion').val());
	if($('#validacion').val() != null){
		console.log($('#validacion').val());
		formData.append('validacion', $('#validacion').val());
	}
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
				$('#sede').val(localStorage.getItem("wappsi_sede"));
				localStorage.setItem("wappsi_institucion", $("#institucion").val());
				if($('#sede').val() != ""){
					//cargarNiveles();
					//cargarDispositivos();
				}
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

function buscarTotalesSedes(){
	console.log('Buscando Totales de las entregas en las sedes.');

	var formData = new FormData();

	formData.append('anno', $('#anno').val());
	formData.append('mes', $('#mes').val());
	formData.append('dia', $('#dia').val());
	formData.append('semana', $('#semana').val());
	formData.append('municipio', $('#municipio').val());
	formData.append('institucion', $('#institucion').val());
	formData.append('sede', $('#sede').val());

	$.ajax({
		type: "post",
		url: "functions/fn_buscar_totales_entregas.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				console.log('Totales cargados');
				$('.sedes').html(data.cuerpo);
				ultimoRegistro = data.ultimo_registro;
				console.log("Ultimo Registro: "+ultimoRegistro);

				// $('#dia').html(data.opciones);
				// $('#dia').val(localStorage.getItem("wappsi_dia"));
				// localStorage.setItem("wappsi_dia", $("#dia").val());
				// if($('#semana').val() != ""){
				// 	cargarDias()
				// }
				$('#loader').fadeOut();
				setTimeout("buscarNuevosRegistros()",2000);
		

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

function buscarNuevosRegistros(){
	console.log('Buscando Nuevos Registros');
	console.log(ultimoRegistro);

	var formData = new FormData();

	formData.append('ultimoRegistro', ultimoRegistro);
	formData.append('anno', $('#anno').val());
	formData.append('mes', $('#mes').val());
	formData.append('dia', $('#dia').val());
	formData.append('semana', $('#semana').val());
	formData.append('municipio', $('#municipio').val());
	formData.append('institucion', $('#institucion').val());
	formData.append('sede', $('#sede').val());

	$.ajax({
		type: "post",
		url: "functions/fn_buscar_nuevos_registros.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			console.log(data);
			if(data.estado == 1){
				console.log('Terminada la verificación de nuevos registros.');
				var codSede = data.codSede;
				$('.entregas').prepend(data.cuerpo);
				console.log("Sede que recibió registro: "+codSede);

				var aux = $('.entregado-'+codSede).html();
				aux = parseInt(aux);
				aux++;
				$('.entregado-'+codSede).html(aux);

				
				
				
				
				
				if ( $(".entrega").length > 9){
					$(".entrega").last().remove();
				}

				//entregas


				// $('.sedes').html(data.cuerpo);
				// ultimoRegistro = data.ultimo_registro;
				// console.log("Ultimo Registro: "+ultimoRegistro);

				// // $('#dia').html(data.opciones);
				// // $('#dia').val(localStorage.getItem("wappsi_dia"));
				// // localStorage.setItem("wappsi_dia", $("#dia").val());
				// // if($('#semana').val() != ""){
				// // 	cargarDias()
				// // }
				$('#loader').fadeOut();

			}
			else{
				//Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
	setTimeout(buscarNuevosRegistros,2000);
}