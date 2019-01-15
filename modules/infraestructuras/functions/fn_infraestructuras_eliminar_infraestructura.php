<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$idinfraestructura = $_POST['idinfraestructura'];

$consultaNomSede = "SELECT sedes.nom_sede FROM Infraestructura, sedes".$_SESSION['periodoActual']." as sedes WHERE sedes.cod_sede = Infraestructura.cod_sede AND Infraestructura.id = ".$idinfraestructura;
$resultadoNomSede = $Link->query($consultaNomSede);
if ($resultadoNomSede->num_rows > 0) {
	if ($infoSede = $resultadoNomSede->fetch_assoc()) {
		$nom_sede = $infoSede['nom_sede'];
	}
} else {
	$nom_sede = "nombre no encontrado.";
}

$borrarInfraestructura = "DELETE FROM Infraestructura WHERE id = ".$idinfraestructura;
if ($Link->query($borrarInfraestructura)=== true) {

	$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '42', 'Eliminó el diagnóstico de infraestructura de la sede <strong>".$nom_sede."</strong>')";
	$Link->query($sqlBitacora);

	echo "1";
} else {
	echo "0";
}

 ?>