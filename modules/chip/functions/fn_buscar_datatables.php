<?php 
include '../../../config.php';
require_once '../../../db/conexion.php';

$tipoInforme = (isset($_POST["tipoInforme"]) && $_POST["tipoInforme"]) ? $Link->real_escape_string($_POST["tipoInforme"]) : "";
$mes = (isset($_POST["mes"]) && $_POST["mes"]) ? $Link->real_escape_string($_POST["mes"]) : "";

$mesesNom = [ 	"01" => "ENERO", 
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
					"12" => "DICIEMBRE" ];

// primero vamos a buscar la ultima focalizacion del mes que enviamos a consultar
$consultaUltimaFoca = "SELECT DISTINCT SEMANA AS semana FROM planilla_semanas WHERE MES = '$mes' ORDER BY CONSECUTIVO DESC LIMIT 1";
$respuestaUltimaFoca = $Link->query($consultaUltimaFoca) or die ('Error al consultar la ultima la ultima focalizacion del mes ln 12');
if ($respuestaUltimaFoca->num_rows > 0) {
    $dataUltimaFoca = $respuestaUltimaFoca->fetch_assoc();
    $tablaInformes = "focalizacion".$dataUltimaFoca['semana']; // nombre de la tabla de la ultima focalizacion del mes consultado
}

// consulta para traer el nombre del operador y el numero del contrato de parametros
$consultaParametros = " SELECT Operador, NumContrato FROM parametros LIMIT 1";
$respuestaParametros = $Link->query($consultaParametros) or die ('Error al consultar los parametros ln 18');
if ($respuestaParametros->num_rows > 0) {
    $dataParametros = $respuestaParametros->fetch_assoc();
    $operador = $dataParametros['Operador'];
    $contrato = $dataParametros['NumContrato'];
}

$tHead = '';
$tFoot = '';	
$tBody = '';

