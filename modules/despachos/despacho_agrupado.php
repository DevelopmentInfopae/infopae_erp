<?php
include '../../config.php';
ini_set('memory_limit','6000M');
require_once '../../autentication.php';
require('../../fpdf181/fpdf.php');
include '../../php/funciones.php';
require_once '../../db/conexion.php';






$largoNombre = 40;
$sangria = " - ";




//var_dump($_POST);

$tablaAnno = $_SESSION['periodoActual'];
$tablaAnnoCompleto = $_SESSION['periodoActualCompleto'];

//require_once 'autenticacion.php';


$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
$largoNombre = 40;
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

  $consGrupoEtario = "SELECT * FROM grupo_etario ";
  $resGrupoEtario = $Link->query($consGrupoEtario);
  if ($resGrupoEtario->num_rows > 0) {
    while ($ge = $resGrupoEtario->fetch_assoc()) {
      $get[] = $ge['DESCRIPCION'];
    }
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





  $consulta = " select de.*, tc.descripcion , u.Ciudad, s.nom_sede, tc.jornada
  from despachos_enc$mesAnno de
  inner join sedes$anno  s on de.cod_Sede = s.cod_sede
  inner join ubicacion u on s.cod_mun_sede = u.CodigoDANE
  left join tipo_complemento tc on de.Tipo_Complem = tc.CODIGO
  where Tipo_Doc = 'DES' and de.Num_Doc = $valor ";



 // echo "<br>$consulta<br>";



  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link). $consulta);






  if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();

    $sede = $row['nom_sede'];
    $despacho['num_doc'] = $row['Num_Doc'];
    $despacho['cod_sede'] = $row['cod_Sede'];
    $despacho['tipo_complem'] = $row['Tipo_Complem'];
    $modalidad = $despacho['tipo_complem'];
    $despacho['semana'] = $row['Semana'];
    $despacho['cobertura'] = $row['Cobertura'];
    $despacho['ciudad'] = $row['Ciudad'];
    $diasDespacho = $row['Dias'];








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
  $arrayDiasDespacho = explode(',', $diasDespacho);
  // Buscar el mes de la semana a la que pertenecen los despachos
  $auxDias = $row['Dias'];
  $diasMostrar[] = $auxDias;

  $auxMenus = $row['Menus'];
  $menusMostrar[] = $auxMenus;

  if (!in_array($row['Semana'], $semanasMostrar, true)) {
    $semanasMostrar[] =  $row['Semana'];
    $semana = $row['Semana'];
    $consulta = " select * from planilla_semanas where SEMANA = '$semana' ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link). $consulta);


    $cantDias = $resultado->num_rows;
    if($resultado->num_rows >= 1){
      $mesInicial = '';
      $mesesIniciales = 0;
      while($row = $resultado->fetch_assoc()){







          $clave = array_search(intval($row['DIA']), $arrayDiasDespacho);
        if($clave !== false){
          $ciclo = $row['CICLO'];
          $ciclos[] = $ciclo;
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









/*
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

*/


      }
      $dias .= " de  $mes";
    }


    /*if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();
    $mes = $row['MES'];
    $ciclo = $row['CICLO'];
    $ciclos[] = $ciclo;
    $mes = mesEnLetras($mes);

    }
    else{
    echo "<br>N°o se obtuvo més de la semana de despacho.<br>";
  }*/
    // Termina la busqueda del mes al que pertenecen los despachos
}

  $despachos[] = $despacho;
  }


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

// $auxCiclos = '';
// for ($i=0; $i < count($ciclos) ; $i++) {
//   if($i > 0){
//     $auxCiclos = $auxCiclos.", ";
//   }
//   $auxCiclos = $auxCiclos.$ciclos[$i];
// }

