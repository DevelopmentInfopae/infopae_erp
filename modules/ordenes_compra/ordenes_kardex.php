<?php
include '../../config.php';
require_once '../../autentication.php';
require_once '../../db/conexion.php';
include '../../php/funciones.php';

set_time_limit (0);
ini_set('memory_limit','6000M');
$largoNombre = 28;

$sangria = " - ";
$tamannoFuente = 7;
$digitosDecimales = 2;
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
}

for ($kDespachos=0; $kDespachos < count($_POST) ; $kDespachos++) {
  // Borrando variables array para usarlas en cada uno de los despachos
  unset($sedes);
  unset($items);
  unset($menus);
  unset($sedesCobertura);
  unset($complementosCantidades);

  $claves = array_keys($_POST);
  $aux = $claves[$kDespachos];
  $despacho = $_POST[$aux];
  $consulta = " SELECT de.*, tc.descripcion, s.nom_sede, s.nom_inst, u.Ciudad, td.Descripcion as tipoDespachoNm, tc.jornada
  FROM despachos_enc$mesAnno de
  left join sedes$anno s on de.cod_sede = s.cod_sede
  left join ubicacion u on s.cod_mun_sede = u.CodigoDANE
  left join tipo_complemento tc on de.Tipo_Complem = tc.CODIGO
  left join tipo_despacho td on de.TipoDespacho = td.Id
  WHERE de.Num_Doc = $despacho ";

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

    }
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

