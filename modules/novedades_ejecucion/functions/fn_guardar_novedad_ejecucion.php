<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';
// var_dump($_POST);

$focalizados = [];
$sinFocalizar = [];
$novedades = [];
$novdadesSinfocalizar = [];

$periodoActual = (isset($_SESSION['periodoActual']) && $_SESSION['periodoActual'] != '') ? mysqli_real_escape_string($Link, $_SESSION["periodoActual"]) : "";

$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
$insitucion = (isset($_POST['insitucion']) && $_POST['insitucion'] != '') ? mysqli_real_escape_string($Link, $_POST["insitucion"]) : "";
$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST["semana"]) : "";
$tipoComplemento = (isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST["tipoComplemento"]) : "";
$observaciones = (isset($_POST['observaciones']) && $_POST['observaciones'] != '') ? mysqli_real_escape_string($Link, $_POST["observaciones"]) : "";

// FOCALIZADOS

//Array de los días de la semana
$dias = array();
$dias['D1'] = 0;
$dias['D2'] = 0;
$dias['D3'] = 0;
$dias['D4'] = 0;
$dias['D5'] = 0;

// Dias de esa semana
$consultaNovedad = "SELECT * FROM planilla_semanas WHERE semana = \"$semana\"";
$resultadoNovedades = $Link->query($consultaNovedad);
if($resultadoNovedades->num_rows > 0){
	while($row = $resultadoNovedades->fetch_assoc()) {
		if($row['NOMDIAS'] == 'lunes'){
			$dias['D1']	= 1;
		} else if($row['NOMDIAS'] == 'martes'){
			$dias['D2']	= 1;
		} else if($row['NOMDIAS'] == 'miércoles'){
			$dias['D3']	= 1;
		} else if($row['NOMDIAS'] == 'jueves'){
			$dias['D4']	= 1;
		} else if($row['NOMDIAS'] == 'viernes'){
			$dias['D5']	= 1;
		}
	}
}

