<?php
set_time_limit (0);

function search_products($Link, $codigoPreparacion, $alimento){ // buscamos los alimentos de las preparaciones
    $minutaDiaGrupo = [];
    $consultaDetalle = " SELECT det.codigo, det.Cantidad 
                            FROM fichatecnicadet det 
                            INNER JOIN fichatecnica enc ON enc.id = det.idft
                            WHERE enc.codigo = '$codigoPreparacion' ";
    if ($alimento != '') {
        $consultaDetalle .= " AND det.codigo = '$alimento' ";
    }                         
    $respuestaDetalle = $Link->query($consultaDetalle) or die ('Error al consultar el detalle de los alimentos Ln 14') ;
    if ($respuestaDetalle->num_rows > 0) {
        while ($dataDetalle = $respuestaDetalle->fetch_assoc()) {
            $minutaDiaGrupo[] = $dataDetalle; 
        } // while alimentos
    } // respuesta detalle alimentos  
    return $minutaDiaGrupo;
}

function search_warehouse_id($Link, $cod_sede, $complemento){ // buscamos el id de la bodega
    $consultaBodega = " SELECT id FROM inventarios_bodegas_enc WHERE bodega = '$cod_sede' ";
    if ($_SESSION['p_inventory'] == 2) {
        $consultaBodega .= " AND complemento = '$complemento' ";
    }
    $respuestaBodega = $Link->query($consultaBodega);
    if ($respuestaBodega->num_rows > 0) {
        $dataBodega = $respuestaBodega->fetch_assoc();
        $id = $dataBodega['id'];
        return $id;
    }else{
        return 0;
    }
}

function validate_date($Link, $cod_sede, $complemento, $bigYear, $month, $day, $fechas, $actualizado){
    $consultaBodega = " SELECT synchronization_date FROM inventarios_bodegas_enc WHERE bodega = '$cod_sede' ";
    if ($_SESSION['p_inventory'] == 2) {
        $consultaBodega .= " AND complemento = '$complemento' ";
    } 
    $respuestaBodega = $Link->query($consultaBodega);
    if ($respuestaBodega->num_rows > 0) {
        $dataBodega = $respuestaBodega->fetch_assoc();
        $dateSincronized = $dataBodega['synchronization_date']; 
        if (($dateSincronized) < ($bigYear."-".$month."-".$day)) {
            if (($fechas[$bigYear."-".$month."-".$day]) == 1) {
                return true;
            }else{
                if (isset($fechas[$dateSincronized]) && (($fechas[$dateSincronized])+1 == $fechas[$bigYear."-".$month."-".$day])) {
                    return true;
                }else{
                    return false;
                }
            }
        }if (($dateSincronized) == ($bigYear."-".$month."-".$day)) {
            if ($actualizado == true) {
                return true;
            }
            else{
                return false;
            }
        }
    }else{
        return false;
    }
}

function search_factors($Link, $periodoActual){  // buscamos los factores de conversion 
    $consultaFactoresCoversion = " SELECT   Codigo AS codigo, 
                                            CantidadUnd1 AS factor 
                                        FROM productos$periodoActual
                                        WHERE Codigo LIKE '03___%' ";
    $respuestaFactoresCoversion = $Link->query($consultaFactoresCoversion) or die ('Error en facores Ln 36');
    if ($respuestaFactoresCoversion->num_rows > 0) {
        while ($dataFactores = $respuestaFactoresCoversion->fetch_assoc()) {
            $factores[$dataFactores['codigo']] = $dataFactores['factor'];
        }
    }
    return $factores;
}

/******************* BODEGAS SECUNDARIAS *********************/
function sum_remision($Link, $bodega, $alimento){ // se buscan las sumatorias de las bodegas secundarias
    $suma = [];
    $consultaRemisionesEnc = " SHOW TABLES LIKE 'despachos_enc%' ";
    $respuestaRemisionesEnc = $Link->query($consultaRemisionesEnc) or die ('Error ln 73');
    if ($respuestaRemisionesEnc->num_rows > 0) {
        while ($dataRemisionesEnc = $respuestaRemisionesEnc->fetch_assoc()) {
            $aux = array_values($dataRemisionesEnc);
            $aux=substr(($aux[0]), 13);
            $consultaRemisiones = " SELECT  SUM(Cantidad) AS total
                                        FROM productosmov$aux  enc
                                        INNER JOIN productosmovdet$aux det ON enc.Numero = det.Numero
                                        INNER JOIN despachos_enc$aux encDes ON encDes.Num_Doc = enc.Numero
                                        WHERE enc.BodegaDestino = '$bodega' AND det.CodigoProducto = '$alimento' AND encDes.estado = 1
                                        GROUP BY enc.Numero ";
            if ($_SESSION['p_inventory'] == 2) {
                $consultaRemisiones .= " , encDes.Tipo_Complem ";
            }  
            $respuestaRemisiones = $Link->query($consultaRemisiones) or die('Error consultando remisiones Ln84');
            if ($respuestaRemisiones->num_rows > 0) {
                while ($dataRemisiones = $respuestaRemisiones->fetch_assoc()) {
                    if ($_SESSION['p_inventory'] == 2) {
                        isset($suma[$dataRemisiones['Tipo_Complem']]) ? $suma[$dataRemisiones['Tipo_Complem']] += $dataRemisiones['total'] : $suma[$dataRemisiones['Tipo_Complem']] = $dataRemisiones['total'];
                    }else{
                        isset($suma['total']) ? $suma['total'] += $dataRemisiones['total'] : $suma['total'] = $dataRemisiones['total'];
                    }
                }
            }      
        }
        return $suma;
    } 
}

