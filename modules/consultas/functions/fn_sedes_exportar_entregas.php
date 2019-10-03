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

$consulta_planilla_dias = "SELECT * FROM planilla_dias WHERE mes = '$mes';";
$respuesta_planilla_dias = $Link->query($consulta_planilla_dias) or die("Error al consulta planilla_dias: ". $Link->error);
if ($respuesta_planilla_dias->num_rows > 0) {
	while ($registro_planilla_dias = $respuesta_planilla_dias->fetch_assoc()) {
		$planilla_dias = $registro_planilla_dias;
	}
}

$consulta_planilla_semanas = "SELECT DIA AS dia FROM planilla_semanas WHERE MES = '$mes' AND SEMANA = '$semana';";
$respuesta_planilla_semanas = $Link->query($consulta_planilla_semanas) or die("Error al consulta planilla_semanas: ". $Link->error);
if ($respuesta_planilla_semanas->num_rows > 0) {
	while ($registro_planilla_semanas = $respuesta_planilla_semanas->fetch_assoc()) {
		$planilla_semanas[] = $registro_planilla_semanas;
	}
}

$consulta_semanas_mes = "SELECT DISTINCT SEMANA AS semana FROM planilla_semanas WHERE MES = '$mes';";
$respuesta_semanas_mes = $Link->query($consulta_semanas_mes) or die("Error al consulta planilla_semanas: ". $Link->error);
if ($respuesta_semanas_mes->num_rows > 0) {
	$indice_semana = 0;
	while ($registro_semanas_mes = $respuesta_semanas_mes->fetch_assoc()) {
		$indice_semana++;

		if ($registro_semanas_mes["semana"] == $semana) {
			$posicion_semana = $indice_semana;
		}
	}
}

$titulos_columnas = ["Abreviatura", "Número documento", "Primer apellido", "Segundo apellido", "Primer nombre", "Segundo nombre", "Género", "Dirección", "Teléfono", "Fecha nacimiento", "Estrato", "Sisben", "Discapacidad", "Etnia", "Poblacion victima", "Código municipio", "Nombre municipio", "Código institucion", "Nombre institución", "Código sede", "Nombre sede", "Grado", "Grupo", "Jornada", "Edad", "Residencia", "Complemento"];
$fila = 0;
$cadena_dias_entregas = "";
foreach ($planilla_dias as $clave_dia => $valor_dia) {
	if ($fila > 2) {
		foreach ($planilla_semanas as $clave_semana => $valor_semana) {
			if ($valor_dia == $valor_semana["dia"]) {
				$titulos_columnas[] = $clave_dia;
				$cadena_dias_entregas .= "IF($clave_dia = 1, 'X', '') AS $clave_dia ,";
			}
		}
	}

	$fila++;
}

$consulta_entregas = "SELECT
	tdc.Abreviatura AS abreviatura,
	enr.num_doc AS numero_documento,
	enr.ape1 AS primer_apellido,
	enr.ape2 AS segundo_apellido,
	enr.nom1 AS primer_nombre,
	enr.nom2 AS segundo_nombre,
	enr.genero AS genero,
	enr.dir_res AS direccion_residencia,
	enr.telefono AS telefono,
	enr.fecha_nac AS fecha_nacimiento,
	est.nombre AS nombre_estrato,
	enr.sisben AS sisben,
	dis.nombre AS nombre_discapacidad,
	etn.DESCRIPCION AS nombre_etnia,
	pvc.nombre AS nombre_poblacion_victima,
	enr.cod_inst AS codigo_institucion,
	sed.nom_inst AS nombre_institucion,
	enr.cod_sede AS codigo_sede,
	sed.nom_sede AS nombre_sede,
	sed.cod_mun_sede AS codigo_municipio,
	ubi.Departamento AS nombre_municipio,
	enr.cod_grado AS grado,
	enr.nom_grupo AS grupo,
	jor.nombre AS jornada,
	enr.edad AS edad,
	if(enr.zona_res_est = 1, 'Urbano', 'Rural') AS residencia,
	enr.tipo_complem$posicion_semana AS complemento, ".
	trim($cadena_dias_entregas, ", ") .
" FROM entregas_res_$mes$periodo_actual enr
INNER JOIN tipodocumento tdc ON tdc.id = enr.tipo_doc
LEFT JOIN estrato est ON est.id = enr.cod_estrato
INNER JOIN discapacidades dis ON dis.id = enr.cod_discap
LEFT JOIN etnia etn ON etn.id = enr.etnia
LEFT JOIN pobvictima pvc ON pvc.id = enr.cod_pob_victima
INNER JOIN sedes$periodo_actual sed ON sed.cod_sede = enr.cod_sede
INNER JOIN jornada jor ON jor.id = enr.cod_jorn_est
INNER JOIN ubicacion ubi ON ubi.CodigoDANE = sed.cod_mun_sede;"; echo $consulta_entregas;
$respuesta_entregas = $Link->query($consulta_entregas) or die("Error al consultar prioriozacion$semana: ". $Link->error);
if ($respuesta_entregas->num_rows > 0){
	$excel = new Spreadsheet();
	$archivo = $excel->getActiveSheet();

	$estilos_titulos = [
  'font'  => [
      'bold'  => true,
      'color' => ['rgb' => '000000'],
      'size'  => 11,
      'name'  => 'Calibri'
  ]];

	$columna = "A";

  for ($i = 0; $i < count($titulos_columnas); $i++) {
  	$archivo->setCellValue($columna ."1", $titulos_columnas[$i])->getStyle($columna ."1")->applyFromArray($estilos_titulos);
  	$columna++;
  }

	$fila = 2;
	while($registros_entregas = $respuesta_entregas->fetch_assoc()){
		$columna_entregas = "A";
		foreach ($registros_entregas as $clave_entregas => $valor_entregas) {
			$archivo->setCellValue($columna_entregas . $fila, $valor_entregas);

			$columna_entregas++;
		}

		$fila++;
	}

	foreach(range("A", "Z") as $columna2) {
    $archivo->getColumnDimension($columna2)->setAutoSize(true);
	}

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=Entregas.xlsx');

	$escritor = new Xlsx($excel);
	$escritor->save('php://output');
} else {
	echo "no hay registros para los filtros seleccionados";
}
