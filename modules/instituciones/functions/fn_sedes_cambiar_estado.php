<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	// Declaración de variables pasadas mediante AJAX
	$periodoActual = $_SESSION["periodoActual"];
	$codigo = mysqli_real_escape_string($Link, $_POST["codigo"]);
	$estado = mysqli_real_escape_string($Link, $_POST["estado"]);
	// exit(var_dump($_POST));
	$estado = ($estado == 1) ? 0 : 1;
	
	$consulta1="UPDATE sedes$periodoActual SET estado='$estado' WHERE id = '$codigo'";
	$resultado1=$Link->query($consulta1);
	if($resultado1){

		$consulta2 = "SELECT * FROM sedes$periodoActual WHERE id = '$codigo'";
		$resultado2 = $Link->query($consulta2);
		if ($resultado2){
			$registros2 = $resultado2->fetch_assoc();

			// Registro de la Bitácora
			$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '30', 'Actualizó el estado de la Sede: ".$registros2["nom_sede"].".')";
			$Link->query($consultaBitacora);
		}

		$respuestaAJAX = [
    	"estado" => 1,
    	"mensaje" => "El estado de la sede ha sido guardada con éxito!"
    ];
	} else {
    $respuestaAJAX = [
    	"estado" => 0,
    	"mensaje" => "El estado de la sede NO pudo ser guardada."
    ];
	}

	echo json_encode($respuestaAJAX);