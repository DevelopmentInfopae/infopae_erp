<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$tabla = "productos".$_SESSION['periodoActual'];

if (isset($_POST['tipo_conteo'])) {
	$tipo_conteo = $_POST['tipo_conteo'];
} else {
	$tipo_conteo = "";
}

if (isset($_POST['descripcion'])) {
	$descripcion = $_POST['descripcion'];
} else {
	$descripcion = "";
}

if (isset($_POST['unidadMedida'])) {
	$unidadMedida = $_POST['unidadMedida'];
} else {
	$unidadMedida = "";
}

if (isset($_POST['cantidadMes'])) {
	$cantidadMes = $_POST['cantidadMes'];
} else {
	$cantidadMes = "";
}

$NombreUnidad = [];
$CantidadUnd = [];


if (isset($_POST['unidadMedidaPresentacion'])) {
	$unidadMedidaPresentacion = $_POST['unidadMedidaPresentacion'];
} else {
	$unidadMedidaPresentacion = "";
}

if (isset($_POST['cantPresentacion'])) {
	$cantPresentacion = $_POST['cantPresentacion'];
} else {
	$cantPresentacion = "";
}

$NombreUnidad[1] = $unidadMedida;
if ($unidadMedidaPresentacion[1] == "u") {
	$CantidadUnd[1] = $cantidadMes * $cantPresentacion[1];
} else {
	$CantidadUnd[1] = $cantidadMes;
}

for ($i=1; $i <= sizeof($unidadMedidaPresentacion); $i++) { 
	if ($unidadMedidaPresentacion[1] == "u") {
		$NombreUnidad[$i+1] = " x ".$cantPresentacion[$i]." ".$unidadMedida;
	} else {
		$NombreUnidad[$i+1] = " x ".$cantPresentacion[$i]." ".$unidadMedidaPresentacion[$i];
	}
	$CantidadUnd[$i+1] = $cantPresentacion[$i];
}

for ($j=1; $j <=5; $j++) { 
	if (!isset($NombreUnidad[$j])) {
		$NombreUnidad[$j] = "";
		$CantidadUnd[$j] = "";
	}
}


$obtenerConsecutivo = "SELECT * FROM ".$tabla." WHERE Codigo LIKE '05".$tipo_conteo."%' AND Nivel = '3' ORDER BY Codigo DESC LIMIT 1";
$resultadoConsecutivo = $Link->query($obtenerConsecutivo);
if ($resultadoConsecutivo->num_rows > 0) {
	if ($consecutivo=$resultadoConsecutivo->fetch_assoc()) {
		$nuevoCodigo = $consecutivo['Codigo']+1;
		$nuevoCodigo = "0".$nuevoCodigo;
	}
} else {
	$nuevoCodigo = "05".$tipo_conteo."001";
}

$insertar = "INSERT INTO ".$tabla." (id, Codigo, Descripcion, Nivel, NombreUnidad1, NombreUnidad2, NombreUnidad3, NombreUnidad4, NombreUnidad5, CantidadUnd1, CantidadUnd2, CantidadUnd3, CantidadUnd4, CantidadUnd5, TipodeProducto, FecExpDesc, TipoDespacho) VALUES ('', '".$nuevoCodigo."', '".$descripcion."', '3', '".$NombreUnidad[1]."', '".$NombreUnidad[2]."', '".$NombreUnidad[3]."', '".$NombreUnidad[4]."', '".$NombreUnidad[5]."', '".$CantidadUnd[1]."', '".$CantidadUnd[2]."', '".$CantidadUnd[3]."', '".$CantidadUnd[4]."', '".$CantidadUnd[5]."', 'Insumo','".date('d/m/Y')."', '4')";

if ($Link->query($insertar)===true) {
	$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '51', 'Registró el insumo <strong>".$descripcion."</strong> con código <strong>".$nuevoCodigo."</strong> ')";
	$Link->query($sqlBitacora);
	echo "1";
} else {
	echo "Error ".$insertar;;
}

