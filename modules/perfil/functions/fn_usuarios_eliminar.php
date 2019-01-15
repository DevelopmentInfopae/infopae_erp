<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	$id = $_POST["id"];
	$usuarioSession = 90;
	$fechaHora = date("Y-m-d H-i-s");


	$consulta1 = " DELETE FROM usuarios WHERE id = '$id' ";
	$resultado1 = $Link->query($consulta1) or die ("Unable to execute query.". mysql_error($Link));

	if($resultado1){
		$result = array(
			"estado" => 1, 
			"mensaje" => "El usuario ha sido eliminado con éxito."
		);

		$consulta = "SELECT nombre FROM usuarios WHERE id='$id'";
		$resultado = $Link->query($consulta) or die ("Unable to execute query.". mysql_error($Link));
		$dato = $resultado->fetch_assoc();
		$nombre = $dato["nombre"];
		
		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('$fechaHora', '$usuarioSession', '28', 'Eliminó al usuario <strong>$nombre</strong>')";
		$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
	} else {
		$result = array(
			"estado" => 0, 
			"mensaje" => "El usuario NO pudo ser eliminado."
		);
	}

	echo json_encode($result);