<?php
error_reporting(E_ALL);
//set_time_limit (0);
ini_set('memory_limit','6000M');
include '../../config.php';
require_once '../../autentication.php';
require('../../fpdf181/fpdf.php');
require_once '../../db/conexion.php';
include '../../php/funciones.php';


$largoNombre = 30;
$sangria = " - ";
$tamannoFuente = 6;
$digitosDecimales = 2;

//var_dump($_POST);

$tablaAnno = $_SESSION['periodoActual'];
$tablaAnnoCompleto = $_SESSION['periodoActualCompleto'];

//require_once 'autenticacion.php';

  $consGrupoEtario = "SELECT * FROM grupo_etario ";
  $resGrupoEtario = $Link->query($consGrupoEtario);
  if ($resGrupoEtario->num_rows > 0) {
    while ($ge = $resGrupoEtario->fetch_assoc()) {
      $get[] = $ge['DESCRIPCION'];
    }
  }


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



// var_dump($_POST);

$_POST = array_slice($_POST, $corteDeVariables);
$_POST = array_values($_POST);

// var_dump($_POST);

$codesedes = [];



$annoActual = $tablaAnnoCompleto;
//var_dump($_POST);
$despachosRecibidos = $_POST;


foreach ($despachosRecibidos as &$valor){
  //echo "<br>".$valor."<br>";
  $consulta = " SELECT de.*, tc.descripcion , u.Ciudad, tc.jornada, s.nom_inst , s.nom_sede FROM despachos_enc$mesAnno de 
                INNER JOIN sedes$anno  s ON de.cod_Sede = s.cod_sede 
                INNER JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE 
                INNER JOIN tipo_complemento tc ON de.Tipo_Complem = tc.CODIGO 
                WHERE Tipo_Doc = 'DES' AND de.Num_Doc = $valor ";
  // echo $consulta;
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();
    $codesedes[$row['cod_Sede']] = $row['nom_sede'];
  }

}

// var_dump($codesedes);

        class PDF extends FPDF{
          function Header(){}
          function Footer(){}
        }

        //CREACION DEL PDF
        // Creación del objeto de la clase heredada

        $pdf= new PDF('L','mm',array(280,220));
        $pdf->SetMargins(8, 6.31, 8);
        $pdf->SetAutoPageBreak(false,5);
        $pdf->AliasNbPages();

// Se va a hacer una cossulta pare cojer los datos de cada movimiento, entre ellos el
// municipio que lo usaremos en los encabezados de la tabla.

