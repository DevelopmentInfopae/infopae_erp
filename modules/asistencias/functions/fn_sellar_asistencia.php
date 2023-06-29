<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

// !IMPORTANTE: Se supone que los que usan el modulo de asistencia usan el tipo de validación tablet o ninguno.
$tipoValidacion = 'Planilla';
$bandera = 0;

$anno = $annoAsistencia2D;
$annoCompleto = $annoasistencia; 

if(isset($_POST['mes']) && $_POST['mes'] != ""){
	$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}else{
	$mes = $mesAsistencia;
}
if(isset($_POST['dia']) && $_POST['dia'] != ""){
	$dia = mysqli_real_escape_string($Link, $_POST['dia']);
}else{
	$dia = $diaAsistencia;
}

$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;
include 'fn_validar_existencias_tablas.php';

$fechaConsulta = "$annoCompleto-$mes-$dia";
$fechaConsulta = strtotime($fechaConsulta);
$diaSemana = date("w", $fechaConsulta);

$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);
$semana = mysqli_real_escape_string($Link, $_POST['semana']);
$sede = mysqli_real_escape_string($Link, $_POST['sede']);
$complemento = mysqli_real_escape_string($Link, $_POST['complemento']);

// Buscando el menor día de la semana para buscar el consecutivo del día actual dentro de la semana.
$consulta = " SELECT * FROM planilla_semanas WHERE semana = \"$semana\" LIMIT 1 "; // aca traemos varios dias
$result = $Link->query($consulta) or die ('Error consultado las fechas Ln39'.$consulta. mysqli_error($Link));
$row = $result->fetch_assoc();

$primerDia = $row['NOMDIAS'];
if($primerDia == 'domingo'){
	$primerDia = 0;
}
else if($primerDia == 'lunes'){
	$primerDia = 1;
}
else if($primerDia == 'martes'){
	$primerDia = 2;
}
else if($primerDia == 'miércoles'){
	$primerDia = 3;
}
else if($primerDia == 'jueves'){
	$primerDia = 4;
}
else if($primerDia == 'viernes'){
	$primerDia = 5;
}
else if($primerDia == 'sabado'){
	$primerDia = 6;
}
$indice = $primerDia;
$consecutivo = 1;

if($indice != $diaSemana){
	while($indice != $diaSemana){
		$indice++;
		if($indice > 6){
			$indice = 0;
		}
		if($indice != 0 && $indice != 6){
			$consecutivo++;
			if($consecutivo > 5){
				$consecutivo = 1;
			}
		}
	}
}

// La variable $consecutivo corresponde al consecutivo de día en una semana de 5 días
// Buscando el consecutivo del día actual dentro del mes.
$consulta = " SELECT 	D1,  D2,  D3,  D4,  D5, 
						D6,  D7,  D8,  D9,  D10, 
						D11, D12, D13, D14, D15, 
						D16, D17, D18, D19, D20, 
						D21, D22, D23, D24, D25,
						D26, D27, D28, D29, D30, D31 
					FROM planilla_dias WHERE mes = \"$mes\" AND ano = \"$annoCompleto\" "; 
$result = $Link->query($consulta) or die ('Leyendo planilla dias Ln91.'.$consulta. mysqli_error($Link));
$row = $result->fetch_assoc();
$diaIndice = array_search($dia, $row);

// INSERTANDO NOVEDADES DE FOCALIZACIÓN
// Se va a cargar la asistencia del día actual para hacer la sinserción
// Hay que buscar el tipo de complemento
$consulta = "";
$consultaConsumo = "";
$consulta = " SELECT a.* 
				FROM asistencia_det$mes$anno a 
				LEFT JOIN focalizacion$semana f ON f.tipo_doc = a.tipo_doc AND f.num_doc = a.num_doc 
				WHERE a.mes = \"$mes\" AND a.semana = \"$semana\" AND a.dia = \"$dia\" AND f.cod_sede = \"$sede\" AND a.complemento = \"$complemento\" ";
$result = $Link->query($consulta) or die ('Consultado los datos almacenados Ln104'.$consulta. mysqli_error($Link));
$aux = 0;
$repitentes = 0;
$entregas = 0;

// Todos los registros de ese día en 0
$consultaConsumo = " UPDATE entregas_res_$mes$anno ent
						INNER JOIN focalizacion$semana foc ON (ent.cod_sede = foc.cod_sede AND ent.tipo_complem = foc.tipo_complemento AND ent.num_doc = foc.num_doc) 
						set $diaIndice = \"1\" WHERE ent.cod_Sede = \"$sede\" AND ent.tipo = \"F\" AND ent.tipo_complem = \"$complemento\" ";
