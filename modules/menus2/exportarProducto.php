<?php 
require_once '../../config.php';
require_once '../../db/conexion.php';
require '../../vendor/autoload.php';

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

// creamos un nuevo libro de trabajo
$spreadsheet = new Spreadsheet();

// accedemos a la hoja activa de ese libro 
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('D2', 'Sistema de Información Tecnológico InfoPAE');
$sheet->mergeCells('D2:P4');
$sheet->setCellValue('D5', 'Información Producto Alimentario');
$sheet->mergeCells('D5:P7');
$sheet->mergeCells('B2:C7');

$titulos1 = [
    'font' => [
        'bold' => true,
        'size'  => 12,
        'name' => 'calibrí',
        // 'color' => '#033B73'
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


$titulos = [
    'font' => [
        'bold' => true,
        'size'  => 12,
        'name' => 'calibrí',
        'color' => ['argb' => 'FDFEFE']
        // 'color' => '#033B73'
    ],
    'fill' => [
      'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
      'color' => ['argb' => '0C1846'],
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

$titulos2 = [
    'font' => [
        'bold' => true,
        'size'  => 10,
        'name' => 'calibrí',
        // 'color' => ['argb' => 'FDFEFE']
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        // 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_LEFT,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
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

$titulos3 = [
    'font' => [
        'bold' => true,
        'size'  => 10,
        'name' => 'calibrí',
        // 'color' => ['argb' => 'FDFEFE']
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

$color = [
 'fill' => [
     'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
     'color' => ['argb' => 'FFFFFF'],
    ],
];


$sheet->getStyle("D2:P4")->applyFromArray($titulos1);
$sheet->getStyle("D5:P7")->applyFromArray($titulos1);
$sheet->getStyle("D7:P7")->applyFromArray($titulos1);
$sheet->getStyle('B2:C7')->applyFromArray($titulos1);

$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath('../../upload/logotipos/infopae.png');
$drawing->setHeight(90);
$drawing->setCoordinates('B2');
$drawing->setOffsetX(25);
$drawing->setOffsetY(17);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

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

$informacion = [];
$periodoActual = $_SESSION['periodoActual'];
$idProducto = $_POST['idProductoExportar'];

$consultaProducto = " SELECT p.TipodeProducto AS tipoProducto,
							 m.grupo_alim AS subtipoProducto, 
							 p.Descripcion AS descripcion, 
							 p.Codigo AS codigo, 
							 td.Descripcion AS tipoDespacho, 
							 p.NombreUnidad1 AS unidadMedida,
							 p.NombreUnidad2 AS unidadMedida2,
							 m.kcalxg AS calorias, 
							 m.kcaldgrasa AS kiloCalorias, 
							 m.Grasa_Sat AS grasasSaturadas, 
							 m.Grasa_poliins AS grasasPolisaturadas, 
							 m.Grasa_Monoins AS grasasMonosaturadas, 
							 m.Grasa_Trans AS grasasTrans, 
							 m.Fibra_dietaria AS fibra,
							 m.Azucares AS azucares, 
							 m.Proteinas AS proteinas,
							 m.Colesterol AS colesteros, 
							 m.Sodio AS sodio, 
							 m.Zinc AS zinc, 
							 m.Calcio AS calcio, 
							 m.Hierro AS hierro, 
							 m.Vit_A AS vitaminaA, 
							 m.Vit_C AS vitaminaC, 
							 m.Vit_B1 AS vitaminaB1, 
							 m.Vit_B2 AS vitaminaB2, 
							 m.Vit_B3 AS vitaminaB3,
							 m.Acido_Fol AS acidoFolico, 
							 m.Referencia AS referencia, 
							 m.cod_Referencia AS codigoReferencia  
						FROM productos21 p
						INNER JOIN menu_aportes_calynut m ON p.Codigo = m.cod_prod
						INNER JOIN tipo_despacho td ON p.TipoDespacho = td.Id
						WHERE p.Id = $idProducto;";

$respuestaProducto = $Link->query($consultaProducto) or die ('Error al consultar el producto alimentario ' . mysqli_error($Link));
if ($respuestaProducto->num_rows > 0) {
	$dataProducto = $respuestaProducto->fetch_assoc();
	$informacion = $dataProducto;
}						

$numFila = 10;
$sheet->setCellValue("B".$numFila, "INFORMACIÓN BASICA");
$sheet->mergeCells("B".$numFila .":". "F".$numFila);
$tituloInformacionBasica = "B".$numFila .":". "F".$numFila;
$numFila ++;

$sheet->setCellValue("B".$numFila, "Tipo de producto");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["tipoProducto"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "SubTipo de producto");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["subtipoProducto"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Descripción");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["descripcion"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Código");
$sheet->setCellValue("C".$numFila, $informacion["codigo"]);
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Tipo de despacho");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["tipoDespacho"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila,"Unidad de medida");
$nombreUnidad = '';
if (strtolower($informacion['unidadMedida']) == 'u') {
	$nombreUnidad = "Unidad";
}else if(strtolower($informacion['unidadMedida']) == 'g'){
	$nombreUnidad = "Gramos";
}else if(strtolower($informacion['unidadMedida']) == 'cc'){
	$nombreUnidad = "Centímetros Cúbicos";
}
$sheet->setCellValue("C".$numFila, strtoupper($nombreUnidad));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Cantidad de medida");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["unidadMedida2"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$subtitulosInformacionBasica = "B11:B17";
$tablaInformacionBasica = "C11:F17";

// seccion para mostar los aportes caloricos
$numFila = 10;
$letra = "I";
$sheet->setCellValue($letra.$numFila, "Aportes Calorías y Nutrientes");
$sheet->mergeCells($letra.$numFila .":". "P".$numFila);
$tituloAportes = $letra.$numFila .":". "P".$numFila;
$numFila = $numFila + 2;
$numFila2 = $numFila;
$letra2 = $letra;

$sheet->setCellValue($letra2.$numFila2, "Calorías");
$tituloCalorias = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['calorias']);
$subtituloCalorias = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Kcal desde la grasa");
$tituloKiloCalorias = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['kiloCalorias']);
$subtituloKiloCalorias = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Grasa saturada");
$tituloGrasaSaturada = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['grasasSaturadas']);
$subtituloGrasaSaturada = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Grasa poliinsaturada");
$tituloGrasaPolisaturada = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['grasasPolisaturadas']);
$subtituloGrasaPolisaturada = $letra2.$numFila2;

$numFila2 = $numFila2 + 2;
$numFila = $numFila2;
$letra2 = $letra;
$sheet->setCellValue($letra2.$numFila2, "Grasa monoinsaturada");
$tituloGrasaMonoinsaturada = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['grasasMonosaturadas']);
$subtituloGrasaMonoinsaturada = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Grasa trans");
$tituloGrasaTrans = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['grasasTrans']);
$subtituloGrasaTrans = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Fibra dietaria");
$tituloFibra = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['fibra']);
$subtituloFibra = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Azúcares");
$tituloAzucares = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['azucares']);
$subtituloAzucares = $letra2.$numFila2;

