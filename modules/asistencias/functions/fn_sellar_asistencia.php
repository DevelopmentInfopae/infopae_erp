<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

$bandera = 0;

// var_dump($_POST);
//var_dump($_SESSION);

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







// Representación numérica del día de la semana
// 0 (para domingo) hasta 6 (para sábado)
$fechaConsulta = "$annoCompleto-$mes-$dia";
$fechaConsulta = strtotime($fechaConsulta);
$diaSemana = date("w", $fechaConsulta);






$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);

$semana = mysqli_real_escape_string($Link, $_POST['semana']);
$sede = mysqli_real_escape_string($Link, $_POST['sede']);

// Buscando el menor día de la semana para buscar el consecutivo del día actual dentro de la semana.
$consulta = " select * from planilla_semanas where semana = \"$semana\" limit 1 ";
$result = $Link->query($consulta) or die ('Actualización de asistencia'.$consulta. mysqli_error($Link));
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
else if($primerDia == 'miercoles'){
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


// TODO
//Buscando el consecutivo del día actual dentro del mes.

$consulta = " select D1, D2, D3, D4, D5, D6, D7, D8, D9, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31 from planilla_dias where mes = \"$mes\" and ano = \"$annoCompleto\" "; 

//echo $consulta;
$result = $Link->query($consulta) or die ('Leyendo planilla dias.'.$consulta. mysqli_error($Link));
$row = $result->fetch_assoc();
$diaIndice = array_search($dia, $row);
//var_dump($diaIndice);
//echo array_search($dia, $row);
//var_dump($row);










// Insertando Novedades de Focalización
// Se va a cargar la asistencia del día actual para hacer la sinserción


// Hay que buscar el tipo de complementeo
$consulta = "";
$consultaConsumo = "";

$consulta = " select a.*, f.Tipo_complemento as complemento from asistencia_det$mes$anno a left join focalizacion$semana f on f.tipo_doc = a.tipo_doc and f.num_doc = a.num_doc where a.mes = \"$mes\" and a.semana = \"$semana\" and a.dia = \"$dia\" and f.cod_sede = \"$sede\" ";

//echo "<br>$consulta<br>";


$result = $Link->query($consulta) or die ('Actualización de asistencia'.$consulta. mysqli_error($Link));


$aux = 0;
$repitentes = 0;
$entregas = 0;





// Todos los registros de ese día en 0
$consultaConsumo = " update entregas_res_$mes$anno set $diaIndice = \"0\" where cod_Sede = \"$sede\"";
//echo "<br><br>$consultaConsumo<br><br>";
$resultConsumo = $Link->query($consultaConsumo) or die ('Error al actualizar Entregas Res<br>'.$consultaConsumo. mysqli_error($Link));

while($row = $result->fetch_assoc()){
	//$aux++;
	//echo "<br>$aux<br>";
	// var_dump($row);	
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


	$consulta2 = " select * from novedades_focalizacion where cod_sede = \"$sede\" and tipo_doc_titular = \"$tipoDoc\" and num_doc_titular = \"$numDoc\" and tipo_complem = \"$complemento\" and semana = \"$semana\" ";
	//echo "<br>$consulta2<br>";
	$resultado2 = $Link->query($consulta2) or die ('No se pudieron cargar los grupos. '.$consulta2. mysqli_error($Link));
	if($resultado2->num_rows >= 1){
		// Actualizar la novedad de focalización
		$consulta3 = " update novedades_focalizacion set d$indice = \"$consumio\" where cod_sede = \"$sede\" and tipo_doc_titular = \"$tipoDoc\" and num_doc_titular = \"$numDoc\" and tipo_complem = \"$complemento\" and semana = \"$semana\" ";
	}
	else{
		$consulta3 = " insert into novedades_focalizacion (id_usuario, fecha_hora, cod_sede, tipo_doc_titular, num_doc_titular, tipo_complem, semana, d1, d2, d3, d4, d5, estado, tiponovedad ) values ";
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
	}
	//echo "<br><br>$consulta3<br><br>";
	$result3 = $Link->query($consulta3) or die ('Novedades de focalización'.$consulta3. mysqli_error($Link));












	













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

	//echo "<br><br>$diaIndice<br><br>";



	//Tiene que actualizar solo a los tipo F asi como los que repiten son tipo R
	
	if($consumio == 1){
		$entregas++;
		$consultaConsumo = " update entregas_res_$mes$anno set $diaIndice = \"$consumio\" where tipo = \"F\" and tipo_doc = \"$tipoDoc\" and num_doc = \"$numDoc\" and cod_Sede = \"$sede\" ";
		//echo "<br><br>$consultaConsumo<br><br>";
		$resultConsumo = $Link->query($consultaConsumo) or die ('Error al actualizar Entregas Res<br>'.$consultaConsumo. mysqli_error($Link));
		//echo "<br>".$Link->affected_rows;	

		$consultaEntregasRepitio = "";
		$consultaRepite = "";
	
		if($repitio == 1){
			//$entregas++;
			$repitentes++;
			$consultaEntregasRepitio = " select * from entregas_res_$mes$anno where tipo_doc = \"$tipoDoc\" and num_doc = \"$numDoc\" and tipo = \"R\" and cod_Sede = \"$sede\"  "; 
	
	
			//echo "$consultaEntregasRepitio<br>";
	
			$resultadoEntregasRepitio = $Link->query($consultaEntregasRepitio) or die ('No se pudieron cargar los grupos. '.$consultaEntregasRepitio. mysqli_error($Link));
			
			
			
			
			if($resultadoEntregasRepitio->num_rows >= 1){
				$consultaRepite = " update entregas_res_$mes$anno set $diaIndice = 1 where tipo_doc = \"$tipoDoc\" and num_doc = \"$numDoc\" and tipo = \"R\" and cod_Sede = \"$sede\" ";
	
				//echo "$consultaRepite<br>";
	
	
				$resultRepite = $Link->query($consultaRepite) or die ('Actualizando repite en entregas'.$consultaRepite. mysqli_error($Link));
	
			}else{
				$consultaRepite = " insert into entregas_res_$mes$anno (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo, tipo, tipo_complem1, tipo_complem2, tipo_complem3, tipo_complem4, tipo_complem5, tipo_complem, $diaIndice) select tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo,  \"R\" as tipo, tipo_complem1, tipo_complem2, tipo_complem3, tipo_complem4, tipo_complem5, tipo_complem, \"1\" as $diaIndice from  entregas_res_$mes$anno WHERE tipo_doc = \"$tipoDoc\" and num_doc = \"$numDoc\" and cod_Sede = \"$sede\" ";
	
				//echo "$consultaRepite<br>";
	
				$resultRepite = $Link->query($consultaRepite) or die ('Insertando registro en entregas de titular que repite'.$consultaRepite. mysqli_error($Link));
			}
		}

	}
	
	
	
	
	
	
	
	
	
	
}
//var_dump($entregas);
//var_dump($repitentes);





// Cierre de la asistencia
$consulta = "select * from asistencia_enc$mes$anno where mes = \"$mes\" and semana = \"$semana\" and dia = \"$dia\" and cod_sede = \"$sede\" ";
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los grupos. '.$consulta. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$consulta = "update asistencia_enc$mes$anno set estado = 2 where mes = \"$mes\" and semana = \"$semana\" and dia = \"$dia\" and cod_sede = \"$sede\" ";
	//echo "<br><br>$consulta<br><br>";
	$result = $Link->query($consulta) or die ('Cierre de asistencia '.$consulta. mysqli_error($Link));
}else{
	$consulta = "insert into  asistencia_enc$mes$anno (mes, semana, dia, estado, cod_sede) values ( \"$mes\",\"$semana\",\"$dia\",2,\"$sede\"  ) ";
	$result = $Link->query($consulta) or die ('Cierre de asistencia '.$consulta. mysqli_error($Link));	
}




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