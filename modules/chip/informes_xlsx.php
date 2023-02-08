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

// variables que recibimos del formulario
$tipo = $_POST['tipoInforme'];
$mes = $_POST['mes'];

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

if ($tipo == 1) {

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

    // Manejo titulo numero 
    if ($_SESSION['p_Municipio'] == 0) { // cuando el contrato es departamental
        $numeroTitulo = '1168'.$_SESSION['p_CodDepartamento'].'000';
    }else if($_SESSION['p_Municipio'] != 0){ // cuando el contrato es municipal
        $numeroTitulo = '1168'.$_SESSION['p_Municipio'];
    }
    $primerDia = _data_first_month_day($mes);
    $ultimoDia = _data_last_month_day($mes);

    $sheet->setCellValue('A3', $numeroTitulo. " - Departamento de " . $_SESSION['p_Departamento']);
    $sheet->mergeCells('A3:F3');
    $sheet->setCellValue('A4', "GENERAL ");
    $sheet->mergeCells('A4:F4');
    $sheet->setCellValue('A5', $primerDia ." al " .$ultimoDia);
    $sheet->mergeCells('A5:F5');
    $sheet->setCellValue('A6', "MEN PAE EJECUCION DE RECURSOS ");
    $sheet->mergeCells('A6:F6');
    $sheet->setCellValue('A7', "MEN_PAE_003_TIPO_DE_POBLACION ");
    $sheet->mergeCells('A7:F7');
    $sheet->getStyle("A3:F7")->applyFromArray($titulos1);

    $sheet->setCellValue('A10', "CODIGO");
    $sheet->setCellValue('B10', "NOMBRE");
    $sheet->setCellValue('C10', "NUMERO DE CONTRATO");
    $sheet->setCellValue('D10', "GRUPO ETNICO");
    $sheet->setCellValue('E10', "NUMERO DE TITULARES DE DERECHO(Unidad)");
    $sheet->setCellValue('F10', "VICTIMAS DEL CONFLICTO ARMADO(Personas)");
    $sheet->getStyle("A10:F10")->applyFromArray($titulos2);

    $sheet->getColumnDimension("A")->setWidth(11); 
    $sheet->getColumnDimension("B")->setWidth(39); 
    $sheet->getColumnDimension("C")->setWidth(22); 
    $sheet->getColumnDimension("D")->setWidth(18); 
    $sheet->getColumnDimension("E")->setWidth(25); 
    $sheet->getColumnDimension("F")->setWidth(25); 

    $linea=11;
    foreach ($etniaAgrupada as $key => $value) {
        $sheet->setCellValue("A$linea", "");
        $sheet->setCellValue("B$linea", $operador);
        $sheet->setCellValue("C$linea", $contrato);
        $sheet->setCellValue("D$linea", $key);
        $sheet->setCellValue("E$linea", $value[0]);
        $sheet->setCellValue("F$linea", $value[1]);
        $linea++;
    }
    $linea--;
    $sheet->getStyle("A11:F$linea")->applyFromArray($infor);
    $sheet->getStyle("E11:F$linea")->applyFromArray($infor2);
    $sheet->getStyle("B11:C$linea")->applyFromArray($color2);
    $writer = new Xlsx($spreadsheet);
    $reader->setReadDataOnly(false);
    $writer->setIncludeCharts(TRUE);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'."POBLACION ".$nombreMeses[$mes].".xlsx".'"');
    $writer->save('php://output','POBLACION' .$nombreMeses[$mes]. '.xlsx');
}

