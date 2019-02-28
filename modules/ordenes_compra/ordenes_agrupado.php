<?php
include '../../config.php';
require_once '../../autentication.php';
require_once '../../db/conexion.php';
include '../../php/funciones.php';


$largoNombre = 30;
$sangria = " - ";
$tamannoFuente = 6;

ini_set('memory_limit','6000M');

//var_dump($_POST);

$tablaAnno = $_SESSION['periodoActual'];
$tablaAnnoCompleto = $_SESSION['periodoActualCompleto'];

//require_once 'autenticacion.php';

require '../../vendor/autoload.php';

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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$titulos = [
    'font' => [
        'bold' => true,
        'size'  => 7,
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

$infor = [
    'font' => [
        'size'  => 7,
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


$rowNum = 1;

$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

date_default_timezone_set('America/Bogota');
$hoy = date("d/m/Y");
$fechaDespacho = $hoy;

// Se va a recuperar el mes y el año para las tablaMesAnno
$mesAnno = '';
$mes = $_POST['mesiConsulta'];
if($mes < 10){
  $mes = '0'.$mes;
}
$mes = trim($mes);
$anno = $_POST['annoi'];
$anno = substr($anno, -2);
$anno = trim($anno);
$mesAnno = $mes.$anno;

  $cget = "SELECT * FROM grupo_etario";
  $resGrupoEtario = $Link->query($cget);
  if ($resGrupoEtario->num_rows > 0) {
    while ($ge = $resGrupoEtario->fetch_assoc()) {
      $get[] = $ge['DESCRIPCION'];
    }
  }

$ruta = '';
if(isset($_POST['rutaNm']) && $_POST['rutaNm']!= ''){
  $ruta = $_POST['rutaNm'];
}

//var_dump($_POST);
$corteDeVariables = 15;
if(isset($_POST['seleccionarVarios'])){
  $corteDeVariables++;
}
if(isset($_POST['informeRuta'])){
  $corteDeVariables++;
}
if(isset($_POST['ruta'])){
  $corteDeVariables++;
}
if(isset($_POST['rutaNm'])){
  $corteDeVariables++;
}
$_POST = array_slice($_POST, $corteDeVariables);
$_POST = array_values($_POST);
//var_dump($_POST);

$annoActual = $tablaAnnoCompleto;
//var_dump($_POST);
$despachosRecibidos = $_POST;

// Se va a hacer una cossulta pare cojer los datos de cada movimiento, entre ellos el
// municipio que lo usaremos en los encabezados de la tabla.

$despachos = array();
$sedes = array();
$tipos = array();
$semanas = array();
$municipios = array();

$semanasMostrar = array();
$diasMostrar = array();
$ciclos = array();
$ciclo = '';
$sede = '';

$dias = '';
$mes = '';

foreach ($despachosRecibidos as &$valor){

  //echo "<br>".$valor."<br>";

  $consulta = " select de.*, tc.descripcion , u.Ciudad, tc.jornada
  from despachos_enc$mesAnno de
  inner join sedes$anno  s on de.cod_Sede = s.cod_sede
  inner join ubicacion u on s.cod_mun_sede = u.CodigoDANE
  left join tipo_complemento tc on de.Tipo_Complem = tc.CODIGO
  where Tipo_Doc = 'DES' and de.Num_Doc = $valor ";

  //echo "<br>$consulta<br>";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

  if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();

    $despacho['num_doc'] = $row['Num_Doc'];
    $despacho['cod_sede'] = $row['cod_Sede'];
    $despacho['tipo_complem'] = $row['Tipo_Complem'];
    $modalidad = $despacho['tipo_complem'];
    $despacho['semana'] = $row['Semana'];
    $despacho['cobertura'] = $row['Cobertura'];
    $despacho['ciudad'] = $row['Ciudad'];
    $descripcionTipo = $row['descripcion'];
    $jornada = $row['jornada'];

    $aux = $row['FechaHora_Elab'];
    $aux = strtotime($aux);
    if($fechaDespacho < $aux){
      $fechaDespacho = date("d/m/Y",$aux);
    }

    // Agregando elementos a los demas array_walk_recursive

    $sedes[] = $row['cod_Sede'];
    $tipos[] = $row['Tipo_Complem'];
    $semanas[] = $row['Semana'];
    $municipios[] = $row['Ciudad'];

    //TRATAMIENTO DE LOS DIAS

    // Buscar el mes de la semana a la que pertenecen los despachos
    $auxDias = $row['Dias'];
    $diasDespacho = $row['Dias'];
  $diasMostrar[] = $auxDias;

  $auxMenus = $row['Menus'];
  $menusMostrar[] = $auxMenus;

  $arrayDiasDespacho = explode(',', $diasDespacho)
  ;

if (!in_array($row['Semana'], $semanasMostrar, true)) {
  $semanasMostrar[] =  $row['Semana'];
  $semana = $row['Semana'];
  $consulta = " select * from planilla_semanas where SEMANA = '$semana' ";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

  $cantDias = $resultado->num_rows;
  if($resultado->num_rows >= 1){
    $mesInicial = '';
    $mesesIniciales = 0;
    while($row = $resultado->fetch_assoc()){

      $clave = array_search(intval($row['DIA']), $arrayDiasDespacho);
      if($clave !== false){
        $ciclo = $row['CICLO'];
        if($mesInicial != $row['MES']){
          $mesesIniciales++;
          if($mesesIniciales > 1){
            $dias .= " de  $mes ";
          }
          $mesInicial = $row['MES'];
          $mes = $row['MES'];
          $mes = mesEnLetras($mes);
        }else{
          if($dias != ''){
            $dias .= ', ';
          }
        }
        $dias = $dias.intval($row['DIA']);
      }// Termina el if de la Clave


    }
    $dias .= " de  $mes";
  }
}

  }
  $despachos[] = $despacho;
}

$auxDias = '';
for ($i=0; $i < count($diasMostrar) ; $i++) {
  if($i > 0){
    $auxDias = $auxDias.",";
  }
  $auxDias = $auxDias.$diasMostrar[$i];
}
$auxDias = explode(',', $auxDias);
$auxDias = array_unique ($auxDias);
sort($auxDias);
$cantDias = count($auxDias);
$auxDias = implode(", ",$auxDias);
sort($semanasMostrar);
sort($ciclos);

$auxDias = "X ".$cantDias." DIAS ".$auxDias." ".$mes;
$auxDias = strtoupper($auxDias);

$auxSemana = '';
for ($i=0; $i < count($semanasMostrar) ; $i++) {
  if($i > 0){
    $auxSemana = $auxSemana.", ";
  }
  $auxSemana = $auxSemana.$semanasMostrar[$i];
}

$auxCiclos = '';
for ($i=0; $i < count($ciclos) ; $i++) {
  if($i > 0){
    $auxCiclos = $auxCiclos.", ";
  }
  $auxCiclos = $auxCiclos.$ciclos[$i];
}


$auxMenus = '';
for ($i=0; $i < count($menusMostrar) ; $i++) {
  if($i > 0){
    $auxMenus = $auxMenus.",";
  }
  $auxMenus = $auxMenus.$menusMostrar[$i];
}
$auxMenus = explode(',', $auxMenus);
$auxMenus = array_unique ($auxMenus);
sort($auxMenus);
$auxMenus = implode(", ",$auxMenus);

$municipios = array_unique($municipios);
$municipios = array_values($municipios);
$tipo = $tipos[0];
$semana = $semanas[0];
// Se armara un array con las coverturas de las sedes para cada uno de los grupos etarios
// y al final se creara un array con los totales de las sedes.

$total1 = 0;
$total2 = 0;
$total3 = 0;
$totalTotal = 0;
for ($i=0; $i < count($sedes) ; $i++) {

  $auxSede = $sedes[$i];

  $consulta = " select cod_sede, Etario1_$tipo, Etario2_$tipo, Etario3_$tipo
  from sedes_cobertura where semana = '$semana' and cod_sede = $auxSede and Ano = $annoActual ";

  // Consulta que busca las coberturas de las diferentes sedes.
  //echo "<br><br>".$consulta."<br><br>";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){


    while($row = $resultado->fetch_assoc()) {
      $sedeCobertura['cod_sede'] = $row['cod_sede'];
      $aux1 = "Etario1_$tipo";
      $sedeCobertura['grupo1'] = $row[$aux1];
      $aux2 = "Etario2_$tipo";
      $sedeCobertura['grupo2'] = $row[$aux2];
      $aux3 = "Etario3_$tipo";
      $sedeCobertura['grupo3'] = $row[$aux3];
      $sedeCobertura['total'] = $row[$aux1] + $row[$aux2] + $row[$aux3];
      $sedesCobertura[] = $sedeCobertura;
      $total1 = $total1 + $row[$aux1];
      $total2 = $total2 + $row[$aux2];
      $total3 = $total3 + $row[$aux3];
      $totalTotal = $totalTotal +  $sedeCobertura['total'];
    }

  }
}

$totalesSedeCobertura  = array(
    "grupo1" => $total1,
    "grupo2" => $total2,
    "grupo3" => $total3,
    "total"  => $totalTotal
);
// Termina el tema de las coberturas por sede

$totalGrupo1 = 0;
$totalGrupo2 = 0;
$totalGrupo3 = 0;

// Vamos a buscar los alimentos de los depachos
$alimentos = array();
for ($i=0; $i < count($despachos) ; $i++) {
  $despacho = $despachos[$i];
  $numero = $despacho['num_doc'];
  //$consulta = " select * from despachos_det$mesAnno where Tipo_Doc = 'DES' and Num_Doc = $numero ";



  $consulta = " select DISTINCT dd.id, dd.*, pmd.CantU1,  pmd.CantU2, pmd.CantU3, pmd.CantU4, pmd.CantU5, pmd.CanTotalPresentacion
  from despachos_det$mesAnno dd
  left join productosmovdet$mesAnno pmd on dd.Tipo_Doc = pmd.Documento and dd.Num_Doc = pmd.Numero
  where dd.Tipo_Doc = 'DES' and dd.Num_Doc = $numero  ";

 // echo "<br>".$consulta."<br>";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()){
      $alimento = array();
      $alimento['codigo'] = $row['cod_Alimento'];
      $auxGrupo = $row['Id_GrupoEtario'];
      $alimento['grupo'.$auxGrupo] = $row['Cantidad'];



      $alimento['cantotalpresentacion'] = $row['CanTotalPresentacion'];
      $alimento['cantu2'] = $row['CantU2'];
      $alimento['cantu3'] = $row['CantU3'];
      $alimento['cantu4'] = $row['CantU4'];
      $alimento['cantu5'] = $row['CantU5'];

      // Guardamos el numero de documento para discriminar la unidades de cada despacho
      $alimento['Num_Doc'] = $row['Num_Doc'];



    $alimento['componente'] = '';
    $alimento['presentacion'] = '';
    $alimento['grupo_alim'] = '';

    $alimento['nombreunidad2'] = '';
    $alimento['nombreunidad3'] = '';
    $alimento['nombreunidad4'] = '';
    $alimento['nombreunidad5'] = '';
    $alimentos[] = $alimento;
    }
  }
}

