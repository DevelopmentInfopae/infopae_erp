<?php

require_once '../../config.php';
require_once '../../db/conexion.php';
require '../../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Layout;

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

if (isset($_GET['semana'])) {
	$semanaA = $_GET['semana'];

$sheet->setCellValue('D5', 'Sistema de Información Tecnológico InfoPAE');
$sheet->mergeCells('D5:P5');
$sheet->setCellValue('D6', 'Información Estadística basada en la ejecución de entrega de complementos alimentarios');
$sheet->mergeCells('D6:P6');
$sheet->setCellValue('B9', 'Totales por semana');
$sheet->mergeCells('B9:P9');
$sheet->mergeCells('B2:C7');
$titulos = [
    'font' => [
        'bold' => true,
        'size'  => 12,
        'name' => 'calibrí'
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$sheet->getStyle("D5:P5")->applyFromArray($titulos);
$sheet->getStyle("D6:P6")->applyFromArray($titulos);
$sheet->getStyle("D7:P7")->applyFromArray($titulos);
$sheet->getStyle('B9:P9')->applyFromArray($titulos);
$sheet->getStyle('B2:C7')->applyFromArray($titulos);

$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath('../../upload/logotipos/infopae.png');
$drawing->setHeight(90);
$drawing->setCoordinates('B2');
$drawing->setOffsetX(25);
$drawing->setOffsetY(17);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$infor = [
    'font' => [
        'size'  => 9,
        'name' => 'calibrí'
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];


$periodoActual = $_SESSION['periodoActual'];

$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");
  $periodo = 1;
  $diasSemanas = [];
  $consDiasSemanas = "SELECT GROUP_CONCAT(DIA) AS Dias, MES, SEMANA FROM planilla_semanas WHERE CONCAT(ANO, '-', MES, '-', DIA) <= '".date('Y-m-d')."' GROUP BY SEMANA";
  $resDiasSemanas = $Link->query($consDiasSemanas);
  if ($resDiasSemanas->num_rows > 0) {
    while ($dataDiasSemanas = $resDiasSemanas->fetch_assoc()) {
      $semanasP[$periodo] = $dataDiasSemanas['SEMANA'];
      $consultaTablas = "SELECT 
                           table_name AS tabla
                          FROM 
                           information_schema.tables
                          WHERE 
                           table_schema = DATABASE() AND table_name = 'entregas_res_".$dataDiasSemanas['MES']."$periodoActual'";
      $resTablas = $Link->query($consultaTablas);
      if ($resTablas->num_rows > 0) {
        $semanaPos = $dataDiasSemanas['SEMANA'];
        $arrDias = explode(",", $dataDiasSemanas['Dias']);
        sort($arrDias);
        // print_r($arrDias);
        $diasSemanas[$dataDiasSemanas['MES']][$semanaPos] = $arrDias; //obtenemos un array ordenado del siguiente modo array[mes][semana] = array[dias]
      }
      $periodo++;
    }
  }

  $tipoComplementos = [];
  $consComplemento="SELECT * FROM tipo_complemento";
  $resComplemento = $Link->query($consComplemento);
  if ($resComplemento->num_rows > 0) {
    while ($Complemento = $resComplemento->fetch_assoc()) {
      $tipoComplementos[] = $Complemento['CODIGO'];
    }
  }

  $totalesSemanas = [];
  $sumTotalesSemanas = [];

  foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
    $datos = "";
    $diaD = 1;
    $sem=0;
    $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno
      if ($semana == $sem.'b') {
        $mismaSemanaB = "SELECT COUNT(dia) as numero FROM planilla_semanas WHERE semana IN ('$semana','$sem') GROUP BY dia LIMIT 1";
        $respuestaSemanaB = $Link->query($mismaSemanaB) or die('Error al consultar los días de la misma semana' . mysqli_error($Link));
        if ($respuestaSemanaB->num_rows > 0) {
          $dataSemanaB = $respuestaSemanaB->fetch_assoc();
          $numeroDiasRepetidos = $dataSemanaB['numero'];
          if ($numeroDiasRepetidos == 2) {
            $diaD = 1;
          }
        }
      }
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
        // echo $mes." - ".$semana." - ".$D." - ".$dia."</br>";
        $consultaPlanillaDias = "SELECT D$diaD FROM planilla_dias WHERE D$diaD = $dia AND mes = $mes;";
        // echo $consultaPlanillaDias."<br>";
        $respuestaConsultaPlanillaDias = $Link->query($consultaPlanillaDias);
        $consultaPlanillaDias = "SELECT D$diaD FROM planilla_dias WHERE D$diaD = $dia AND mes = $mes;";
        if ($respuestaConsultaPlanillaDias->num_rows == 1) {
          $datos.="SUM(D$diaD) + ";
          $diaD++;
        }
      }
      $datos = trim($datos, "+ ");
      $datos.= " AS semana_".$semana.", ";
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }
    $datos = trim($datos, ", ");
    $consultaRes = "SELECT $datos FROM $tabla";
    // echo $consultaRes."</br>";
    if ($resRes = $Link->query($consultaRes)) {
    
      if ($resRes->num_rows > 0) {
        if ($Res = $resRes->fetch_assoc()) {
          for ($i=1; $i <=$sem ; $i++) { //según el último número de semana guardado previamente, recorremos las semanas que nos devuelve el mes.
            if (strlen($i) == 1) {
              $i = "0".$i;
            }

            if (isset($Res['semana_'.$i])) {

              if ($Res['semana_'.$i] == "") {
                $resSemana = 0;
              } else {
                $resSemana = $Res['semana_'.$i];
              }

                $totalesSemanas[$mes]['semana_'.$i] = $resSemana; //ordenamos una array para totales por semana del siguiente modo array[mes][semana] = total semana
                if (isset($sumTotalesSemanas['semana_'.$i])) {
                  $sumTotalesSemanas['semana_'.$i] += $resSemana;
                } else {
                  $sumTotalesSemanas['semana_'.$i] = $resSemana;
                }
            }
          }
        }
      }
    }
  }

$numFila=11;


$sheet->setCellValue("B".$numFila, "Mes");
$Letra='C';
foreach ($sumTotalesSemanas as $semana => $total) {
	$sheet->setCellValue($Letra.$numFila, ucfirst(str_replace("_", " ", $semana)));
	$LFStyle = $Letra; //Letra donde finalizan los títulos de columnas, se almacena la última recorrida.
	$Letra++;
}
$sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;
$inicioGrafica = $numFila;
$Letra='B';
$semanaAct ="";
foreach ($totalesSemanas as $mes => $semanas) {
	$sheet->setCellValue("B".$numFila, $mesesNom[$mes]);
	foreach ($semanas as $semana => $total) {
		if ($semanaAct == "" || $semanaAct != $semana) {
			$Letra++;
			$semanaAct = $semana;
		}
		$sheet->setCellValue($Letra.$numFila, $total);
	}
	$numFila++;
}
$finGrafica = $numFila;

$sheet->getStyle("B".$inicioGrafica.":".$Letra.$finGrafica)->applyFromArray($infor);

$sheet->setCellValue("B".$numFila, "Total");
$Letra='C';
foreach ($sumTotalesSemanas as $semana => $total) {
	$sheet->setCellValue($Letra.$numFila, $total);
	$Letra++;
}

$numFila = $numFila+2;
$sheet->setCellValue('B'.$numFila, 'Totales por tipo complemento alimentario');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;
foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
    $datos = "";
    $diaD = 1;
    $sem=0;
    $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
        // echo $mes." - ".$semana." - ".$D." - ".$dia."</br>";
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $datos = trim($datos, "+ ");
      $datos.= " AS semana_".$semana.", ";
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }
    $datos = trim($datos, ", ");
    $consultaRes = "SELECT $datos FROM $tabla";
    // echo $consultaRes."</br>";

    foreach ($tipoComplementos as $key => $complemento) {
      $consultaResComplemento = "SELECT ";
      $diaD = 1;
      foreach ($semanas as $semana => $dias) {
        foreach ($dias as $D => $dia) {
          $consultaResComplemento.="SUM(D$diaD) + ";
          $diaD++;
        }
      }
      $consultaResComplemento = trim($consultaResComplemento, "+ ");
      $consultaResComplemento.=" AS TOTAL FROM $tabla WHERE tipo_complem = '$complemento'";
      // echo $consultaResComplemento."</br>";
      if ($resResComplemento = $Link->query($consultaResComplemento)) {
        if ($resResComplemento->num_rows > 0) {
          if ($ResComplemento = $resResComplemento->fetch_assoc()) {

            if ($ResComplemento['TOTAL'] == "") {
              $ResComplemento2 = 0;
            } else {
              $ResComplemento2 = $ResComplemento['TOTAL'];
            }


            if (isset($totalesComplementos[$mes][$complemento])) {
              $totalesComplementos[$mes][$complemento] += $ResComplemento2;
            } else {
              $totalesComplementos[$mes][$complemento] = $ResComplemento2;
            }

            if (isset($sumTotalesComplementos[$complemento])) {
              $sumTotalesComplementos[$complemento] += $ResComplemento2;
            } else {
              $sumTotalesComplementos[$complemento] = $ResComplemento2;
            }
          }
        }
      }
    }

  }
