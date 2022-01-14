var municipioBusqueda = "";
var institucionBusqueda = "";
var sedeBusqueda = "";
var ultimoRegistro = 0;
var totalEntregas = 0;
var totalEntregado = 0;
var flagBuscarNuevos = 0;
var data = [];

$(function() {
	var container = $("#flot-line-chart-moving");
	var maximum = container.outerWidth() / 2 || 300;
	function getRandomData() {
		if (data.length) {
			data = data.slice(1);
		}
		while (data.length < maximum) {
			var previous = data.length ? data[data.length - 1] : 50;
			var y = previous + Math.random() * 10 - 5;
			var y = totalEntregado;
			data.push(y < 0 ? 0 : y > 1000 ? 1000 : y);
		}
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
			max: 1000
		},
		legend: {
			show: true
		}
	});

	setInterval(function updateRandom() {
		series[0].data = getRandomData();
		plot.setData(series);
		var opts = plot.getOptions();
		opts.yaxes[0].max = totalEntregado * 2; 
		plot.setupGrid();
		plot.draw();
	}, 40);
});

$(document).ready(function(){
	mueveReloj();
	fechaActual();
	cargarMunicipios();

	alturaVentana = $( document  ).height();
	alturaVentana = $( window ).height();
	alturaZonaTop = $(".dashboard-top").height();
	$(".dashboard-bottom").height(alturaVentana-alturaZonaTop-20);
	$(".sedes").height(alturaVentana-alturaZonaTop-100);
	$(".entregas").height(alturaVentana-alturaZonaTop-100);
	$( window ).resize(function() {
		alturaVentana = $( document  ).height();
		alturaVentana = $( window ).height();
		alturaZonaTop = $(".dashboard-top").height();
		$(".dashboard-bottom").height(alturaVentana-alturaZonaTop-20);
		$(".sedes").height(alturaVentana-alturaZonaTop-100);
		$(".entregas").height(alturaVentana-alturaZonaTop-100);
	});
			
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
		data = [];
		municipioBusqueda = $('#municipio').val();
		institucionBusqueda = $('#institucion').val();
		sedeBusqueda = $('#sede').val();
		ultimoRegistro = 0;
		totalEntregas = 0;
		totalEntregado = 0;
		$('.sedes').html('');
		$('.entregas').html('');
		buscarTotalesSedes();
	});

	$( "#btnSicronizar" ).click(function() {
		sincronizar();
	});
});

function fechaActual(){
	let date = new Date();
	let day = date.getDate();
	let month = date.getMonth() + 1;
	let year = date.getFullYear();
	if(month < 10){ fechaActual = `${day}/0${month}/${year}`; }
	else{ fechaActual = `${day}/${month}/${year}`; }
}

function mueveReloj(){ 
	momentoActual = new Date();
	hora = momentoActual.getHours();
	minuto = momentoActual.getMinutes();
	segundo = momentoActual.getSeconds();
	var ampm = hora >= 12 ? 'pm' : 'am';
	hora = hora % 12;
	hora = hora ? hora : 12; 
	hora = hora < 10 ? '0'+hora : hora;
	minuto = minuto < 10 ? '0'+minuto : minuto;
	segundo = segundo < 10 ? '0'+segundo : segundo;
	horaImprimible = hora + ":" + minuto + '' + ampm;
	$('.hora-actual').html(horaImprimible);
	setTimeout(mueveReloj,1000);
}

function cargarMunicipios(){
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_municipios.php",
		dataType: "json",
		contentType: false,
		processData: false,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				var aux = $('#municipio').val();
				$('#municipio').html(data.opciones);
				$('#loader').fadeOut();
				cargarInstituciones();
				municipioBusqueda = $('#municipio').val();
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
	if($('#validacion').val() != null){
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
				$("#institucion").val($("#target option:first").val());
				if($('#institucion').val() != ""){ cargarSedes() }
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
	if($('#validacion').val() != null){
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
				$("#sede").val($("#target option:first").val());
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
	flagBuscarNuevos = 0;
	var formData = new FormData();
	formData.append('anno', $('#anno').val());
	formData.append('mes', $('#mes').val());
	formData.append('dia', $('#dia').val());
	formData.append('semana', $('#semana').val());
	formData.append('municipio', municipioBusqueda);
	formData.append('institucion', institucionBusqueda);
	formData.append('sede', sedeBusqueda);
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_totales_entregas.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ },
		success: function(data){
			if(data.estado == 1){
				$('.sedes').html(data.cuerpo);
				totalEntregas = data.total_entregas;
				totalEntregado = data.total_entregado;
				flagBuscarNuevos = 1;				
				$('.totales-contenido').fadeIn();
				$('.total-entregado').html(totalEntregado);
				$('.total-entregar').html(totalEntregas);
				$('.overlay').fadeOut();
				if(flagBuscarNuevos == 1){
					setTimeout("buscarNuevosRegistros()",500);
				}
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('.overlay').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function buscarNuevosRegistros(){
	var formData = new FormData();
	formData.append('ultimoRegistro', ultimoRegistro);
	formData.append('anno', $('#anno').val());
	formData.append('mes', $('#mes').val());
	formData.append('dia', $('#dia').val());
	formData.append('semana', $('#semana').val());
	formData.append('municipio', municipioBusqueda);
	formData.append('institucion', institucionBusqueda);
	formData.append('sede', sedeBusqueda);
	if(flagBuscarNuevos == 1){
		$.ajax({
			type: "post",
			url: "functions/fn_buscar_nuevos_registros.php",
			dataType: "json",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){
				flagBuscarNuevos = 0;
				$('#loader').fadeIn(); 
			},
			success: function(data){
				flagBuscarNuevos = 1;
				if(data.estado == 1){
					var codSede = data.codSede;
					$('.entregas').prepend(data.cuerpo);
					var aux = $('.entregado-'+codSede).html();
					aux = parseInt(aux);
					aux++;
					$('.entregado-'+codSede).html(aux);
					var auxt = $('.total-'+codSede).html();
					auxt = parseInt(auxt);
					if($('.circulo-'+codSede).hasClass( "gris" )){
						$('.circulo-'+codSede).removeClass("gris");
						$('.circulo-'+codSede).addClass("naranja");
					}
					if(aux >= auxt){
						$('.circulo-'+codSede).removeClass("gris");
						$('.circulo-'+codSede).removeClass("naranja");
						$('.circulo-'+codSede).addClass("verde");
					}
					totalEntregado = totalEntregado + 1;
					$('.total-entregado').html(totalEntregado);
					if(data.ultimoRegistro > 0){ ultimoRegistro = data.ultimoRegistro; }
					else{ ultimoRegistro = 0; }
					if ( $(".entrega").length > 10){ $(".entrega").last().remove();}
					$('#loader').fadeOut();
					// desencadenador();				
				}else{
					// desencadenador();
				}
				buscarTotalesSedes();
			},
			error: function(data){
				console.log(data);
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
			}
		});
	}
}

function sincronizar(){
	var formData = new FormData();
	$.ajax({
		type: "post",
		url: "functions/fn_sincronizacion_sqlserver_mysql.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ 
			$('.overlay').fadeIn(); 
		},
		success: function(data){
			if(data.estado == 1){
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('.overlay').fadeOut();}});
			}
			$('.overlay').fadeOut();
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}

function desencadenador () {
	$.ajax({
		url: 'desencadenador2.php',
		type: 'GET',
		// dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
		// data: {param1: 'value1'},
	})
	.done(function(data) {
		// console.log(data);
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		// console.log("complete");
	});
	
}