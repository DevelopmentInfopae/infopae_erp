<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

if (isset($_POST['despachosEliminar'])) {
	$idDespachos = $_POST['despachosEliminar'];
} else {
	echo '<script type="text/javascript"> alert("Error al obtener despachos a eliminar.");window.close(); </script>';
}

if (isset($_POST['tablaMesInicio'])) {
	$tablaMes = $_POST['tablaMesInicio'];
} else {
	echo '<script type="text/javascript"> alert("Error al obtener tabla de despacho.");window.close(); </script>';
}

$insmov = "insumosmov".$tablaMes.$_SESSION['periodoActual'];
$insmovdet = "insumosmovdet".$tablaMes.$_SESSION['periodoActual'];
$despachosNum = [];
$despachos = [];

$idArray = explode(",", $idDespachos);
foreach ($idArray as $key => $value) {
	$val = trim($value);
	if ($val != "") {
		$despachos[$key] = $val;
	}	
}

foreach ($despachos as $key => $id) {
	$consulta = "SELECT Numero FROM $insmov WHERE Id = '".$id."'";
	$resultado = $Link->query($consulta);
	if ($resultado->num_rows > 0) {
		while ($despaData = $resultado->fetch_assoc()) {
			$despachosNum[$id] = $despaData['Numero'];
		}
	} else {
		exit(' Error '.$consulta);
	}
}

$numerosDespachos = "";
foreach ($despachos as $key => $id) {
	$delete = "DELETE FROM $insmov WHERE Id = '".$id."'";
	if ($Link->query($delete)) {
		$delete = "DELETE FROM $insmovdet WHERE Numero = '".$despachosNum[$id]."'";
		if ($Link->query($delete)) {
			$numerosDespachos.=$despachosNum[$id].", ";
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
