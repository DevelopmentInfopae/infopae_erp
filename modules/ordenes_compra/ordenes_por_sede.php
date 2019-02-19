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

$sheet->mergeCells('A'.$rowNum.':G'.($rowNum+3));

$sheet->setCellValue('H'.$rowNum, 'PROGRAMA DE ALIMENTACIÓN ESCOLAR');
$sheet->mergeCells('H'.$rowNum.':R'.$rowNum);
$rowNum++;
$sheet->setCellValue('H'.$rowNum, 'REMISIÓN ENTREGA DE VÍVERES EN INSTITUCIÓN EDUCATIVA');
$sheet->mergeCells('H'.$rowNum.':R'.$rowNum);
$rowNum++;
$sheet->setCellValue('H'.$rowNum, $descripcionTipo);
$sheet->mergeCells('H'.$rowNum.':R'.$rowNum);
$rowNum++;
$sheet->setCellValue('H'.$rowNum, $tipoDespachoNm);
$sheet->mergeCells('H'.$rowNum.':R'.$rowNum);
$rowNum++;

$sheet->setCellValue('A'.$rowNum, 'OPERADOR:');
$sheet->mergeCells('A'.$rowNum.':B'.$rowNum);
$sheet->setCellValue('C'.$rowNum, $_SESSION['p_Operador']);
$sheet->mergeCells('C'.$rowNum.':L'.$rowNum);
$sheet->setCellValue('M'.$rowNum, 'FECHA DE ELABORACIÓN:');
$sheet->mergeCells('M'.$rowNum.':O'.$rowNum);
$sheet->setCellValue('P'.$rowNum, $fechaDespacho);
$sheet->mergeCells('P'.$rowNum.':R'.$rowNum);

$rowNum++;

$sheet->setCellValue('A'.$rowNum, 'ETC:');
$sheet->mergeCells('A'.$rowNum.':D'.$rowNum);
$sheet->setCellValue('E'.$rowNum, $_SESSION['p_Nombre ETC']);
$sheet->mergeCells('E'.$rowNum.':I'.$rowNum);

$sheet->setCellValue('J'.$rowNum, 'MUNICIPIO O VEREDA:');
$sheet->mergeCells('K'.$rowNum.':M'.$rowNum);
$sheet->setCellValue('N'.$rowNum, $municipio);
$sheet->mergeCells('N'.$rowNum.':R'.$rowNum);

$rowNum++;

$institucion = substr( $institucion, 0, 54 );
$sede = substr( $sede, 0, 44);

$sheet->setCellValue('A'.$rowNum, 'INSTITUCIÓN O CENTRO EDUCATIVO:');
$sheet->mergeCells('A'.$rowNum.':D'.$rowNum);
$sheet->setCellValue('E'.$rowNum, $institucion);
$sheet->mergeCells('E'.$rowNum.':I'.$rowNum);

$sheet->setCellValue('J'.$rowNum, 'SEDE EDUCATIVA:');
$sheet->mergeCells('K'.$rowNum.':M'.$rowNum);
$sheet->setCellValue('N'.$rowNum, $sede);
$sheet->mergeCells('N'.$rowNum.':R'.$rowNum);

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

$consGrupoEtario = "SELECT * FROM grupo_etario ";
$resGrupoEtario = $Link->query($consGrupoEtario);
if ($resGrupoEtario->num_rows > 0) {
  while ($ge = $resGrupoEtario->fetch_assoc()) {
    $get[] = $ge['DESCRIPCION'];
  }
}

$sheet->setCellValue('A'.$rowNum, $get[0]);
$sheet->mergeCells('A'.$rowNum.':C'.$rowNum);
$sheet->setCellValue('A'.($rowNum+1), $get[1]);
$sheet->mergeCells('A'.($rowNum+1).':C'.($rowNum+1));
$sheet->setCellValue('A'.($rowNum+2), $get[2]);
$sheet->mergeCells('A'.($rowNum+2).':C'.($rowNum+2));

$sheet->setCellValue('D'.$rowNum, $sedeGrupo1);
$sheet->mergeCells('D'.$rowNum.':F'.$rowNum);
$sheet->setCellValue('D'.($rowNum+1), $sedeGrupo2);
$sheet->mergeCells('D'.($rowNum+1).':F'.($rowNum+1));
$sheet->setCellValue('D'.($rowNum+2), $sedeGrupo3);
$sheet->mergeCells('D'.($rowNum+2).':F'.($rowNum+2));

