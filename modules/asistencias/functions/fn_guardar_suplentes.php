<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

$anno = $annoAsistencia2D;
$periodoActual = $_SESSION['periodoActual'];
$mes = ((isset($_POST['mes']) && $_POST['mes'] != "") ? $_POST['mes'] : "");
$dia = ((isset($_POST['dia']) && $_POST['dia'] != "") ? $_POST['dia'] : "");
$semana = ((isset($_POST['semana']) && $_POST['semana'] != " ") ? $_POST['semana'] : "");
$sede = ((isset($_POST['sede']) && $_POST['sede'] != " ") ? $_POST['sede'] : "");
$complemento = ((isset($_POST['complemento']) && $_POST['complemento'] != " ") ? $_POST['complemento'] : "");
$suplentes = ((isset($_POST['suplente']) && $_POST['suplente'] != " ") ? $_POST['suplente'] : "");
$contadorInsertEntregas = 0;
$contadorInserNovedades = 0;
$resultadoInsertEntregas = 0;
$resultadoInserNovedades = 0;

$consultaD = " SELECT D1,D2,D3,D4,D5,D6,D7,D8,D9,D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20 FROM planilla_dias WHERE mes = $mes ";
$respuestaD = $Link->query($consultaD) or die ('Error al consultar el D en entregas ' . mysqli_error($Link));
if ($respuestaD->num_rows > 0) {
	$dataD = $respuestaD->fetch_assoc();
	$D = array_search($dia, $dataD);
}

$tipoComplem = "";
$consultaOrdenSemana = " SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE MES = '$mes' ";
$respuestaOrdenSemana = $Link->query($consultaOrdenSemana) or die ('Error al consultar las semanas ' . mysqli_error($Link));
if ($respuestaOrdenSemana->num_rows > 0 ) {
	while ($dataOrdenSemana = $respuestaOrdenSemana->fetch_assoc()) {
		$ordenSemana[] = $dataOrdenSemana;
	}
	foreach ($ordenSemana as $key => $value) {
		if ($value['semana'] == $semana) {
			$aux = $key+1;
			$tipoComplem = "tipo_complem$aux";
		}
	}
}

$consultaOrdenDia = " SELECT NOMDIAS FROM planilla_semanas WHERE MES = $mes AND SEMANA = $semana AND DIA = $dia ";
$respuestaOrdenDia = $Link->query($consultaOrdenDia) or die ('Error al consultar el nombre del día ' . mysqli_error($Link));
if ($respuestaOrdenDia->num_rows > 0) {
	$dataOrdenDia = $respuestaOrdenDia->fetch_assoc();
	$nomDia = $dataOrdenDia['NOMDIAS'];
	switch ($nomDia) {
    	case ($nomDia == 'Lunes' || $nomDia == 'lunes'):
        	$d = "d1";
        	break;
    	case ($nomDia == 'Martes' || $nomDia == 'martes'):
        	$d = "d2";
        	break;
    	case ($nomDia == 'Miércoles' || $nomDia == 'miércoles' || $nomDia == 'Miercoles' || $nomDia == 'miercoles')	:
        	$d = "d3";
        	break;
    	case ($nomDia == 'Jueves' || $nomDia == 'jueves'):
        	$d = "d4";
        	break;
    	case ($nomDia == 'Viernes' || $nomDia == 'viernes'):
    		$d = "d5";
    		break;        
	}
}
// echo "$nomDia";
$InsertEntregas = " INSERT INTO entregas_res_$mes$periodoActual  (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo, tipo, nom_acudiente, doc_acudiente, tel_acudiente, parentesco_acudiente, $tipoComplem, tipo_complem, $D)	 VALUES ";

$inserNovedades = " INSERT INTO novedades_focalizacion ( id_usuario, fecha_hora, cod_sede, tipo_doc_titular, num_doc_titular, tipo_complem, semana, d1, d2, d3, d4, d5, estado, tiponovedad ) VALUES "; 