function res_minuta($Link, $fechaSinc, $periodoActual, $bodega, $alimento, $factores){
    $resta = [];
    $consultaMenus = " SELECT ANO, MES, DIA, SEMANA, MENU FROM planilla_semanas WHERE CONCAT(ANO, '-', MES, '-', DIA) <= '$fechaSinc'"; 
    $respuestaMenus = $Link->query($consultaMenus) or die ('Error consultado los menus Ln 54');
    if ($respuestaMenus->num_rows > 0) {
        while ($dataMenus = $respuestaMenus->fetch_assoc()) { // primero vamos a iterar en los menus díarios que estan ejecutados
            $menuDia = $dataMenus['MENU'];
            $year = substr($dataMenus['ANO'],2,2);
            $bigYear = $dataMenus['ANO'];
            $day = $dataMenus['DIA'];
            $week = $dataMenus['SEMANA'];
            $month = $dataMenus['MES'];
            $consultaComplementos = " SELECT DISTINCT(Cod_Tipo_complemento) AS complemento FROM productos$periodoActual p WHERE Orden_Ciclo = '" .$dataMenus['MENU']. "' ";
            $respuestaComplementos = $Link->query($consultaComplementos) or die ('Error consultado complementos Ln58');
            if ($respuestaComplementos->num_rows > 0) {
                $m = 1;
                while ($dataComplementos = $respuestaComplementos->fetch_assoc()) { // aca iteramos en cada complemento que tenga menus de ese ciclo
                    /*************** DESDE VAMOS A BUSCAR LAS MINUTAS DEL DÍA ******************/
                    $minutaDia=[];
                    for ($i=1; $i <=$_SESSION['cant_gruposEtarios'] ; $i++) { 
                        $consultaEncabezado = " SELECT det.codigo, det.Cantidad 
                                                    FROM fichatecnicadet det 
                                                    INNER JOIN fichatecnica enc ON enc.id = det.idft
                                                    INNER JOIN productos$year p ON p.codigo = enc.codigo
                                                    WHERE p.Codigo LIKE '01%' AND Cod_Tipo_complemento = '" .$dataComplementos['complemento']. "' 
                                                            AND Orden_Ciclo = $menuDia  AND Cod_Grupo_Etario = $i ";
                        $respuestaEncabezado = $Link->query($consultaEncabezado) or die ('Error consultando alimentos Ln 89');
                        if ($respuestaEncabezado->num_rows > 0) { // obtenemos las preparaciones activas
                            $minutaDiaGrupo = [];
                            while ($dataEncabezado = $respuestaEncabezado->fetch_assoc()) {
                                $codigoPreparacion = $dataEncabezado['codigo'];
                                $minutaDiaGrupo[$codigoPreparacion] =  search_products($Link, $codigoPreparacion, $alimento);               
                            } // while preparaciones
                            $minutaDia[$i] = $minutaDiaGrupo; 
                        } // respuesta preparaciones
                    }// for grupos etarios
                    // exit(var_dump($minutaDia));
                    /********************* TERMINAMOS DE BUSCAR LAS MINUTAS ***********************************/

                    $consultaRemisiones = " SHOW TABLES LIKE 'despachos_enc%' ";
                    $respuestaRemisiones = $Link->query($consultaRemisiones) or die ('Error ln 14');
                    if ($respuestaRemisiones->num_rows > 0) {
                        while ($dataRemisiones = $respuestaRemisiones->fetch_assoc()) {
                            $aux = array_values($dataRemisiones);
                            $aux=substr(($aux[0]), 13);
                            $consultaDespachos = " SELECT   p.BodegaOrigen, 
                                                            p.BodegaDestino, 
                                                            d.Tipo_Complem, 
                                                            d.Cobertura_G1, 
                                                            d.Cobertura_G2, 
                                                            d.Cobertura_G3, 
                                                            d.Cobertura_G4, 
                                                            d.Cobertura_G5, 
                                                            d.FechaHora_Elab
                                                        FROM productosmov$aux p
                                                        INNER JOIN despachos_enc$aux d ON p.Numero = d.Num_Doc
                                                        WHERE d.estado = 1 AND Dias LIKE '%$day%' 
                                                            AND d.Tipo_Complem = '" .$dataComplementos['complemento']. "'
                                                            AND d.SEMANA LIKE '%$week%' AND d.cod_Sede = '$bodega' ";                                   
                            $respuestaDespachos = $Link->query($consultaDespachos) or die ('Error al consultar los despachos ln 28');
                            while ($dataDespachos = $respuestaDespachos->fetch_assoc()) {  // desde aca iteramos en los despachos creados en ese día en ese complemento
                                $fechaElaboracionDespacho = $dataDespachos['FechaHora_Elab'];
                                $complementoDespacho = $dataDespachos['Tipo_Complem'];
        
                                /************** PRIMERO BUSCAMOS SI ESTE DESPACHO EN CURSO TIENE ALGUN INTERCAMBIO ****************/ 
                                for ($i=1; $i <=$_SESSION['cant_gruposEtarios'] ; $i++) { 
                                    $minutaIntercambio = $minutaDia[$i];
                                    $consultaIntercambio = " SELECT id, 
                                                                    tipo_intercambio, 
                                                                    variacion_menu,
                                                                    menu,
                                                                    cod_grupo_etario,
                                                                    cod_producto,
                                                                    estado
                                                                FROM novedades_menu         
                                                                WHERE fecha_registro < '$fechaElaboracionDespacho'
                                                                    AND (fecha_reversion > '$fechaElaboracionDespacho' OR estado = 1) 
                                                                    AND tipo_complem = '$complementoDespacho' 
                                                                    AND semana = '$week'
                                                                    AND cod_grupo_etario = '$i' "; 
                                    $respuestaIntercambio = $Link->query($consultaIntercambio) or die ('Error al consultar los intercambios Ln 90');                                
                                    if ($respuestaIntercambio->num_rows > 0) {
                                        while ($dataIntercambio = $respuestaIntercambio->fetch_assoc()) {
                                            if ($dataIntercambio['tipo_intercambio'] == 1 && $dataIntercambio['menu'] == $menuDia ) { // entramos cuando el intercambio es por alimento 
                                                $codigoPreparacion = $dataIntercambio['cod_producto']; 
                                                $consultaIntercambioDet = " SELECT '$codigoPreparacion',
                                                                                    cod_producto, 
                                                                                    pesobruto 
                                                                                FROM  novedades_menudet
                                                                                WHERE tipo = 1 AND id_novedad = " .$dataIntercambio['id']. "  AND cod_producto = '$alimento' ";
                                                $respuestaIntercambioDet = $Link->query($consultaIntercambioDet) or die ('Error al consultar los intercambios Ln 103');
                                                if ($respuestaIntercambioDet->num_rows > 0) {
                                                    if (isset($minutaIntercambio[$codigoPreparacion])) { // eliminamos los datos de la anterior preparacion
                                                        unset($minutaIntercambio[$codigoPreparacion]);
                                                    }
                                                    unset($auxDetalle);
                                                    while ($dataIntercambioDet = $respuestaIntercambioDet->fetch_assoc()) {
                                                        $auxDetalle[] = [   'codigo' => $dataIntercambioDet['cod_producto'],
                                                                            'Cantidad' => $dataIntercambioDet['pesobruto'] ];              
                                                    }
                                                    $detalle[$codigoPreparacion] = $auxDetalle;                                    
                                                    $minutaIntercambio[$codigoPreparacion] = $auxDetalle;  
                                                }                             
                                            }// condicion cuando el intercambio es por alimento
                                            if ($dataIntercambio['tipo_intercambio'] == 2 && $dataIntercambio['menu'] == $menuDia) {
                                                $codigoMenu = $dataIntercambio['cod_producto'];
                                                $consultaIntercambioDet = " SELECT '$codigoMenu',
                                                                                    cod_producto, 
                                                                                    pesobruto,
                                                                                    tipo
                                                                                FROM  novedades_menudet
                                                                                WHERE tipo = 1 AND id_novedad = " .$dataIntercambio['id']. " ORDER BY tipo  ";
                                                $respuestaIntercambioDet = $Link->query($consultaIntercambioDet) or die ('Error al consultar los intercambios Ln 103');
                                                if ($respuestaIntercambioDet->num_rows > 0) {
                                                    unset($minutaIntercambio);
                                                    while ($dataIntercambioDet = $respuestaIntercambioDet->fetch_assoc()) {
                                                        $codigoPreparacion = $dataIntercambioDet['cod_producto'];
                                                        $minutaIntercambio[$codigoPreparacion] =  search_products($Link, $codigoPreparacion, $alimento);  
                                                    }
                                                }   
                                            } // condicion cuando el intercambio es por preparacion
                                            if ($dataIntercambio['tipo_intercambio'] == 3) { // entra cuando el intercambio es por día
                                                $consultaIntercambioDet = " SELECT  cod_producto,  
                                                                                    orden_ciclo
                                                                                FROM  novedades_menudet
                                                                                WHERE tipo = 1 AND id_novedad = " .$dataIntercambio['id']. "
                                                                                    AND orden_ciclo = " .$menuDia. "
                                                                                ORDER BY tipo  "; 
                                                $respuestaCambioDet = $Link->query($consultaIntercambioDet) or die('Error al consultar los intercambios');
                                                if ($respuestaCambioDet->num_rows > 0) {
                                                    $dataCambioDet = $respuestaCambioDet->fetch_assoc();
                                                    $consultaEncabezado = " SELECT det.codigo, det.Cantidad 
                                                                                FROM fichatecnicadet det 
                                                                                INNER JOIN fichatecnica enc ON enc.id = det.idft
                                                                                INNER JOIN productos$year p ON p.codigo = enc.codigo
                                                                                WHERE p.Codigo = '" .$dataCambioDet['cod_producto']. "'";
                                                    $respuestaEncabezado = $Link->query($consultaEncabezado) or die ('Error al consultar el detalle de las preparaciones 42');
                                                    if ($respuestaEncabezado->num_rows > 0) { // obtenemos las preparaciones activas
                                                        $minutaDiaGrupo = [];
                                                        while ($dataEncabezado = $respuestaEncabezado->fetch_assoc()) {
                                                            $codigoPreparacion = $dataEncabezado['codigo'];
                                                            $minutaIntercambio[$codigoPreparacion] =  search_products($Link, $codigoPreparacion, $alimento);               
                                                        } // while preparaciones
                                                    } // respuesta preparaciones    
                                                }  
                                            }
                                        } // iteracion de los intercambios
                                        $minutaDia[$i] = $minutaIntercambio; 
                                    } // existe intercambios en este despachos
                                    // $minutaDefinitiva[$i] = $minutaIntercambio;
                                } // iteracion de cada grupo etario 
                                /*************** EN ESTE PUNTO YA TENEMOS LAS MINUTAS CON INTERCAMBIOS ******************/
                                for ($i=1; $i <=$_SESSION['cant_gruposEtarios'] ; $i++) { // recorremos todos los grupos etarios
                                    $menuAux = $minutaDia[$i];
                                    $coberturaAux = $dataDespachos['Cobertura_G'.$i];
                                    foreach ($menuAux as $keyP => $valueP) { // recorremos todas las preparacines del menu[grupo]
                                        foreach ($valueP as $key => $value) {
                                            $aux = ($value['Cantidad'] * $coberturaAux) * $factores[$value['codigo']];
                                            if ($_SESSION['p_inventory'] == 2) {
                                                isset($resta[$dataDespachos['Tipo_Complem']]) ? $resta[$dataDespachos['Tipo_Complem']] += $aux : $resta[$dataDespachos['Tipo_Complem']] = $aux;
                                            }else{
                                                isset($resta['total']) ? $resta['total'] += $aux : $resta['total'] = $aux;
                                            }
                                        }
                                    }    
                                }
                                $m++;
                            }  // iteracion de cada despacho mensual
                        }  // iteracion de cada despacho  
                    }
                } // iteracion complementos
            }
        } // iteracion días
    }
    return $resta;
}
/******************* BODEGAS SECUNDARIAS *********************/