$numFila++;
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
$sheet->setCellValue("B".$numFila, "Mes");
$Letra = "C";
foreach ($sumTotalesComplementos as $complemento => $total) {
	$sheet->setCellValue($Letra.$numFila, $complemento);
	$LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
	$Letra++;
}
$sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;
$inicioGrafica = $numFila; //Número de Fila donde inician los datos para la gráfica.
foreach ($totalesComplementos as $mes => $complementos) {
	$Letra = "B";
	$sheet->setCellValue($Letra.$numFila, $mesesNom[$mes]);
	foreach ($complementos as $complemento => $total) {
		$Letra++;
		$sheet->setCellValue($Letra.$numFila, $total);
	}
	$numFila++;
	$letraFinLabels = $Letra; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
$sheet->setCellValue("B".$numFila, "Total");
$Letra = "C";
foreach ($sumTotalesComplementos as $complemento => $total) {
	$sheet->setCellValue($Letra.$numFila, $total);
	$LFStyle = $Letra;
	$Letra++;
}
$finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
$sheet->getStyle("B".$inicioGrafica.":".$LFStyle.$finGrafica)->applyFromArray($infor);
$dataSeriesLabels = [];
$dataSeriesValues = [];

$Letra = "C";

for ($i=$Letra; $i <= $letraFinLabels ; $i++) { 
	$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$i.'$'.$filaLabelsGrafica, null, 1);
	$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$i.'$'.$inicioGrafica.':$'.$i.'$'.$finGrafica.')', null, 4);
}

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$inicioGrafica.':$B$'.$finGrafica, null, 4), //	Q1 to Q4
];

//	Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Totales por tipo complemento alimentario');
$yAxisLabel = new Title('Entregas ejecutadas');

//	Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);


$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.$numFila);
$numFila = $numFila+14;
$chart->setBottomRightPosition('K'.$numFila);

$sheet->addChart($chart);

$numFila = $numFila+2;

$sheet->setCellValue('B'.$numFila, 'Total Tipo Complemento por semana '.$semanaA);
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;
/// Por semana
unset($totalesComplementos);
$dias = [];
$diasComplementos = [];
$mesDeDia = [];
$totalesComplementos = [];

