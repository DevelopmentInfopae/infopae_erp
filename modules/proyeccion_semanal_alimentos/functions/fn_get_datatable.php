<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

function get_products_day( $Link, $complemento, $tipoAlimento, $menuDia){
    $gruposEtarios = $_SESSION['cant_gruposEtarios'];
    $periodoActual = $_SESSION['periodoActual'];
    $minutaDia = [];
    // primero iteramos en los grupos etarios
    for ($i=1; $i<=$gruposEtarios ; $i++) { 
        $codigoPreparacion = '';
        $consultaEncabezado = " SELECT det.codigo 
                                    FROM fichatecnicadet det 
                                    INNER JOIN fichatecnica enc ON enc.id = det.idft
                                    INNER JOIN productos$periodoActual p ON p.codigo = enc.codigo
                                    WHERE p.Codigo LIKE '01%' AND Cod_Tipo_complemento = '" .$complemento. "' 
                                    AND Orden_Ciclo = $menuDia  AND Cod_Grupo_Etario = $i ";
        $respuestaEncabezado = $Link->query($consultaEncabezado) or die ('Error al consultar el detalle de las preparaciones 42');
        if ($respuestaEncabezado->num_rows > 0) { // obtenemos las preparaciones activas
            $minutaDiaGrupo = [];
            while ($dataEncabezado = $respuestaEncabezado->fetch_assoc()) {
                $codigoPreparacion .= $dataEncabezado['codigo'].", ";  
            } // while preparaciones
            $minutaDiaGrupo =  search_products($Link, trim($codigoPreparacion, ', '), $tipoAlimento, $periodoActual);    
            $minutaDia[$i] = $minutaDiaGrupo; 
        } // respuesta preparaciones
    }
    return $minutaDia;
}

function search_products($Link, $codigoPreparacion, $tipoAlimento, $periodoActual){ // buscamos los alimentos de las preparaciones

    $minutaDiaGrupo = [];
    $consultaDetalle = " SELECT det.codigo, SUM(det.Cantidad) AS Cantidad, det.componente
                            FROM fichatecnicadet det 
                            INNER JOIN fichatecnica enc ON enc.id = det.idft
                            INNER JOIN productos$periodoActual p ON p.codigo = det.codigo
                            WHERE enc.codigo in ($codigoPreparacion) ";
    if ($tipoAlimento != '99') {
        $consultaDetalle .= " AND p.TipoDespacho = '$tipoAlimento' ";
    }
    $consultaDetalle .= "   GROUP BY det.codigo
                            ORDER BY det.componente ";  
                                  
    $respuestaDetalle = $Link->query($consultaDetalle) or die ('Error al consultar el detalle de los alimentos Ln 115') ;
    if ($respuestaDetalle->num_rows > 0) {
        while ($dataDetalle = $respuestaDetalle->fetch_object()) {
            $minutaDiaGrupo[] = $dataDetalle; 
        } // while alimentos
    } // respuesta detalle alimentos  
    return $minutaDiaGrupo;
}

$mes = $_POST['datos']['mes'];
$semana = $_POST['datos']['semana'];
$dias = $_POST['datos']['dias'];
$complementos = $_POST['datos']['complementos'];
$tipoAlimento = $_POST['datos']['tipoAlimento'];

$municipio = $_POST['datos']['municipio'];
$ruta = $_POST['datos']['ruta'];
$sector = $_POST['datos']['sector'];
$institucion = $_POST['datos']['institucion'];
$sede = $_POST['datos']['sede'];

$gruposEtarios = $_SESSION['cant_gruposEtarios'];
$periodoActual = $_SESSION['periodoActual'];
$coberturas = [];
$menuDia = '';
$dataSede = [];
$datosE = [];
// exit(var_dump($gruposEtarios));
/***** MANEJO DE UNIDADES DE MEDIDA *****/
$consultaUndMedida = " SELECT   p.codigo, 
                                p.NombreUnidad2, 
                                p.CantidadUnd1, 
                                (SELECT Descripcion FROM tipo_despacho WHERE id = p.TipoDespacho) AS tipoDespacho 
                            FROM productos$periodoActual p WHERE TipodeProducto = 'Alimento' ";
$respuestaUndMedida = $Link->query($consultaUndMedida) or die ('Err consultado las und de medida');
if ($respuestaUndMedida->num_rows > 0) {
    while ($dataUndMedida = $respuestaUndMedida->fetch_object()) {
        $undMedidas[$dataUndMedida->codigo] = $dataUndMedida;
    }
}

