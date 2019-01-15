<?php

// session_start();
//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"]!="SI" || ( $_SESSION["perfil"]!=0 && $_SESSION["perfil"]!=1 && $_SESSION["perfil"]!=5 && $_SESSION["perfil"]!=6 ) ) {
	//si no existe, envio a la página para loguearse.
	header("Location: $baseUrl/login.php");
	exit();
}

//var_dump($_SESSION);
$usuario = $_SESSION['usuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];


if(isset($index) && $index == 1){
	if ($_SESSION["perfil"]==6){
		header("Location: $baseUrl/index_rector.php");
	}
}

