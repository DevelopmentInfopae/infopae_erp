<?php 
$idPerfil = $_SESSION['perfil'];
$permisos = [];
$consultaPermisos = " SELECT *
						FROM perfiles	
						WHERE id = $idPerfil; ";

$respuestaPermisos = $Link->query($consultaPermisos) or die ('Error al consultar los permisos. ' . mysqli_error($Link));						
if ($respuestaPermisos->num_rows > 0 ) {
	$dataPermisos = $respuestaPermisos->fetch_assoc();
	$permisos = $dataPermisos;
}
