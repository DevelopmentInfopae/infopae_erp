<?php
set_time_limit (0);
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require_once 'model/inventory_model.php';

if ($_SESSION['p_inventory'] != 0) { // aca entramos solo cuando el inventario este activo
    $alimento = $_POST['codigo'];
    $bodega = $_POST['bodega'];
    $fechaSinc = $_POST['fechaSic'];
    $periodoActual = $_SESSION['periodoActual'];
    $suma = [];
    $resta = [];

    $factores = search_factors($Link, $periodoActual);

    $consultaSedes = " SELECT cod_sede FROM sedes$periodoActual WHERE cod_sede = $bodega ";
    $respuestaSedes = $Link->query($consultaSedes) or die('Error al consultar ln 9');
    if ($respuestaSedes->num_rows > 0) {  // aca entramos cuando las bodegas son secundarias

        /********************** REMISIONES O SUMAS ******************************/
        $suma = sum_remision($Link, $bodega, $alimento);
        /********************** REMISIONES O SUMAS ******************************/

        /************************ MINUTAS O RESTAS ******************************/
        $resta = res_minuta($Link, $fechaSinc, $periodoActual, $bodega, $alimento, $factores);
        /************************ MINUTAS O RESTAS ******************************/
    }else{ // aca entramos cuando las bodegas son principales

        /*********************** ORDENES DE COMPRA o SUMAS ****************************/
        $suma = sum_ord($Link, $bodega, $alimento); 
        /*********************** ORDENES DE COMPRA o SUMAS ****************************/

        /*********************** REMISIONES DE ALIMENTOS o RESTAS ****************************/
        $resta = res_remision($Link, $bodega, $alimento); 
        /*********************** REMISIONES DE ALIMENTOS o RESTAS ****************************/
    } 

    foreach ($suma as $keyS => $valueS) {
        if ($_SESSION['p_inventory'] == 2) {
            $id_bodega = search_warehouse_id($Link, $bodega, $keyS);
            $actualizacionArriba = " UPDATE inventarios_bodegas_det SET cantidad = $valueS WHERE id_bodega = '$id_bodega' AND codigo = '$alimento' ";
            $res = $Link->query($actualizacionArriba) or die ('Error en actualizaciones Ln114 '.$res);
        }else{
            $id_bodega = search_warehouse_id($Link, $bodega, '');
            $actualizacionArriba = " UPDATE inventarios_bodegas_det SET cantidad = $valueS WHERE id_bodega = '$id_bodega' AND codigo = '$alimento' ";
            $res = $Link->query($actualizacionArriba) or die ('Error en actualizaciones Ln118 '.$res);
        }
    }
    foreach ($resta as $keyR => $valueR) {
        if ($_SESSION['p_inventory'] == 2) {
            $id_bodega = search_warehouse_id($Link, $bodega, $keyR);
            if (!empty($suma)) {
                $actualizacionAbajo = " UPDATE inventarios_bodegas_det SET cantidad = (cantidad-$valueR) WHERE id_bodega = '$id_bodega' AND codigo = '$alimento' ";
                $res = $Link->query($actualizacionAbajo) or die ('Error en actualizaciones Ln125 '.$res);
            }else{
                $actualizacionAbajo = " UPDATE inventarios_bodegas_det SET cantidad = $valueR WHERE id_bodega = '$id_bodega' AND codigo = '$alimento' ";
                $res = $Link->query($actualizacionAbajo) or die ('Error en actualizaciones Ln125 '.$res);
            }
        }else{
            $id_bodega = search_warehouse_id($Link, $bodega, '');
            if (!empty($suma)) {
                $actualizacionArriba = " UPDATE inventarios_bodegas_det SET cantidad = (cantidad-$valueR) WHERE id_bodega = '$id_bodega' AND codigo = '$alimento' ";
                $res = $Link->query($actualizacionArriba) or die ('Error en actualizaciones Ln118 '.$res);
            }else{
                $actualizacionArriba = " UPDATE inventarios_bodegas_det SET cantidad = $valueR WHERE id_bodega = '$id_bodega' AND codigo = '$alimento' ";
                $res = $Link->query($actualizacionArriba) or die ('Error en actualizaciones Ln118 '.$res);
            }
        }
    }
    $resultadoAJAX = [
        "estado" => 1,
        "mensaje" => "Actualización Exitosa" 
    ];
    echo json_encode($resultadoAJAX);
    exit; 
} // validacion inventario activo



































