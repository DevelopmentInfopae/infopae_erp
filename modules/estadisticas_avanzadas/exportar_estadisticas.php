<?php

require_once '../../config.php';
require_once '../../db/conexion.php';
require '../../vendor/autoload.php';

// definimos los parametros para el nuevo libro de excel que vamos a crear
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


// creamos un nuevo libro de trabajo
$spreadsheet = new Spreadsheet();

// accedemos a la hoja activa de ese libro 
$sheet = $spreadsheet->getActiveSheet();

// array para nombre de los meses
$nombreMeses = array('1' => 'Enero', '2' => 'Febrero', '3' => 'Marzo', '4' => 'Abril', '5' => 'Mayo', '6' => 'Junio', '7' => 'Julio', '8' => 'Agosto', '9' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
$letrasAbecedario = array('1' => 'A','2' => 'B','3' => 'C','4' => 'D','5' => 'E','6' => 'F','7' => 'G','8' => 'H','9' => 'I','10' => 'J','11' => 'K',
'12' => 'L','13' => 'M','14' => 'N','15' => '0','16' => 'P','17' => 'Q','18' => 'R','19' => 'S', '20' => 'T','21' => 'U','22' => 'V','23' => 'W','24' => 'X','25' => 'Y','26' => 'Z',);

$sheet->setCellValue('D2', 'Sistema de Información Tecnológico InfoPAE');
$sheet->mergeCells('D2:P4');
$sheet->setCellValue('D5', 'Información Estadística basada en la ejecución de entrega de complementos alimentarios');
$sheet->mergeCells('D5:P7');
$sheet->setCellValue('B9', 'Totales por semana');
$sheet->mergeCells('B9:P9');
$sheet->mergeCells('B2:C7');

$titulos1 = [
    'font' => [
        'bold' => true,
        'size'  => 12,
        'name' => 'calibrí',
        // 'color' => '#033B73'
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


$titulos = [
    'font' => [
        'bold' => true,
        'size'  => 12,
        'name' => 'calibrí',
        'color' => ['argb' => 'FDFEFE']
        // 'color' => '#033B73'
    ],
    'fill' => [
      'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
      'color' => ['argb' => '0C1846'],
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

$titulos2 = [
    'font' => [
        'bold' => true,
        'size'  => 10,
        'name' => 'calibrí',
        'color' => ['argb' => 'FDFEFE']
    ],
    'fill' => [
      'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
      'color' => ['argb' => '0C1846'],
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


$sheet->getStyle("D2:P4")->applyFromArray($titulos1);
$sheet->getStyle("D5:P7")->applyFromArray($titulos1);
$sheet->getStyle("D7:P7")->applyFromArray($titulos1);
// $sheet->getStyle('B9:P9')->applyFromArray($titulos);
$sheet->getStyle('B2:C7')->applyFromArray($titulos1);

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

// INICIO SECCION EXPORTAR TOTALES POR SEMANA

$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

// consulta para el numero de dias que se encuentra por mes en las tablas entregas_res
  $diasSemanas = [];
  $consDiasSemanas = "SELECT GROUP_CONCAT(DIA) AS Dias, MES, SEMANA FROM planilla_semanas WHERE CONCAT(ANO, '-', MES, '-', DIA) <= '".date('Y-m-d')."' GROUP BY SEMANA";
  $resDiasSemanas = $Link->query($consDiasSemanas);
  if ($resDiasSemanas->num_rows > 0) {
    while ($dataDiasSemanas = $resDiasSemanas->fetch_assoc()) {

      $consultaTablas = "SELECT 
                           table_name AS tabla
                          FROM 
                           information_schema.tables
                          WHERE 
                           table_schema = DATABASE() AND table_name = 'entregas_res_".$dataDiasSemanas['MES']."$periodoActual'";
      $resTablas = $Link->query($consultaTablas);
      if ($resTablas->num_rows > 0) {
        $semanaPos = str_replace("b", "", $dataDiasSemanas['SEMANA']);
        $arrDias = explode(",", $dataDiasSemanas['Dias']);
        sort($arrDias);
        // print_r($arrDias);
        $diasSemanas[$dataDiasSemanas['MES']][$semanaPos] = $arrDias; //obtenemos un array ordenado del siguiente modo array[mes][semana] = array[dias]
      }
    }
  }

  // variable en la que almacenamos los valores que se encuentran en la tabla tipo_ complemento 
  $tipoComplementos = [];
  $consComplemento="SELECT * FROM tipo_complemento WHERE valorRacion > 0;";
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
// letra en la que empiza la los valores de la tabla
$Letra='C';
foreach ($sumTotalesSemanas as $semana => $total) {
  $sheet->setCellValue($Letra.$numFila, ucfirst(str_replace("_", " ", $semana)));
  $LFStyle = $Letra; //Letra donde finalizan los títulos de columnas, se almacena la última recorrida.
  $Letra++;
}

$titulosTablaTotalesSemanas = 'B'.$numFila.':'.$LFStyle.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;
$inicioGrafica = $numFila;
$Letra='B';
$semanaAct ="";

// letras para recorrer la tabla completa y validar que valores estan nulos y colocarlos en 0
$letraInicial = $Letra;
$filaInicial = $numFila;

$totales = [];
foreach ($totalesSemanas as $mes => $semanas) {
  $sheet->setCellValue("B".$numFila, $mesesNom[$mes]);
    foreach ($semanas as $semana => $total) {
      $totales[$semana] = $total;
      if ($semanaAct == "" || $semanaAct != $semana) {
        $Letra++;
        $semanaAct = $semana;
      }
      $sheet->setCellValue($Letra.$numFila, $total);      
    }   
  $numFila++;
  $letraFinLabels = $Letra; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
// letras para recorrer la tabla completa y validar que valores estan en nulos para colocarlos en 0
$letraFinal = $Letra;
$filaFinal = $numFila;
$pValue   = 1;

$ultimaF = 0;
$ultimaL = 0;
// ciclo para recorrer la tabla creada y colocar en 0 los valores que estan vacios ademas se guarda y la ultima letra y fila
for ($i = $filaInicial; $i <= $filaFinal; $i++) { 
    for ($j=$letraInicial; $j <= $letraFinal ; $j++) {       
        if ($sheet->getCell("$j$i", false) != null ) {
          continue;
        }else{
            $sheet->setCellValue("$j$i",'');
            $ultimaLF = $j.($i-1);
            $ultimaL = $j;
            $ultimaF = $i;
        }
    }
}

// fila en la que termina la tabla 
$finGrafica = $numFila;

// aplicamos los estilos a el contenido de la tabla
$sheet->getStyle("B".$inicioGrafica.":".$Letra.($finGrafica-1))->applyFromArray($infor);
$sheet->setCellValue("B".$numFila, "Total");
$Letra='C';

// llenamos el total de la tabla
foreach ($sumTotalesSemanas as $semana => $total) {
  $sheet->setCellValue($Letra.$numFila, $total);
  $Letra++;
}

$finGrafica = $numFila;
$sheet->getStyle("B".$inicioGrafica.":".$LFStyle.($finGrafica-1))->applyFromArray($infor);
$pieTablaTotalesSemana = "B".$finGrafica.":".$LFStyle.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2);

// INICIO GRAFICA
$dataSeriesLabels = [];
$dataSeriesValues = [];
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica


// letra donde empiezan los datos
$Letra = 'C'; 
 
for ($j=$filaInicial; $j < $ultimaF; $j++) { 
    // valores con los que se van a llenar la grafica en las barras
    $dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$Letra.'$'.$j.':'.'$'.$ultimaL.'$'.$j.')', null, 4);
}

for ($j=$filaInicial; $j < $ultimaF; $j++) { 
    // valores con los que se van a agrupar los datos para este caso los meses 
    $dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, '(Worksheet!$B'.'$'.$j.':'.'$B'.'$'.$j.')', null, 4);
}

// valores  los datos para este caso las semanas va a ir en el pie de la grafica señalando el nombre de cada barra
$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$Letra.'$'.'11'.':$'.$ultimaL.'$'.'11', null, 4),
];


// constructor de los datos 
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);

// valores que van a ir sobre las barras 
$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

// set series in plot area
$plotArea = new PlotArea($layout, [$series]);
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

// titulo de la grafica
$title = new Title('Totales por semanas');
$yAxisLabel = new Title('Entregas ejecutadas');

// Create the chart
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

// no mandar las letras con $ en esta seccion
$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.($numFila+2));

// no mandar la letra con $ en esta seccion
$numFila = $numFila+14;
$chart->setBottomRightPosition('L'.($numFila+2));

$sheet->addChart($chart);
// FIN CREACION DE LA GRAFICA 
// FIN SECCION DE TOTALES POR SEMANA 


// INICIO SECCION TOTALES POR TIPO DE COMPLEMENTO 
// titulo
$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por tipo complemento alimentario');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesComplementos = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;

// ciclo para armar la y ejecutar la consulta que nos trae los datos para las tablas
foreach ($diasSemanas as $mes => $semanas) { //recorremos los meses
    $datos = "";
    $diaD = 1;
    $sem=0;
    $tabla="entregas_res_$mes$periodoActual"; //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    foreach ($semanas as $semana => $dias) { //recorremos las semanas del mes en turno
      foreach ($dias as $D => $dia) { //recorremos los días de la semana en turno
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

// INICIO TABLA 
$numFila++;
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
$sheet->setCellValue("B".$numFila, "Mes");
$Letra = "C";
foreach ($sumTotalesComplementos as $complemento => $total) {
  $sheet->setCellValue($Letra.$numFila, $complemento);
  $Letra++;
  $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
}
$sheet->setCellValue($Letra.$numFila, 'TOTAL');

$titulosTablaTotalesComplementos = 'B'.$numFila.':'.$LFStyle.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;

$inicioGrafica = $numFila; //Número de Fila donde inician los datos para la gráfica.
foreach ($totalesComplementos as $mes => $complementos) {
  $Letra = "B";
  $sheet->setCellValue($Letra.$numFila, $mesesNom[$mes]);
  $totalComple = 0;
  foreach ($complementos as $complemento => $total) {
    
    $Letra++;
    $sheet->setCellValue($Letra.$numFila, $total);
    $LetraF = $Letra;
    $totalComple = $totalComple + $total;
    
  }
  $LetraF++;
  $sheet->setCellValue(($LetraF).$numFila, $totalComple);
  
  $numFila++;
  $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
$sheet->setCellValue("B".$numFila, "Total");
$Letra = "C";
$totalCompleTotal = 0;
foreach ($sumTotalesComplementos as $complemento => $total) {
  $sheet->setCellValue($Letra.$numFila, $total);
  
  $Letra++;
  $LetraF = $Letra;
  $totalCompleTotal = $totalCompleTotal + $total;
  $LFStyle = $Letra;
}
$sheet->setCellValue(($LetraF).$numFila, $totalCompleTotal);
$finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
$sheet->getStyle("B".$inicioGrafica.":".$LFStyle.($finGrafica-1))->applyFromArray($infor);
$pieTablaTotalesComplemento = "B".$finGrafica.":".$LFStyle.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2);    
// FIN TABLA

// INICIO GRAFICA 
$dataSeriesLabels = [];
$dataSeriesValues = [];

$Letra = "C";

for ($i=$Letra; $i < $letraFinLabels ; $i++) { 
 $dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$i.'$'.$filaLabelsGrafica, null, 1);
 $dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$i.'$'.$inicioGrafica.':$'.$i.'$'.($finGrafica-1).')', null, 4);
}

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$inicioGrafica.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
];

// Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);
$series->setPlotDirection(DataSeries::DIRECTION_BAR);

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(true);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Totales por tipo complemento alimentario');
$yAxisLabel = new Title('Entregas ejecutadas');

// Create the chart
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

$chart->setTopLeftPosition('K'.(--$inicioGrafica));
$numFila = $inicioGrafica+22;
$chart->setBottomRightPosition('V'.$numFila);
$sheet->addChart($chart);
// FIN GRAFICA
// FIN SECCION TOTALES POR COMPLEMENTO 


// INICIO SECCION TOTALES POR GENERO
$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por género');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesGenero = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;

// ciclo para armar la consulta y guardar los datos con que vamos a armar la tabla
$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];

// ciclo para recorrer los meses
foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 

    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }

    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT genero, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY genero";
    $periodo = 1;
  
  if ($resConsultaRes = $Link->query($consultaRes)) {
        if ($resConsultaRes->num_rows > 0) {
          while ($ResEdad = $resConsultaRes->fetch_assoc()) {
            $respuesta[$periodo] = $ResEdad;
          $periodo++;   
          }         
        }
      }    
    $respuesta2[$mes] = $respuesta;
    $mesesRecorridos .= $mes; 
}

$arrayMes = explode("0", $mesesRecorridos);

// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

$numFila++;
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
$sheet->setCellValue("B".$numFila, "Genero");
$Letra = "C";
// exit(var_dump($arrayMes));
foreach ($arrayMes as $meses) {
  foreach ($nombreMeses as $mes => $nombre) {
      if ($meses == $mes) {
        $nombreMes = $nombre;
      }
  }
  $sheet->setCellValue($Letra.$numFila, $nombreMes);
  $Letra++;
  $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
}
$sheet->setCellValue($Letra.$numFila, 'Total');

$tituloTablaTotalesGenero = 'B'.$numFila.':'.$LFStyle.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;

// se inicia a llenar la tabla 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($valoresMes as $valorMes => $valor) {
    // convertimos la respuesta a un array asociativo con la clave primaria edad mes
    $generos[$valor['genero']][$mes] = $valor['TOTAL'];  
  }
}

$nombreGenero = '';
$filaInicialTabla = $numFila;
foreach ($generos as $genero => $valorGenero) {
  $Letra = "B";
  if ($genero == 'F') {
    $nombreGenero = 'Femenino';
  }else if ($genero == 'M') {
    $nombreGenero = 'Masculino';
  }
  $sheet->setCellValue($Letra.$numFila, $nombreGenero);
  
  $totalGenero = 0;
  foreach ($valorGenero as $valores => $valor) {   
    $Letra++;
    $sheet->setCellValue($Letra.$numFila, $valor);
    $LetraF = $Letra;
    $totalGenero = $totalGenero + $valor;  
  }
  $LetraF++;
  $sheet->setCellValue(($LetraF).$numFila, $totalGenero);
  
  $numFila++;
  $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
$sheet->setCellValue("B".$numFila, "Total");

foreach ($respuesta2 as $mes => $valoresMes) {
    foreach ($valoresMes as $valorMes => $valor) {
      // convertimos la respuesta a un array asociativo con la clave primaria edad mes
      $generos[$valor['genero']][$mes] = $valor['TOTAL'];  
    }
  }

$tTotal = 0;
$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
  foreach ($generos as $genero => $valorGenero) {
    foreach ($valorGenero as $mes => $valorMes) {   
      $totalMes[$mes] += $valorMes;
    }
}

$Letra = "C";
foreach ($totalMes as $total) {
  if ($total <> 0) {
      $sheet->setCellValue($Letra.$numFila, $total);
      $Letra++;
      $tTotal += $total;
      $LetraF = $Letra;
      $LFStyle = $Letra;
    }    
  }  
$sheet->setCellValue(($LetraF).$numFila, $tTotal);

$finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
$sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor); 
$pieTabalTotalesGenero = "B".$finGrafica.":".$LFStyle.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2); 
// FIN TABLA

// INICIO GRAFICA
$dataSeriesLabels = [];
$dataSeriesValues = [];

$Letra = "C";

$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.$filaLabelsGrafica, null, 1);

$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
];

// exit(var_dump($dataSeriesValues));

// Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_PIECHART, // plotType
    null, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);

$layout = new Layout();
$layout->setShowVal(false);
$layout->setShowPercent(true);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Totales por género');
$yAxisLabel = new Title('Entregas ejecutadas');

// Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    null  // yAxisLabel
);

$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.($numFila+2));

// no mandar la letra con $ en esta seccion
$numFila = $numFila+17;
$chart->setBottomRightPosition('L'.($numFila+2));
$sheet->addChart($chart);

// FIN GRAFICA
// FIN SECCION TOTALES POR GENERO 


// INICIO SECCION TOTALES POR EDAD
// encabezado
$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por edad');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesEdad = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;

$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];

// ciclo para recorrer los meses
foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 

    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }

    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT edad, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY edad ORDER BY convert(edad, UNSIGNED)";
    $periodo = 1;
  
  if ($resConsultaRes = $Link->query($consultaRes)) {
        if ($resConsultaRes->num_rows > 0) {
          while ($ResEdad = $resConsultaRes->fetch_assoc()) {
            $respuesta[$periodo] = $ResEdad;
            $periodo++;   
          }          
        }
      }
    
    $respuesta2[$mes] = $respuesta;
    $mesesRecorridos .= $mes; 
}

// convertirmos el string en un array 
$arrayMes = explode("0", $mesesRecorridos);

// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// llenamos el encabezado
$numFila++;
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
$sheet->setCellValue("B".$numFila, "Edad");
$Letra = "C";
foreach ($arrayMes as $meses) {
  foreach ($nombreMeses as $mes => $nombre) {
      if ($meses == $mes) {
        $nombreMes = $nombre;
      }
  }
  $sheet->setCellValue($Letra.$numFila, $nombreMes);
  $Letra++;
  $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
}
$sheet->setCellValue($Letra.$numFila, 'Total');

$tituloTablaTotalesEdad = 'B'.$numFila.':'.$LFStyle.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;
// terminamos de llenar el encabezado

// empezamos a llenar el body de la tabla 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($valoresMes as $valorMes => $valor) {
    // convertimos la respuesta a un array asociativo con la clave primaria edad mes
    $edades[$valor['edad']][$mes] = $valor['TOTAL'];  
  }
}

foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($edades as $edad => $valorEdad) {
    if (isset($edades[$edad][$mes])) {
      continue;
    }else{
      $edades[$edad][$mes] = '0';
    }
    asort($edades[$edad]);
    // asort(intval($edades));
    ksort($edades); 
  }
}

$filaInicialTabla = $numFila;
foreach ($edades as $edad => $valorEdad) {
  $Letra = "B";
  $sheet->setCellValue($Letra.$numFila, $edad);
  
  $totalEdad = 0;
  foreach ($valorEdad as $valores => $valor) {   
    $Letra++;
    $sheet->setCellValue($Letra.$numFila, $valor);
    $LetraF = $Letra;
    $totalEdad = $totalEdad + $valor;  
  }
  $LetraF++;
  $sheet->setCellValue(($LetraF).$numFila, $totalEdad);
  
  $numFila++;
  $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
$sheet->setCellValue("B".$numFila, "Total");
// terminamos de llenar el cuerpo de la tabla

// empezamos a llenar el foot de la tabla
$tTotal = 0;
$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
  foreach ($edades as $edad => $valorEdad) {
    foreach ($valorEdad as $mes => $valorMes) {   
      $totalMes[$mes] += $valorMes;
    }
}

$Letra = "C";
foreach ($totalMes as $total) {
  if ($total <> 0) {
      $sheet->setCellValue($Letra.$numFila, $total);
      $Letra++;
      $tTotal += $total;
      $LetraF = $Letra;
      $LFStyle = $Letra;
    }    
  }  
$sheet->setCellValue(($LetraF).$numFila, $tTotal);

$finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
$sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor); 
$pieTablaTotalesEdad = "B".$finGrafica.":".$LFStyle.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2);
// terminamos de llenar el foot de la tabla


// INICIO GRAFICA
$dataSeriesLabels = [];
$dataSeriesValues = [];
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

// letra donde empiezan los datos
$Letra = 'C'; 
// exit(var_dump($letrasAbecedario));
for ($i = 1; $i <= 26 ; $i++) { 
  if ($letrasAbecedario[$i] == $LetraF ) {
    $letraFinal = $letrasAbecedario[$i-1];
  }
}

$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.($filaInicialTabla-1), null, 1);

$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
];

// exit(var_dump($letraFinal));

// constructor de los datos 
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);
// exit(var_dump($series));

// valores que van a ir sobre las barras 
$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

// set series in plot area
$plotArea = new PlotArea($layout, [$series]);
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

// titulo de la grafica
$title = new Title('Totales por edad');
$yAxisLabel = new Title('Entregas ejecutadas');

// Create the chart
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

// no mandar las letras con $ en esta seccion
$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.($numFila+2));

// no mandar la letra con $ en esta seccion
$numFila = $numFila+20;
$chart->setBottomRightPosition('L'.($numFila+2));

$sheet->addChart($chart);
// FIN CREACION DE LA GRAFICA 
// FIN SECCION DE TOTALES POR EDADES


// INICIO SECCION TOTALES POR ESTRATO
// INICIO TABLA
// encabezado
$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por estrato');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesEstrato = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;

$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];

foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 
    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }
    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT cod_estrato AS estrato, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY cod_estrato";
    $periodo = 1;
  
  if ($resConsultaRes = $Link->query($consultaRes)) {
    if ($resConsultaRes->num_rows > 0) {
      while ($resEstrato = $resConsultaRes->fetch_assoc()) {
        $respuesta[$periodo] = $resEstrato;
        $periodo++;   
      }          
    } 
  }
  $respuesta2[$mes] = $respuesta;
  $mesesRecorridos .= $mes;   
}

