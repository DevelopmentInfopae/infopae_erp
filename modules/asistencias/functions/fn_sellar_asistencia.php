<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// var_dump($_POST);
//var_dump($_SESSION);

$fecha = date("Y-m-d H:i:s");
$anno = date("y"); 
$mes = date("m");
$dia = intval(date("d"));
// Representación numérica del día de la semana
// 0 (para domingo) hasta 6 (para sábado)
$diaSemana = date("w");

$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);

$semana = mysqli_real_escape_string($Link, $_POST['semana']);
$sede = mysqli_real_escape_string($Link, $_POST['sede']);

// Buscando el menor día de la semana para buscar el consecutivo del día actual dentro de la semana.
$consulta = " select * from planilla_semanas where semana = $semana limit 1 ";
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

// Insertando Novedades de Focalización
// Se va a cargar la asistencia del día actual para hacer la sinserción


// Hay que buscar el tipo de complementeo

$consulta = "select a.*, f.Tipo_complemento as complemento from Asistencia$mes$anno a left join focalizacion$semana f on f.tipo_doc = a.tipo_doc and f.num_doc = a.num_doc where a.mes = $mes and a.semana = $semana and a.dia = $dia ";

// echo "<br>$consulta<br>";

$result = $Link->query($consulta) or die ('Actualización de asistencia'.$consulta. mysqli_error($Link));
$aux = 0;

$consulta = " insert into novedades_focalizacion (id_usuario, fecha_hora, cod_sede, tipo_doc_titular, num_doc_titular, tipo_complem, semana, d1, d2, d3, d4, d5, estado, tiponovedad ) values ";

while($row = $result->fetch_assoc()){
	// var_dump($row);
	$tipoDoc = $row['tipo_doc'];
	$numDoc = $row['num_doc'];
	$complemento = $row['complemento'];


	if($aux > 0){
		$consulta .= " , ";
	}
	$consulta .= " ( ";
	$consulta .= " \"$id_usuario\" ";
	$consulta .= " , \"$fecha\" ";
	$consulta .= " , \"$sede\" ";
	$consulta .= " , \"$tipoDoc\" ";
	$consulta .= " , \"$numDoc\" ";
	$consulta .= " , \"$complemento\" ";
	$consulta .= " , \"$semana\" ";
	
	if($consecutivo == 1){$consulta .= " , \"1\" "; }else{$consulta .= " , \"0\" "; }
	if($consecutivo == 2){$consulta .= " , \"1\" "; }else{$consulta .= " , \"0\" "; }
	if($consecutivo == 3){$consulta .= " , \"1\" "; }else{$consulta .= " , \"0\" "; }
	if($consecutivo == 4){$consulta .= " , \"1\" "; }else{$consulta .= " , \"0\" "; }
	if($consecutivo == 5){$consulta .= " , \"1\" "; }else{$consulta .= " , \"0\" "; }

	$consulta .= " , \"1\" ";
	$consulta .= " , \"1\" ";

	$consulta .= " ) ";
	$aux++;





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
}

echo "<br>$consulta<br>";






// $consulta = " update Asistencia$mes$anno set asistencia = $valor, repite = 0, consumio = 0, repitio = 0 where Asistencia$mes$anno.mes = \"$mes\" and Asistencia$mes$anno.semana = \"$semana\" and Asistencia$mes$anno.dia = \"$dia\" and Asistencia$mes$anno.num_doc  = \"$documento\" and Asistencia$mes$anno.tipo_doc  = \"$tipoDocumento\"";



// $result = $Link->query($consulta) or die ('Actualización de asistencia'.$consulta. mysqli_error($Link));



// if($result && $Link->affected_rows <= 0 ){
// 	$consulta = " insert into Asistencia$mes$anno ( tipo_doc, num_doc, fecha, mes, semana, dia, asistencia, id_usuario ) values (\"$tipoDocumento\", \"$documento\", \"$fecha\", \"$mes\", \"$semana\", \"$dia\", \"$valor\", \"$id_usuario\" ) ";
// 	$result = $Link->query($consulta) or die ('Inserción de asistencia'.$consulta. mysqli_error($Link));
// }
// if($result){
// 	$resultadoAJAX = array(
// 		"state" => 1,
// 		"message" => "El registro se ha actualizado con éxito.",
//   	);
// }else{
// 	$resultadoAJAX = array(
// 		"state" => 2,
// 		"message" => "Error al hacer la actualización del registro.",
//   	);
// }


// echo json_encode($resultadoAJAX);