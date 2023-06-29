<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require '../../../vendor/autoload.php';

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

$periodo_actual = $_SESSION["periodoActual"];
$mes = ($_GET["mes"] != "") ? $Link->real_escape_string($_GET["mes"]) : "";
$semana = ($_GET["semana"] != "") ? $Link->real_escape_string($_GET["semana"]) : "";
$region = isset($_GET["region"]) ? $_GET["region"] : false;

$resComp = $Link->query(" SELECT CODIGO FROM tipo_complemento ORDER BY CODIGO ");
if ($resComp->num_rows > 0) {
	while ($dataComp = $resComp->fetch_object()) {
		$complementos[] = $dataComp;
	}
}
$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];

$conComp = '';
foreach ($complementos as $key => $value) {
	$conComp .= "pri.".$value->CODIGO.", ";
	for ($i=1; $i<=$cantGruposEtarios; $i++) { 
		$conComp .= "pri.Etario".$i."_".$value->CODIGO.", ";
	}
}
$conComp = trim($conComp, ", ");

$consulta_priorizacion = "SELECT
							sed.cod_mun_sede,
							sed.sector AS sector,
							ubi.region AS region,
							ubi.Ciudad AS ciudad,
							sed.cod_inst AS codigo_institucion,
							sed.nom_inst AS nombre_institucion,
							sed.cod_sede AS codigo_sede,
							sed.nom_sede AS nombre_sede,
							pri.cant_Estudiantes AS cantidad_estudiante,
							$conComp
						FROM priorizacion$semana pri
						INNER JOIN sedes$periodo_actual sed ON sed.cod_sede = pri.cod_sede
						INNER JOIN ubicacion ubi ON ubi.CodigoDANE = sed.cod_mun_sede";

// exit(var_dump($consulta_priorizacion));

$respuesta_priorizacion = $Link->query($consulta_priorizacion) or die("Error al consultar prioriozacion$semana: ". $Link->error);
if ($respuesta_priorizacion->num_rows > 0){
	$fila = 2;

	$excel = new Spreadsheet();
	$archivo = $excel->getActiveSheet();

	$estilos_titulos = [
  		'font'  => [
      		'bold'  => true,
      		'color' => ['rgb' => '000000'],
      		'size'  => 11,
      		'name'  => 'Calibri'
  		]
	];

	$archivo->setCellValue("A1", "Ciudad")->getStyle('A1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("B1", "Región")->getStyle('B1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("C1", "Zona")->getStyle('C1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("D1", "Código institución")->getStyle('D1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("E1", "Nombre institución")->getStyle('E1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("F1", "Código sede")->getStyle('F1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("G1", "Nombre sede")->getStyle('G1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("H1", "Cantidad estudiantes")->getStyle('H1')->applyFromArray($estilos_titulos);
	$letra = 'I';
	foreach ($complementos as $key => $value) {
		$archivo->setCellValue($letra."1", $value->CODIGO)->getStyle($letra.'1')->applyFromArray($estilos_titulos);
		$letra++;
		for ($i=1; $i<=$cantGruposEtarios; $i++) { 
			$archivo->setCellValue($letra."1", "Grupo etario ".$i." ".$value->CODIGO)->getStyle($letra.'1')->applyFromArray($estilos_titulos);
			$letra++;
		}
	}

	while($registros_priorizacion = $respuesta_priorizacion->fetch_assoc()){
		$archivo->setCellValue("A". $fila, $registros_priorizacion["ciudad"]);
		$archivo->setCellValue("B". $fila, $registros_priorizacion["region"]);
		$archivo->setCellValue("C". $fila, $registros_priorizacion["sector"] == 1 ? "Rural" : "Urbano");
		$archivo->setCellValueExplicit("D". $fila, $registros_priorizacion["codigo_institucion"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$archivo->setCellValue("E". $fila, $registros_priorizacion["nombre_institucion"]);
		$archivo->setCellValueExplicit("F". $fila, $registros_priorizacion["codigo_sede"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$archivo->setCellValue("G". $fila, $registros_priorizacion["nombre_sede"]);
		$archivo->setCellValue("H". $fila, $registros_priorizacion["cantidad_estudiante"]);
		$letra = "I";
		foreach ($complementos as $key => $value) {
			$archivo->setCellValue($letra. $fila, $registros_priorizacion[$value->CODIGO]);
			$letra++;
			for ($i=1; $i<=$cantGruposEtarios; $i++) { 
				$archivo->setCellValue($letra. $fila, $registros_priorizacion["Etario".$i."_".$value->CODIGO]);
				$letra++;
			}
		}
		$priorizaciones[] = $registros_priorizacion;
		$fila++;
	}

	foreach(range("A","AB") as $columna) {
    $archivo->getColumnDimension($columna)->setAutoSize(true);
	}

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=Priorizaciones.xlsx');

	$escritor = new Xlsx($excel);
	$escritor->save('php://output');
} else {
	echo "no hay registros para los filtros seleccionados";
}
