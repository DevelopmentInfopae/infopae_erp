<?php
//var_dump($_POST);
error_reporting(E_ALL);
require_once 'autenticacion.php';
require('fpdf181/fpdf.php');
require_once 'db/conexion.php';
//echo "Tiempo de ejecución: ";
//echo ini_get('max_execution_time');
set_time_limit (0);
//echo "<br> Tiempo de ejecución: ";
//echo ini_get('max_execution_time');
//echo "<br> Limite de Memoria: ";
//echo ini_get('memory_limit');
ini_set('memory_limit','6000M');
//echo "<br> Limite de Memoria: ";
//echo ini_get('memory_limit');
$largoNombre = 28;

$sangria = "    ";
$tamannoFuente = 7;
$digitosDecimales = 2;
date_default_timezone_set('America/Bogota');
$hoy = date("d/m/Y");
$fechaDespacho = $hoy;




//var_dump($_POST);
//var_dump($_SESSION);










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
  $corteDeVariables = 14;
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





class PDF extends FPDF{
  function Header(){}
  function Footer(){}
}

//CREACION DEL PDF
  // Creación del objeto de la clase heredada
  $pdf= new PDF('L','mm',array(330,215.9));
  $pdf->SetMargins(8, 6.31, 8);
  $pdf->SetAutoPageBreak(false,5);
  $pdf->AliasNbPages();
  include 'funciones/funciones.php';

















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








//var_dump($alimentos);

  // Buscando propiedades y cantidades de cada alimento.