$arrayMes = explode("0", $mesesRecorridos);
// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// llenamos el encabezado
$numFila++;
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
$sheet->setCellValue("B".$numFila, "Estrato");
$Letra = "C";
foreach ($arrayMes as $meses) {
  foreach ($nombreMeses as $mes => $nombre) {
      if ($meses == $mes) {
        $nombreMes = $nombre;
      }
  }
  $sheet->setCellValue($Letra.$numFila, $nombreMes);
  $Letra++;
  $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
}
$sheet->setCellValue($Letra.$numFila, 'Total');

$tituloTablaTotalesEstrato = 'B'.$numFila.':'.$LFStyle.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;
// terminamos de llenar el encabezado

// empezamos a llenar el body de la tabla 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($valoresMes as $valorMes => $valor) {
    // convertimos la respuesta a un array asociativo con la clave primaria edad mes
    $estratos[$valor['estrato']][$mes] = $valor['TOTAL'];  
  }
}

// funcion para llenar campos cuando haya  un dato en un mes y en otro no 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($estratos as $estrato => $valorEstrato) {
    if (isset($estratos[$estrato][$mes])) {
      continue;
    }else{
      $estratos[$estrato][$mes] = '0';
    }
      asort($estratos[$estrato]);
  }
}

$filaInicialTabla = $numFila;
foreach ($estratos as $estrato => $valorEstrato) {
  $Letra = "B";
  $estratoString = '';
  if ($estrato == 0) { $estratoString = 'Estrato 0';}
  if ($estrato == 1) { $estratoString = 'Estrato 1';}
  if ($estrato == 2) { $estratoString = 'Estrato 2';}
  if ($estrato == 3) { $estratoString = 'Estrato 3';}
  if ($estrato == 4) { $estratoString = 'Estrato 4';}
  if ($estrato == 5) { $estratoString = 'Estrato 5';}
  if ($estrato == 6) { $estratoString = 'Estrato 6';}
  if ($estrato == 9 || $estrato == 99) { $estratoString = 'No aplica';}

  $sheet->setCellValue($Letra.$numFila, $estratoString);
  $totalEstrato = 0;
  foreach ($valorEstrato as $valores => $valor) {   
    $Letra++;
    $sheet->setCellValue($Letra.$numFila, $valor);
    $LetraF = $Letra;
    $totalEstrato = $totalEstrato + $valor;  
  }
  $LetraF++;
  $sheet->setCellValue(($LetraF).$numFila, $totalEstrato);
  
  $numFila++;
  $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
$sheet->setCellValue("B".$numFila, "Total");
// terminamos de llenar el cuerpo de la tabla

// empezamos a llenar el foot de la tabla
$tTotal = 0;
$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
  foreach ($estratos as $estrato => $valorEstrato) {
    foreach ($valorEstrato as $mes => $valorMes) {   
      $totalMes[$mes] += $valorMes;
    }
}

$Letra = "C";
foreach ($totalMes as $total) {
  if ($total <> 0) {
      $sheet->setCellValue($Letra.$numFila, $total);
      $Letra++;
      $tTotal += $total;
      $LetraF = $Letra;
      $LFStyle = $Letra;
    }    
  }  
$sheet->setCellValue(($LetraF).$numFila, $tTotal);

$finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
$sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor); 
$pieTablaTotalesEstrato = "B".$finGrafica.":".$LFStyle.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2); 
// terminamos de llenar el foot de la tabla

// INICIO GRAFICA
$dataSeriesLabels = [];
$dataSeriesValues = [];
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

// letra donde empiezan los datos
$Letra = 'C'; 
// exit(var_dump($letrasAbecedario));
for ($i = 1; $i <= 26 ; $i++) { 
  if ($letrasAbecedario[$i] == $LetraF ) {
    $letraFinal = $letrasAbecedario[$i-1];
  }
}

$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.($filaInicialTabla-1), null, 1);

$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
];

// exit(var_dump($letraFinal));

// constructor de los datos 
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);
// exit(var_dump($series));

// valores que van a ir sobre las barras 
$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

// set series in plot area
$plotArea = new PlotArea($layout, [$series]);
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

// titulo de la grafica
$title = new Title('Totales por estrato');
$yAxisLabel = new Title('Entregas ejecutadas');

// Create the chart
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

// no mandar las letras con $ en esta seccion
$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.($numFila+2));

// no mandar la letra con $ en esta seccion
$numFila = $numFila+20;
$chart->setBottomRightPosition('L'.($numFila+2));

$sheet->addChart($chart);
// FIN CREACION DE LA GRAFICA 
// FIN SECCION DE TOTALES POR ESTRATO


// INICIO SECCION TOTALES POR ZONA DE RESIDENCIA 
// encabezado
$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por zona de residencia');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesResidencia = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;

$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];

foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
  $diaD = 1;
  $sem=0;
  //tabla donde se busca, según mes(obtenido de consulta anterior) y año
  $tabla="entregas_res_$mes$periodoActual"; 
  // ciclo para recorrer las semanas
  foreach ($semanas as $semana => $dias) {
    // ciclo para recorrer los dias de la semana
    foreach ($dias as $D => $dia) { 
      $datos.="SUM(D$diaD) + ";
      $diaD++;
    }
    $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
  }

  $datos = trim($datos, "+ ");
  $consultaRes = "SELECT zona_res_est, $datos ";
  $consultaRes.=" AS TOTAL FROM $tabla GROUP BY zona_res_est";
  $periodo = 1;
  // echo $consultaRes;
  if ($resConsultaRes = $Link->query($consultaRes)) {
    if ($resConsultaRes->num_rows > 0) {
      while ($resEstrato = $resConsultaRes->fetch_assoc()) {
        $respuesta[$periodo] = $resEstrato;
        $periodo++;   
      }      
    }
  }
  $respuesta2[$mes] = $respuesta;
  $mesesRecorridos .= $mes;   
}

$arrayMes = explode("0", $mesesRecorridos);
// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// llenamos el encabezado
$numFila++;
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
$sheet->setCellValue("B".$numFila, "Zona de residencia");
$Letra = "C";
foreach ($arrayMes as $meses) {
  foreach ($nombreMeses as $mes => $nombre) {
      if ($meses == $mes) {
        $nombreMes = $nombre;
      }
  }
  $sheet->setCellValue($Letra.$numFila, $nombreMes);
  $Letra++;
  $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
}
$sheet->setCellValue($Letra.$numFila, 'Total');

$tituloTablaTotalesResidencia = 'B'.$numFila.':'.$LFStyle.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;
// terminamos de llenar el encabezado

// empezamos a llenar el body de la tabla 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($valoresMes as $valorMes => $valor) {
    // convertimos la respuesta a un array asociativo con la clave primaria edad mes
    $residencias[$valor['zona_res_est']][$mes] = $valor['TOTAL'];  
  }
}

$filaInicialTabla = $numFila;
foreach ($residencias as $residencia => $valorResidencia) {
  $Letra = "B";
  $zona = '';
  if ($residencia == 1) {
    $zona = 'Rural';
  }
  elseif ($residencia == 2) {
    $zona = 'Urbano';
  }
  else{
    $zona = 'Indefinido';
  }
  $sheet->setCellValue($Letra.$numFila, $zona);
  
  $totalResidencia = 0;
  foreach ($valorResidencia as $valores => $valor) {   
    $Letra++;
    $sheet->setCellValue($Letra.$numFila, $valor);
    $LetraF = $Letra;
    $totalResidencia = $totalResidencia + $valor;  
  }
  $LetraF++;
  $sheet->setCellValue(($LetraF).$numFila, $totalResidencia);
  
  $numFila++;
  $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
$sheet->setCellValue("B".$numFila, "Total");
// terminamos de llenar el cuerpo de la tabla

// empezamos a llenar el foot de la tabla
$tTotal = 0;
$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
  foreach ($residencias as $residencia => $valorResidencia) {
    foreach ($valorResidencia as $mes => $valorMes) {   
      $totalMes[$mes] += $valorMes;
    }
}

$Letra = "C";
foreach ($totalMes as $total) {
  if ($total <> 0) {
      $sheet->setCellValue($Letra.$numFila, $total);
      $Letra++;
      $tTotal += $total;
      $LetraF = $Letra;
      $LFStyle = $Letra;
    }    
  }  
$sheet->setCellValue(($LetraF).$numFila, $tTotal);

$finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
$sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor); 
$pieTablaTotalesResidencia = "B".$finGrafica.":".$LFStyle.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2); 
// terminamos de llenar el foot de la tabla


// INICIO GRAFICA
$dataSeriesLabels = [];
$dataSeriesValues = [];
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

// letra donde empiezan los datos
$Letra = 'C'; 
// exit(var_dump($letrasAbecedario));
for ($i = 1; $i <= 26 ; $i++) { 
  if ($letrasAbecedario[$i] == $LetraF ) {
    $letraFinal = $letrasAbecedario[$i-1];
  }
}

$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.$filaLabelsGrafica, null, 1);

$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
];


// constructor de los datos 
$series = new DataSeries(
    DataSeries::TYPE_PIECHART, // plotType
    null, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);
// exit(var_dump($series));

// valores que van a ir sobre las barras 
$layout = new Layout();
$layout->setShowVal(false);
$layout->setShowPercent(true);

// set series in plot area
$plotArea = new PlotArea($layout, [$series]);
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

// titulo de la grafica
$title = new Title('Totales por zona de residencia');
$yAxisLabel = new Title('Entregas ejecutadas');

// Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    null // yAxisLabel
);

// no mandar las letras con $ en esta seccion
$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.($numFila+2));

// no mandar la letra con $ en esta seccion
$numFila = $numFila+17;
$chart->setBottomRightPosition('L'.($numFila+2));

$sheet->addChart($chart);
// FIN CREACION DE LA GRAFICA 
// FIN SECCION DE TOTALES POR ZONA DE RESIDENCIA


// TOTALES POR GRADO DE ESCOLARIDAD
$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por escolaridad');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesEscolaridad = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;

$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];
$nomGrados = [];
$nombreGrado = '';

// consulta para traer el nombre de los grados
$periodo = 0;
$consNomGrados = "SELECT id, nombre FROM grados";
$resNomGrados = $Link->query($consNomGrados);
if ($resNomGrados->num_rows >0) {
  while ($dataNomGrados = $resNomGrados->fetch_assoc()) {
    $nomGrados[$dataNomGrados['id']] = $dataNomGrados;
    $periodo++;
  }
}
// exit(var_dump($nomGrados));
foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 
    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }
    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT cod_grado, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY cod_grado";
    $periodo = 1;

  if ($resConsultaRes = $Link->query($consultaRes)) {
    if ($resConsultaRes->num_rows > 0) {
      while ($resEstrato = $resConsultaRes->fetch_assoc()) {
        $respuesta[$periodo] = $resEstrato;
        $periodo++;   
      }       
    }
  }
  $respuesta2[$mes] = $respuesta;
  $mesesRecorridos .= $mes;   
}