$auxCiclos = $ciclos[0];

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

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link). $consulta);
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
// var_dump($despachos);
$alimentos = array();
for ($i=0; $i < count($despachos) ; $i++) {
  $despacho = $despachos[$i];
  if ($despacho == null) {
    continue;
  }
  $numero = $despacho['num_doc'];
  //$consulta = " select * from despachos_det$mesAnno where Tipo_Doc = 'DES' and Num_Doc = $numero ";
  $consulta = " select dd.*, pmd.CantU1,  pmd.CantU2, pmd.CantU3, pmd.CantU4, pmd.CantU5, pmd.CanTotalPresentacion, pmd.Numero as Num_Doc
  from despachos_det$mesAnno dd
  left join productosmovdet$mesAnno pmd on dd.Tipo_Doc = pmd.Documento and dd.Num_Doc = pmd.Numero and dd.cod_Alimento = pmd.CodigoProducto
  where dd.Tipo_Doc = 'DES' and dd.Num_Doc = $numero  ";




//echo "<br><br>$consulta<br><br>";





  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link). $consulta);



  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()){
      $alimento = array();
      $alimento['codigo'] = $row['cod_Alimento'];
      $alimento['Num_Doc'] = $row['Num_Doc'];
      $auxGrupo = $row['Id_GrupoEtario'];
      $alimento['grupo'.$auxGrupo] = $row['Cantidad'];

      $alimento['cantotalpresentacion'] = $row['CanTotalPresentacion'];
      $alimento['cantu2'] = $row['CantU2'];
      $alimento['cantu3'] = $row['CantU3'];
      $alimento['cantu4'] = $row['CantU4'];
      $alimento['cantu5'] = $row['CantU5'];

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
        $alimentoTotal['grupo1'] = $alimentoTotal['grupo1'] + $alimento['grupo1'];
        $alimentoTotal['grupo2'] = $alimentoTotal['grupo2'] + $alimento['grupo2'];
        $alimentoTotal['grupo3'] = $alimentoTotal['grupo3'] + $alimento['grupo3'];


        $alimentoTotal['cantotalpresentacion'] = $alimento['cantotalpresentacion'];
        $alimentoTotal['cantu2'] = $alimento['cantu2'];
        $alimentoTotal['cantu3'] = $alimento['cantu3'];
        $alimentoTotal['cantu4'] = $alimento['cantu4'];
        $alimentoTotal['cantu5'] = $alimento['cantu5'];






        $alimentosTotales[$j] = $alimentoTotal;
        break;
      }
  }
  if($encontrado == 0){
    $alimentosTotales[] = $alimento;
  }
}

// var_dump($alimentosTotales);









// Vamos a traer los datos que faltan para mostrar en la tabla
for ($i=0; $i < count($alimentosTotales) ; $i++) {
  $alimentoTotal = $alimentosTotales[$i];
  $auxCodigo = $alimentoTotal['codigo'];
  $auxDespacho = $alimentoTotal["Num_Doc"];
          $consulta = "SELECT DISTINCT p.Codigo, p.Descripcion AS Componente, p.nombreunidad2 presentacion,m.grupo_alim,m.orden_grupo_alim, p.NombreUnidad2, p.NombreUnidad3, p.NombreUnidad4, p.NombreUnidad5,
            (SELECT Umedida FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $auxDespacho AND CodigoProducto = $auxCodigo limit 1 ) AS Umedida
              FROM productos$anno p
              LEFT JOIN fichatecnicadet ftd ON ftd.codigo=p.Codigo
              INNER JOIN menu_aportes_calynut m ON p.Codigo=m.cod_prod
              WHERE p.Codigo = $auxCodigo";



 //echo "<br><br>$consulta<br><br>";


  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link). $consulta);
  if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();
    $alimentoTotal['componente'] = $row['Componente'];
    $alimentoTotal['presentacion'] = $row['Umedida'];
    $alimentoTotal['grupo_alim'] = $row['grupo_alim'];

    $alimentoTotal['nombreunidad2'] = $row['NombreUnidad2'];
    $alimentoTotal['nombreunidad3'] = $row['NombreUnidad3'];
    $alimentoTotal['nombreunidad4'] = $row['NombreUnidad4'];
    $alimentoTotal['nombreunidad5'] = $row['NombreUnidad5'];











  }
  $alimentosTotales[$i] = $alimentoTotal;
}

