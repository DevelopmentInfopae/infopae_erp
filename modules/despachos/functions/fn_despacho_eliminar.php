<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// 	Con el numero de despacho se liminan
//	Él movimiento
//	El movimiento det
// 	El despacho det
// 	Y se cambia el estado del encabezado del despacho

$numero = $_POST['despachos'];
$annoi = substr($_POST['annoi'], 2);
$mesi = $_POST['mesi'];
if($mesi < 10){
  $mesi = "0".$mesi;
}

$numero = trim($numero,",");
$arrayDespachos = explode(",", $numero); // convertimos los numeros recibidos en un array 
$numeroBitacora = '' ;

foreach ($arrayDespachos as $key => $value) {
    $numeroBitacora .=  trim($value, "'") .",";
    $consultaEncabezado = " SELECT estado FROM despachos_enc$mesi$annoi WHERE Num_Doc = $value ";
    $respuestaEncabezado = $Link->query($consultaEncabezado) or die ('Error al consultar datos del encabezado');
    if ($respuestaEncabezado->num_rows > 0) {
        $dataEncabezado = $respuestaEncabezado->fetch_assoc();
        if ($dataEncabezado['estado'] == 1) { // desde aca manejamos el inventario si el despacho esta en estado enviado
            if ($_SESSION['p_inventory'] != 0) {
                $condicionComplemento = '';
                if ($_SESSION['p_inventory'] == 2) {
                    $condicionComplemento = " AND invenc.complemento = ORDEN.Tipo_Complem ";
                }
                $actualizacionDetalleInvDestino = " UPDATE inventarios_bodegas_det inv
                                                    INNER JOIN inventarios_bodegas_enc invenc ON inv.id_bodega = invenc.id
                                                    INNER JOIN (
                                                        SELECT  encd.Tipo_Complem AS Tipo_Complem, 
                                                                enc.bodegaDestino AS bodega, 
                                                                det.CodigoProducto AS cod_Alimento, SUM(det.Cantidad) AS cantidad
                                                            FROM productosmovdet$mesi$annoi det 
                                                            INNER JOIN despachos_enc$mesi$annoi encd ON det.Numero = encd.Num_Doc
                                                            INNER JOIN productosmov$mesi$annoi enc ON det.Numero = enc.Numero
                                                            WHERE enc.Numero = $value	
                                                            GROUP BY det.CodigoProducto
                                                    ) AS ORDEN ON inv.codigo = ORDEN.cod_Alimento
                                                    SET inv.cantidad = (inv.cantidad - ORDEN.cantidad)  
                                                    WHERE invenc.bodega = ORDEN.bodega $condicionComplemento "; 
                $resultado = $Link->query($actualizacionDetalleInvDestino) or die ('Error al actualizar el inventario');	
                if ($resultado) {
                    $actualizacionDetalleInvOrigen = " UPDATE inventarios_bodegas_det inv
                                                            INNER JOIN inventarios_bodegas_enc invenc ON inv.id_bodega = invenc.id
                                                            INNER JOIN (
                                                                SELECT  encd.Tipo_Complem AS Tipo_Complem, 
                                                                        enc.bodegaOrigen AS bodega, 
                                                                        det.CodigoProducto AS cod_Alimento, SUM(det.Cantidad) AS cantidad
                                                                    FROM productosmovdet$mesi$annoi det 
                                                                    INNER JOIN despachos_enc$mesi$annoi encd ON det.Numero = encd.Num_Doc
                                                                    INNER JOIN productosmov$mesi$annoi enc ON det.Numero = enc.Numero
                                                                    WHERE enc.Numero = $value	
                                                                    GROUP BY det.CodigoProducto
                                                                ) AS ORDEN ON inv.codigo = ORDEN.cod_Alimento
                                                            SET inv.cantidad = (inv.cantidad + ORDEN.cantidad)  
                                                            WHERE invenc.bodega = ORDEN.bodega $condicionComplemento ";
                    $resultado = $Link->query($actualizacionDetalleInvOrigen) or die ('Error al actualizar el inventario');	                                         
                }
            }
        }
    }
    $tipo = 'DES';
    $anno = date('y');

    //Eliminando el detalle del movimiento
    $consulta = "DELETE FROM productosmovdet$mesi$annoi where Documento = '$tipo' and Numero = $value "; 
    $Link->query($consulta) or die ('Error al eliminar productos detalle '. mysqli_error($Link));

    //Eliminando el encabezado del movimiento
    $consulta = "DELETE FROM productosmov$mesi$annoi where Documento = '$tipo' and Numero  = $value ";
    $Link->query($consulta) or die ('Error al eliminar productos encabezado '. mysqli_error($Link));

    //Eliminando el detalle del despacho
    $consulta = "DELETE FROM despachos_det$mesi$annoi where Tipo_Doc = '$tipo' and Num_Doc = $value ";
    $Link->query($consulta) or die ('Error al eliminar despachos detalle '. mysqli_error($Link));

    //Actualizando es estado del despacho
    $consulta = "UPDATE despachos_enc$mesi$annoi set Estado = 0 where Tipo_Doc = '$tipo' and Num_Doc = $value "; 
    $Link->query($consulta) or die ('Error al actualizar despachos encabezado '. mysqli_error($Link));
}

$numeroBitacora = trim($numeroBitacora, ",");

//Insertando el registro en la bitacora
$consultaBitacora = " INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) 
                      VALUES ('" . date("Y-m-d H-i-s") . "', 
                              '" . $_SESSION["idUsuario"] . "', 
                              '55', 
                              ' Se eliminó los despachos Numero: <strong>".$numeroBitacora."</strong>' )";
$Link->query($consultaBitacora) or die ('Error al insertar la bitacora '. mysqli_error($Link));

echo "1";