$numFila2 = $numFila2 + 2;
$numFila = $numFila2;
$letra2 = $letra;
$sheet->setCellValue($letra2.$numFila2, "Proteínas");
$tituloProteinas = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['proteinas']);
$subtituloProteinas = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Colesterol");
$tituloColesterol = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['colesteros']);
$subtituloColesterol = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Sodio");
$tituloSodio = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['sodio']);
$subtituloSodio = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Zinc");
$tituloZinc = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['zinc']);
$subtituloZin = $letra2.$numFila2;

$numFila2 = $numFila2 + 2;
$numFila = $numFila2;
$letra2 = $letra;
$sheet->setCellValue($letra2.$numFila2, "Calcio");
$tituloCalcio = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['calcio']);
$subtituloCalcio = $letra2.$numFila2;


$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Hierro");
$tituloHierro = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['hierro']);
$subtituloHierro = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Vitamina A");
$tituloVitaminaA = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['vitaminaA']);
$subtituloVitaminaA = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Vitamina C");
$tituloVitaminaC = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['vitaminaC']);
$subtituloVitaminaC = $letra2.$numFila2;

$numFila2 = $numFila2 + 2;
$numFila = $numFila2;
$letra2 = $letra;
$sheet->setCellValue($letra2.$numFila2, "Vitamina B1");
$tituloVitaminaB = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['vitaminaB1']);
$subtituloVitaminaB = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Vitamina B2");
$tituloVitaminaB2 = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['vitaminaB2']);
$subtituloVitaminaB2 = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Vitamina B3");
$tituloVitaminaB3 = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['vitaminaB3']);
$subtituloVitaminaB3 = $letra2.$numFila2;
 
$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Ácido fólico");
$tituloAcidoFolico = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['acidoFolico']);
$subtituloAcidoFolico = $letra2.$numFila2;

$numFila2 = $numFila2 + 2;
$numFila = $numFila2;
$letra2 = $letra;
$sheet->setCellValue($letra2.$numFila2, "Referencia TCA");
$tituloReferencia = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['referencia']);
$subtituloReferencia = $letra2.$numFila2;

$numFila2 = $numFila;
$letra2 ++; 
$letra2 ++; 
$sheet->setCellValue($letra2.$numFila2, "Código TCA");
$tituloReferenciaCodigo = $letra2.$numFila2;
$numFila2 ++;
$sheet->setCellValue($letra2.$numFila2, $informacion['codigoReferencia']);
$subtituloReferenciaCodigo = $letra2.$numFila2;

// estilos

