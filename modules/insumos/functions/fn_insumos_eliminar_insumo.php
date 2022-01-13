<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$codigoproducto = $_POST['codigoProducto'];
$eliminar = "DELETE FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoproducto;

if ($Link->query($eliminar)===true) {
	echo "1";
	$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '53', 'Eliminó el insumo con código <strong>".$codigoproducto."</strong> ')";
	$Link->query($sqlBitacora);
} else {
	echo "Error : ".$eliminar;
}