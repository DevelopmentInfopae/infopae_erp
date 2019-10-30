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

$archivo->setCellValue("A1", "Código institución")->getStyle('A1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("B1", "Nombre institución")->getStyle('B1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("C1", "Código Sede")->getStyle('C1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("D1", "Nombre Sede")->getStyle('D1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("E1", "Nombre Municipio")->getStyle('E1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("F1", "Manipuladora APS")->getStyle('F1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("G1", "Manipuladora CAJMPS")->getStyle('G1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("H1", "Manipuladora CAJMRI")->getStyle('H1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("I1", "Manipuladora CAJTRI")->getStyle('I1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("J1", "Manipuladora CAJTPS")->getStyle('J1')->applyFromArray($estilo_titulo);

$sedes = consultar_sedes($Link);
if (! empty($sedes)) {
	$fila = 2;
	foreach ($sedes as $sede) {
		$archivo->setCellValue("A". $fila, $sede->codigoInstitucion);
		$archivo->setCellValue("B". $fila, $sede->nombreInstitucion);
		$archivo->setCellValue("C". $fila, $sede->codigoSede);
		$archivo->setCellValue("D". $fila, $sede->nombreSede);
		$archivo->setCellValue("E". $fila, $sede->nombreMunicipio);
		$fila++;
	}
}

foreach(range("A","V") as $columna) { $archivo->getColumnDimension($columna)->setAutoSize(true); }

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
header('Content-Disposition: attachment;filename=Manipuladoras.csv');

$escritor = new Csv($excel);
$escritor->save('php://output');


function consultar_sedes($Link) {
	$periodoActual = $_SESSION["periodoActual"];
	$sedes = [];
	$consulta = "SELECT
					cod_inst AS codigoInstitucion,
				    nom_inst AS nombreInstitucion,
				    cod_sede AS codigoSede,
				    nom_sede AS nombreSede,
					ubicacion.Ciudad AS nombreMunicipio
				FROM
				    sedes$periodoActual AS sedes
				    INNER JOIN ubicacion AS ubicacion ON ubicacion.CodigoDANE = sedes.cod_mun_sede
				ORDER BY nombreInstitucion, nombreSede";
	$respuesta = $Link->query($consulta);
	if ($respuesta->num_rows > 0) {
		while ($sede = $respuesta->fetch_object()) {
			$sedes[] = $sede;
		}

		return $sedes;
	} else { return $sedes; }
}