$arrayMes = explode("0", $mesesRecorridos);
// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// llenamos el encabezado
$numFila++;
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
$sheet->setCellValue("B".$numFila, "Grado");
$Letra = "C";
foreach ($arrayMes as $meses) {
  foreach ($nombreMeses as $mes => $nombre) {
      if ($meses == $mes) {
        $nombreMes = $nombre;
      }
  }
  $sheet->setCellValue($Letra.$numFila, $nombreMes);
  $Letra++;
  $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
}
$sheet->setCellValue($Letra.$numFila, 'Total');
$tituloTablaTotalesEscolaridad = 'B'.$numFila.':'.$LFStyle.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;
// terminamos de llenar el encabezado

// empezamos a llenar el body de la tabla 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($valoresMes as $valorMes => $valor) {
    // convertimos la respuesta a un array asociativo con la clave primaria edad mes
    $escolaridades[$valor['cod_grado']][$mes] = $valor['TOTAL'];  
  }
}

foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($escolaridades as $escolaridad => $valorEscolaridad) {
    if (isset($escolaridades[$escolaridad][$mes])) {
      continue;
    }else{
      $escolaridades[$escolaridad][$mes] = '0';
    }
      asort($escolaridades[$escolaridad]);
  }
} 

$filaInicialTabla = $numFila;
foreach ($escolaridades as $escolaridad => $valorEscolaridad) {
  $Letra = "B";
  $nombreGrado = '';
  foreach ($nomGrados as $grado => $valor) {
    if ($valor['id'] == $escolaridad) {
      $nombreGrado = $valor['nombre'];
    }
  }
  $sheet->setCellValue($Letra.$numFila, $nombreGrado);
  
  $totalEscolaridad = 0;
  foreach ($valorEscolaridad as $valores => $valor) {   
    $Letra++;
    $sheet->setCellValue($Letra.$numFila, $valor);
    $LetraF = $Letra;
    $totalEscolaridad = $totalEscolaridad + $valor;  
  }
  $LetraF++;
  $sheet->setCellValue(($LetraF).$numFila, $totalEscolaridad);
  
  $numFila++;
  $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
$sheet->setCellValue("B".$numFila, "Total");
// terminamos de llenar el cuerpo de la tabla

// empezamos a llenar el foot de la tabla
$tTotal = 0;
$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
  foreach ($escolaridades as $escolaridad => $valorEscolaridad) {
    foreach ($valorEscolaridad as $mes => $valorMes) {   
      $totalMes[$mes] += $valorMes;
    }
}

$Letra = "C";
foreach ($totalMes as $total) {
  if ($total <> 0) {
      $sheet->setCellValue($Letra.$numFila, $total);
      $Letra++;
      $tTotal += $total;
      $LetraF = $Letra;
      $LFStyle = $Letra;
    }    
  }  
$sheet->setCellValue(($LetraF).$numFila, $tTotal);
$finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
$sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor);
$pieTablaTotalesEscolaridad = "B".$finGrafica.":".$LFStyle.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2); 
// terminamos de llenar el foot de la tabla


// INICIO GRAFICA
$dataSeriesLabels = [];
$dataSeriesValues = [];
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

// letra donde empiezan los datos
$Letra = 'C'; 
// exit(var_dump($letrasAbecedario));
for ($i = 1; $i <= 26 ; $i++) { 
  if ($letrasAbecedario[$i] == $LetraF ) {
    $letraFinal = $letrasAbecedario[$i-1];
  }
}

$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.($filaInicialTabla-1), null, 1);

$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
];


$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);
$series->setPlotDirection(DataSeries::DIRECTION_BAR);

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Totales por escolaridad');
$yAxisLabel = new Title('Entregas ejecutadas');

// Create the chart
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

// no mandar las letras con $ en esta seccion
$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.($numFila+2));

// no mandar la letra con $ en esta seccion
$numFila = $numFila+20;
$chart->setBottomRightPosition('L'.($numFila+2));

$sheet->addChart($chart);
// FIN CREACION DE LA GRAFICA 
// FIN SECCION TOTALES POR ESCOLARIDAD


// INICIO SECCION TOTALES POR JORNADA
$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por jornada');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesJornada = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;

$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];
$nomJornadas = [];

$consNomJornadas = "SELECT id, nombre FROM jornada";
$resNomJornadas = $Link->query($consNomJornadas);
if ($resNomJornadas->num_rows > 0) {
  while ($dataNomJornadas = $resNomJornadas->fetch_assoc()) {
    // exit(var_dump($dataNomJornadas));
    $nomJornadas[$dataNomJornadas['id']] = $dataNomJornadas['nombre'];
  }
}

foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
  $diaD = 1;
  $sem=0;
  //tabla donde se busca, según mes(obtenido de consulta anterior) y año
  $tabla="entregas_res_$mes$periodoActual"; 
  // ciclo para recorrer las semanas
  foreach ($semanas as $semana => $dias) {
    // ciclo para recorrer los dias de la semana
      foreach ($dias as $D => $dia) { 
      $datos.="SUM(D$diaD) + ";
      $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
  }
  $datos = trim($datos, "+ ");
  $consultaRes = "SELECT cod_jorn_est, $datos ";
  $consultaRes.=" AS TOTAL FROM $tabla GROUP BY cod_jorn_est";
  $periodo = 1;

  if ($resConsultaRes = $Link->query($consultaRes)) {
    if ($resConsultaRes->num_rows > 0) {
      while ($resEstrato = $resConsultaRes->fetch_assoc()) {
        $respuesta[$periodo] = $resEstrato;
        $periodo++;   
      }   
    }
  }
  $respuesta2[$mes] = $respuesta;
  $mesesRecorridos .= $mes;   
}

$arrayMes = explode("0", $mesesRecorridos);
// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// llenamos el encabezado
$numFila++;
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
$sheet->setCellValue("B".$numFila, "Jornada");
$Letra = "C";
foreach ($arrayMes as $meses) {
  foreach ($nombreMeses as $mes => $nombre) {
      if ($meses == $mes) {
        $nombreMes = $nombre;
      }
  }
  $sheet->setCellValue($Letra.$numFila, $nombreMes);
  $Letra++;
  $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
}
$sheet->setCellValue($Letra.$numFila, 'Total');
$tituloTablaTotalesJornada = 'B'.$numFila.':'.$LFStyle.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;
// terminamos de llenar el encabezado

// empezamos a llenar el body de la tabla 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($valoresMes as $valorMes => $valor) {
    // convertimos la respuesta a un array asociativo con la clave primaria edad mes
    $jornadas[$valor['cod_jorn_est']][$mes] = $valor['TOTAL'];  
  }
}

// funcion para llenar campos cuando haya  un dato en un mes y en otro no 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($jornadas as $jornada => $valorJornada) {
    if (isset($jornadas[$jornada][$mes])) {
      continue;
    }else{
      $jornadas[$jornada][$mes] = '0';
    }
      asort($jornadas[$jornada]);
  }
} 

$filaInicialTabla = $numFila;
foreach ($jornadas as $jornada => $valorJornada) {
  $Letra = "B";
  $nombreJornada = '';
  foreach ($nomJornadas as $idJornada => $valor) {
    if ($idJornada == $jornada) {
      $nombreJornada = $valor;
    }
  }
  $sheet->setCellValue($Letra.$numFila, $nombreJornada);
  
  $totalJornada = 0;
  foreach ($valorJornada as $valores => $valor) {   
    $Letra++;
    $sheet->setCellValue($Letra.$numFila, $valor);
    $LetraF = $Letra;
    $totalJornada = $totalJornada + $valor;  
  }
  $LetraF++;
  $sheet->setCellValue(($LetraF).$numFila, $totalJornada);
  
  $numFila++;
  $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
$sheet->setCellValue("B".$numFila, "Total");
// terminamos de llenar el cuerpo de la tabla

// empezamos a llenar el foot de la tabla
$tTotal = 0;
$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
  foreach ($jornadas as $jornada => $valorJornada) {
    foreach ($valorJornada as $mes => $valorMes) {   
      $totalMes[$mes] += $valorMes;
    }
  }

$Letra = "C";
foreach ($totalMes as $total) {
  if ($total <> 0) {
      $sheet->setCellValue($Letra.$numFila, $total);
      $Letra++;
      $tTotal += $total;
      $LetraF = $Letra;
      $LFStyle = $Letra;
    }    
  }  
$sheet->setCellValue(($LetraF).$numFila, $tTotal);
$finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
$sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor); 
$pieTablaTotalesJornada = "B".$finGrafica.":".$LFStyle.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2); 
// terminamos de llenar el foot de la tabla

// INICIO GRAFICA
$dataSeriesLabels = [];
$dataSeriesValues = [];
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

// letra donde empiezan los datos
$Letra = 'C'; 
// exit(var_dump($letrasAbecedario));
for ($i = 1; $i <= 26 ; $i++) { 
  if ($letrasAbecedario[$i] == $LetraF ) {
    $letraFinal = $letrasAbecedario[$i-1];
  }
}

$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.($filaInicialTabla-1), null, 1);

$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
];


// constructor de los datos 
$series = new DataSeries(
    DataSeries::TYPE_PIECHART, // plotType
    null, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);
// exit(var_dump($series));

// valores que van a ir sobre las barras 
$layout = new Layout();
$layout->setShowVal(false);
$layout->setShowPercent(true);

// set series in plot area
$plotArea = new PlotArea($layout, [$series]);
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

// titulo de la grafica
$title = new Title('Totales por jornada');
$yAxisLabel = new Title('Entregas ejecutadas');

// Create the chart
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

// no mandar las letras con $ en esta seccion
$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.($numFila+2));

// no mandar la letra con $ en esta seccion
$numFila = $numFila+17;
$chart->setBottomRightPosition('L'.($numFila+2));

$sheet->addChart($chart);
// FIN CREACION DE LA GRAFICA 
// FIN SECCION TOTALES POR JORNADA


// INICIO SECCION TOTALES POR MUNICIPIO O POR SEDES EDUCATIVAS DEPENDIENDO DEL TIPO DE CONTRATO
$codigoMunicipio = '';
$consCodigoMunicipio = "SELECT codMunicipio FROM parametros";
$resCodigoMunicipio = $Link->query($consCodigoMunicipio);
if ($resCodigoMunicipio->num_rows > 0) {
  while ($dataCodigoMunicipio = $resCodigoMunicipio->fetch_assoc()) {
    $codigoMunicipio = $dataCodigoMunicipio['codMunicipio'];
  }
}

