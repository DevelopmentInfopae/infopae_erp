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

$periodo_actual = $_SESSION["periodoActual"];

$c_sede = "SELECT
		    cod_mun_sede AS codigo_municipio,
		    Ciudad AS nombre_municipio,
		    cod_inst AS codigo_institucion,
		    nom_inst AS nombre_institucion,
		    cod_sede AS codigo,
		    nom_sede AS nombre
		FROM
		    sedes$periodo_actual s
		        INNER JOIN
		    ubicacion u ON u.CodigoDANE = s.cod_mun_sede;";
$r_sede = $Link->query($c_sede) or die("Error al consultar las sedes: ". $Link->error);
if ($r_sede->num_rows > 0){

	$excel = new Spreadsheet();
	$archivo = $excel->getActiveSheet();

	$estilos_titulos = [
  	'font'  => [
      	'bold'  => true,
      	'color' => ['rgb' => '000000'],
      	'size'  => 11,
      	'name'  => 'Calibri'
  	]];

	$archivo->setCellValue("A1", "Codigo Municipio")->getStyle('A1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("B1", 'Nombre Municipio')->getStyle('B1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("C1", "Codigo Institucion")->getStyle('C1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("D1", "Nombre institucion")->getStyle('D1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("E1", "Codigo sede")->getStyle('E1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("F1", "Nombre sede")->getStyle('F1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("G1", "Mes")->getStyle('G1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("H1", "Semana")->getStyle('H1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("I1", "Fecha desde")->getStyle('I1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("J1", "Fecha hasta")->getStyle('J1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("K1", "Horario")->getStyle('K1')->applyFromArray($estilos_titulos);

	$fila = 2;
	while($sede = $r_sede->fetch_object()){
		$archivo->setCellValue("A". $fila, $sede->codigo_municipio);
		$archivo->setCellValue("B". $fila, $sede->nombre_municipio);
		$archivo->setCellValue("C". $fila, $sede->codigo_institucion);
		$archivo->setCellValue("D". $fila, $sede->nombre_institucion);
		$archivo->setCellValue("E". $fila, $sede->codigo);
		$archivo->setCellValue("F". $fila, $sede->nombre);

		$fila++;
	}

	foreach(range("A","K") as $columna) {
    $archivo->getColumnDimension($columna)->setAutoSize(true);
	}

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
	header('Content-Disposition: attachment;filename=Plantilla_cronograma.csv');

	$escritor = new Csv($excel);
	$escritor->save('php://output');
} else {
	echo "no hay registros para los filtros seleccionados";
}