foreach ($diasSemanas as $mes => $SemanasArray) {
	$datos = "";
	$diaD = 0;
	foreach ($SemanasArray as $semanaF => $dia) {
		// echo $semanaF."\n";
		foreach ($dia as $id => $diaR) {
			$diaD++;
			if ($semanaF == $semanaA) {
			 $datos.="SUM(D$diaD) + ";
			 $dias[] = $diaR;
			 $mesDeDia[$diaR] = $mes;
			}
		}
	}

	if ($datos != "") {
		$datos = trim($datos, "+ ");
		$consComplementos ="SELECT tipo_complem , $datos  AS totalSemana FROM entregas_res_$mes$periodoActual GROUP BY tipo_complem;";

		$resComplementos = $Link->query($consComplementos);
		if ($resComplementos->num_rows > 0) {
			while ($Complementos = $resComplementos->fetch_assoc()) {
        if (is_null($Complementos['tipo_complem'])) {
          continue;
        }

				if (isset($totalesComplementos[$Complementos['tipo_complem']])) {
					$totalesComplementos[$Complementos['tipo_complem']] += $Complementos['totalSemana'];
				} else {
					$totalesComplementos[$Complementos['tipo_complem']] = $Complementos['totalSemana'];
				}
			}
		}
	}
}

$diasTxt = "del ".$dias[0]." de ".$mesesNom[$mesDeDia[$dias[0]]]." al ".$dias[sizeof($dias)-1]." de ".$mesesNom[$mesDeDia[$dias[sizeof($dias)-1]]];

$sheet->setCellValue('D7', 'Para la semana '.$semanaA.', '.$diasTxt.' del '.$_SESSION['periodoActualCompleto']);
$sheet->mergeCells('D7:P7');

$numFila++;
$filaLabelsGrafica = $numFila;
$sheet->setCellValue('B'.$numFila, "Tipo complemento");
$sheet->setCellValue('C'.$numFila, "Total");

$sheet->getStyle('B'.$numFila.':C'.$numFila)->applyFromArray($titulos);

$sumTotal=0;

$numFila++;

$inicioGrafica=$numFila; //Fila donde inician los datos para la gráfica
foreach ($totalesComplementos as $complemento => $total) {
	$sumTotal += $total;
	$sheet->setCellValue('B'.$numFila, $complemento);
	$sheet->setCellValue('C'.$numFila, $total);
	$numFila++;
}
$sheet->getStyle("B".$inicioGrafica.":C".$numFila)->applyFromArray($infor);
$finGrafica = $numFila-1; //Fila donde finalizan los datos para la gráfica
$sheet->setCellValue('B'.$numFila, "Total");
$sheet->setCellValue('C'.$numFila, $sumTotal);

$numFila++;

$Letra = "C";
$letraFinLabels = $Letra;

$dataSeriesLabels = [];
$dataSeriesValues = [];
$xAxisTickValues = [];

for ($i=$Letra; $i <= $letraFinLabels ; $i++) { 
	$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$i.'$'.$filaLabelsGrafica, null, 1);
	$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$i.'$'.$inicioGrafica.':$'.$i.'$'.$finGrafica.')', null, 4);
}

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$inicioGrafica.':$B$'.$finGrafica, null, 4), //	Q1 to Q4
];

//	Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Tipo Complemento - Semana '.$semanaA);
$yAxisLabel = new Title('Entregas ejecutadas');

//	Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.$numFila);
$numFila = $numFila+14;
$chart->setBottomRightPosition('K'.$numFila);

$sheet->addChart($chart);

$numFila += 2;

$sheet->setCellValue('B'.$numFila, 'Totales semana por tipo complemento alimentario y grupo etario - Semana '.$semanaA);
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$totalesEtarios = [];
$etarios = [];

$edadInicial = 0;
$edadFinal=0;

$consGrupoEtario = "SELECT * FROM grupo_etario ORDER BY EDADINICIAL ASC";
$resGrupoEtario = $Link->query($consGrupoEtario);
if ($resGrupoEtario->num_rows > 0) {
	while ($dataGrupoEtario = $resGrupoEtario->fetch_assoc()) {
		$etarios[$dataGrupoEtario['ID']]['inicial'] = $dataGrupoEtario['EDADINICIAL'];
		$etarios[$dataGrupoEtario['ID']]['final'] = $dataGrupoEtario['EDADFINAL'];
		$etarios[$dataGrupoEtario['ID']]['DESC'] = $dataGrupoEtario['DESCRIPCION'];
	}
}

foreach ($diasSemanas as $mes => $SemanasArray) {
	$datos = "";
	$diaD = 0;
	foreach ($SemanasArray as $semanaF => $dia) {
		// echo $semanaF."\n";
		foreach ($dia as $id => $diaR) {
			$diaD++;
			if ($semanaF == $semanaA) {
			 $datos.="SUM(D$diaD) + ";
			}
		}
	}

	if ($datos != "") {
		$datos = trim($datos, "+ ");
		$cntGE = 1;
		foreach ($etarios as $ID => $etario) {
			if ($cntGE == 1) {
				$condicional = " CAST(edad AS DECIMAL(5,0)) <= ".$etario['final']." ";
			} else if ($cntGE == sizeof($etarios)) {
				$condicional = " CAST(edad AS DECIMAL(5,0)) >= ".$etario['inicial']." ";
			} else {
				$condicional = " CAST(edad AS DECIMAL(5,0)) >= ".$etario['inicial']." AND CAST(edad AS DECIMAL(5,0)) <= ".$etario['final']." ";
			}

			$consComplementos ="SELECT tipo_complem , $datos AS totalSemana FROM entregas_res_$mes$periodoActual WHERE $condicional GROUP BY tipo_complem;";
			// echo $consComplementos."\n";
			$resComplementos = $Link->query($consComplementos);
			if ($resComplementos->num_rows > 0) {
				while ($Complementos = $resComplementos->fetch_assoc()) {
          if (is_null($Complementos['tipo_complem'])) {
            continue;
          }
					if (isset($totalesEtarios[$etario['DESC']][$Complementos['tipo_complem']])) {
						$totalesEtarios[$etario['DESC']][$Complementos['tipo_complem']] += $Complementos['totalSemana'];
					} else {
						$totalesEtarios[$etario['DESC']][$Complementos['tipo_complem']] = $Complementos['totalSemana'];
					}
				}
			}
		$cntGE++;
		}
	}
}

