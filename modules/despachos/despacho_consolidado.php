<?php
	error_reporting(E_ALL);
	ini_set('memory_limit','6000M');
	include '../../config.php';
	require_once '../../autentication.php';
	require('../../fpdf181/fpdf.php');
	require_once '../../db/conexion.php';
	include '../../php/funciones.php';

	date_default_timezone_set('America/Bogota');

	$largoNombre = 30;
	$sangria = " - ";
	$tamannoFuente = 6;

	$tablaAnno = $_SESSION['periodoActual'];
	$tablaAnnoCompleto = $_SESSION['periodoActualCompleto'];

// $Link = new mysqli($Hostname, $Username, $Password, $Database);
// if ($Link->connect_errno) {
//   echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
// }
// $Link->set_charset("utf8");

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
$corteDeVariables = 16;
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

  $arrayDiasDespacho = explode(',', $diasDespacho);

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

/*
    $semanasMostrar[] =  $row['Semana'];
    $semana = $row['Semana'];
    $consulta = " select * from planilla_semanas where SEMANA = '$semana' ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();
    $mes = $row['MES'];
    $ciclo = $row['CICLO'];
    $ciclos[] = $ciclo;
    $mes = mesEnLetras($mes);

    }
    else{
    echo "<br>N°o se obtuvo més de la semana de despacho.<br>";
    }
    // Termina la busqueda del mes al que pertenecen los despachos

*/

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

  // $consulta = " select cod_sede, Etario1_$tipo, Etario2_$tipo, Etario3_$tipo from sedes_cobertura where semana = '$semana' and cod_sede = $auxSede and Ano = $annoActual ";
  $consulta = "SELECT Cobertura_G1, Cobertura_G2, Cobertura_G3, cod_sede FROM despachos_enc$mesAnno WHERE semana = '$semana' AND cod_sede = $auxSede AND Tipo_Complem = '". $tipo ."'";

  // Consulta que busca las coberturas de las diferentes sedes.
  //echo "<br><br>".$consulta."<br><br>";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){


    while($row = $resultado->fetch_assoc()) {
      $sedeCobertura['cod_sede'] = $row['cod_sede'];
      $aux1 = "Cobertura_G1";
      $sedeCobertura['grupo1'] = $row[$aux1];
      $aux2 = "Cobertura_G2";
      $sedeCobertura['grupo2'] = $row[$aux2];
      $aux3 = "Cobertura_G3";
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

mysqli_close ( $Link );






/*************************************************************/
/*************************************************************/
/*************************************************************/
/*************************************************************/
//var_dump($alimentosTotales);
unset($sort);
unset($grupo);
$sort = array();
$grupo = array();
foreach($alimentosTotales as $kOrden=>$vOrden) {
	//echo '<br>'.$vOrden['componente'];
    $sort['componente'][$kOrden] = $vOrden['componente'];
    $sort['grupo_alim'][$kOrden] = $vOrden['orden_grupo_alim']; //Se cambia el orden de acuerdo al orden por grupo de alimento
    $grupo[$kOrden] = $vOrden['grupo_alim'];
}
// array_multisort($sort['grupo_alim'], SORT_ASC, $sort['componente'], SORT_ASC,$alimentosTotales);
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
$pdf->SetFont('Arial','',$tamannoFuente);

include 'despacho_consolidado_footer.php';
include 'despacho_consolidado_header.php';

//var_dump($item);
$filas = 0;
$grupoAlimActual = '';
$pdf->SetFont('Arial','',$tamannoFuente);
$grupoAlimActual = '';


   for ($i=0; $i < count($alimentosTotales ) ; $i++) {
      $item = $alimentosTotales[$i];
   	if($item['componente'] != ''){



      $pdf->SetTextColor(0,0,0);
      $pdf->SetFillColor(255,255,255);








      //Grupo Alimenticio
    $largoNombreGrupo = 25;
     $caracteresPorLinea = 10;
    $anchoCelda = 23.788;
    if($item['grupo_alim'] != $grupoAlimActual){
        $grupoAlimActual = $item['grupo_alim'];

        $filas = array_count_values($grupo)[$grupoAlimActual];
        $cantAlimentosGrupo = $filas;

        // Se va a realizar una busqueda por si hay filas adicionales debido a presentaciones.
        for ($j=$i;$j < $i+$cantAlimentosGrupo; $j++) {
          $aux = $alimentosTotales[$j];
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

        // Mirar si caben todas las filas del grupo.
        if(($current_y + (4*$filas)) > 187){
          $pdf->AddPage();
          include 'despacho_por_sede_footer.php';
          include 'despacho_consolidado_header.php';
          $pdf->SetFont('Arial','',$tamannoFuente);

        }



        $current_y = $pdf->GetY();
        $current_x = $pdf->GetX();
        $altura = 4 * $filas;
        $pdf->Cell($anchoCelda,$altura,'',1,0,'C',False);



        $aux = $item['grupo_alim'];
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



        $pdf->SetXY($current_x, $current_y+$margenSuperiorCelda);
        $pdf->MultiCell(23.788,$altura,utf8_decode($aux),0,'C',False);






        $pdf->SetXY($current_x+$anchoCelda, $current_y);
    }
    else{
         $pdf->SetX($current_x+$anchoCelda);
    }
// Termina la impresión de grupo alimenticio



  // Se verifica que no haya cantidades en las direntes presentaciones, para no mostrar la primera fila.
  //if($item['cantu2'] <= 0 && $item['cantu3'] <= 0 && $item['cantu4'] <= 0 && $item['cantu5'] <= 0){

    if(1==1){














      $aux = $item['componente'];
      // $long_nombre=strlen($aux);
      // if($long_nombre > $largoNombre){
      //    $aux = substr($aux,0,$largoNombre);
      // }

      // Alimento
      $pdf->Cell(49,4,utf8_decode($aux),1,0,'L',False);

      if($item['presentacion'] == 'u'){
         $aux = round(0+$item['grupo1']);
      }else{
         $aux = 0+$item['grupo1'];
         $aux = number_format($aux, 2, '.', '');
      }
      $pdf->Cell(13.1,4,utf8_decode($aux),1,0,'C',False);


    if($item['presentacion'] == 'u'){
      $aux = round(0+$item['grupo2']);
    }else{
      $aux = 0+$item['grupo2'];
      $aux = number_format($aux, 2, '.', '');
    }
    $pdf->Cell(13.1,4,utf8_decode($aux),1,0,'C',False);

    if($item['presentacion'] == 'u'){
      $aux = round(0+$item['grupo3']);
    }else{
      $aux = 0+$item['grupo3'];
      $aux = number_format($aux, 2, '.', '');
    }
    $pdf->Cell(13.1,4,utf8_decode($aux),1,0,'C',False);



    $pdf->Cell(13.141,4,$item['presentacion'],1,0,'C',False);



    $aux = $item['grupo1']+$item['grupo2']+$item['grupo3'];
    $aux = number_format($aux, 2, '.', '');

   /*




    if($alimento['cantu2'] <= 0 && $alimento['cantu3'] <= 0 && $alimento['cantu4'] <= 0 && $alimento['cantu5'] <= 0){}else{ $aux = ''; }


*/



//MOSTRAR O NO CUANDO HAY PRESENTACIONES


    // Para no mostrar lso totales de los alimentos que tienen diferentes presentaciones.
    if($item['cantotalpresentacion'] > 0 ){
      //$aux = '';
    }


    //Imprimiendo CNT TOTAL
    $pdf->Cell(13.141,4,$aux,1,0,'C',False);

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




/*

     if($alimento['cantu2'] <= 0 && $alimento['cantu3'] <= 0 && $alimento['cantu4'] <= 0 && $alimento['cantu5'] <= 0){}else{ //$aux = '';
      }


*/

    //Imprimiendo TOTAL


  // CANTIDAD ENTREGADA
  $pdf->Cell(10.6,4,$aux,1,0,'C',False);
    //total entregado
  $pdf->Cell(10.6,4,'',1,0,'C',False);
  $pdf->Cell(10.6,4,'',1,0,'C',False);

  // ESPECIFICACIÓN DE CALIDAD
  $pdf->Cell(13.7,4,'',1,0,'C',False);
  $pdf->Cell(13.7,4,'',1,0,'C',False);

  // FALTANTES
  $pdf->Cell(9.2,4,'',1,0,'C',False);
  $pdf->Cell(8.9,4,'',1,0,'C',False);
  $pdf->Cell(13.9,4,'',1,0,'C',False);

  //DEVOLUCIÓN
  $pdf->Cell(9.3,4,'',1,0,'C',False);
  $pdf->Cell(9.3,4,'',1,0,'C',False);
  $pdf->Cell(15.2,4,'',1,0,'C',False);

  $pdf->Ln(4);


}//Termina el if que validad si hay cantidades en las unidades con el fin de ocultar la fila inicial.



    $alimento = $item;











          $unidad = 2;
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
            $pdf->Cell(68.3,4,utf8_decode($aux),1,0,'L',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.141,4,'',1,0,'C',False);
            $pdf->Cell(13.141,4,'',1,0,'C',False);
            $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
			// CANTIDAD ENTREGADA
		    $pdf->Cell(9.3,4,$aux,1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    // ESPECIFICACIÓN DE CALIDAD
		    $pdf->Cell(11,4,'',1,0,'C',False);
		    $pdf->Cell(11,4,'',1,0,'C',False);
		    // FALTANTES
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    //DEVOLUCIÓN
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
            $pdf->Ln(4);
          }

          $unidad = 3;
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
            $pdf->Cell(68.3,4,utf8_decode($aux),1,0,'L',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.141,4,'',1,0,'C',False);
            $pdf->Cell(13.141,4,'',1,0,'C',False);
            $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
			// CANTIDAD ENTREGADA
		    $pdf->Cell(9.3,4,$aux,1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    // ESPECIFICACIÓN DE CALIDAD
		    $pdf->Cell(11,4,'',1,0,'C',False);
		    $pdf->Cell(11,4,'',1,0,'C',False);
		    // FALTANTES
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    //DEVOLUCIÓN
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
            $pdf->Ln(4);
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
            $pdf->Cell(68.3,4,utf8_decode($aux),1,0,'L',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.141,4,'',1,0,'C',False);
            $pdf->Cell(13.141,4,'',1,0,'C',False);
            $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
			// CANTIDAD ENTREGADA
		    $pdf->Cell(9.3,4,$aux,1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    // ESPECIFICACIÓN DE CALIDAD
		    $pdf->Cell(11,4,'',1,0,'C',False);
		    $pdf->Cell(11,4,'',1,0,'C',False);
		    // FALTANTES
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    //DEVOLUCIÓN
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
            $pdf->Ln(4);
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
            $pdf->Cell(68.3,4,utf8_decode($aux),1,0,'L',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.1,4,'',1,0,'C',False);
            $pdf->Cell(13.141,4,'',1,0,'C',False);
            $pdf->Cell(13.141,4,'',1,0,'C',False);
            $aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
			// CANTIDAD ENTREGADA
		    $pdf->Cell(9.3,4,$aux,1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    // ESPECIFICACIÓN DE CALIDAD
		    $pdf->Cell(11,4,'',1,0,'C',False);
		    $pdf->Cell(11,4,'',1,0,'C',False);
		    // FALTANTES
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    //DEVOLUCIÓN
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
		    $pdf->Cell(9.3,4,'',1,0,'C',False);
            $pdf->Ln(4);
          }











}
  }



 $current_y = $pdf->GetY();
  if($current_y > 175){
    $filas = 0;
    $pdf->AddPage();
    include 'despacho_consolidado_footer.php';
    include 'despacho_consolidado_header.php';
  }



  $current_y = $pdf->GetY();
  // $pdf->Cell(0,5,$current_y,0,5,'C',False);
  if($current_y > 167){
    $pdf->AddPage();
    include 'despacho_consolidado_footer.php';
    include 'despacho_consolidado_header.php';
  }
  include 'despacho_firma_planilla.php';






$pdf->Output();