// vamos desde este punto a manejar los informes en diferentes partes por que son difirentes cantidades de columnas
if ($tipoInforme == 1) { // informe poblacion
    $etniaAgrupada = [
        "MAYORITARIO" => [],
        "INDIGENA" => [],
        "AFRO" => [],
        "RAZAL" => [],
        "ROM" => []
    ];

    $consultaConVictimas = " SELECT     e.ID, 
                                        e.DESCRIPCION, 
                                        COUNT(f.num_doc) AS cantidad, 
                                        e.indigena 
                                    FROM $tablaInformes f
                                    JOIN etnia e ON f.etnia = e.ID
                                    GROUP BY e.id ";
    $respuestaConVictimas = $Link->query($consultaConVictimas) or die ('Error al consultar en la focalizacion ln 25');
    if ($respuestaConVictimas->num_rows > 0) {
        while ($dataConVictimas = $respuestaConVictimas->fetch_assoc()) {
            $etniaConVictima[]=$dataConVictimas;
        }
        $consultaSinVictimas = " SELECT     e.ID, 
                                            e.DESCRIPCION, 
                                            COUNT(f.num_doc) AS cantidad, 
                                            e.indigena  
                                        FROM $tablaInformes f
                                        JOIN etnia e ON f.etnia = e.ID
                                        WHERE f.cod_pob_victima != (SELECT id FROM pobvictima WHERE nombre = 'NO APLICA')
                                        GROUP BY e.id ";
        $respuestaSinVictimas = $Link->query($consultaSinVictimas) or die('Error al consultar en la focalizacion ln 38');
        if ($respuestaSinVictimas->num_rows > 0) {
            while ($dataSinVictimas = $respuestaSinVictimas->fetch_assoc()) {
                $etniaSinVictimas[] = $dataSinVictimas;
            }
        }                                
    }

    $etniaAgrupada['MAYORITARIO'][0] = $etniaAgrupada['MAYORITARIO'][1] = 0;
    $etniaAgrupada['INDIGENA'][0] = $etniaAgrupada['INDIGENA'][1] = 0;
    $etniaAgrupada['AFRO'][0] = $etniaAgrupada['AFRO'][1] = 0;
    $etniaAgrupada['RAZAL'][0] = $etniaAgrupada['RAZAL'][1] = 0;
    $etniaAgrupada['ROM'][0] = $etniaAgrupada['ROM'][1] = 0;

    foreach ($etniaConVictima as $key => $value) {
        if ($value['indigena'] != 0) {
            $etniaAgrupada['INDIGENA'][0] += $value['cantidad'];
        }else{
            if ($value['DESCRIPCION'] == 'Negritudes' || $value['DESCRIPCION'] == 'Afrodecendientes') {
                $etniaAgrupada['AFRO'][0] += $value['cantidad'];
            }
            if ($value['DESCRIPCION'] == 'No Aplica') {
                $etniaAgrupada['MAYORITARIO'][0] += $value['cantidad'];
            }
            if ($value['DESCRIPCION'] == 'Razal') {
                $etniaAgrupada['RAZAL'][0] += $value['cantidad'];
            }
            if ($value['DESCRIPCION'] == 'Rom') {
                $etniaAgrupada['ROM'][0] += $value['cantidad'];
            }
        }
    }
    // exit(var_dump($etniaAgrupada));
    foreach ($etniaSinVictimas as $key => $value) {
        if ($value['indigena'] != 0) {
            $etniaAgrupada['INDIGENA'][1] += $value['cantidad'];
        }else{
            if ($value['DESCRIPCION'] == 'Negritudes' || $value['DESCRIPCION'] == 'Afrodecendientes') {
                $etniaAgrupada['AFRO'][1] += $value['cantidad'];
            }
            if ($value['DESCRIPCION'] == 'No Aplica') {
                $etniaAgrupada['MAYORITARIO'][1] += $value['cantidad'];
            }
            if ($value['DESCRIPCION'] == 'Razal') {
                $etniaAgrupada['RAZAL'][1] += $value['cantidad'];
            }
            if ($value['DESCRIPCION'] == 'Rom') {
                $etniaAgrupada['ROM'][1] += $value['cantidad'];
            }
        }
    }

    // empezamos a montar la tabla
    $tHead = '';
    $tHead .= "<table class='table table-striped table-bordered table-hover selectableRows' id='box-table-movimientos' >";
    $tHead .= "<thead id='tHead'>";
    $tHead .= "<tr style='height: 4em;'>";
    $tHead .= "<th> CODIGO </th>";
    $tHead .= "<th> NOMBRE </th>";
    $tHead .= "<th> NUMERO DE CONTRATO </th>";
    $tHead .= "<th> GRUPO ETNICO </th>";
    $tHead .= "<th> NUMERO DE TITULARES DE DERECHO(Unidad) </th>";
    $tHead .= "<th> VICTIMAS DEL CONFLICTO ARMADO(Personas) </th>";
    $tHead .= "</tr>";
    $tHead .= "</thead>";

    $tBody = '';
    $tBody .= "<tbody id='tBody'>";
    foreach ($etniaAgrupada as $keyA => $valueA) {
        $tBody .= '<tr>';
        $tBody .= "<td> </td>";    
        $tBody .= "<td> $operador </td>";    
        $tBody .= "<td> $contrato </td>";
        $tBody .= "<td>" .$keyA. "</td>";    
        $tBody .= "<td>" .$valueA[0]. "</td>";    
        $tBody .= "<td>" .$valueA[1]. "</td>"; 
        $tBody .= "</tr>";   
    }
    $tBody .= "</tbody>";

    $tFoot = '';
    $tFoot .= "<tfoot id='tFoot'>";
    $tFoot .= "<tr style='height: 4em;'>";
    $tFoot .= "<th> CODIGO </th>";
    $tFoot .= "<th> NOMBRE </th>";
    $tFoot .= "<th> NUMERO DE CONTRATO </th>";
    $tFoot .= "<th> GRUPO ETNICO </th>";
    $tFoot .= "<th> NUMERO DE TITULARES DE DERECHO(Unidad) </th>";
    $tFoot .= "<th> VICTIMAS DEL CONFLICTO ARMADO(Personas) </th>";
    $tFoot .= "</tr>";
    $tFoot .= "</tfoot>";
    $tFoot .= "</table>";

    $tabla = $tHead." ".$tBody." ".$tFoot;    
}