// Datos del estudiante Focalizado
$consultaNovedad = "select f.tipo_doc, td.Abreviatura, f.num_doc, CONCAT(nom1,\" \",nom2,\" \",ape1,\" \",ape2) AS nombre, \"$tipoComplemento\" AS complemento, \"0\" as D1, \"0\" as D2, \"0\" as D3, \"0\" as D4, \"0\" as D5 FROM focalizacion$semana f LEFT JOIN tipodocumento td ON f.tipo_doc = td.id WHERE f.cod_sede = $sede AND f.Tipo_complemento = '$tipoComplemento' AND f.activo = 1";
$resultadoNovedades = $Link->query($consultaNovedad);
if($resultadoNovedades->num_rows > 0){
	while($titular = $resultadoNovedades->fetch_assoc()) {
		$titular['D1'] = $dias['D1'];
		$titular['D2'] = $dias['D2'];
		$titular['D3'] = $dias['D3'];
		$titular['D4'] = $dias['D4'];
		$titular['D5'] = $dias['D5'];
		$focalizados[] = $titular;
		// Buscando diferencias
		$documento = $titular['num_doc'];
		$bandera = 0;
		if($titular['D1'] == 1){
			if(!isset($_POST[$documento.'_D1'])){
				$bandera++;
				$titular['D1'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D1'])){
				$bandera++;
				$titular['D1'] = 1;
			}
		}

		if($titular['D2'] == 1){
			if(!isset($_POST[$documento.'_D2'])){
				$bandera++;
				$titular['D2'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D2'])){
				$bandera++;
				$titular['D2'] = 1;
			}
		}

		if($titular['D3'] == 1){
			if(!isset($_POST[$documento.'_D3'])){
				$bandera++;
				$titular['D3'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D3'])){
				$bandera++;
				$titular['D3'] = 1;
			}
		}

		if($titular['D4'] == 1){
			if(!isset($_POST[$documento.'_D4'])){
				$bandera++;
				$titular['D4'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D4'])){
				$bandera++;
				$titular['D4'] = 1;
			}
		}


		if($titular['D5'] == 1){
			if(!isset($_POST[$documento.'_D5'])){
				$bandera++;
				$titular['D5'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D5'])){
				$bandera++;
				$titular['D5'] = 1;
			}
		}
		if($bandera != 0){
			$novedades[] = $titular;
		}
	}
}



// NO FOCALIZADOS
// Datos del estudiante
$consultaNovedad = "select f.tipo_doc, td.Abreviatura, f.num_doc, CONCAT(nom1,\" \",nom2,\" \",ape1,\" \",ape2) AS nombre, \"$tipoComplemento\" AS complemento, \"0\" as D1, \"0\" as D2, \"0\" as D3, \"0\" as D4, \"0\" as D5 FROM focalizacion$semana f LEFT JOIN tipodocumento td ON f.tipo_doc = td.id WHERE f.cod_sede = $sede AND f.Tipo_complemento = '$tipoComplemento' AND f.activo = 0";
$resultadoNovedades = $Link->query($consultaNovedad);
if($resultadoNovedades->num_rows > 0){
	while($titular = $resultadoNovedades->fetch_assoc()) {
		$titular['D1'] = 0;
		$titular['D2'] = 0;
		$titular['D3'] = 0;
		$titular['D4'] = 0;
		$titular['D5'] = 0;
		$sinFocalizar[] = $titular;
		// Buscando diferencias
		$documento = $titular['num_doc'];
		$bandera = 0;
		if($titular['D1'] == 1){
			if(!isset($_POST[$documento.'_D1'])){
				$bandera++;
				$titular['D1'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D1'])){
				$bandera++;
				$titular['D1'] = 1;
			}
		}

		if($titular['D2'] == 1){
			if(!isset($_POST[$documento.'_D2'])){
				$bandera++;
				$titular['D2'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D2'])){
				$bandera++;
				$titular['D2'] = 1;
			}
		}

		if($titular['D3'] == 1){
			if(!isset($_POST[$documento.'_D3'])){
				$bandera++;
				$titular['D3'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D3'])){
				$bandera++;
				$titular['D3'] = 1;
			}
		}

		if($titular['D4'] == 1){
			if(!isset($_POST[$documento.'_D4'])){
				$bandera++;
				$titular['D4'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D4'])){
				$bandera++;
				$titular['D4'] = 1;
			}
		}


		if($titular['D5'] == 1){
			if(!isset($_POST[$documento.'_D5'])){
				$bandera++;
				$titular['D5'] = 0;
			}
		}else{
			if(isset($_POST[$documento.'_D5'])){
				$bandera++;
				$titular['D5'] = 1;
			}
		}
		if($bandera != 0){
			$novdadesSinfocalizar[] = $titular;
		}
	}
}

$usuario = $_SESSION['idUsuario'];
date_default_timezone_set('America/Bogota');
$fecha = date('Y-m-d H:i:s');

// var_dump($novedades);
// var_dump($novdadesSinfocalizar);

$consulta = " insert into novedades_focalizacion(id_usuario, fecha_hora, cod_sede, tipo_doc_titular, num_doc_titular, tipo_complem, semana, d1, d2, d3, d4, d5, observaciones ) values ";
$aux = 0;
foreach ($novedades as $novedad) {
	if($aux > 0){
		$consulta .= " , ";
	}
	$tipoDoc = $novedad['tipo_doc'];
	$numDoc = $novedad['num_doc'];
	$d1 = $novedad['D1'];
	$d2 = $novedad['D2'];
	$d3 = $novedad['D3'];
	$d4 = $novedad['D4'];
	$d5 = $novedad['D5'];
	$consulta .= " ($usuario, '$fecha', $sede, '$tipoDoc', '$numDoc' , '$tipoComplemento', '$semana', '$d1', '$d2', '$d3', '$d4', '$d5', '$observaciones') ";
	$aux++;
}
foreach ($novdadesSinfocalizar as $novedad) {
	if($aux > 0){
		$consulta .= " , ";
	}
	$tipoDoc = $novedad['tipo_doc'];
	$numDoc = $novedad['num_doc'];
	$d1 = $novedad['D1'];
	$d2 = $novedad['D2'];
	$d3 = $novedad['D3'];
	$d4 = $novedad['D4'];
	$d5 = $novedad['D5'];
	$consulta .= " ($usuario, '$fecha', $sede, '$tipoDoc', '$numDoc' , '$tipoComplemento', '$semana', '$d1', '$d2', '$d3', '$d4', '$d5', '$observaciones') ";
	$aux++;
}
$resultado = $Link->query($consulta);
//Ubicar los dias para entregas RES y con el numDoc encontramos al titular
$diasPlanilla = array();
$consulta = "SELECT * FROM planilla_semanas WHERE semana = \"$semana\"";
$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
	while($row = $resultado->fetch_assoc()) {
		if($row['NOMDIAS'] == 'lunes'){
			$diaPlanilla['indiceDias']	= 1;
		} else if($row['NOMDIAS'] == 'martes'){
			$diaPlanilla['indiceDias']	= 2;
		} else if($row['NOMDIAS'] == 'miércoles'){
			$diaPlanilla['indiceDias']	= 3;
		} else if($row['NOMDIAS'] == 'jueves'){
			$diaPlanilla['indiceDias']	= 4;
		} else if($row['NOMDIAS'] == 'viernes'){
			$diaPlanilla['indiceDias']	= 5;
		}
		$diaPlanilla['diaPlanilla'] = $row['DIA'];
		$diasPlanilla[] = $diaPlanilla;
	}
}


// Validando existencia en entregas_res
foreach ($novdadesSinfocalizar as $novedad){
	// var_dump($novedad);
	$aux = $novedad['num_doc'];
	$consulta = "SELECT * FROM entregas_res_$mes$periodoActual WHERE num_doc = \"$aux\"";
	$resultado = $Link->query($consulta);
	if(!$resultado->num_rows > 0){
		$consulta = " insert into entregas_res_$mes$periodoActual (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia
		, resguardo
		, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_mun_inst, cod_mun_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad
		, zona_res_est, activo, tipo_complem1, tipo_complem2, tipo_complem3, tipo_complem4, tipo_complem5, tipo_complem, D1, D2, D3, D4,D5, D6, D7, D8, D9, D10, D11, D12
		, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22
		)
		  SELECT tipo_doc,num_doc, (SELECT td.abreviatura FROM tipodocumento td WHERE td.id = f.tipo_doc) AS tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia
		  , resguardo
		  , cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_inst, cod_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad
		, zona_res_est, activo, '$tipoComplemento', '$tipoComplemento', '$tipoComplemento', '$tipoComplemento', '$tipoComplemento', '$tipoComplemento', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
		, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0

		  FROM focalizacion$semana f WHERE  f.num_doc = $aux ";
		$Link->query($consulta);
	}
}












//Aplicando Novedades en entregasRES
$consulta = "";
foreach ($novedades as $novedad){
	$consulta .= " update entregas_res_$mes$periodoActual set ";
	$indice = 0;
	foreach ($diasPlanilla as $diaPlanilla) {
		if($indice > 0){
			$consulta .= " , ";
		}
		$aux = $diaPlanilla['diaPlanilla'];
		$consulta .= " D$aux = ";
		$aux = $diaPlanilla['indiceDias'];
		$aux = $novedad['D'.$aux];
		$consulta .= " $aux ";
		$indice++;
	}
	$aux = $novedad['num_doc'];
	$consulta .= " where num_doc = $aux; ";
}
foreach ($novdadesSinfocalizar as $novedad){
	$consulta .= " update entregas_res_$mes$periodoActual set ";
	$indice = 0;
	foreach ($diasPlanilla as $diaPlanilla) {
		if($indice > 0){
			$consulta .= " , ";
		}
		$aux = $diaPlanilla['diaPlanilla'];
		$consulta .= " D$aux = ";
		$aux = $diaPlanilla['indiceDias'];
		$aux = $novedad['D'.$aux];
		$consulta .= " $aux ";
		$indice++;
	}
	$aux = $novedad['num_doc'];
	$consulta .= " where num_doc = $aux; ";
}
$Link->multi_query($consulta);

//SELECT * FROM entregas_res_1118 WHERE num_doc = 1096780362 OR num_doc = 1096780130



$respuestaAJAX = [
	"estado" => 1,
	"mensaje" => "Se ha realizado correctamente el registro."
];
echo json_encode($respuestaAJAX);
