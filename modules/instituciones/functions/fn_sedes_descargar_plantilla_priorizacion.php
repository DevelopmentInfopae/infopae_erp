<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
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

$excel = new Spreadsheet();
$archivo = $excel->getActiveSheet();

$estilo_titulo = [
	'font'  => [
      	'bold'  => true,
      	'color' => ['rgb' => '000000'],
      	'size'  => 11,
      	'name'  => 'Calibri'
    ]
];

$archivo->setCellValue("A1", "Codigo institucion")->getStyle('A1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("B1", "Codigo sede")->getStyle('B1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("C1", "cant_Estudiantes")->getStyle('C1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("D1", "num_est_focalizados")->getStyle('D1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("E1", "num_est_activos")->getStyle('E1')->applyFromArray($estilo_titulo);

$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
$consultaComplementos = "SELECT CODIGO FROM tipo_complemento ";
$respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complementos' . mysqli_error($Link));
if ($respuestaComplementos->num_rows > 0) {
	while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
		$complementos[] = $dataComplementos['CODIGO']; 
	}
}

$letra = 'E';
$columnas = '';
foreach ($complementos as $key => $value) {
	$letra++;
	$archivo->setCellValue($letra.'1', $value)->getStyle($letra.'1')->applyFromArray($estilo_titulo);	
}

for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
	foreach ($complementos as $key1 => $value1) {
		$letra++;
		$columnas = "Etario".$i."_".$value1;
		$archivo->setCellValue($letra.'1', $columnas)->getStyle($letra.'1')->applyFromArray($estilo_titulo);	
	}	
}


// exit(var_dump(tieneAcentos("Roberto Garcia Peña")));
foreach(range("A","AZ") as $columna) { $archivo->getColumnDimension($columna)->setAutoSize(true); }

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');
header('Content-Disposition: attachment;filename=Plantilla_Priorizacion.csv');

$escritor = new Csv($excel);
$escritor->save('php://output');
