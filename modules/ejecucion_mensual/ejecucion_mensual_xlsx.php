<?php

require_once '../../config.php';
require_once '../../db/conexion.php';
require '../../vendor/autoload.php';
require_once '../../php/funciones.php';

// definimos los parametros para el nuevo libro de excel que vamos a crear
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Supervisor;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Layout;

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

// creamos un nuevo libro de trabajo
$spreadsheet = new Spreadsheet();

// accedemos a la hoja activa de ese libro 
$sheet = $spreadsheet->getActiveSheet();

// array para nombre de los meses
$nombreMeses = array(   '01' => 'Enero', 
                        '02' => 'Febrero', 
                        '03' => 'Marzo', 
                        '04' => 'Abril', 
                        '05' => 'Mayo', 
                        '06' => 'Junio', 
                        '07' => 'Julio', 
                        '08' => 'Agosto', 
                        '09' => 'Septiembre', 
                        '10' => 'Octubre', 
                        '11' => 'Noviembre', 
                        '12' => 'Diciembre');

// array para agregar letras del abecedario si crece alguna tabla o grafica agregar las letras en el siguiente array
$letrasAbecedario = array(  '1'=>'A', '2'=>'B', '3'=>'C', '4'=>'D', '5'=>'E', '6'=>'F', '7'=>'G', '8'=>'H', '9'=>'I', '10'=>'J', '11'=>'K', '12'=>'L', '13'=>'M',
                            '14'=>'N', '15'=>'0', '16'=>'P', '17'=>'Q', '18'=>'R', '19'=>'S', '20'=>'T', '21'=>'U', '22'=>'V', '23'=>'W', '24'=>'X', '25'=>'Y', 
                            '26'=>'Z', '27'=>'AA', '28'=>'AB', '29'=>'AC', '30'=>'AD', '31'=>'AE', '32'=>'AF', '33'=>'AG', '34'=>'AH', '35'=>'AI', '36'=>'AJ', '37'=>'AK', 
                            '38'=>'AL', '39'=>'AM', '40'=>'AN', '41'=>'A0', '42'=>'AP', '43'=>'AQ', '44'=>'AR', '45'=>'AS', '46'=>'AT', '47'=>'AU', '48'=>'AV', '49'=>'AW', '50'=>'AX', '51'=>'AY', '52'=>'AZ',);