$sheet->setCellValue('G'.$rowNum, $sedeGrupo1);
$sheet->mergeCells('G'.$rowNum.':I'.$rowNum);
$sheet->setCellValue('G'.($rowNum+1), $sedeGrupo2);
$sheet->mergeCells('G'.($rowNum+1).':I'.($rowNum+1));
$sheet->setCellValue('G'.($rowNum+2), $sedeGrupo3);
$sheet->mergeCells('G'.($rowNum+2).':I'.($rowNum+2));

$cantDias = explode(',', $diasDespacho);
$cantDias = count($cantDias);
$auxDias = "X ".$cantDias." DIAS ".strtoupper($dias);

$sheet->setCellValue('J'.$rowNum, $auxDias);
$sheet->mergeCells('J'.$rowNum.':L'.$rowNum);
$sheet->setCellValue('J'.($rowNum+1), 'SEMANA: '.$semana);
$sheet->mergeCells('J'.($rowNum+1).':L'.($rowNum+1));

$sheet->setCellValue('M'.$rowNum, 'SEMANA: '.$ciclo);
$sheet->mergeCells('M'.$rowNum.':O'.$rowNum);
$sheet->setCellValue('M'.($rowNum+1), 'MENUS: '.$auxMenus);
$sheet->mergeCells('M'.($rowNum+1).':O'.($rowNum+1));

$jm = '';
$jt = '';

// 2 es la jornada de la mañana
// 3 es la jornada de la tarde
if($jornada == 2){
  $jm = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
}else if($jornada == 3){
  $jt = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
}

$sheet->setCellValue('P'.$rowNum, 'JM:'.$jm);
$sheet->mergeCells('P'.$rowNum.':R'.$rowNum);
$sheet->setCellValue('P'.($rowNum+1), 'JT:'.$jt);
$sheet->mergeCells('P'.($rowNum+1).':R'.($rowNum+1));

$rowNum+=3;

$sheet->setCellValue('A'.$rowNum, 'GRUPO ALIMENTO');
$sheet->mergeCells('A'.$rowNum.':A'.($rowNum+1));
$sheet->setCellValue('B'.$rowNum, 'ALIMENTO');
$sheet->mergeCells('B'.$rowNum.':B'.($rowNum+1));

$sheet->setCellValue('C'.$rowNum, 'CNT DE ALIMENTOS POR NÚMEROS DE RACIONES');
$sheet->mergeCells('C'.$rowNum.':E'.$rowNum);
$sheet->setCellValue('C'.($rowNum+1), $get[0].'');
$sheet->setCellValue('D'.($rowNum+1), $get[1].'');
$sheet->setCellValue('E'.($rowNum+1), $get[2].'');

$sheet->setCellValue('F'.$rowNum, 'UNIDAD DE MEDIDA');
$sheet->mergeCells('F'.$rowNum.':F'.($rowNum+1));
$sheet->setCellValue('G'.$rowNum, 'TOTAL REQ');
$sheet->mergeCells('G'.$rowNum.':G'.($rowNum+1));

$sheet->setCellValue('H'.$rowNum, 'CANTIDAD ENTREGADA');
$sheet->mergeCells('H'.$rowNum.':J'.$rowNum);
$sheet->setCellValue('H'.($rowNum+1), 'TOTAL');
$sheet->setCellValue('I'.($rowNum+1), 'C');
$sheet->setCellValue('J'.($rowNum+1), 'NC');

$sheet->setCellValue('K'.$rowNum, 'ESPECIFICACIÓN DE CALIDAD');
$sheet->mergeCells('K'.$rowNum.':L'.$rowNum);
$sheet->setCellValue('K'.($rowNum+1), 'C');
$sheet->setCellValue('L'.($rowNum+1), 'NC');

$sheet->setCellValue('M'.$rowNum, 'FALTANTES');
$sheet->mergeCells('M'.$rowNum.':O'.$rowNum);
$sheet->setCellValue('M'.($rowNum+1), 'SI');
$sheet->setCellValue('N'.($rowNum+1), 'NO');
$sheet->setCellValue('O'.($rowNum+1), 'CANT');

