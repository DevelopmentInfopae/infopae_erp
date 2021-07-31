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
pri.APS AS aps,
pri.Etario1_APS AS grupo_etario_1_aps,
pri.Etario2_APS AS grupo_etario_2_aps,
pri.Etario3_APS AS grupo_etario_3_aps,
pri.CAJMRI AS cajmri,
pri.Etario1_CAJMRI AS grupo_etario_1_cajmri,
pri.Etario2_CAJMRI AS grupo_etario_2_cajmri,
pri.Etario3_CAJMRI AS grupo_etario_3_cajmri,
pri.CAJTRI AS cajtri,
pri.Etario1_CAJTRI AS grupo_etario_1_cajtri,
pri.Etario2_CAJTRI AS grupo_etario_2_cajtri,
pri.Etario3_CAJTRI AS grupo_etario_3_cajtri,
pri.CAJMPS AS cajmps,
pri.Etario1_CAJMPS AS grupo_etario_1_cajmps,
pri.Etario2_CAJMPS AS grupo_etario_2_cajmps,
pri.Etario3_CAJMPS AS grupo_etario_3_cajmps,
pri.RPC AS rpc,
pri.Etario1_RPC AS grupo_etario_1_rpc,
pri.Etario2_RPC AS grupo_etario_2_rpc,
pri.Etario3_RPC AS grupo_etario_3_rpc
FROM priorizacion$semana pri
INNER JOIN sedes$periodo_actual sed ON sed.cod_sede = pri.cod_sede
INNER JOIN ubicacion ubi ON ubi.CodigoDANE = sed.cod_mun_sede
WHERE sed.estado = 1
";

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
  ]];


	$archivo->setCellValue("A1", "Ciudad")->getStyle('A1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("B1", "Región")->getStyle('B1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("C1", "Zona")->getStyle('C1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("D1", "Código institución")->getStyle('D1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("E1", "Nombre institución")->getStyle('E1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("F1", "Código sede")->getStyle('F1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("G1", "Nombre sede")->getStyle('G1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("H1", "Cantidad estudiantes")->getStyle('H1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("I1", "APS")->getStyle('I1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("J1", "Grupo etario 1 APS")->getStyle('J1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("K1", "Grupo etario 2 APS")->getStyle('K1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("L1", "Grupo etario 3 APS")->getStyle('L1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("M1", "CAJMRI")->getStyle('M1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("N1", "Grupo etario 1 CAJMRI")->getStyle('N1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("O1", "Grupo etario 2 CAJMRI")->getStyle('O1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("P1", "Grupo etario 3 CAJMRI")->getStyle('P1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("Q1", "CAJTRI")->getStyle('Q1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("R1", "Grupo etario 1 CAJTRI")->getStyle('R1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("S1", "Grupo etario 2 CAJTRI")->getStyle('S1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("T1", "Grupo etario 3 CAJTRI")->getStyle('T1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("U1", "CAJMPS")->getStyle('U1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("V1", "Grupo etario 1 CAJMPS")->getStyle('V1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("W1", "Grupo etario 2 CAJMPS")->getStyle('W1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("X1", "Grupo etario 3 CAJMPS")->getStyle('X1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("Y1", "RPC")->getStyle('Y1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("Z1", "Grupo etario 1 RPC")->getStyle('Z1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("AA1", "Grupo etario 2 RPC")->getStyle('AA1')->applyFromArray($estilos_titulos);
	$archivo->setCellValue("AB1", "Grupo etario 3 RPC")->getStyle('AB1')->applyFromArray($estilos_titulos);

	while($registros_priorizacion = $respuesta_priorizacion->fetch_assoc()){
		$archivo->setCellValue("A". $fila, $registros_priorizacion["ciudad"]);
		$archivo->setCellValue("B". $fila, $registros_priorizacion["region"]);
		$archivo->setCellValue("C". $fila, $registros_priorizacion["sector"] == 1 ? "Rural" : "Urbano");
		$archivo->setCellValueExplicit("D". $fila, $registros_priorizacion["codigo_institucion"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$archivo->setCellValue("E". $fila, $registros_priorizacion["nombre_institucion"]);
		$archivo->setCellValueExplicit("F". $fila, $registros_priorizacion["codigo_sede"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$archivo->setCellValue("G". $fila, $registros_priorizacion["nombre_sede"]);
		$archivo->setCellValue("H". $fila, $registros_priorizacion["cantidad_estudiante"]);
		$archivo->setCellValue("I". $fila, $registros_priorizacion["aps"]);
		$archivo->setCellValue("J". $fila, $registros_priorizacion["grupo_etario_1_aps"]);
		$archivo->setCellValue("K". $fila, $registros_priorizacion["grupo_etario_2_aps"]);
		$archivo->setCellValue("L". $fila, $registros_priorizacion["grupo_etario_3_aps"]);
		$archivo->setCellValue("M". $fila, $registros_priorizacion["cajmri"]);
		$archivo->setCellValue("N". $fila, $registros_priorizacion["grupo_etario_1_cajmri"]);
		$archivo->setCellValue("O". $fila, $registros_priorizacion["grupo_etario_2_cajmri"]);
		$archivo->setCellValue("P". $fila, $registros_priorizacion["grupo_etario_3_cajmri"]);
		$archivo->setCellValue("Q". $fila, $registros_priorizacion["cajtri"]);
		$archivo->setCellValue("R". $fila, $registros_priorizacion["grupo_etario_1_cajtri"]);
		$archivo->setCellValue("S". $fila, $registros_priorizacion["grupo_etario_2_cajtri"]);
		$archivo->setCellValue("T". $fila, $registros_priorizacion["grupo_etario_3_cajtri"]);
		$archivo->setCellValue("U". $fila, $registros_priorizacion["cajmps"]);
		$archivo->setCellValue("V". $fila, $registros_priorizacion["grupo_etario_1_cajmps"]);
		$archivo->setCellValue("W". $fila, $registros_priorizacion["grupo_etario_2_cajmps"]);
		$archivo->setCellValue("X". $fila, $registros_priorizacion["grupo_etario_3_cajmps"]);
		$archivo->setCellValue("Y". $fila, $registros_priorizacion["rpc"]);
		$archivo->setCellValue("Z". $fila, $registros_priorizacion["grupo_etario_1_rpc"]);
		$archivo->setCellValue("AA". $fila, $registros_priorizacion["grupo_etario_2_rpc"]);
		$archivo->setCellValue("AB". $fila, $registros_priorizacion["grupo_etario_3_rpc"]);
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