// Vamos unificar los alimentos para que no se repitan
$alimento = $alimentos[0];

if(!isset($alimento['grupo1'])){ $alimento['grupo1'] = 0;}else{ $totalGrupo1 = $totalesSedeCobertura['grupo1']; }
if(!isset($alimento['grupo2'])){ $alimento['grupo2'] = 0;}else{ $totalGrupo2 = $totalesSedeCobertura['grupo2']; }
if(!isset($alimento['grupo3'])){ $alimento['grupo3'] = 0;}else{ $totalGrupo3 = $totalesSedeCobertura['grupo3']; }
$alimentosTotales = array();
$alimentosTotales[] = $alimento;

for ($i=1; $i < count($alimentos) ; $i++) {
  $alimento = $alimentos[$i];
  if(!isset($alimento['grupo1'])){ $alimento['grupo1'] = 0;}else{ $totalGrupo1 = $totalesSedeCobertura['grupo1']; }
  if(!isset($alimento['grupo2'])){ $alimento['grupo2'] = 0;}else{ $totalGrupo2 = $totalesSedeCobertura['grupo2']; }
  if(!isset($alimento['grupo3'])){ $alimento['grupo3'] = 0;}else{ $totalGrupo3 = $totalesSedeCobertura['grupo3']; }
  $encontrado = 0;
  for ($j=0; $j < count($alimentosTotales) ; $j++) {
      $alimentoTotal = $alimentosTotales[$j];
      if($alimento['codigo'] == $alimentoTotal['codigo']){
        $encontrado++;

        // Marzo 30 de 2017 sumando presentaciones

      if($alimentoTotal['Num_Doc'] != $alimento['Num_Doc']){
          $alimentoTotal['cantotalpresentacion'] = $alimentoTotal['cantotalpresentacion'] + $alimento['cantotalpresentacion'];
          $alimentoTotal['cantu2'] = $alimentoTotal['cantu2'] + $alimento['cantu2'];
          $alimentoTotal['cantu3'] = $alimentoTotal['cantu3'] + $alimento['cantu3'];
          $alimentoTotal['cantu4'] = $alimentoTotal['cantu4'] + $alimento['cantu4'];
          $alimentoTotal['cantu5'] = $alimentoTotal['cantu5'] + $alimento['cantu5'];
          $alimentoTotal['Num_Doc'] = $alimento['Num_Doc'];
      }

        // Marzo 30 de 2017 sumando presentaciones

        $alimentoTotal['grupo1'] = $alimentoTotal['grupo1'] + $alimento['grupo1'];
        $alimentoTotal['grupo2'] = $alimentoTotal['grupo2'] + $alimento['grupo2'];
        $alimentoTotal['grupo3'] = $alimentoTotal['grupo3'] + $alimento['grupo3'];

        $alimentosTotales[$j] = $alimentoTotal;
        break;
      }
  }
  if($encontrado == 0){
    $alimentosTotales[] = $alimento;
  }
}