/******************* BODEGAS PRINCIPALES ****************************/
function sum_ord($Link, $bodega, $alimento){
    if ($_SESSION['p_inventory'] == 2) { $suma[] = 0; }
    if ($_SESSION['p_inventory'] == 1) { $suma['total'] = 0; }
    $consultaOrdMeses = " SHOW TABLES LIKE 'orden_compra_enc%' ";
    $respuestaOrdMeses = $Link->query($consultaOrdMeses) or die ('Error ln 298');
    if ($respuestaOrdMeses->num_rows > 0) {  // buscamos las tablas de ordenes mensuales que estan creadas
        while ($dataOrdMeses = $respuestaOrdMeses->fetch_assoc()) { // iteramos en las ordenes de compra mensuales para buscar la sumatoria
            $aux = array_values($dataOrdMeses);
            $aux=substr(($aux[0]), 16); // tabla
            $consultaCompras = " SELECT SUM(Cantidad) AS total,
                                        enc.Tipo_Complem
                                    FROM orden_compra_enc$aux  enc
                                    INNER JOIN orden_compra_det$aux det ON enc.Num_Doc = det.Num_Doc
                                    WHERE enc.bodega = '$bodega' AND det.cod_Alimento = '$alimento' AND enc.estado = 1
                                    GROUP BY enc.Num_OCO "; 
            if ($_SESSION['p_inventory'] == 2) { // agrupamos en caso de que el inventario sea por bodega y complemento
                $consultaCompras .= " , enc.Tipo_Complem ";
            }              
            $respuestaCompras = $Link->query($consultaCompras) or die('Error consultando compras Ln67');
            if ($respuestaCompras->num_rows > 0) { 
                while ($dataCompras = $respuestaCompras->fetch_assoc()) {
                    if ($_SESSION['p_inventory'] == 2) { // sumamos en caso que el inventario sea por bodega y complemento
                        isset($suma[$dataCompras['Tipo_Complem']]) ? $suma[$dataCompras['Tipo_Complem']] += $dataCompras['total'] : $suma[$dataCompras['Tipo_Complem']] = $dataCompras['total'];
                    }else{
                        isset($suma['total']) ? $suma['total'] += $dataCompras['total'] : $suma['total'] = $dataCompras['total'];
                    }
                }
            }                                      
        }
    }else{
        $suma['total'] = 0;
    } 
    return $suma; 
}

