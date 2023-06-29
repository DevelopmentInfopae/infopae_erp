<?php
    require_once '../../../db/conexion.php';
    include '../../../config.php';
    $num_oco = $_POST['Num_OCO'];
    $bodega = $_POST['bodega'];
    $complemento = $_POST['complemento'];
    $nameWarehouse = $_POST['nameWarehouse'];
    $mes = (isset($_POST['mes']) && $_POST['mes'] < 10) ? '0'.$_POST['mes'] : $_POST['mes'];
    $periodoActual = $_SESSION['periodoActual'];

    /*************************** buscar el dato de los alimentos en las ordenes creadas *********************************/
    $consultaOrden = " SELECT   cod_Alimento, 
                                SUM(Cantidad) AS cantidad
                            FROM orden_compra_enc$mes$periodoActual enc 
                            INNER JOIN orden_compra_det$mes$periodoActual det ON enc.Num_Doc = det.Num_Doc
                            WHERE enc.Num_OCO = $num_oco
                            GROUP BY cod_Alimento ";
    $respuestaOrden = $Link->query($consultaOrden) or die ('Error consultado alimentos Ln 17');
    if ($respuestaOrden->num_rows > 0) {  // si existen alimentos en la orden
        while ($dataOrden = $respuestaOrden->fetch_assoc()) { // almacenamos en un array asociativo [codigo => cantidad] 
            $orden[$dataOrden['cod_Alimento']] = $dataOrden['cantidad'];
        }
        $insercionMasivaDetalle = " INSERT INTO inventarios_bodegas_det (id_bodega, codigo, cantidad, fecha_entrada) VALUES ";
        $validacionComplementoI = '';
        if ($_SESSION['p_inventory'] == 2) { // en caso que el inventario sea por complemento 
            $validacionComplementoI = " AND e.complemento = '$complemento' ";
        }
        $consultaValidacion = " SELECT e.id 
                                FROM inventarios_bodegas_enc e
                                WHERE e.bodega = '$bodega' $validacionComplementoI ";                       
        $respuestaValidacion = $Link->query($consultaValidacion) or die ('Error al consultar Ln10');
        if ($respuestaValidacion->num_rows > 0) {  // validacion si esta creada la bodega en los inventarios
            $dataValidacion = $respuestaValidacion->fetch_assoc();
            $id_bodega = $dataValidacion['id']; // id de bodega existente
            $consultaValidacionDetalle = " SELECT d.id
                                                FROM inventarios_bodegas_det d 
                                                WHERE d.id_bodega = '" .$id_bodega. "'";
            $respuestaValidacionDetalle = $Link->query($consultaValidacionDetalle) or die ('Error al consultar Ln21');
            if ($respuestaValidacionDetalle->num_rows > 0) { // si ya existen productos en el detalle del inventario
                $bandera = false;
                foreach ($orden as $key => $value) {
                    $validacionExiste = " SELECT d.id
                                            FROM inventarios_bodegas_det d 
                                            WHERE d.id_bodega = '" .$id_bodega. "' AND d.codigo = '" .$key. "'";
                    $respuestaExiste = $Link->query($validacionExiste) or die ('Error al validar existencia de detalles Ln 45');
                    if ($respuestaExiste->num_rows > 0) {
                        $dataExiste = $respuestaExiste->fetch_assoc();
                        $updateCantidad = " UPDATE inventarios_bodegas_det SET cantidad = (cantidad + $value), fecha_entrada = '" .date("Y-m-d H:i:s"). "' WHERE id = " .$dataExiste['id'];
                        $Link->query($updateCantidad) or die('Error Ln48');
                    }else{
                        $bandera = true;
                        $insercionMasivaDetalle .= " ('$id_bodega', '" .$key. "', '" .$value. "', '" .date("Y-m-d H:i:s"). "'), ";
                    }                        
                }
                if ($bandera == true) {
                    $insercionMasivaDetalle = trim($insercionMasivaDetalle, ", "); 
                    $Link->query($insercionMasivaDetalle) or die('Error al insertar el detalle del inventario Ln54');
                }
            }else{ // si no existe productos creados en el detalle
                foreach ($orden as $key => $value) {
                    $insercionMasivaDetalle .= " ('$id_bodega', '" .$key. "', '" .$value. "', '" .date("Y-m-d H:i:s"). "'), ";
                }
                $insercionMasivaDetalle = trim($insercionMasivaDetalle, ", "); 
                $Link->query($insercionMasivaDetalle) or die('Error al insertar el detalle del inventario Ln60');
            } 
            $Link->query(" UPDATE inventarios_bodegas_enc SET inventario_inicial = 1 WHERE id = '" .$id_bodega. "'" );
            $Link->query(" UPDATE orden_compra_enc$mes$periodoActual set estado = 1 WHERE Num_OCO = $num_oco " );
            $Link->query(" UPDATE orden_compra_enc$mes$periodoActual set fecha_recibo = '".date('Y-m-d')."' WHERE Num_OCO = $num_oco " );
            echo "1";
            exit();                          
        }else{ // si no esta creada la bodega en los inventarios
            echo "No se encontro la bodega: <strong>" .$nameWarehouse. "</strong>" ;
        }   
    }else{ // si no existen alimentos en la orden 
        echo "No se encontraron alimentos en la orden Número: <strong>" .$num_oco. "</strong>" ;
    }    
                         
  

