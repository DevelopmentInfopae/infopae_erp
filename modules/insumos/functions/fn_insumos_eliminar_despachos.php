<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';

if (isset($_POST['idDespacho'])) {
	$despachos = $_POST['idDespacho'];
} else {
	echo '<script type="text/javascript"> alert("Error al obtener despachos a eliminar.");window.close(); </script>';
}

if (isset($_POST['tablaMes'])) {
	$tablaMes = $_POST['tablaMes'];
} else {
	echo '<script type="text/javascript"> alert("Error al obtener tabla de despacho.");window.close(); </script>';
}

$insmov = "insumosmov".$tablaMes.$_SESSION['periodoActual'];
$insmovdet = "insumosmovdet".$tablaMes.$_SESSION['periodoActual'];
$despachosNum = [];

foreach ($despachos as $key => $despacho) {
	$consulta = "SELECT Numero FROM $insmov WHERE Id = '".$despacho."'";
	$resultado = $Link->query($consulta);
	if ($resultado->num_rows > 0) {
		while ($despaData = $resultado->fetch_assoc()) {
			$despachosNum[$despacho] = $despaData['Numero'];
		}
	} else {
		exit(' Error '.$consulta);
	}
}

$numerosDespachos = "";
foreach ($despachos as $key => $despacho) {
	$delete = "DELETE FROM $insmov WHERE Id = '".$despacho."'";
	if ($Link->query($delete)) {
		$delete = "DELETE FROM $insmovdet WHERE Numero = '".$despachosNum[$despacho]."'";
		if ($Link->query($delete)) {
			$numerosDespachos.=$despachosNum[$despacho].", ";
		} else {
			exit(' Error '.$delete);
		}
	} else {
		exit(' Error '.$delete);
	}
}

$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '54', 'Eliminó los despachos de Insumos con números : <strong>".trim($numerosDespachos, ", ")."</strong> ')";
$Link->query($sqlBitacora);
echo "1";