function res_remision($Link, $bodega, $alimento){
    $resta = [];
    $consultaRemisionesEnc = " SHOW TABLES LIKE 'despachos_enc%' ";
    $respuestaRemisionesEnc = $Link->query($consultaRemisionesEnc) or die ('Error ln 73');
    if ($respuestaRemisionesEnc->num_rows > 0) {
        while ($dataRemisionesEnc = $respuestaRemisionesEnc->fetch_assoc()) {
            $aux = array_values($dataRemisionesEnc);
            $aux=substr(($aux[0]), 13);
            $consultaRemisiones = " SELECT  SUM(Cantidad) AS total
                                        FROM productosmov$aux  enc
                                        INNER JOIN productosmovdet$aux det ON enc.Numero = det.Numero
                                        INNER JOIN despachos_enc$aux encDes ON encDes.Num_Doc = enc.Numero
                                        WHERE enc.BodegaOrigen = '$bodega' AND det.CodigoProducto = '$alimento' AND encDes.estado = 1
                                        GROUP BY enc.Numero ";
            if ($_SESSION['p_inventory'] == 2) {
                $consultaRemisiones .= " , encDes.Tipo_Complem ";
            }         
            $respuestaRemisiones = $Link->query($consultaRemisiones) or die('Error consultando remisiones Ln84');
            if ($respuestaRemisiones->num_rows > 0) {
                while ($dataRemisiones = $respuestaRemisiones->fetch_assoc()) {
                    if ($_SESSION['p_inventory'] == 2) {
                        isset($resta[$dataRemisiones['Tipo_Complem']]) ? $resta[$dataRemisiones['Tipo_Complem']] += $dataRemisiones['total'] : $resta[$dataRemisiones['Tipo_Complem']] = $dataRemisiones['total'];
                    }else{
                        isset($resta['total']) ? $resta['total'] += $dataRemisiones['total'] : $resta['total'] = $dataRemisiones['total'];
                    }
                }
            }      
        }
    }else{
        $resta['total'] = 0;
    }
    return $resta;
}
/******************* BODEGAS PRINCIPALES ****************************/

