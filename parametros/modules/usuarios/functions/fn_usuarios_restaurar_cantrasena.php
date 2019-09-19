<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

  $clave = sha1(substr($_POST["nombre"], 0, 1) . $_POST["numeroDocumento"]);
	$id = $_POST["id"];

	$consulta1 = " UPDATE usuarios SET clave = '$clave' WHERE id = '$id'";
	$resultado1 = $Link->query($consulta1) or die ("Unable to execute query.". mysql_error($Link));
	if ($resultado1){
		$respuestaAJAX = array(
			"estado" => 1,
			"mensaje" => "La contraseña ha sido restaurada con éxito."
		); 

		$consulta = "SELECT nombre FROM usuarios WHERE id='$id'";
		$resultado = $Link->query($consulta) or die ("Unable to execute query.". mysql_error($Link));
		$dato = $resultado->fetch_assoc();
		$nombre = $dato["nombre"];

		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '24', 'Restauró la contraseña del usuario <strong>$nombre</strong>')";
		$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
	} else {
		$respuestaAJAX = array(
			"estado" => 0,
			"mensaje" => "La contraseña NO ha sido restaurada con éxito."
		);
	}

	echo json_encode($respuestaAJAX);