$resultConsumo = $Link->query($consultaConsumo) or die ('Error al actualizar Entregas Ln113'. mysqli_error($Link));

$consulta3 = " INSERT INTO novedades_focalizacion (
								id_usuario, 
								fecha_hora, 
								cod_sede, 
								tipo_doc_titular, 
								num_doc_titular, 
								tipo_complem, 
								semana, 
								d1, d2, d3, d4, d5, 
								estado, 
								tiponovedad ) 
							values ";
$consultaConsumo = "";
$consultaRepite1 = "";
$consultaRepite0 = "";

$consultaRepite = " INSERT INTO entregas_res_$mes$anno (
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
									tipo_complem1, 
									tipo_complem2, 
									tipo_complem3, 
									tipo_complem4, 
									tipo_complem5, 
									tipo_complem, 
									$diaIndice) ";
$aux = 0;
$repitentes = 0;

while($row = $result->fetch_assoc()){
	$tipoDoc = $row['tipo_doc'];
	$numDoc = $row['num_doc'];
	$complemento = $row['complemento'];
	$consumio = $row['consumio'];
	$repitio = $row['repitio'];

	/*
	NOVEDADES DE FOCALIZACIÓN
	documento, dia de la semana
	25 = D1, hay que mirar en el mes actual cual fue el primer día de la semana 
	para saber el consecutivo dentro de la semana del día actual
	Si repite se registra 2 veces
	Planilla semana tiene como se inició el mes
	tipo de novedad = 1
	novedad estado 1
	*/

	if($aux > 0){
		$consulta3 .= " , ";
	}
	
	$consulta3 .= " ( ";
	$consulta3 .= " \"$id_usuario\" ";
	$consulta3 .= " , \"$fecha\" ";
	$consulta3 .= " , \"$sede\" ";
	$consulta3 .= " , \"$tipoDoc\" ";
	$consulta3 .= " , \"$numDoc\" ";
	$consulta3 .= " , \"$complemento\" ";
	$consulta3 .= " , \"$semana\" ";
	if($consecutivo == 1){$consulta3 .= " , \"$consumio\" "; }else{$consulta3 .= " , \"0\" "; }
	if($consecutivo == 2){$consulta3 .= " , \"$consumio\" "; }else{$consulta3 .= " , \"0\" "; }
	if($consecutivo == 3){$consulta3 .= " , \"$consumio\" "; }else{$consulta3 .= " , \"0\" "; }
	if($consecutivo == 4){$consulta3 .= " , \"$consumio\" "; }else{$consulta3 .= " , \"0\" "; }
	if($consecutivo == 5){$consulta3 .= " , \"$consumio\" "; }else{$consulta3 .= " , \"0\" "; }
	$consulta3 .= " , \"1\" ";
	$consulta3 .= " , \"1\" ";
	$consulta3 .= " ) ";
	
	if($repitio == 1){
		$consulta3 .= " , ";
		$consulta3 .= " ( ";
		$consulta3 .= " \"$id_usuario\" ";
		$consulta3 .= " , \"$fecha\" ";
		$consulta3 .= " , \"$sede\" ";
		$consulta3 .= " , \"$tipoDoc\" ";
		$consulta3 .= " , \"$numDoc\" ";
		$consulta3 .= " , \"$complemento\" ";
		$consulta3 .= " , \"$semana\" ";
		if($consecutivo == 1){$consulta3 .= " , \"1\" "; }else{$consulta3 .= " , \"0\" "; }
		if($consecutivo == 2){$consulta3 .= " , \"1\" "; }else{$consulta3 .= " , \"0\" "; }
		if($consecutivo == 3){$consulta3 .= " , \"1\" "; }else{$consulta3 .= " , \"0\" "; }
		if($consecutivo == 4){$consulta3 .= " , \"1\" "; }else{$consulta3 .= " , \"0\" "; }
		if($consecutivo == 5){$consulta3 .= " , \"1\" "; }else{$consulta3 .= " , \"0\" "; }
		$consulta3 .= " , \"1\" ";
		$consulta3 .= " , \"1\" ";
		$consulta3 .= " ) ";
	}
	/* TERMINA LA CONSTRUCCIÓN DE LA CONSULTA PARA ACTUALIZAR NOVEDADES DE FOCALIZACIÓN */

	/*
	ENTREGAS RES
	Quitar los desmarcados
	inserta repitente columna tipo
	F = -focalización
	R = Repitente
		Todo en cero
	toca verificar (documento,sede, complemento)

	Buscar en focalización -> Complemento niño
	*/
	//Tiene que actualizar solo a los tipo F asi como los que repiten son tipo R
	if($consumio == 0){
		$consultaConsumo .= " UPDATE entregas_res_$mes$anno set $diaIndice = \"$consumio\" where tipo_doc = \"$tipoDoc\" and num_doc = \"$numDoc\" and cod_Sede = \"$sede\" and tipo = \"F\" and tipo_complem = \"$complemento\" and TipoValidacion = \"$tipoValidacion\" ; ";
	}
	if ($repitio == 0) {
		$consultaRepitioEntregas0 = " SELECT num_doc FROM entregas_res_$mes$anno WHERE cod_sede = \"$sede\" AND num_doc = '$numDoc' AND Tipo = \"R\" AND TipoValidacion = \"$tipoValidacion\" ";
		$respuestaRepitioEntregas0 = $Link->query($consultaRepitioEntregas0) or die ('Error al consultar los repitentes ' . mysqli_error($Link));
		if ($respuestaRepitioEntregas0->num_rows > 0) {
			$consultaRepite0 .= " UPDATE entregas_res_$mes$anno SET $diaIndice = \"$repitio\" WHERE cod_sede = \"$sede\" AND num_doc = \"$numDoc\" AND Tipo = \"R\" AND TipoValidacion = \"$tipoValidacion\" ; ";
		}
	}
	if($repitio == 1){
		// validamos si ese repitente ya existe en la tabla entregas, si existe se actualiza el D que esta repitiendo si no se crea
		$consultaRepitioEntregas = " SELECT num_doc FROM entregas_res_$mes$anno WHERE  cod_sede = \"$sede\" AND num_doc = '$numDoc' AND Tipo = \"R\" AND TipoValidacion = \"$tipoValidacion\" ";
		$respuestaRepitioEntregas = $Link->query($consultaRepitioEntregas) or die ('Error al consultar los repitentes ' . mysqli_error($Link));
		if ($respuestaRepitioEntregas->num_rows > 0) {
			$consultaRepite1 .= " UPDATE entregas_res_$mes$anno SET $diaIndice = \"$repitio\" WHERE cod_sede = \"$sede\" AND num_doc = \"$numDoc\" AND Tipo = \"R\" AND TipoValidacion = \"$tipoValidacion\" ; ";
		}else{
			if($repitentes > 0){
				$consultaRepite .= " UNION ALL ";
			}
			$repitentes++;
			$consultaRepite .= " select tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo,  \"R\" as tipo, tipo_complem1, tipo_complem2, tipo_complem3, tipo_complem4, tipo_complem5, tipo_complem, \"1\" as $diaIndice from  entregas_res_$mes$anno WHERE tipo_doc = \"$tipoDoc\" and num_doc = \"$numDoc\" and cod_Sede = \"$sede\" ";
		}
	}
	$aux++;
}