if ($tipo == 2) {  // manejo segundo formato

    // arrays de estilos 
    $titulos1 = [
        'font' => [
            'bold' => true,
            'size'  => 16,
            'name' => 'Calibri',
        ],
        'alignment' => [
            'wrapText' => true,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'BDBDBD'],
        ]
    ];
    $infor = [
        'font' => [
            'size'  => 11,
            'name' => 'Calibri'
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
            'color' => ['argb' => 'D8D8D8'],
        ]
    ];
    $infor2 = [
        'font' => [
            'size'  => 10,
            'name' => 'Arial'
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
        'fill' => [
            'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'E0E6F8'],
        ]
    ];
    $infor3 = [
        'alignment' => [
            'wrapText' => true,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ];
    $borders = [
        'borders' => array(
            'outline' => array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                // 'color' => array('argb' => 'FFFF0000'),
            ),
        ),
    ];


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

    $sheet->setCellValue('B3', "MEN PAE SERVICIO CONTRATADO");
    $sheet->mergeCells('B3:F3');
    $sheet->getStyle("B3:F3")->applyFromArray($titulos1);

    $sheet->setCellValue('B5', "NOMBRE");
    $sheet->setCellValue('C5', "N° DE CONTRATO");
    $sheet->setCellValue('D5', "GRUPO ETNICO");
    $sheet->setCellValue('E5', "N° DE TITULARES DE DERECHO");
    $sheet->setCellValue('F5', "VICTIMAS DEL CONFLICTO");
    $sheet->getStyle("B5:F5")->applyFromArray($infor);
    $sheet->getStyle("B5:F5")->applyFromArray($borders);

    $sheet->getColumnDimension("B")->setWidth(48); 
    $sheet->getColumnDimension("C")->setWidth(22); 
    $sheet->getColumnDimension("D")->setWidth(22); 
    $sheet->getColumnDimension("E")->setWidth(12); 
    $sheet->getColumnDimension("F")->setWidth(12); 

    $linea=6;
    foreach ($etniaAgrupada as $key => $value) {
        $sheet->setCellValue("B$linea", $operador);
        $sheet->setCellValue("C$linea", $contrato);
        $sheet->setCellValue("D$linea", $value['descripcion']);
        $sheet->setCellValue("E$linea", $value['cantidadT']);
        $sheet->setCellValue("F$linea", $value['cantidadS']);
        $linea++;
    }
    $linea--;

    for ($i=6; $i <= $linea ; $i++) { 
        $sheet->getRowDimension($i)->setRowHeight(30);
    }

    $sheet->getStyle("B6:F$linea")->applyFromArray($infor2);
    $sheet->getStyle("B6:F$linea")->applyFromArray($borders);
    $sheet->getStyle("E6:F$linea")->applyFromArray($infor3)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

    $writer = new Xlsx($spreadsheet);
    $reader->setReadDataOnly(false);
    $writer->setIncludeCharts(TRUE);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'."FORMATO 3 EJECUCIÓN DE RECURSOS ".$nombreMeses[$mes].".xlsx".'"');
    $writer->save('php://output','POBLACION' .$nombreMeses[$mes]. '.xlsx');
}