/*
   cantu2,
    cantu3,
    cantu4,
    cantu5,
    cantotalpresentacion,


*/




  for ($i=0; $i < count($alimentos) ; $i++) {
    $alimento = $alimentos[$i];
    $auxCodigo = $alimento['codigo'];

    $consulta = " select distinct ftd.codigo, ftd.Componente,
    p.nombreunidad2 presentacion,
    p.cantidadund1 cantidadPresentacion,





    m.grupo_alim, ftd.UnidadMedida, ( select Cantidad

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








    from fichatecnicadet ftd inner join productos$anno p on ftd.codigo=p.codigo inner join menu_aportes_calynut m on ftd.codigo=m.cod_prod where ftd.codigo = $auxCodigo and ftd.tipo = 'Alimento'  ";









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

  /*************************************************************/
  /*************************************************************/
  /*************************************************************/
  /*************************************************************/
  //var_dump($alimentosTotales);
  $sort = array();
  foreach($alimentos as $k=>$v) {
      $sort['componente'][$k] = $v['componente'];
      $sort['grupo_alim'][$k] = $v['grupo_alim'];
      $grupo[$k] = $v['grupo_alim'];
  }
  array_multisort($sort['grupo_alim'], SORT_ASC, $sort['componente'], SORT_ASC,$alimentos);
  sort($grupo);
  //var_dump($alimentosTotales);
  /*************************************************************/
  /*************************************************************/
  /*************************************************************/
  /*************************************************************/













  $pdf->AddPage();
  $pdf->SetTextColor(0,0,0);
  $pdf->SetFillColor(255,255,255);
  $pdf->SetDrawColor(0,0,0);
  $pdf->SetLineWidth(.05);
  $pdf->SetFont('Arial','',$tamannoFuente);


  include 'despacho_por_sede_footer.php';
  include 'despacho_kardex4_header.php';
  $grupoAlimActual = '';
  for ($i=0; $i < count($alimentos ) ; $i++) {
    $pdf->SetFont('Arial','',$tamannoFuente);
    $alimento = $alimentos[$i];
    // Se toma la distancia en y para determinar si se hace un salto de pagina.
    $current_y = $pdf->GetY();
    //$pdf->Cell(0,5,$current_y,0,5,'C',False);
    if($current_y > 205){

        $pdf->AddPage();
        include 'despacho_por_sede_footer.php';
        include 'despacho_kardex4_header.php';
    }
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFillColor(255,255,255);






    //Grupo Alimenticio
    $largoNombreGrupo = 25;
    if($alimento['grupo_alim'] != $grupoAlimActual){
        $grupoAlimActual = $alimento['grupo_alim'];
        $filas = array_count_values($grupo)[$grupoAlimActual];


        // Codigo para validar que todas las filas del grupo caben en la hoja
        $alturaGrupo = 4 * $filas;
        if($current_y + $alturaGrupo > 205){
            $pdf->AddPage();
            include 'despacho_por_sede_footer.php';
            include 'despacho_kardex4_header.php';
        }
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        // Termina codigo para validar que todas las filas del grupo caben en la hoja







        $current_y = $pdf->GetY();
        $current_x = $pdf->GetX();
        $altura = 4 * $filas;
        $pdf->Cell(44.388,$altura,'',1,0,'C',False);



        $aux = $alimento['grupo_alim'];
        $aux = mb_strtoupper($aux, 'UTF-8');
        $long_nombre=strlen($aux);
        if($long_nombre > $largoNombreGrupo){
            $margenSuperiorCelda = ((4 * $filas)/2)-4;
            $altura = 4;
            //$aux = substr($aux,0,$largoNombreGrupo);
        }
        else{
            $margenSuperiorCelda = 0;
        }






        $pdf->SetXY($current_x, $current_y+$margenSuperiorCelda);
        $pdf->MultiCell(44.388,$altura,utf8_decode($aux),0,'C',False);






        $pdf->SetXY($current_x+44.388, $current_y);
    }
    else{
         $pdf->SetX(52.388);
    }













    $aux = $alimento['componente'];
    $long_nombre=strlen($aux);
    if($long_nombre > $largoNombre){
      $aux = substr($aux,0,$largoNombre);
    }
    $pdf->Cell(44,4,utf8_decode($aux),1,0,'L',False);

    // Unidad de presentación
    $pdf->Cell(13.141,4,$alimento['presentacion'],1,0,'C',False);




 //echo "<br>".$alimento['componente'];
 //echo "<br>Total: ".$alimento['cant_total']."<br>";
 //echo "<br>";
 $cantTotal = $alimento['cant_grupo1'] + $alimento['cant_grupo2'] + $alimento['cant_grupo3'];
 //echo  $cantTotal;
 //echo "<br>";
 $b = $alimento['D1'] + $alimento['D2'] + $alimento['D3'] + $alimento['D4'] + $alimento['D5'];
 //echo $b;
 //echo "<br>";

  $cantTotal = number_format( $cantTotal, $digitosDecimales);
 $b = number_format($b, $digitosDecimales);



    //echo  $cantTotal;
    //echo "<br>";

    //echo $b;
    //echo "<br>";

    //echo  $cantTotal - $b;
    //echo "<br><br>";











    if($alimento['presentacion'] == 'u'){
      $aux = round(0+$cantTotal);

    }else{
      $aux = 0+$cantTotal;


    }
    //TOTAL REQUERIDO
    $pdf->Cell(17.471,4,$aux,1,0,'C',False);

    if($alimento['cantotalpresentacion'] > 0){
      $aux = 0+$alimento['cantotalpresentacion'];


    }







    ////$aux = number_format_unlimited_precision($aux,$digitosDecimales,'.');
    //$pdf->Cell(17.471,4,$aux,1,0,'C',False);


    // Nueva columna existencias
    $pdf->Cell(18,4,'',1,0,'C',False);


    //Bandera para saber si ha ocurrido una Resta.
    $banderaResta = 0;

    //echo "<br>".$alimento['componente']."<br>";

    //echo "<br>".number_format($alimento['cant_grupo1'], $digitosDecimales, '.', '')."<br>";
    //echo "<br>".number_format($alimento['cant_grupo2'], $digitosDecimales, '.', '')."<br>";
    //echo "<br>".number_format($alimento['cant_grupo3'], $digitosDecimales, '.', '')."<br>";



     //$alimento['cant_total'] = $alimento['cant_grupo1'] + $alimento['cant_grupo2'] + $alimento['cant_grupo3'];
     $alimento['cant_total'] = round_up($alimento['cant_grupo1'], $digitosDecimales) + round_up($alimento['cant_grupo2'], $digitosDecimales) + round_up($alimento['cant_grupo3'], $digitosDecimales);
    // $alimento['cant_total'] = number_format($alimento['cant_grupo1'], $digitosDecimales, '.', '') + number_format($alimento['cant_grupo2'], $digitosDecimales, '.', '') + number_format($alimento['cant_grupo3'], $digitosDecimales, '.', '');



     //var_dump($alimento);

    $saldo = $cantTotal;
    //echo "<br>Total: ".$saldo."<br>";



    for( $k = 1; $k <= 5; $k++ ){
        $consumoDia = 0;
        //echo "<br>Día $k";
        //echo "<br> Valor Dia:".$alimento['D'.$k]."<br>";

        for( $m = 1; $m <= $k; $m++ ){
            //echo "<br>m=$m";
            $consumoDia = $consumoDia +  $alimento['D'.$m];
        }
         $saldo = $cantTotal;










        $consumoDia = number_format($consumoDia, $digitosDecimales);
        //$consumoDia = $alimento['D'.$k];
        //$consumoDia = round_down($alimento['D'.$k], $digitosDecimales);
        //echo "<br>".$saldo." - ".$consumoDia."<br>";


        $saldo = $saldo - $consumoDia;


        $consumoDia = $alimento['D'.$k];
        $consumoDia = number_format($consumoDia, $digitosDecimales);









        $pdf->Cell(31.8,4,$consumoDia,'1',0,'C',False);
        //$pdf->Cell(31.8,4,'','1',0,'C',False);


    }

    $pdf->Cell(18,4,'','1',0,'C',False);



























    $pdf->Ln(4);
  }//Termina el for de los alimentos





  $current_y = $pdf->GetY();
  //$pdf->Cell(0,5,$current_y,0,5,'C',False);
  if($current_y > 175){

    $pdf->AddPage();
    include 'despacho_por_sede_footer.php';
    include 'despacho_kardex4_header.php';
  }

	$current_y = $pdf->GetY();
	// La hoja mide de alto 215.9 mm
	// La firma mide aprox 91 mm
	if((215.9 - $current_y) < 91){
		$pdf->AddPage();
		include 'despacho_kardex4_header.php';
		include 'despacho_firma_planilla_kardex4.php';
	}else{
		include 'despacho_firma_planilla_kardex4.php';
	}
}
mysqli_close ( $Link );
$pdf->Output();
