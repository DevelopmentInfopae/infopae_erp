<?php
set_time_limit (0);
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require_once 'model/inventory_model.php';


if ($_SESSION['p_inventory'] != 0) { // aca entramos solo cuando el inventario este activo
    $bigYear = date('Y');
    $year = date("y");
    $month = $_POST['mes'];
    $week = $_POST['semana'];
    $day = $_POST['dia'];
    $complemento = $_POST['complemento'];
    $bodega = $_POST['bodega'];
    $actualizado = false;
    $periodoActual = $_SESSION['periodoActual'];

    /******* validacion inicial de fechas pendientes por sincronizar *******/ 
    $fechaSincronizacionForm = $bigYear."-".$month."-".$day;
    $consultaValidacion = "SELECT synchronization_date, bodega
                            FROM inventarios_bodegas_enc inc
                            INNER JOIN sedes$periodoActual s ON s.cod_sede = inc.bodega
                            WHERE synchronization_date < '$fechaSincronizacionForm' 
                                AND  synchronization_date > '0000-00-00' ";
    if ($bodega != '') {
        $consultaValidacion .= " AND bodega = '$bodega' ";
    }
    if ($_SESSION['p_inventory'] == 2) {
        $consultaValidacion .= " AND complemento = '$complemento' ";
    }                    
    $respuestaValidacion = $Link->query($consultaValidacion) or die ('Error validando');
    if ($respuestaValidacion->num_rows > 0) {
        $dataValidacion = $respuestaValidacion->fetch_object();
        $datos = $Link->query(" SELECT u.Ciudad, s.nom_sede FROM sedes$periodoActual s INNER JOIN ubicacion u ON u.codigoDANE = s.cod_mun_sede WHERE s.cod_sede = '" .$dataValidacion->bodega. "'");
        if ($datos->num_rows > 0) {
            $datosM = $datos->fetch_object();
            $resultadoAJAX = [
                "estado" => 0,
                "mensaje" => "  La bodega <strong>" .$datosM->nom_sede. "</strong> de <strong>" .$datosM->Ciudad. "</strong> 
                                tiene fechas pendientes por sincronizar antes de <strong>" .$fechaSincronizacionForm. "</strong> " 
            ];
            echo json_encode($resultadoAJAX);
            exit; 
        }
        // break;
    }

    $conMesSemanaDespacho = " SELECT ANO, MES_DESPACHO, SEMANA_DESPACHO, MENU
                                FROM planilla_semanas 
                                WHERE ANO = '$bigYear' AND MES = '$month' AND DIA = '$day'  " ; 
    $respuestaMesSemanaDespacho = $Link->query($conMesSemanaDespacho) or die ('Error al consultar el mes y semana despachado Ln16');
    if ($respuestaMesSemanaDespacho->num_rows > 0) { // aca entramos cuando encontramos el menu del día buscado
        $dataMesSemanaDespacho = $respuestaMesSemanaDespacho->fetch_assoc();
        $tabla = $dataMesSemanaDespacho['MES_DESPACHO'].substr($dataMesSemanaDespacho['ANO'],2,2);
        $semanaDespacho = $dataMesSemanaDespacho['SEMANA_DESPACHO']; // variable donde almacenamos la semana para buscar los despachos
        $menuDia = $dataMesSemanaDespacho['MENU']; // variable donde almacenamos el menu del día buscado

        $consultaComplementos = " SELECT CODIGO FROM tipo_complemento WHERE ValorRacion > 0 ";
        if ($complemento != '') {
            $consultaComplementos .= " AND CODIGO = '$complemento' ";
        }
        $respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complementos ln 24');
        if ($respuestaComplementos->num_rows > 0) {  // vamos a buscar los complementos activos

            $factores = search_factors($Link, $year);

            /**************** SEDES *********************************/
            $consultaSedes = " SELECT cod_sede, nom_sede 
                                    FROM sedes$year ";
            $respuestaSedes = $Link->query($consultaSedes);
            if ($respuestaSedes->num_rows > 0) {
                while ($dataSedes = $respuestaSedes->fetch_assoc()) {
                    $sedes[$dataSedes['cod_sede']] = $dataSedes['nom_sede'];
                }
            }
            /**************** SEDES *********************************/   

            /**************** FECHAS *********************************/   
            $consultaFechas = " SELECT CONCAT(ANO,'-',MES_REAL,'-',DIA) AS fecha, CONSECUTIVO FROM planilla_semanas ORDER BY CONSECUTIVO ";
            $respuestaFechas = $Link->query($consultaFechas);
            if ($respuestaFechas->num_rows > 0) {
                while ($dataFechas = $respuestaFechas->fetch_assoc()) {
                    $fechas[$dataFechas['fecha']] = $dataFechas['CONSECUTIVO'];
                }
            }
            /**************** FECHAS *********************************/   
            
            /**************** MESES NOMBRES *********************************/   
            $mesesNombres = [   "01" => "ENERO", 
                                "02" => "FEBRERO",
                                "03" => "MARZO",
                                "04" => "ABRIL",
                                "05" => "MAYO",
                                "06" => "JUNIO",
                                "07" => "JULIO",
                                "08" => "AGOSTO",
                                "09" => "SEPTIEMBRE",
                                "10" => "OCTUBRE",
                                "11" => "NOVIEMBRE",
                                "12" => "DICIEMBRE"
                            ];
            /**************** MESES NOMBRES *********************************/   

            while ($dataComplementos = $respuestaComplementos->fetch_assoc()) { // primero buscamos los despachos de este complemento en este día del mes
                $consultaDespachos = " SELECT   p.BodegaOrigen, 
                                                p.BodegaDestino, 
                                                d.Tipo_Complem, 
                                                d.Cobertura_G1, 
                                                d.Cobertura_G2, 
                                                d.Cobertura_G3, 
                                                d.Cobertura_G4, 
                                                d.Cobertura_G5, 
                                                d.FechaHora_Elab
                                            FROM productosmov$tabla p
                                            INNER JOIN despachos_enc$tabla d ON p.Numero = d.Num_Doc
                                            WHERE d.estado = 1 AND Dias LIKE '%$day%' 
                                                    AND d.Tipo_Complem = '" .$dataComplementos['CODIGO']. "'
                                                    AND d.SEMANA LIKE '%$semanaDespacho%' "; 
                if (isset($_POST['bodega']) && $_POST['bodega'] != '') {
                    $consultaDespachos .= " AND d.cod_Sede = '" .$bodega. "'";
                }                                 
                $respuestaDespachos = $Link->query($consultaDespachos) or die ('Error al consultar los despachos ln 28');
                if ($respuestaDespachos->num_rows > 0) { // validamos que existan despachos en el día en buscado

                    /*************** DESDE VAMOS A BUSCAR LAS MINUTAS DEL DÍA ******************/
                    $minutaDia=[];
                    for ($i=1; $i <=$_SESSION['cant_gruposEtarios'] ; $i++) { 
                        $consultaEncabezado = " SELECT det.codigo, det.Cantidad 
                                                    FROM fichatecnicadet det 
                                                    INNER JOIN fichatecnica enc ON enc.id = det.idft
                                                    INNER JOIN productos$year p ON p.codigo = enc.codigo
                                                    WHERE p.Codigo LIKE '01%' AND Cod_Tipo_complemento = '" .$dataComplementos['CODIGO']. "' 
                                                            AND Orden_Ciclo = $menuDia  AND Cod_Grupo_Etario = $i ";
                        $respuestaEncabezado = $Link->query($consultaEncabezado) or die ('Error al consultar el detalle de las preparaciones 42');
                        if ($respuestaEncabezado->num_rows > 0) { // obtenemos las preparaciones activas
                            $minutaDiaGrupo = [];
                            while ($dataEncabezado = $respuestaEncabezado->fetch_assoc()) {
                                $codigoPreparacion = $dataEncabezado['codigo'];
                                $minutaDiaGrupo[$codigoPreparacion] =  search_products($Link, $codigoPreparacion, '');               
                            } // while preparaciones
                            $minutaDia[$i] = $minutaDiaGrupo; 
                        } // respuesta preparaciones
                    }// for grupos etarios
                    /********************* TERMINAMOS DE BUSCAR LAS MINUTAS ***********************************/

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
                                                                        WHERE tipo = 1 AND id_novedad = " .$dataIntercambio['id']. "  ";
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
                                                $minutaIntercambio[$codigoPreparacion] =  search_products($Link, $codigoPreparacion, '');  
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
                                                    $minutaIntercambio[$codigoPreparacion] =  search_products($Link, $codigoPreparacion, '');               
                                                } // while preparaciones
                                            } // respuesta preparaciones    
                                        }  
                                    }
                                } // iteracion de los intercambios
                            } // existe intercambios en este despachos
                            // $minutaDefinitiva[$i] = $minutaIntercambio;
                            $minutaDia[$i] = $minutaIntercambio;
                        } // iteracion de cada grupo etario 
                        /*************** EN ESTE PUNTO YA TENEMOS LAS MINUTAS CON INTERCAMBIOS ******************/
                        // exit(var_dump(validate_date($Link, $dataDespachos['BodegaDestino'], $dataDespachos['Tipo_Complem'], $bigYear, $month, $day, $fechas, $actualizado )));
                        if (validate_date($Link, $dataDespachos['BodegaDestino'], $dataDespachos['Tipo_Complem'], $bigYear, $month, $day, $fechas, $actualizado ) === true) {
                            $cantidades=[];
                            for ($i=1; $i <=$_SESSION['cant_gruposEtarios'] ; $i++) { // recorremos todos los grupos etarios
                                $menuAux = $minutaDia[$i];
                                $coberturaAux = $dataDespachos['Cobertura_G'.$i];
                                $id_bodega = search_warehouse_id($Link, $dataDespachos['BodegaDestino'], $dataDespachos['Tipo_Complem'] ); 
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
                            foreach ($cantidades as $keyD => $valueD) {
                                $updateInventory = "UPDATE inventarios_bodegas_det 
                                                        SET cantidad = cantidad - " .$valueD. ", fecha_salida = '" .date("Y-m-d H:i:s"). "' 
                                                        WHERE id_bodega = '$id_bodega' AND codigo = '" .$keyD. "' ";
                                $respuesta = $Link->query($updateInventory);
                                if ($respuesta) {
                                    $resultadoAJAX = [
                                        "estado" => 1,
                                        "mensaje" => "Actualizacion Exitosa"
                                    ];
                                } 
                                else{
                                    $resultadoAJAX = [
                                        "estado" => 0,
                                        "mensaje" => "Error en el proceso"
                                    ];
                                }
                                $updateSincDate = $Link->query( " UPDATE inventarios_bodegas_enc SET synchronization_date = '" .($bigYear."-".$month."-".$day). "' WHERE id = '$id_bodega' " );                        
                                if ($updateSincDate) {
                                    $actualizado = true;
                                }
                            }  
                        }else if (validate_date($Link, $dataDespachos['BodegaDestino'], $dataDespachos['Tipo_Complem'], $bigYear, $month, $day, $fechas, $actualizado ) === false){
                            $resultadoAJAX = [
                                "estado" => 0,
                                "mensaje" => "La bodega <strong>" .$sedes[$dataDespachos['BodegaDestino']].  "</strong> tiene fechas sincronizadas ó pendientes por sincronizar antes del día " .$day. " del mes " .$mesesNombres[$month]. " " 
                            ];
                            echo json_encode($resultadoAJAX);
                            exit; 
                        }
                    } // iteracion de cada despacho
                } // while despachos
            } // while complementos
            if (!isset($resultadoAJAX)) {
                $resultadoAJAX = [
                    "estado" => 0,
                    "mensaje" => "No existen despachos enviados con los parámetros seleccionados " 
                ];
                echo json_encode($resultadoAJAX);
                exit; 
            }
        } // respuesta Complementos
    } // respuestaDia
} // inventario activo

echo json_encode($resultadoAJAX);
exit;