function search_descripcion($Link, $periodoActual, $alimento){
    $consultaNombreAlimento = " SELECT Descripcion, NombreUnidad2 FROM productos$periodoActual WHERE codigo = '$alimento' "; 
    $respuestaNombreAlimento = $Link->query($consultaNombreAlimento);
    if ($respuestaNombreAlimento->num_rows > 0) {
        $dataNombreAlimento = $respuestaNombreAlimento->fetch_assoc();
    }
    return $dataNombreAlimento;
}

function search_remision_warehouse_secundary($Link, $bodega, $alimento){
    /*********************** REMISIONES DE ALIMENTOS ****************************/
    $consultaRemisiones2 = " SHOW TABLES LIKE 'despachos_enc%' ";
    $remisiones = [];
    $respuestaRemisiones2 = $Link->query($consultaRemisiones2) or die ('Error ln 14');
    if ($respuestaRemisiones2->num_rows > 0) {
        while ($dataRemisiones2 = $respuestaRemisiones2->fetch_assoc()) { 
            $n=1;
            $aux = array_values($dataRemisiones2);
            $aux=substr(($aux[0]), 13);
            $consultaRemisiones = " SELECT  CONCAT(Tipo_Doc,'-',Num_Doc) AS Num_Doc, 
                                            FechaHora_Elab,
                                            SUM(Cantidad) AS total
                                        FROM productosmov$aux  enc
                                        INNER JOIN productosmovdet$aux det ON enc.Numero = det.Numero
                                        INNER JOIN despachos_enc$aux encDes ON encDes.Num_Doc = enc.Numero
                                        WHERE enc.BodegaDestino = '$bodega' AND det.CodigoProducto = '$alimento' AND encDes.estado = 1
                                        GROUP BY enc.Numero
                                        ORDER BY encDes.FechaHora_Elab "; 
            $respuestaRemisiones = $Link->query($consultaRemisiones) or die('Error consultando remisiones Ln58');
            if ($respuestaRemisiones->num_rows > 0) {
                while ($dataRemisiones = $respuestaRemisiones->fetch_assoc()) {
                    $auxRemisiones['Numero'] = $dataRemisiones['Num_Doc'];
                    $auxRemisiones['total'] = $dataRemisiones['total'];
                    $auxRemisiones['tipo'] = 'e';
                    $remisiones[$dataRemisiones['FechaHora_Elab'].'-'.$n] = $auxRemisiones;
                    $n++;
                }
                ksort($remisiones);
            }      
        }
    }
    return $remisiones;
    /*********************** REMISIONES DE ALIMENTOS ****************************/
}

