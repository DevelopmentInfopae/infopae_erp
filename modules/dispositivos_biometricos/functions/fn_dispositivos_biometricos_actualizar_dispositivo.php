<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';

// exit(var_dump($_POST));
if (isset($_POST['cod_sede'])) {
	$cod_sede = $_POST['cod_sede'];
} else {
	$cod_sede = "";
}

if (isset($_POST['referencia'])) {
	$referencia = $_POST['referencia'];
} else {
	$referencia = "";
}

if (isset($_POST['num_serial'])) {
	$num_serial = $_POST['num_serial'];
} else {
	$num_serial = "";
}

if (isset($_POST['id_usuario'])) {
	$id_usuario = $_POST['id_usuario'];
} else {
	$id_usuario = "";
}

if (isset($_POST['tipo'])) {
	$tipo = $_POST['tipo'];
} else {
	$tipo = "";
}

if (isset($_POST['iddispositivo'])) {
	$iddispositivo = $_POST['iddispositivo'];
} else {
	$iddispositivo = "";
}

if (isset($_POST['nom_sede'])) {
	$nom_sede = $_POST['nom_sede'];
} else {
	$nom_sede = "";
}

$serialSinEspacios = trim($num_serial);
$caracteres = strlen($serialSinEspacios);
if ($caracteres == 0) {
	echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "No se puede crear serial en blanco o con caracteres."}]}';
	exit();
}

$serialSinEspacios = trim($num_serial);
if ($serialSinEspacios < 0) {
	echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "No se puede crear serial negativo."}]}';
	exit();
}


$sqlDispositivo = "UPDATE dispositivos SET referencia = '".$referencia."', num_serial = '".$num_serial."', cod_sede = '".$cod_sede."', id_usuario = '".$id_usuario."', tipo = '".$tipo."' WHERE id = ".$iddispositivo;

if ($Link->query($sqlDispositivo)===true) {

	if (isset($_POST['num_doc'])) {
		$num_doc = $_POST['num_doc'];

		if (isset($_POST['tipo_doc'])) {
			$tipo_doc = $_POST['tipo_doc'];
		} else {
			$tipo_doc = "";
		}

		if (isset($_POST['id_bioest'])) {
			$id_bioest = $_POST['id_bioest'];
		} else {
			$id_bioest = "";
		}

		if (isset($_POST['idBiometria'])) {
			$idBiometria = $_POST['idBiometria'];
		} else {
			$idBiometria = "";
		}
		// exit(var_dump($_POST));
		// validacion que no haya id repetidos en la db
		foreach ($id_bioest as $key => $value) {
			$consultaValidacionDB = "SELECT * FROM biometria WHERE id_bioest = '" .$value. "' AND cod_sede = '".$cod_sede."' AND id_dispositivo = ".$iddispositivo.";";
			$respuestaDB = $Link->query($consultaValidacionDB) or die ('Error al consultar biometrias' . mysqli_error($Link));
			if ($respuestaDB->num_rows > 0) {
				$respuesta = '{"respuesta" : [{"exitoso" : "2", "respuesta" : "El id '.$value.' ya se encuentra registrado"}]}';
				exit($respuesta);
			}
			
		}

		$sqlIngresarBiometria = "INSERT INTO biometria (id, tipo_doc, num_doc, id_dispositivo, id_bioest, cod_sede) VALUES ";
		foreach ($num_doc as $llave => $documento) {
			if ($id_bioest[$documento] != "") {
					$sqlIngresarBiometria.=" ('', '".$tipo_doc[$documento]."', '".$documento."', '".$iddispositivo."', '".$id_bioest[$documento]."', '".$cod_sede."'), ";
				}
		}

		$sqlIngresarBiometria = trim($sqlIngresarBiometria, ", ");
			if ($Link->query($sqlIngresarBiometria)===true) {
				echo '{"respuesta" : [{"exitoso" : "1", "respuesta" : "Editado con éxito."}]}';//Si se editaron correctamente las biometrías existentes y se ingresaron correctamente las nuevas.
				$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '44', 'Actualizó la información del dispositivo biométrico con número de serial <strong>".$num_serial."</strong>')";
				$Link->query($sqlBitacora);
			} else {
					echo '{"respuesta" : [{"exitoso" : "1", "respuesta" : "Editado con éxito."}]}';
			}

	}
}

