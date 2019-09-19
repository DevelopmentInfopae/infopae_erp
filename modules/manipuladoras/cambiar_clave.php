<?php 
include_once 'config.php'; 
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"]!="SI") {
	header("Location: index.php");
	exit();
}
?>
<!DOCTYPE html>
<html>

<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title><?php echo $tituloProyecto; ?> - Cambiar Contraseña</title>
		<link rel="shortcut icon" href="favicon.ico" />

		<link href="theme/css/bootstrap.min.css" rel="stylesheet">
		<link href="theme/font-awesome/css/font-awesome.css" rel="stylesheet">

		<link href="theme/css/animate.css" rel="stylesheet">
		<link href="theme/css/style.css" rel="stylesheet">

</head>

<body class="gray-bg fnd-login">
	<div class="middle-box text-center loginscreen animated fadeInDown caja-login">
		<div>
			<div> <img src="img/logo.png" alt="logo" class="logo" > </div>
			<div><h3 style="color:#ffffff;">Es necesario cambiar la contraseña actual</h3></div>
			<form class="m-t" role="form" action="">
				<div class="form-group">
					<input type='password' class="form-control" name="pass" id="pass" required placeholder="Contraseña Actual">
				</div>				
				<div class="form-group">
					<input type='password' class="form-control" name="pass1" id="pass1" required placeholder="Nueva Contraseña">
				</div>				
				<div class="form-group">
					<input type='password' class="form-control" name="pass2" id="pass2" required placeholder="Confirme Nueva Contraseña">
				</div>
				<button type='button'  class="btn btn-primary block full-width m-b" value='Enviar' onclick="cambiar_pass()">Enviar</button>
			</form>
		</div>
		<div id="debug"></div>
		<p class="m-t"> <small>&copy; 2018</small> </p>
	</div>


		<!-- Mainly scripts -->
		<script src="theme/js/jquery-3.1.1.min.js"></script>
		<script src="theme/js/bootstrap.min.js"></script>

		<script src="js/funciones.js"></script>

		<script type="text/javascript">
		function cambiar_pass(){
			var bandera = 0;			
			var pass = $('#pass').val();
			var pass1 = $('#pass1').val();
			var pass2 = $('#pass2').val();

			if ( pass == "" ) {
				alert("Debe digitar su contraseña actual.");	
				bandera++;
				$('#pass').focus();
			} else if ( pass1.length < 7 ) {
				alert("Las contraseña debe tener como minimo 7 caracteres.");	
				bandera++;
				$('#pass1').focus();
			}
			else if ( pass1 != pass2 ) {
				alert("Las contraseñas no coinciden por favor verifiquelas");
				bandera++;
				$('#pass2').focus();
			}

			if(bandera == 0){ 
				pass=sha1(pass);
				pass1=sha1(pass1);
				pass2=sha1(pass2);
				var datos = {
					"pass" : pass,
					"pass1" : pass1,
					"pass2" : pass2
				};
				$.ajax({
					type:"POST",
					url:"functions/fn_cambioclave.php",
					dataType:"html",
					data:datos,
					beforeSend:function(){}, 
					success:function(response){
						console.log(response);
						if ( response == 1 ){
							alert("La contrase\xf1a se cambio exitosamente.");
							window.location = "login.php";
							// var i = <?php echo $_SESSION['perfil'];  ?>;
							// if ( i != 4 ) {
							// 	window.location = "inicio.php";
							// } else if ( i == 4 ) {
							// 	window.location = "carga_de_archivos.php";
							// }
						} else if ( response == 2 ) {
							alert("La clave actual no es correcta.");
						} else if ( response == 3 ) {
							alert("Las contraseñas no coinciden por favor verifiquelas");
						} else {
							$('#debug').html(response);
						}
							
					}
				});
			} 
		}
		</script>







</body>

</html>





