function search_minuta_dayli($Link, $bodega, $alimento, $sinc, $periodoActual, $factores){
    /************************ MENUS DIARIOS *************************************/
    $minutas = [];
    $consultaMenus = " SELECT ANO, MES, DIA, SEMANA, MENU FROM planilla_semanas WHERE CONCAT(ANO, '-', MES, '-', DIA) <= '$sinc' "; 
    $respuestaMenus = $Link->query($consultaMenus) or die ('Error consultado los menus Ln 54');
    if ($respuestaMenus->num_rows > 0) {
        while ($dataMenus = $respuestaMenus->fetch_assoc()) { // primero vamos a iterar en los menus díarios que estan ejecutados
            $menuDia = $dataMenus['MENU'];
            $year = substr($dataMenus['ANO'],2,2);
            $bigYear = $dataMenus['ANO'];
            $day = $dataMenus['DIA'];
            $week = $dataMenus['SEMANA'];
            $month = $dataMenus['MES'];
            $consultaComplementos = " SELECT DISTINCT(Cod_Tipo_complemento) AS complemento FROM productos$periodoActual p WHERE Orden_Ciclo = '" .$dataMenus['MENU']. "' ";
            $respuestaComplementos = $Link->query($consultaComplementos) or die ('Error consultado complementos Ln58');
            if ($respuestaComplementos->num_rows > 0) {
                $m = 1;
                while ($dataComplementos = $respuestaComplementos->fetch_assoc()) { // aca iteramos en cada complemento que tenga menus de ese ciclo
                    /*************** DESDE VAMOS A BUSCAR LAS MINUTAS DEL DÍA ******************/
                    $minutaDia=[];
                    for ($i=1; $i <=$_SESSION['cant_gruposEtarios'] ; $i++) { 
                        $consultaEncabezado = " SELECT det.codigo, det.Cantidad 
                                                    FROM fichatecnicadet det 
                                                    INNER JOIN fichatecnica enc ON enc.id = det.idft
                                                    INNER JOIN productos$year p ON p.codigo = enc.codigo
                                                    WHERE p.Codigo LIKE '01%' AND Cod_Tipo_complemento = '" .$dataComplementos['complemento']. "' 
                                                            AND Orden_Ciclo = $menuDia  AND Cod_Grupo_Etario = $i ";
                        $respuestaEncabezado = $Link->query($consultaEncabezado) or die ('Error consultando alimentos Ln 89');
                        if ($respuestaEncabezado->num_rows > 0) { // obtenemos las preparaciones activas
                            $minutaDiaGrupo = [];
                            while ($dataEncabezado = $respuestaEncabezado->fetch_assoc()) {
                                $codigoPreparacion = $dataEncabezado['codigo'];
                                $minutaDiaGrupo[$codigoPreparacion] =  search_products($Link, $codigoPreparacion, $alimento);               
                            } // while preparaciones
                            $minutaDia[$i] = $minutaDiaGrupo; 
                        } // respuesta preparaciones
                    }// for grupos etarios
                    /********************* TERMINAMOS DE BUSCAR LAS MINUTAS ***********************************/

                    $consultaRemisiones = " SHOW TABLES LIKE 'despachos_enc%' ";
                    $respuestaRemisiones = $Link->query($consultaRemisiones) or die ('Error ln 14');
                    if ($respuestaRemisiones->num_rows > 0) {
                        while ($dataRemisiones = $respuestaRemisiones->fetch_assoc()) {
                            $aux = array_values($dataRemisiones);
                            $aux=substr(($aux[0]), 13);
                            $consultaDespachos = " SELECT   p.BodegaOrigen, 
                                                            p.BodegaDestino, 
                                                            d.Tipo_Complem, 
                                                            d.Cobertura_G1, 
                                                            d.Cobertura_G2, 
                                                            d.Cobertura_G3, 
                                                            d.Cobertura_G4, 
                                                            d.Cobertura_G5, 
                                                            d.FechaHora_Elab
                                                        FROM productosmov$aux p
                                                        INNER JOIN despachos_enc$aux d ON p.Numero = d.Num_Doc
                                                        WHERE d.estado = 1 AND Dias LIKE '%$day%' 
                                                            AND d.Tipo_Complem = '" .$dataComplementos['complemento']. "'
                                                            AND d.SEMANA LIKE '%$week%' AND d.cod_Sede = '$bodega' ";                                   
                            $respuestaDespachos = $Link->query($consultaDespachos) or die ('Error al consultar los despachos ln 28');
                            while ($dataDespachos = $respuestaDespachos->fetch_assoc()) {  // desde aca iteramos en los despachos creados en ese día en ese complemento
                                $fechaElaboracionDespacho = $dataDespachos['FechaHora_Elab'];
                                $complementoDespacho = $dataDespachos['Tipo_Complem'];
        
                                /************** PRIMERO BUSCAMOS SI ESTE DESPACHO EN CURSO TIENE ALGUN INTERCAMBIO ****************/ 
                                for ($i=1; $i <=$_SESSION['cant_gruposEtarios'] ; $i++) { 
                                    $minutaIntercambio = $minutaDia[$i];
                                    $consultaIntercambio = " SELECT id, 
                                                                    tipo_intercambio, 
                                                                    variacion_menu,
                                                                    menu,
                                                                    cod_grupo_etario,
                                                                    cod_producto,
                                                                    estado
                                                                FROM novedades_menu         
                                                                WHERE fecha_registro < '$fechaElaboracionDespacho'
                                                                    AND (fecha_reversion > '$fechaElaboracionDespacho' OR estado = 1) 
                                                                    AND tipo_complem = '$complementoDespacho' 
                                                                    AND semana = '$week'
                                                                    AND cod_grupo_etario = '$i' "; 
                                    $respuestaIntercambio = $Link->query($consultaIntercambio) or die ('Error al consultar los intercambios Ln 90');                                
                                    if ($respuestaIntercambio->num_rows > 0) {
                                        while ($dataIntercambio = $respuestaIntercambio->fetch_assoc()) {
                                            if ($dataIntercambio['tipo_intercambio'] == 1 && $dataIntercambio['menu'] == $menuDia ) { // entramos cuando el intercambio es por alimento 
                                                $codigoPreparacion = $dataIntercambio['cod_producto']; 
                                                $consultaIntercambioDet = " SELECT '$codigoPreparacion',
                                                                                    cod_producto, 
                                                                                    pesobruto 
                                                                                FROM  novedades_menudet
                                                                                WHERE tipo = 1 AND id_novedad = " .$dataIntercambio['id']. "  AND cod_producto = '$alimento' ";
                                                $respuestaIntercambioDet = $Link->query($consultaIntercambioDet) or die ('Error al consultar los intercambios Ln 103');
                                                if ($respuestaIntercambioDet->num_rows > 0) {
                                                    if (isset($minutaIntercambio[$codigoPreparacion])) { // eliminamos los datos de la anterior preparacion
                                                        unset($minutaIntercambio[$codigoPreparacion]);
                                                    }
                                                    unset($auxDetalle);
                                                    while ($dataIntercambioDet = $respuestaIntercambioDet->fetch_assoc()) {
                                                        $auxDetalle[] = [   'codigo' => $dataIntercambioDet['cod_producto'],
                                                                            'Cantidad' => $dataIntercambioDet['pesobruto'] ];              
                                                    }
                                                    $detalle[$codigoPreparacion] = $auxDetalle;                                    
                                                    $minutaIntercambio[$codigoPreparacion] = $auxDetalle;  
                                                }                             
                                            }// condicion cuando el intercambio es por alimento
                                            if ($dataIntercambio['tipo_intercambio'] == 2 && $dataIntercambio['menu'] == $menuDia) {
                                                $codigoMenu = $dataIntercambio['cod_producto'];
                                                $consultaIntercambioDet = " SELECT '$codigoMenu',
                                                                                    cod_producto, 
                                                                                    pesobruto,
                                                                                    tipo
                                                                                FROM  novedades_menudet
                                                                                WHERE tipo = 1 AND id_novedad = " .$dataIntercambio['id']. " ORDER BY tipo  ";
                                                $respuestaIntercambioDet = $Link->query($consultaIntercambioDet) or die ('Error al consultar los intercambios Ln 103');
                                                if ($respuestaIntercambioDet->num_rows > 0) {
                                                    unset($minutaIntercambio);
                                                    while ($dataIntercambioDet = $respuestaIntercambioDet->fetch_assoc()) {
                                                        $codigoPreparacion = $dataIntercambioDet['cod_producto'];
                                                        $minutaIntercambio[$codigoPreparacion] =  search_products($Link, $codigoPreparacion, $alimento);  
                                                    }
                                                }   
                                            } // condicion cuando el intercambio es por preparacion
                                            if ($dataIntercambio['tipo_intercambio'] == 3) { // entra cuando el intercambio es por día
                                                $consultaIntercambioDet = " SELECT  cod_producto,  
                                                                                    orden_ciclo
                                                                                FROM  novedades_menudet
                                                                                WHERE tipo = 1 AND id_novedad = " .$dataIntercambio['id']. "
                                                                                    AND orden_ciclo = " .$menuDia. "
                                                                                ORDER BY tipo  "; 
                                                $respuestaCambioDet = $Link->query($consultaIntercambioDet) or die('Error al consultar los intercambios');
                                                if ($respuestaCambioDet->num_rows > 0) {
                                                    $dataCambioDet = $respuestaCambioDet->fetch_assoc();
                                                    $consultaEncabezado = " SELECT det.codigo, det.Cantidad 
                                                                                FROM fichatecnicadet det 
                                                                                INNER JOIN fichatecnica enc ON enc.id = det.idft
                                                                                INNER JOIN productos$year p ON p.codigo = enc.codigo
                                                                                WHERE p.Codigo = '" .$dataCambioDet['cod_producto']. "'";
                                                    $respuestaEncabezado = $Link->query($consultaEncabezado) or die ('Error al consultar el detalle de las preparaciones 42');
                                                    if ($respuestaEncabezado->num_rows > 0) { // obtenemos las preparaciones activas
                                                        $minutaDiaGrupo = [];
                                                        while ($dataEncabezado = $respuestaEncabezado->fetch_assoc()) {
                                                            $codigoPreparacion = $dataEncabezado['codigo'];
                                                            $minutaIntercambio[$codigoPreparacion] =  search_products($Link, $codigoPreparacion, $alimento);               
                                                        } // while preparaciones
                                                    } // respuesta preparaciones    
                                                }  
                                            }
                                        } // iteracion de los intercambios
                                        $minutaDia[$i] = $minutaIntercambio; 
                                    } // existe intercambios en este despachos
                                    // $minutaDefinitiva[$i] = $minutaIntercambio;
                                } // iteracion de cada grupo etario 
                                /*************** EN ESTE PUNTO YA TENEMOS LAS MINUTAS CON INTERCAMBIOS ******************/
                                $cantidades=[];
                                for ($i=1; $i <=$_SESSION['cant_gruposEtarios'] ; $i++) { // recorremos todos los grupos etarios
                                    $menuAux = $minutaDia[$i];
                                    $coberturaAux = $dataDespachos['Cobertura_G'.$i];
                                    foreach ($menuAux as $keyP => $valueP) { // recorremos todas las preparacines del menu[grupo]
                                        foreach ($valueP as $key => $value) {
                                            $aux = ($value['Cantidad'] * $coberturaAux) * $factores[$value['codigo']];
                                            if (isset($cantidades[$value['codigo']])) {
                                                $cantidades[$value['codigo']] += $aux;
                                            }else{
                                                $cantidades[$value['codigo']] = $aux;
                                            }
                                        }
                                    }    
                                }
                                $auxMinuta['Numero'] = "Menu-".$menuDia.' - '. $dataComplementos['complemento'];
                                $auxMinuta['total'] = isset($cantidades[$alimento]) ? $cantidades[$alimento] : 0;
                                $auxMinuta['tipo'] = 's';
                                if ($auxMinuta['total'] > 0) {
                                    $minutas[$bigYear."-".$month."-".$day."-".$m] = $auxMinuta;
                                    $m++;
                                }
                            }  // iteracion de cada despacho mensual
                        }  // iteracion de cada despacho  
                    }
                } // iteracion complementos
            }
        } // iteracion días
    }
    return $minutas;
    /************************ MENUS DIARIOS *************************************/
}

