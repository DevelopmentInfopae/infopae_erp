<?php

require_once '../../../config.php';
require_once '../../../db/conexion.php';

$num_doc = $_POST['num_doc'];

$focexiste = [];

$consultarFocalizacion = "SELECT table_name AS tabla FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name like 'focalizacion%' ";
$resultadoFocalizacion = $Link->query($consultarFocalizacion);
if ($resultadoFocalizacion->num_rows > 0) {
    while ($focalizacion = $resultadoFocalizacion->fetch_assoc()) {
    	$bus = "SELECT num_doc FROM ".$focalizacion['tabla']." WHERE num_doc = ".$num_doc;
    	$res = $Link->query($bus);
    	if ($res->num_rows > 0) {
    		$focexiste[] = $focalizacion['tabla'];
    	}
     }
} 
$cnt =0;
foreach ($focexiste as $foc) {
	$sqldesactivar = "UPDATE ".$foc." SET activo = 1 WHERE num_doc = ".$num_doc."; ";
	if ($Link->query($sqldesactivar)===true) {
		$cnt++;
	}
}

if ($cnt == sizeof($focexiste) && $cnt > 0) {
     $sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '49', 'Activó al títular de derecho con número de identificación <strong>".$num_doc."</strong>')";
    $Link->query($sqlBitacora);
	echo "1";
}
?>