//var_dump($alimentosTotales);

// Vamos a traer los datos que faltan para mostrar en la tabla
for ($i=0; $i < count($alimentosTotales) ; $i++) {
  $alimentoTotal = $alimentosTotales[$i];
  $auxCodigo = $alimentoTotal['codigo'];
  $consulta = " select distinct ftd.codigo, ftd.Componente,p.nombreunidad2 presentacion,m.grupo_alim,m.orden_grupo_alim, p.NombreUnidad2, p.NombreUnidad3, p.NombreUnidad4, p.NombreUnidad5
  from  fichatecnicadet ftd
  inner join productos$anno  p on ftd.codigo=p.codigo
  inner join menu_aportes_calynut m on ftd.codigo=m.cod_prod
  where ftd.codigo = $auxCodigo and ftd.tipo = 'Alimento' ";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();
    $alimentoTotal['componente'] = $row['Componente'];
    $alimentoTotal['presentacion'] = $row['presentacion'];
    $alimentoTotal['grupo_alim'] = $row['grupo_alim'];
    $alimentoTotal['orden_grupo_alim'] = $row['orden_grupo_alim'];

    $alimentoTotal['nombreunidad2'] = $row['NombreUnidad2'];
    $alimentoTotal['nombreunidad3'] = $row['NombreUnidad3'];
    $alimentoTotal['nombreunidad4'] = $row['NombreUnidad4'];
    $alimentoTotal['nombreunidad5'] = $row['NombreUnidad5'];

    $alimentosTotales[$i] = $alimentoTotal;
  }

}