function search_ord_warehouse_principaly($Link, $bodega, $alimento){
    /*********************** ORDENES DE COMPRA ****************************/
    $compras = [];
    $consultaOrdMeses = " SHOW TABLES LIKE 'orden_compra_enc%' ";
    $respuestaOrdMeses = $Link->query($consultaOrdMeses) or die ('Error ln 14');
    if ($respuestaOrdMeses) {  // si encontramos resultados
        while ($dataOrdMeses = $respuestaOrdMeses->fetch_assoc()) {
            $n = 1;
            $aux = array_values($dataOrdMeses);
            $aux=substr(($aux[0]), 16);
            $consultaCompras = " SELECT CONCAT(enc.Tipo_Doc, '-', enc.Num_OCO) AS Num_OCO, 
                                            FechaHora_Elab,
                                            SUM(Cantidad) AS total
                                        FROM orden_compra_enc$aux  enc
                                        INNER JOIN orden_compra_det$aux det ON enc.Num_Doc = det.Num_Doc
                                        WHERE enc.bodega = '$bodega' AND det.cod_Alimento = '$alimento' AND enc.estado = 1
                                        GROUP BY enc.Num_OCO 
                                        ORDER BY enc.FechaHora_Elab "; 
            $respuestaCompras = $Link->query($consultaCompras) or die('Error consultando compras Ln26');
            if ($respuestaCompras->num_rows > 0) {
                while ($dataCompras = $respuestaCompras->fetch_assoc()) {
                    $auxCompra['Numero'] = $dataCompras['Num_OCO'];
                    $auxCompra['total'] = $dataCompras['total'];
                    $auxCompra['tipo'] = 'e';
                    $compras[$dataCompras['FechaHora_Elab'].'-'.$n] = $auxCompra;
                    $n++;  
                }
                ksort($compras);
            }                                        
        }
    }
    return $compras;
    /*********************** ORDENES DE COMPRA ****************************/
}