// EN CASO QUE SEA CONTRATO DEPARTAMENTAL SE EJECUTARA POR ACA
if ($codigoMunicipio == '0') {
  $numFila = $numFila+5;
  $sheet->setCellValue('B'.$numFila, 'Totales por municipio');
  $sheet->mergeCells('B'.$numFila.':P'.$numFila);
  $tituloTotalesMunicipio = 'B'.$numFila.':P'.$numFila;
  // $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
  $numFila++;

  $mesesRecorridos = ""; 
  $respuesta = [];
  $respuesta2 = [];
  $codDepartamento = $_SESSION['p_CodDepartamento'];

  foreach ($diasSemanas as $mes => $semanas) {
    $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 
    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }
    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT ubicacion.codigoDane, ubicacion.Ciudad, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla JOIN ubicacion ON $tabla.cod_mun_res = ubicacion.CodigoDane GROUP BY cod_mun_res";
    $periodo = 1;

    if ($resConsultaRes = $Link->query($consultaRes)) {
      if ($resConsultaRes->num_rows > 0) {
        while ($resEstrato = $resConsultaRes->fetch_assoc()) {
          $respuesta[$periodo] = $resEstrato;
          $periodo++;   
        }  
      }
    }
    $respuesta2[$mes] = $respuesta;
    $mesesRecorridos .= $mes;
    }

    $arrayMes = explode("0", $mesesRecorridos);

    // funcion para quitar espacios vacios de un array
    foreach ($arrayMes as $key => $link) {
      if($link === '') 
        { 
        unset($arrayMes[$key]); 
        } 
    }

    // llenamos el encabezado
    $numFila++;
    $filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
    $sheet->setCellValue("B".$numFila, "Municipio");
    $Letra = "C";
    // exit(var_dump($arrayMes));
    foreach ($arrayMes as $meses) {
      foreach ($nombreMeses as $mes => $nombre) {
        if ($meses == $mes) {
          $nombreMes = $nombre;
        }
      }
      $sheet->setCellValue($Letra.$numFila, $nombreMes);
      $Letra++;
      $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
    }
    $sheet->setCellValue($Letra.$numFila, 'Total');
    $tituloTablaTotalesMunicipio = 'B'.$numFila.':'.$LFStyle.$numFila;
    // $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
    $numFila++;
    // terminamos de llenar el encabezado

    // empezamos a llenar el body de la tabla 
    foreach ($respuesta2 as $mes => $valoresMes) {
        foreach ($valoresMes as $valorMes => $valor) {
          // convertimos la respuesta a un array asociativo con la clave primaria edad mes
          $municipios[$valor['Ciudad']][$mes] = $valor['TOTAL'];  
          $codigos[$valor['codigoDane']][$mes] = $valor['TOTAL'];
      }
    }
    $filaInicialTabla = $numFila;
    foreach ($municipios as $municipio => $valorMunicipio) {
        $Letra = "B";
        $sheet->setCellValue($Letra.$numFila, $municipio);
        $valorTotal = 0;
        foreach ($valorMunicipio as $valores => $valor) {        
          $Letra++;
          $sheet->setCellValue($Letra.$numFila, $valor);
          $LetraF = $Letra;
          $valorTotal = $valorTotal + $valor;  
        }
        $LetraF++;
        $sheet->setCellValue(($LetraF).$numFila, $valorTotal);
  
        $numFila++;
        $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
    }
    $sheet->setCellValue("B".$numFila, "Total");
    // terminamos de llenar el cuerpo de la tabla

    // empezamos a llenar el foot de la tabla
    $tTotal = 0;
    $totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
    foreach ($municipios as $municipio => $valorMunicipio) {
      foreach ($valorMunicipio as $mes => $valorMes) {   
        $totalMes[$mes] += $valorMes;
      }
    }

    $Letra = "C";
    foreach ($totalMes as $total) {
      if ($total <> 0) {
        $sheet->setCellValue($Letra.$numFila, $total);
        $Letra++;
        $tTotal += $total;
        $LetraF = $Letra;
        $LFStyle = $Letra;
      }    
    }  
    $sheet->setCellValue(($LetraF).$numFila, $tTotal);
    $finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
    $sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor); 
    $pieTablaTotalesMunicipio = "B".$finGrafica.":".$LFStyle.$finGrafica;
    // $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2);
    // terminamos de llenar el foot de la tabla


    // INICIO GRAFICA
    $dataSeriesLabels = [];
    $dataSeriesValues = [];
    $filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

    // letra donde empiezan los datos
    $Letra = 'C'; 
    // exit(var_dump($letrasAbecedario));
    for ($i = 1; $i <= 26 ; $i++) { 
      if ($letrasAbecedario[$i] == $LetraF ) {
        $letraFinal = $letrasAbecedario[$i-1];
      }
    }

    $dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.($filaInicialTabla-1), null, 1);

    $dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

    $xAxisTickValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
    ];

    $series = new DataSeries(
        DataSeries::TYPE_BARCHART, // plotType
        DataSeries::GROUPING_CLUSTERED, // plotGrouping
        range(0, count($dataSeriesValues) - 1), // plotOrder
        $dataSeriesLabels, // plotLabel
        $xAxisTickValues, // plotCategory
        $dataSeriesValues        // plotValues
    );
    $series->setPlotDirection(DataSeries::DIRECTION_BAR);

    $layout = new Layout();
    $layout->setShowVal(true);
    $layout->setShowPercent(false);

    $plotArea = new PlotArea($layout, [$series]);

    $legend = new Legend(Legend::POSITION_RIGHT, null, false);

    $title = new Title('Totales por municipio');
    $yAxisLabel = new Title('Entregas ejecutadas');

    // Create the chart
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

    // no mandar las letras con $ en esta seccion
    $numFila = $numFila+2;
    $chart->setTopLeftPosition('B'.($numFila+2));

    // no mandar la letra con $ en esta seccion
    $numFila = $numFila+55;
    $chart->setBottomRightPosition('L'.($numFila+2));

    $sheet->addChart($chart);
    // FIN CREACION DE LA GRAFICA 
    // FIN SECCION TOTALES POR MUNICIPIO
}else {

  $numFila = $numFila+5;
  $sheet->setCellValue('B'.$numFila, 'Totales por sedes educativas');
  $sheet->mergeCells('B'.$numFila.':P'.$numFila);
  $tituloTotalesSedeEducativa = 'B'.$numFila.':P'.$numFila;
  // $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
  $numFila++;

  $mesesRecorridos = ""; 
  $respuesta = [];
  $respuesta2 = [];
  $codDepartamento = $_SESSION['p_CodDepartamento'];

  foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 
    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }

    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT cod_sede, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla GROUP BY cod_sede";
    $periodo = 1; 
  
    if ($resConsultaRes = $Link->query($consultaRes)) {
      if ($resConsultaRes->num_rows > 0) {
        while ($resEstrato = $resConsultaRes->fetch_assoc()) {
          $respuesta[$periodo] = $resEstrato;
          $periodo++;   
          }
        }
      }
    $respuesta2[$mes] = $respuesta;
    $mesesRecorridos .= $mes;

    // consulta en la que vamos a almacenar las sedes que se van a mostrar en la tabla 
    $consultaSedes = "SELECT cod_sede, nom_sede FROM sedes$periodoActual";
    if ($resConsultaSedes = $Link->query($consultaSedes)) {
      if ($resConsultaSedes->num_rows > 0) {
        while ($resSedes = $resConsultaSedes->fetch_assoc()) {
        $respuestaSedes[$resSedes['cod_sede']] = $resSedes['nom_sede'];
        }
      }
    }
  }

  $arrayMes = explode("0", $mesesRecorridos);
  // funcion para quitar espacios vacios de un array
  foreach ($arrayMes as $key => $link) {
    if($link === '') 
      { 
        unset($arrayMes[$key]); 
      } 
  }

  // llenamos el encabezado
    $numFila++;
    $filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
    $sheet->setCellValue("B".$numFila, "Sede educativa");
    $Letra = "C";
    // exit(var_dump($arrayMes));
    foreach ($arrayMes as $meses) {
      foreach ($nombreMeses as $mes => $nombre) {
          if ($meses == $mes) {
            $nombreMes = $nombre;
          }
      }
      $sheet->setCellValue($Letra.$numFila, $nombreMes);
      $Letra++;
      $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
    }
    $sheet->setCellValue($Letra.$numFila, 'Total');
    $tituloTablaTotalesSedeEducativa = 'B'.$numFila.':'.$LFStyle.$numFila;
    // $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
    $numFila++;
    // terminamos de llenar el encabezado

    // empezamos a llenar el body de la tabla 
    foreach ($respuesta2 as $mes => $valoresMes) {
      foreach ($valoresMes as $valorMes => $valor) {
        // convertimos la respuesta a un array asociativo con la clave primaria edad mes
        $sedes[$valor['cod_sede']][$mes] = $valor['TOTAL']; 

      }
    }
    $filaInicialTabla = $numFila;
    foreach ($sedes as $sede => $valorSede) {
      foreach ($respuestaSedes as $codigoSede => $nombre) {
        if ($sede == $codigoSede) {
            $nombreSede = $nombre;
        }
      } 
      $Letra = "B";
      $sheet->setCellValue($Letra.$numFila, $nombreSede);
      $valorTotal = 0;
        foreach ($valorSede as $valores => $valor) {        
          $Letra++;
          $sheet->setCellValue($Letra.$numFila, $valor);
          $LetraF = $Letra;
          $valorTotal = $valorTotal + $valor;  
        }
        $LetraF++;
        $sheet->setCellValue(($LetraF).$numFila, $valorTotal);
  
        $numFila++;
        $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
    }
    $sheet->setCellValue("B".$numFila, "Total");
    // terminamos de llenar el cuerpo de la tabla

    // empezamos a llenar el foot de la tabla
    $tTotal = 0;
    $totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
    foreach ($sedes as $sede => $valorSede) {
      foreach ($valorSede as $mes => $valorMes) {   
        $totalMes[$mes] += $valorMes;
      }
    }

    $Letra = "C";
    foreach ($totalMes as $total) {
      if ($total <> 0) {
        $sheet->setCellValue($Letra.$numFila, $total);
        $Letra++;
        $tTotal += $total;
        $LetraF = $Letra;
        $LFStyle = $Letra;
      }    
    }  
    $sheet->setCellValue(($LetraF).$numFila, $tTotal);
    $finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
    $sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor);
    $pieTablaTotalesSede = "B".$finGrafica.":".$LFStyle.$finGrafica;
    // $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2);  
    // terminamos de llenar el foot de la tabla


    // INICIO GRAFICA
    $dataSeriesLabels = [];
    $dataSeriesValues = [];
    $filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

    // letra donde empiezan los datos
    $Letra = 'C'; 
    // exit(var_dump($letrasAbecedario));
    for ($i = 1; $i <= 26 ; $i++) { 
      if ($letrasAbecedario[$i] == $LetraF ) {
        $letraFinal = $letrasAbecedario[$i-1];
      }
    }

    $dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.($filaInicialTabla-1), null, 1);

    $dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

    $xAxisTickValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
    ];

    $series = new DataSeries(
        DataSeries::TYPE_BARCHART, // plotType
        DataSeries::GROUPING_CLUSTERED, // plotGrouping
        range(0, count($dataSeriesValues) - 1), // plotOrder
        $dataSeriesLabels, // plotLabel
        $xAxisTickValues, // plotCategory
        $dataSeriesValues        // plotValues
    );
    $series->setPlotDirection(DataSeries::DIRECTION_BAR);

    $layout = new Layout();
    $layout->setShowVal(true);
    $layout->setShowPercent(false);

    $plotArea = new PlotArea($layout, [$series]);

    $legend = new Legend(Legend::POSITION_RIGHT, null, false);

    $title = new Title('Totales por sedes educativas');
    $yAxisLabel = new Title('Entregas ejecutadas');

    // Create the chart
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

    // no mandar las letras con $ en esta seccion
    $numFila = $numFila+2;
    $chart->setTopLeftPosition('B'.($numFila+2));

    // no mandar la letra con $ en esta seccion
    $numFila = $numFila+55;
    $chart->setBottomRightPosition('L'.($numFila+2));

    $sheet->addChart($chart);
    // FIN CREACION DE LA GRAFICA 
    // FIN SECCION TOTALES POR SEDE EDUCATIVA
}