$numFila+=2;
$cnt=0;
$inicioGrafica = $numFila;
$sheet->setCellValue('B'.$numFila, 'Grupo Etario');
$Letra = 'C';

foreach ($totalesEtarios as $grupoEtario => $arrayComplementos) {
	if ($cnt==0) {
		foreach ($arrayComplementos as $complemento => $total) {
			$sheet->setCellValue($Letra.$numFila, $complemento);
			$Letra++;
			$LFStyle = $Letra;
			$cnt++;
		}
	} else {
		break;
	}
}

$sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$sheet->setCellValue($Letra.$numFila, 'Total');

$numFila++;

$sumTotalesComplementos = [];
$totaldetotales=0;

foreach ($totalesEtarios as $grupoEtario => $arrayComplementos) {
	$sheet->setCellValue("B".$numFila, $grupoEtario);
	$Letra = 'C';
	$sumTotalEtario = 0; 
	foreach ($arrayComplementos as $complemento => $total) {
		$sheet->setCellValue($Letra.$numFila, $total);
		$sumTotalEtario+=$total;
		$Letra++;

		if (isset($sumTotalesComplementos[$complemento])) {
			$sumTotalesComplementos[$complemento] += $total;
		} else {
			$sumTotalesComplementos[$complemento] = $total;
		}
		$totaldetotales+=$total;
	}
	$sheet->setCellValue($Letra.$numFila, $sumTotalEtario);
	$numFila++;
}
$sheet->setCellValue("B".$numFila, 'Total');

$Letra = "B";

foreach ($sumTotalesComplementos as $complemento => $total){
	$Letra++;
	$sheet->setCellValue($Letra.$numFila, $total);
}
$Letra++;
$letraFinLabels = $Letra;
$sheet->setCellValue($Letra.$numFila, $totaldetotales);

$finGrafica = $numFila;
$CIStyle = $inicioGrafica+1; //Columna donde inicia el estilo de la información. (Se aumenta en uno la variable que normalmente se usa, por que inicia desde los titulos de columnas por temas de gráficas).
$sheet->getStyle("B".$CIStyle.":".$letraFinLabels.$finGrafica)->applyFromArray($infor);
$numFila++;

$dataSeriesLabels = [];
$dataSeriesValues = [];
$xAxisTickValues = [];
$cnt = $inicioGrafica;
$Letra = "C";
for ($j=$inicioGrafica+1; $j < $finGrafica; $j++) { 
	$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$j, null, 1);
	$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$C'.$j.':$'.$letraFinLabels.'$'.$j.')', null, 4);
}
	

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$'.$inicioGrafica.':$'.$letraFinLabels.'$'.$inicioGrafica, null, 4), //	Q1 to Q4
];

//	Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Tipo de complemento por grupo etario - Semana '.$semanaA);
$yAxisLabel = new Title('Entregas ejecutadas');

//	Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.$numFila);
$numFila = $numFila+14;
$chart->setBottomRightPosition('K'.$numFila);

$sheet->addChart($chart);

$numFila += 2;

$dias = [];
$diasComplementos = [];
$mesDeDia = [];

foreach ($diasSemanas as $mes => $SemanasArray) {
	$datos = "";
	$diaD = 0;
	foreach ($SemanasArray as $semanaF => $dia) {
		// echo $semanaF."\n";
		foreach ($dia as $id => $diaR) {
			$diaD++;
			if ($semanaF == $semanaA) {
			 $datos.="SUM(D$diaD) AS '".$diaR."', ";
			 $dias[] = $diaR;
			 $mesDeDia[$diaR] = $mes;
			}
		}
	}

	if ($datos != "") {
		$datos = trim($datos, ", ");

		$consComplementos ="SELECT tipo_complem , $datos FROM entregas_res_$mes$periodoActual GROUP BY tipo_complem;";
		$resComplementos = $Link->query($consComplementos);
		if ($resComplementos->num_rows > 0) {
			while ($Complementos = $resComplementos->fetch_assoc()) {
				// print_r($Complementos);
				foreach ($dias as $id => $diaFecha) {
					// echo "Dia : ".$diaFecha."- Mes :".$mes;
          if (is_null($Complementos['tipo_complem'])) {
            continue;
          }
					if (!isset($diasComplementos[$diaFecha."-".$mesesNom[$mesDeDia[$diaFecha]]][$Complementos['tipo_complem']]) && isset($Complementos[$diaFecha])) {
						$diasComplementos[$diaFecha."-".$mesesNom[$mesDeDia[$diaFecha]]][$Complementos['tipo_complem']] = $Complementos[$diaFecha];
					} else if (!isset($diasComplementos[$diaFecha."-".$mesesNom[$mesDeDia[$diaFecha]]][$Complementos['tipo_complem']])) {
						$diasComplementos[$diaFecha."-".$mesesNom[$mesDeDia[$diaFecha]]][$Complementos['tipo_complem']] = 0;
					}
				}
			}
		}
		// echo $consComplementos."\n";
	}
}

