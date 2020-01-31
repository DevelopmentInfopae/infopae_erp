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
	buscarNuevosRegistros();

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

function buscarNuevosRegistros(){
	console.log('Buscando Nuevos Registros');
	setTimeout(buscarNuevosRegistros,2000);
}