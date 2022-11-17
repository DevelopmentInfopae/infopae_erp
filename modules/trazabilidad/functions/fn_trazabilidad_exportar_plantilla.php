<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require_once '../../../vendor/autoload.php';
set_time_limit (0);
ini_set('memory_limit', '-1');


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
$semanaFinal = ($_GET["semanaFinal"] != "") ? $Link->real_escape_string($_GET["semanaFinal"]) : "";
$tipo = ($_GET["tipo"] != "") ? $Link->real_escape_string($_GET["tipo"]) : "";
$nombreMes = [	"01" => "ENERO",
					"02" => "FEBRERO",
					"03" => "MARZO",
					"04" => "ABRIL",
					"05" => "MAYO",
					"06" => "JUNIO",
					"07" => "JULIO",
					"08" => "AGOSTO",
					"09" => "SEPTIEMBRE",
					"10" => "OCTUBRE",
					"11" => "NOVIEMBRE",
					"12" => "DICIEMBRE" ];

$condicionTipo = " WHERE 1 = 1 AND ( ";
$consultaConsecutivoInicial = " SELECT min(CONSECUTIVO) AS minConsecutivo FROM planilla_semanas WHERE SEMANA = '$semana' ";
$respuestaConsecutivoInicial = $Link->query($consultaConsecutivoInicial) OR die ('Error al consultar el primer consecutivo LN 46');
if ($respuestaConsecutivoInicial->num_rows > 0) {
	$dataConsecutivoInicial = $respuestaConsecutivoInicial->fetch_assoc();
	$minConsecutivo = $dataConsecutivoInicial['minConsecutivo'];
	$consultaConsecutivoFinal = " SELECT max(CONSECUTIVO) AS maxConsecutivo FROM planilla_semanas WHERE SEMANA = '$semanaFinal' "; 
	$respuestaConsecutivoFinal = $Link->query($consultaConsecutivoFinal) OR die ('Error al consultar el segundo consecutivo LN 51');
	if ($respuestaConsecutivoFinal->num_rows > 0) {
		$dataConsecutivoFinal = $respuestaConsecutivoFinal->fetch_assoc();
		$maxConsecutivo = $dataConsecutivoFinal['maxConsecutivo'];
		$consultaSemanasInvolucradas = " SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE CONSECUTIVO BETWEEN $minConsecutivo AND $maxConsecutivo "; 
		$resSemanasInvolucradas = $Link->query($consultaSemanasInvolucradas) OR die ('Error al consultar las semanas involucradas Ln 56');
		if ($resSemanasInvolucradas->num_rows > 0) {
			while ($dataSemanas = $resSemanasInvolucradas->fetch_assoc()) {
				$condicionTipo .= "   ( d.semana LIKE '%" .$dataSemanas['semana']. "%' )  OR";
			}
		}
	}
}
$condicionTipo = trim($condicionTipo, 'OR');
$condicionTipo .= ' )';					

