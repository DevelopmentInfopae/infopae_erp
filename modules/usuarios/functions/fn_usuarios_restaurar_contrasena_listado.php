<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	$id = $_POST["id"];
  $consulta = "SELECT nombre, num_doc FROM usuarios WHERE id = '$id'";
	$resultado = $Link->query($consulta) or die ("Unable to execute query.". mysql_error($Link));
	if($resultado){
		$datos = $resultado->fetch_assoc();
		if($datos["nombre"] != "" && $datos["num_doc"] != ""){
		  $clave = sha1(substr($datos["nombre"], 0, 1) . $datos["num_doc"]);

			$consulta1 = " UPDATE usuarios SET clave = '$clave' WHERE id = '$id'";
			$resultado1 = $Link->query($consulta1) or die ("Unable to execute query.". mysql_error($Link));
			if ($resultado1){
				$respuestaAJAX = [
					"estado" => 1,
					"mensaje" => "La contraseña ha sido restaurada con éxito."
				];

				$nombre = $datos["nombre"]; 
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '24', 'Restauró la contraseña del usuario <strong>$nombre</strong>')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			}
		} else {
			$respuestaAJAX = [
				"estado" => 0,
				"mensaje" => "La contraseña NO ha sido restaurada con éxito."
			];
		}
	} else {
		$respuestaAJAX = [
			"estado" => 0,
			"mensaje" => "No es posible restaurar la contraseña debido a que <strong>NO</strong> tiene <strong>Número de documento</strong> o <strong>Nombre de usuario</strong>. Por favor, registre dichos datos para poder continuar con el proceso."
		];
	}

	echo json_encode($respuestaAJAX);