if ($tipoInforme == 2) {
    $consultaEtniaTodos = " SELECT  f.etnia, 
                                    e.descripcion, 
                                    count(*) AS cantidad        
                                FROM $tablaInformes f 
                                INNER JOIN etnia e ON (f.etnia=e.ID)  
                                GROUP BY f.etnia ";
    $respuestaEtniaTodos = $Link->query($consultaEtniaTodos) or die ('Error al consultar las etnias ln 142');
    if ($respuestaEtniaTodos->num_rows > 0) {
        while ($dataEtniaTodos = $respuestaEtniaTodos->fetch_assoc()) {
            $etniaAgrupada[$dataEtniaTodos['etnia']] = 	[	'descripcion' => $dataEtniaTodos['descripcion'],
                                                            'cantidadT' => $dataEtniaTodos['cantidad'],
                                                            'cantidadS' => 0
                                                        ];
        }
    }

    $consultaConVictimas = " SELECT f.etnia, 
                                    e.descripcion, 
                                    count(*) AS cantidad 
                                FROM $tablaInformes f 
                                INNER JOIN etnia e ON (f.etnia=e.ID) 
                                WHERE f.cod_pob_victima != (SELECT id FROM pobvictima WHERE nombre = 'NO APLICA')
                                GROUP BY f.etnia " ;
    $respuestaConVictimas = $Link->query($consultaConVictimas) or die ('Error consultar las etnias con victimas ln 159');
    if ($respuestaConVictimas->num_rows > 0) {
        while ($dataConVictimas = $respuestaConVictimas->fetch_assoc()) {
            $etniaAgrupada[$dataConVictimas['etnia']]['cantidadS'] = $dataConVictimas['cantidad']; 
        }
    }

    // empezamos a montar la tabla
    $tHead = '';
    $tHead .= "<table class='table table-striped table-bordered table-hover selectableRows' id='box-table-movimientos' >";
    $tHead .= "<thead id='tHead'>";
    $tHead .= "<tr style='height: 4em;''>";
    $tHead .= "<th> NOMBRE </th>";
    $tHead .= "<th> N° DE CONTRATO </th>";
    $tHead .= "<th> GRUPO ETNICO </th>";
    $tHead .= "<th> N° DE TITULARES DE DERECHO </th>";
    $tHead .= "<th> VICTIMAS DEL CONFLICTO </th>";
    $tHead .= "</tr>";
    $tHead .= "</thead>";

    $tBody = '';
    $tBody .= "<tbody>";
    foreach ($etniaAgrupada as $keyA => $valueA) {
        $tBody .= '<tr>';
        $tBody .= "<td> $operador </td>";    
        $tBody .= "<td> $contrato </td>";
        $tBody .= "<td>" .$valueA['descripcion']. "</td>";    
        $tBody .= "<td>" .$valueA['cantidadT']. "</td>";    
        $tBody .= "<td>" .$valueA['cantidadS']. "</td>"; 
        $tBody .= "</tr>";   
    }
    $tBody .= "</tbody>";

    $tFoot = '';
    $tFoot .= "<tfoot>";
    $tFoot .= "<tr style='height: 4em;''>";
    $tFoot .= "<th> NOMBRE </th>";
    $tFoot .= "<th> N° DE CONTRATO </th>";
    $tFoot .= "<th> GRUPO ETNICO </th>";
    $tFoot .= "<th> N° DE TITULARES DE DERECHO </th>";
    $tFoot .= "<th> VICTIMAS DEL CONFLICTO </th>";
    $tFoot .= "</tr>";
    $tFoot .= "</tfoot";
    $tFoot .= "</table";

    $tabla = $tHead." ".$tBody." ".$tFoot;    
}

