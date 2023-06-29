<?php
require_once '../../../db/conexion.php';
include '../../../config.php';

$numero = $_POST['despacho'];
$tipo = 'DES';
$anno = date('y');

$annoi = substr($_POST['annoi'], 2);
$mesi = $_POST['mesi'];
if($mesi < 10){
	$mesi = "0".$mesi;
}

if ($_POST['estado'] == 1) {
	if ($_SESSION['p_inventory'] != 0) {
		$condicionComplemento = '';
		if ($_SESSION['p_inventory'] == 2) {
			$condicionComplemento = " AND invenc.complemento = ORDEN.Tipo_Complem ";
		}
		$actualizacionDetalleInv = " UPDATE inventarios_bodegas_det inv
										INNER JOIN inventarios_bodegas_enc invenc ON inv.id_bodega = invenc.id
										INNER JOIN (SELECT enc.Tipo_Complem AS Tipo_Complem, enc.bodega AS bodega, det.cod_Alimento AS cod_Alimento, SUM(det.Cantidad) AS cantidad
														FROM orden_compra_det$mesi$annoi det 
														INNER JOIN orden_compra_enc$mesi$annoi enc ON det.Num_Doc = enc.Num_Doc
														WHERE enc.Num_OCO = $numero	
														GROUP BY det.cod_Alimento
													) AS ORDEN ON inv.codigo = ORDEN.cod_Alimento
										SET inv.cantidad = (inv.cantidad - ORDEN.cantidad)  
										WHERE invenc.bodega = ORDEN.bodega $condicionComplemento
										"; 
		$Link->query($actualizacionDetalleInv) or die ('Error al actualizar el inventario');								
	}
}


$consulta = " DELETE FROM orden_compra_det$mesi$annoi 
				WHERE Num_Doc IN ( 
					SELECT DISTINCT Num_Doc FROM orden_compra_enc$mesi$annoi WHERE Num_OCO = $numero 
					) ";
$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
$Link->query(" UPDATE orden_compra_enc$mesi$annoi SET estado = 0 WHERE Num_OCO = $numero ");
//Insertando el registro en la bitacora
$consultaBitacora = " INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) 
                      VALUES ('" . date("Y-m-d H-i-s") . "', 
                              '" . $_SESSION["idUsuario"] . "', 
                              '100', 
                              ' Se eliminó la orden de compra Número: <strong>".$numero."</strong>' )";
$Link->query($consultaBitacora) or die ('Error al insertar la bitacora '. mysqli_error($Link));

$Link->close();
echo "1";