if ($tipo == 1 || $tipo == 3) { // rutas 
	$fileName = '';
	if ($tipo == 1) { // Plantilla
		$fileName = " Plantilla de trazabilidad RUTAS de semana $semana a semana $semanaFinal.xlsx ";
	}elseif ($tipo == 3) { // Informe
		$fileName = " Informe Trazabilidad Ruta " .$nombreMes[$mes]." de semana $semana a semana $semanaFinal.xlsx ";
	}
	$titulos_columnas = 	[
								"Id", 
								"Documento", 
								"Número", 
								"Semana", 
								"Tipo Complemento", 
								"Ciudad", 
								"Codigo Institución", 
								"Nombre institución", 
								"Bodega Destino", 
								"Nombre Sede", 
								"Tipo Transporte", 
								"Placa", 
								"Responsable Recibe", 
								"Fecha Despacho"
							];

	$consultaProductos = " SELECT p.Id, 
											p.Documento, 
											p.Numero, 
											d.Semana, 
											d.Tipo_Complem, 
											u.Ciudad,
											s.cod_inst,
											s.nom_inst,
											p.BodegaDestino, 
											s.nom_sede,
											p.TipoTransporte,
											p.Placa,
											p.ResponsableRecibe, 
											p.fecha_despacho							
									FROM productosmov$mes$periodo_actual p 
									INNER JOIN despachos_enc$mes$periodo_actual d on (p.numero=d.Num_Doc) 
									INNER JOIN sedes$periodo_actual s on (p.BodegaDestino=s.cod_sede) 
									INNER JOIN ubicacion u on (s.cod_mun_sede=u.CodigoDANE) 
									$condicionTipo
									ORDER BY u.Ciudad
									"; 					
	$respuestaProductos = $Link->query($consultaProductos) or die("Error al consultar: ". $Link->error);	
	if ($respuestaProductos->num_rows > 0){
		$excel = new Spreadsheet();
		$archivo = $excel->getActiveSheet();
		$estilos_titulos = [
			'font'  => [
		  		'bold'  => true,
		  		'color' => ['rgb' => '000000'],
		  		'size'  => 11,
		  		'name'  => 'Calibri'
				],
			'fill' => [
      		'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
      		'color' => ['argb' => 'c2c2c2'],
    			]
			];

		$columna = "A";
		for ($i = 0; $i < count($titulos_columnas); $i++) {
			$archivo->setCellValue($columna ."1", $titulos_columnas[$i])->getStyle($columna ."1")->applyFromArray($estilos_titulos);
			$columna++;
		}

		$fila = 2;
		while($dataProductos = $respuestaProductos->fetch_assoc()){
			if ($tipo == 1) {
				$dataProductos['Placa'] = '';
				$dataProductos['ResponsableRecibe'] = '';
				$dataProductos['fecha_despacho'] = '';
			}
			$productos[] = $dataProductos;
		}

		$archivo->fromArray($productos, null, 'A2');
		
		foreach(range("A", "Z") as $columna2) {
    		$archivo->getColumnDimension($columna2)->setAutoSize(true);
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=$fileName");

		$escritor = new Xlsx($excel);
		$escritor->save('php://output');
	} else {
		echo "no hay registros para los filtros seleccionados";
	}
}

else if ($tipo == 2 || $tipo == 4) {
	$fileName = "";
	if ($tipo == 2) { // Plantilla
		$fileName = " Plantilla de trazabilidad DETALLE de semana $semana a semana $semanaFinal.xlsx";
	}elseif ($tipo == 4) { // Informe
		$fileName = " Informe Trazabilidad de semana " .$semana. " a semana " .$semanaFinal. " mes " .$nombreMes[$mes]. ".xlsx";
	}
	$titulos_columnas = [
								"Id", 
								"Documento", 
								"Número", 
								"Semana", 
								"Tipo Complemento", 
								"Ciudad", 
								"Codigo Institución", 
								"Nombre institución", 
								"Bodega Destino", 
								"Nombre Sede", 
								"Codigo Producto", 
								"Descripción", 
								"Cantidad", 
								"Unidad de medida",
								"Lote",
								"FechaVencimiento",
								"Marca",
								"Fecha sacrificio",
								"Fecha empaque",
								"Codigo interno",
								"Observación"
							];

	$consultaProductos = " SELECT 			p.Id, 
											p.Documento, 
											p.Numero, 
											d.Semana,
											d.Tipo_Complem,
											u.Ciudad, 
											s.cod_inst, 
											s.nom_inst, 
											p.BodegaDestino,
											s.nom_sede, 
											p.CodigoProducto,
											p.Descripcion, 
											p.Cantidad, 
											p.Umedida, 
											p.Lote, 
											p.FechaVencimiento, 
											p.marca, 
											p.fecha_sacrificio, 
											p.fecha_empaque,  
											p.codigo_interno, 
											p.observacion										
										FROM productosmovdet$mes$periodo_actual p 
										INNER JOIN despachos_enc$mes$periodo_actual d on (p.numero=d.Num_Doc) 
										INNER JOIN sedes$periodo_actual s on (p.BodegaDestino=s.cod_sede) 
										INNER JOIN ubicacion u on (s.cod_mun_sede=u.CodigoDANE) 
										$condicionTipo
										ORDER BY u.Ciudad
										"; 

	$respuestaProductos = $Link->query($consultaProductos) or die("Error al consultar: ". $Link->error);	
	if ($respuestaProductos->num_rows > 0){
		$excel = new Spreadsheet();
		$archivo = $excel->getActiveSheet();
		$estilos_titulos = [
			'font'  => [
		  		'bold'  => true,
		  		'color' => ['rgb' => '000000'],
		  		'size'  => 11,
		  		'name'  => 'Calibri'
				],
			'fill' => [
      		'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
      		'color' => ['argb' => 'c2c2c2'],
    			]
			];

		$columna = "A";
		for ($i = 0; $i < count($titulos_columnas); $i++) {
			$archivo->setCellValue($columna ."1", $titulos_columnas[$i])->getStyle($columna ."1")->applyFromArray($estilos_titulos);
			$columna++;
		}

		$fila = 2;
		while($dataProductos = $respuestaProductos->fetch_assoc()){
			if ($tipo == 2) {
				$dataProductos['Lote'] = '';
				$dataProductos['FechaVencimiento'] = '';
				$dataProductos['marca'] = '';
				$dataProductos['fecha_sacrificio'] = '';
				$dataProductos['fecha_empaque'] = '';
				$dataProductos['codigo_interno'] = '';
				$dataProductos['observacion'] = '';
			}
			$productos[] = $dataProductos;
		}
		$archivo->fromArray($productos, null, 'A2');

		foreach(range("A", "Z") as $columna2) {
    		$archivo->getColumnDimension($columna2)->setAutoSize(true);
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=$fileName");

		$escritor = new Xlsx($excel);
		$escritor->save('php://output');
	} else {
		echo "no hay registros para los filtros seleccionados";
	}
}