unset($sort);
unset($grupo);

$sort = array();
$grupo = array();

foreach($alimentosTotales as $kOrden=>$vOrden) {
    $sort['componente'][$kOrden] = $vOrden['componente'];
    $sort['grupo_alim'][$kOrden] = $vOrden['orden_grupo_alim']; //Se cambia el orden de acuerdo al orden por grupo de alimento
    $grupo[$kOrden] = $vOrden['grupo_alim'];
}
array_multisort($sort['grupo_alim'], SORT_ASC,$alimentosTotales);
sort($grupo);

// HEADER

$inicioTitulos = $rowNum;

$logoInfopae = $_SESSION['p_Logo ETC'];

$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath($logoInfopae);
$drawing->setHeight(60);
$drawing->setCoordinates('A'.$rowNum);
$drawing->setOffsetX(5);
$drawing->setOffsetY(5);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$sheet->mergeCells('A'.$rowNum.':H'.($rowNum+3));

$sheet->setCellValue('I'.$rowNum, 'PROGRAMA DE ALIMENTACIÓN ESCOLAR');
$sheet->mergeCells('I'.$rowNum.':R'.$rowNum);
$rowNum++;
$sheet->setCellValue('I'.$rowNum, 'REMISIÓN ENTREGA DE VÍVERES EN INSTITUCIÓN EDUCATIVA');
$sheet->mergeCells('I'.$rowNum.':R'.$rowNum);
$rowNum++;
$sheet->setCellValue('I'.$rowNum, $descripcionTipo);
$sheet->mergeCells('I'.$rowNum.':R'.$rowNum);
$rowNum+=2;

