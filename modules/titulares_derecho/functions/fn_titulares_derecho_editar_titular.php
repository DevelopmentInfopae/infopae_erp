<?php 

  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

if (isset($_POST['tipo_doc'])) {
	$tipo_doc = $_POST['tipo_doc'];
} else {
	$tipo_doc = "";
}

if (isset($_POST['num_doc'])) {
	$num_doc = $_POST['num_doc'];
} else {
	$num_doc = "";
}

if (isset($_POST['nom1'])) {
	$nom1 = $_POST['nom1'];
} else {
	$nom1 = "";
}

if (isset($_POST['nom2'])) {
	$nom2 = $_POST['nom2'];
} else {
	$nom2 = "";
}

if (isset($_POST['ape1'])) {
	$ape1 = $_POST['ape1'];
} else {
	$ape1 = "";
}

if (isset($_POST['ape2'])) {
	$ape2 = $_POST['ape2'];
} else {
	$ape2 = "";
}

if (isset($_POST['genero'])) {
	$genero = $_POST['genero'];
} else {
	$genero = "";
}

if (isset($_POST['telefono'])) {
	$telefono = $_POST['telefono'];
} else {
	$telefono = "";
}

if (isset($_POST['fecha_nac'])) {
	$fecha_nac = $_POST['fecha_nac'];
} else {
	$fecha_nac = "";
}

if (isset($_POST['cod_mun_nac'])) {
	$cod_mun_nac = $_POST['cod_mun_nac'];
} else {
	$cod_mun_nac = "";
}

if (isset($_POST['dir_res'])) {
	$dir_res = $_POST['dir_res'];
} else {
	$dir_res = "";
}

if (isset($_POST['cod_mun_res'])) {
	$cod_mun_res = $_POST['cod_mun_res'];
} else {
	$cod_mun_res = "";
}

if (isset($_POST['cod_estrato'])) {
	$cod_estrato = $_POST['cod_estrato'];
} else {
	$cod_estrato = "";
}

if (isset($_POST['sisben'])) {
	$sisben = $_POST['sisben'];
} else {
	$sisben = "";
}

if (isset($_POST['cod_discap'])) {
	$cod_discap = $_POST['cod_discap'];
} else {
	$cod_discap = "";
}

if (isset($_POST['etnia'])) {
	$etnia = $_POST['etnia'];
} else {
	$etnia = "";
}

if (isset($_POST['cod_pob_victima'])) {
	$cod_pob_victima = $_POST['cod_pob_victima'];
} else {
	$cod_pob_victima = "";
}

if (isset($_POST['cod_inst'])) {
	$cod_inst = $_POST['cod_inst'];
} else {
	$cod_inst = "";
}

if (isset($_POST['cod_sede'])) {
	$cod_sede = $_POST['cod_sede'];
} else {
	$cod_sede = "";
}

if (isset($_POST['cod_grado'])) {
	$cod_grado = $_POST['cod_grado'];
} else {
	$cod_grado = "";
}

if (isset($_POST['nom_grupo'])) {
	$nom_grupo = $_POST['nom_grupo'];
} else {
	$nom_grupo = "";
}

if (isset($_POST['cod_jorn_est'])) {
	$cod_jorn_est = $_POST['cod_jorn_est'];
} else {
	$cod_jorn_est = "";
}

if (isset($_POST['repitente'])) {
	$repitente = $_POST['repitente'];
} else {
	$repitente = "";
}

if (isset($_POST['semana'])) {
	$semana = $_POST['semana'];
} else {
	$semana = "";
}

if (isset($_POST['tipo_complemento'])) {
	$tipo_complemento = $_POST['tipo_complemento'];
} else {
	$tipo_complemento = "";
}

if (isset($_POST['id_comp_semana'])) {
	$id_comp_semana = $_POST['id_comp_semana'];
} else {
	$id_comp_semana = "";
}


