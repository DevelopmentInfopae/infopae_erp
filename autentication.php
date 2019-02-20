<?php
	// COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
	if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"]!="SI" || ( $_SESSION["perfil"]!=0 && $_SESSION["perfil"]!=1 && $_SESSION["perfil"]!=5 && $_SESSION["perfil"]!=6 ) ) {
		//si no existe, envio a la página para loguearse.
		header("Location: $baseUrl/login.php");
		exit();
	}

	$url_peticion = trim($_SERVER["REQUEST_URI"], "/");
	$posicion = strpos($url_peticion, "/");
	$url_lista = $posicion != FALSE ? substr($url_peticion, $posicion) : "";

	if ($token_seguridad != $_SESSION["token_seguridad"]) {
	?>
		<script>
			var	confirmacion =  confirm('Está intentando ingresar a otro sistema. Desea continuar?');
			if (confirmacion) {
				location.href = '<?= $baseUrl; ?>/cerrar_sesion.php';
			} else {
				location.href = '<?= $_SESSION["url"] . $url_lista; ?>';
			}
		</script>
	<?php
	}

	$usuario = $_SESSION['usuario'];
	$tipoUsuario = $_SESSION['tipoUsuario'];


	if(isset($index) && $index == 1){
		if ($_SESSION["perfil"]==6){
			header("Location: $baseUrl/index_rector.php");
		}
	}

