<?php
include '../../config.php';
require_once '../../autentication.php';
require_once '../../db/conexion.php';
set_time_limit (0);
ini_set('memory_limit','6000M');
$largoNombre = 30;
$sangria = " - ";
$tamannoFuente = 6;
date_default_timezone_set('America/Bogota');

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


$rowNum = 1;

$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

$mesAnno = '';
if( isset($_POST['despachoAnnoI']) && isset($_POST['despachoMesI']) && isset($_POST['despacho']) ){
  // Se va a recuperar el mes y el año para las tablaMesAnno
  $mes = $_POST['despachoMesI'];
  if($mes < 10){
    $mes = '0'.$mes;
  }
  $mes = trim($mes);
  $anno = $_POST['despachoAnnoI'];
  $anno = substr($anno, -2);
  $anno = trim($anno);
  $mesAnno = $mes.$anno;
  $_POST = array_slice($_POST, 2);
  $_POST = array_values($_POST);
}else{
  // Se va a recuperar el mes y el año para las tablaMesAnno
  $mes = $_POST['mesiConsulta'];
  if($mes < 10){
    $mes = '0'.$mes;
  }
  $mes = trim($mes);
  $anno = $_POST['annoi'];
  $anno = substr($anno, -2);
  $anno = trim($anno);
  $mesAnno = $mes.$anno;

  // var_dump($_POST);
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
  // echo "<br><br>";
  // var_dump($_POST);

}

  include '../../php/funciones.php';