if (isset($_POST['nom_acudiente'])) {
	$nom_acudiente = $_POST['nom_acudiente'];
} else {
	$nom_acudiente = "";
}

if (isset($_POST['doc_acudiente'])) {
	$doc_acudiente = $_POST['doc_acudiente'];
} else {
	$doc_acudiente = "";
}

if (isset($_POST['tel_acudiente'])) {
	$tel_acudiente = $_POST['tel_acudiente'];
} else {
	$tel_acudiente = "";
}

$consultaZonaSede = "SELECT sector FROM sedes".$_SESSION['periodoActual']." WHERE cod_sede = ".$cod_sede;
$resultadoZonaSede = $Link->query($consultaZonaSede);
if ($resultadoZonaSede->num_rows > 0) {
	if ($zonasede = $resultadoZonaSede->fetch_assoc()) {
		$zona_res_est = $zonasede['sector'];
	}
} else {
	$zona_res_est = 0;
}

$valida = 0;


foreach ($semana as $id => $tabla) {


$date1 = new DateTime($fecha_nac);
$date2 = new DateTime(date('d-m-Y'));
$diff = $date1->diff($date2);                   
$edad = $diff->y;

	if (isset($id_comp_semana[$id])) {
		$insertar = "UPDATE ".$tabla." SET tipo_doc = '".$tipo_doc."', nom1 = '".$nom1."', nom2 = '".$nom2."', ape1 = '".$ape1."', ape2 = '".$ape2."', genero = '".$genero."', dir_res = '".$dir_res."', cod_mun_res = '".$cod_mun_res."', telefono = '".$telefono."', cod_mun_nac = '".$cod_mun_nac."', fecha_nac = '".$fecha_nac."', cod_estrato = '".$cod_estrato."', sisben = '".$sisben."', cod_discap = '".$cod_discap."', etnia = '".$etnia."', cod_pob_victima = '".$cod_pob_victima."', cod_inst = '".$cod_inst."', cod_sede = '".$cod_sede."', cod_grado = '".$cod_grado."', nom_grupo = '".$nom_grupo."', cod_jorn_est = '".$cod_jorn_est."', repitente = '".$repitente."', edad = '".$edad."', zona_res_est = '".$zona_res_est."', tipo_complemento = '".$tipo_complemento[$id]."', nom_acudiente = '".$nom_acudiente."', doc_acudiente = '".$doc_acudiente."', tel_acudiente = '".$tel_acudiente."' WHERE id = ".$id_comp_semana[$id];
	} else {
		$insertar = "INSERT INTO ".$tabla." (id, tipo_doc, num_doc, nom1, nom2, ape1, ape2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, sede_con_faltantes, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, activo, tipo_complemento, nom_acudiente, doc_acudiente, tel_acudiente) VALUES ('', '".$tipo_doc."', '".$num_doc."', '".$nom1."', '".$nom2."', '".$ape1."', '".$ape2."', '".$genero."', '".$dir_res."', '".$cod_mun_res."', '".$telefono."', '".$cod_mun_nac."', '".$fecha_nac."', '".$cod_estrato."', '".$sisben."', '".$cod_discap."', '".$etnia."', '0', '".$cod_pob_victima."', '', '', '".$cod_inst."', '".$cod_sede."', '', '".$cod_grado."', '".$nom_grupo."', '".$cod_jorn_est."', '', '".$repitente."', '".$edad."', '".$zona_res_est."', '0', '".$tipo_complemento[$id]."', '".$nom_acudiente."', '".$doc_acudiente."', '".$tel_acudiente."')";
	}

	if ($Link->query($insertar)===true) {
		$valida++;
	}
}

	if ($valida == sizeof($semana)) {
		$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '49', 'Editó datos del títular de derecho con número de identificación <strong>".$num_doc."</strong>')";
		$Link->query($sqlBitacora);
		echo "1";
	} else {
		echo $valida."-".sizeof($semana);
	}
 ?>