$sheet->setCellValue('B'.$numFila, 'Totales por dias tipo complemento alimentario - Semana '.$semanaA);
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila += 2;

$inicioGrafica = $numFila;
$sheet->setCellValue('B'.$numFila, 'Día');

$Letra = "C";
$cnt = 0;
foreach ($diasComplementos as $dia => $Complementos) {
	if ($cnt == 0) {
		foreach ($Complementos as $complemento => $total) {
			$sheet->setCellValue($Letra.$numFila, $complemento);
			$Letra++;
			$LFStyle = $Letra;
		}
		$cnt++;
	} else {
		break;
	}
}

$sheet->setCellValue($Letra.$numFila, 'Total');
$sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;

$totalComplemento = [];
$totaldeTotales = 0;

foreach ($diasComplementos as $dia => $Complementos) {
	$totalDia = 0;
	$Letra = "B";
	$sheet->setCellValue($Letra.$numFila, $dia);
	$Letra++;
	foreach ($Complementos as $complemento => $total) {
		$sheet->setCellValue($Letra.$numFila, $total);
		$Letra++;
		$totalDia += $total;

		if (isset($totalComplemento[$complemento])) {
			$totalComplemento[$complemento] += $total;
		} else {
			$totalComplemento[$complemento] = $total;
		}

		$totaldeTotales += $total;

	}
	$sheet->setCellValue($Letra.$numFila, $totalDia);
	$numFila++;
}

$finGrafica = $numFila;
$CIStyle = $inicioGrafica + 1;
$sheet->getStyle("B".$CIStyle.":".$Letra.$finGrafica)->applyFromArray($infor);

$sheet->setCellValue("B".$numFila, "Total");
$Letra = "B";
foreach ($totalComplemento as $complemento => $total) {
	$Letra++;
	$sheet->setCellValue($Letra.$numFila, $total);
}


$letraFinLabels = $Letra;
$Letra++;
$sheet->setCellValue($Letra.$numFila, $totaldeTotales);
$numFila++;

$dataSeriesLabels = [];
$dataSeriesValues = [];
$xAxisTickValues = [];
$Letra = "C";

$inicioGrafica++;
$finGrafica--;

for ($i=$Letra; $i <= $letraFinLabels ; $i++) { 
	$filaLabels = $inicioGrafica-1;
	$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$i.'$'.$filaLabels, null, 1);
	$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$i.'$'.$inicioGrafica.':$'.$i.'$'.$finGrafica.')', null, 4);
}

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$inicioGrafica.':$B$'.$finGrafica, null, 4), //	Q1 to Q4
];

//	Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Totales por dias tipo complemento alimentario - Semana '.$semanaA);
$yAxisLabel = new Title('Entregas ejecutadas');

//	Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.$numFila);
$numFila = $numFila+14;
$chart->setBottomRightPosition('K'.$numFila);

$sheet->addChart($chart);

$numFila += 2;

$sheet->setCellValue('B'.$numFila, 'Totales por municipio  y semana');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$municipios = [];
$totalesMunicipios = [];
$totalesMunicipios2 = [];
$totalesMunicipios2 = [];
$sumTotalesSemanas = [];

$consultaMunicipios = "SELECT DISTINCT
                        ubicacion.CodigoDANE, ubicacion.Ciudad
                    FROM
                        ubicacion,
                        parametros
                    WHERE
                        ubicacion.ETC = 0 
                        AND ubicacion.CodigoDane LIKE CONCAT(parametros.CodDepartamento, '%')
                        AND EXISTS( SELECT DISTINCT
                            cod_mun
                        FROM
                            instituciones
                        WHERE
                            cod_mun = ubicacion.CodigoDANE)
                    ORDER BY ubicacion.Ciudad ASC";
$resultadoMunicipios = $Link->query($consultaMunicipios);
if ($resultadoMunicipios->num_rows > 0) {
	while ($dataMunicipios = $resultadoMunicipios->fetch_assoc()) {
		$municipios[$dataMunicipios['CodigoDANE']] = $dataMunicipios['Ciudad']; 
	}
}

$codDepartamento = 0;

$conscodDepartamento = "SELECT CodDepartamento FROM parametros";
$rescodDepartamento = $Link->query($conscodDepartamento);

if ($rescodDepartamento->num_rows > 0) {
	if ($datacodDepartamento = $rescodDepartamento->fetch_assoc()) {
		$codDepartamento = $datacodDepartamento['CodDepartamento'];
	}
}