$sheet->setCellValue('A'.$rowNum, 'OPERADOR:');
$sheet->mergeCells('A'.$rowNum.':C'.$rowNum);
$sheet->setCellValue('D'.$rowNum, $_SESSION['p_Operador']);
$sheet->mergeCells('D'.$rowNum.':K'.$rowNum);

$sheet->setCellValue('L'.$rowNum, 'FECHA:');
$sheet->mergeCells('L'.$rowNum.':M'.$rowNum);
$sheet->setCellValue('N'.$rowNum, $fechaDespacho);
$sheet->mergeCells('N'.$rowNum.':R'.$rowNum);

$rowNum++;

$sheet->setCellValue('A'.$rowNum, 'ETC:');
$sheet->mergeCells('A'.$rowNum.':C'.$rowNum);
$sheet->setCellValue('D'.$rowNum, $_SESSION['p_Nombre ETC']);
$sheet->mergeCells('D'.$rowNum.':H'.$rowNum);

if($ruta == '' || $ruta == 'Todos'){

  $sheet->setCellValue('I'.$rowNum, 'MUNICIPIO O VEREDA:');
  $sheet->mergeCells('I'.$rowNum.':J'.$rowNum);

  $aux = '';
  for ($ii=0; $ii < count($municipios) ; $ii++) {
    if($ii > 0){
      $aux = $aux.", ";
    }
    $aux = $aux.$municipios[$ii];
  }

  $sheet->setCellValue('K'.$rowNum, $aux);
  $sheet->mergeCells('K'.$rowNum.':R'.$rowNum);

} else {

  $sheet->setCellValue('I'.$rowNum, 'RUTA:');
  $sheet->mergeCells('I'.$rowNum.':J'.$rowNum);
  $sheet->setCellValue('K'.$rowNum, $ruta);
  $sheet->mergeCells('K'.$rowNum.':R'.$rowNum);

}

$rowNum++;

