<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require_once '../../../php/funciones.php';

function get_rations( $Link, $complemento, $tipo){ // complemento = tipo en db y tipo = jornadaUnica en db
    $rations = 0;
    $diasContrato = $_SESSION['p_dias_contrato'];
    $consulta = " SELECT COALESCE(sum(numero_raciones_contratadas), 0) as rations  
                            FROM tipo_complemento 
                            WHERE tipo = '$complemento'
                                AND jornadaUnica = $tipo ";                                                 
    $respuesta = $Link->query($consulta) or die ('Error line 10');
    if ($respuesta->num_rows > 0) { // obtenemos las preparaciones activas
        $data = $respuesta->fetch_object();
        $rations = $data->rations * $diasContrato; 
    }
    return $rations;
}

function get_cost( $Link, $complemento, $tipo){
    $cost = 0;
    $consulta = " SELECT COALESCE(ValorRacion) as cost  
                            FROM tipo_complemento 
                            WHERE tipo = '$complemento'
                                AND jornadaUnica = $tipo ";                                                 
    $respuesta = $Link->query($consulta) or die ('Error line 27');
    if ($respuesta->num_rows > 0) { // obtenemos las preparaciones activas
        $data = $respuesta->fetch_object();
        $cost = $data->cost; 
    }
    return $cost;
}

function get_complement ($link, $tipo=null, $jornada='all') {
    
    $complementos = []; 
    $consultaComplementos = " SELECT * FROM tipo_complemento";
    if ($tipo) {
        $consultaComplementos .= " WHERE tipo = '$tipo' ";
    }
    if ($jornada !== 'all') {
        $consultaComplementos .= " AND jornadaUnica = $jornada ";
    }
    $respuestaComplementos = $link->query($consultaComplementos) or die ('Error line 44');
    if ($respuestaComplementos->num_rows > 0) {
        while ($dataComplementos = $respuestaComplementos->fetch_object()) {
            $complementos[] = $dataComplementos;
        }
    }
    return $complementos;
}

function get_days_entregas(){
    $dias = "";
    for ($i = 1; $i <= 31; $i++) {
        $dias .= "D$i + ";
    }   
    return trim($dias, '+ ');
}

function get_entregas($Link, $mesPeriodo, $dias, $complemento){
    $entregas = 0;
    $tipo_complem = $complemento->CODIGO;
    $consultaEntregas = "SELECT SUM($dias) AS entregas FROM entregas_res_$mesPeriodo WHERE tipo_complem = '$tipo_complem'";
    $respuestaEntregas = $Link->query($consultaEntregas) or die ($consultaEntregas);
    if ($respuestaEntregas->num_rows > 0) {
        $dataEntregas = $respuestaEntregas->fetch_object();
        $entregas += $dataEntregas->entregas;
    }
    return $entregas;
}

$respuestaMeses = $Link->query(" SELECT DISTINCT MES AS mes FROM planilla_semanas ");
if ($respuestaMeses->num_rows > 0) {
    while ($dataMeses = $respuestaMeses->fetch_object()) {
        $meses[] = $dataMeses;
        $ultimoMes = $dataMeses->mes;
    }
    $nomMes = array( "01" => "ENERO", 
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
                    "12" => "DICIEMBRE");                
}

$mesPos = $_POST['datos']['mes'];
$periodoActual = $_SESSION['periodoActual'];
$coberturas = [];
$menuDia = '';
$dataSede = [];
$datosE = [];
$dias = get_days_entregas();

$arrayBase= [
    "totales"                   => ["title" => "",],
    "racionesCompRegular"       => ["title" => "N° Raciones entregadas Complementos Alimentarios JM/JT PAE REGULAR",],
    "costoUnitCompRegular"      => ["title" => "Costo Unitario Complementos Alimentarios JM/JT PAE REGULAR",],
    "costoTotalCompRegular"     => ["title" => "Costo Total Raciones  Complementos Alimentarios JM/JT PAE REGULAR",],
    "racionesIndUnica"          => ["title" => " N° Raciones entregadas Complementos Alimentarios JM/JT  PAE RACION INDUSTRIALIZADA",],
    "costoUnitIndUnica"         => ["title" => "Costo Unitario Complementos Alimentarios JM/JT  PAE RACION INDUSTRIALIZADA",],
    "costoTotalIndUnica"        => ["title" => "Costo Total Raciones Complementos Alimentarios JM/JT  PAE JORNADA ÚNICA",],
    "racionesAlmRegular"        => ["title" => "N° Raciones entregadas Almuerzos PAE REGULAR",],
    "costoUnitAlmRegular"       => ["title" => "Costo Unitario Almuerzos PAE REGULAR",],
    "costoTotalAlmRegular"      => ["title" => "Costo Total Raciones Almuerzos PAE REGULAR",],
    "racionesAlmUnica"          => ["title" => "N° Raciones entregadas Almuerzos  PAE JORNADA ÚNICA",],
    "costoUnitAlmUnica"         => ["title" => "Costo Unitario Almuerzos  PAE JORNADA ÚNICA",],
    "costoTotalAlmUnica"        => ["title" => "Costo Total Raciones Almuerzos  PAE JORNADA ÚNICA",],
];