$sem2=0;
  foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
    $datos = "";
    $diaD = 1;
    $sem;
    $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno
    if ($semana == $sem.'b') {
        $mismaSemanaB = "SELECT COUNT(dia) as numero FROM planilla_semanas WHERE semana IN ('$semana','$sem') GROUP BY dia LIMIT 1";
        $respuestaSemanaB = $Link->query($mismaSemanaB) or die('Error al consultar los días de la misma semana' . mysqli_error($Link));
        if ($respuestaSemanaB->num_rows > 0) {
          $dataSemanaB = $respuestaSemanaB->fetch_assoc();
          $numeroDiasRepetidos = $dataSemanaB['numero'];
          if ($numeroDiasRepetidos == 2) {
            $diaD = 1;
          }
        }
      }   
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
        $consultaPlanillaDias = "SELECT D$diaD FROM planilla_dias WHERE D$diaD = $dia AND mes = $mes;";
            // echo $consultaPlanillaDias."<br>";
        $respuestaConsultaPlanillaDias = $Link->query($consultaPlanillaDias);
        $consultaPlanillaDias = "SELECT D$diaD FROM planilla_dias WHERE D$diaD = $dia AND mes = $mes;";
        if ($respuestaConsultaPlanillaDias->num_rows == 1) {
          $datos.="SUM(D$diaD) + ";
          $diaD++;
        }
      }
      $datos = trim($datos, "+ ");
      $datos.= " AS semana_".$semana.", ";
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
      $sem2++;
    }
    $datos = trim($datos, ", ");
    // echo $consultaRes."</br>";
 		$consMunicipios = "SELECT cod_mun_sede, $datos FROM $tabla GROUP BY cod_mun_sede";
 		// echo $consMunicipios."\n";
 		$resMunicipios= $Link->query($consMunicipios);
	 	if ($resMunicipios->num_rows > 0) {
	 		while ($dataMunicipios = $resMunicipios->fetch_assoc()) {
	 			foreach ($municipios as $codigo => $Ciudad) {
	 				if ($codigo == $dataMunicipios['cod_mun_sede']) {
			 			foreach ($semanasP as $semanaP) {
              $i = $semanaP;
			 				if (strlen($i) == 1) {
				              $i = "0".$i;
				            }
			 				if (!isset($dataMunicipios["semana_".$i]) && !isset($totalesMunicipios[$Ciudad]["semana_".$i])) {
			 					$totalesMunicipios[$Ciudad]["semana_".$i] = 0;
			 				} else if (!isset($dataMunicipios["semana_".$i]) && isset($totalesMunicipios[$Ciudad]["semana_".$i])) {
			 					# code...
			 				} else {
			 					if (isset($totalesMunicipios[$Ciudad]["semana_".$i])) {
			 						$totalesMunicipios[$Ciudad]["semana_".$i] += $dataMunicipios['semana_'.$i];
			 					} else {
			 						$totalesMunicipios[$Ciudad]["semana_".$i] = $dataMunicipios['semana_'.$i];
			 					}
			 				}

			 				if (isset($totalesMunicipios2[$Ciudad]) && isset($dataMunicipios['semana_'.$i])) {
			                  $totalesMunicipios2[$Ciudad] += $dataMunicipios['semana_'.$i];
			                } else if (!isset($totalesMunicipios2[$Ciudad]) && isset($dataMunicipios['semana_'.$i])) {
			                  $totalesMunicipios2[$Ciudad] = $dataMunicipios['semana_'.$i];
			                }

			                if (isset($totalesMunicipios3[$codigo]) && isset($dataMunicipios['semana_'.$i])) {
			                  $totalesMunicipios3[$codigo][0] += $dataMunicipios['semana_'.$i];
			                } else if (!isset($totalesMunicipios3[$codigo]) && isset($dataMunicipios['semana_'.$i])) {
			                  $totalesMunicipios3[$codigo][0] = $dataMunicipios['semana_'.$i];
			                  $totalesMunicipios3[$codigo][1] = $Ciudad;
			                }

			 				if (isset($sumTotalesSemanas['semana_'.$i]) && isset($dataMunicipios['semana_'.$i])) {
			                  $sumTotalesSemanas['semana_'.$i] += $dataMunicipios['semana_'.$i];
			                } else if (!isset($sumTotalesSemanas['semana_'.$i]) && isset($dataMunicipios['semana_'.$i])) {
			                  $sumTotalesSemanas['semana_'.$i] = $dataMunicipios['semana_'.$i];
			                }
			 			}
			 		}
		 		}
	 		}
	 	}

  }

$numFila += 2;

$inicioGrafica = $numFila;

$sheet->setCellValue('B'.$numFila, 'Municipio');
$sheet->setCellValue('C'.$numFila, 'Total');

$Letra = "D";
foreach ($sumTotalesSemanas as $semana => $total) {
	$sheet->setCellValue($Letra.$numFila, ucfirst(str_replace("_", " ", $semana)));
	if (strpos($semana, $semanaA)) {
		$LetraLabels = $Letra;
	}
	$LFStyle = $Letra;
	$Letra++;
}


$sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);

$numFila++;
$totaldetotales = 0;
foreach ($totalesMunicipios as $ciudad => $semanas) {
	$Letra = "B";
	$sheet->setCellValue($Letra.$numFila, $ciudad);
	$Letra++;
	$sheet->setCellValue($Letra.$numFila, $totalesMunicipios2[$ciudad]);
	$Letra++;
	foreach ($semanas as $semana => $total) {
		$sheet->setCellValue($Letra.$numFila, $total);
		$totaldetotales += $total;
		$Letra++;
	}
	$numFila++;
}

$Letra = "B";
$sheet->setCellValue($Letra.$numFila, "Total");
$Letra++;
$sheet->setCellValue($Letra.$numFila, $totaldetotales);
$Letra++;
foreach ($sumTotalesSemanas as $semana => $total) {
	$sheet->setCellValue($Letra.$numFila, $total);
	$LFStyle = $Letra;
	$Letra++;
}
$letraFinLabels = $Letra;
$finGrafica = $numFila;

$CIStyle = $inicioGrafica+1;
$sheet->getStyle("B".$CIStyle.":".$LFStyle.$finGrafica)->applyFromArray($infor);

$numFila += 2;

$dataSeriesLabels = [];
$dataSeriesValues = [];
$xAxisTickValues = [];
$Letra = "C";

$finGrafica--;

$dataSeriesLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$LetraLabels.'$'.$inicioGrafica, null, 1), //	Q1 to Q4
];
$inicioGrafica++;
$dataSeriesValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$'.$LetraLabels.'$'.$inicioGrafica.':$'.$LetraLabels.$finGrafica, null, 4), //	Q1 to Q4
];

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$inicioGrafica.':$B$'.$finGrafica, null, 4), //	Q1 to Q4
];

//	Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);

// exit(var_dump($series));

$series->setPlotDirection(DataSeries::DIRECTION_HORIZONTAL);

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title(' ');
$yAxisLabel = new Title('Entregas ejecutadas');