$sheet->getColumnDimension("I")->setWidth(22); 
$sheet->getColumnDimension("J")->setWidth(11); 
$sheet->getColumnDimension("K")->setWidth(22); 
$sheet->getColumnDimension("L")->setWidth(11); 
$sheet->getColumnDimension("M")->setWidth(22); 
$sheet->getColumnDimension("N")->setWidth(11); 
$sheet->getColumnDimension("O")->setWidth(22); 
$sheet->getColumnDimension("P")->setWidth(11); 
$sheet->getColumnDimension("B")->setWidth(35); 

$sheet->getStyle("A1:Z1000")->applyFromArray($color);
$sheet->getStyle($tituloInformacionBasica)->applyFromArray($titulos);
$sheet->getStyle($subtitulosInformacionBasica)->applyFromArray($titulos2);
$sheet->getStyle($tablaInformacionBasica)->applyFromArray($infor);
$sheet->getStyle($tituloAportes)->applyFromArray($titulos);
$sheet->getStyle($tituloCalorias)->applyFromArray($titulos3);
$sheet->getStyle($subtituloCalorias)->applyFromArray($infor);
$sheet->getStyle($tituloKiloCalorias)->applyFromArray($titulos3);
$sheet->getStyle($subtituloKiloCalorias)->applyFromArray($infor);
$sheet->getStyle($tituloGrasaSaturada)->applyFromArray($titulos3);
$sheet->getStyle($subtituloGrasaSaturada)->applyFromArray($infor);
$sheet->getStyle($tituloGrasaPolisaturada)->applyFromArray($titulos3);
$sheet->getStyle($subtituloGrasaPolisaturada)->applyFromArray($infor);
$sheet->getStyle($tituloGrasaMonoinsaturada)->applyFromArray($titulos3);
$sheet->getStyle($subtituloGrasaMonoinsaturada)->applyFromArray($infor);
$sheet->getStyle($tituloGrasaTrans)->applyFromArray($titulos3);
$sheet->getStyle($subtituloGrasaTrans)->applyFromArray($infor);
$sheet->getStyle($tituloFibra)->applyFromArray($titulos3);
$sheet->getStyle($subtituloFibra)->applyFromArray($infor);
$sheet->getStyle($tituloAzucares)->applyFromArray($titulos3);
$sheet->getStyle($subtituloAzucares)->applyFromArray($infor);
$sheet->getStyle($tituloProteinas)->applyFromArray($titulos3);
$sheet->getStyle($subtituloProteinas)->applyFromArray($infor);
$sheet->getStyle($tituloColesterol)->applyFromArray($titulos3);
$sheet->getStyle($subtituloColesterol)->applyFromArray($infor);
$sheet->getStyle($tituloSodio)->applyFromArray($titulos3);
$sheet->getStyle($subtituloSodio)->applyFromArray($infor);
$sheet->getStyle($tituloZinc)->applyFromArray($titulos3);
$sheet->getStyle($subtituloZin)->applyFromArray($infor);
$sheet->getStyle($tituloCalcio)->applyFromArray($titulos3);
$sheet->getStyle($subtituloCalcio)->applyFromArray($infor);
$sheet->getStyle($tituloHierro)->applyFromArray($titulos3);
$sheet->getStyle($subtituloHierro)->applyFromArray($infor);
$sheet->getStyle($tituloVitaminaA)->applyFromArray($titulos3);
$sheet->getStyle($subtituloVitaminaA)->applyFromArray($infor);
$sheet->getStyle($tituloVitaminaC)->applyFromArray($titulos3);
$sheet->getStyle($subtituloVitaminaC)->applyFromArray($infor);
$sheet->getStyle($tituloVitaminaB)->applyFromArray($titulos3);
$sheet->getStyle($subtituloVitaminaB)->applyFromArray($infor);
$sheet->getStyle($tituloVitaminaB2)->applyFromArray($titulos3);
$sheet->getStyle($subtituloVitaminaB2)->applyFromArray($infor);
$sheet->getStyle($tituloVitaminaB3)->applyFromArray($titulos3);
$sheet->getStyle($subtituloVitaminaB3)->applyFromArray($infor);
$sheet->getStyle($tituloAcidoFolico)->applyFromArray($titulos3);
$sheet->getStyle($subtituloAcidoFolico)->applyFromArray($infor);
$sheet->getStyle($tituloReferencia)->applyFromArray($titulos3);
$sheet->getStyle($subtituloReferencia)->applyFromArray($infor);
$sheet->getStyle($tituloReferenciaCodigo)->applyFromArray($titulos3);
$sheet->getStyle($subtituloReferenciaCodigo)->applyFromArray($infor);

$writer = new Xlsx($spreadsheet);
$writer->setIncludeCharts(TRUE);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Producto.xlsx"');
$writer->save('php://output','Producto.xlsx');