for ($k=0; $k < count($_POST) ; $k++){
  // Borrando variables array para usarlas en cada uno de los despachos
  unset($sedes);
  unset($items);
  unset($menus);
  unset($sedesCobertura);
  unset($complementosCantidades);

  $claves = array_keys($_POST);
  $aux = $claves[$k];
  $despacho = $_POST[$aux];
  $consulta = " SELECT de.*, tc.descripcion, s.nom_sede, s.nom_inst, u.Ciudad, td.Descripcion as tipoDespachoNm, tc.jornada
  FROM despachos_enc$mesAnno de
  left join sedes$anno s on de.cod_sede = s.cod_sede
  left join ubicacion u on s.cod_mun_sede = u.CodigoDANE
  left join tipo_complemento tc on de.Tipo_Complem = tc.CODIGO
  left join tipo_despacho td on de.TipoDespacho = td.Id
  WHERE de.Num_Doc = $despacho ";

  // echo '<br><br>'.$consulta.'<br><br>';

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

  if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();
  }
  $municipio = $row['Ciudad'];
  $institucion = $row['nom_inst'];
  $sede = $row['nom_sede'];
  $codSede = $row['cod_Sede'];
  $semana = $row['Semana'];
  $cobertura = $row['Cobertura'];
  $modalidad = $row['Tipo_Complem'];
  $descripcionTipo = $row['descripcion'];
  $tipoDespachoNm = $row['tipoDespachoNm'];
  $jornada = $row['jornada'];

  $fechaDespacho = $row['FechaHora_Elab'];
  $fechaDespacho = strtotime($fechaDespacho);
  $fechaDespacho = date("d/m/Y",$fechaDespacho);

  $auxDias = $row['Dias'];
  $diasDespacho = $row['Dias'];
  $auxDias = str_replace(",", ", ", $auxDias);

  $auxMenus = $row['Menus'];
  $auxMenus = str_replace(",", ", ", $auxMenus);

  $tipo = $modalidad;
  $sedes[] = $codSede;

 // Iniciando la busqueda de los días que corresponden a esta semana de contrato.
 $arrayDiasDespacho = explode(',', $diasDespacho);
 $dias = '';
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
   }//Termina el while
   $dias .= " de  $mes";
 }
 // Termina la busqueda de los días que corresponden a esta semana de contrato.

  // Bucando la cobertura para la sede en esa semana para el tipo de complementosCantidades
  $cantSedeGrupo1 = 0;
  $cantSedeGrupo2 = 0;
  $cantSedeGrupo3 = 0;

  $consulta = " select Etario1_$modalidad as grupo1, Etario2_$modalidad as grupo2, Etario3_$modalidad as grupo3
  from sedes_cobertura
  where semana = '$semana' and cod_sede  = $codSede ";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();
    $cantSedeGrupo1 = $row['grupo1'];
    $cantSedeGrupo2 = $row['grupo2'];
    $cantSedeGrupo3 = $row['grupo3'];
  }

  //echo "<br>".$cantSedeGrupo1;
  //echo "<br>".$cantSedeGrupo2;
  //echo "<br>".$cantSedeGrupo3;

  // A medida que se recoja la información de los aliemntos se
  // determianra si todos los grupos etarios fueron beneficiados
  // y usaremos las cantidades de las siguientes variables.

  $sedeGrupo1 = 0;
  $sedeGrupo2 = 0;
  $sedeGrupo3 = 0;


  // Se van a buscar los alimentos de este despacho.
  $alimentos = array();
  $consulta = " select distinct cod_alimento
  from despachos_det$mesAnno
  where Tipo_Doc = 'DES'
  and Num_Doc = $despacho
  order by cod_alimento asc ";

  //echo "<br><br>CONSULTA LOS CODIGOS DE ALIMENTOS DE ESTE DESPACHO<br>$consulta<br><br>";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()){
      $alimento = array();
      $alimento['codigo'] = $row['cod_alimento'];
      $alimentos[] = $alimento;
    }
  }


  for ($i=0; $i < count($alimentos) ; $i++) {
    $alimento = $alimentos[$i];
    $auxCodigo = $alimento['codigo'];

    $consulta = " select distinct ftd.codigo, ftd.Componente,
    p.nombreunidad2 presentacion,
    p.cantidadund1 cantidadPresentacion,
    m.grupo_alim, m.orden_grupo_alim, ftd.UnidadMedida, ( select Cantidad

    from despachos_det$mesAnno

    where Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 1 ) as cant_grupo1, ( select Cantidad
    from despachos_det$mesAnno
    where Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 2 ) as cant_grupo2,

    (select Cantidad from despachos_det$mesAnno where Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 3) as cant_grupo3,

    (SELECT cantu2 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu2,
    (SELECT cantu3 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu3,
    (SELECT cantu4 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu4,
    (SELECT cantu5 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu5,
    (SELECT cantotalpresentacion FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantotalpresentacion,

    p.cantidadund2,
    p.cantidadund3,
    p.cantidadund4,
    p.cantidadund5,
    p.nombreunidad2,
    p.nombreunidad3,
    p.nombreunidad4,
    p.nombreunidad5

    from fichatecnicadet ftd inner join productos$anno p on ftd.codigo=p.codigo inner join menu_aportes_calynut m on ftd.codigo=m.cod_prod where ftd.codigo = $auxCodigo and ftd.tipo = 'Alimento'  order by m.orden_grupo_alim ASC, ftd.Componente DESC ";

    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

    $alimento['componente'] = '';
    $alimento['presentacion'] = '';
    $alimento['grupo_alim'] = '';
    $alimento['cant_grupo1'] = 0;
    $alimento['cant_grupo2'] = 0;
    $alimento['cant_grupo3'] = 0;
    $alimento['cant_total'] = 0;




    if($resultado->num_rows >= 1){
      while($row = $resultado->fetch_assoc()){
        $alimento['componente'] = $row['Componente'];
        $alimento['presentacion'] = $row['presentacion'];
        $alimento['cantidadpresentacion'] = $row['cantidadPresentacion'];
        $alimento['grupo_alim'] = $row['grupo_alim'];
        $alimento['orden_grupo_alim'] = $row['orden_grupo_alim'];
        $alimento['cant_grupo1'] = $row['cant_grupo1'];
        $alimento['cant_grupo2'] = $row['cant_grupo2'];
        $alimento['cant_grupo3'] = $row['cant_grupo3'];
        $alimento['cant_total'] = $row['cant_grupo1'] + $row['cant_grupo2'] + $row['cant_grupo3'];

        $alimento['cantu2'] = $row['cantu2'];
        $alimento['cantu3'] = $row['cantu3'];
        $alimento['cantu4'] = $row['cantu4'];
        $alimento['cantu5'] = $row['cantu5'];
        $alimento['cantotalpresentacion'] = $row['cantotalpresentacion'];
        $alimento['cantidadund2'] = $row['cantidadund2'];
        $alimento['cantidadund3'] = $row['cantidadund3'];
        $alimento['cantidadund4'] = $row['cantidadund4'];
        $alimento['cantidadund5'] = $row['cantidadund5'];
        $alimento['nombreunidad2'] = $row['nombreunidad2'];
        $alimento['nombreunidad3'] = $row['nombreunidad3'];
        $alimento['nombreunidad4'] = $row['nombreunidad4'];
        $alimento['nombreunidad5'] = $row['nombreunidad5'];


        if($row['cant_grupo1'] > 0){
          $sedeGrupo1 = $cantSedeGrupo1;
        }
        if($row['cant_grupo2'] > 0){
          $sedeGrupo2 = $cantSedeGrupo2;
        }
        if($row['cant_grupo3'] > 0){
          $sedeGrupo3 = $cantSedeGrupo3;
        }
      }
    }
    $alimentos[$i] = $alimento;
  }

unset($sort);
unset($grupo);
  $sort = array();
  $grupo = array();
  foreach($alimentos as $kOrden=>$vOrden) {
      $sort['componente'][$kOrden] = $vOrden['componente'];
      $sort['grupo_alim'][$kOrden] = $vOrden['orden_grupo_alim']; //Se cambia el orden de acuerdo al orden por grupo de alimento
      $sort['cantidadpresentacion'][$kOrden] = $vOrden['cantidadpresentacion'];
      $grupo[$kOrden] = $vOrden['grupo_alim'];
  }

  // array_multisort($sort['grupo_alim'], SORT_ASC, $sort['componente'], SORT_ASC, $sort['cantidadpresentacion'], SORT_NUMERIC, SORT_ASC, $alimentos);

  //var_dump($alimentos);
  array_multisort($sort['grupo_alim'], SORT_ASC,$alimentos);
  sort($grupo);

// HEADER
$logoInfopae = $_SESSION['p_Logo ETC'];

$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath($logoInfopae);
$drawing->setHeight(70);
$drawing->setCoordinates('A'.$rowNum);
$drawing->setOffsetX(25);
$drawing->setOffsetY(17);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$sheet->setCellValue('H'.$rowNum, 'PROGRAMA DE ALIMENTACIÓN ESCOLAR');
$rowNum++;
$sheet->setCellValue('H'.$rowNum, 'REMISIÓN ENTREGA DE VÍVERES EN INSTITUCIÓN EDUCATIVA');
$rowNum++;
$sheet->setCellValue('H'.$rowNum, $descripcionTipo);
$rowNum++;
$sheet->setCellValue('H'.$rowNum, $tipoDespachoNm);
$rowNum++;

$sheet->setCellValue('A'.$rowNum, 'OPERADOR:');
$sheet->setCellValue('C'.$rowNum, $_SESSION['p_Operador']);
$sheet->setCellValue('M'.$rowNum, 'FECHA DE ELABORACIÓN:');
$sheet->setCellValue('P'.$rowNum, $fechaDespacho);

$rowNum++;

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(117,4.76,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->SetFont('Arial','B',$tamannoFuente);
// $pdf->Cell(8,4.76,utf8_decode('ETC:'),0,0,'L',False);
// $pdf->SetFont('Arial','',$tamannoFuente);
// $pdf->Cell(109,4.76,utf8_decode($_SESSION['p_Nombre ETC']),0,0,'L',False);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(146.4,4.76,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->SetFont('Arial','B',$tamannoFuente);
// $pdf->Cell(33,4.76,utf8_decode('MUNICIPIO O VEREDA:'),0,0,'L',False);
// $pdf->SetFont('Arial','',$tamannoFuente);
// $pdf->Cell(113.4,4.76,utf8_decode($municipio),0,0,'L',False);
// $pdf->Ln(4.76);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(153.1,4.76,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->SetFont('Arial','B',$tamannoFuente);
// $pdf->Cell(54,4.76,utf8_decode('INSTITUCIÓN O CENTRO EDUCATIVO:'),0,0,'L',False);
// $pdf->SetFont('Arial','',$tamannoFuente);

// $institucion = substr( $institucion, 0, 54 );

// $pdf->Cell(99.1,4.76,utf8_decode($institucion),0,0,'L',False);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(110.3,4.76,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->SetFont('Arial','B',$tamannoFuente);
// $pdf->Cell(28,4.76,utf8_decode('SEDE EDUCATIVA:'),0,0,'L',False);
// $pdf->SetFont('Arial','',$tamannoFuente);


// $sede = substr( $sede, 0, 44);

// $pdf->Cell(82.3,4.76,utf8_decode($sede),0,0,'L',False);
// $pdf->Ln(4.76);
// $pdf->Ln(0.8);

// $pdf->SetFont('Arial','B',$tamannoFuente);
// $pdf->Cell(42.5,8,'RANGO DE EDAD',1,0,'C',False);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(36.7,8,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(36.7,4,utf8_decode('N° DE RACIONES'),0,4,'C',False);
// $pdf->Cell(36.7,4,utf8_decode('ADJUDICADAS'),0,0,'C',False);
// $pdf->SetXY($current_x+36.7, $current_y);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(36.7,8,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(36.7,4,utf8_decode('N° DE RACIONES'),0,4,'C',False);
// $pdf->Cell(36.7,4,utf8_decode('ATENDIDAS'),0,0,'C',False);
// $pdf->SetXY($current_x+36.7, $current_y);

// $pdf->Cell(45,8,utf8_decode('N° DE DÍAS A ATENDER'),1,0,'C',False);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(57.8,8,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(57.8,4,utf8_decode('N° DE MENÚ Y SEMANA DEL CICLO DE'),0,4,'C',False);
// $pdf->Cell(57.8,4,utf8_decode('MENÚS ENTREGADO'),0,0,'C',False);
// $pdf->SetXY($current_x+57.8, $current_y);

// $pdf->Cell(44.7,8,utf8_decode('TOTAL RACIONES'),1,0,'C',False);
// $pdf->Ln(8);

// $pdf->SetFont('Arial','',$tamannoFuente);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(42.5,14.1,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $consGrupoEtario = "SELECT * FROM grupo_etario ";
// $resGrupoEtario = $Link->query($consGrupoEtario);
// if ($resGrupoEtario->num_rows > 0) {
//   while ($ge = $resGrupoEtario->fetch_assoc()) {
//     $get[] = $ge['DESCRIPCION'];
//   }
// }

// $pdf->Cell(42.5,4.7,utf8_decode($get[0]),1,4.7,'C',False);
// $pdf->Cell(42.5,4.7,utf8_decode($get[1]),1,4.7,'C',False);
// $pdf->Cell(42.5,4.7,utf8_decode($get[2]),1,0,'C',False);
// $pdf->SetXY($current_x+42.5, $current_y);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(36.7,14.1,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo1),1,4.7,'C',False);
// $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo2),1,4.7,'C',False);
// $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo3),1,0,'C',False);
// $pdf->SetXY($current_x+36.7, $current_y);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(36.7,14.1,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo1),1,4.7,'C',False);
// $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo2),1,4.7,'C',False);
// $pdf->Cell(36.7,4.7,utf8_decode($sedeGrupo3),1,0,'C',False);
// $pdf->SetXY($current_x+36.7, $current_y);

// $pdf->SetFillColor(255,255,255);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(45,14.1,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y+2.35);

// $cantDias = explode(',', $diasDespacho);
// $cantDias = count($cantDias);
// $auxDias = "X ".$cantDias." DIAS ".strtoupper($dias);

// $pdf->MultiCell(45,4.7,$auxDias,0,'C',False);
// $pdf->SetXY($current_x, $current_y+9.4);
// $pdf->MultiCell(45,4.7,'SEMANA: '.$semana,0,'C',False);

// $pdf->SetXY($current_x+45, $current_y);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(57.8,14.1,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y+2.35);
// $pdf->Cell(57.8,4.7,'SEMANA: '.$ciclo,0,0,'C',False);

// $pdf->SetXY($current_x, $current_y+7,05);
// $pdf->Cell(57.8,4.7,'MENUS: '.$auxMenus,0,0,'C',False);
// $pdf->SetFont('Arial','',$tamannoFuente);

// $pdf->SetXY($current_x+57.8, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(44.7,14.1,'',1,0,'L',False);
// $pdf->SetXY($current_x+2, $current_y+2.35);

// $jm = '';
// $jt = '';

// // 2 es la jornada de la mañana
// // 3 es la jornada de la tarde
// if($jornada == 2){
//   $jm = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
// }else if($jornada == 3){
//   $jt = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
// }
// $pdf->Cell(7,4.7,'JM:',0,0,'L',False);
// $pdf->Cell(33,4.7,$jm,'B',0,'L',False);
// $pdf->SetXY($current_x+2, $current_y+7.05);
// $pdf->Cell(7,4.7,'JT:',0,0,'L',False);
// $pdf->Cell(33,4.7,$jt,'B',0,'L',False);

// $pdf->SetXY($current_x, $current_y+14.1);
// $pdf->Ln(0.8);

// $pdf->SetFont('Arial','B',$tamannoFuente);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(23.788,15,'',1,0,'C',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Ln(2.5);
// $pdf->MultiCell(23.755,5,'GRUPO ALIMENTO',0,'C',False);
// $pdf->SetXY($current_x+23.788, $current_y);

// $pdf->Cell(48.972,15,'ALIMENTO',1,0,'C',False);

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(39.33,15,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(39.33,8,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(39.33,4,'CNT DE ALIMENTOS POR',0,4,'C',False);
// $pdf->Cell(39.33,4,utf8_decode('NÚMEROS DE RACIONES'),0,4,'C',False);
// $current_y2 = $pdf->GetY();
// $current_x2 = $pdf->GetX();
// $pdf->Cell(13.1,7,utf8_decode(''),1,0,'C',False);
// $pdf->SetXY($current_x2, $current_y2);

// $etario_1 = str_replace(" + 11 meses", "", $get[0]);
// $etario_2 = str_replace(" + 11 meses", "", $get[1]);
// $etario_3 = str_replace(" + 11 meses", "", $get[2]);

// $etario_1 = str_replace(" años", "", $etario_1);
// $etario_2 = str_replace(" años", "", $etario_2);
// $etario_3 = str_replace(" años", "", $etario_3);

// $pdf->Cell(13.1,3.5,utf8_decode($etario_1),0,3.5,'C',False);
// $pdf->Cell(13.1,3.5,utf8_decode('AÑOS'),0,3.5,'C',False);

// $pdf->SetXY($current_x2+13.1, $current_y2);
// $current_y2 = $pdf->GetY();
// $current_x2 = $pdf->GetX();
// $pdf->Cell(13.1,7,utf8_decode(''),1,0,'C',False);
// $pdf->SetXY($current_x2, $current_y2);
// $pdf->Cell(13.1,3.5,utf8_decode($etario_2),0,3.5,'C',False);
// $pdf->Cell(13.1,3.5,utf8_decode('AÑOS'),0,3.5,'C',False);

// $pdf->SetXY($current_x2+13.1, $current_y2);
// $current_y2 = $pdf->GetY();
// $current_x2 = $pdf->GetX();
// $pdf->Cell(13.1,7,utf8_decode(''),1,0,'C',False);
// $pdf->SetXY($current_x2, $current_y2);
// $pdf->Cell(13.1,3.5,utf8_decode($etario_3),0,3.5,'C',False);
// $pdf->Cell(13.1,3.5,utf8_decode('AÑOS'),0,3.5,'C',False);

// $pdf->SetXY($current_x+39.33, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(13.141,15,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(13.141,5,'UNIDAD',0,5,'C',False);
// $pdf->Cell(13.141,5,'DE',0,5,'C',False);
// $pdf->Cell(13.141,5,'MEDIDA',0,5,'C',False);


// $pdf->SetXY($current_x+13.141, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(13.141,15,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y+2.5);
// $pdf->Cell(13.141,5,'TOTAL',0,5,'C',False);
// $pdf->Cell(13.141,5,'REQ',0,5,'C',False);


// $pdf->SetXY($current_x+13.141, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(31.838,15,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(31.838,4,'CANTIDAD',0,4,'C',False);
// $pdf->Cell(31.838,4,'ENTREGADA','B',4,'C',False);
// $pdf->Cell(10.6,7,'TOTAL','R',0,'C',False);
// $pdf->Cell(10.638,7,'C','R',0,'C',False);
// $pdf->Cell(10.6,7,'NC','R',0,'C',False);

// $pdf->SetXY($current_x+31.838, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(27.252,15,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(27.252,4,utf8_decode('ESPECIFICACIÓN'),0,4,'C',False);
// $pdf->Cell(27.252,4,utf8_decode('DE CALIDAD'),'B',4,'C',False);
// $pdf->Cell(13.626,7,'C','R',0,'C',False);
// $pdf->Cell(13.626,7,'NC','R',0,'C',False);

// $pdf->SetXY($current_x+27.252, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(32.191,15,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(32.191,8,utf8_decode('FALTANTES'),'B',8,'C',False);
// $pdf->Cell(9.349,7,'SI','R',0,'C',False);
// $pdf->Cell(8.819,7,'NO','R',0,'C',False);
// $pdf->Cell(14.023,7,'CANT','R',0,'C',False);

// $pdf->SetXY($current_x+32.191, $current_y);
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();
// $pdf->Cell(33.747,15,'',1,0,'L',False);
// $pdf->SetXY($current_x, $current_y);
// $pdf->Cell(33.747,8,utf8_decode('DEVOLUCIÓN'),'B',8,'C',False);
// $pdf->Cell(9.26,7,'SI','R',0,'C',False);
// $pdf->Cell(9.084,7,'NO','R',0,'C',False);
// $pdf->Cell(15.403,7,'CANT','R',0,'C',False);

// $pdf->SetXY($current_x, $current_y);
// $pdf->Ln(15);
// HEADER

$rowNum++;


  $filas = 0;
  $grupoAlimActual = '';


  for ($i=0; $i < count($alimentos ) ; $i++) {
    $alimento = $alimentos[$i];
      if($alimento['componente'] != ''){


    if(!isset($alimento['D1'])){
      $alimento['D1'] = 0;
    }
    if(!isset($alimento['D2'])){
      $alimento['D2'] = 0;
    }
    if(!isset($alimento['D3'])){
      $alimento['D3'] = 0;
    }
    if(!isset($alimento['D4'])){
      $alimento['D4'] = 0;
    }
    if(!isset($alimento['D5'])){
      $alimento['D5'] = 0;
    }
    if(!isset($alimento['cantu2'])){
      $alimento['cantu2'] = 0;
    }
    if(!isset($alimento['cantu3'])){
      $alimento['cantu3'] = 0;
    }
    if(!isset($alimento['cantu4'])){
      $alimento['cantu4'] = 0;
    }
    if(!isset($alimento['cantu5'])){
      $alimento['cantu5'] = 0;
    }
    if(!isset($alimento['cantotalpresentacion'])){
      $alimento['cantotalpresentacion'] = 0;
    }
    //Grupo Alimenticio
    $largoNombreGrupo = 25;
     $caracteresPorLinea = 10;
    $anchoCelda = 23.788;
    if($alimento['grupo_alim'] != $grupoAlimActual){
        $grupoAlimActual = $alimento['grupo_alim'];

        $filas = array_count_values($grupo)[$grupoAlimActual];
        $cantAlimentosGrupo = $filas;

        // Se va a realizar una busqueda por si hay filas adicionales debido a presentaciones.
        for ($j=$i;$j < $i+$cantAlimentosGrupo; $j++) {
          $aux = $alimentos[$j];
          if($aux['cantu2'] > 0){
            $filas++;
          }
          if($aux['cantu3'] > 0){
            $filas++;
          }
          if($aux['cantu5'] > 0){
            $filas++;
          }
          if($aux['cantu4'] > 0){
            $filas++;
          }
          if($aux['cantu2'] > 0 || $aux['cantu3'] > 0 || $aux['cantu4'] > 0 || $aux['cantu5'] > 0){
            //$filas--;
          }
        }
        //Termina la busqueda de las filas adicionales debido a presetaciones,

        $altura = 4 * $filas;

        $aux = $alimento['grupo_alim'];
        $aux = mb_strtoupper($aux, 'UTF-8');
        $long_nombre=strlen($aux);
        // Altura para cada linea de la celda
        $altura = 4;
        $margenSuperiorCelda = 0;

        if( $long_nombre > ( $caracteresPorLinea * $filas )  ){
          $margenSuperiorCelda = 0;
          $largoMaximo = ($caracteresPorLinea * $filas) - 2;
          $aux = substr($aux,0,$largoMaximo);
        }

        if($filas == 1){
          $margenSuperiorCelda = 0;
        }

        if($filas == 2){
          $margenSuperiorCelda = 0;
        }


        if(  $long_nombre <= 10 && $filas > 2 ){
          $margenSuperiorCelda = (($filas - 1) / 2)*$altura ;
        }

        if( $long_nombre > 10 && $long_nombre <= 20 && $filas > 2 ){
          $margenSuperiorCelda = (($filas - 2) / 2)*$altura ;
        }

         if( $long_nombre > 20 && $long_nombre <= 30 && $filas > 3 ){
          $margenSuperiorCelda = (($filas - 3) / 2)*$altura ;
        }

         if( $long_nombre > 30 && $filas > 4 ){
          $margenSuperiorCelda = (($filas - 4) / 2)*$altura ;
        }

        $sheet->setCellValue('A'.$rowNum, $aux);
        $sheet->mergeCells('A'.$rowNum.':A'.($rowNum+($filas-1)));


    }
    else{
         // $pdf->SetX($current_x+$anchoCelda);
    }
    // Termina la impresión de grupo alimenticio


  // Se verifica que no haya cantidades en las direntes presentaciones, para no mostrar la primera fila.
  //if($alimento['cantu2'] <= 0 && $alimento['cantu3'] <= 0 && $alimento['cantu4'] <= 0 && $alimento['cantu5'] <= 0){

    if(1==1){

    $aux = $alimento['componente'];
    // $long_nombre=strlen($aux);
    // if($long_nombre > $largoNombre){
    //   $aux = substr($aux,0,$largoNombre);
    // }
     //23.788
    // 72.76

      //Alimento
    $sheet->setCellValue('B'.$rowNum, $aux);
    // $pdf->Cell(48.9,4,utf8_decode($aux),1,0,'L',False);

    if($alimento['presentacion'] == 'u'){
      $aux = round($alimento['cant_grupo1']);
    }else{
      $aux = 0+$alimento['cant_grupo1'];
      $aux = number_format($aux, 2, '.', '');
    }
    $sheet->setCellValue('C'.$rowNum, $aux);
    // $pdf->Cell(13.1,4,utf8_decode($aux),1,0,'C',False);


    if($alimento['presentacion'] == 'u'){
      $aux = round($alimento['cant_grupo2']);
    }else{
      $aux = 0+$alimento['cant_grupo2'];
      $aux = number_format($aux, 2, '.', '');
    }
    $sheet->setCellValue('D'.$rowNum, $aux);
    // $pdf->Cell(13.1,4,utf8_decode($aux),1,0,'C',False);

    if($alimento['presentacion'] == 'u'){
      $aux = round($alimento['cant_grupo3']);
    }else{
      $aux = 0+$alimento['cant_grupo3'];
      $aux = number_format($aux, 2, '.', '');
    }
    $sheet->setCellValue('E'.$rowNum, $aux);
    // $pdf->Cell(13.1,4,utf8_decode($aux),1,0,'C',False);

    //UNIDAD DE MEDIDA
    $sheet->setCellValue('F'.$rowNum, $alimento['presentacion']);
    // $pdf->Cell(13.141,4,$alimento['presentacion'],1,0,'C',False);


    $aux = number_format($aux, 2, '.', '');

//MOSTRAR O NO CUANDO HAY PRESENTACIONES

    if($alimento['cantu2'] <= 0 && $alimento['cantu3'] <= 0 && $alimento['cantu4'] <= 0 && $alimento['cantu5'] <= 0){

    }else{
      // $aux = '';
    }

    if ($alimento['presentacion'] == 'u') {
      // $aux = number_format($alimento['cant_total'], 2, '.', '');
      $aux = round($alimento['cant_total']);
    } else {
      $aux = number_format($alimento['cant_total'], 2, '.', '');
    }

    $sheet->setCellValue('G'.$rowNum, $aux);
    // $pdf->Cell(13.141,4,$aux,1,0,'C',False);
    //total requerido


    if($alimento['cantotalpresentacion'] > 0){
      $aux = 0+$alimento['cantotalpresentacion'];
      $aux = number_format($aux, 2, '.', '');
    }

    if($alimento['cantu2'] <= 0 && $alimento['cantu3'] <= 0 && $alimento['cantu4'] <= 0 && $alimento['cantu5'] <= 0){}
    else{
      //$aux = '';
    }


if(strpos($alimento['componente'], "huevo")){
  $aux = ceil($alimento['cant_total']);
}else{

  if ($alimento['presentacion'] == 'u') {
    $aux = round(0+$alimento['cant_total']);
  } else {
    $aux = number_format($alimento['cant_total'], 2, '.', '');
    // $aux = number_format($aux, 0, '.', '');
  }
}

    // CANTIDAD ENTREGADA
    $sheet->setCellValue('H'.$rowNum, $aux);
    // $pdf->Cell(10.7,4,$aux,1,0,'C',False);
      //total entregado
    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // ESPECIFICACIÓN DE CALIDAD
    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');
    // $pdf->Cell(13.6,4,'',1,0,'C',False);
    // $pdf->Cell(13.7,4,'',1,0,'C',False);
    // FALTANTES

    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');
    // $pdf->Cell(9.3,4,'',1,0,'C',False);
    // $pdf->Cell(8.9,4,'',1,0,'C',False);
    // $pdf->Cell(14,4,'',1,0,'C',False);
    //DEVOLUCIÓN
    $sheet->setCellValue('P'.$rowNum, '');
    $sheet->setCellValue('Q'.$rowNum, '');
    $sheet->setCellValue('R'.$rowNum, '');
    // $pdf->Cell(9.3,4,'',1,0,'C',False);
    // $pdf->Cell(9.1,4,'',1,0,'C',False);
    // $pdf->Cell(15.4,4,'',1,0,'C',False);

    // $pdf->Ln(4);
    $rowNum++;

    }//Termina el if que validad si hay cantidades en las unidades con el fin de ocultar la fila inicial.

  $unidad = 2;
  if($alimento['cantu'.$unidad] > 0){
    $presentacion = " ".$alimento['nombreunidad'.$unidad];

    $aux = $sangria.$alimento['componente'].$presentacion;

    $long_nombre=strlen($aux);
    if($long_nombre > $largoNombre){
      $aux = substr($aux,0,$largoNombre);
    }
    $sheet->setCellValue('B'.$rowNum, $aux);
    $sheet->setCellValue('C'.$rowNum, '');
    $sheet->setCellValue('D'.$rowNum, '');
    $sheet->setCellValue('E'.$rowNum, '');
    $sheet->setCellValue('F'.$rowNum, '');
    $sheet->setCellValue('G'.$rowNum, '');

    // $pdf->Cell(48.9,4,utf8_decode($aux),1,0,'L',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.141,4,'',1,0,'C',False);
    // $pdf->Cell(13.141,4,'',1,0,'C',False);
    $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
    // CANTIDAD ENTREGADA

    $sheet->setCellValue('H'.$rowNum, $aux);
    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');
    // $pdf->Cell(10.7,4,$aux,1,0,'C',False);
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // ESPECIFICACIÓN DE CALIDAD
    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');
    // $pdf->Cell(13.6,4,'',1,0,'C',False);
    // $pdf->Cell(13.7,4,'',1,0,'C',False);
    // FALTANTES

    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');
    // $pdf->Cell(9.3,4,'',1,0,'C',False);
    // $pdf->Cell(8.9,4,'',1,0,'C',False);
    // $pdf->Cell(14,4,'',1,0,'C',False);
    //DEVOLUCIÓN

    $sheet->setCellValue('P'.$rowNum, '');
    $sheet->setCellValue('Q'.$rowNum, '');
    $sheet->setCellValue('R'.$rowNum, '');

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
    $sheet->setCellValue('B'.$rowNum, $aux);
    $sheet->setCellValue('C'.$rowNum, '');
    $sheet->setCellValue('D'.$rowNum, '');
    $sheet->setCellValue('E'.$rowNum, '');
    $sheet->setCellValue('F'.$rowNum, '');
    $sheet->setCellValue('G'.$rowNum, '');

    // $pdf->Cell(48.9,4,utf8_decode($aux),1,0,'L',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.141,4,'',1,0,'C',False);
    // $pdf->Cell(13.141,4,'',1,0,'C',False);
    $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
    // CANTIDAD ENTREGADA

    $sheet->setCellValue('H'.$rowNum, $aux);
    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');
    // $pdf->Cell(10.7,4,$aux,1,0,'C',False);
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // ESPECIFICACIÓN DE CALIDAD
    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');
    // $pdf->Cell(13.6,4,'',1,0,'C',False);
    // $pdf->Cell(13.7,4,'',1,0,'C',False);
    // FALTANTES

    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');
    // $pdf->Cell(9.3,4,'',1,0,'C',False);
    // $pdf->Cell(8.9,4,'',1,0,'C',False);
    // $pdf->Cell(14,4,'',1,0,'C',False);
    //DEVOLUCIÓN

    $sheet->setCellValue('P'.$rowNum, '');
    $sheet->setCellValue('Q'.$rowNum, '');
    $sheet->setCellValue('R'.$rowNum, '');

    $rowNum++;
  }

  $unidad = 4;
  if($alimento['cantu'.$unidad] > 0){
    $pdf->SetX($current_x+$anchoCelda);

    $presentacion = " ".$alimento['nombreunidad'.$unidad];

    $pdf->SetTextColor(0,0,0);
    $pdf->SetFillColor(255,255,255);

    $aux = $sangria.$alimento['componente'].$presentacion;
    $long_nombre=strlen($aux);
    if($long_nombre > $largoNombre){
      $aux = substr($aux,0,$largoNombre);
    }
    $sheet->setCellValue('B'.$rowNum, $aux);
    $sheet->setCellValue('C'.$rowNum, '');
    $sheet->setCellValue('D'.$rowNum, '');
    $sheet->setCellValue('E'.$rowNum, '');
    $sheet->setCellValue('F'.$rowNum, '');
    $sheet->setCellValue('G'.$rowNum, '');

    // $pdf->Cell(48.9,4,utf8_decode($aux),1,0,'L',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.141,4,'',1,0,'C',False);
    // $pdf->Cell(13.141,4,'',1,0,'C',False);
    $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
    // CANTIDAD ENTREGADA

    $sheet->setCellValue('H'.$rowNum, $aux);
    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');
    // $pdf->Cell(10.7,4,$aux,1,0,'C',False);
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // ESPECIFICACIÓN DE CALIDAD
    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');
    // $pdf->Cell(13.6,4,'',1,0,'C',False);
    // $pdf->Cell(13.7,4,'',1,0,'C',False);
    // FALTANTES

    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');
    // $pdf->Cell(9.3,4,'',1,0,'C',False);
    // $pdf->Cell(8.9,4,'',1,0,'C',False);
    // $pdf->Cell(14,4,'',1,0,'C',False);
    //DEVOLUCIÓN

    $sheet->setCellValue('P'.$rowNum, '');
    $sheet->setCellValue('Q'.$rowNum, '');
    $sheet->setCellValue('R'.$rowNum, '');

    $rowNum++;
  }

  $unidad = 5;
  if($alimento['cantu'.$unidad] > 0){
    $pdf->SetX($current_x+$anchoCelda);


    $presentacion = " ".$alimento['nombreunidad'.$unidad];


    $pdf->SetTextColor(0,0,0);
    $pdf->SetFillColor(255,255,255);

    $aux = $sangria.$alimento['componente'].$presentacion;
    $long_nombre=strlen($aux);
    if($long_nombre > $largoNombre){
      $aux = substr($aux,0,$largoNombre);
    }
    $sheet->setCellValue('B'.$rowNum, $aux);
    $sheet->setCellValue('C'.$rowNum, '');
    $sheet->setCellValue('D'.$rowNum, '');
    $sheet->setCellValue('E'.$rowNum, '');
    $sheet->setCellValue('F'.$rowNum, '');
    $sheet->setCellValue('G'.$rowNum, '');

    // $pdf->Cell(48.9,4,utf8_decode($aux),1,0,'L',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.1,4,'',1,0,'C',False);
    // $pdf->Cell(13.141,4,'',1,0,'C',False);
    // $pdf->Cell(13.141,4,'',1,0,'C',False);
    $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
    // CANTIDAD ENTREGADA

    $sheet->setCellValue('H'.$rowNum, $aux);
    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');
    // $pdf->Cell(10.7,4,$aux,1,0,'C',False);
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // $pdf->Cell(10.6,4,'',1,0,'C',False);
    // ESPECIFICACIÓN DE CALIDAD
    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');
    // $pdf->Cell(13.6,4,'',1,0,'C',False);
    // $pdf->Cell(13.7,4,'',1,0,'C',False);
    // FALTANTES

    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');
    // $pdf->Cell(9.3,4,'',1,0,'C',False);
    // $pdf->Cell(8.9,4,'',1,0,'C',False);
    // $pdf->Cell(14,4,'',1,0,'C',False);
    //DEVOLUCIÓN

    $sheet->setCellValue('P'.$rowNum, '');
    $sheet->setCellValue('Q'.$rowNum, '');
    $sheet->setCellValue('R'.$rowNum, '');

    $rowNum++;
  }
}


}//Termina el for de los alimentos
}

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="ordenes_por_sede.xlsx"');
$writer->save('php://output','ordenes_por_sede.xlsx');

mysqli_close ( $Link );