mysqli_close ( $Link );







/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/
//  var_dump($alimentos);
unset($sort);
unset($grupo);
$sort = array();
$grupo = array();
foreach($alimentosTotales as $kOrden=>$vOrden) {
    $sort['componente'][$kOrden] = $vOrden['componente'];
    $sort['grupo_alim'][$kOrden] = $vOrden['grupo_alim'];
    $grupo[$kOrden] = $vOrden['grupo_alim'];
}
array_multisort($sort['grupo_alim'], SORT_ASC,$alimentosTotales);

//var_dump($alimentos);
//array_multisort($sort['grupo_alim'], SORT_ASC,$alimentos);
sort($grupo);
//var_dump($alimentosTotales);
/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/

















class PDF extends FPDF{
  function Header(){}
  function Footer(){}
}

//CREACION DEL PDF
// Creación del objeto de la clase heredada
$pdf= new PDF('L','mm',array(279.4,215.9));
$pdf->SetMargins(8, 6.31, 8);
$pdf->SetAutoPageBreak(false,5);
$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(.05);
$pdf->SetFont('Arial','',8);

include 'despacho_agrupado_footer.php';
include 'despacho_agrupado_header.php';

//var_dump($item);
$filas = 0;
$grupoAlimActual = '';
$pdf->SetFont('Arial','',8);
$grupoAlimActual = '';


   for ($i=0; $i < count($alimentosTotales ) ; $i++) {
      $item = $alimentosTotales[$i];
      if($item['componente'] != ''){




      $filas = $filas+1;
      if($filas > 26){
         $filas = 0;
         $pdf->AddPage();
         include 'despacho_agrupado_footer.php';
         include 'despacho_agrupado_header.php';
         $pdf->SetFont('Arial','',8);
      }

      $pdf->SetTextColor(0,0,0);
      $pdf->SetFillColor(255,255,255);

      $aux = $item['componente'];
      $long_nombre=strlen($aux);
      if($long_nombre > $largoNombre){
         $aux = substr($aux,0,$largoNombre);
      }

      $pdf->Cell(72.76,5,utf8_decode($aux),1,0,'L',False);

      if($item['presentacion'] == 'u'){
         $aux = round(0+$item['grupo1']);
      }else{
         $aux = 0+$item['grupo1'];
         $aux = number_format($aux, 2, '.', '');
      }
      $pdf->Cell(13.1,5,utf8_decode($aux),1,0,'C',False);


    if($item['presentacion'] == 'u'){
      $aux = round(0+$item['grupo2']);
    }else{
      $aux = 0+$item['grupo2'];
      $aux = number_format($aux, 2, '.', '');
    }
    $pdf->Cell(13.1,5,utf8_decode($aux),1,0,'C',False);

    if($item['presentacion'] == 'u'){
      $aux = round(0+$item['grupo3']);
    }else{
      $aux = 0+$item['grupo3'];
      $aux = number_format($aux, 2, '.', '');
    }
    $pdf->Cell(13.1,5,utf8_decode($aux),1,0,'C',False);
    $pdf->Cell(13.141,5,$item['presentacion'],1,0,'C',False);


    $aux = $item['grupo1']+$item['grupo2']+$item['grupo3'];

    if ((strpos($alimento['componente'], "huevo"))) {

      $aux = ceil($aux);

    } else {
      if($item['presentacion'] == 'u'){
        $aux = round(0+$aux);
      } else{
        $aux = number_format($aux, 2, '.', '');
      }
    }


    $pdf->Cell(13.141,5,($aux),1,0,'C',False);
      //total


    if($item['cantotalpresentacion'] > 0){

      $aux = 0+$item['cantotalpresentacion'];
      $aux = number_format($aux, 2, '.', '');
    }

    $pdf->Cell(10.6,5,$aux,1,0,'C',False);

    $pdf->Cell(10.638,5,'',1,0,'C',False);
    $pdf->Cell(10.6,5,'',1,0,'C',False);
    $pdf->Cell(13.626,5,'',1,0,'C',False);
    $pdf->Cell(13.626,5,'',1,0,'C',False);
    $pdf->Cell(9.349,5,'',1,0,'C',False);
    $pdf->Cell(8.819,5,'',1,0,'C',False);
    $pdf->Cell(14.023,5,'',1,0,'C',False);
    $pdf->Cell(9.26,5,'',1,0,'C',False);
    $pdf->Cell(9.084,5,'',1,0,'C',False);
    $pdf->Cell(15.403,5,'',1,0,'C',False);
    $pdf->Ln(5);



    $alimento = $item;









                $unidad = 2;
          if($alimento['cantu'.$unidad] > 0){


            $presentacion = " ".$alimento['nombreunidad'.$unidad];

            $filas = $filas+1;
            if($filas > 26){
              $filas = 0;
              $pdf->AddPage();
              include 'despacho_por_sede_footer.php';
              include 'despacho_agrupado_header.php';
            }
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(255,255,255);

            $aux = $sangria.$alimento['componente'].$presentacion;



            $long_nombre=strlen($aux);
            if($long_nombre > $largoNombre){
              $aux = substr($aux,0,$largoNombre);
            }
            $pdf->Cell(72.76,5,utf8_decode($aux),1,0,'L',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.141,5,'',1,0,'C',False);
            $pdf->Cell(13.141,5,'',1,0,'C',False);
            $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
            $pdf->Cell(10.6,5,$aux,1,0,'C',False);
            $pdf->Cell(10.638,5,'',1,0,'C',False);
            $pdf->Cell(10.6,5,'',1,0,'C',False);
            $pdf->Cell(13.626,5,'',1,0,'C',False);
            $pdf->Cell(13.626,5,'',1,0,'C',False);
            $pdf->Cell(9.349,5,'',1,0,'C',False);
            $pdf->Cell(8.819,5,'',1,0,'C',False);
            $pdf->Cell(14.023,5,'',1,0,'C',False);
            $pdf->Cell(9.26,5,'',1,0,'C',False);
            $pdf->Cell(9.084,5,'',1,0,'C',False);
            $pdf->Cell(15.403,5,'',1,0,'C',False);
            $pdf->Ln(5);
          }

          $unidad = 3;
          if($alimento['cantu'.$unidad] > 0){

            $presentacion = " ".$alimento['nombreunidad'.$unidad];


            $filas = $filas+1;
            if($filas > 26){
              $filas = 0;
              $pdf->AddPage();
              include 'despacho_por_sede_footer.php';
              include 'despacho_agrupado_header.php';
            }
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(255,255,255);

            $aux = $sangria.$alimento['componente'].$presentacion;
            $long_nombre=strlen($aux);
            if($long_nombre > $largoNombre){
              $aux = substr($aux,0,$largoNombre);
            }
            $pdf->Cell(72.76,5,utf8_decode($aux),1,0,'L',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.141,5,'',1,0,'C',False);
            $pdf->Cell(13.141,5,'',1,0,'C',False);
            $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
            $pdf->Cell(10.6,5,$aux,1,0,'C',False);
            $pdf->Cell(10.638,5,'',1,0,'C',False);
            $pdf->Cell(10.6,5,'',1,0,'C',False);
            $pdf->Cell(13.626,5,'',1,0,'C',False);
            $pdf->Cell(13.626,5,'',1,0,'C',False);
            $pdf->Cell(9.349,5,'',1,0,'C',False);
            $pdf->Cell(8.819,5,'',1,0,'C',False);
            $pdf->Cell(14.023,5,'',1,0,'C',False);
            $pdf->Cell(9.26,5,'',1,0,'C',False);
            $pdf->Cell(9.084,5,'',1,0,'C',False);
            $pdf->Cell(15.403,5,'',1,0,'C',False);
            $pdf->Ln(5);
          }

          $unidad = 4;
          if($alimento['cantu'.$unidad] > 0){

            $presentacion = " ".$alimento['nombreunidad'.$unidad];

            $filas = $filas+1;
            if($filas > 26){
              $filas = 0;
              $pdf->AddPage();
              include 'despacho_por_sede_footer.php';
              include 'despacho_agrupado_header.php';
            }
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(255,255,255);

            $aux = $sangria.$alimento['componente'].$presentacion;
            $long_nombre=strlen($aux);
            if($long_nombre > $largoNombre){
              $aux = substr($aux,0,$largoNombre);
            }
            $pdf->Cell(72.76,5,utf8_decode($aux),1,0,'L',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.141,5,'',1,0,'C',False);
            $pdf->Cell(13.141,5,'',1,0,'C',False);
            $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
            $pdf->Cell(10.6,5,$aux,1,0,'C',False);
            $pdf->Cell(10.638,5,'',1,0,'C',False);
            $pdf->Cell(10.6,5,'',1,0,'C',False);
            $pdf->Cell(13.626,5,'',1,0,'C',False);
            $pdf->Cell(13.626,5,'',1,0,'C',False);
            $pdf->Cell(9.349,5,'',1,0,'C',False);
            $pdf->Cell(8.819,5,'',1,0,'C',False);
            $pdf->Cell(14.023,5,'',1,0,'C',False);
            $pdf->Cell(9.26,5,'',1,0,'C',False);
            $pdf->Cell(9.084,5,'',1,0,'C',False);
            $pdf->Cell(15.403,5,'',1,0,'C',False);
            $pdf->Ln(5);
          }

          $unidad = 5;
          if($alimento['cantu'.$unidad] > 0){


           $presentacion = " ".$alimento['nombreunidad'.$unidad];




            $filas = $filas+1;
            if($filas > 26){
              $filas = 0;
              $pdf->AddPage();
              include 'despacho_por_sede_footer.php';
              include 'despacho_agrupado_header.php';
            }
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(255,255,255);

            $aux = $sangria.$alimento['componente'].$presentacion;
            $long_nombre=strlen($aux);
            if($long_nombre > $largoNombre){
              $aux = substr($aux,0,$largoNombre);
            }
            $pdf->Cell(72.76,5,utf8_decode($aux),1,0,'L',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.1,5,'',1,0,'C',False);
            $pdf->Cell(13.141,5,'',1,0,'C',False);
            $pdf->Cell(13.141,5,'',1,0,'C',False);
            $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
            $pdf->Cell(10.6,5,$aux,1,0,'C',False);
            $pdf->Cell(10.638,5,'',1,0,'C',False);
            $pdf->Cell(10.6,5,'',1,0,'C',False);
            $pdf->Cell(13.626,5,'',1,0,'C',False);
            $pdf->Cell(13.626,5,'',1,0,'C',False);
            $pdf->Cell(9.349,5,'',1,0,'C',False);
            $pdf->Cell(8.819,5,'',1,0,'C',False);
            $pdf->Cell(14.023,5,'',1,0,'C',False);
            $pdf->Cell(9.26,5,'',1,0,'C',False);
            $pdf->Cell(9.084,5,'',1,0,'C',False);
            $pdf->Cell(15.403,5,'',1,0,'C',False);
            $pdf->Ln(5);
          }





















}
  }// Termina el for de los alimentos



 $current_y = $pdf->GetY();
  if($current_y > 175){
    $filas = 0;
    $pdf->AddPage();
    include 'despacho_agrupado_footer.php';
    include 'despacho_agrupado_header.php';
  }
  include 'despacho_firma_planilla.php';



$pdf->Output();