if ($tipo == 3) {
       // arrays de estilos 
    $titulos1 = [
        'font' => [
            'size'  => 10,
            'name' => 'Arial',
        ],
        'alignment' => [
            'wrapText' => true,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ];

    $titulos2 = [
        'font' => [
            'bold' => true,
            'size'  => 16,
            'name' => 'Calibri',
        ],
        'alignment' => [
            'wrapText' => true,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'D8D8D8'],
        ]
    ];

    $titulos3 = [
        'font' => [
            'size'  => 10,
            'name' => 'Arial',
        ],
        'alignment' => [
            'wrapText' => true,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'DFDEDE'],
        ],
        'borders' => [
            'diagonalDirection' => Borders::DIAGONAL_BOTH,
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ];

    $titulos4 = [
        'font' => [
            'size'  => 11,
            'name' => 'Calibri',
            'color' => ['argb' => '008000']
        ],
        'alignment' => [
            'wrapText' => true,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => '90FA9D'],
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
            'size'  => 10,
            'name' => 'Arial'
        ],
        'alignment' => [
            'wrapText' => true,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'C2DCF9'],
        ],
        'borders' => [
            'diagonalDirection' => Borders::DIAGONAL_BOTH,
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ];

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

        $daysExecute = 0;
        for ($i=1; $i <=$filas ; $i++) { 
            $dailyExecution[$i] = 0;
            $totalRationsExecuted[$i] = 0;
            $totalValueExecuted[$i] = 0;
        }

        foreach ($meses as $keyM => $valueM) {
            $hoja = new Worksheet($spreadsheet, $nombreMeses[$valueM]);
            $spreadsheet->addSheet($hoja, intval($valueM));
            $spreadsheet->setActiveSheetIndex(intval($valueM));
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A3', "FORMATO 002 EJECUCIÓN DE RECURSOS");
            $sheet->mergeCells('A3:L3');
            $sheet->getStyle("A3:L3")->applyFromArray($titulos1);
        
            $sheet->setCellValue('A4', "MEN PAE SERVICIO CONTRATADO");
            $sheet->mergeCells('A4:L4');
            $sheet->getStyle("A4:L4")->applyFromArray($titulos2);

            $sheet->setCellValue('A6', "NOMBRE");
            $sheet->setCellValue('B6', "N° CONTRATO");
            $sheet->setCellValue('C6', "TIPO DE RACIÓN");
            $sheet->setCellValue('D6', "VALOR PRECIO RACIÓN");
            $sheet->setCellValue('E6', "DÍAS ATENCIÓN CONTRATADOS");
            $sheet->setCellValue('F6', "N° DE RACIONES CONTRATADAS");
            $sheet->setCellValue('G6', "TOTAL RACIONES");
            $sheet->setCellValue('H6', "TOTAL VALOR CONTRATADO");
            $sheet->setCellValue('I6', "DIAS ATENCIÓN EJECUTADOS");
            $sheet->setCellValue('J6', "NUMERO DE RACIÓN EJECUTADA DIARIA");
            $sheet->setCellValue('K6', "TOTAL RACIONES EJECUTADAS");
            $sheet->setCellValue('L6', "VALOR EJECUTADO");

            $sheet->getStyle('A6:F6')->applyFromArray($titulos3);
            $sheet->getStyle('G6:H6')->applyFromArray($titulos4);
            $sheet->getStyle('I6:J6')->applyFromArray($titulos3);
            $sheet->getStyle('K6:L6')->applyFromArray($titulos4);

            $arrayTemporal = $data[$valueM];
            $bandera = 0;
            $linea=7;
            foreach ($arrayTemporal as $keyT => $valueT) {
                $sheet->setCellValue('A'.$linea, $valueT['name']); // operator name
                $sheet->setCellValue('B'.$linea, $valueT['contract']); // contract number
                $sheet->setCellValue('C'.$linea, $valueT['type']); // type ration
                $sheet->getCell('D'.$linea)->setValueExplicit($valueT['value_type'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // value ration
                $sheet->getStyle('D'.$linea)->getNumberFormat()->setFormatCode('$ #,##0.00');
                
                $sheet->setCellValue('E'.$linea, $valueT['days_attention']); // dias de atencion

                $sheet->getCell('F'.$linea)->setValueExplicit($valueT['contrated_rations'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
                $sheet->getStyle('F'.$linea)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->getCell('G'.$linea)->setValueExplicit($valueT['total_servings'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
                $sheet->getStyle('G'.$linea)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->getCell('H'.$linea)->setValueExplicit($valueT['value_contract'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
                $sheet->getStyle('H'.$linea)->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $sheet->setCellValue('I'.$linea, $valueT['days_executed']);
                if($bandera == 0){$daysExecute += $valueT['days_executed']; $bandera = 1;}

                $sheet->getCell('J'.$linea)->setValueExplicit($valueT['daily_execution'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas.
                $sheet->getStyle('J'.$linea)->getNumberFormat()->setFormatCode('#,##0.00');
                $dailyExecution[$keyT] += $valueT['daily_execution'];

                $sheet->getCell('K'.$linea)->setValueExplicit($valueT['total_rations_executed'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
                $sheet->getStyle('K'.$linea)->getNumberFormat()->setFormatCode('#,##0');
                $totalRationsExecuted[$keyT] += $valueT['total_rations_executed'];

                $sheet->getCell('L'.$linea)->setValueExplicit($valueT['value_executed'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
                $sheet->getStyle('L'.$linea)->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $totalValueExecuted[$keyT] += $valueT['value_executed'];
                $linea++;
            }
            
            $sheet->getColumnDimension("A")->setWidth(38); 
            $sheet->getColumnDimension("B")->setWidth(22); 
            $sheet->getColumnDimension("C")->setWidth(28); 
            $sheet->getColumnDimension("D")->setWidth(22); 
            $sheet->getColumnDimension("E")->setWidth(20); 
            $sheet->getColumnDimension("F")->setWidth(20); 
            $sheet->getColumnDimension("G")->setWidth(20); 
            $sheet->getColumnDimension("H")->setWidth(20); 
            $sheet->getColumnDimension("I")->setWidth(20); 
            $sheet->getColumnDimension("J")->setWidth(20); 
            $sheet->getColumnDimension("K")->setWidth(20); 
            $sheet->getColumnDimension("L")->setWidth(20); 
            $sheet->getStyle('A7:L'.($linea-1))->applyFromArray($infor);

        }
        $linea = 7; 
        $acumula = $valueM+1;
        $spreadsheet->removeSheetByIndex(0);
        $hoja = new Worksheet($spreadsheet, 'Acumulativo');
        $spreadsheet->addSheet($hoja, $acumula);
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A3', "FORMATO 002 EJECUCIÓN DE RECURSOS");
        $sheet->mergeCells('A3:L3');
        $sheet->getStyle("A3:L3")->applyFromArray($titulos1);
        
        $sheet->setCellValue('A4', "MEN PAE SERVICIO CONTRATADO");
        $sheet->mergeCells('A4:L4');
        $sheet->getStyle("A4:L4")->applyFromArray($titulos2);

        $sheet->setCellValue('A6', "NOMBRE");
        $sheet->setCellValue('B6', "N° CONTRATO");
        $sheet->setCellValue('C6', "TIPO DE RACIÓN");
        $sheet->setCellValue('D6', "VALOR PRECIO RACIÓN");
        $sheet->setCellValue('E6', "DÍAS ATENCIÓN CONTRATADOS");
        $sheet->setCellValue('F6', "N° DE RACIONES CONTRATADAS");
        $sheet->setCellValue('G6', "TOTAL RACIONES");
        $sheet->setCellValue('H6', "TOTAL VALOR CONTRATADO");
        $sheet->setCellValue('I6', "DIAS ATENCIÓN EJECUTADOS");
        $sheet->setCellValue('J6', "NUMERO DE RACIÓN EJECUTADA DIARIA");
        $sheet->setCellValue('K6', "TOTAL RACIONES EJECUTADAS");
        $sheet->setCellValue('L6', "VALOR EJECUTADO");

        $sheet->getStyle('A6:F6')->applyFromArray($titulos3);
        $sheet->getStyle('G6:H6')->applyFromArray($titulos4);
        $sheet->getStyle('I6:J6')->applyFromArray($titulos3);
        $sheet->getStyle('K6:L6')->applyFromArray($titulos4);

        foreach ($arrayTemporal as $keyT => $valueT) {
            $sheet->setCellValue('A'.$linea, $valueT['name']); // operator name
            $sheet->setCellValue('B'.$linea, $valueT['contract']); // contract number
            $sheet->setCellValue('C'.$linea, $valueT['type']); // type ration
            $sheet->getCell('D'.$linea)->setValueExplicit($valueT['value_type'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // value ration
            $sheet->getStyle('D'.$linea)->getNumberFormat()->setFormatCode('$ #,##0.00');
            
            $sheet->setCellValue('E'.$linea, $valueT['days_attention']); // dias de atencion

            $sheet->getCell('F'.$linea)->setValueExplicit($valueT['contrated_rations'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
            $sheet->getStyle('F'.$linea)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->getCell('G'.$linea)->setValueExplicit($valueT['total_servings'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
            $sheet->getStyle('G'.$linea)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->getCell('H'.$linea)->setValueExplicit($valueT['value_contract'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
            $sheet->getStyle('H'.$linea)->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $sheet->setCellValue('I'.$linea, $daysExecute);
 
            $sheet->getCell('J'.$linea)->setValueExplicit($dailyExecution[$keyT], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
            $sheet->getStyle('J'.$linea)->getNumberFormat()->setFormatCode('#,##0.00');

            $sheet->getCell('K'.$linea)->setValueExplicit($totalRationsExecuted[$keyT], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
            $sheet->getStyle('K'.$linea)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->getCell('L'.$linea)->setValueExplicit($totalValueExecuted[$keyT], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC ); // numero de raciones contratadas
            $sheet->getStyle('L'.$linea)->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $linea++;
        }
        $sheet->getColumnDimension("A")->setWidth(38); 
        $sheet->getColumnDimension("B")->setWidth(22); 
        $sheet->getColumnDimension("C")->setWidth(28); 
        $sheet->getColumnDimension("D")->setWidth(22); 
        $sheet->getColumnDimension("E")->setWidth(20); 
        $sheet->getColumnDimension("F")->setWidth(20); 
        $sheet->getColumnDimension("G")->setWidth(20); 
        $sheet->getColumnDimension("H")->setWidth(20); 
        $sheet->getColumnDimension("I")->setWidth(20); 
        $sheet->getColumnDimension("J")->setWidth(20); 
        $sheet->getColumnDimension("K")->setWidth(20); 
        $sheet->getColumnDimension("L")->setWidth(20); 
        $sheet->getStyle('A7:L'.($linea-1))->applyFromArray($infor);
    }

    $writer = new Xlsx($spreadsheet);
    $reader->setReadDataOnly(false);
    $writer->setIncludeCharts(TRUE);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'."FORMATO ".$nombreMeses[$mes].".xlsx".'"');
    $writer->save('php://output','POBLACION' .$nombreMeses[$mes]. '.xlsx');
}





