<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	// Declaración de variables pasadas mediante AJAX
	$codigo = mysqli_real_escape_string($Link, $_POST["codigo"]);
	$estado = mysqli_real_escape_string($Link, $_POST["estado"]);

	$estado = ($estado == "true") ? 1 : 0;
	
	$consulta1="UPDATE instituciones SET estado='$estado' WHERE id = '$codigo'";
	$resultado1=$Link->query($consulta1);
	if($resultado1){

		$consulta2 = "SELECT * FROM instituciones WHERE id = '$codigo'";
		$resultado2 = $Link->query($consulta2);
		if ($resultado2){
			$registros2 = $resultado2->fetch_assoc();

			// Registro de la Bitácora
			$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '30', 'Actualizó el estado de la institución: ".$registros2["nom_inst"].".')";
			$Link->query($consultaBitacora);
		}

		$respuestaAJAX = [
    	"estado" => 1,
    	"mensaje" => "El estado de la institución ha sido guardada con éxito!"
    ];
	} else {
    $respuestaAJAX = [
    	"estado" => 0,
    	"mensaje" => "El estado de la institución NO pudo ser guardada."
    ];
	}

	echo json_encode($respuestaAJAX);