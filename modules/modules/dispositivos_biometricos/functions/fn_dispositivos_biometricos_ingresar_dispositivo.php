<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';


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

if (isset($_POST['nom_sede'])) {
	$nom_sede = $_POST['nom_sede'];
} else {
	$nom_sede = "";
}

$sqlDispositivo = "INSERT INTO dispositivos (referencia, num_serial, cod_sede, id_usuario, tipo) VALUES ('".$referencia."', '".$num_serial."', '".$cod_sede."', '".$id_usuario."', '".$tipo."')";

if ($Link->query($sqlDispositivo)===true) {
	$idDispositivo = $Link->insert_id;

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

		$cntValida = 0;
		$sqlBiometria = "INSERT INTO biometria (tipo_doc, num_doc, id_dispositivo, id_bioest, cod_sede) VALUES ";
		for ($i=0; $i < sizeof($num_doc) ; $i++) { 
			if ($id_bioest[$num_doc[$i]] != "") {
				$cntValida++;
				$sqlBiometria .= "('".$tipo_doc[$num_doc[$i]]."', '".$num_doc[$i]."', '".$idDispositivo."', '".$id_bioest[$num_doc[$i]]."', '".$cod_sede."'), ";
			}
		}

		$sqlBiometria = trim($sqlBiometria, ', ');

		if ($cntValida > 0) {
			if ($Link->query($sqlBiometria)===true) {
				echo '{"respuesta" : [{"exitoso" : "1", "respuesta" : "Creado con éxito."}]}'; //Registró biometrías con éxito
				$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '43', 'Registró un dispositivito biométrico para la sede <strong>".$nom_sede."</strong> con número de serial <strong>".$num_serial."</strong>')";
				$Link->query($sqlBitacora);
			} else {
				echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error al crear datos de Biometría."}]}';
			}
		} else {
			echo '{"respuesta" : [{"exitoso" : "1", "respuesta" : "Creado con éxito."}]}'; //Si cargó la tabla de estudiantes y de los estudiantes no les registró ninguna biometría.
			$sqlBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '43', 'Registró un dispositivito biométrico para la sede <strong>".$nom_sede."</strong> con número de serial <strong>".$num_serial."</strong>')";
				$Link->query($sqlBitacora);
		}
	} else {
		echo '{"respuesta" : [{"exitoso" : "1", "respuesta" : "Creado con éxito."}]}'; //Si no cargó la tabla de estudiantes
		$sqlBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '43', 'Registró un dispositivito biométrico para la sede <strong>".$nom_sede."</strong> con número de serial <strong>".$num_serial."</strong>')";
				$Link->query($sqlBitacora);
	}
} else {
	echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error al crear el dispositivo."}]}';
}

 ?>