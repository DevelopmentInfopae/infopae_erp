<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

// Declaración de variables pasadas mediante AJAX
$email = mysqli_real_escape_string($Link, $_POST["email"]);
$codigo = mysqli_real_escape_string($Link, $_POST["codigo"]);
$rector = mysqli_real_escape_string($Link, $_POST["rector"]);
$nombre = mysqli_real_escape_string($Link, $_POST["nombre"]);
$telefono = mysqli_real_escape_string($Link, $_POST["telefono"]);
$municipio = mysqli_real_escape_string($Link, $_POST["municipio"]);

// validacion para que no venga un nombre vacio
$nombreSinEspacios = trim($nombre);
$caracteres = strlen($nombreSinEspacios);
if ($caracteres == 0) {
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'No se puede crear con nombre en blanco.'
	];
	exit(json_encode($respuestaAJAX));
}

// Consultar si la institucion ya existe en la BD
$consulta1 = "SELECT codigo_inst FROM instituciones WHERE codigo_inst = '$codigo';";
$resultado1 = $Link->query($consulta1);
if($resultado1->num_rows > 0){
	$respuestaAJAX = [
    	"estado" => 0,
    	"mensaje" => "El código de institución N°: <strong>".$codigo."</strong> ya se encuentra registrado en el sistema. Por favor intente con un código diferente."
    ];
	} else {
		$consulta2="INSERT INTO instituciones (codigo_inst, nom_inst, cod_mun, tel_int, email_inst, cc_rector) VALUES ('$codigo', '$nombre', '$municipio', '$telefono', '$email', '$rector')";
		$resultado2=$Link->query($consulta2);
		if($resultado2){
			// Registro de la Bitácora
			$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date('Y-m-d H-i-s') ."', '" . $_SESSION["idUsuario"] . "', '36', 'Creó la institución: <strong>".$nombre."</strong>.')";
			$Link->query($consultaBitacora);

			$respuestaAJAX = [
	    	"estado" => 1,
	    	"mensaje" => "La institución ha sido guardada con éxito!"
	    ];
		}
	}

	mysqli_close($Link);
	echo json_encode($respuestaAJAX);