$sheet->setCellValue('P'.$rowNum, 'DEVOLUCIÓN');
$sheet->mergeCells('P'.$rowNum.':R'.$rowNum);
$sheet->setCellValue('P'.($rowNum+1), 'SI');
$sheet->setCellValue('Q'.($rowNum+1), 'NO');
$sheet->setCellValue('R'.($rowNum+1), 'CANT');

$rowNum+=2;
$finTitulos = $rowNum-1;
// HEADER
$inicioInfo = $rowNum;

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

    if(1==1){

    $aux = $alimento['componente'];

    $sheet->setCellValue('B'.$rowNum, $aux);

    if($alimento['presentacion'] == 'u'){
      $aux = round($alimento['cant_grupo1']);
    }else{
      $aux = 0+$alimento['cant_grupo1'];
      $aux = number_format($aux, 2, '.', '');
    }
    $sheet->setCellValue('C'.$rowNum, $aux);


    if($alimento['presentacion'] == 'u'){
      $aux = round($alimento['cant_grupo2']);
    }else{
      $aux = 0+$alimento['cant_grupo2'];
      $aux = number_format($aux, 2, '.', '');
    }
    $sheet->setCellValue('D'.$rowNum, $aux);

    if($alimento['presentacion'] == 'u'){
      $aux = round($alimento['cant_grupo3']);
    }else{
      $aux = 0+$alimento['cant_grupo3'];
      $aux = number_format($aux, 2, '.', '');
    }
    $sheet->setCellValue('E'.$rowNum, $aux);

    $sheet->setCellValue('F'.$rowNum, $alimento['presentacion']);

    $aux = number_format($aux, 2, '.', '');

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

    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');

    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');

    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');

    $sheet->setCellValue('P'.$rowNum, '');
    $sheet->setCellValue('Q'.$rowNum, '');
    $sheet->setCellValue('R'.$rowNum, '');

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

    $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');

    $sheet->setCellValue('H'.$rowNum, $aux);
    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');

    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');


    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');


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

    $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');

    $sheet->setCellValue('H'.$rowNum, $aux);
    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');

    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');

    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');

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

    $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');

    $sheet->setCellValue('H'.$rowNum, $aux);
    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');

    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');

    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');

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

    $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');

    $sheet->setCellValue('H'.$rowNum, $aux);
    $sheet->setCellValue('I'.$rowNum, '');
    $sheet->setCellValue('J'.$rowNum, '');
    $sheet->setCellValue('K'.$rowNum, '');
    $sheet->setCellValue('L'.$rowNum, '');

    $sheet->setCellValue('M'.$rowNum, '');
    $sheet->setCellValue('N'.$rowNum, '');
    $sheet->setCellValue('O'.$rowNum, '');

    $sheet->setCellValue('P'.$rowNum, '');
    $sheet->setCellValue('Q'.$rowNum, '');
    $sheet->setCellValue('R'.$rowNum, '');

    $rowNum++;
  }
}


}//Termina el for de los alimentos
$sheet->setCellValue('A'.$rowNum, 'OBSERVACIONES:');
$sheet->mergeCells('A'.$rowNum.':B'.$rowNum);
$sheet->mergeCells('C'.$rowNum.':R'.$rowNum);
$sheet->mergeCells('A'.($rowNum+1).':R'.($rowNum+1));

$rowNum += 5;
$finInfo = $rowNum - 4;

  $sheet->getStyle("A".$inicioTitulos.":R".$finTitulos)->applyFromArray($titulos);
  $sheet->getStyle("A".$inicioInfo.":R".$finInfo)->applyFromArray($infor);
}

$sheet->getColumnDimension("A")->setWidth(16); 

$sheet->getColumnDimension("B")->setWidth(24); 

$sheet->getColumnDimension("C")->setWidth(12); 
$sheet->getColumnDimension("D")->setWidth(12); 
$sheet->getColumnDimension("E")->setWidth(12); 

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="ordenes_por_sede.xlsx"');
$writer->save('php://output','ordenes_por_sede.xlsx');

mysqli_close ( $Link );
