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

	$consultaValidaSemana = "SELECT num_doc FROM $tabla WHERE num_doc = $num_doc;";
	$respuestaValidaSemana = $Link->query($consultaValidaSemana) or die ('Error al consultar la focalizacion. ' . mysqli_error($Link));
	if ($respuestaValidaSemana->num_rows > 0) {
		$semanaExiste = substr($tabla, 12, 2);
		$respuestaAjax = [ 'estado' => "0", 'semana' => $semanaExiste];
		exit(json_encode($respuestaAjax));
	}

	$date1 = new DateTime($fecha_nac);
	$date2 = new DateTime(date('d-m-Y'));
	$diff = $date1->diff($date2);                   
	$edad = $diff->y;

	$insertar = "INSERT INTO ".$tabla." (id, tipo_doc, num_doc, nom1, nom2, ape1, ape2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, sede_con_faltantes, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, activo, tipo_complemento, nom_acudiente, doc_acudiente, tel_acudiente, parentesco_acudiente) VALUES ('', '".$tipo_doc."', '".$num_doc."', '".$nom1."', '".$nom2."', '".$ape1."', '".$ape2."', '".$genero."', '".$dir_res."', '".$cod_mun_res."', '".$telefono."', '".$cod_mun_nac."', '".$fecha_nac."', '".$cod_estrato."', '".$sisben."', '".$cod_discap."', '".$etnia."', '0', '".$cod_pob_victima."', '', '', '".$cod_inst."', '".$cod_sede."', '', '".$cod_grado."', '".$nom_grupo."', '".$cod_jorn_est."', 'MATRICULADO', '".$repitente."', '".$edad."', '".$zona_res_est."', '1', '".$tipo_complemento[$id]."', '".$nom_acudiente."', '".$doc_acudiente."', '".$tel_acudiente."', '".$parantesco_acudiente."')";
	// exit(var_dump($_POST));

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

		$consultaDias = "SELECT DISTINCT(DIA) AS dia FROM planilla_semanas WHERE MES = $mes;";
		$respuestaDias = $Link->query($consultaDias) or die ('Error al consultar los dias del mes ' . mysqli_error($Link));
		if ($respuestaDias->num_rows > 0) {
			while ($dataDias = $respuestaDias->fetch_assoc()) {
				$dias[] = $dataDias['dia'];
			}
		}
		// var_dump($dias);
		$complemento = [];
		$ultimoComplemento = '';
		foreach ($semanas as $key => $semanaB) {
			foreach ($semana as $id => $tabla) {
				$semanaPost = substr($tabla, 12);
				if ($semanaB == $semanaPost) {
					$complemento[$semanaB] = $tipo_complemento[$id]; 
				}
			}
		}

		foreach ($semanas as $key => $semanaB) {
			if (isset($complemento[$semanaB])) {
				$complementos .= "'".$complemento[$semanaB]."'".",";
				$ultimoComplemento = $complemento[$semanaB];
			}else{
				$complementos .= "' '" .",";
			}
		}

		if (count($semanas) == 1) {
			$complementos .= "'" .$ultimoComplemento. "'" ."," . "'" .$ultimoComplemento. "'" ."," ."'" .$ultimoComplemento. "'" . "," . "'" .$ultimoComplemento. "'" ."," . "'" .$ultimoComplemento. "'";
		}else if (count($semanas) == 2) {
			$complementos .= "'" .$ultimoComplemento. "'" ."," . "'" .$ultimoComplemento. "'" ."," ."'" .$ultimoComplemento. "'" . "," . "'" .$ultimoComplemento. "'";
		}else if (count($semanas) == 3) {
			$complementos .= "'" .$ultimoComplemento. "'" ."," . "'" .$ultimoComplemento. "'" ."," ."'" .$ultimoComplemento. "'" ;
		}else if (count($semanas) == 4) {
			$complementos .= "'" .$ultimoComplemento. "'" ."," . "'" .$ultimoComplemento. "'";
		}else if (count($semanas) == 5) {
			$complementos .= "'" .$ultimoComplemento. "'" .",";		
		}

		$D = '';
		foreach ($dias as $key => $dia) {
			$D .= 1 .",";
		}

		$numeroDiasActivos = count($dias);
		$numeroDiasDesactivos = 31 - $numeroDiasActivos;

		for ($i = 0; $i < $numeroDiasDesactivos; $i++) { 
			$D .= 0 . ",";
		}

		$D = substr($D, 0, -1);
		$complementos = substr($complementos, 1, -1);

		$insertarEntregas = "INSERT INTO " .'entregas_res_'.$mes.$periodoActual. "(
			id,
			tipo_doc,
			num_doc,
			tipo_doc_nom,
			ape1,
			ape2,
			nom1,
			nom2,
			genero,
			dir_res,
			cod_mun_res,
			telefono,
			cod_mun_nac,
			fecha_nac,
			cod_estrato,
			sisben,
			cod_discap,
			etnia,
			resguardo,
			cod_pob_victima,
			des_dept_nom,
			nom_mun_desp,
			cod_sede,
			cod_inst,
			cod_mun_inst,
			cod_mun_sede,
			nom_sede,
			nom_inst,
			cod_grado,
			nom_grupo,
			cod_jorn_est,
			estado_est,
			repitente,
			edad,
			zona_res_est,
			id_disp_est,
			TipoValidacion,
			activo,
			tipo,
			nom_acudiente,
			doc_acudiente,
			tel_acudiente,
			parentesco_acudiente,
			tipo_complem1,
			tipo_complem2,
			tipo_complem3,
			tipo_complem4,
			tipo_complem5,
			tipo_complem,
			D1,D2,D3,D4,D5,D6,D7,D8,D9,D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20,D21,D22,D23,D24,D25,D26,D27,D28,D29,D30,D31) VALUES (
			'',
			'".$tipo_doc."',
			'".$num_doc."',
			'".$tipoDocNom."',
			'".$ape1."',
			'".$ape2."',
			'".$nom1."',
			'".$nom2."',
			'".$genero."',
			'".$dir_res."',
			'".$cod_mun_res."',
			'".$telefono."',
			'".$cod_mun_nac."',
			'".$fecha_nac."',
			'".$cod_estrato."',
			'".$sisben."',
			'".$cod_discap."',
			'".$etnia."',
			'99',
			'".$cod_pob_victima."',
			'',
			'',
			'".$cod_sede."',
			'".$cod_inst."',
			'".$cod_mun_res."',
			'".$cod_mun_res."',
			'".$nomSede."',
			'".$nomInst."',
			'".$cod_grado."',
			'".$nom_grupo."',
			'".$cod_jorn_est."',
			'MATRICULADO',
			'".$repitente."',
			'".$edad."',
			'".$zona_res_est."',
			'0',
			'',
			'1',
			'F',
			'".$nom_acudiente."',
			'".$doc_acudiente."',
			'".$tel_acudiente."',
			'".$parantesco_acudiente."',
			'".$complementos."',
			".$D."
			) 	
		";
		$Link->query($insertarEntregas) or die ('Error al insertar las entregas. ' . mysqli_error($Link));

		$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '48', 'Ingresó el títular de derecho con número de identificación <strong>".$num_doc."</strong>')";
		$Link->query($sqlBitacora);
		$respuestaAjax = [ 'estado' => "1", 'semana' => "0"];
		exit(json_encode($respuestaAjax));
	} else {
		echo $valida."-".sizeof($semana);
	}
 ?>