// Ejecutar actualización en novedades de focalización.
$consulta3 .= " ON DUPLICATE KEY UPDATE d$indice = values(d$indice); ";
$consultaRepite .= " ON DUPLICATE KEY UPDATE d$indice = values(d$indice); ";
if($repitentes == 0){ $consultaRepite = ""; }
// echo "<br><br>$consulta3<br><br>";
// echo "<br><br>$consultaConsumo<br><br>";
// echo "<br><br>$consultaRepite<br><br>";
// exit(var_dump($consultaRepite1));
// CIERRE DE LA ASISTENCIA
$consultaCierre = " INSERT INTO asistencia_enc$mes$anno ( dia, semana, mes, cod_sede, complemento, estado ) values ( \"$dia\", \"$semana\", \"$mes\", \"$sede\", \"$complemento\", 2 ) ";
$consultaCierre .= " ON DUPLICATE KEY UPDATE estado = values(estado); ";

$consultaGeneral = $consulta3." ".$consultaConsumo." ".$consultaRepite." ".$consultaCierre. " " .$consultaRepite1. " " .$consultaRepite0;  

$result = $Link->multi_query($consultaGeneral) or die ('Insert error'. mysqli_error($Link));
if($bandera == 0){
	$resultadoAJAX = array(
		"state" => 1,
		"message" => "El registro se ha realizado con éxito.",
  	);
}else{
	$resultadoAJAX = array(
		"state" => 2,
		"message" => "Error al hacer el registro.",
  	);
}
echo json_encode($resultadoAJAX);