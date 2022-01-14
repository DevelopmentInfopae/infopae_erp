<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// Declaración de variables pasadas mediante AJAX
$id = mysqli_real_escape_string($Link, $_POST["id"]);
$email = mysqli_real_escape_string($Link, $_POST["email"]);
$rector = mysqli_real_escape_string($Link, $_POST["rector"]);
$nombre = mysqli_real_escape_string($Link, $_POST["nombre"]);
$estado = mysqli_real_escape_string($Link, $_POST["estado"]);
$telefono = mysqli_real_escape_string($Link, $_POST["telefono"]);
$municipio = mysqli_real_escape_string($Link, $_POST["municipio"]);

// validacion para que no venga un nombre vacio
$nombreSinEspacios = trim($nombre);
$caracteres = strlen($nombreSinEspacios);
if ($caracteres == 0) {
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'No se puede actualizar con nombre en blanco.'
	];
	exit(json_encode($respuestaAJAX));
}

$consulta1="UPDATE instituciones SET nom_inst='$nombre', cod_mun='$municipio', tel_int='$telefono', email_inst='$email', cc_rector='$rector', estado='$estado' WHERE id = '$id'";
	$resultado1=$Link->query($consulta1);
	if($resultado1){
		// Registro de la Bitácora
		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '38', 'Actualizó la institución con nombre: <strong>".$nombre."</strong>.')";
		$Link->query($consultaBitacora);

		$respuestaAJAX = [
    	"estado" => 1,
    	"mensaje" => "La institución ha sido actualizada con éxito!"
    ];
	} else {
    $respuestaAJAX = [
    	"estado" => 0,
    	"mensaje" => "La institución NO pudo ser actualizada."
    ];
	}

	echo json_encode($respuestaAJAX);