// INICIO SECCION TOTALES POR POBLACION DISCAPACIDAD

$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por población con discapacidad');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesDiscapacidad = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;


$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];

foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 
    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }
    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT discapacidades.nombre, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla JOIN discapacidades ON $tabla.cod_discap = discapacidades.id GROUP BY cod_discap";
    $periodo = 1;

    if ($resConsultaRes = $Link->query($consultaRes)) {
      if ($resConsultaRes->num_rows > 0) {
        while ($resEstrato = $resConsultaRes->fetch_assoc()) {
          $respuesta[$periodo] = $resEstrato;
          $periodo++;   
        } 
      }
    }
    $respuesta2[$mes] = $respuesta;
    $mesesRecorridos .= $mes;
}
$arrayMes = explode("0", $mesesRecorridos);

// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// llenamos el encabezado
    $numFila++;
    $filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
    $sheet->setCellValue("B".$numFila, "Discapacidad");
    $Letra = "C";
    // exit(var_dump($arrayMes));
    foreach ($arrayMes as $meses) {
      foreach ($nombreMeses as $mes => $nombre) {
        if ($meses == $mes) {
          $nombreMes = $nombre;
        }
      }
      $sheet->setCellValue($Letra.$numFila, $nombreMes);
      $Letra++;
      $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
    }
    $sheet->setCellValue($Letra.$numFila, 'Total');
    $tituloTablaTotalesDiscapacidad = 'B'.$numFila.':'.$LFStyle.$numFila;
    // $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
    $numFila++;
    // terminamos de llenar el encabezado

    // empezamos a llenar el body de la tabla 
    foreach ($respuesta2 as $mes => $valoresMes) {
        foreach ($valoresMes as $valorMes => $valor) {
          // convertimos la respuesta a un array asociativo con la clave primaria edad mes
          $discapacidades[$valor['nombre']][$mes] = $valor['TOTAL'];  
      }
    }

    // funcion para llenar campos cuando haya  un dato en un mes y en otro no 
    foreach ($respuesta2 as $mes => $valoresMes) {
      foreach ($discapacidades as $discapacidad => $valorDiscapacidad) {
        if (isset($discapacidades[$discapacidad][$mes])) {
          continue;
        }else{
          $discapacidades[$discapacidad][$mes] = '0';
        }
          asort($discapacidades[$discapacidad]);
      }
    }

    $filaInicialTabla = $numFila;
    foreach ($discapacidades as $discapacidad => $valorDiscapacidad) {
        $Letra = "B";
        $sheet->setCellValue($Letra.$numFila, $discapacidad);
        $valorTotal = 0;
        foreach ($valorDiscapacidad as $valores => $valor) {        
          $Letra++;
          $sheet->setCellValue($Letra.$numFila, $valor);
          $LetraF = $Letra;
          $valorTotal = $valorTotal + $valor;  
        }
        $LetraF++;
        $sheet->setCellValue(($LetraF).$numFila, $valorTotal);
  
        $numFila++;
        $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
    }
    $sheet->setCellValue("B".$numFila, "Total");
    // terminamos de llenar el cuerpo de la tabla

    // empezamos a llenar el foot de la tabla
    $tTotal = 0;
    $totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
    foreach ($discapacidades as $discapacidad => $valorDiscapacidad) {
      foreach ($valorDiscapacidad as $mes => $valorMes) {   
        $totalMes[$mes] += $valorMes;
      }
    }

    $Letra = "C";
    foreach ($totalMes as $total) {
      if ($total <> 0) {
        $sheet->setCellValue($Letra.$numFila, $total);
        $Letra++;
        $tTotal += $total;
        $LetraF = $Letra;
        $LFStyle = $Letra;
      }    
    }  
    $sheet->setCellValue(($LetraF).$numFila, $tTotal);
    $finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
    $sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor); 
    $pieTablaTotalesDiscapacidad = "B".$finGrafica.":".$LFStyle.$finGrafica;
    // $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2);
    // terminamos de llenar el foot de la tabla


    // INICIO GRAFICA
    $dataSeriesLabels = [];
    $dataSeriesValues = [];
    $filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

    // letra donde empiezan los datos
    $Letra = 'C'; 
    // exit(var_dump($letrasAbecedario));
    for ($i = 1; $i <= 26 ; $i++) { 
      if ($letrasAbecedario[$i] == $LetraF ) {
        $letraFinal = $letrasAbecedario[$i-1];
      }
    }

    $dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.($filaInicialTabla-1), null, 1);

    $dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

    $xAxisTickValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
    ];

    $series = new DataSeries(
        DataSeries::TYPE_BARCHART, // plotType
        DataSeries::GROUPING_CLUSTERED, // plotGrouping
        range(0, count($dataSeriesValues) - 1), // plotOrder
        $dataSeriesLabels, // plotLabel
        $xAxisTickValues, // plotCategory
        $dataSeriesValues        // plotValues
    );
    $series->setPlotDirection(DataSeries::DIRECTION_BAR);

    $layout = new Layout();
    $layout->setShowVal(true);
    $layout->setShowPercent(false);

    $plotArea = new PlotArea($layout, [$series]);

    $legend = new Legend(Legend::POSITION_RIGHT, null, false);

    $title = new Title('Totales por población con discapacidad');
    $yAxisLabel = new Title('Entregas ejecutadas');

    // Create the chart
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

    // no mandar las letras con $ en esta seccion
    $numFila = $numFila+2;
    $chart->setTopLeftPosition('B'.($numFila+2));

    // no mandar la letra con $ en esta seccion
    $numFila = $numFila+20;
    $chart->setBottomRightPosition('L'.($numFila+2));

    $sheet->addChart($chart);
    // FIN CREACION DE LA GRAFICA 
    // FIN SECCION TOTALES POR SEDE POBLACION DISCAPACIDAD


// INICIO SECCION TOTALES POR POBLACION VICTIMA
$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por población victima');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesPoblacion = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;

$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];

foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 
    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }

    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT pobvictima.nombre, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla JOIN pobvictima ON $tabla.cod_pob_victima = pobvictima.id GROUP BY cod_pob_victima";
    $periodo = 1;

  if ($resConsultaRes = $Link->query($consultaRes)) {
    if ($resConsultaRes->num_rows > 0) {
      while ($resEstrato = $resConsultaRes->fetch_assoc()) {
        $respuesta[$periodo] = $resEstrato;
        $periodo++;   
        }  
      }
    }
    $respuesta2[$mes] = $respuesta;
    $mesesRecorridos .= $mes;
  }

$arrayMes = explode("0", $mesesRecorridos);
// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// llenamos el encabezado
    $numFila++;
    $filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
    $sheet->setCellValue("B".$numFila, "Población victima");
    $Letra = "C";
    // exit(var_dump($arrayMes));
    foreach ($arrayMes as $meses) {
      foreach ($nombreMeses as $mes => $nombre) {
        if ($meses == $mes) {
          $nombreMes = $nombre;
        }
      }
      $sheet->setCellValue($Letra.$numFila, $nombreMes);
      $Letra++;
      $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
    }
    $sheet->setCellValue($Letra.$numFila, 'Total');
    $tituloTablaTotalesPoblacion = 'B'.$numFila.':'.$LFStyle.$numFila;
    // $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
    $numFila++;
    // terminamos de llenar el encabezado

    // empezamos a llenar el body de la tabla 
    foreach ($respuesta2 as $mes => $valoresMes) {
        foreach ($valoresMes as $valorMes => $valor) {
          // convertimos la respuesta a un array asociativo con la clave primaria edad mes
          $victimas[$valor['nombre']][$mes] = $valor['TOTAL'];  
      }
    }

    // funcion para llenar campos cuando haya  un dato en un mes y en otro no 
    foreach ($respuesta2 as $mes => $valoresMes) {
      foreach ($victimas as $victima => $valorVictima) {
        if (isset($victimas[$victima][$mes])) {
          continue;
        }else{
          $victimas[$victima][$mes] = '0';
        }
          asort($victimas[$victima]);
      }
    } 

    $filaInicialTabla = $numFila;
    foreach ($victimas as $victima => $valorVictima) {
        $Letra = "B";
        $sheet->setCellValue($Letra.$numFila, $victima);
        $valorTotal = 0;
        foreach ($valorVictima as $valores => $valor) {        
          $Letra++;
          $sheet->setCellValue($Letra.$numFila, $valor);
          $LetraF = $Letra;
          $valorTotal = $valorTotal + $valor;  
        }
        $LetraF++;
        $sheet->setCellValue(($LetraF).$numFila, $valorTotal);
  
        $numFila++;
        $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
    }
    $sheet->setCellValue("B".$numFila, "Total");
    // terminamos de llenar el cuerpo de la tabla

    // empezamos a llenar el foot de la tabla
    $tTotal = 0;
    $totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
    foreach ($victimas as $victima => $valorVictima) {
      foreach ($valorVictima as $mes => $valorMes) {   
        $totalMes[$mes] += $valorMes;
      }
    }

    $Letra = "C";
    foreach ($totalMes as $total) {
      if ($total <> 0) {
        $sheet->setCellValue($Letra.$numFila, $total);
        $Letra++;
        $tTotal += $total;
        $LetraF = $Letra;
        $LFStyle = $Letra;
      }    
    }  
    $sheet->setCellValue(($LetraF).$numFila, $tTotal);
    $finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
    $sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor); 
    $pieTablaPoblacion = "B".$finGrafica.":".$LFStyle.$finGrafica;
    // $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2); 

    // terminamos de llenar el foot de la tabla


    // INICIO GRAFICA
    $dataSeriesLabels = [];
    $dataSeriesValues = [];
    $filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

    // letra donde empiezan los datos
    $Letra = 'C'; 
    // exit(var_dump($letrasAbecedario));
    for ($i = 1; $i <= 26 ; $i++) { 
      if ($letrasAbecedario[$i] == $LetraF ) {
        $letraFinal = $letrasAbecedario[$i-1];
      }
    }

    $dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.($filaInicialTabla-1), null, 1);

    $dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

    $xAxisTickValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
    ];

    $series = new DataSeries(
        DataSeries::TYPE_BARCHART, // plotType
        DataSeries::GROUPING_CLUSTERED, // plotGrouping
        range(0, count($dataSeriesValues) - 1), // plotOrder
        $dataSeriesLabels, // plotLabel
        $xAxisTickValues, // plotCategory
        $dataSeriesValues        // plotValues
    );
    $series->setPlotDirection(DataSeries::DIRECTION_BAR);

    $layout = new Layout();
    $layout->setShowVal(true);
    $layout->setShowPercent(false);

    $plotArea = new PlotArea($layout, [$series]);

    $legend = new Legend(Legend::POSITION_RIGHT, null, false);

    $title = new Title('Totales por población victima');
    $yAxisLabel = new Title('Entregas ejecutadas');

    // Create the chart
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

    // no mandar las letras con $ en esta seccion
    $numFila = $numFila+2;
    $chart->setTopLeftPosition('B'.($numFila+2));

    // no mandar la letra con $ en esta seccion
    $numFila = $numFila+20;
    $chart->setBottomRightPosition('L'.($numFila+2));

    $sheet->addChart($chart);
    // FIN CREACION DE LA GRAFICA 
    // FIN SECCION TOTALES POR SEDE POBLACION VICTIMA

