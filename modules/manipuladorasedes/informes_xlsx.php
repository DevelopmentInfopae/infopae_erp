<?php
require_once '../../config.php';
require_once '../../db/conexion.php';
require '../../vendor/autoload.php';

// funcion para conseguir el primer día de cualquier mes
function _data_first_month_day($mes) {
    $year = date('Y');
    return date('d-m-Y', mktime(0,0,0, $mes, 1, $year));
}

// funcion para conseguir el ultimo día de cualquier mes 
function _data_last_month_day($mes) { 
    $year = date('Y');
    $day = date("d", mktime(0,0,0, $mes+1, 0, $year));
    return date('d-m-Y', mktime(0,0,0, $mes, $day, $year));
};

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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
// creamos un nuevo libro de trabajo
$spreadsheet = new Spreadsheet();
// accedemos a la hoja activa de ese libro 
$sheet = $spreadsheet->getActiveSheet();

// variables que recibimos del formulario
$semana = $_POST['semana'];

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
        'size'  => 12,
        'name' => 'calibrí',
        'color' => array('rgb' => 'FFFAF0'),
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
    'fill' => [
        'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'color' => ['argb' => '048407'],
    ]
];

$infor = [
    'font' => [
        'size'  => 9,
        'name' => 'calibrí'
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
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
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
];

$color2 = [
    'fill' => [
        'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'color' => ['argb' => 'CEE3F6'],
    ],
];

$periodoActual = $_SESSION['periodoActual'];
$compString = '';
$consultaComplementos = " SELECT CODIGO FROM tipo_complemento ";
$respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complemento');
if ($respuestaComplementos->num_rows > 0) {
    while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
        $compString .= $dataComplementos['CODIGO'] . ", ";
        $complementos[] = $dataComplementos['CODIGO'];
    }
}
$compString = trim($compString, ', ');

$sheet->setCellValue('A1', "CIUDAD");
$sheet->setCellValue('B1', "NOMBRE INSTITUCIÓN");
$sheet->setCellValue('C1', "NOMBRE SEDE");
$letra = 'C';

foreach ($complementos as $keyC => $valueC) {
    $letra++;
    $sheet->setCellValue("$letra"."1", "COBERTURA $valueC");
    $letra++;
    $sheet->setCellValue("$letra"."1", "N° MANIPULADORA $valueC");
}
$sheet->getStyle("A1:$letra".'1')->applyFromArray($titulos1);

  
$consulta = " SELECT    u.Ciudad, 
                        s.nom_inst,
                        s.nom_sede,
                        $compString
                    FROM sedes$periodoActual s
                    INNER JOIN priorizacion$semana p ON p.cod_sede = s.cod_sede 
                    INNER JOIN ubicacion u ON s.cod_mun_sede = u.codigoDANE
                    ORDER BY u.ciudad, s.cod_inst, s.cod_sede "; 
$respuesta = $Link->query($consulta) or die ('Error al consultar la priorizacion'); 
if ($respuesta->num_rows > 0) {
    $linea = 2;
    while ($dataPriorizacion = $respuesta->fetch_assoc()) {
        $sheet->setCellValue("A$linea", $dataPriorizacion['Ciudad']);
        $sheet->setCellValue("B$linea", $dataPriorizacion['nom_inst']);
        $sheet->setCellValue("C$linea", $dataPriorizacion['nom_sede']);
        $letra = 'C';
        foreach ($complementos as $keyC => $valueC) {
            $letra++;
            $sheet->setCellValue("$letra"."$linea", utf8_decode($dataPriorizacion[$valueC]));
            $letra++;
            $consultaManipuladora = " SELECT cant_manipuladora 
                                        FROM parametros_manipuladoras 
                                        WHERE tipo_complem = '$valueC' 
                                            AND limite_inferior < $dataPriorizacion[$valueC]
                                            AND limite_superior > $dataPriorizacion[$valueC] ";
            $respuestaManipuladora = $Link->query($consultaManipuladora) or die('error al consultar ln 96');                             
            if ($respuestaManipuladora->num_rows > 0) {
                $dataManipuladora = $respuestaManipuladora->fetch_assoc();
                $sheet->setCellValue("$letra"."$linea", utf8_decode($dataManipuladora['cant_manipuladora']));
            }else{
                $sheet->setCellValue("$letra"."$linea", utf8_decode($dataPriorizacion[$valueC]));
            }   
        }
        $linea++;
    }
}   

$sheet->getStyle("A1:$letra"."$linea")->applyFromArray($infor);

$sheet->getColumnDimension("A")->setWidth(20); 
$sheet->getColumnDimension("B")->setWidth(35); 
$sheet->getColumnDimension("C")->setWidth(35); 
$letra = 'C';
foreach ($complementos as $keyC => $valueC) {
    $letra++;
    $sheet->getColumnDimension("$letra")->setWidth(20); 
    $letra++;
    $sheet->getColumnDimension("$letra")->setWidth(20); 
}

$writer = new Xlsx($spreadsheet);
$reader->setReadDataOnly(false);
$writer->setIncludeCharts(TRUE);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'."Manipuladoras Semana ".$semana.".xlsx".'"');
$writer->save('php://output','Manipuladoras Semana' .$semana. '.xlsx');