$sheet->setCellValue('A'.$rowNum, 'RANGO DE EDAD');
$sheet->mergeCells('A'.$rowNum.':C'.$rowNum);
$sheet->setCellValue('D'.$rowNum, 'N° DE RACIONES ADJUDICADAS');
$sheet->mergeCells('D'.$rowNum.':F'.$rowNum);
$sheet->setCellValue('G'.$rowNum, 'N° DE RACIONES ATENDIDAS');
$sheet->mergeCells('G'.$rowNum.':I'.$rowNum);
$sheet->setCellValue('J'.$rowNum, 'N° DE DÍAS A ATENDER');
$sheet->mergeCells('J'.$rowNum.':L'.$rowNum);
$sheet->setCellValue('M'.$rowNum, 'N° DE MENÚ Y SEMANA DEL CICLO DE MENÚS ENTREGADO');
$sheet->mergeCells('M'.$rowNum.':O'.$rowNum);
$sheet->setCellValue('P'.$rowNum, 'TOTAL RACIONES');
$sheet->mergeCells('P'.$rowNum.':R'.$rowNum);

$rowNum++;

$sheet->setCellValue('A'.$rowNum, $get[0]);
$sheet->mergeCells('A'.$rowNum.':C'.$rowNum);
$sheet->setCellValue('A'.($rowNum+1), $get[1]);
$sheet->mergeCells('A'.($rowNum+1).':C'.($rowNum+1));
$sheet->setCellValue('A'.($rowNum+2), $get[2]);
$sheet->mergeCells('A'.($rowNum+2).':C'.($rowNum+2));

$sheet->setCellValue('D'.$rowNum, $totalGrupo1);
$sheet->mergeCells('D'.$rowNum.':F'.$rowNum);
$sheet->setCellValue('D'.($rowNum+1), $totalGrupo2);
$sheet->mergeCells('D'.($rowNum+1).':F'.($rowNum+1));
$sheet->setCellValue('D'.($rowNum+2), $totalGrupo3);
$sheet->mergeCells('D'.($rowNum+2).':F'.($rowNum+2));

$sheet->setCellValue('G'.$rowNum, $totalGrupo1);
$sheet->mergeCells('G'.$rowNum.':I'.$rowNum);
$sheet->setCellValue('G'.($rowNum+1), $totalGrupo2);
$sheet->mergeCells('G'.($rowNum+1).':I'.($rowNum+1));
$sheet->setCellValue('G'.($rowNum+2), $totalGrupo3);
$sheet->mergeCells('G'.($rowNum+2).':I'.($rowNum+2));

$sheet->setCellValue('J'.$rowNum, $auxDias);
$sheet->mergeCells('J'.$rowNum.':L'.$rowNum);
$sheet->setCellValue('J'.($rowNum+1), 'SEMANA: '.$semana);
$sheet->mergeCells('J'.($rowNum+1).':L'.($rowNum+1));

$sheet->setCellValue('M'.$rowNum, 'SEMANA: '.$auxCiclos);
$sheet->mergeCells('M'.$rowNum.':O'.$rowNum);
$sheet->setCellValue('M'.($rowNum+1), 'MENUS: '.$auxMenus);
$sheet->mergeCells('M'.($rowNum+1).':O'.($rowNum+1));

$jm = '';
$jt = '';

// 2 es la jornada de la mañana
// 3 es la jornada de la tarde
if($jornada == 2){
  $jm = $totalGrupo1 + $totalGrupo2 + $totalGrupo3;
}else if($jornada == 3){
  $jt = $totalGrupo1 + $totalGrupo2 + $totalGrupo3;
}

$sheet->setCellValue('P'.$rowNum, 'JM:'.$jm);
$sheet->mergeCells('P'.$rowNum.':R'.$rowNum);
$sheet->setCellValue('P'.($rowNum+1), 'JT:'.$jt);
$sheet->mergeCells('P'.($rowNum+1).':R'.($rowNum+1));

$rowNum+=3;

$sheet->setCellValue('A'.$rowNum, 'ALIMENTO');
$sheet->mergeCells('A'.$rowNum.':A'.($rowNum+1));

$sheet->setCellValue('B'.$rowNum, 'CNT DE ALIMENTOS POR NÚMEROS DE RACIONES');
$sheet->mergeCells('B'.$rowNum.':D'.$rowNum);

  $etario_1 = str_replace(" + 11 meses", "", $get[0]);
  $etario_2 = str_replace(" + 11 meses", "", $get[1]);
  $etario_3 = str_replace(" + 11 meses", "", $get[2]);

  $etario_1 = str_replace(" años", "", $etario_1);
  $etario_2 = str_replace(" años", "", $etario_2);
  $etario_3 = str_replace(" años", "", $etario_3);