// INICIO SECCION TOTALES POR ETNIA
$numFila = $numFila+5;
$sheet->setCellValue('B'.$numFila, 'Totales por etnia');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesEtnia = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila++;

$mesesRecorridos = ""; 
$respuesta = [];
$respuesta2 = [];

foreach ($diasSemanas as $mes => $semanas) {
  $datos = "";
    $diaD = 1;
    $sem=0;
    //tabla donde se busca, según mes(obtenido de consulta anterior) y año
    $tabla="entregas_res_$mes$periodoActual"; 
    // ciclo para recorrer las semanas
    foreach ($semanas as $semana => $dias) {
      // ciclo para recorrer los dias de la semana
        foreach ($dias as $D => $dia) { 
        $datos.="SUM(D$diaD) + ";
        $diaD++;
      }
      $sem = $semana; //guardamos el último número de semana del mes, el cual incrementa sin reiniciar en cada mes.
    }
    $datos = trim($datos, "+ ");
    $consultaRes = "SELECT etnia.descripcion, $datos ";
    $consultaRes.=" AS TOTAL FROM $tabla JOIN etnia ON $tabla.etnia = etnia.id GROUP BY etnia";
    $periodo = 1;

    if ($resConsultaRes = $Link->query($consultaRes)) {
      if ($resConsultaRes->num_rows > 0) {
        while ($resEstrato = $resConsultaRes->fetch_assoc()) {
          $respuesta[$periodo] = $resEstrato;
          $periodo++;   
        }          
      }
    }
    $respuesta2[$mes] = $respuesta;
    $mesesRecorridos .= $mes;
}

$arrayMes = explode("0", $mesesRecorridos);
// funcion para quitar espacios vacios de un array
foreach ($arrayMes as $key => $link) {
  if($link === '') 
    { 
        unset($arrayMes[$key]); 
    } 
}

// llenamos el encabezado
$numFila++;
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica
$sheet->setCellValue("B".$numFila, "Etnia");
$Letra = "C";
// exit(var_dump($arrayMes));
foreach ($arrayMes as $meses) {
  foreach ($nombreMeses as $mes => $nombre) {
    if ($meses == $mes) {
      $nombreMes = $nombre;
    }
  }
  $sheet->setCellValue($Letra.$numFila, $nombreMes);
  $Letra++;
  $LFStyle = $Letra; //Última letra(Columna) de relleno para los estilos
}
$sheet->setCellValue($Letra.$numFila, 'Total');
$tituloTablaTotalesEtnia = 'B'.$numFila.':'.$LFStyle.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$LFStyle.$numFila)->applyFromArray($titulos);
$numFila++;
// terminamos de llenar el encabezado

// empezamos a llenar el body de la tabla 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($valoresMes as $valorMes => $valor) {
  // convertimos la respuesta a un array asociativo con la clave primaria edad mes
  $etnias[$valor['descripcion']][$mes] = $valor['TOTAL'];  
  }
}

// funcion para llenar campos cuando haya  un dato en un mes y en otro no 
foreach ($respuesta2 as $mes => $valoresMes) {
  foreach ($etnias as $etnia => $valorEtnia) {
    if (isset($etnias[$etnia][$mes])) {
      continue;
    }else{
      $etnias[$etnia][$mes] = '0';
    }
      asort($etnias[$etnia]);
  }
} 

$filaInicialTabla = $numFila;
foreach ($etnias as $etnia => $valorEtnia) {
  $Letra = "B";
  $sheet->setCellValue($Letra.$numFila, $etnia);
  $valorTotal = 0;
  foreach ($valorEtnia as $valores => $valor) {        
    $Letra++;
    $sheet->setCellValue($Letra.$numFila, $valor);
    $LetraF = $Letra;
    $valorTotal = $valorTotal + $valor;  
  }
    $LetraF++;
    $sheet->setCellValue(($LetraF).$numFila, $valorTotal);
    $numFila++;
    $letraFinLabels = $LetraF; //Letra de columna donde terminan los datos para los labels de la gráfica.
}
$sheet->setCellValue("B".$numFila, "Total");
// terminamos de llenar el cuerpo de la tabla

// empezamos a llenar el foot de la tabla
$tTotal = 0;
$totalMes = ["01" => 0, "02" => 0, "03" => 0, "04" => 0, "05" => 0, "06" => 0, "07" => 0, "08" => 0, "09" => 0, "10" => 0, "11" => 0, "12" => 0];
foreach ($etnias as $etnia => $valorEtnia) {
  foreach ($valorEtnia as $mes => $valorMes) {   
    $totalMes[$mes] += $valorMes;
  }
}

$Letra = "C";
foreach ($totalMes as $total) {
  if ($total <> 0) {
    $sheet->setCellValue($Letra.$numFila, $total);
    $Letra++;
    $tTotal += $total;
    $LetraF = $Letra;
    $LFStyle = $Letra;
  }    
}  

$sheet->setCellValue(($LetraF).$numFila, $tTotal);
$finGrafica = $numFila;//Número de Fila donde terminan los datos para la gráfica.
$sheet->getStyle("B".$filaInicialTabla.":".$LFStyle.($finGrafica-1))->applyFromArray($infor); 
$pieTablaTotalesEtnia = "B".$finGrafica.":".$LFStyle.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$LFStyle.$finGrafica)->applyFromArray($titulos2);
 
// terminamos de llenar el foot de la tabla

// INICIO GRAFICA
$dataSeriesLabels = [];
$dataSeriesValues = [];
$filaLabelsGrafica = $numFila; //Número de fila donde están los labels para la gráfica

// letra donde empiezan los datos
$Letra = 'C'; 
// exit(var_dump($letrasAbecedario));
for ($i = 1; $i <= 26 ; $i++) { 
  if ($letrasAbecedario[$i] == $LetraF ) {
    $letraFinal = $letrasAbecedario[$i-1];
  }
}

$dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$letraFinLabels.'$'.($filaInicialTabla-1), null, 1);

$dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$letraFinLabels.'$'.$filaInicialTabla.':$'.$letraFinLabels.'$'.($finGrafica-1).')', null, 4);

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.$filaInicialTabla.':$B$'.($finGrafica-1), null, 4), // Q1 to Q4
];

$series = new DataSeries(
  DataSeries::TYPE_BARCHART, // plotType
  DataSeries::GROUPING_CLUSTERED, // plotGrouping
  range(0, count($dataSeriesValues) - 1), // plotOrder
  $dataSeriesLabels, // plotLabel
  $xAxisTickValues, // plotCategory
  $dataSeriesValues        // plotValues
);
$series->setPlotDirection(DataSeries::DIRECTION_BAR);

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Totales por etnia');
$yAxisLabel = new Title('Entregas ejecutadas');

// Create the chart
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

// no mandar las letras con $ en esta seccion
$numFila = $numFila+2;
$chart->setTopLeftPosition('B'.($numFila+2));

// no mandar la letra con $ en esta seccion
$numFila = $numFila+30;
$chart->setBottomRightPosition('L'.($numFila+2));

$sheet->addChart($chart);
// FIN CREACION DE LA GRAFICA 
// FIN SECCION TOTALES POR SEDE POBLACION ETNIA
$numFila ++;
$numFila ++;
// INICIO SECCION VALOR RECURSOS EJECUTADOS
$numFila += 2;

$sheet->setCellValue('B'.$numFila, 'Valor de recursos ejecutados');
$sheet->mergeCells('B'.$numFila.':P'.$numFila);
$tituloTotalesValorRecursos = 'B'.$numFila.':P'.$numFila;
// $sheet->getStyle('B'.$numFila.':P'.$numFila)->applyFromArray($titulos);
$numFila += 2;

$consValores = "SELECT 'ValorContrato' AS Concepto, ValorContrato FROM parametros
                UNION
                SELECT CODIGO AS Concepto, ValorRacion AS ValorContrato FROM tipo_complemento WHERE valorRacion > 0;";
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
$numFila2 = $numFila;
$sheet->setCellValue('C'.$numFila, $valorContrato);
$sheet->getStyle('B'.$numFila)->applyFromArray($titulos1);
$sheet->getStyle("C".$numFila)->applyFromArray($infor);
$numFila++;

foreach ($valorRaciones as $complemento => $valor) {
  $sheet->setCellValue('B'.$numFila, 'Valor ofertado por '.$complemento);
  $sheet->setCellValue('C'.$numFila, $valor);
  $sheet->getStyle('B'.$numFila)->applyFromArray($titulos1);
  $sheet->getStyle("C".$numFila)->applyFromArray($infor);
  $numFila++;
}

$finGrafica = $numFila;
$sheet->getStyle('C'.$inicioGrafica.':C'.$finGrafica)->getNumberFormat()->setFormatCode('$ #,##0');


// INICIO TABLA TOTALES POR VALORES EJECUTADOS


$valorComplementos = [];
$totalesComplementos = []; 
$tipoComplementos = [];
$complementos = [];