foreach ($codesedes as $sedecod => $isset) {
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

        $nomInstitucion = '';
        $nomSede = '';

        foreach ($despachosRecibidos as &$valor){
          //echo "<br>".$valor."<br>";
          $consulta = " SELECT de.*, tc.descripcion , u.Ciudad, tc.jornada, s.nom_inst , s.nom_sede FROM despachos_enc$mesAnno de 
                        INNER JOIN sedes$anno  s ON de.cod_Sede = s.cod_sede
                        INNER JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE 
                        LEFT JOIN tipo_complemento tc ON de.Tipo_Complem = tc.CODIGO 
                        WHERE Tipo_Doc = 'DES' AND de.Num_Doc = ".$valor."  AND s.cod_sede = '".$sedecod."' ";
          // echo "<br>$consulta<br>";
          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta);
          if($resultado->num_rows >= 1){
            $row = $resultado->fetch_assoc();
            if($nomInstitucion == ''){
              $nomInstitucion = $row['nom_inst'];
            }
            if($nomSede == ''){
              $nomSede = $row['nom_sede'];
            }

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
            $diasMostrar[] = $auxDias;

            $auxMenus = $row['Menus'];
            $menusMostrar[] = $auxMenus;

            if(!in_array($row['Semana'], $semanasMostrar, true)){
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
            } // Termina la busqueda del mes al que pertenecen los despachos
          $despachos[] = $despacho;
          unset($despacho);
          } else {
            continue;
          }
        }// Termina el For Each de los despachos recibidos
        //var_dump($despachos);

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

        $auxDias = "X ".$cantDias." DíAS ".$auxDias." ".$mes;
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
        $banderaTotales = 0;
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


              //var_dump($row);


              if($banderaTotales == 0){
                $total1 = $total1 + $row[$aux1];
                $total2 = $total2 + $row[$aux2];
                $total3 = $total3 + $row[$aux3];
                $totalTotal = $totalTotal +  $sedeCobertura['total'];
                $banderaTotales++;
              }

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
          $consulta = " select dd.*, pmd.CantU1,  pmd.CantU2, pmd.CantU3, pmd.CantU4, pmd.CantU5, pmd.CanTotalPresentacion 
          from despachos_det$mesAnno dd 
          left join productosmovdet$mesAnno pmd on dd.Tipo_Doc = pmd.Documento and dd.Num_Doc = pmd.Numero and dd.cod_Alimento = pmd.CodigoProducto 
          where dd.Tipo_Doc = 'DES' and dd.Num_Doc = $numero  ";

          //echo "<br>".$consulta."<br>";

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

              $alimento['d1'] = $row['D1'];
              $alimento['d2'] = $row['D2'];
              $alimento['d3'] = $row['D3'];
              $alimento['d4'] = $row['D4'];
              $alimento['d5'] = $row['D5'];

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

            for ($j=0; $j < count($alimentosTotales) ; $j++){
                $alimentoTotal = $alimentosTotales[$j];
                if($alimento['codigo'] == $alimentoTotal['codigo']){
                    $encontrado++;

                    if($alimentoTotal['Num_Doc'] != $alimento['Num_Doc']){
                        $alimentoTotal['cantotalpresentacion'] = $alimentoTotal['cantotalpresentacion'] + $alimento['cantotalpresentacion'];
                        $alimentoTotal['cantu2'] = $alimentoTotal['cantu2'] + $alimento['cantu2'];
                        $alimentoTotal['cantu3'] = $alimentoTotal['cantu3'] + $alimento['cantu3'];
                        $alimentoTotal['cantu4'] = $alimentoTotal['cantu4'] + $alimento['cantu4'];
                        $alimentoTotal['cantu5'] = $alimentoTotal['cantu5'] + $alimento['cantu5'];
                        $alimentoTotal['Num_Doc'] = $alimento['Num_Doc'];
                    }

                    $alimentoTotal['grupo1'] = $alimentoTotal['grupo1'] + $alimento['grupo1'];
                    $alimentoTotal['grupo2'] = $alimentoTotal['grupo2'] + $alimento['grupo2'];
                    $alimentoTotal['grupo3'] = $alimentoTotal['grupo3'] + $alimento['grupo3'];

                    $alimentoTotal['d1'] = $alimentoTotal['d1'] + $alimento['d1'];
                    $alimentoTotal['d2'] = $alimentoTotal['d2'] + $alimento['d2'];
                    $alimentoTotal['d3'] = $alimentoTotal['d3'] + $alimento['d3'];
                    $alimentoTotal['d4'] = $alimentoTotal['d4'] + $alimento['d4'];
                    $alimentoTotal['d5'] = $alimentoTotal['d5'] + $alimento['d5'];

                    $alimentosTotales[$j] = $alimentoTotal;
                    break;
                }
            }
            if($encontrado == 0){
                $alimentosTotales[] = $alimento;
            }
        }

        // Vamos a traer los datos que faltan para mostrar en la tabla
        for ($i=0; $i < count($alimentosTotales) ; $i++) {
          $alimentoTotal = $alimentosTotales[$i];
          $auxCodigo = $alimentoTotal['codigo'];
          $consulta = " select distinct ftd.codigo, ftd.Componente,p.nombreunidad2 presentacion, m.grupo_alim, m.orden_grupo_alim, p.NombreUnidad2, p.NombreUnidad3, p.NombreUnidad4, p.NombreUnidad5
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

        // mysqli_close ( $Link );

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
        array_multisort($sort['grupo_alim'], SORT_ASC, $sort['componente'], SORT_ASC,$alimentosTotales);

        //var_dump($alimentos);
        //array_multisort($sort['grupo_alim'], SORT_ASC,$alimentos);
        sort($grupo);
        //var_dump($alimentosTotales);
        /*************************************************************/
        /*************************************************************/
        /*************************************************************/
        /*************************************************************/


        // var_dump($alimentosTotales);


        $pdf->AddPage();
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.05);
        $pdf->SetFont('Arial','',$tamannoFuente);

        include 'despacho_consolidado_footer.php';
        include 'despacho_kardex4_multiple_header.php';

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
            $caracteresPorLinea = 30;
            $anchoCelda = 42.4;
            if($item['grupo_alim'] != $grupoAlimActual){
              $grupoAlimActual = $item['grupo_alim'];
              $filas = array_count_values($grupo)[$grupoAlimActual];
              $cantAlimentosGrupo = $filas;

              // Mirar si caben todas las filas del grupo.
              if(($current_y + (4*$filas)) > 187){
                $pdf->AddPage();
                include 'despacho_por_sede_footer.php';
                include 'despacho_kardex4_multiple_header.php';
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
              $pdf->MultiCell($anchoCelda,$altura,utf8_decode($aux),0,'C',False);
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
              $long_nombre=strlen($aux);
              if($long_nombre > $largoNombre){
                 $aux = substr($aux,0,$largoNombre);
              }
              $pdf->Cell(45.2,4,utf8_decode($aux),1,0,'L',False);
              // Unidad de medida
              $pdf->Cell(13.141,4,$item['presentacion'],1,0,'C',False);
              $aux = $item['grupo1']+$item['grupo2']+$item['grupo3'];
              if (strpos($item['componente'], "huevo")) {
                $aux = ceil(0+$aux);
              } else {
                if($item['presentacion'] == 'u'){
                  $aux = round(0+$aux);
                }
                else{
                  $aux = number_format($aux, 2, '.', '');
                }
              }
              //TOTAL REQUERIDO
              $pdf->Cell(17.471,4,$aux,1,0,'C',False);
              if($item['cantotalpresentacion'] > 0 ){
                $aux = $item['cantotalpresentacion'];
                if($item['presentacion'] == 'u'){
                  $aux = round(0+$aux);
                }
                else{
                  $aux = number_format($aux, 2, '.', '');
                }
              }
              //Imprimiendo TOTAL
              //$pdf->Cell(17.471,4,$aux,1,0,'C',False);
              $pdf->Cell(16,4,'',1,0,'C',False);
              for( $k = 1; $k <= 5; $k++ ){
                $consumoDia = 0;
                $consumoDia = $item['d'.$k];
                if ($item['presentacion'] == "u") {
                $consumoDia = number_format($consumoDia, 0, '', '');
                  $pdf->Cell(22,4,round($consumoDia),'1',0,'C',False);
                } else {
                  $consumoDia = number_format($consumoDia, $digitosDecimales);
                  $pdf->Cell(22,4,$consumoDia,'1',0,'C',False);
                }
                //$pdf->Cell(31.8,4,'','1',0,'C',False);
              }
              $pdf->Cell(19.7,4,'','1',0,'C',False);
              $pdf->Ln(4);
            }//Termina el if que validad si hay cantidades en las unidades con el fin de ocultar la fila inicial.
            $alimento = $item;
          }
        }

        for ($s=1; $s <=5 ; $s++) { 
          $pdf->Cell(42.4,$altura,'',1,0,'C',False);//GRUPO ALIMENTO
          $pdf->Cell(45.2,$altura,'',1,0,'C',False);//ALIMENTO
          $pdf->Cell(13.141,$altura,'',1,0,'C',False);//U MEDIDA
          $pdf->Cell(17.5,$altura,'',1,0,'C',False);//CANT ENTREGADA
          $pdf->Cell(16,$altura,'',1,0,'C',False); //EXISTENCIAS
          $pdf->Cell(22,$altura,'',1,0,'C',False);
          $pdf->Cell(22,$altura,'',1,0,'C',False);
          $pdf->Cell(22,$altura,'',1,0,'C',False);
          $pdf->Cell(22,$altura,'',1,0,'C',False); //}
          $pdf->Cell(22,$altura,'',1,0,'C',False);
          $pdf->Cell(19.7,$altura,'',1,0,'C',False);
          $pdf->Ln();
        }

        $current_y = $pdf->GetY();
        if($current_y > 175){
          $filas = 0;
          $pdf->AddPage();
          include 'despacho_consolidado_footer.php';
          include 'despacho_kardex4_multiple_header.php';
        }
        // include 'despacho_firma_planilla.php';
        $current_y = $pdf->GetY();
        // La hoja mide de alto 215.9 mm
        // La firma mide aprox 91 mm
        if((215.9 - $current_y) < 60){
          $pdf->AddPage();
          include 'despacho_kardex4_multiple_header.php';
          include 'despacho_firma_planilla_kardex3.php';
        }else{
          include 'despacho_firma_planilla_kardex3.php';
        }
}
mysqli_close ( $Link );
$pdf->Output();