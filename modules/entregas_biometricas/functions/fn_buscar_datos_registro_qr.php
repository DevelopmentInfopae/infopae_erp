<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];

//var_dump($_POST);

$documento = '';
$dispositivo = '';
$fila = '';
$idUsrDispositivo = '';
$fechaActual = '';
$bandera = 0;
$semana = '';

//var_dump($_POST);

if(isset($_POST['lector']) && $_POST['lector'] != ''){
	$documento = mysqli_real_escape_string($Link, $_POST['lector']);
}

if(isset($_POST['dispositivo']) && $_POST['dispositivo'] != ''){
	$dispositivo = mysqli_real_escape_string($Link, $_POST['dispositivo']);
}

/* Busqueda de la semana actual */
$dia = date('d');
$mes = date('m');
$anno = date('Y');
$date = date_create();


$consulta = "SELECT semana FROM planilla_semanas WHERE mes = $mes AND dia = $dia LIMIT 1";
$resultado = $Link->query($consulta) or die ('No se pudo hacer busqueda de la semana. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$semana = $row["semana"];
}

//var_dump($documento);
//var_dump($dispositivo);

// $documento = 1102349197;
// $dispositivo = 5;


$consulta = " SELECT f.nom1, f.nom2, f.ape1, f.ape2, f.cod_grado, f.nom_grupo, b.id_bioest FROM biometria b LEFT JOIN focalizacion$semana f ON b.tipo_doc = f.tipo_doc AND b.num_doc = f.num_doc WHERE b.id_dispositivo = $dispositivo AND b.num_doc = $documento LIMIT 1 ";
//echo "<br>$consulta<br>";

$resultado = $Link->query($consulta) or die ('Error al buscar datos del titular.'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$idUsrDispositivo = $row["id_bioest"];
	$nombre = $row["nom1"].' '.$row["nom2"];
	$apellido = $row["ape1"].' '.$row["ape2"];
	$grado = $row["cod_grado"];
	$grupo = $row["nom_grupo"];
	$fechaActual = date_format($date, 'd/m/Y H:i:s a');
	$fila = " <tr> <td>$fechaActual</td> <td>$nombre</td> <td>$apellido</td> <td>$grado</td> <td>$grupo</td> </tr> ";
}else{
	// No se encontrarón los datos
	$bandera = 1;
}

	// var_dump($semana);
	// var_dump($idUsrDispositivo);

/* Inserción en biometria_reg */
if($bandera == 0){
	$fechaActual = date_format($date, 'Y-m-d H:i:s');
	$consulta = " insert into biometria_reg ( dispositivo_id, usr_dispositivo_id, fecha ) values ( $dispositivo, $idUsrDispositivo, \"$fechaActual\" ) ";
	//echo "<br><br>$consulta<br><br>";
	$resultado = $Link->query($consulta) or die ('Error insertar en biometria reg.'. mysqli_error($Link));
	if(!$resultado){
		// No se pudo hacer la inserción
		$bandera = 2;	
	}
}



if($bandera == 0){
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"fila" => $fila
	);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha cargado con exito."
	);
}
echo json_encode($resultadoAJAX);