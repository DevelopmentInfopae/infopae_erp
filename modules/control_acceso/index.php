<?php
include '../../config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Control de acceso</title>
	<link href="<?php echo $baseUrl; ?>/theme/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="<?php echo $baseUrl; ?>/theme/fontawesome-free-5.11.2-web/css/all.css" rel="stylesheet">
	<link rel="stylesheet" href="estilos.css">
</head>
<body class="control-acceso">



	<div class="control-acceso__contenedor">
		<div class="hora-actual"></div>
		<div class="fecha-actual"></div>
		<div class="instruccion">
		Acerque su carnet al lector
		</div>
		<div id="form-documento"> <input type="text" name="documento" id="documento"> </div>
		<div class="mensaje-validacion">
			
		</div>
		<div class="registro-foto">
			<div class="registro-foto__salida"><i class="fas fa-caret-left"></i></div>
			<div class="registro-foto__foto">
				
			</div>
			<div class="registro-foto__entrada"><i class="fas fa-caret-right"></i></div>
		</div>
		<div class="nombre"></div>
		<div class="cargo"></div>
	</div>











<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>






<script>
	function clockTick(){
		var currentTime = new Date();
		var year = currentTime.getFullYear();
		var month = currentTime.getMonth() + 1;
		var day = currentTime.getDate();
		var hours = currentTime.getHours();
		var minutes = currentTime.getMinutes();
		var seconds = currentTime.getSeconds();
		if(hours < 10){
			hours = "0"+hours;
		}
		if(minutes < 10){
			minutes = "0"+minutes;
		}
		aux = hours + ":"+ minutes;
		$('.hora-actual').html(aux);
	}
	setInterval(function(){clockTick();}, 1000);//setInterval(clockTick, 1000); will also work
	
	var days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
	var months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
	var currentTime = new Date();
	var dayName = days[currentTime.getDay()];
	var monthName = months[currentTime.getMonth()];
	var year = currentTime.getFullYear();
	var day = currentTime.getDate();
	var aux = dayName + ", " + day + " de " + monthName + " " + year;
	console.log(aux);
	$('.fecha-actual').html(aux);

	$(document).ready(function(){
		$( "#documento" ).change(function() {
		var formData = new FormData();
		formData.append('documento', $('#documento').val());
		$.ajax({
			type: "post",
			url: "functions/fn_validar_documento.php",
			dataType: "json",
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function(){ $('#loader').fadeIn(); },
			success: function(data){
				console.log(data);
				if(data.estado == 1){
					//Command:toastr.warning(data.mensaje,"Atención",{onHidden:function(){$('#loader').fadeOut(); location.reload();}});
					//$('#loader').fadeOut();
					$('.mensaje-validacion').removeClass('mensaje-validacion__rechazado');
					$('.mensaje-validacion').addClass('mensaje-validacion__autorizado');
					$('.mensaje-validacion').html('Documento autorizado');
					$('.registro-foto__entrada').removeClass('activado');
					$('.registro-foto__salida').removeClass('activado');
					$('.registro-foto__entrada').removeClass('negado');
					$('.registro-foto__salida').removeClass('negado');
					if(data.tipo == 1){
						$('.registro-foto__entrada').addClass('activado');

					}else{
						$('.registro-foto__salida').addClass('activado');
					}
					$('.nombre').html(data.nombre);
					$('.cargo').html(data.cargo);
					
					var aux = "<img src=\""+data.foto+"\" alt=\"Foto de perfil\">";
					$('.registro-foto__foto').html(aux);


		

					
					
					
				}
				else{
					$('.mensaje-validacion').removeClass('mensaje-validacion__autorizado');
					$('.mensaje-validacion').addClass('mensaje-validacion__rechazado');
					$('.mensaje-validacion').html('Documento no autorizado');
					//$('#loader').fadeOut();
					
					
					$('.registro-foto__entrada').removeClass('activado');
					$('.registro-foto__salida').removeClass('activado');

					$('.registro-foto__entrada').addClass('negado');
					$('.registro-foto__salida').addClass('negado');

					$('.nombre').html('');
					$('.cargo').html('');
					$('.registro-foto__foto').html('');


				}
				$( "#documento" ).val('');
				$( "#documento" ).focus();
			},
			error: function(data){
				console.log(data);
				Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
			}
		});


















		});
	});


</script>
</body>
</html>