foreach ($suplentes as $key => $value) {
	// MANEJO ENTREGAS
	$consultaEntregas = " SELECT num_doc FROM entregas_res_$mes$periodoActual WHERE cod_sede = '$sede' AND num_doc = '" .$value['documento']. "' AND Tipo = 'S'";
	$respuestaEntregas = $Link->query($consultaEntregas) or die ('Error al consultar las entregas ' . mysqli_error($Link));
	if ($respuestaEntregas->num_rows > 0) {
		$valorD = ($value['consume'] == 1) ? 1 : 0;
		$updateEntregas = " UPDATE entregas_res_$mes$periodoActual SET $D = '$valorD', $tipoComplem = '$complemento' WHERE cod_sede = '$sede' AND  num_doc = '" .$value['documento']. "' AND Tipo = 'S'";
		$Link->query($updateEntregas);	
	}else{
		if ($value['consume'] == 1) {
			$consultaSuplente = " SELECT tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo, nom_acudiente, doc_acudiente, tel_acudiente, parentesco_acudiente FROM suplentes$semana WHERE num_doc = '" .$value['documento']. "'";
			$respuestaSuplente = $Link->query($consultaSuplente) or die ('Error al consultar el suplente ' . mysqli_error($Link));
			if ($respuestaSuplente->num_rows > 0) {
				$dataSuplente = $respuestaSuplente->fetch_assoc();
				$suplente = $dataSuplente;
			}
			$InsertEntregas .= "( '" .$suplente['tipo_doc']. "',  
								'" .$suplente['num_doc']. "', 
								'" .$suplente['tipo_doc_nom']. "', 
								'" .$suplente['ape1']. "', 
								'" .$suplente['ape2']. "', 
								'" .$suplente['nom1']. "', 
								'" .$suplente['nom2']. "', 
								'" .$suplente['genero']. "', 
								'" .$suplente['dir_res']. "', 
								'" .$suplente['cod_mun_res']. "', 
								'" .$suplente['telefono']. "', 
								'" .$suplente['cod_mun_nac']. "', 
								'" .$suplente['fecha_nac']. "', 
								'" .$suplente['cod_estrato']. "', 
								'" .$suplente['sisben']. "', 
								'" .$suplente['cod_discap']. "', 
								'" .$suplente['etnia']. "', 
								'" .$suplente['resguardo']. "', 
								'" .$suplente['cod_pob_victima']. "',
								'0',
								'0', 
								'" .$suplente['cod_sede']. "', 
								'" .$suplente['cod_inst']. "', 
								'" .$suplente['cod_mun_inst']. "', 
								'" .$suplente['cod_mun_sede']. "', 
								'" .$suplente['nom_sede']. "', 
								'" .$suplente['nom_inst']. "', 
								'" .$suplente['cod_grado']. "', 
								'" .$suplente['nom_grupo']. "', 
								'" .$suplente['cod_jorn_est']. "', 
								'" .$suplente['estado_est']. "', 
								'" .$suplente['repitente']. "', 
								'" .$suplente['edad']. "', 
								'" .$suplente['zona_res_est']. "', 
								'" .$suplente['id_disp_est']. "', 
								'" .$suplente['TipoValidacion']. "', 
								'1', 
								'S', 
								'" .$suplente['nom_acudiente']. "', 
								'" .$suplente['doc_acudiente']. "', 
								'" .$suplente['tel_acudiente']. "', 
								'" .$suplente['parentesco_acudiente']. "', 
								'$complemento', 
								'$complemento', 
								'1' ), ";
			$contadorInsertEntregas++;									
		}
	}
	// MANEJO NOVEDADES FOCALIZACION 
	$consultaNovedades = " SELECT num_doc_titular FROM novedades_focalizacion WHERE cod_sede = $sede AND num_doc_titular = '" .$value['documento']. "' AND tipo_complem	= '$complemento' AND semana = '$semana' AND tiponovedad = '5' "; 
	$respuestaNovedades = $Link->query($consultaNovedades) or die ('Error al consultar las novedades' . mysqli_error($Link));
	if ($respuestaNovedades->num_rows > 0) {
		$valorD = ($value['consume'] == 1) ? 1 : 0;
		$updateNovedades = " UPDATE novedades_focalizacion SET $d = $valorD WHERE cod_sede = $sede AND num_doc_titular = '" .$value['documento']. "' AND tipo_complem	= '$complemento' AND semana = '$semana' AND tiponovedad = '5' ";
		$Link->query($updateNovedades);
	}else{
		if ($value['consume'] == 1) {
			$usuario = $_SESSION['id_usuario'];
			$fechaHora = date('Y-m-d H:i:s');
			$consultaSuplente = " SELECT tipo_doc, num_doc, cod_sede FROM suplentes$semana WHERE num_doc = '" .$value['documento']. "'";
			$respuestaSuplente = $Link->query($consultaSuplente) or die ('Error al consultar el suplente ' . mysqli_error($Link));
			if ($respuestaSuplente->num_rows > 0) {
				$dataSuplente = $respuestaSuplente->fetch_assoc();
				$suplente = $dataSuplente;
			}
			$inserNovedades .= "('" .$usuario. "', 
								'" .$fechaHora. "', 
								'" .$suplente['cod_sede']. "', 
								'" .$suplente['tipo_doc']. "', 
								'" .$suplente['num_doc']. "', 
								'" .$complemento. "', 
								'" .$semana. "', 
								'" .(($d == 'd1') ? 1 : 0) . "',
								'" .(($d == 'd2') ? 1 : 0) . "', 
								'" .(($d == 'd3') ? 1 : 0) . "', 
								'" .(($d == 'd4') ? 1 : 0) . "', 
								'" .(($d == 'd5') ? 1 : 0) . "', 
								'1',
								'5' ), ";
			$contadorInserNovedades++;	
		}
	}
}
$InsertEntregas = trim($InsertEntregas, ", ");
$inserNovedades = trim($inserNovedades, ", ");
// exit(var_dump($inserNovedades));
if ($contadorInsertEntregas > 0 ) {
	$respuestaInserEntregas = $Link->query($InsertEntregas);	
	if ($respuestaInserEntregas === true) {
		$resultadoInsertEntregas++;
	}
}

if ($contadorInserNovedades > 0 ) {
	$respuestaInsertNovedades = $Link->query($inserNovedades);
	if ($respuestaInsertNovedades === true) {
		$resultadoInserNovedades++;
	}
}
// exit($contadorInserNovedades == 0 && $contadorInsertEntregas == 0);
if($resultadoInserNovedades == 0 && $resultadoInsertEntregas == 0) {
	$respuestaAjax = [
		"estado" => "1",
		"mensaje" => "Suplentes Guardados Exitosamente "
 	];
 	exit(json_encode($respuestaAjax));
}

else if ($resultadoInserNovedades == 1 && $resultadoInsertEntregas >= 0) {
	$respuestaAjax = [
		"estado" => "1",
		"mensaje" => "Suplentes Guardados Exitosamente "
	];
	exit(json_encode($respuestaAjax));
}	
else {
	$respuestaAjax = [
		"estado" => "0",
		"mensaje" => "Error al guardar "
 	];
 	exit(json_encode($respuestaAjax));
}
	
// echo json_encode($respuestaAjax);