//	Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

// $letraFinLabels++;
// $chart->setTopLeftPosition($letraFinLabels.$inicioGrafica);
// $chart->setBottomRightPosition('P'.$finGrafica);

$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.$numFila);
$numFila = $numFila+14;
$chart->setBottomRightPosition('K'.$numFila);

$sheet->addChart($chart);

$numFila += 2;

$sheet->setCellValue('B'.$numFila, 'Valor de recursos ejecutados');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila += 2;

$consValores = "SELECT 'ValorContrato' AS Concepto, ValorContrato FROM parametros
                UNION
                SELECT CODIGO AS Concepto, ValorRacion AS ValorContrato FROM tipo_complemento;";
$resValores = $Link->query($consValores);
$valorRaciones = [];
if ($resValores->num_rows > 0) {
  while($Valores = $resValores->fetch_assoc()) {
    if ($Valores['Concepto'] == "ValorContrato") {
      $valorContrato = $Valores['ValorContrato'];
    } else {
      $valorRaciones[$Valores['Concepto']] = $Valores['ValorContrato'];
    }
  }
}

$inicioGrafica = $numFila;

$sheet->setCellValue('B'.$numFila, 'Valor total del contrato');
$sheet->setCellValue('C'.$numFila, $valorContrato);
$sheet->getStyle('B'.$numFila)->applyFromArray($titulos);
$sheet->getStyle("C".$numFila)->applyFromArray($infor);
$numFila++;

foreach ($valorRaciones as $complemento => $valor) {
  $sheet->setCellValue('B'.$numFila, 'Valor ofertado por '.$complemento);
  $sheet->setCellValue('C'.$numFila, $valor);
  $sheet->getStyle('B'.$numFila)->applyFromArray($titulos);
  $sheet->getStyle("C".$numFila)->applyFromArray($infor);
  $numFila++;
}

$finGrafica = $numFila;

$sheet->getStyle('C'.$inicioGrafica.':C'.$finGrafica)->getNumberFormat()->setFormatCode('$ #,##0');

$valorComplementos = [];
$totalesComplementos = [];

$consTipoComplemento = "SELECT * FROM tipo_complemento";
$resTipoComplemento = $Link->query($consTipoComplemento);
if ($resTipoComplemento->num_rows > 0) {
	while ($TipoComplemento = $resTipoComplemento->fetch_assoc()) {
		$valorComplementos[$TipoComplemento['CODIGO']] = $TipoComplemento['ValorRacion'];
	}
}

$ejecutado = 0;

foreach ($diasSemanas as $mes => $SemanasArray) {
	$datos="";
	$diaD = 1;
	foreach ($SemanasArray as $semana => $dias) { //recorremos las semanas del mes en turno
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
        // echo $mes." - ".$semana." - ".$D." - ".$dia."</br>";
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
    }

	if ($datos != "") {
		$datos = trim($datos, "+ ");
		$consComplementos ="SELECT tipo_complem , $datos  AS total FROM entregas_res_$mes$periodoActual GROUP BY tipo_complem;";
		// echo $consComplementos."\n";
		$resComplementos = $Link->query($consComplementos);
		if ($resComplementos->num_rows > 0) {
			while ($Complementos = $resComplementos->fetch_assoc()) {
        if (is_null($Complementos['tipo_complem'])) {
          continue;
        }

				$ejecutado += $Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0); //porcentajes

				// if ($Complementos['tipo_complem'] == "APS") {
				// 	if (isset($totalesComplementos[$mes]["APS"])) {
				// 		$totalesComplementos[$mes]["APS"]+=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				// 	} else {
				// 		$totalesComplementos[$mes]["APS"]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				// 	}
				// } else {
				// 	if (isset($totalesComplementos[$mes]["AM/PM"])) {
				// 		$totalesComplementos[$mes]["AM/PM"]+=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				// 	} else {
				// 		$totalesComplementos[$mes]["AM/PM"]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
				// 	}
				// }

        if ($Complementos['total'] == '') {
          continue;
        }

        if (isset($totalesComplementos[$mes][$Complementos['tipo_complem']])) {
          $totalesComplementos[$mes][$Complementos['tipo_complem']]+=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
        } else {
          $totalesComplementos[$mes][$Complementos['tipo_complem']]=$Complementos['total']*(isset($valorComplementos[$Complementos['tipo_complem']]) ? $valorComplementos[$Complementos['tipo_complem']] : 0);
        }
        if (!isset($tcom[$Complementos['tipo_complem']])) {
          $tcom[$Complementos['tipo_complem']] = 1;
        }
				
			}
		}
	}

	// if (!isset($totalesComplementos[$mes]["APS"])) {
	// 	$totalesComplementos[$mes]["APS"] = 0;
	// }

	// if (!isset($totalesComplementos[$mes]["AM/PM"])) {
	// 	$totalesComplementos[$mes]["AM/PM"] = 0;
	// }
}

$numFila++;
$sheet->setCellValue('B'.$numFila, 'Mes');
$letra = "B";
foreach ($tcom as $complemento => $set) {
  $letra++;
  $sheet->setCellValue($letra.$numFila, $complemento);
}

$letra++;
$sheet->setCellValue($letra.$numFila, "Total");

// $sheet->setCellValue('C'.$numFila, 'APS');
// $sheet->setCellValue('D'.$numFila, 'AM/PM');
// $sheet->setCellValue('E'.$numFila, 'Total');

$sheet->getStyle('B'.$numFila.':'.$letra.$numFila)->applyFromArray($titulos);
$filaLabels = $numFila;
$numFila++;

