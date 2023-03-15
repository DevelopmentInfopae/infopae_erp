<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require '../../../vendor/autoload.php';

// definimos los parametros para el nuevo libro de excel que vamos a crear
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Layout;

$excel = new Spreadsheet();
$archivo = $excel->getActiveSheet();

$municipio = (isset($_GET['municipioExport']) && $_GET['municipioExport'] != '') ? $_GET['municipioExport'] : '';
$bodega = (isset($_GET['bodegaExport']) && $_GET['bodegaExport'] != '') ? $_GET['bodegaExport'] : '';
$complemento = (isset($_GET['complementoExport']) && $_GET['complementoExport'] != '') ? $_GET['complementoExport'] : '';

$estilo_titulo = [
	'font'  => [
      	'bold'  => true,
      	'color' => ['rgb' => '000000'],
      	'size'  => 11,
      	'name'  => 'Calibri'
    ]
];

$archivo->setCellValue("A1", "Codigo Producto")->getStyle('A1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("B1", "Nombre Producto")->getStyle('B1')->applyFromArray($estilo_titulo);
$archivo->setCellValue("C1", "Cantidad Entrante")->getStyle('C1')->applyFromArray($estilo_titulo);
if ($_SESSION['p_inventory'] == 2) {
	$archivo->setCellValue("D1", "Complemento")->getStyle('D1')->applyFromArray($estilo_titulo);
}

$productos = consultar_productos($Link, $municipio, $bodega, $complemento);
if ( !empty($productos) ) {
	if ($_SESSION['p_inventory'] == 2) {
		$fila = 2;
		foreach ($productos as $producto) {
			$productoNombre = '';
			if (tieneAcentos($producto['descripcion'])) {
				$productoNombre = eliminar_acentos($producto['descripcion']);
			} else if (!tieneAcentos($producto['descripcion'])) {
				$productoNombre = $producto['descripcion'];
			}
			// $archivo->setCellValue("A". $fila, $producto['codigo']);
			$archivo->getCell('A'.$fila)->setValueExplicit($producto['codigo'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING );
			$archivo->setCellValue("B". $fila, $productoNombre);
			$archivo->setCellValue("C". $fila, $producto['cantidad']);
			$archivo->setCellValue("D". $fila, $producto['complemento']);
			$fila++;
		}
	}
	if ($_SESSION['p_inventory'] == 1) {
		$fila = 2;
		foreach ($productos as $producto) {
			$productoNombre = '';
			if (tieneAcentos($producto['descripcion'])) {
				$productoNombre = eliminar_acentos($producto['descripcion']);
			} else if (!tieneAcentos($producto['descripcion'])) {
				$productoNombre = $producto['descripcion'];
			}
			// $archivo->setCellValue("A". $fila, $producto['codigo']);
			$archivo->getCell('A'.$fila)->setValueExplicit($producto['codigo'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING );
			$archivo->setCellValue("B". $fila, $productoNombre);
			$archivo->setCellValue("C". $fila, $producto['cantidad']);
			$fila++;
		}
	}
}

// exit(var_dump(tieneAcentos("Roberto Garcia Peña")));
foreach(range("A","V") as $columna) { $archivo->getColumnDimension($columna)->setAutoSize(true); }

$nombreArchivo = get_name_warehouse($Link, $bodega, $municipio);
// header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');
header("Content-Disposition: attachment;filename=$nombreArchivo.csv");

$escritor = new Csv($excel);
$escritor->save('php://output');

function get_name_warehouse($Link, $bodega, $municipio){
	$periodoActual = $_SESSION["periodoActual"];
	$name = '';
	$consulta = " SELECT 	u.Ciudad, 
						 	s.nom_sede
					FROM sedes$periodoActual s	
					INNER JOIN ubicacion u ON s.cod_mun_sede = u.codigoDANE
					WHERE s.cod_mun_sede = '$municipio' AND s.cod_sede = '$bodega' ";
	$respuesta = $Link->query($consulta) or die ('Error al consultar el nombre de la bodega LN 107');
	if ($respuesta->num_rows > 0) {
		$dataName = $respuesta->fetch_assoc();
		$name = $dataName['Ciudad'] ."_". $dataName['nom_sede'];
	}	
	return $name;			
}

function consultar_productos($Link, $municipio, $bodega, $complemento) {
	$periodoActual = $_SESSION["periodoActual"];
	$validacionComplemento = '';
	if ($_SESSION['p_inventory'] == 2) {
		$validacionComplemento = "e.complemento = '" .$complemento. "'";
	}
	if ($_SESSION['p_inventory'] == 1) {
		$validacionComplemento = "e.complemento = '' ";
	}
	$productos = [];
	$consulta = "SELECT
					p.Codigo AS codigo,
				    p.Descripcion AS descripcion,
                    (SELECT cantidad FROM inventarios_bodegas_det WHERE id_bodega = e.id AND  codigo = p.codigo AND $validacionComplemento ) AS cantidad,
					e.complemento
				FROM
				    productos$periodoActual AS p
				    LEFT JOIN inventarios_bodegas_det i ON p.codigo = i.codigo
					LEFT JOIN inventarios_bodegas_enc e ON e.id = i.id_bodega
                WHERE 
                    p.TipodeProducto = 'Alimento' AND p.nivel = 3 
					";
				
	$respuesta = $Link->query($consulta);
	if ($respuesta->num_rows > 0) {
		$n = 0;
		while ($dataProductos = $respuesta->fetch_assoc()) {
			$codigo = $dataProductos['codigo'];
			$productos[$n]['codigo'] = $codigo;
			$productos[$n]['descripcion'] = $dataProductos['descripcion'];
			$productos[$n]['cantidad'] = $dataProductos['cantidad'];
			$productos[$n]['complemento'] = $complemento;
			$n++;
		}
		return $productos;
	} else { return $productos; }
}

function tieneAcentos($string){
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