foreach ($arrayBase as $key => &$fila) {
    if ($key === 'racionesCompRegular') {
        $rations = get_rations($Link, 'complemento preparado', 0);
        $fila['contract'] = number_format($rations, '2', ',', '.') ;
        $entregasTotales = 0;
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $complementos = get_complement($Link, 'complemento preparado', 0);
            $entregas = 0;
            if (!empty($complementos)) {
                foreach ($complementos as $key => $value) {
                    $mesPeriodo = $mesIterado.$periodoActual;
                    $entregas = get_entregas($Link, $mesPeriodo, $dias, $value);
                }
            }
            $fila['ejecution_'.$mesIterado] = number_format($entregas, '2', ',', '.');
            $entregasTotales += $entregas;
        }
        $fila['total_ejecution'] =  number_format($entregasTotales, '2', ',', '.');
        $fila['total_percentage'] = number_format($entregasTotales / $rations, '2', ',', '.');
        $fila['total_to_execute'] = number_format($rations - $entregasTotales, '2', ',', '.');
    }

    else if ($key === 'costoUnitCompRegular') {
        $cost = get_cost($Link, 'complemento preparado', 0);
        $fila['contract'] = number_format($cost, '2', ',', '.');
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $fila['ejecution_'.$mesIterado] = number_format($cost, '2', ',', '.');
        }
        $fila['total_ejecution'] = '-';
        $fila['total_percentage'] = '-';
        $fila['total_to_execute'] = '-';
    }

    else if ($key === 'costoTotalCompRegular') {
        $rations = get_rations($Link, 'complemento preparado', 0);
        $cost = get_cost($Link, 'complemento preparado', 0);
        $costoRations = $rations * $cost;
        $fila['contract'] = number_format($costoRations, '2', ',', '.');
        $entregasTotales = 0;
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $complementos = get_complement($Link, 'complemento preparado', 0);
            $entregas = 0;
            if (!empty($complementos)) {
                foreach ($complementos as $key => $value) {
                    $mesPeriodo = $mesIterado.$periodoActual;
                    $entregas = get_entregas($Link, $mesPeriodo, $dias, $value);
                }
            }
            $costMes = $entregas * $cost;
            $fila['ejecution_'.$mesIterado] = number_format($costMes, '2', ',', '.');
            $entregasTotales += $costMes;
        }
        $fila['total_ejecution'] =  number_format($entregasTotales, '2', ',', '.');
        $fila['total_percentage'] = number_format($entregasTotales / $costoRations, '2', ',', '.');
        $fila['total_to_execute'] = number_format($costoRations - $entregasTotales, '2', ',', '.');
    }

    
    else if ($key === 'racionesIndUnica') {
        $rations = get_rations($Link, 'industrializado', 1);
        $fila['contract'] = number_format($rations, '2', ',', '.') ;
        $entregasTotales = 0;
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $complementos = get_complement($Link, 'industrializado', 1);
            $entregas = 0;
            if (!empty($complementos)) {
                foreach ($complementos as $key => $value) {
                    $mesPeriodo = $mesIterado.$periodoActual;
                    $entregas = get_entregas($Link, $mesPeriodo, $dias, $value);
                }
            }
            $fila['ejecution_'.$mesIterado] = number_format($entregas, '2', ',', '.') ;
            $entregasTotales += $entregas;
        }
        $fila['total_ejecution'] =  number_format($entregasTotales, '2', ',', '.');
        $fila['total_percentage'] = number_format($entregasTotales / ($rations == 0 ? 1 : $rations), '2', ',', '.');
        $fila['total_to_execute'] = number_format($rations - $entregasTotales, '2', ',', '.');
    }

    else if ($key === 'costoUnitIndUnica') {
        $cost = get_cost($Link, 'industrializado', 1);
        $fila['contract'] = number_format($cost, '2', ',', '.');
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $fila['ejecution_'.$mesIterado] = number_format($cost, '2', ',', '.');
        }
        $fila['total_ejecution'] = '-';
        $fila['total_percentage'] = '-';
        $fila['total_to_execute'] = '-';
    }

    else if ($key === 'costoTotalIndUnica') {
        $rations = get_rations($Link, 'industrializado', 1);
        $cost = get_cost($Link, 'industrializado', 1);
        $costoRations = $rations * $cost;
        $fila['contract'] = number_format($costoRations, '2', ',', '.');
        $entregasTotales = 0;
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $complementos = get_complement($Link, 'industrializado', 1);
            $entregas = 0;
            if (!empty($complementos)) {
                foreach ($complementos as $key => $value) {
                    $mesPeriodo = $mesIterado.$periodoActual;
                    $entregas = get_entregas($Link, $mesPeriodo, $dias, $value);
                }
            }
            $costoMes = $entregas * $cost;
            $fila['ejecution_'.$mesIterado] = number_format($costoMes, '2', ',', '.');
            $entregasTotales += $costoMes;
        }
        $fila['total_ejecution'] =  number_format($entregasTotales);
        $fila['total_percentage'] = number_format($entregasTotales / ($costoRations == 0 ? 1 : $costoRations), '2', ',', '.');
        $fila['total_to_execute'] = number_format($costoRations - $entregasTotales, '2', ',', '.');
    }

    // almuerzo jornada regular 
    else if ($key === 'racionesAlmRegular') {
        $rations = get_rations($Link, 'almuerzo preparado', 0);
        $fila['contract'] = number_format($rations, '2', ',', '.');
        $entregasTotales = 0;
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $complementos = get_complement($Link, 'almuerzo preparado', 0);
            $entregas = 0;
            if (!empty($complementos)) {
                foreach ($complementos as $key => $value) {
                    $mesPeriodo = $mesIterado.$periodoActual;
                    $entregas = get_entregas($Link, $mesPeriodo, $dias, $value);
                }
            }
            $fila['ejecution_'.$mesIterado] = number_format($entregas, '2', ',', '.') ;
            $entregasTotales += $entregas;
        }
        $fila['total_ejecution'] =  number_format($entregasTotales, '2', ',', '.');
        $fila['total_percentage'] = number_format($entregasTotales / ($rations == 0 ? 1 : $rations), '2', ',', '.');
        $fila['total_to_execute'] = number_format($rations - $entregasTotales, '2', ',', '.');
    }

    else if ($key === 'costoUnitAlmRegular') {
        $cost = get_cost($Link, 'almuerzo preparado', 0);
        $fila['contract'] = number_format($cost, '2', ',', '.');
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $fila['ejecution_'.$mesIterado] = number_format($cost, '2', ',', '.');
        }
        $fila['total_ejecution'] = '-';
        $fila['total_percentage'] = '-';
        $fila['total_to_execute'] = '-';
    }

    else if ($key === 'costoTotalAlmRegular') {
        $rations = get_rations($Link, 'almuerzo preparado', 0);
        $cost = get_cost($Link, 'almuerzo preparado', 0);
        $costoRations = $rations * $cost;
        $fila['contract'] = number_format($costoRations, '2', ',', '.');
        $entregasTotales = 0;
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $complementos = get_complement($Link, 'almuerzo preparado', 0);
            $entregas = 0;
            if (!empty($complementos)) {
                foreach ($complementos as $key => $value) {
                    $mesPeriodo = $mesIterado.$periodoActual;
                    $entregas = get_entregas($Link, $mesPeriodo, $dias, $value);
                }
            }
            $costoMes = $entregas * $cost;
            $fila['ejecution_'.$mesIterado] = number_format($costoMes, '2', ',', '.');
            $entregasTotales += $costoRations;
        }
        $fila['total_ejecution'] =  number_format($entregasTotales, '2', ',', '.');
        $fila['total_percentage'] = number_format($entregasTotales / ($costoRations == 0 ? 1 : $costoRations), '2', ',', '.');
        $fila['total_to_execute'] = number_format($costoRations - $entregasTotales, '2', ',', '.');
    }

    // almuerzo jornada unica 
    if ($key === 'racionesAlmUnica') {
        $rations = get_rations($Link, 'almuerzo preparado', 1);
        $fila['contract'] = number_format($rations, '2', ',', '.');
        $entregasTotales = 0;
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $complementos = get_complement($Link, 'almuerzo preparado', 1);
            $entregas = 0;
            if (!empty($complementos)) {
                foreach ($complementos as $key => $value) {
                    $mesPeriodo = $mesIterado.$periodoActual;
                    $entregas = get_entregas($Link, $mesPeriodo, $dias, $value);
                }
            }
            $fila['ejecution_'.$mesIterado] = number_format($entregas, '2', ',', '.');
            $entregasTotales += $entregas;
        }
        $fila['total_ejecution'] =  number_format($entregasTotales, '2', ',', '.');
        $fila['total_percentage'] = number_format($entregasTotales / ($rations == 0 ? 1 : $rations), '2', ',', '.');
        $fila['total_to_execute'] = number_format($rations - $entregasTotales, '2', ',', '.');
    }

    else if ($key === 'costoUnitAlmUnica') {
        $cost = get_cost($Link, 'almuerzo preparado', 1);
        $fila['contract'] = number_format($cost, '2', ',', '.');
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $fila['ejecution_'.$mesIterado] = number_format($cost, '2', ',', '.');
        }
        $fila['total_ejecution'] = '-';
        $fila['total_percentage'] = '-';
        $fila['total_to_execute'] = '-';
    }

    else if ($key === 'costoTotalAlmUnica') {
        $rations = get_rations($Link, 'almuerzo preparado', 1);
        $cost = get_cost($Link, 'almuerzo preparado', 1);
        $costoRations = $rations * $cost;
        $fila['contract'] = number_format($costoRations, '2', ',', '.');
        $entregasTotales = 0;
        foreach ($meses as $keyM => $mes) { 
            $mesIterado = $mes->mes;
            $complementos = get_complement($Link, 'almuerzo preparado', 1);
            $entregas = 0;
            if (!empty($complementos)) {
                foreach ($complementos as $key => $value) {
                    $mesPeriodo = $mesIterado.$periodoActual;
                    $entregas = get_entregas($Link, $mesPeriodo, $dias, $value);
                }
            }
            $costoMes = $entregas * $cost;
            $fila['ejecution_'.$mesIterado] = number_format($costoMes, '2', ',', '.');
            $entregasTotales += $costoMes;
        }
        $fila['total_ejecution'] =  number_format($entregasTotales, '2', ',', '.');
        $fila['total_percentage'] = number_format($entregasTotales / ($costoRations == 0 ? 1 : $costoRations), '2', ',', '.');
        $fila['total_to_execute'] = number_format($costoRations - $entregasTotales, '2', ',', '.');
    }

    else if ($key === 'totales') {
        $costMes = 0;
        $costoRations = ( get_rations($Link, 'complemento preparado', 0) * get_cost($Link, 'complemento preparado', 0) ) 
                        + ( get_rations($Link, 'industrializado', 1) * get_cost($Link, 'industrializado', 1) )
                        + ( get_rations($Link, 'almuerzo preparado', 0) * get_cost($Link, 'almuerzo preparado', 0) )
                        + ( get_rations($Link, 'almuerzo preparado', 1) * get_cost($Link, 'almuerzo preparado', 1) );
        $fila['contract'] = number_format($costoRations, '2', ',', '.');
        $entregasTotales = 0;
        foreach ($meses as $keyM => $mes) { 
            $costMes = 0;
            $mesIterado = $mes->mes;
            $complementos = get_complement($Link, null, 'all');
            if (!empty($complementos)) {
                foreach ($complementos as $key => $value) {
                    $entregas = 0;
                    $mesPeriodo = $mesIterado.$periodoActual;
                    $entregas = get_entregas($Link, $mesPeriodo, $dias, $value);
                    $costMes += $entregas * $value->ValorRacion;
                }
            }
            $fila['ejecution_'.$mesIterado] = number_format($costMes, '2', ',', '.');
            $entregasTotales += $costMes;
        }
        $fila['total_ejecution'] =  number_format($entregasTotales, '2', ',', '.');
        $fila['total_percentage'] = number_format($entregasTotales / $costoRations, '2', ',', '.');
        $fila['total_to_execute'] = number_format($costoRations - $entregasTotales, '2', ',', '.');
    }
}

array_push($datosE, $arrayBase['totales']);
array_push($datosE, $arrayBase['racionesCompRegular']);
array_push($datosE, $arrayBase['costoUnitCompRegular']);
array_push($datosE, $arrayBase['costoTotalCompRegular']);
array_push($datosE, $arrayBase['racionesIndUnica']);
array_push($datosE, $arrayBase['costoUnitIndUnica']);
array_push($datosE, $arrayBase['costoTotalIndUnica']);
array_push($datosE, $arrayBase['racionesAlmRegular']);
array_push($datosE, $arrayBase['costoUnitAlmRegular']);
array_push($datosE, $arrayBase['costoTotalAlmRegular']);
array_push($datosE, $arrayBase['racionesAlmUnica']);
array_push($datosE, $arrayBase['costoUnitAlmUnica']);
array_push($datosE, $arrayBase['costoTotalAlmUnica']);

$output = [
    'sEcho' => 1,
    'iTotalRecords' => count($datosE),
    'iTotalDisplayRecords' => count($datosE),
    'aaData' => $datosE
];

echo json_encode($output);