if ($tipoInforme == 3){ // informe ejecucion de recursos
    $consultaParametros = " SELECT Operador, NumContrato, diasAtencion FROM parametros LIMIT 1 ";
    $filas = 3;
    $respuestaParametros = $Link->query($consultaParametros) or die ('Error al consultar los parametros');
    if ($respuestaParametros->num_rows > 0) {
        $dataParametros = $respuestaParametros->fetch_assoc();
        $nombre = $dataParametros['Operador'];
        $numContrato = $dataParametros['NumContrato'];
        $diasAtencion = $dataParametros['diasAtencion'];
    }

    $consultaMeses = " SELECT mes FROM planilla_dias WHERE CAST(MES AS SIGNED) <= CAST(" .$mes. " AS SIGNED) "; // vamos a recorrer todos los meses del año 
    // exit(var_dump($consultaMeses));
    $respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar los meses del año ln 252');
    if ($respuestaMeses->num_rows > 0) {
        while ($dataMeses = $respuestaMeses->fetch_assoc()) {
            $meses[]=$dataMeses['mes'];
            $consultaCantidadDias = " SELECT * FROM planilla_dias WHERE mes = '" .$dataMeses['mes']. "'";
            $respuestaCantidadDias = $Link->query($consultaCantidadDias) or die ('Error consultando la cantidad de días ln 170');
            if ($respuestaCantidadDias->num_rows > 0) {
                $diasEjecutados = 0;
                while ($dataCantidadDias = $respuestaCantidadDias->fetch_assoc()) {
                    $sum = '(';
                    for ($i=1; $i <=31 ; $i++) { 
                        if ($dataCantidadDias['D'.$i] != '') {
                            $diasEjecutados++;
                            $sum .= "SUM(D$i) + ";
                        }
                    }
                    $sum = trim($sum, ' + ');
                    $sum .= ') AS ejecutado ';
                }
            }
            $consultaEntregas = "SELECT t.CODIGO AS complemento,  
                                        COUNT(e.num_doc) AS cantidad,
                                        t.jornadaUnica AS jornadaUnica,
                                        t.valorRacion AS valor,
                                        t.numero_raciones_contratadas AS raciones,
                                        $sum
                                    FROM entregas_res_".$dataMeses['mes'].$_SESSION['periodoActual']. " AS e
                                    RIGHT JOIN tipo_complemento t on  t.CODIGO = e.tipo_complem 
                                    GROUP BY e.tipo_complem
                                    ORDER BY t.CODIGO " ; 
            $respuestaEntregas = $Link->query($consultaEntregas) or die ('Error al consultar la focalizacion ln 267');
            if ($respuestaEntregas->num_rows > 0) {
                while ($dataEntregas = $respuestaEntregas->fetch_assoc()) {
                    if ($dataEntregas['complemento'] == 'APS') {
                        $dataE[$dataMeses['mes']][1]['name'] = $nombre;
                        $dataE[$dataMeses['mes']][1]['contract'] = $numContrato;
                        $dataE[$dataMeses['mes']][1]['type'] = 'PAE REGULAR ALMUERZOS';
                        $dataE[$dataMeses['mes']][1]['value_type'] = $dataEntregas['valor'];
                        $dataE[$dataMeses['mes']][1]['days_attention'] = $diasAtencion;
                        $dataE[$dataMeses['mes']][1]['contrated_rations'] = ($dataEntregas['jornadaUnica'] == 1) ? '0' : $dataEntregas['raciones'];
                        $dataE[$dataMeses['mes']][1]['total_servings'] = ($dataEntregas['jornadaUnica'] == 1) ? '0' : ($dataEntregas['raciones'] * $diasAtencion);
                        $dataE[$dataMeses['mes']][1]['value_contract'] = ($dataEntregas['jornadaUnica'] == 1) ? '0' : ($dataEntregas['raciones'] * $diasAtencion) * $dataEntregas['valor']; // total contratado
                        $dataE[$dataMeses['mes']][1]['days_executed'] = $diasEjecutados; // dias ejecutados
                        $dataE[$dataMeses['mes']][1]['daily_execution'] = ($dataEntregas['jornadaUnica'] == 1) ? '0' : $dataEntregas['ejecutado'] / $diasEjecutados; // Raciones ejecutadas diarias
                        $dataE[$dataMeses['mes']][1]['total_rations_executed'] = ($dataEntregas['jornadaUnica'] == 1) ? '0' : $dataEntregas['ejecutado'];  // total raciones ejecutadas
                        $dataE[$dataMeses['mes']][1]['value_executed'] = ($dataEntregas['jornadaUnica'] == 1) ? '0' : $dataEntregas['ejecutado'] * $dataEntregas['valor'];  // valor ejecutado

                        if ($dataEntregas['jornadaUnica'] == 1) {
                            $filas = 4;
                            $dataE[$dataMeses['mes']][4]['name'] = $nombre;
                            $dataE[$dataMeses['mes']][4]['contract'] = $numContrato;
                            $dataE[$dataMeses['mes']][4]['type'] = 'JORNADA UNICA ALMUERZOS ';
                            $dataE[$dataMeses['mes']][4]['value_type'] = $dataEntregas['valor'];
                            $dataE[$dataMeses['mes']][4]['days_attention'] = $diasAtencion;
                            $dataE[$dataMeses['mes']][4]['contrated_rations'] = $dataEntregas['raciones'];
                            $dataE[$dataMeses['mes']][4]['total_servings'] = ($dataEntregas['raciones'] * $diasAtencion);
                            $dataE[$dataMeses['mes']][4]['value_contract'] = ($dataEntregas['raciones'] * $diasAtencion) * $dataEntregas['valor']; // total contratado
                            $dataE[$dataMeses['mes']][4]['days_executed'] = $diasEjecutados; // dias ejecutados
                            $dataE[$dataMeses['mes']][4]['daily_execution'] = $dataEntregas['ejecutado'] / $diasEjecutados; // Raciones ejecutadas diarias
                            $dataE[$dataMeses['mes']][4]['total_rations_executed'] = $dataEntregas['ejecutado'];  // total raciones ejecutadas
                            $dataE[$dataMeses['mes']][4]['value_executed'] = $dataEntregas['ejecutado'] * $dataEntregas['valor'];  // valor ejecutado
                        }
                    }
                    if ($dataEntregas['complemento'] == 'CAJMPS' || $dataEntregas['complemento'] == 'CAJTPS') {
                        $dataE[$dataMeses['mes']][2]['name'] = $nombre;
                        $dataE[$dataMeses['mes']][2]['contract'] = $numContrato;
                        $dataE[$dataMeses['mes']][2]['type'] = 'PAE REGULAR COMPLEMENTOS ALIMENTARIOS PREPARADO EN SITIO ';
                        $dataE[$dataMeses['mes']][2]['value_type'] = $dataEntregas['valor'];
                        $dataE[$dataMeses['mes']][2]['days_attention'] = $diasAtencion;
                        $dataE[$dataMeses['mes']][2]['contrated_rations'] = isset($dataE[$dataMeses['mes']][2]['contrated_rations']) ? $dataE[$dataMeses['mes']][2]['contrated_rations'] + $dataEntregas['raciones'] : $dataEntregas['raciones'];
                        $dataE[$dataMeses['mes']][2]['total_servings'] = isset($dataE[$dataMeses['mes']][2]['total_servings']) ? $dataE[$dataMeses['mes']][2]['total_servings'] + $dataEntregas['raciones'] * $diasAtencion : $dataEntregas['raciones'] * $diasAtencion;
                        $dataE[$dataMeses['mes']][2]['value_contract'] = isset($dataE[$dataMeses['mes']][2]['value_contract']) ? $dataE[$dataMeses['mes']][2]['value_contract'] + ($dataEntregas['raciones'] * $diasAtencion) * $dataEntregas['valor'] : ($dataEntregas['raciones'] * $diasAtencion) * $dataEntregas['valor']; // total contratado
                        $dataE[$dataMeses['mes']][2]['days_executed'] = $diasEjecutados; // dias ejecutados
                        $dataE[$dataMeses['mes']][2]['daily_execution'] = isset($dataE[$dataMeses['mes']][2]['daily_execution']) ? $dataE[$dataMeses['mes']][2]['daily_execution'] + $dataEntregas['ejecutado'] / $diasEjecutados : $dataEntregas['ejecutado'] / $diasEjecutados; // Raciones ejecutadas diarias
                        $dataE[$dataMeses['mes']][2]['total_rations_executed'] = isset($dataE[$dataMeses['mes']][2]['total_rations_executed']) ? $dataE[$dataMeses['mes']][2]['total_rations_executed'] + $dataEntregas['ejecutado'] : $dataEntregas['ejecutado'] ;  // total raciones ejecutadas
                        $dataE[$dataMeses['mes']][2]['value_executed'] = isset($dataE[$dataMeses['mes']][2]['value_executed']) ? $dataE[$dataMeses['mes']][2]['value_executed'] + $dataEntregas['ejecutado'] * $dataEntregas['valor'] : $dataEntregas['ejecutado'] * $dataEntregas['valor'];  // valor ejecutado
                    }
                    if ($dataEntregas['complemento'] == 'CAJMRI' || $dataEntregas['complemento'] == 'CAJTRI') {
                        $dataE[$dataMeses['mes']][3]['name'] = $nombre;
                        $dataE[$dataMeses['mes']][3]['contract'] = $numContrato;
                        $dataE[$dataMeses['mes']][3]['type'] = 'PAE REGULAR COMPLEMENTOS ALIMENTARIOS REFRIGERIOS ';
                        $dataE[$dataMeses['mes']][3]['value_type'] = $dataEntregas['valor'];
                        $dataE[$dataMeses['mes']][3]['days_attention'] = $diasAtencion;
                        $dataE[$dataMeses['mes']][3]['contrated_rations'] = isset($dataE[$dataMeses['mes']][3]['contrated_rations']) ? $dataE[$dataMeses['mes']][3]['contrated_rations'] + $dataEntregas['raciones'] : $dataEntregas['raciones'];
                        $dataE[$dataMeses['mes']][3]['total_servings'] = isset($dataE[$dataMeses['mes']][3]['total_servings']) ? $dataE[$dataMeses['mes']][3]['total_servings'] + $dataEntregas['raciones'] * $diasAtencion : $dataEntregas['raciones'] * $diasAtencion;
                        $dataE[$dataMeses['mes']][3]['value_contract'] = isset($dataE[$dataMeses['mes']][3]['value_contract']) ? $dataE[$dataMeses['mes']][3]['value_contract'] + ($dataEntregas['raciones'] * $diasAtencion) * $dataEntregas['valor'] : ($dataEntregas['raciones'] * $diasAtencion) * $dataEntregas['valor']; // total contratado
                        $dataE[$dataMeses['mes']][3]['days_executed'] = $diasEjecutados; // dias ejecutados
                        $dataE[$dataMeses['mes']][3]['daily_execution'] = isset($dataE[$dataMeses['mes']][3]['daily_execution']) ? $dataE[$dataMeses['mes']][3]['daily_execution'] + $dataEntregas['ejecutado'] / $diasEjecutados : $dataEntregas['ejecutado'] / $diasEjecutados; // Raciones ejecutadas diarias
                        $dataE[$dataMeses['mes']][3]['total_rations_executed'] = isset($dataE[$dataMeses['mes']][3]['total_rations_executed']) ? $dataE[$dataMeses['mes']][3]['total_rations_executed'] + $dataEntregas['ejecutado'] : $dataEntregas['ejecutado'] ;  // total raciones ejecutadas
                        $dataE[$dataMeses['mes']][3]['value_executed'] = isset($dataE[$dataMeses['mes']][3]['value_executed']) ? $dataE[$dataMeses['mes']][3]['value_executed'] + $dataEntregas['ejecutado'] * $dataEntregas['valor'] : $dataEntregas['ejecutado'] * $dataEntregas['valor'];  // valor ejecutado
                    }
                }
            }                            
        }

        foreach ($meses as $keyS => $valueS) {
            $auxTemporal = $dataE[$valueS];
            for ($i=1; $i <=$filas ; $i++) { 
                $data[$valueS][$i] = isset($dataE[$valueS][$i]) ? $dataE[$valueS][$i] : ''; 
            }
        }   
        
        // empezamos a montar la tabla
        $tdiv = " <div class='clients-list'> ";
            $tdiv .=  " <ul class='nav nav-tabs'>";
            $auxIndice = 1;
            foreach ($meses as $key => $value) {
                $tdiv .= " <li class= "; 
                $tdiv .= ($auxIndice == $mes) ? "'active'" : "' '"; 
                $tdiv .= " >";
                $tdiv .= " <a data-toggle='tab' href= '#tab-" .$value. "'>";
                $tdiv .= " <i class='fa fa-bar-chart'></i> " .$mesesNom[$value]. " ";
                $tdiv .= " </a> ";
                $tdiv .= " </li>";
                $auxIndice++;
            }
            if ($value == $mes) {
                $tdiv .= " <li>";
                $tdiv .= " <a data-toggle='tab' href= '#tab-" .'T'. "'>";
                $tdiv .= " <i class='fa fa-bar-chart'></i> " .'ACUMULATIVO'. " ";
                $tdiv .= " </a> ";
                $tdiv .= " </li>";
                $auxIndice++;
            }
            $tdiv .= "</ul><br>";        
            $tdiv .= "<div class='tab-content'>";

                $auxIndice = 1;
                $tabla = '';
                $daysExecute = 0;
                for ($i=1; $i <=$filas ; $i++) { 
                    $dailyExecution[$i] = 0;
                    $totalRationsExecuted[$i] = 0;
                    $totalValueExecuted[$i] = 0;
                }
                
                foreach ($meses as $key => $value){             
                    $tHead = '';
                    $tHead .= " <div id= 'tab-" .$value. "' class='tab-pane"; 
                    if($auxIndice == $mes){ $tHead .=  " active'";} else { $tHead .= "'";}
                    $tHead .= " >";
                        $tHead .= "<div class='table-responsive'>";
                            $tHead .= "<table class='table table-striped table-hover box-table-$value' id='box-table-movimientos' >";
                                $tHead .= "<thead>";
                                    $tHead .= "<tr>";
                                        $tHead .= "<th> NOMBRE </th>";
                                        $tHead .= "<th> N° DE CONTRATO </th>";
                                        $tHead .= "<th> TIPO DE RACIÓN </th>";
                                        $tHead .= "<th> VALOR PRECIO RACIÓN </th>";
                                        $tHead .= "<th> DÍAS ATENCIÓN CONTRATADOS </th>";
                                        $tHead .= "<th> N° DE RACIONES CONTRATADAS </th>";
                                        $tHead .= "<th> TOTAL RACIONES </th>";
                                        $tHead .= "<th> TOTAL VALOR CONTRATADO </th>";
                                        $tHead .= "<th> DIAS ATENCIÓN EJECUTADOS </th>";
                                        $tHead .= "<th> NUMERO DE RACIÓN EJECUTADA DIARIA </th>";
                                        $tHead .= "<th> TOTAL RACIONES EJECUTADAS </th>";
                                        $tHead .= "<th> VALOR EJECUTADO </th>";
                                    $tHead .= "</tr>";
                                $tHead .= "</thead>";

                                $tBody = '';
                                $tBody .= "<tbody>";
                                    $arrayTemporal = $data[$value];
                                    $bandera = 0;
                                    foreach ($arrayTemporal as $keyT => $valueT) {
                                        $tBody .= "<tr>";
                                            $tBody .= "<td>" .$valueT['name']. "</td>";
                                            $tBody .= "<td>" .$valueT['contract']. "</td>";
                                            $tBody .= "<td>" .$valueT['type']. "</td>";
                                            $tBody .= "<td>" .'$'.number_format($valueT['value_type'], '2', ',', '.'). "</td>";
                                            $tBody .= "<td>" .$valueT['days_attention']. "</td>";
                                            $tBody .= "<td>" .number_format($valueT['contrated_rations'], '0', ',', '.'). "</td>";
                                            $tBody .= "<td>" .number_format($valueT['total_servings'], '0', ',', '.'). "</td>";
                                            $tBody .= "<td>" .'$'.number_format($valueT['value_contract'],'0',',','.'). "</td>";
                                            $tBody .= "<td>" .$valueT['days_executed']. "</td>";
                                            if($bandera == 0){$daysExecute += $valueT['days_executed']; $bandera = 1;}
                                            $tBody .= "<td>" .number_format($valueT['daily_execution'],'0',',','.'). "</td>";
                                            $dailyExecution[$keyT] += $valueT['daily_execution'];
                                            $tBody .= "<td>" .number_format($valueT['total_rations_executed'],'0',',','.'). "</td>";
                                            $totalRationsExecuted[$keyT] += $valueT['total_rations_executed'];
                                            $tBody .= "<td>" .'$'.number_format($valueT['value_executed'],'2',',','.'). "</td>";
                                            $totalValueExecuted[$keyT] += $valueT['value_executed'];
                                        $tBody .= "</tr>";
                                    }
                                $tBody .= "</tbody>";
                                 
                                $tFoot = '';
                                $tFoot .= " <tfoot> ";
                                    $tFoot .= " <tr> ";
                                        $tFoot .= " <th> NOMBRE </th> ";
                                        $tFoot .= " <th> N° DE CONTRATO </th> ";
                                        $tFoot .= " <th> TIPO DE RACIÓN </th> ";
                                        $tFoot .= " <th> VALOR PRECIO RACIÓN </th> ";
                                        $tFoot .= " <th> DÍAS ATENCIÓN CONTRATADOS </th> ";
                                        $tFoot .= " <th> N° DE RACIONES CONTRATADAS </th> ";
                                        $tFoot .= " <th> TOTAL RACIONES </th> ";
                                        $tFoot .= " <th> TOTAL VALOR CONTRATADO </th> ";
                                        $tFoot .= " <th> DIAS ATENCIÓN EJECUTADOS </th> ";
                                        $tFoot .= " <th> NUMERO DE RACIÓN EJECUTADA DIARIA </th> ";
                                        $tFoot .= " <th> TOTAL RACIONES EJECUTADAS </th> ";
                                        $tFoot .= " <th> VALOR EJECUTADO </th> ";
                                    $tFoot .= " </tr> ";
                                $tFoot .= " </tfoot> ";
                            $tFoot .= " </table> ";
                        $tFoot .= " </div> ";
                    $tFoot .= " </div> ";

                    $tabla .= $tHead." ".$tBody." ".$tFoot; 
                    $auxIndice++;		
                }
                if ($value == $mes) {
                    $tHead = '';
                    $tHead .= " <div id= 'tab-" .'T'. "' class='tab-pane"; 
                    if($auxIndice == 1){ $tHead .=  " active'";} else { $tHead .= "'";}
                    $tHead .= " >";
                        $tHead .= "<div class='table-responsive'>";
                            $tHead .= "<table class='table table-striped table-hover box-table-T' id='box-table-movimientos' >";
                                $tHead .= "<thead>";
                                    $tHead .= "<tr>";
                                        $tHead .= "<th> NOMBRE </th>";
                                        $tHead .= "<th> N° DE CONTRATO </th>";
                                        $tHead .= "<th> TIPO DE RACIÓN </th>";
                                        $tHead .= "<th> VALOR PRECIO RACIÓN </th>";
                                        $tHead .= "<th> DÍAS ATENCIÓN CONTRATADOS </th>";
                                        $tHead .= "<th> N° DE RACIONES CONTRATADAS </th>";
                                        $tHead .= "<th> TOTAL RACIONES </th>";
                                        $tHead .= "<th> TOTAL VALOR CONTRATADO </th>";
                                        $tHead .= "<th> DIAS ATENCIÓN EJECUTADOS </th>";
                                        $tHead .= "<th> NUMERO DE RACIÓN EJECUTADA DIARIA </th>";
                                        $tHead .= "<th> TOTAL RACIONES EJECUTADAS </th>";
                                        $tHead .= "<th> VALOR EJECUTADO </th>";
                                    $tHead .= "</tr>";
                                $tHead .= "</thead>";

                                $tBody = '';
                                $tBody .= "<tbody>";
                                    $arrayTemporal = $data[$value];
                                    foreach ($arrayTemporal as $keyT => $valueT) {
                                        // var_dump($keyT);
                                        $tBody .= "<tr>";
                                            $tBody .= "<td>" .$valueT['name']. "</td>";
                                            $tBody .= "<td>" .$valueT['contract']. "</td>";
                                            $tBody .= "<td>" .$valueT['type']. "</td>";
                                            $tBody .= "<td>" .'$'.number_format($valueT['value_type'], '2', ',', '.'). "</td>";
                                            $tBody .= "<td>" .$valueT['days_attention']. "</td>";
                                            $tBody .= "<td>" .number_format($valueT['contrated_rations'], '0', ',', '.'). "</td>";
                                            $tBody .= "<td>" .number_format($valueT['total_servings'], '0', ',', '.'). "</td>";
                                            $tBody .= "<td>" .'$'.number_format($valueT['value_contract'],'0',',','.'). "</td>";
                                            $tBody .= "<td>" .$daysExecute. "</td>";
                                            $tBody .= "<td>" .number_format($dailyExecution[$keyT],'0',',','.'). "</td>";
                                            $tBody .= "<td>" .number_format($totalRationsExecuted[$keyT],'0',',','.'). "</td>";
                                            $tBody .= "<td>" .'$'.number_format($totalValueExecuted[$keyT],'2',',','.'). "</td>";
                                        $tBody .= "</tr>";
                                    }
                                $tBody .= "</tbody>";
                                 
                                $tFoot = '';
                                $tFoot .= " <tfoot> ";
                                    $tFoot .= " <tr> ";
                                        $tFoot .= " <th> NOMBRE </th> ";
                                        $tFoot .= " <th> N° DE CONTRATO </th> ";
                                        $tFoot .= " <th> TIPO DE RACIÓN </th> ";
                                        $tFoot .= " <th> VALOR PRECIO RACIÓN </th> ";
                                        $tFoot .= " <th> DÍAS ATENCIÓN CONTRATADOS </th> ";
                                        $tFoot .= " <th> N° DE RACIONES CONTRATADAS </th> ";
                                        $tFoot .= " <th> TOTAL RACIONES </th> ";
                                        $tFoot .= " <th> TOTAL VALOR CONTRATADO </th> ";
                                        $tFoot .= " <th> DIAS ATENCIÓN EJECUTADOS </th> ";
                                        $tFoot .= " <th> NUMERO DE RACIÓN EJECUTADA DIARIA </th> ";
                                        $tFoot .= " <th> TOTAL RACIONES EJECUTADAS </th> ";
                                        $tFoot .= " <th> VALOR EJECUTADO </th> ";
                                    $tFoot .= " </tr> ";
                                $tFoot .= " </tfoot> ";
                            $tFoot .= " </table> ";
                        $tFoot .= " </div> ";
                    $tFoot .= " </div> ";

                    $tabla .= $tHead." ".$tBody." ".$tFoot; 	
                }					
		    $tdiv .= $tabla." </div> </div> ";	
            $tabla = $tdiv; 
      
    }
}

$data['tabla'] = $tabla;
echo json_encode($data);