// arrays de estilos 
$titulos1 = [
    'font' => [
        'bold' => true,
        'size'  => 12,
        'name' => 'calibrí',
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
];

$titulos2 = [
    'font' => [
        'bold' => true,
        'size'  => 9,
        'name' => 'calibrí',
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$titulos3 = [
    'font' => [
        'bold' => true,
        'size'  => 9,
        'name' => 'calibrí',
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$infor = [
    'font' => [
        'size'  => 9,
        'name' => 'calibrí'
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$infor2 = [
    'font' => [
        'size'  => 9,
        'name' => 'calibrí'
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
];

$allBorders = [
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$borderTop = [
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'top' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$borderLeft = [
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'left' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$borderRight = [
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'right' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$borderBot = [
   'borders' => [
   'diagonalDirection' => Borders::DIAGONAL_BOTH,
      'bottom' => [
         'borderStyle' => Border::BORDER_THIN,
      ],
   ],
];

$color = [
    'fill' => [
        'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'color' => ['argb' => 'FFFFFF'],
    ],
];

// declaracion de variables
$mesPost = $_POST['mes'];
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

$letter = 'C';
$respuestaMeses = $Link->query(" SELECT DISTINCT MES AS mes FROM planilla_semanas ");
if ($respuestaMeses->num_rows > 0) {
    while ($dataMeses = $respuestaMeses->fetch_object()) {
        $letter ++;
        $meses[] = $dataMeses;
        $ultimoMes = $dataMeses->mes;
        if ($ultimoMes == $mesPost) {
            break;
        }
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

$periodoActual = $_SESSION['periodoActual'];
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

$complementos = get_complement($Link, null, 'all');
$compJornadaRegular = $compJornadaUnica = $almuerzoJornadaRegular = $almuerzoJornadaUnica = 0;
foreach ($complementos as $keyc => $complemento) {
    if ($complemento->tipo == 'complemento preparado' && $complemento->jornadaUnica == '0') {
        $compJornadaRegular += $complemento->numero_raciones_contratadas;
    }
    else if ($complemento->tipo == 'complemento preparado' && $complemento->jornadaUnica == '1') {
        $compJornadaUnica += $complemento->numero_raciones_contratadas;
    }
    else if ($complemento->tipo == 'almuerzo preparado' && $complemento->jornadaUnica == '0') {
        $almuerzoJornadaRegular += $complemento->numero_raciones_contratadas;
    }
    else if ($complemento->tipo == 'almuerzo preparado' && $complemento->jornadaUnica == '1') {
        $almuerzoJornadaUnica += $complemento->numero_raciones_contratadas;
    }
}

# Consulta principal para obtener los datos de la compra
$sheet = $spreadsheet->getActiveSheet();

// Configuración común para todas las hojas
$sheet->setTitle('Informe ejecucion mensual'); // Título de la hoja
$sheet->setCellValue('F1', 'INFORME DE EJECUCIÓN  DE RECURSOS');
$sheet->mergeCells("F1:P2")->getStyle("F1:P2")->applyFromArray($titulos1)->applyFromArray($allBorders);
$sheet->mergeCells('A1:E2')->getStyle("A1:E2")->applyFromArray($titulos1)->applyFromArray($allBorders);
$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$logoInfopae = $_SESSION['p_Logo ETC']; 
$drawing->setName('LogoOperador');
$drawing->setDescription('LogoOperador');
$drawing->setPath($logoInfopae);
$drawing->setHeight(65);
$drawing->setCoordinates('A1');
$drawing->setOffsetX(20);
$drawing->setOffsetY(10);
$drawing->setWorksheet($sheet);

# operador
$linea = 4;
$sheet->setCellValue("A$linea", "Periodo de ejecución: "); 
$sheet->getStyle("A$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("B$linea", ""); 
$sheet->mergeCells("B$linea:P$linea")->getStyle("B$linea:P$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$linea++;
$sheet->setCellValue("A$linea", "Operador:"); 
$sheet->getStyle("A$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("B$linea", $_SESSION['p_Operador'] ); 
$sheet->mergeCells("B$linea:P$linea")->getStyle("B$linea:P$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$linea++;
$sheet->setCellValue("A$linea", "Contrato número: "); 
$sheet->getStyle("A$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("B$linea", $_SESSION['p_NumContrato']); 
$sheet->mergeCells("B$linea:F$linea")->getStyle("B$linea:F$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$sheet->setCellValue("G$linea", "Valor del contrato: "); 
$sheet->mergeCells("G$linea:H$linea")->getStyle("G$linea:H$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("I$linea", number_format($_SESSION['p_valor_contrato'], '2', ',', '.')); 
$sheet->mergeCells("I$linea:M$linea")->getStyle("I$linea:M$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$sheet->setCellValue("N$linea", "Días contratados: "); 
$sheet->getStyle("N$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("O$linea", $_SESSION['p_dias_contrato']); 
$sheet->mergeCells("O$linea:P$linea")->getStyle("O$linea:P$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$linea++;
$sheet->setCellValue("A$linea", "N°Complemento Alimentario JM/JT PAE REGULAR "); 
$sheet->getStyle("A$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("B$linea", $compJornadaRegular); 
$sheet->mergeCells("B$linea:G$linea")->getStyle("B$linea:G$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$sheet->setCellValue("H$linea", "N° Almuerzos PAE REGULAR "); 
$sheet->mergeCells("H$linea:I$linea")->getStyle("H$linea:I$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("J$linea", $almuerzoJornadaRegular ); 
$sheet->mergeCells("J$linea:P$linea")->getStyle("J$linea:P$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$linea++;
$sheet->setCellValue("A$linea", "N°Complemento Alimentario JM/JT PAE JORNADA ÚNICA"); 
$sheet->getStyle("A$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("B$linea", $compJornadaUnica ); 
$sheet->mergeCells("B$linea:G$linea")->getStyle("B$linea:G$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$sheet->setCellValue("H$linea", "N° Almuerzos PAE JORNADA ÚNICA "); 
$sheet->mergeCells("H$linea:I$linea")->getStyle("H$linea:I$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("J$linea",  $almuerzoJornadaUnica ); 
$sheet->mergeCells("J$linea:P$linea")->getStyle("J$linea:P$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$linea++;
$sheet->setCellValue("A$linea", "Fecha acta de inicio: "); 
$sheet->getStyle("A$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("B$linea", ""); 
$sheet->mergeCells("B$linea:P$linea")->getStyle("B$linea:P$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$linea++;
$sheet->setCellValue("A$linea", "Fecha elaboración informe: "); 
$sheet->getStyle("A$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("B$linea", date('Y-m-d')); 
$sheet->mergeCells("B$linea:P$linea")->getStyle("B$linea:P$linea")->applyFromArray($infor2)->applyFromArray($allBorders);

$linea++;
$linea++;
$sheet->setCellValue("A$linea", "EJECUCIÓN DEL RECURSO: "); 
$linea2 = $linea+1;
$sheet->mergeCells("A$linea:A$linea2")->getStyle("A$linea:A$linea2")->applyFromArray($titulos2)->applyFromArray($allBorders);
$sheet->setCellValue("B$linea", "Contrato"); 
$sheet->getStyle("B$linea")->applyFromArray($titulos2)->applyFromArray($allBorders);

$letter = "C";
foreach ($meses as $keyM => $mes) {
    $mesValor = $mes->mes;
    $sheet->setCellValue("$letter$linea", "Ejecución ".$nomMes[$mesValor]); 
    $sheet->getStyle("$letter$linea")->applyFromArray($titulos2)->applyFromArray($allBorders); 
    $sheet->getColumnDimension($letter)->setWidth(20);
    $letter++;
}

$sheet->setCellValue("$letter$linea", "Total ejecutado a la fecha"); 
$sheet->getStyle("$letter$linea")->applyFromArray($titulos2)->applyFromArray($allBorders); 
$letter++;

$sheet->setCellValue("$letter$linea", "% de Ejecución financiera"); 
$sheet->getStyle("$letter$linea")->applyFromArray($titulos2)->applyFromArray($allBorders); 
$letter++;

$sheet->setCellValue("$letter$linea", "Total por ejecutar"); 
$sheet->getStyle("$letter$linea")->applyFromArray($titulos2)->applyFromArray($allBorders); 
$letter++;
$linea++;

foreach ($datosE as $keyD => $data) {
    $letter = "A";
    $sheet->setCellValue("$letter$linea", $data['title']); 
    $sheet->getStyle("$letter$linea")->applyFromArray($infor)->applyFromArray($allBorders); 
    $letter++;

    $sheet->setCellValue("$letter$linea", $data['contract']); 
    $sheet->getStyle("$letter$linea")->applyFromArray($infor)->applyFromArray($allBorders); 
    $letter++;

    foreach ($meses as $keyM => $mes) {
        $mesValor = $mes->mes;
        $sheet->setCellValue("$letter$linea", $data['ejecution_'.$mesValor]); 
        $sheet->getStyle("$letter$linea")->applyFromArray($infor)->applyFromArray($allBorders); 
        $letter++;
    }

    $sheet->setCellValue("$letter$linea", $data['total_ejecution']); 
    $sheet->getStyle("$letter$linea")->applyFromArray($infor)->applyFromArray($allBorders); 
    $sheet->getColumnDimension($letter)->setWidth(20);
    $letter++;

    $sheet->setCellValue("$letter$linea", $data['total_percentage']); 
    $sheet->getStyle("$letter$linea")->applyFromArray($infor)->applyFromArray($allBorders); 
    $sheet->getColumnDimension($letter)->setWidth(20);
    $letter++;

    $sheet->setCellValue("$letter$linea", $data['total_to_execute']); 
    $sheet->getStyle("$letter$linea")->applyFromArray($infor)->applyFromArray($allBorders); 
    $sheet->getColumnDimension($letter)->setWidth(20);
    $letter++;
    $linea++;
}
$sheet->getStyle("A1:BB".($linea+10))->applyFromArray($color);
$sheet->getColumnDimension('A')->setWidth(30);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('N')->setWidth(15);
$sheet->getDefaultRowDimension()->setRowHeight(-1);
$sheet->getRowDimension(1)->setRowHeight(30);
$sheet->getRowDimension(2)->setRowHeight(30);
    
$writer = new Xlsx($spreadsheet);
$writer->setIncludeCharts(TRUE);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="informe_ejecucion_mensual.xlsx"');
$writer->save('php://output','informe_ejecucion_mensual.xlsx');