foreach ($complementos as $keyC => $valueC) { // iteracion complementos
    $coberturaNovedadAcumulado = [];
    if(isset($codigos)){
        unset($codigos);
    }
    $etarios = '';
    for ($i=1; $i <= $gruposEtarios ; $i++) { 
        $etarios .= "Etario".$i."_".$valueC.", ";
    }
    $etarios = trim($etarios, ", ");
    $consultaSedes = "  SELECT  sc.cod_sede, 
                                sc.$valueC, 
                                $etarios, 
                                s.nom_inst, 
                                s.nom_sede, 
                                IF(s.sector = 1, 'RURAL', 'URBANO') AS sector
                        FROM sedes_cobertura sc
                        INNER JOIN sedes$periodoActual s ON s.cod_sede = sc.cod_sede
                        WHERE sc.semana = '$semana' AND sc.$valueC > 0
                            AND s.cod_mun_sede = '$municipio' ";
    if ($ruta != '') {
        $consultaSedes .= " AND sc.cod_sede IN ( SELECT cod_Sede FROM rutasedes WHERE IDRUTA = $ruta ) ";
    }                        
    if ($sector != '') {
        $consultaSedes .= " AND s.sector = $sector ";
    }
    if ($institucion != '') {
        $consultaSedes .= " AND s.cod_inst = $institucion ";
    }
    if ($sede != '') {
        $consultaSedes .= " AND s.cod_sede = $sede ";
    }   
        
    $respuestaCoberturas = $Link->query($consultaSedes) or die ('Error Ln 45 consultado Coberturas');
    if ($respuestaCoberturas->num_rows > 0) {
        while ($dataCoberturas = $respuestaCoberturas->fetch_object()) {  // aca iteramos en cada sede que tenga cobertura
            $coberturas[$dataCoberturas->cod_sede] = $dataCoberturas;  // aca guardamos las coberturas semanales
            $sedeEnCurso = $dataCoberturas->cod_sede;
            if (isset($data)) {
                unset($data);
            }
            if (isset($dataC)) {
                unset($dataC);
            }
            foreach ($dias as $keyD => $valueD) { // iteramos cada día para encontrar la minuta del día
                if (isset($data['cantidad_total_dia'.$valueD])) {
                    unset($data['cantidad_total_dia'.$valueD]);
                }

                // manejo de minutas diarias
                $consultaMenu = " SELECT Orden_Ciclo 
                                    FROM productos$periodoActual p 
                                    INNER JOIN planilla_semanas ps ON ps.MENU = p.Orden_Ciclo 
                                    WHERE ps.DIA = '$valueD' AND p.TipodeProducto = 'Menú' AND Cod_Tipo_complemento = '$valueC' LIMIT 1 "; 
                $respuestaMenu = $Link->query($consultaMenu);
                if ($respuestaMenu->num_rows > 0) {
                    $dataMenu = $respuestaMenu->fetch_object();
                    $menuDia = $dataMenu->Orden_Ciclo;  // variable en la que guardamos el menu del día que estamos iterando ej menu4, menu7
                }
                $productoDia = get_products_day($Link, $valueC, $tipoAlimento, $menuDia ); // almacenamos la minuta del día en curso


                $coberturaNovedad = '';
                // primero miramos si trabajamos con la novedad o con la cobertura existente
                $consultaDia = " SELECT DISTINCT(cod_sede) AS cod_sede, $valueC, $etarios 
                                    FROM novedades_priorizacion_dia 
                                    WHERE dia = '$valueD' AND cod_sede = $sedeEnCurso 
                                    ORDER BY ID DESC LIMIT 1 "; 
                $respuestaDia = $Link->query($consultaDia) or die ('Err Ln 51');
                if ($respuestaDia->num_rows > 0) { // si existe una novedad de priorizacion día se calcula con estas coberturas
                    $coberturaNovedad = [];
                    while ($dataCoberturasNovedad = $respuestaDia->fetch_object()) {
                        $coberturaNovedad[$dataCoberturasNovedad->cod_sede] = $dataCoberturasNovedad;
                        $coberturaNovedadAcumulado[$valueD][$dataCoberturas->cod_sede] = $dataCoberturasNovedad;
                    }
                }
                // var_dump($coberturaNovedad);
                for ($i=1; $i<=$gruposEtarios; $i++) {                    
                    $minutaDiaGrupo = $productoDia[$i];
                    $etario = "Etario$i"."_".$valueC;
                    foreach ($minutaDiaGrupo as $keyM => $valueM) { 
                        $codigos[$valueM->codigo] = $valueM->codigo;
                        $data['nom_inst'][$valueM->codigo] = $dataCoberturas->nom_inst; 
                        $data['nom_sede'][$valueM->codigo] = $dataCoberturas->nom_sede;
                        $data['sector'][$valueM->codigo] = $dataCoberturas->sector;
                        $data['complemento'][$valueM->codigo] = "$valueC";
                        $data['tipoAlimento'][$valueM->codigo] = $undMedidas[$valueM->codigo]->tipoDespacho;
                        $data['cod_alimento'][$valueM->codigo] = $valueM->codigo;
                        $data['alimento'][$valueM->codigo] = $valueM->componente;
                        $data['undMedida'][$valueM->codigo] = $undMedidas[$valueM->codigo]->NombreUnidad2;
                     
                        if ($coberturaNovedad != '') { // aca entramos cuando existe la noveda de día
                            $data['cobertura_g'.$i."_dia".$valueD][$valueM->codigo] = $coberturaNovedad[$dataCoberturas->cod_sede]->$etario;
                            $auxCantidad = ( ($valueM->Cantidad * $coberturaNovedad[$dataCoberturas->cod_sede]->$etario) * $undMedidas[$valueM->codigo]->CantidadUnd1 );
                            $data['cantidad_g'.$i."_dia".$valueD][$valueM->codigo] = $auxCantidad;
                            $data['cobertura_total_dia'.$valueD][$valueM->codigo] = $coberturaNovedad[$dataCoberturas->cod_sede]->$valueC;

                            if (isset($data['cantidad_total_dia'.$valueD][$valueM->codigo]) && $data['cantidad_total_dia'.$valueD][$valueM->codigo] > 0) {
                                $data['cantidad_total_dia'.$valueD][$valueM->codigo] += $auxCantidad; 
                            }else{
                                $data['cantidad_total_dia'.$valueD][$valueM->codigo] = $auxCantidad;
                            }

                            if (isset($data['cantidadTotalGeneral'][$valueM->codigo])) { 
                                $data['cantidadTotalGeneral'][$valueM->codigo] += $auxCantidad;
                            }
                            if (!isset($data['cantidadTotalGeneral'][$valueM->codigo])){
                                $data['cantidadTotalGeneral'][$valueM->codigo] = $auxCantidad;
                            }
                        }
                        if ($coberturaNovedad == '') {
                            $data['cobertura_g'.$i."_dia".$valueD][$valueM->codigo] = $dataCoberturas->$etario;
                            $auxCantidad = ( ($valueM->Cantidad * $dataCoberturas->$etario) * $undMedidas[$valueM->codigo]->CantidadUnd1 );
                            $data['cantidad_g'.$i."_dia".$valueD][$valueM->codigo] = $auxCantidad;
                            $data['cobertura_total_dia'.$valueD][$valueM->codigo] = $dataCoberturas->$valueC;

                            if (isset($data['cantidad_total_dia'.$valueD][$valueM->codigo]) && $data['cantidad_total_dia'.$valueD][$valueM->codigo] > 0) {
                                $data['cantidad_total_dia'.$valueD][$valueM->codigo] += $auxCantidad; 
                            }else{
                                $data['cantidad_total_dia'.$valueD][$valueM->codigo] = $auxCantidad;
                            }

                            if (isset($data['cantidadTotalGeneral'][$valueM->codigo])) { 
                                $data['cantidadTotalGeneral'][$valueM->codigo] += $auxCantidad;
                            }
                            if (!isset($data['cantidadTotalGeneral'][$valueM->codigo])){
                                $data['cantidadTotalGeneral'][$valueM->codigo] = $auxCantidad;
                            }
                        }
                        // var_dump($data);
                    }  // for alimentos
                }  // for grupos etarios
            } // fin iteracion dias  
            $dataC[] = $data; 
            // var_dump($coberturaNovedadAcumulado);
            foreach ($codigos as $keyCodigo => $valueCodigo) { // iteracion de los codigos
                if (isset($datos)) {
                    unset($datos);
                } 
                foreach ($dataC as $key => $value) { // iteracion de los dìas de la minuta
                    foreach ($value as $keyS => $valueS) { // iteracion en el array en los datos con la lleve del alimento
                        if (isset($valueS[$valueCodigo])) {   
                            if( stristr($keyS, 'cantidad') !== FALSE ){  
                                $datos[$keyS] = number_format($valueS[$valueCodigo], 2, ',', '.'); 
                            }else{
                                $datos[$keyS] = $valueS[$valueCodigo];
                            } 
                            $ultimateCode = $valueCodigo;
                            continue;
                        }
                        if(!isset($valueS[$valueCodigo])){
                            if( stristr($keyS, 'cobertura') !== FALSE ){  
                                // manejo coberturas por defecto en rows sin datos G1
                                if (stristr($keyS, 'cobertura_g1') !== FALSE) {
                                    $etarioAux = "Etario1_".$valueC;
                                    $diaAux = substr($keyS, 16);
                                    if (isset($coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]) && $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede] != '') {
                                        $datos[$keyS] = $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]->$etarioAux;
                                    }else{
                                        $datos[$keyS] = $dataCoberturas->$etarioAux;
                                    }
                                }

                                // manejo coberturas por defecto en rows sin datos G2
                                if (stristr($keyS, 'cobertura_g2') !== FALSE) {
                                    $etarioAux = "Etario2_".$valueC;
                                    $diaAux = substr($keyS, 16);
                                    if (isset($coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]) && $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede] != '') {
                                        $datos[$keyS] = $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]->$etarioAux;
                                    }else{
                                        $datos[$keyS] = $dataCoberturas->$etarioAux;
                                    }
                                }

                                // manejo coberturas por defecto en rows sin datos G3
                                if (stristr($keyS, 'cobertura_g3') !== FALSE) {
                                    $etarioAux = "Etario3_".$valueC;
                                    $diaAux = substr($keyS, 16);
                                    if (isset($coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]) && $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede] != '') {
                                        $datos[$keyS] = $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]->$etarioAux;
                                    }else{
                                        $datos[$keyS] = $dataCoberturas->$etarioAux;
                                    }
                                }

                                // manejo coberturas por defecto en rows sin datos G4
                                if (stristr($keyS, 'cobertura_g4') !== FALSE) {
                                    $etarioAux = "Etario4_".$valueC;
                                    $diaAux = substr($keyS, 16);
                                    if (isset($coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]) && $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede] != '') {
                                        $datos[$keyS] = $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]->$etarioAux;
                                    }else{
                                        $datos[$keyS] = $dataCoberturas->$etarioAux;
                                    }
                                }

                                // manejo coberturas por defecto en rows sin datos G1
                                if (stristr($keyS, 'cobertura_g5') !== FALSE) {
                                    $etarioAux = "Etario5_".$valueC;
                                    $diaAux = substr($keyS, 16);
                                    if (isset($coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]) && $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede] != '') {
                                        $datos[$keyS] = $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]->$etarioAux;
                                    }else{
                                        $datos[$keyS] = $dataCoberturas->$etarioAux;
                                    }
                                }

                                // manejo coberturas por defecto en rows sin datos G1
                                if (stristr($keyS, 'cobertura_total') !== FALSE) {
                                    $etarioAux = $valueC;
                                    $diaAux = substr($keyS, 19);
                                    if (isset($coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]) && $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede] != '') {
                                        $datos[$keyS] = $coberturaNovedadAcumulado[$diaAux][$dataCoberturas->cod_sede]->$etarioAux;
                                    }else{
                                        $datos[$keyS] = $dataCoberturas->$etarioAux;
                                    }
                                }
                            }
                            if( stristr($keyS, 'cantidad') !== FALSE ){  
                                $datos[$keyS] = number_format(0, 2, ',', '.'); 
                            }
                            continue;
                        } 
                    } 
                    if (isset($datos)) {
                        $datosE[] = $datos;
                    } 
                }
            } 
        } // fin iteracion coberturas   
    }
} // fin iteracion complementos

// exit(var_dump($datosE));

$output = [
    'sEcho' => 1,
    'iTotalRecords' => count($datosE),
    'iTotalDisplayRecords' => count($datosE),
    'aaData' => $datosE
];

echo json_encode($output);




