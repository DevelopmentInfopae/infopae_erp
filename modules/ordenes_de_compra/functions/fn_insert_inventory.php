<?php
    $insercionMasivaDetalle = " INSERT INTO inventarios_bodegas_det (id_bodega, codigo, cantidad, fecha_entrada) VALUES ";
    $cantidadTotalI = [];
    for ($x=0; $x < count($sedesCobertura) ; $x++) { // por cada sede cobertura
        $sedeI = $sedesCobertura[$x];
        $cod_sedeI = $bodega;
        $validacionComplementoI = '';
        if ($_SESSION['p_inventory'] != 2) {
            $ValidacionComplementoI .= " AND e.complemento = '$tipo' ";
        }
        $consultaValidacion = " SELECT e.id 
                                FROM inventarios_bodegas_enc e
                                WHERE e.bodega = '$cod_sedeI' $validacionComplementoI ";
        $respuestaValidacion = $Link->query($consultaValidacion) or die ('Error al consultar Ln10');
        if ($respuestaValidacion->num_rows > 0) {  // validacion si existe la bodega en inventario
            $dataValidacion = $respuestaValidacion->fetch_assoc();
            $id_bodega = $dataValidacion['id']; // id de bodega existente
            $consultaValidacionDetalle = " SELECT d.id
                                            FROM inventarios_bodegas_det d 
                                            WHERE d.id_bodega = '" .$id_bodega. "'";
            $respuestaValidacionDetalle = $Link->query($consultaValidacionDetalle) or die ('Error al consultar Ln16');
            if ($respuestaValidacionDetalle->num_rows > 0) { // entramos a esta condicion si existe detalles de los productos
                # code...
            }else{ // desde aca no encontro ningun detalle insertamos masivamente el detalle del inventario
                // $insercionMasivaDetalle = " INSERT INTO inventarios_bodegas_det (id_bodega, codigo, cantidad, fecha_entrada) VALUES ";
                for ($y=0; $y<count($complementosCantidades); $y++) { 
                    $cantidadI = 0;
                    $insercionMasivaDetalle .= " ( ";
                    $complemento = $complementosCantidades[$y]; 
                    for ($l=1; $l<=$cantGruposEtarios ; $l++) { 
                        $grupoIndex = "grupo".$l;
                        if (isset($complemento[$grupoIndex])) {
                            if($complemento[$grupoIndex]>0){
                                $cantidadI += ( $complemento[$grupoIndex] * $sedeI[$grupoIndex] );
                                if (isset($cantidadTotalI[$complemento['codigo']])) { $cantidadTotalI[$complemento['codigo']] += $cantidadI; }
                                if (!isset($cantidadTotalI[$complemento['codigo']])) { $cantidadTotalI[$complemento['codigo']] = $cantidadI; }
                            }
                        }
                    }
                    // $insercionMasivaDetalle .= " '$id_bodega', '" .$complemento['codigo']. "', '" .$cantidadI. "', '" .date("Y-m-d H:i:s"). "'), ";
                }
                // $insercionMasivaDetalle = trim($insercionMasivaDetalle, ", ");
                // $Link->query($insercionMasivaDetalle) or die('Error al insertar el detalle del inventario');
            }                                
        }else{ // si no existe
            echo "No se encontro bodega de inventario para la sede codigo : <strong>" .$cod_sedeI. "</strong>" ;
			break;
        }                        
    } // sedes_cobertura

    exit(var_dump($cantidadTotalI));

