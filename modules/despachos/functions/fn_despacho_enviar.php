<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

/*************** variables principales ********************/
$numero = $_POST['despacho'];
$annoi = substr($_POST['annoi'], 2);
$mesi = $_POST['mesi'];
if($mesi < 10){ $mesi = "0".$mesi; }
$complemento = $_POST['complemento'];
$nameWarehouse = $_POST['nom_sede'];

/*************************** buscar el dato de los alimentos en los despachos creados *********************************/
$consultaProductos = " SELECT   det.CodigoProducto, 
                                SUM(det.Cantidad) AS cantidad,
                                enc.BodegaDestino,
                                enc.BodegaOrigen
                            FROM productosmov$mesi$annoi enc 
                            INNER JOIN productosmovdet$mesi$annoi det ON enc.Numero = det.Numero
                            WHERE enc.Numero = $numero
                            GROUP BY CodigoProducto "; 
$respuestaProductos = $Link->query($consultaProductos) or die ('Error consultado productos Ln 20');
if ($respuestaProductos->num_rows > 0) {  // si existen productos
    while ($dataProductos = $respuestaProductos->fetch_assoc()) { // almacenamos en un array asociativo [codigo => cantidad] 
        $productos[$dataProductos['CodigoProducto']] = $dataProductos['cantidad'];
        $bodega = $dataProductos['BodegaDestino'];
        $bodegaOrigen = $dataProductos['BodegaOrigen'];
    }
    $insercionMasivaDetalle = " INSERT INTO inventarios_bodegas_det (id_bodega, codigo, cantidad, fecha_entrada) VALUES ";
    $validacionComplementoI = '';
    if ($_SESSION['p_inventory'] == 2) { // en caso que el inventario sea por complemento 
        $validacionComplementoI = " AND e.complemento = '$complemento' ";
    }
    $consultaValidacion = " SELECT e.id 
                                FROM inventarios_bodegas_enc e
                                WHERE e.bodega = '$bodega' $validacionComplementoI ";                    
    $respuestaValidacion = $Link->query($consultaValidacion) or die ('Error al consultar Ln33');
    if ($respuestaValidacion->num_rows > 0) {  // validacion si esta creada la bodega en los inventarios
        $dataValidacion = $respuestaValidacion->fetch_assoc();
        $id_bodega = $dataValidacion['id']; // id de bodega existente
        $consultaValidacionDetalle = " SELECT d.id
                                        FROM inventarios_bodegas_det d 
                                        WHERE d.id_bodega = '" .$id_bodega. "'";
        $respuestaValidacionDetalle = $Link->query($consultaValidacionDetalle) or die ('Error al consultar Ln41');
        if ($respuestaValidacionDetalle->num_rows > 0) { // si ya existen productos en el detalle del inventario
            $bandera = false;
            foreach ($productos as $key => $value) { // iteramos en los productos
                $validacionExiste = " SELECT d.id
                                        FROM inventarios_bodegas_det d 
                                        WHERE d.id_bodega = '" .$id_bodega. "' AND d.codigo = '" .$key. "'";
                $respuestaExiste = $Link->query($validacionExiste) or die ('Error al validar existencia de detalles Ln 48');
                if ($respuestaExiste->num_rows > 0) {  // entramos si existe el producto en el inventario para sumar la cantidad
                    $dataExiste = $respuestaExiste->fetch_assoc();
                    $updateCantidad = " UPDATE inventarios_bodegas_det SET cantidad = (cantidad + $value) WHERE id = " .$dataExiste['id'];
                    $Link->query($updateCantidad) or die('Error Ln48');
                }else{  // si en esta iteracion no existe el producto en el detalle
                    $bandera = true;
                    $insercionMasivaDetalle .= " ('$id_bodega', '" .$key. "', '" .$value. "', '" .date("Y-m-d H:i:s"). "'), ";
                }                        
            }  // aca termina la iteracion de los productos
            if ($bandera == true) {  // si en alguna iteracion se crea un producto en el inventario entramos aca a realizar la insercion masiva
                $insercionMasivaDetalle = trim($insercionMasivaDetalle, ", "); 
                $Link->query($insercionMasivaDetalle) or die('Error al insertar el detalle del inventario Ln54');
            }
        }else{ // si no existe productos creados en el detalle insertamos masivamente el inventario
            foreach ($productos as $key => $value) {
                $insercionMasivaDetalle .= " ('$id_bodega', '" .$key. "', '" .$value. "', '" .date("Y-m-d H:i:s"). "'), ";
            }
            $insercionMasivaDetalle = trim($insercionMasivaDetalle, ", "); 
            $Link->query($insercionMasivaDetalle) or die('Error al insertar el detalle del inventario Ln60');
        } 
        $Link->query(" UPDATE inventarios_bodegas_enc SET inventario_inicial = 1 WHERE id = '" .$id_bodega. "'" );
        $Link->query(" UPDATE despachos_enc$mesi$annoi set estado = 1 WHERE Num_Doc = $numero " );
        $Link->query(" UPDATE productosmov$mesi$annoi set fecha_envio = '".date('Y-m-d')."' WHERE Numero = $numero " );

        /************* ACTUALIZACION CANTIDADES EN LAS BODEGAS PRINCIPALES ****************/
        // tenemos que iterar en los productos que se envian para descontar cantidades de bodega origen crear o actualizar segun sea el caso
        // se debe de hacer el procedimiento dos veces por que son dos id de bodega diferente origen y destino
        $condicionComplemento = '';
        if ($_SESSION['p_inventory'] == 2) {
			$condicionComplemento = " AND enc.complemento = '$complemento' ";
		}
        foreach ($productos as $key => $value) {
            $consultaExistenciaOrigen = " SELECT id
                                            FROM inventarios_bodegas_enc enc  
                                            WHERE enc.bodega = '$bodegaOrigen' $condicionComplemento";
            $respuestaExistenciaOrigen = $Link->query($consultaExistenciaOrigen);
            if ($respuestaExistenciaOrigen->num_rows > 0) { // obtenemos el id de la bodega Origen
                $dataExistenciaOrigen = $respuestaExistenciaOrigen->fetch_assoc();
                $id_bodegaOrigen = $dataExistenciaOrigen['id'];  // id de la bodega de origen
                $consultaProducto = " SELECT id FROM inventarios_bodegas_det WHERE id_bodega = $id_bodegaOrigen  AND codigo = '$key' "; 
                $respuestaProducto = $Link->query($consultaProducto) or die('Error al consultar Ln 91');
                if ($respuestaProducto->num_rows > 0) { // existe en este caso entramos a actualizar
                    $updateOrigen = " UPDATE inventarios_bodegas_det 
                                            SET cantidad = (cantidad - $value),
                                                fecha_salida = '" .date("Y-m-d H:i:s"). "'
                                                WHERE id_bodega = $id_bodegaOrigen  AND codigo = '$key' ";
                    $Link->query($updateOrigen) or die ('Error Ln 97');
                }else{
                    $aux = 0-$value;
                    $insertOrigen = " INSERT    INTO inventarios_bodegas_det (id_bodega, codigo, cantidad, fecha_salida ) 
                                                VALUES (    '$id_bodegaOrigen', 
                                                            '$key', 
                                                            '$aux', 
                                                            '" .date("Y-m-d H:i:s"). "' ) "; 
                    $Link->query($insertOrigen) or die('Error Ln 105');
                }
            }                                
        }

        //Insertando el registro en la bitacora
        $consultaBitacora = " INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) 
                                        VALUES ('" . date("Y-m-d H-i-s") . "', 
                                                '" . $_SESSION["idUsuario"] . "', 
                                                '110', 
                                                ' Se ingreso inventario en la bodega: <strong>".$nameWarehouse."</strong>' )";
        $Link->query($consultaBitacora) or die ('Error al insertar la bitacora '. mysqli_error($Link));
        echo "1";
        exit();                          
    }else{ // si no esta creada la bodega en los inventarios
        echo "No se encontro la bodega: <strong>" .$nameWarehouse. "</strong>" ;
    }   
}else{ // si no existen alimentos en la orden 
    echo "No se encontraron alimentos en el despacho : <strong> N°" .$numero. "</strong>" ;
}    