//CONSULTA LOS CODIGOS DE ALIMENTOS DE ESTE DESPACHO

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


    ( SELECT sum(D1) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D1,
    ( SELECT sum(D2) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D2,
    ( SELECT sum(D3) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D3,
    ( SELECT sum(D4) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D4,
    ( SELECT sum(D5) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D5,

    p.cantidadund2,
    p.cantidadund3,
    p.cantidadund4,
    p.cantidadund5,
    p.nombreunidad2,
    p.nombreunidad3,
    p.nombreunidad4,
    p.nombreunidad5

    from fichatecnicadet ftd inner join productos$anno p on ftd.codigo=p.codigo inner join menu_aportes_calynut m on ftd.codigo=m.cod_prod where ftd.codigo = $auxCodigo and ftd.tipo = 'Alimento' order by orden_grupo_alim ASC, ftd.Componente DESC ";


    // CONSULTA DETALLES DE ALIMENTOS DE ESTE DESPACHO
    //echo "<br>$consulta<br>";


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
        // $alimento['cantotalpresentacion'] = round($row['cantu2']) + round($row['cantu3']) + round($row['cantu4']) + round($row['cantu5']);
        $alimento['nombreunidad2'] = $row['nombreunidad2'];
        $alimento['nombreunidad3'] = $row['nombreunidad3'];
        $alimento['nombreunidad4'] = $row['nombreunidad4'];
        $alimento['nombreunidad5'] = $row['nombreunidad5'];

        $alimento['D1'] = $row['D1'];
        $alimento['D2'] = $row['D2'];
        $alimento['D3'] = $row['D3'];
        $alimento['D4'] = $row['D4'];
        $alimento['D5'] = $row['D5'];



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
  $sort = array();
  foreach($alimentos as $k=>$v) {
      $sort['componente'][$k] = $v['componente'];
      $sort['grupo_alim'][$k] = $v['orden_grupo_alim']; //Se cambia el orden de acuerdo al orden por grupo de alimento
      $grupo[$k] = $v['grupo_alim'];
  }
  // array_multisort($sort['grupo_alim'], SORT_ASC, $sort['componente'], SORT_ASC,$alimentos);
  array_multisort($sort['grupo_alim'], SORT_ASC,$alimentos);
  sort($grupo);
//HEADER

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
$sheet->mergeCells('H'.$rowNum.':P'.$rowNum);
$rowNum++;
$sheet->setCellValue('H'.$rowNum, 'KARDEX DE VÍVERES EN INSTITUCIÓN EDUCATIVA');
$sheet->mergeCells('H'.$rowNum.':P'.$rowNum);
$rowNum++;
$sheet->setCellValue('H'.$rowNum, $descripcionTipo);
$sheet->mergeCells('H'.$rowNum.':P'.$rowNum);
$rowNum++;
$sheet->setCellValue('H'.$rowNum, $tipoDespachoNm);
$sheet->mergeCells('H'.$rowNum.':P'.$rowNum);
$rowNum++;

$sheet->setCellValue('A'.$rowNum, 'OPERADOR:');
$sheet->mergeCells('A'.$rowNum.':B'.$rowNum);
$sheet->setCellValue('C'.$rowNum, $_SESSION['p_Operador']);
$sheet->mergeCells('C'.$rowNum.':P'.$rowNum);

$rowNum++;

$sheet->setCellValue('A'.$rowNum, 'ETC:');
$sheet->mergeCells('A'.$rowNum.':B'.$rowNum);
$sheet->setCellValue('C'.$rowNum, $_SESSION['p_Nombre ETC']);
$sheet->mergeCells('C'.$rowNum.':G'.$rowNum);

$sheet->setCellValue('H'.$rowNum, 'MUNICIPIO O VEREDA:');
$sheet->mergeCells('H'.$rowNum.':J'.$rowNum);
$sheet->setCellValue('K'.$rowNum, $municipio);
$sheet->mergeCells('K'.$rowNum.':P'.$rowNum);

$rowNum++;

$sheet->setCellValue('A'.$rowNum, 'INSTITUCIÓN O CENTRO EDUCATIVO:');
$sheet->mergeCells('A'.$rowNum.':C'.$rowNum);
$institucion = substr( $institucion, 0, 54 );
$sheet->setCellValue('D'.$rowNum, $institucion);
$sheet->mergeCells('D'.$rowNum.':G'.$rowNum);

$sheet->setCellValue('H'.$rowNum, 'SEDE EDUCATIVA:');
$sheet->mergeCells('H'.$rowNum.':J'.$rowNum);
$sede = substr( $sede, 0, 54 );
$sheet->setCellValue('K'.$rowNum, $sede);
$sheet->mergeCells('K'.$rowNum.':P'.$rowNum);

$rowNum++;

$sheet->setCellValue('A'.$rowNum, 'RANGO DE EDAD');
$sheet->mergeCells('A'.$rowNum.':C'.$rowNum);
$sheet->setCellValue('D'.$rowNum, 'N° DE RACIONES ADJUDICADAS');
$sheet->mergeCells('D'.$rowNum.':E'.$rowNum);
$sheet->setCellValue('F'.$rowNum, 'N° DE RACIONES ATENDIDAS');
$sheet->mergeCells('F'.$rowNum.':G'.$rowNum);
$sheet->setCellValue('H'.$rowNum, 'N° DE DÍAS A ATENDER');
$sheet->mergeCells('H'.$rowNum.':J'.$rowNum);
$sheet->setCellValue('K'.$rowNum, 'N° DE MENÚ Y SEMANA DEL CICLO DE MENÚS ENTREGADO');
$sheet->mergeCells('K'.$rowNum.':L'.$rowNum);
$sheet->setCellValue('M'.$rowNum, 'TOTAL RACIONES');
$sheet->mergeCells('M'.$rowNum.':P'.$rowNum);

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
$sheet->mergeCells('D'.$rowNum.':E'.$rowNum);
$sheet->setCellValue('D'.($rowNum+1), $sedeGrupo2);
$sheet->mergeCells('D'.($rowNum+1).':E'.($rowNum+1));
$sheet->setCellValue('D'.($rowNum+2), $sedeGrupo3);
$sheet->mergeCells('D'.($rowNum+2).':E'.($rowNum+2));

$sheet->setCellValue('F'.$rowNum, $sedeGrupo1);
$sheet->mergeCells('F'.$rowNum.':G'.$rowNum);
$sheet->setCellValue('F'.($rowNum+1), $sedeGrupo2);
$sheet->mergeCells('F'.($rowNum+1).':G'.($rowNum+1));
$sheet->setCellValue('F'.($rowNum+2), $sedeGrupo3);
$sheet->mergeCells('F'.($rowNum+2).':G'.($rowNum+2));

$auxDias = "X ";
$cantDias = explode(',', $dias);
$cantDias = count($cantDias);
$auxDias = "X ".$cantDias." DIAS ".strtoupper($dias);

$sheet->setCellValue('H'.$rowNum, $auxDias);
$sheet->mergeCells('H'.$rowNum.':J'.$rowNum);


$sheet->setCellValue('K'.$rowNum, "SEMANA : ".$ciclo);
$sheet->mergeCells('K'.$rowNum.':L'.$rowNum);
$sheet->setCellValue('K'.($rowNum+1), 'MENUS: '.$auxMenus);
$sheet->mergeCells('K'.($rowNum+1).':L'.($rowNum+1));

$jm = 0;
$jt = 0;

// 2 es la jornada de la mañana
// 3 es la jornada de la tarde
if($jornada == 2){
  $jm = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
}else if($jornada == 3){
  $jt = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3;
}

if($modalidad == 'APS'){
  $sheet->setCellValue('M'.$rowNum, "".$jt);
  $sheet->mergeCells('M'.$rowNum.':P'.$rowNum);
}else{
  $sheet->setCellValue('M'.$rowNum, "JM: ".$jm);
  $sheet->mergeCells('M'.$rowNum.':P'.$rowNum);
  $sheet->setCellValue('M'.($rowNum+1), "JT: ".$jt);
  $sheet->mergeCells('M'.($rowNum+1).':P'.($rowNum+1));
}

$rowNum+=3;

$sheet->setCellValue('A'.$rowNum, "GRUPO ALIMENTO");
$sheet->mergeCells('A'.$rowNum.':A'.($rowNum+1));
$sheet->setCellValue('B'.$rowNum, "ALIMENTO");
$sheet->mergeCells('B'.$rowNum.':B'.($rowNum+1));
$sheet->setCellValue('C'.$rowNum, "UNIDAD DE MEDIDA");
$sheet->mergeCells('C'.$rowNum.':C'.($rowNum+1));
$sheet->setCellValue('D'.$rowNum, "CANT REQUERIDA");
$sheet->mergeCells('D'.$rowNum.':D'.($rowNum+1));
$sheet->setCellValue('E'.$rowNum, "EXISTENCIAS");
$sheet->mergeCells('E'.$rowNum.':E'.($rowNum+1));

$sheet->setCellValue('F'.$rowNum, "LUNES");
$sheet->mergeCells('F'.$rowNum.':G'.$rowNum);
$sheet->setCellValue('F'.($rowNum+1), "CANT");
$sheet->setCellValue('G'.($rowNum+1), "SALIDA");

$sheet->setCellValue('H'.$rowNum, "MARTES");
$sheet->mergeCells('H'.$rowNum.':I'.$rowNum);
$sheet->setCellValue('H'.($rowNum+1), "CANT");
$sheet->setCellValue('I'.($rowNum+1), "SALIDA");

$sheet->setCellValue('J'.$rowNum, "MIÉRCOLES");
$sheet->mergeCells('J'.$rowNum.':K'.$rowNum);
$sheet->setCellValue('J'.($rowNum+1), "CANT");
$sheet->setCellValue('K'.($rowNum+1), "SALIDA");

$sheet->setCellValue('L'.$rowNum, "JUEVES");
$sheet->mergeCells('L'.$rowNum.':M'.$rowNum);
$sheet->setCellValue('L'.($rowNum+1), "CANT");
$sheet->setCellValue('M'.($rowNum+1), "SALIDA");

$sheet->setCellValue('N'.$rowNum, "VIERNES");
$sheet->mergeCells('N'.$rowNum.':O'.$rowNum);
$sheet->setCellValue('N'.($rowNum+1), "CANT");
$sheet->setCellValue('O'.($rowNum+1), "SALIDA");

$sheet->setCellValue('P'.$rowNum, "SALDO");
$sheet->mergeCells('P'.$rowNum.':P'.($rowNum+1));

$rowNum+=2;

$finTitulos = $rowNum-1;

$inicioInfo = $rowNum;

//HEADER

  $grupoAlimActual = '';

  for ($i=0; $i < count($alimentos ) ; $i++) {
    $alimento = $alimentos[$i];
    // Se toma la distancia en y para determinar si se hace un salto de pagina.
    //Grupo Alimenticio
    $largoNombreGrupo = 25;
    if($alimento['grupo_alim'] != $grupoAlimActual){
        $grupoAlimActual = $alimento['grupo_alim'];
        $filas = array_count_values($grupo)[$grupoAlimActual];

        // Codigo para validar que todas las filas del grupo caben en la hoja
        $alturaGrupo = 4 * $filas;
        // Termina codigo para validar que todas las filas del grupo caben en la hoja

        $altura = 4 * $filas;

        $aux = $alimento['grupo_alim'];
        $aux = mb_strtoupper($aux, 'UTF-8');
        $long_nombre=strlen($aux);
        if($long_nombre > $largoNombreGrupo){
            $margenSuperiorCelda = ((4 * $filas)/2)-4;
            $altura = 4;
            //$aux = substr($aux,0,$largoNombreGrupo);
        } else{
            $margenSuperiorCelda = 0;
        }

        $sheet->setCellValue('A'.$rowNum, $aux);
        $sheet->mergeCells('A'.$rowNum.':A'.($rowNum+($filas-1)));
    } else{
         // $pdf->SetX(52.388);
    }

    $aux = $alimento['componente'];
    $long_nombre=strlen($aux);
    if($long_nombre > $largoNombre){
      $aux = substr($aux,0,$largoNombre);
    }
    $sheet->setCellValue('B'.$rowNum, $aux);
    // $pdf->Cell(44,4,utf8_decode($aux),1,0,'L',False);

    // Unidad de presentación
    $sheet->setCellValue('C'.$rowNum, $alimento['presentacion']);
    // $pdf->Cell(13.141,4,$alimento['presentacion'],1,0,'C',False);

 $cantTotal = $alimento['cant_grupo1'] + $alimento['cant_grupo2'] + $alimento['cant_grupo3'];

 $b = $alimento['D1'] + $alimento['D2'] + $alimento['D3'] + $alimento['D4'] + $alimento['D5'];

  if ($alimento['presentacion'] == 'u') {
    $cantTotal = ceil($cantTotal);
  } else {
    if ($alimento['presentacion'] == "u") {
      $cantTotal = round($cantTotal);
    } else {
      $cantTotal = number_format( $cantTotal, $digitosDecimales);
    }
  }

 $b = number_format($b, $digitosDecimales);
  $aux = 0+$cantTotal;
    //TOTAL REQUERIDO
    $sheet->setCellValue('D'.$rowNum, $aux);
    // $pdf->Cell(17.471,4,$aux,1,0,'C',False);

    if($alimento['cantotalpresentacion'] > 0){
    	$aux = 0+$alimento['cantotalpresentacion'];
	}

    $sheet->setCellValue('E'.$rowNum, '');
    // $pdf->Cell(18,4,'',1,0,'C',False);

    $banderaResta = 0;

    $alimento['cant_total'] = round_up($alimento['cant_grupo1'], $digitosDecimales) + round_up($alimento['cant_grupo2'], $digitosDecimales) + round_up($alimento['cant_grupo3'], $digitosDecimales);

    $saldo = $cantTotal;

    $letra = "F";

    for( $k = 1; $k <= 5; $k++ ){
        $consumoDia = 0;

        for( $m = 1; $m <= $k; $m++ ){
            $consumoDia = $consumoDia +  $alimento['D'.$m];
        }
         $saldo = $cantTotal;

        $consumoDia = number_format($consumoDia, $digitosDecimales);

        $saldo = $saldo - $consumoDia;

        //echo "<br>".$saldo."<br>";

        if ($alimento['presentacion'] == "u") {
          // $pdf->Cell(15.9,4,round($consumoDia),'1',0,'C',False);
          // $consumoDia = number_format($consumoDia, 0, '', '');
          $consumoDia = $alimento['D'.$k];
          $consumoDia = number_format($consumoDia, 0, '', '');
          $sheet->setCellValue($letra.$rowNum, round($consumoDia));
          // $pdf->Cell(15.9,4,round($consumoDia),'1',0,'C',False);
        } else {
          $consumoDia = $alimento['D'.$k];
          $consumoDia = number_format($consumoDia, $digitosDecimales);
          $sheet->setCellValue($letra.$rowNum, $consumoDia);
          // $pdf->Cell(15.9,4,$consumoDia,'1',0,'C',False);
        }
        $letra++;

        $sheet->setCellValue($letra.$rowNum, '');
        // $pdf->Cell(15.9,4,'','1',0,'C',False);
        //$pdf->Cell(13,4,'','1',0,'C',False);
        $letra++;

    }

    $sheet->setCellValue($letra.$rowNum, '');
    $rowNum++;
  }//Termina el for de los alimentos

  $rowNum++;
  $sheet->setCellValue('A'.$rowNum, "OBSERVACIONES : ");
  $sheet->mergeCells('A'.$rowNum.':B'.$rowNum);
  $sheet->mergeCells('C'.$rowNum.':P'.$rowNum);
  $rowNum++;
  $sheet->mergeCells('A'.$rowNum.':P'.$rowNum);

  $finInfo = $rowNum;

  $rowNum+=3;


  $sheet->getStyle("A".$inicioTitulos.":P".$finTitulos)->applyFromArray($titulos);
  $sheet->getStyle("A".$inicioInfo.":P".$finInfo)->applyFromArray($infor);

 }

$sheet->getColumnDimension("A")->setWidth(16); 

$sheet->getColumnDimension("B")->setWidth(24); 


$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="ordenes_kardex.xlsx"');
$writer->save('php://output','ordenes_kardex.xlsx');

mysqli_close ( $Link );
