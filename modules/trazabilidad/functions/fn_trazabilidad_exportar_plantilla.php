<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require '../../../vendor/autoload.php';
ini_set('memory_limit','6000M');

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

if ($tipo == 1 || $tipo == 3) {
	// exit(var_dump($semana));
	$condicionTipo = '';
	$fileName = '';
	if ($tipo == 1) {
		$condicionTipo = " WHERE d.semana = '$semana' ";
		$fileName = " Plantilla de trazabilidad RUTAS semana $semana.xlsx ";
	}elseif ($tipo == 3) {
		$condicionTipo = ($semana != '') ? " WHERE d.semana = '$semana' " : "";
		if ($semana != '') {
			$fileName = " Informe Trazabilidad Ruta semana " .$semana. " de ". $nombreMes[$mes]. ".xlsx";
		}else{
			$fileName = " Informe Trazabilidad Ruta " .$nombreMes[$mes].".xlsx ";
		}
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
								"Tipo Transporte", 
								"Placa", 
								"Responsable Recibe", 
								"Fecha Despacho"
							];

	$consultaProductos = " SELECT p.Id, 
										p.Documento, 
										p.Numero, 
										p.BodegaDestino, 
										p.TipoTransporte, 
										p.Placa, 
										p.ResponsableRecibe, 
										d.Semana, 
										d.Tipo_Complem, 
										s.cod_inst, 
										s.nom_inst, 
										s.nom_sede, 
										u.Ciudad,
										p.fecha_despacho
									FROM productosmov$mes$periodo_actual p 
									INNER JOIN despachos_enc$mes$periodo_actual d on (p.numero=d.Num_Doc) 
									INNER JOIN sedes$periodo_actual s on (p.BodegaDestino=s.cod_sede) 
									INNER JOIN ubicacion u on (s.cod_mun_sede=u.CodigoDANE) 
									$condicionTipo
									ORDER BY p.Id
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
			$productos[] = $dataProductos;
		}

		foreach ($productos as $key => $value) {
			$columna = 'A';
			$archivo->setCellValue($columna.$fila, $value['Id']);
			$columna++;
			$archivo->setCellValue($columna.$fila, $value['Documento']);
			$columna++;
			$archivo->setCellValue($columna.$fila, $value['Numero']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['Semana']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['Tipo_Complem']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['Ciudad']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['cod_inst']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['nom_inst']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['BodegaDestino']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['nom_sede']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, ($tipo == 1) ? "" : $value['TipoTransporte']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, ($tipo == 1) ? "" : $value['Placa']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, ($tipo == 1) ? "" : $value['ResponsableRecibe']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, ($tipo == 1) ? "" : $value['fecha_despacho']);		
			$columna++;
			$fila++;
		}

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
	$condicionTipo = '';
	$fileName = "";
	if ($tipo == 2) {
		$condicionTipo = " WHERE d.semana = '$semana' ";
		$fileName = " Plantilla de trazabilidad DETALLE semana $semana.xlsx";
	}elseif ($tipo == 4) {
		$condicionTipo = ($semana != '') ? " WHERE d.semana = '$semana' " : "";
		if ($semana != '') {
			$fileName = " Informe Trazabilidad semana " .$semana. " de " .$nombreMes[$mes]. ".xlsx";
		}else{
			$fileName = " Informe Trazabilidad " .$nombreMes[$mes]. ".xlsx";
		}
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

	$consultaProductos = " SELECT p.Id, 
											p.Documento, 
											p.Numero, 
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
											p.observacion, 
											p.BodegaDestino, 
											d.Semana, 
											d.Tipo_Complem, 
											s.cod_inst, 
											s.nom_inst, 
											s.nom_sede, 
											u.Ciudad 
										FROM productosmovdet$mes$periodo_actual p 
										INNER JOIN despachos_enc$mes$periodo_actual d on (p.numero=d.Num_Doc) 
										INNER JOIN sedes$periodo_actual s on (p.BodegaDestino=s.cod_sede) 
										INNER JOIN ubicacion u on (s.cod_mun_sede=u.CodigoDANE) 
										$condicionTipo
										ORDER BY p.Id
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
			$productos[] = $dataProductos;
		}

		foreach ($productos as $key => $value) {
			$columna = 'A';
			$archivo->setCellValue($columna.$fila, $value['Id']);
			$columna++;
			$archivo->setCellValue($columna.$fila, $value['Documento']);
			$columna++;
			$archivo->setCellValue($columna.$fila, $value['Numero']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['Semana']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['Tipo_Complem']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['Ciudad']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['cod_inst']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['nom_inst']);
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['BodegaDestino']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['nom_sede']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['CodigoProducto']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['Descripcion']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['Cantidad']);		
			$columna++;		
			$archivo->setCellValue($columna.$fila, $value['Umedida']);		
			$columna++;			
			$archivo->setCellValue($columna.$fila, ($tipo == 2 ) ? "" : $value['Lote']);		
			$columna++;			
			$archivo->setCellValue($columna.$fila, ($tipo == 2 ) ? "" : $value['FechaVencimiento']);		
			$columna++;			
			$archivo->setCellValue($columna.$fila, ($tipo == 2 ) ? "" : $value['marca']);		
			$columna++;			
			$archivo->setCellValue($columna.$fila, ($tipo == 2 ) ? "" : $value['fecha_sacrificio']);		
			$columna++;			
			$archivo->setCellValue($columna.$fila, ($tipo == 2 ) ? "" : $value['fecha_empaque']);		
			$columna++;			
			$archivo->setCellValue($columna.$fila, ($tipo == 2 ) ? "" : $value['codigo_interno']);		
			$columna++;			
			$archivo->setCellValue($columna.$fila, ($tipo == 2 ) ? "" : $value['observacion']);		
			$columna++;
			$fila++;
		}

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
