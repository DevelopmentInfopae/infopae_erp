<?php 
require_once '../../config.php';
require_once '../../db/conexion.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$periodoActual = $_SESSION["periodoActual"];

$tipo_complemento_consulta = "SELECT * FROM tipo_complemento";
$tipo_complemento_result = $Link->query($tipo_complemento_consulta);
$tipo_complemento_datos = [];
$txt_complementos = '';
if ($tipo_complemento_result->num_rows > 0) {
	while ($tipo_complemento_data = $tipo_complemento_result->fetch_assoc()) {
		$tipo_complemento_datos[] = $tipo_complemento_data['CODIGO'];
		$txt_complementos .= "Manipuladora_".$tipo_complemento_data['CODIGO'].", ";
	}
}
$txt_complementos = trim($txt_complementos, ", ");

$sheet->setCellValue('A1', 'Municipio');
$sheet->setCellValue('B1', 'Institución');
$sheet->setCellValue('C1', 'Sede');
$letra = "D";
foreach ($tipo_complemento_datos as $key => $tipo_complemento) {
	$sheet->setCellValue($letra.'1', $tipo_complemento);
	$letra++;
}

$consulta = "SELECT ubicacion.Ciudad, instituciones.nom_inst, sedes.nom_sede, $txt_complementos FROM sedes$periodoActual as sedes
	INNER JOIN  instituciones ON instituciones.codigo_inst = sedes.cod_inst
    INNER JOIN ubicacion ON ubicacion.CodigoDANE = instituciones.cod_mun;";
$result = $Link->query($consulta) or die ('Error al consultar, revisar tabla tipo_complemento: '. mysqli_error($Link));
$nrow = 2;
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$sheet->setCellValue('A'.$nrow, $row['Ciudad']);
		$sheet->setCellValue('B'.$nrow, $row['nom_inst']);
		$sheet->setCellValue('C'.$nrow, $row['nom_sede']);
		$letra = "D";
		$total_sede = 0;
		foreach ($tipo_complemento_datos as $key => $tipo_complemento) {
			$sheet->setCellValue($letra.$nrow, $row['Manipuladora_'.$tipo_complemento]);
			$letra++;
			$total_sede += $row['Manipuladora_'.$tipo_complemento];
		}
		$sheet->setCellValue($letra.$nrow, $total_sede);
		$nrow++;
	}
}

$sheet->setCellValue($letra.'1', 'Total');
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="ManipuladorasRequeridas.xlsx"');
$writer->save('php://output','ManipuladorasRequeridas.xlsx');