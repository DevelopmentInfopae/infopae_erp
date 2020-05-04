<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
$nombre = '';
$cargo = '';
$foto = '';



//var_dump($_SESSION);

// Declaración de variables.
$documento = "";
$tipo = 1;
$documento = (isset($_POST["documento"]) && $_POST["documento"] != "") ? mysqli_real_escape_string($Link, $_POST["documento"]) : "";
$consulta = " SELECT Nombre, Cargo, Foto FROM empleados WHERE Nitcc = \"$documento\" ";
//echo "<br><br>$consulta<br><br>";
$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
	$row = $resultado->fetch_assoc();
	$nombre = $row['Nombre'];
	$cargo = $row['Cargo'];
	$foto = $row['Foto'];




	$consulta2 = " SELECT tipo FROM control_personal WHERE num_doc = \"$documento\" ORDER BY id DESC limit 1 ";
	//echo "<br><br>$consulta2<br><br>";
	
	
	$resultado2 = $Link->query($consulta2);
	if($resultado2->num_rows > 0){
		$row2 = $resultado2->fetch_assoc();
		if($row2['tipo'] == 1){
			$tipo = 2;	
		}
	}

	date_default_timezone_set('America/Bogota');
	$fecha = date("Y-m-d H:i:s");
	$consulta3 = " insert into control_personal (fecha, tipo, num_doc) values (\"$fecha\", \"$tipo\", \"$documento\") ";
	$resultado3 = $Link->query($consulta3);

	$resultadoAJAX = array(
		"estado" => 1,
		"tipo" => $tipo,
		"nombre" => $nombre,
		"cargo" => $cargo,
		"foto" => $foto,
		"mensaje" => ""
	);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => ""
	);
}
echo json_encode($resultadoAJAX);