function search_remision_warehouse_principaly($Link, $bodega, $alimento){
    /*********************** REMISIONES DE ALIMENTOS ****************************/
    $remisiones = [];
    $consultaRemisionesEnc = " SHOW TABLES LIKE 'despachos_enc%' ";
    $respuestaRemisionesEnc = $Link->query($consultaRemisionesEnc) or die ('Error ln 14');
    if ($respuestaRemisionesEnc->num_rows > 0) {
        while ($dataRemisionesEnc = $respuestaRemisionesEnc->fetch_assoc()) {
            $n=1;
            $aux = array_values($dataRemisionesEnc);
            $aux=substr(($aux[0]), 13);
            $consultaRemisiones = " SELECT  CONCAT(Tipo_Doc,'-',Num_Doc) AS Num_Doc, 
                                            enc.fecha_envio,
                                            SUM(Cantidad) AS total
                                        FROM productosmov$aux  enc
                                        INNER JOIN productosmovdet$aux det ON enc.Numero = det.Numero
                                        INNER JOIN despachos_enc$aux encDes ON encDes.Num_Doc = enc.Numero
                                        WHERE enc.BodegaOrigen = '$bodega' AND det.CodigoProducto = '$alimento' AND encDes.estado = 1
                                        GROUP BY enc.Numero
                                        ORDER BY enc.fecha_envio ";
            $respuestaRemisiones = $Link->query($consultaRemisiones) or die('Error consultando remisiones Ln58'.$consultaRemisiones);
            if ($respuestaRemisiones->num_rows > 0) {
                while ($dataRemisiones = $respuestaRemisiones->fetch_assoc()) {
                    $auxRemisiones['Numero'] = $dataRemisiones['Num_Doc'];
                    $auxRemisiones['total'] = $dataRemisiones['total'];
                    $auxRemisiones['tipo'] = 's';
                    $remisiones[$dataRemisiones['fecha_envio'].'-'.$n] = $auxRemisiones;
                    $n++;
                }
                ksort($remisiones);
            }      
        }
    }
    return $remisiones;
    /*********************** REMISIONES DE ALIMENTOS ****************************/
}