$sheet->setCellValue('B'.($rowNum+1), $etario_1." años");
$sheet->setCellValue('C'.($rowNum+1), $etario_2." años");
$sheet->setCellValue('D'.($rowNum+1), $etario_3." años");

$sheet->setCellValue('E'.$rowNum, 'UNIDAD DE MEDIDA');
$sheet->mergeCells('E'.$rowNum.':E'.($rowNum+1));
$sheet->setCellValue('F'.$rowNum, 'CNT TOTAL');
$sheet->mergeCells('F'.$rowNum.':F'.($rowNum+1));

$sheet->setCellValue('G'.$rowNum, 'CANTIDAD ENTREGADA');
$sheet->mergeCells('G'.$rowNum.':I'.$rowNum);
$sheet->setCellValue('G'.($rowNum+1), 'TOTAL');
$sheet->setCellValue('H'.($rowNum+1), 'C');
$sheet->setCellValue('I'.($rowNum+1), 'NC');

$sheet->setCellValue('J'.$rowNum, 'ESPECIFICACIÓN DE CALIDAD');
$sheet->mergeCells('J'.$rowNum.':K'.$rowNum);
$sheet->setCellValue('J'.($rowNum+1), 'C');
$sheet->setCellValue('K'.($rowNum+1), 'NC');

$sheet->setCellValue('L'.$rowNum, 'FALTANTES');
$sheet->mergeCells('L'.$rowNum.':N'.$rowNum);
$sheet->setCellValue('L'.($rowNum+1), 'SI');
$sheet->setCellValue('M'.($rowNum+1), 'NO');
$sheet->setCellValue('N'.($rowNum+1), 'CANT');

$sheet->setCellValue('O'.$rowNum, 'DEVOLUCIÓN');
$sheet->mergeCells('O'.$rowNum.':Q'.$rowNum);
$sheet->setCellValue('O'.($rowNum+1), 'SI');
$sheet->setCellValue('P'.($rowNum+1), 'NO');
$sheet->setCellValue('Q'.($rowNum+1), 'CANT');

$rowNum+=2;

$finTitulos = $rowNum-1;

$inicioInfo = $rowNum;

// HEADER

