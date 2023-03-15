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
$archivo->setCellValue("B1", "Nombre institucion")->getStyle('B1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("C1", "Codigo Sede")->getStyle('C1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("D1", "Nombre Sede")->getStyle('D1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("E1", "Nombre Municipio")->getStyle('E1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("F1", "Manipuladora APS")->getStyle('F1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("G1", "Manipuladora CAJMPS")->getStyle('G1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("H1", "Manipuladora CAJMRI")->getStyle('H1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("I1", "Manipuladora CAJTRI")->getStyle('I1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("J1", "Manipuladora CAJTPS")->getStyle('J1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("K1", "Manipuladora RPC")->getStyle('J1')->applyFromArray($estilo_titulo);

$sedes = consultar_sedes($Link);
if (! empty($sedes)) {
	$fila = 2;
	foreach ($sedes as $sede) {
		$municipioNombre = '';
		$institucionNombre = '';
		$sedeNombre = '';
		if (tieneAcentos($sede->nombreMunicipio)) {
			$municipioNombre = eliminar_acentos($sede->nombreMunicipio);
		} else if (!tieneAcentos($sede->nombreMunicipio)) {
			$municipioNombre = $sede->nombreMunicipio;
		}
		// echo $municipioNombre;
		if (tieneAcentos($sede->nombreInstitucion)) {
			$institucionNombre = eliminar_acentos($sede->nombreInstitucion);
		} else if (!tieneAcentos($sede->nombreInstitucion)) {
			$institucionNombre = $sede->nombreInstitucion;
		}
		if (tieneAcentos($sede->nombreSede)) {
			$sedeNombre = eliminar_acentos($sede->nombreSede);
		} else if (!tieneAcentos($sede->nombreSede)) {
			$sedeNombre = $sede->nombreSede;
		}

		$archivo->setCellValue("A". $fila, $sede->codigoInstitucion);
		$archivo->setCellValue("B". $fila, $institucionNombre);
		$archivo->setCellValue("C". $fila, $sede->codigoSede);
		$archivo->setCellValue("D". $fila, $sedeNombre);
		$archivo->setCellValue("E". $fila, $municipioNombre);
		$fila++;
	}
}

// exit(var_dump(tieneAcentos("Roberto Garcia Peña")));
foreach(range("A","V") as $columna) { $archivo->getColumnDimension($columna)->setAutoSize(true); }

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');
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
				    WHERE sedes.estado = 1
				ORDER BY ubicacion.Ciudad, cod_inst, cod_sede";
	// exit(var_dump($consulta));			
	$respuesta = $Link->query($consulta);
	if ($respuesta->num_rows > 0) {
		while ($sede = $respuesta->fetch_object()) {
			$sedes[] = $sede;
		}

		return $sedes;
	} else { return $sedes; }
}

function tieneAcentos($string)
{
	if(preg_match('/á|é|í|ó|ú|Á|É|Í|Ó|Ú|à|è|ì|ò|ù|À|È|Ì|Ò|Ù|ñ|Ñ|ä|ë|ï|ö|ü|Ä|Ë|Ï|Ö|Ü|â|ê|î|ô|û|Â|Ê|Î|Ô|Û|ý|Ý|ÿ/', $string)===1)
		return true;
	return false;
}


function eliminar_acentos($cadena){
		
		//Reemplazamos la A y a
		$cadena = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
		$cadena
		);
 
		//Reemplazamos la E y e
		$cadena = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$cadena );
 
		//Reemplazamos la I y i
		$cadena = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$cadena );
 
		//Reemplazamos la O y o
		$cadena = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$cadena );
 
		//Reemplazamos la U y u
		$cadena = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$cadena );
 
		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$cadena
		);
		
		return $cadena;
	}