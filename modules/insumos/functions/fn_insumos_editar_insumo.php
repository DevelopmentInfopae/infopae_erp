<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$tabla = "productos".$_SESSION['periodoActual'];

if (isset($_POST['idinsumo'])) {
	$idinsumo = $_POST['idinsumo'];
} else {
	$idinsumo = "";
}

if (isset($_POST['codigoinsumo'])) {
	$codigoinsumo = $_POST['codigoinsumo'];
} else {
	$codigoinsumo = "";
}

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
$CantidadUnd[1] = $cantidadMes;

for ($i=1; $i <= sizeof($unidadMedidaPresentacion); $i++) {
	if ($unidadMedidaPresentacion[1] == "u") {
		$NombreUnidad[$i+1] = $unidadMedida;
	} else {
		if (strpos($cantPresentacion[$i], ".")) {
			$nomMedida = substr($cantPresentacion[$i], 0, strpos($cantPresentacion[$i], "."));
		} else {
			$nomMedida = $cantPresentacion[$i];
		}
		$NombreUnidad[$i+1] = " x ".$nomMedida." ".$unidadMedidaPresentacion[$i];
	}
	$CantidadUnd[$i+1] = $cantPresentacion[$i];
}

for ($j=1; $j <=5; $j++) {
	if (!isset($NombreUnidad[$j])) {
		$NombreUnidad[$j] = "";
		$CantidadUnd[$j] = 0;
	}
}

if (substr($codigoinsumo, 2, 2) != $tipo_conteo) {
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
} else {
	$nuevoCodigo = $codigoinsumo;
}

$insertar = "UPDATE ".$tabla." SET Descripcion = '".$descripcion."', NombreUnidad1 = '".$NombreUnidad[1]."', NombreUnidad2 = '".$NombreUnidad[2]."', NombreUnidad3 = '".$NombreUnidad[3]."', NombreUnidad4 = '".$NombreUnidad[4]."', NombreUnidad5 = '".$NombreUnidad[5]."', CantidadUnd1 = '".$CantidadUnd[1]."', CantidadUnd2 = '".$CantidadUnd[2]."', CantidadUnd3 = '".$CantidadUnd[3]."', CantidadUnd4 = '".$CantidadUnd[4]."', CantidadUnd5 = '".$CantidadUnd[5]."', Codigo = '".$nuevoCodigo."' WHERE id = ".$idinsumo;

if ($Link->query($insertar)===true) {
	$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '52', 'Actualizó el insumo <strong>".$descripcion."</strong> con código <strong>".$nuevoCodigo."</strong> ')";
	$Link->query($sqlBitacora);
	echo '{"respuesta" : [{"exitoso":"1", "codigo" : "'.$nuevoCodigo.'"}]}';
} else {
	echo "Error ".$insertar;;
}