//var_dump($item);
$filas = 0;
$grupoAlimActual = '';
$grupoAlimActual = '';


   for ($i=0; $i < count($alimentosTotales ) ; $i++) {
      $item = $alimentosTotales[$i];
    if($item['componente'] != ''){


    if(1==1){

      $aux = $item['componente'];

      $sheet->setCellValue('A'.$rowNum, $aux);


      if($item['presentacion'] == 'u'){
         $aux = round(0+$item['grupo1']);
      }else{
         $aux = 0+$item['grupo1'];
         $aux = number_format($aux, 2, '.', '');
      }
        $sheet->setCellValue('B'.$rowNum, $aux);


    if($item['presentacion'] == 'u'){
      $aux = round(0+$item['grupo2']);
    }else{
      $aux = 0+$item['grupo2'];
      $aux = number_format($aux, 2, '.', '');
    }
      $sheet->setCellValue('C'.$rowNum, $aux);

    if($item['presentacion'] == 'u'){
      $aux = round(0+$item['grupo3']);
    }else{
      $aux = 0+$item['grupo3'];
      $aux = number_format($aux, 2, '.', '');
    }
        $sheet->setCellValue('D'.$rowNum, $aux);


        $sheet->setCellValue('E'.$rowNum, $item['presentacion']);

    $aux = $item['grupo1']+$item['grupo2']+$item['grupo3'];
    $aux = number_format($aux, 2, '.', '');

    //MOSTRAR O NO CUANDO HAY PRESENTACIONES

    // Para no mostrar lso totales de los alimentos que tienen diferentes presentaciones.
    if($item['cantotalpresentacion'] > 0 ){
      //$aux = '';
    }

    //Imprimiendo CNT TOTAL
        $sheet->setCellValue('F'.$rowNum, $aux);

    if($item['presentacion'] == 'u'){
      $aux = round(0+$aux);
    }
    else{
      $aux = number_format($aux, 2, '.', '');
    }

    if($item['cantotalpresentacion'] > 0 ){
      $aux = $item['cantotalpresentacion'];
      $aux2 = $aux;
      if($item['presentacion'] == 'u'){
        $aux = round(0+$aux);
      }
      else{
        $aux = number_format($aux, 2, '.', '');
      }
    }

    // Para no mostrar lso totales de los alimentos que tienen diferentes presentaciones.
    if($item['cantotalpresentacion'] > 0 ){
      //$aux = '';
    }

  // CANTIDAD ENTREGADA
        $sheet->setCellValue('G'.$rowNum, $aux);
        $rowNum++;
}

$alimento = $item;

    $unidad = 2;
    if($alimento['cantu'.$unidad] > 0){
      $presentacion = " ".$alimento['nombreunidad'.$unidad];
      $aux = $sangria.$alimento['componente'].$presentacion;
      $long_nombre=strlen($aux);
      if($long_nombre > $largoNombre){
        $aux = substr($aux,0,$largoNombre);
      }
      $sheet->setCellValue('A'.$rowNum, $aux);
      $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
      $sheet->setCellValue('F'.$rowNum, $aux);
      $rowNum++;
    }

    $unidad = 3;
    if($alimento['cantu'.$unidad] > 0){
      $presentacion = " ".$alimento['nombreunidad'.$unidad];
      $aux = $sangria.$alimento['componente'].$presentacion;
      $long_nombre=strlen($aux);
      if($long_nombre > $largoNombre){
        $aux = substr($aux,0,$largoNombre);
      }
      $sheet->setCellValue('A'.$rowNum, $aux);
      $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
      $sheet->setCellValue('F'.$rowNum, $aux);
      $rowNum++;
    }

    $unidad = 4;
    if($alimento['cantu'.$unidad] > 0){
      $presentacion = " ".$alimento['nombreunidad'.$unidad];
      $aux = $sangria.$alimento['componente'].$presentacion;
      $long_nombre=strlen($aux);
      if($long_nombre > $largoNombre){
        $aux = substr($aux,0,$largoNombre);
      }
      $sheet->setCellValue('A'.$rowNum, $aux);
      $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
      $sheet->setCellValue('F'.$rowNum, $aux);
      $rowNum++;
    }

    $unidad = 5;
    if($alimento['cantu'.$unidad] > 0){
      $presentacion = " ".$alimento['nombreunidad'.$unidad];
      $aux = $sangria.$alimento['componente'].$presentacion;
      $long_nombre=strlen($aux);
      if($long_nombre > $largoNombre){
        $aux = substr($aux,0,$largoNombre);
      }
      $sheet->setCellValue('A'.$rowNum, $aux);
      $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
      $sheet->setCellValue('F'.$rowNum, $aux);
      $rowNum++;
    }



      }
  }

$sheet->setCellValue('A'.$rowNum, 'OBSERVACIONES : ');
$sheet->mergeCells('B'.$rowNum.':R'.$rowNum);
$rowNum++;
$sheet->mergeCells('A'.$rowNum.':R'.$rowNum);

$finInfo = $rowNum;

$sheet->getStyle("A".$inicioTitulos.":R".$finTitulos)->applyFromArray($titulos);
$sheet->getStyle("A".$inicioInfo.":R".$finInfo)->applyFromArray($infor);

$sheet->getColumnDimension("A")->setWidth(24); 

// $pdf->Output();

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="ordenes_agrupado.xlsx"');
$writer->save('php://output','ordenes_agrupado.xlsx');

mysqli_close ( $Link );