$totalesComplement = [];
$totales = [];
$inicioGrafica = $numFila;
foreach ($totalesComplementos as $mes => $complementos) {
	$sheet->setCellValue("B".$numFila, $mesesNom[$mes]);
	$totalMes = 0;
  $letra = "B";
  foreach ($complementos as $complemento => $total) {
    $letra++;
    $sheet->setCellValue($letra.$numFila, $total);
    $totalMes += $total;

    if (isset($totalesComplement[$complemento])) {
      $totalesComplement[$complemento] += $total;
    } else {
      $totalesComplement[$complemento] = $total;
    }

  }
  $letra++;
	$sheet->setCellValue($letra.$numFila, $totalMes);
	$numFila++;
}
$finGrafica = $numFila;
$letraFinLabels = $letra;

$sheet->setCellValue('B'.$numFila, 'Total');
$tTotal = 0;
$letra = "B";

foreach ($totalesComplement as $complemento => $total) {
  $letra++;
  $sheet->setCellValue($letra.$numFila, $total);
  $tTotal += $total;
}
$letra++;
$sheet->setCellValue($letra.$numFila, $tTotal);
$sheet->getStyle("B".$inicioGrafica.":".$letra.$finGrafica)->applyFromArray($infor);

for ($i="C"; $i <= $letra ; $i++) { 
  $sheet->getStyle($i.$inicioGrafica.':'.$i.$finGrafica)->getNumberFormat()->setFormatCode('$ #,##0');
}

$dataSeriesLabels = [];
$dataSeriesValues = [];
$xAxisTickValues = [];
$Letra = "C";

for ($i=$Letra; $i <= $letraFinLabels ; $i++) { 
	$filaLabels = $inicioGrafica-1;
	$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$i.'$'.$filaLabels, null, 1);
	$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$i.'$'.$inicioGrafica.':$'.$i.'$'.$finGrafica.')', null, 4);
}

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$inicioGrafica.':$B$'.$finGrafica, null, 4), //	Q1 to Q4
];

//	Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Valor de recursos ejecutados');
$yAxisLabel = new Title('Recursos ejecutadas');

//	Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.$numFila);
$numFila = $numFila+14;
$chart->setBottomRightPosition('K'.$numFila);

$sheet->addChart($chart);

$numFila += 2;

$sheet->setCellValue('B'.$numFila, 'Valor de recursos ejecutados');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila += 2;

$porejecutar = $valorContrato - $ejecutado;

$porcenEjecutado = 100 * $ejecutado / $valorContrato;
$porcenPorEjecutar = 100 * $porejecutar / $valorContrato;

$sheet->setCellValue('B'.$numFila, '');
$sheet->setCellValue('C'.$numFila, 'Total');
$sheet->setCellValue('D'.$numFila, '%');
$inicioGrafica = $numFila;
$sheet->getStyle('B'.$numFila.':D'.$numFila)->applyFromArray($titulos);
$numFila++;

$CIStyle = $numFila;
$sheet->setCellValue('B'.$numFila, 'Valor contrato');
$sheet->setCellValue('C'.$numFila, $valorContrato);
$sheet->setCellValue('D'.$numFila, '100 %');

$numFila++;

$sheet->setCellValue('B'.$numFila, 'Valor por ejecutar');
$sheet->setCellValue('C'.$numFila, $porejecutar);
$sheet->setCellValue('D'.$numFila, $porcenPorEjecutar.' %');

$numFila++;

$sheet->setCellValue('B'.$numFila, 'Valor ejecutado');
$sheet->setCellValue('C'.$numFila, $ejecutado);
$sheet->setCellValue('D'.$numFila, $porcenEjecutado.' %');
$finGrafica = $numFila;

$sheet->getStyle("B".$CIStyle.":D".$numFila)->applyFromArray($infor);
$sheet->getStyle('C'.$inicioGrafica.':C'.$finGrafica)->getNumberFormat()->setFormatCode('$ #,##0');
$numFila++;

$dataSeriesLabels = [];
$dataSeriesValues = [];
$xAxisTickValues = [];

	$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$'.$inicioGrafica, null, 1);
	$inicioGrafica += 2;
	$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$C$'.$inicioGrafica.':$C$'.$finGrafica.')', null, 4);

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$inicioGrafica.':$B$'.$finGrafica, null, 4), //	Q1 to Q4
];

//	Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_PIECHART, // plotType
    null, // plotGrouping (Pie charts don't have any grouping)
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues          // plotValues
);

$layout = new Layout();
$layout->setShowVal(false);
$layout->setShowPercent(true);

$plotArea = new PlotArea($layout, [$series]);
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Valor de recursos ejecutados');
$yAxisLabel = new Title('Recursos ejecutadas');

//	Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.$numFila);
$numFila = $numFila+17;
$chart->setBottomRightPosition('K'.$numFila);

$sheet->addChart($chart);

$sheet->getColumnDimension("B")->setWidth(25); 

$Cantidad_de_columnas_a_crear=30; 
$Contador=3; 
$Letra='C'; 
while($Contador<$Cantidad_de_columnas_a_crear) 
{ 
    $sheet->getColumnDimension($Letra)->setWidth(15); 
    $Contador++; 
    $Letra++; 
}

// $sheet->getRowDimension("4")->setRowHeight(30);

$color = [
	'fill' => [
    	'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    	'color' => ['argb' => 'FFFFFF'],
    	],
    ];

$sheet->getStyle("A1:Z500")->applyFromArray($color);

$writer = new Xlsx($spreadsheet);
$reader->setReadDataOnly(false);
$writer->setIncludeCharts(TRUE);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="estadisticas_semana_'.$semanaA.'.xlsx"');
$writer->save('php://output','estadisticas_semana_'.$semanaA.'.xlsx');

} else {
	echo "<script>alert('No se ha definido semana.');location.href='index.php';</script>";
}
