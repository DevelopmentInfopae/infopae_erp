<?php 

  require_once '../../../config.php';
  require_once '../../../db/conexion.php';
// exit(var_dump($_POST));
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

if (isset($_POST['parantesco_acudiente'])) {
	$parantesco_acudiente = $_POST['parantesco_acudiente'];
} else {
	$parantesco_acudiente = "";
}

$periodoActual = $_SESSION['periodoActual'];

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
		$insertar = "UPDATE ".$tabla." SET tipo_doc = '".$tipo_doc."', nom1 = '".$nom1."', nom2 = '".$nom2."', ape1 = '".$ape1."', ape2 = '".$ape2."', genero = '".$genero."', dir_res = '".$dir_res."', cod_mun_res = '".$cod_mun_res."', telefono = '".$telefono."', cod_mun_nac = '".$cod_mun_nac."', fecha_nac = '".$fecha_nac."', cod_estrato = '".$cod_estrato."', sisben = '".$sisben."', cod_discap = '".$cod_discap."', etnia = '".$etnia."', cod_pob_victima = '".$cod_pob_victima."', cod_inst = '".$cod_inst."', cod_sede = '".$cod_sede."', cod_grado = '".$cod_grado."', nom_grupo = '".$nom_grupo."', cod_jorn_est = '".$cod_jorn_est."', repitente = '".$repitente."', edad = '".$edad."', zona_res_est = '".$zona_res_est."', tipo_complemento = '".$tipo_complemento[$id]."', nom_acudiente = '".$nom_acudiente."', doc_acudiente = '".$doc_acudiente."', tel_acudiente = '".$tel_acudiente."', parentesco_acudiente = '".$parantesco_acudiente."' WHERE id = ".$id_comp_semana[$id];


	} else {
		$insertar = "INSERT INTO ".$tabla." (id, tipo_doc, num_doc, nom1, nom2, ape1, ape2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, sede_con_faltantes, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, activo, tipo_complemento, nom_acudiente, doc_acudiente, tel_acudiente, parentesco_acudiente) VALUES ('', '".$tipo_doc."', '".$num_doc."', '".$nom1."', '".$nom2."', '".$ape1."', '".$ape2."', '".$genero."', '".$dir_res."', '".$cod_mun_res."', '".$telefono."', '".$cod_mun_nac."', '".$fecha_nac."', '".$cod_estrato."', '".$sisben."', '".$cod_discap."', '".$etnia."', '0', '".$cod_pob_victima."', '', '', '".$cod_inst."', '".$cod_sede."', '', '".$cod_grado."', '".$nom_grupo."', '".$cod_jorn_est."', '', '".$repitente."', '".$edad."', '".$zona_res_est."', '0', '".$tipo_complemento[$id]."', '".$nom_acudiente."', '".$doc_acudiente."', '".$tel_acudiente."', '".$parantesco_acudiente."')";
	}

	if ($Link->query($insertar)===true) {
		$valida++;
	}
}

	if ($valida == sizeof($semana)) {

		// ingresar titular de derecho a entregas del mes correspondiente
		$mes = '';
		$tipoDocNom = '';
		$nomSede = '';
		$nomInst = '';
		$semanas = [];
		$dias = [];	
		$complementos = '';
		$ultimaSemana = substr($tabla, 12);
		$consultaMes = "SELECT DISTINCT(mes) AS mes FROM planilla_semanas WHERE semana = $ultimaSemana LIMIT 1 ";
		$respuestaMes = $Link->query($consultaMes) or die ('Error al consultar el mes de entrega. ' . mysqli_error($Link));
		if ($respuestaMes->num_rows > 0) {
			$dataMes = $respuestaMes->fetch_assoc();
			$mes = $dataMes['mes'];
		}

		$consultaTipoDocNom = "SELECT Abreviatura FROM tipodocumento WHERE id = $tipo_doc; ";
		$respuestaTipoDocNom = $Link->query($consultaTipoDocNom) or die ('Error al consultar el tipo de documento ' . mysqli_error($Link));
		if ($respuestaTipoDocNom->num_rows > 0) {
			$dataTipoDocNom = $respuestaTipoDocNom->fetch_assoc();
			$tipoDocNom = $dataTipoDocNom['Abreviatura'];
		}

		$consultaNomSede = "SELECT nom_sede, nom_inst FROM sedes$periodoActual WHERE cod_sede = $cod_sede;";
		$respuestaNomSede = $Link->query($consultaNomSede) or die ('Error el nombre de la sede educativa ' . mysqli_error($Link));
		if ($respuestaNomSede->num_rows > 0) {
			$dataNomSede = $respuestaNomSede->fetch_assoc();
			$nomSede = $dataNomSede['nom_sede'];
			$nomInst = $dataNomSede['nom_inst'];
		}

		$consultaSemanaComplem = "SELECT DISTINCT(SEMANA) FROM planilla_semanas WHERE MES = $mes;";
		$respuestaSemanaComplem = $Link->query($consultaSemanaComplem) or die ('Error al consultar las semanas del mes. ' .mysqli_error($Link));
		if ($respuestaSemanaComplem->num_rows > 0) {
			while ($dataSemanaComplem = $respuestaSemanaComplem->fetch_assoc()) {
				$semanas[$dataSemanaComplem['SEMANA']] = $dataSemanaComplem['SEMANA'];
			}
		}

		$complementos = '';
		$contadorComplemento = 1;
		foreach ($tipo_complemento as $key => $complemento) {
			if (isset($tipo_complemento[$key]) && !empty($tipo_complemento[$key])) {
				$complementos .= "tipo_complem".$key . " = '" .$complemento. "', "; 
			}else{
				$complementos .= "tipo_complem".$key . " = " . "''" . ", ";
			}
		}
		$complementos .= "tipo_complem = '" .$complemento."'";

		$updateEntregas = "UPDATE " .'entregas_res_'.$mes.$periodoActual. " SET 
			tipo_doc = '".$tipo_doc."',
			num_doc = '" .$num_doc. "',
			tipo_doc_nom = '" .$tipoDocNom. "',
			ape1 = '" .$ape1. "',
			ape2 = '" .$ape2. "',
			nom1 = '" .$nom1. "',
			nom2 = '" .$nom2. "',
			genero = '" .$genero. "',
			dir_res = '" .$dir_res. "',
			cod_mun_res = '" .$cod_mun_res. "',
			telefono = '" .$telefono. "',
			cod_mun_nac = '" .$cod_mun_res. "',
			fecha_nac = '" .$fecha_nac. "',
			cod_estrato = '" .$cod_estrato. "',
			sisben = '" .$sisben. "',
			cod_discap = '" .$cod_discap. "',
			etnia = '" .$etnia. "',
			cod_pob_victima = '" .$cod_pob_victima. "',
			cod_sede = '" .$cod_sede. "',
			cod_inst = '" .$cod_inst. "',
			cod_mun_inst = '" .$cod_mun_res. "',
			cod_mun_sede = '" .$cod_mun_res. "',
			nom_sede = '" .$nomSede. "',
			nom_inst = '" .$nomInst. "',
			cod_grado = '" .$cod_grado. "',
			nom_grupo = '" .$nom_grupo. "',
			cod_jorn_est = '" .$cod_jorn_est. "',
			repitente = '" .$repitente. "',
			edad = '" .$edad. "',
			zona_res_est = '" .$zona_res_est. "',
			nom_acudiente = '" .$nom_acudiente. "',
			doc_acudiente = '" .$doc_acudiente. "',
			tel_acudiente = '" .$tel_acudiente. "',
			parentesco_acudiente = '" .$parantesco_acudiente. "',
			" . $complementos . " WHERE id = ".$id_comp_semana[$id];

		$Link->query($updateEntregas) or die ('Error al actualizar las entregas ' . mysqli_error($Link));

		$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '49', 'Editó datos del títular de derecho con número de identificación <strong>".$num_doc."</strong>')";
		$Link->query($sqlBitacora);
		echo "1";
	} else {
		echo $valida."-".sizeof($semana);
	}
 ?>