$consTipoComplemento = "SELECT * FROM tipo_complemento";
$resTipoComplemento = $Link->query($consTipoComplemento);
if ($resTipoComplemento->num_rows > 0) {
  while ($TipoComplemento = $resTipoComplemento->fetch_assoc()) {
    $valorComplementos[$TipoComplemento['CODIGO']] = $TipoComplemento['ValorRacion'];
    $tipoComplementos[] = $TipoComplemento['CODIGO'];
    $complementos[$TipoComplemento['ID']] = $TipoComplemento['CODIGO'];
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

    $arrayCom;  
  if ($datos != "") {
    $datos = trim($datos, "+ ");
    $consComplementos ="SELECT t.CODIGO , $datos  AS total FROM entregas_res_$mes$periodoActual AS e right JOIN tipo_complemento AS t ON t.CODIGO = e.tipo_complem WHERE t.valorRacion > 0 GROUP BY t.CODIGO ";
    // echo $consComplementos."\n";
    $resComplementos = $Link->query($consComplementos);
    $tcom = [];
    if ($resComplementos->num_rows > 0) {
      while ($Complementos = $resComplementos->fetch_assoc()) {

        if ($Complementos['total'] == '' || $Complementos['total'] == null) {
          $Complementos['total'] = 0;
          // continue;
        }

        $ejecutado += $Complementos['total']*(isset($valorComplementos[$Complementos['CODIGO']]) ? $valorComplementos[$Complementos['CODIGO']] : 0); //porcentajes
        if (isset($totalesComplementos[$mes][$Complementos['CODIGO']])) {
          $totalesComplementos[$mes][$Complementos['CODIGO']]+=$Complementos['total']*(isset($valorComplementos[$Complementos['CODIGO']]) ? $valorComplementos[$Complementos['CODIGO']] : 0);
        } else {
          $totalesComplementos[$mes][$Complementos['CODIGO']]=$Complementos['total']*(isset($valorComplementos[$Complementos['CODIGO']]) ? $valorComplementos[$Complementos['CODIGO']] : 0);
        }
          $arrayCom[$Complementos['CODIGO']] = ($Complementos['CODIGO']);
      }

    }
  }


}

$numFila++;
$numFila = $numFila+6;
$sheet->setCellValue('B'.$numFila, 'Mes');
$letra = "B";
foreach ($arrayCom as $complemento => $set) {
  $letra++;
  $sheet->setCellValue($letra.$numFila, $complemento);
}

$numFila3 = $numFila;
$letra++;
$sheet->setCellValue($letra.$numFila, "Total");

$tituloTablaTotalesValorEjecutado = 'B'.$numFila.':'.$letra.$numFila;
// $sheet->getStyle('B'.$numFila.':'.$letra.$numFila)->applyFromArray($titulos);
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
$sheet->getStyle("B".$inicioGrafica.":".$letra.($finGrafica-1))->applyFromArray($infor);
$pieTablaValorEjecutado = "B".$finGrafica.":".$letra.$finGrafica;
// $sheet->getStyle("B".$finGrafica.":".$letra.$finGrafica)->applyFromArray($titulos2);

for ($i="C"; $i <= $letra ; $i++) { 
  $sheet->getStyle($i.$inicioGrafica.':'.$i.$finGrafica)->getNumberFormat()->setFormatCode('$ #,##0');
}
$finGrafica3 = $finGrafica;

$porejecutar = $valorContrato - $ejecutado;

$porcenEjecutado = 100 * $ejecutado / $valorContrato;
$porcenPorEjecutar = 100 * $porejecutar / $valorContrato;

$sheet->setCellValue('E'.$numFila2, '');
$sheet->setCellValue('F'.$numFila2, 'Total');
$sheet->setCellValue('G'.$numFila2, '%');
$inicioGrafica = $numFila2;
$tituloTablaTotalesPorcentaje = 'E'.$numFila2.':G'.$numFila2;
// $sheet->getStyle('E'.$numFila2.':G'.$numFila2)->applyFromArray($titulos);
$numFila2++;

$CIStyle = $numFila2;
$sheet->setCellValue('E'.$numFila2, 'Valor contrato');
$sheet->setCellValue('F'.$numFila2, $valorContrato);
$sheet->setCellValue('G'.$numFila2, '100 %');

$numFila2++;

$sheet->setCellValue('E'.$numFila2, 'Valor por ejecutar');
$sheet->setCellValue('F'.$numFila2, $porejecutar);
$sheet->setCellValue('G'.$numFila2, number_format($porcenPorEjecutar,2).' %');

$numFila2++;

$sheet->setCellValue('E'.$numFila2, 'Valor ejecutado');
$sheet->setCellValue('F'.$numFila2, $ejecutado);
$sheet->setCellValue('G'.$numFila2, number_format($porcenEjecutado,2).' %');
$finGrafica = $numFila2;

$sheet->getStyle("E".$CIStyle.":G".$numFila2)->applyFromArray($infor);
$sheet->getStyle('F'.$inicioGrafica.':F'.$finGrafica)->getNumberFormat()->setFormatCode('$ #,##0');
$numFila2++;



$dataSeriesLabels = [];
$dataSeriesValues = [];
$xAxisTickValues = [];
$Letra = "C";
$letraFinal = '';

for ($i = 1; $i <= 26 ; $i++) { 
  if ($letrasAbecedario[$i] == $letraFinLabels ) {
    $letraFinal = $letrasAbecedario[$i-1];
  }
}

for ($i=$Letra; $i <= $letraFinal ; $i++) { 
 $filaLabels = $inicioGrafica-1;
 $dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$i.'$'.$numFila3, null, 1);
 $dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$'.$i.'$'.($numFila3+1).':$'.$i.'$'.$finGrafica3.')', null, 4);
}

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$'.($numFila3+1).':$B$'.($finGrafica3-1), null, 4), // Q1 to Q4
];

// Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues        // plotValues
);

// exit(var_dump($series));

$layout = new Layout();
$layout->setShowVal(true);
$layout->setShowPercent(false);

$plotArea = new PlotArea($layout, [$series]);

$legend = new Legend(Legend::POSITION_RIGHT, null, false);

$title = new Title('Valor de recursos ejecutados');
$yAxisLabel = new Title('Recursos ejecutados');

// Create the chart
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
$numFila = $numFila+20;
$chart->setBottomRightPosition('L'.$numFila);

$sheet->addChart($chart);



$dataSeriesLabels = [];
$dataSeriesValues = [];
$xAxisTickValues = [];

 $dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$F$'.$inicioGrafica, null, 1);
 $inicioGrafica += 2;
 $dataSeriesValues[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '(Worksheet!$F$'.$inicioGrafica.':$F$'.$finGrafica.')', null, 4);

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$E$'.$inicioGrafica.':$E$'.$finGrafica, null, 4), // Q1 to Q4
];

// Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_PIECHART, // plotType
    null, // plotGrouping (Pie charts don't have any grouping)
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues          // plotValues
);

// exit(var_dump($series));

$layout = new Layout();
$layout->setShowVal(false);
$layout->setShowPercent(true);

$plotArea = new PlotArea($layout, [$series]);
$legend = new Legend(Legend::POSITION_RIGHT, null, false);

// $title = new Title('Valor de recursos ejecutados');
// $yAxisLabel = new Title('Recursos ejecutadas');

// Create the chart
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

$numFila = $inicioGrafica-2;
$chart->setTopLeftPosition('L'.$numFila);
$numFila = $numFila+12;
$chart->setBottomRightPosition('O'.$numFila);

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

$sheet->getRowDimension("4")->setRowHeight(30);

$color = [
 'fill' => [
     'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
     'color' => ['argb' => 'FFFFFF'],
     ],
    ];

$sheet->getStyle("A1:Z1000")->applyFromArray($color);

$sheet->getStyle('B9:P9')->applyFromArray($titulos);
$sheet->getStyle($titulosTablaTotalesSemanas)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesComplementos)->applyFromArray($titulos);
$sheet->getStyle($titulosTablaTotalesComplementos)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesGenero)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesGenero)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesEdad)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesEdad)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesEstrato)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesEstrato)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesResidencia)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesResidencia)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesEscolaridad)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesEscolaridad)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesJornada)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesJornada)->applyFromArray($titulos);
if (isset($tituloTotalesMunicipio)) {
    $sheet->getStyle($tituloTotalesMunicipio)->applyFromArray($titulos);
    $sheet->getStyle($tituloTablaTotalesMunicipio)->applyFromArray($titulos);
}
if (isset($tituloTotalesSedeEducativa)) {
    $sheet->getStyle($tituloTotalesSedeEducativa)->applyFromArray($titulos);
    $sheet->getStyle($tituloTablaTotalesSedeEducativa)->applyFromArray($titulos);
}
$sheet->getStyle($tituloTotalesDiscapacidad)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesDiscapacidad)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesPoblacion)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesPoblacion)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesEtnia)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesEtnia)->applyFromArray($titulos);
$sheet->getStyle($tituloTotalesValorRecursos)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesValorEjecutado)->applyFromArray($titulos);
$sheet->getStyle($tituloTablaTotalesPorcentaje)->applyFromArray($titulos);

$sheet->getStyle($pieTablaTotalesSemana)->applyFromArray($titulos2);
$sheet->getStyle($pieTablaTotalesComplemento)->applyFromArray($titulos2);
$sheet->getStyle($pieTabalTotalesGenero)->applyFromArray($titulos2);
$sheet->getStyle($pieTablaTotalesEdad)->applyFromArray($titulos2);
$sheet->getStyle($pieTablaTotalesEstrato)->applyFromArray($titulos2);
$sheet->getStyle($pieTablaTotalesResidencia)->applyFromArray($titulos2);
$sheet->getStyle($pieTablaTotalesEscolaridad)->applyFromArray($titulos2);
$sheet->getStyle($pieTablaTotalesJornada)->applyFromArray($titulos2);
if (isset($pieTablaTotalesMunicipio)) {
    $sheet->getStyle($pieTablaTotalesMunicipio)->applyFromArray($titulos2);
}
if (isset($pieTablaTotalesSede)) {
    $sheet->getStyle($pieTablaTotalesSede)->applyFromArray($titulos2);
}
$sheet->getStyle($pieTablaTotalesDiscapacidad)->applyFromArray($titulos2);
$sheet->getStyle($pieTablaPoblacion)->applyFromArray($titulos2);
$sheet->getStyle($pieTablaTotalesEtnia)->applyFromArray($titulos2);
$sheet->getStyle($pieTablaValorEjecutado)->applyFromArray($titulos2);

$sheet->getColumnDimension("B")->setWidth(35); 

$writer = new Xlsx($spreadsheet);
$reader->setReadDataOnly(false);
// exit(var_dump($writer));
$writer->setIncludeCharts(TRUE);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="estadisticas_avanzadas.xlsx"');
$writer->save('php://output','estadisticas_avanzadas.xlsx');





