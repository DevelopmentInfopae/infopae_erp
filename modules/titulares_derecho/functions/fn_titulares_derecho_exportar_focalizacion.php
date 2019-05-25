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

$consulta_focalizacion = "SELECT
	tdc.Abreviatura AS abreviatura,
	foc.num_doc AS numero_documento,
	foc.ape1 AS primer_apellido,
	foc.ape2 AS segundo_apellido,
	foc.nom1 AS primer_nombre,
	foc.nom2 AS segundo_nombre,
	foc.genero AS genero,
	foc.dir_res AS direccion_residencia,
	foc.telefono AS telefono,
	foc.fecha_nac AS fecha_nacimiento,
	est.nombre AS nombre_estrato,
	foc.sisben AS sisben,
	dis.nombre AS nombre_discapacidad,
	etn.DESCRIPCION AS nombre_etnia,
	pvc.nombre AS nombre_poblacion_victima,
	foc.cod_inst AS codigo_institucion,
	sed.nom_inst AS nombre_institucion,
	foc.cod_sede AS codigo_sede,
	sed.nom_sede AS nombre_sede,
	sed.cod_mun_sede AS codigo_municipio,
	ubi.Departamento AS nombre_municipio,
	foc.cod_grado AS grado,
	foc.nom_grupo AS grupo,
	jor.nombre AS jornada,
	foc.edad AS edad,
	if(foc.zona_res_est = 1, 'Urbano', 'Rural') AS residencia,
	foc.Tipo_complemento AS complemento
FROM focalizacion$semana foc
LEFT JOIN tipodocumento tdc ON tdc.id = foc.tipo_doc
LEFT JOIN estrato est ON est.id = foc.cod_estrato
LEFT JOIN discapacidades dis ON dis.id = foc.cod_discap
LEFT JOIN etnia etn ON etn.id = foc.etnia
LEFT JOIN pobvictima pvc ON pvc.id = foc.cod_pob_victima
LEFT JOIN sedes$periodo_actual sed ON sed.cod_sede = foc.cod_sede
LEFT JOIN jornada jor ON jor.id = foc.cod_jorn_est
LEFT JOIN ubicacion ubi ON ubi.CodigoDANE = sed.cod_mun_sede;";
$respuesta_focalizacion = $Link->query($consulta_focalizacion) or die("Error al consultar focalizacion$semana: ". $Link->error);

if ($respuesta_focalizacion->num_rows > 0){
	$excel = new Spreadsheet();
	$archivo = $excel->getActiveSheet();

	$estilos_titulos = [
	  'font'  => [
      'bold' => true,
      'color' => ['rgb' => '000000'],
      'size' => 11,
      'name' => 'Calibri'
	  ]
	];

  $columna = "A";
  $titulos_columnas = ["Abreviatura", "Número documento", "Primer apellido", "Segundo apellido", "Primer nombre", "Segundo nombre", "Género", "Dirección", "Teléfono", "Fecha nacimiento", "Estrato", "Sisben", "Discapacidad", "Etnia", "Poblacion victima", "Código municipio", "Nombre municipio", "Código institucion", "Nombre institución", "Código sede", "Nombre sede", "Grado", "Grupo", "Jornada", "Edad", "Residencia", "Complemento"];
  for ($i = 0; $i < count($titulos_columnas); $i++) {
  	$archivo->setCellValue($columna ."1", $titulos_columnas[$i])->getStyle($columna ."1")->applyFromArray($estilos_titulos);
  	$columna++;
  }

	$fila = 2;
	while($registros_focalizacion = $respuesta_focalizacion->fetch_assoc()){
		$archivo->setCellValue("A". $fila, $registros_focalizacion["abreviatura"]);
		$archivo->setCellValueExplicit("B". $fila, $registros_focalizacion["numero_documento"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$archivo->setCellValue("C". $fila, $registros_focalizacion["primer_apellido"]);
		$archivo->setCellValue("D". $fila, $registros_focalizacion["segundo_apellido"]);
		$archivo->setCellValue("E". $fila, $registros_focalizacion["primer_nombre"]);
		$archivo->setCellValue("F". $fila, $registros_focalizacion["segundo_nombre"]);
		$archivo->setCellValue("G". $fila, $registros_focalizacion["genero"]);
		$archivo->setCellValue("H". $fila, $registros_focalizacion["direccion_residencia"]);
		$archivo->setCellValueExplicit("I". $fila, $registros_focalizacion["telefono"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$archivo->setCellValue("J". $fila, $registros_focalizacion["fecha_nacimiento"]);
		$archivo->setCellValue("K". $fila, $registros_focalizacion["nombre_estrato"]);
		$archivo->setCellValue("L". $fila, $registros_focalizacion["sisben"]);
		$archivo->setCellValue("M". $fila, $registros_focalizacion["nombre_discapacidad"]);
		$archivo->setCellValue("N". $fila, $registros_focalizacion["nombre_etnia"]);
		$archivo->setCellValue("O". $fila, $registros_focalizacion["nombre_poblacion_victima"]);
		$archivo->setCellValueExplicit("P". $fila, $registros_focalizacion["codigo_institucion"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$archivo->setCellValue("Q". $fila, $registros_focalizacion["nombre_institucion"]);
		$archivo->setCellValueExplicit("R". $fila, $registros_focalizacion["codigo_sede"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$archivo->setCellValue("S". $fila, $registros_focalizacion["nombre_sede"]);
		$archivo->setCellValueExplicit("T". $fila, $registros_focalizacion["codigo_municipio"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$archivo->setCellValue("U". $fila, $registros_focalizacion["nombre_municipio"]);
		$archivo->setCellValue("V". $fila, $registros_focalizacion["grado"]);
		$archivo->setCellValue("W". $fila, $registros_focalizacion["grupo"]);
		$archivo->setCellValue("X". $fila, $registros_focalizacion["jornada"]);
		$archivo->setCellValue("Y". $fila, $registros_focalizacion["edad"]);
		$archivo->setCellValue("Z". $fila, $registros_focalizacion["residencia"]);
		$archivo->setCellValue("AA". $fila, $registros_focalizacion["complemento"]);

		$fila++;
	}

	foreach(range("A", "Z") as $columna2) {
    $archivo->getColumnDimension($columna2)->setAutoSize(true);
	}

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=Focalizaciones.xlsx');

	$escritor = new Xlsx($excel);
	$escritor->save('php://output');
} else {
	echo "no hay registros para los filtros seleccionados";
}
