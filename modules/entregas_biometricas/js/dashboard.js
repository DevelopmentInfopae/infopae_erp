$(document).ready(function(){
	mueveReloj();
	fechaActual();

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
	  console.log(`${day}-0${month}-${year}`);
	  fechaActual = `${day}-0${month}-${year}`;
	}else{
	  console.log(`${day}-${month}-${year}`);
	  fechaActual = `${day}-${month}-${year}`;
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

	horaImprimible = hora + " : " + minuto + " : " + segundo + ' ' + ampm;

	//document.form_reloj.reloj.value = horaImprimible 
	$('.hora-actual').html(horaImprimible);

	//La función se tendrá que llamar así misma para que sea dinámica, 
	//de esta forma:

	setTimeout(mueveReloj,1000);
}