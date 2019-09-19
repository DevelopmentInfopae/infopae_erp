<?php

	session_start();
	//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
	if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"]!="SI" || ( $_SESSION["perfil"]!=4 ) ) {
    	//si no existe, envio a la página para loguearse.
		header("Location: $baseUrl/login.php");
		exit();
	}

	//var_dump($_SESSION);
	$usuario = $_SESSION['usuario'];
	$tipoUsuario = $_SESSION['tipoUsuario'];
?>
