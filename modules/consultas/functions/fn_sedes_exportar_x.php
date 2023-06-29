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
// exit(var_dump($_GET));
$mes = ($_GET["mes"] != "") ? $Link->real_escape_string($_GET["mes"]) : "";
$zona = ($_GET["zona"] != "") ? $Link->real_escape_string($_GET["zona"]) : "";

// Se crea una condicion para el caso que el usuario sea de tipo rector
$condicionRector = "";
if ($_SESSION['perfil'] == "6" && $_SESSION['num_doc'] != '') {
	$consultaInstituciones = "SELECT codigo_inst FROM instituciones WHERE cc_rector = " . $_SESSION['num_doc'] . " LIMIT 1;";
	$respuestaInstituciones = $Link->query($consultaInstituciones) or die ('Error al consultar la institución' . mysqli_error($Link));
	if ($respuestaInstituciones->num_rows > 0) {
		$dataInstituciones = $respuestaInstituciones->fetch_assoc();
		$codigoIntitucion = $dataInstituciones['codigo_inst'];
	}
	$condicionRector = " AND enr.cod_inst = " . $codigoIntitucion . " ";
}

// Se crea una condicion para el caso que el usuario sea de tipo coordinador
$condicionCoordinador = '';
if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != '') {
  	$codigoSedes = "";
  	$documentoCoordinador = $_SESSION['num_doc'];
  	$consultaCodigoSedes = "SELECT cod_sede FROM sedes$periodo_actual WHERE id_coordinador = $documentoCoordinador;";
	$respuestaCodigoSedes = $Link->query($consultaCodigoSedes) or die('Error al consultar el código de la sede ' . mysqli_error($Link));
	if ($respuestaCodigoSedes->num_rows > 0) {
		$codigoInstitucion = '';
		while ($dataCodigoSedes = $respuestaCodigoSedes->fetch_assoc()) {
			$codigoSedeRow = $dataCodigoSedes['cod_sede'];
			$consultaCodigoInstitucion = "SELECT cod_inst FROM sedes$periodo_actual WHERE cod_sede = $codigoSedeRow;";
			$respuestaCodigoInstitucion = $Link->query($consultaCodigoInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
			if ($respuestaCodigoInstitucion->num_rows > 0) {
				$dataCodigoInstitucion = $respuestaCodigoInstitucion->fetch_assoc();
				$codigoInstitucionRow = $dataCodigoInstitucion['cod_inst'];
				if ($codigoInstitucionRow == $codigoInstitucion || $codigoInstitucion == '') {
					$codigoSedes .= "'$codigoSedeRow'".",";
					$codigoInstitucion = $codigoInstitucionRow; 
				}
			}
		}
	}
	$codigoSedes = substr($codigoSedes, 0 , -1);
	$condicionCoordinador = " AND enr.cod_sede IN ($codigoSedes) ";
}

$titulos_columnas = [
						"Municipio", 
						"Código Institución", 
						"Nombre Institución", 
						"Código Sede", 
						"Nombre Sede",
                        "tipo_complem", 
						"Número hoja", 
						"Raciones", 
					];

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

    $consultaDias = " SELECT    D1, D2, D3, D4, D5,
                                D6, D7, D8, D9, D10,
                                D11, D12, D13, D14, D15,
                                D16, D17, D18, D19, D20,
                                D21, D22, D23, D24, D25,
                                D26, D27, D28, D29, D30, D31 
                            FROM planilla_dias WHERE mes = '" .$mes. "' ";
    $respuestaDias = $Link->query($consultaDias) or die ('Error ln 98');
    $sumatoriaDias = '';
    if ($respuestaDias->num_rows > 0) {
        $dataDias = $respuestaDias->fetch_assoc();
        foreach ($dataDias as $key => $value) {
            if ($value != '') {
                $sumatoriaDias .= $key . " + ";
            }
        }
        $sumatoriaDias = trim($sumatoriaDias, ' + ');
        $fila = 2;
        $consultaMunicipios = " SELECT DISTINCT(cod_mun_sede) AS  cod_mun_res, Ciudad 
                                FROM sedes$periodo_actual s 
                                INNER JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE 
                                WHERE s.Zona_Pae = '$zona' "; 
        $respuestaMunicipios = $Link->query($consultaMunicipios) or die ('Error ln 133');
        if ($respuestaMunicipios->num_rows > 0) {
            while ($dataMunicipios = $respuestaMunicipios->fetch_assoc()) {
                $ciudad = $dataMunicipios['Ciudad'];
                $consultaComplementos = " SELECT DISTINCT(tipo_complem) AS tipo_complem FROM entregas_res_$mes$periodo_actual WHERE cod_mun_res = " .$dataMunicipios['cod_mun_res']. " ORDER BY tipo_complem ";
                $respuestaComplementos = $Link->query($consultaComplementos) or die ('Error ln 119');
                if ($respuestaComplementos->num_rows > 0) {
                    while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
                        $complemento = $dataComplementos['tipo_complem'];
                        $consultaSedes = " SELECT DISTINCT(e.cod_sede) 
                                            FROM entregas_res_$mes$periodo_actual e
                                            INNER JOIN sedes$periodo_actual s ON e.cod_sede = s.cod_sede
                                            WHERE e.cod_mun_res = " .$dataMunicipios['cod_mun_res']. " 
                                            AND e.tipo_complem = '" .$dataComplementos['tipo_complem']. "' 
                                            AND s.Zona_Pae = '" .$zona. "'
                                            ORDER BY e.nom_inst, e.nom_sede "; 
                        $respuestaSedes = $Link->query($consultaSedes) or die ('Error ln 124');
                        while ($dataSedes = $respuestaSedes->fetch_assoc()) {
                            $consulta = " SELECT    '$ciudad' AS Ciudad, 
                                                    cod_inst, 
                                                    nom_inst, 
                                                    cod_sede, 
                                                    nom_sede, 
                                                    '$complemento' AS tipo_complem,
                                                    (   $sumatoriaDias    
                                                    ) AS suma 
                                            FROM  entregas_res_$mes$periodo_actual      
                                            WHERE cod_sede = '" .$dataSedes['cod_sede']. "' AND tipo_complem = '" .$complemento. "' 
                                            ORDER BY cod_grado, nom_grupo, ape1, ape2, nom1, nom2 
                                            "; 
                            $respuesta = $Link->query($consulta) or die ('Error ln 141');
                            if ($respuesta->num_rows > 0) {
                                $hoja = 1;
                                $numero = 0;
                                $auxSuma = 0;
                                while ($data = $respuesta->fetch_assoc()) {
                                    $ciudad = $data['Ciudad'];
                                    $cod_inst = $data['cod_inst'];
                                    $nom_inst = $data['nom_inst'];
                                    $cod_sede = $data['cod_sede'];
                                    $nom_sede = $data['nom_sede'];
                                    $tipo_complem = $data['tipo_complem'];
                                    $auxSuma += $data['suma'];
                                    $numero++;
                                    $columna_entregas = 'A';
                                    if ($numero == 25) {
                                        $archivo->setCellValue($columna_entregas.$fila, $ciudad);
                                        $columna_entregas++;
                                        $archivo->setCellValue($columna_entregas.$fila, $cod_inst);
                                        $columna_entregas++;
                                        $archivo->setCellValue($columna_entregas.$fila, $nom_inst);
                                        $columna_entregas++;
                                        $archivo->setCellValue($columna_entregas.$fila, $cod_sede);
                                        $columna_entregas++;
                                        $archivo->setCellValue($columna_entregas.$fila, $nom_sede);
                                        $columna_entregas++;
                                        $archivo->setCellValue($columna_entregas.$fila, $tipo_complem);
                                        $columna_entregas++;
                                        $archivo->setCellValue($columna_entregas.$fila, $hoja);
                                        $columna_entregas++;
                                        $archivo->setCellValue($columna_entregas.$fila, $auxSuma);
                                        $auxSuma = 0;
                                        $hoja++;
                                        $fila++;
                                        $numero = 0;
                                    }
                                }
                                if ($numero > 0) {
                                    $archivo->setCellValue($columna_entregas.$fila, $ciudad);
                                    $columna_entregas++;
                                    $archivo->setCellValue($columna_entregas.$fila, $cod_inst);
                                    $columna_entregas++;
                                    $archivo->setCellValue($columna_entregas.$fila, $nom_inst);
                                    $columna_entregas++;
                                    $archivo->setCellValue($columna_entregas.$fila, $cod_sede);
                                    $columna_entregas++;
                                    $archivo->setCellValue($columna_entregas.$fila, $nom_sede);
                                    $columna_entregas++;
                                    $archivo->setCellValue($columna_entregas.$fila, $tipo_complem);
                                    $columna_entregas++;
                                    $archivo->setCellValue($columna_entregas.$fila, $hoja);
                                    $columna_entregas++;
                                    $archivo->setCellValue($columna_entregas.$fila, $auxSuma);
                                    $auxSuma = 0;
                                    $hoja++;
                                    $fila++;
                                    $numero = 0;
                                }
                            }                
                        }
                    }
                }
            }
        }
    
        foreach(range("A", "Z") as $columna2) {
            $archivo->getColumnDimension($columna2)->setAutoSize(true);
        }
    // exit();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=Entregas.xlsx');
    
        $escritor = new Xlsx($excel);
        $escritor->save('php://output');
    
    }else{
        echo "no hay registros para los filtros seleccionados";
    }
    
   
