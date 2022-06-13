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
$paginasObservaciones = 1;
$tablaAnno = $_SESSION['periodoActual'];
$tablaAnnoCompleto = $_SESSION['periodoActualCompleto'];

$consGrupoEtario = "SELECT * FROM grupo_etario ";
$resGrupoEtario = $Link->query($consGrupoEtario);
if ($resGrupoEtario->num_rows > 0) {
   while ($ge = $resGrupoEtario->fetch_assoc()) {
      $get[] = $ge['DESCRIPCION'];
   }
}

date_default_timezone_set('America/Bogota');
$hoy = date("d/m/Y");
$fechaDespacho = $hoy;

// Se va a recuperar el mes y el año para las tablaMesAnno
$mesAnno = '';
$mes = $_POST['mesiConsulta'];
if($mes < 10){ $mes = '0'.$mes; }
$mes = trim($mes);
$anno = $_POST['annoi'];
$anno = substr($anno, -2);
$anno = trim($anno);
$mesAnno = $mes.$anno;
$ruta = '';

if(isset($_POST['rutaNm']) && $_POST['rutaNm']!= ''){ $ruta = $_POST['rutaNm']; }

$corteDeVariables = 16;
if(isset($_POST['seleccionarVarios'])){ $corteDeVariables++; }
if(isset($_POST['informeRuta'])){ $corteDeVariables++; }
if(isset($_POST['ruta'])){ $corteDeVariables++; }
if(isset($_POST['rutaNm'])){ $corteDeVariables++; }
if(isset($_POST['paginasObservaciones'])){ $paginasObservaciones = $_POST['paginasObservaciones']; $corteDeVariables++; }
$imprimirMes = 0;
if(isset($_POST['imprimirMes'])){
	if($_POST['imprimirMes'] == 'on'){ $imprimirMes = 1;}
	$corteDeVariables++;
}
$_SESSION['observacionesDespachos'] = "";
if(isset($_POST['observaciones'])){
   if($_POST['observaciones'] != ""){ $_SESSION['observacionesDespachos'] = $_POST['observaciones']; }
   $corteDeVariables++;
}
$_POST = array_slice($_POST, $corteDeVariables);
$_POST = array_values($_POST);

$codesedes = [];
$annoActual = $tablaAnnoCompleto;
$despachosRecibidos = $_POST;

foreach ($despachosRecibidos as &$valor){
   $consulta = "  SELECT   de.*, 
                           tc.descripcion, 
                           u.Ciudad, 
                           tc.jornada, 
                           s.nom_inst, 
                           s.nom_sede 
                  FROM despachos_enc$mesAnno de
                  INNER JOIN sedes$anno  s ON de.cod_Sede = s.cod_sede
                  INNER JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE
                  INNER JOIN tipo_complemento tc ON de.Tipo_Complem = tc.CODIGO
                  WHERE Tipo_Doc = 'DES' AND de.Num_Doc = $valor ";
   $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
   if($resultado->num_rows >= 1){
      $row = $resultado->fetch_assoc();
      $codesedes[$row['cod_Sede']] = $row['nom_sede'];
   }
}

$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];

if ($cantGruposEtarios == 3) {
   //CREACION DEL PDF
   class PDF extends FPDF{
      function Header(){}
      function Footer(){}
      var $angle=0;
      function Rotate($angle, $x=-1, $y=-1) {
         if($x==-1)
            $x=$this->x;
         if($y==-1)
            $y=$this->y;
         if($this->angle!=0)
            $this->_out('Q');
         $this->angle=$angle;
         if($angle!=0) {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
         }
      }

      function Rotate_text($x, $y, $txt, $angle) {
         //Text rotated around its origin
         $this->Rotate($angle, $x, $y);
         $this->Text($x, $y, $txt);
         $this->Rotate(0);
      }
   }

   $pdf= new PDF('L','mm',array(279, 216));
   $pdf->SetMargins(8, 6.31, 8);
   $pdf->SetAutoPageBreak(TRUE, 5);
   $pdf->AliasNbPages();

   // Se va a hacer una cossulta pare cojer los datos de cada movimiento, entre ellos el municipio que lo usaremos en los encabezados de la tabla.
   foreach ($codesedes as $sedecod => $isset) {
      $despachos = array();
      $mes = '';
      $sede = '';
      $dias = '';
      $ciclo = '';
      $nomSede = '';
      $sedes = array();
      $tipos = array();
      $ciclos = array();
      $semanas = array();
      $nomInstitucion = '';
      $municipios = array();
      $diasMostrar = array();
      $semanasMostrar = array();
     
      foreach ($despachosRecibidos as &$valor) {
         $consulta = " SELECT de.*, 
                              tc.descripcion, 
                              u.Ciudad, 
                              tc.jornada, 
                              s.nom_inst, 
                              s.nom_sede 
                           FROM despachos_enc$mesAnno de
                           INNER JOIN sedes$anno  s ON de.cod_Sede = s.cod_sede
                           INNER JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE
                           LEFT JOIN tipo_complemento tc ON de.Tipo_Complem = tc.CODIGO
                           WHERE Tipo_Doc = 'DES' AND de.Num_Doc = '".$valor."'  AND s.cod_sede = '".$sedecod."'";

         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta);
         if($resultado->num_rows >= 1) {
            $row = $resultado->fetch_assoc();
            if($nomInstitucion == '') { $nomInstitucion = $row['nom_inst']; }
            if($nomSede == '') { $nomSede = $row['nom_sede']; }
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
            if($fechaDespacho < $aux) { $fechaDespacho = date("d/m/Y",$aux); }

            // Agregando elementos a los demas array_walk_recursive
            $sedes[] = $row['cod_Sede'];
            $tipos[] = $row['Tipo_Complem'];
            $semanas[] = $row['Semana'];
            $municipios[] = $row['Ciudad'];

            //TRATAMIENTO DE LOS DIAS
            // Buscar el mes de la semana a la que pertenecen los despachos
            $auxDias = $row['Dias'];
            $auxDias2 = explode(",", $auxDias);
            $diasMostrar[] = $auxDias;
            $auxMenus = $row['Menus'];
            $menusMostrar[] = $auxMenus;
            $arreglo_meses = [];
            $semanaString = '';

            if(!in_array($row['Semana'], $semanasMostrar, true)) {
               $semanasMostrar[] =  $row['Semana'];
               $semana = $row['Semana'];
               $semanaArray = explode(',', $semana);

               foreach ($semanaArray as $key1 => $value1) {
                  $aux = trim($value1);
                  $semanaString .=   "'$aux'".',';
               }
               $semanaString = trim($semanaString, ",");
               $consulta = "SELECT * FROM planilla_semanas WHERE SEMANA IN ($semanaString) "; 
               $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
               if($resultado->num_rows >= 1) {
                  while($datos_semanas = $resultado->fetch_assoc()) {
                     foreach ($auxDias2 as $dia) {
                        if ($dia == $datos_semanas["DIA"]) {
                           $mes_actual = mesEnLetras($datos_semanas['MES']);
                           if (! in_array($mes_actual, $arreglo_meses)) { $arreglo_meses[] = $mes_actual;}
                        }
                     }
                  }
                  $meses = implode($arreglo_meses, " / ");
                  $row = $resultado->fetch_assoc();
                  $mes = $row['MES'];
                  $ciclo = $row['CICLO'];
                  $ciclos[] = $ciclo;
                  $mes = mesEnLetras($mes);
               }
               else { echo "<br>N°o se obtuvo més de la semana de despacho.<br>"; }
            } // Termina la busqueda del mes al que pertenecen los despachos 
            $despachos[] = $despacho;
            unset($despacho);
         }
         else { 
            continue; 
         }
      }// Termina el For Each de los despachos recibidos

      // Algoritmo para recorrer los dias de la semana.
      $auxDias = '';
      for ($i=0; $i < count($diasMostrar) ; $i++) {
         if($i > 0) { $auxDias = $auxDias.","; }
         $auxDias = $auxDias.$diasMostrar[$i];
      }

      $auxDias = explode(',', $auxDias);
      $auxDias = array_unique ($auxDias);
      // sort($auxDias);
      $cantDias = count($auxDias);
      $auxDias = implode(", ",$auxDias);
      sort($semanasMostrar);
      sort($ciclos);

      $auxDias = "X ".$cantDias." DíAS ".$auxDias." ".$meses;
      $auxDias = strtoupper($auxDias);
      $auxSemana = '';

      for ($i=0; $i < count($semanasMostrar) ; $i++) {
         if($i > 0) { $auxSemana = $auxSemana.", "; }
         $auxSemana = $auxSemana.$semanasMostrar[$i];
      }

      $auxCiclos = '';
      for ($i=0; $i < count($ciclos) ; $i++) {
         if($i > 0) { $auxCiclos = $auxCiclos.", "; }
         $auxCiclos = $auxCiclos.$ciclos[$i];
      }

      $auxMenus = '';
      for ($i=0; $i < count($menusMostrar) ; $i++) {
         if($i > 0) { $auxMenus = $auxMenus.","; }
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

      // Se armara un array con las coverturas de las sedes para cada uno de los grupos etarios y al final se creara un array con los totales de las sedes.
      $total1 = 0;
      $total2 = 0;
      $total3 = 0;
      $totalTotal = 0;
      $banderaTotales = 0;

      for ($i=0; $i < count($sedes) ; $i++) {
         $auxSede = $sedes[$i];

         // Consulta que busca las coberturas de las diferentes sedes.
         $consulta = "SELECT Cobertura_G1, Cobertura_G2, Cobertura_G3, cod_sede, (Cobertura_G1 + Cobertura_G2 + Cobertura_G3) AS SumaCoberturas FROM despachos_enc$mesAnno WHERE semana = '$semana' AND cod_sede = $auxSede AND Tipo_Complem = '$tipo' and estado <> 0  ORDER BY SumaCoberturas DESC LIMIT 1";
         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
         if($resultado->num_rows >= 1) {
            while($row = $resultado->fetch_assoc()) {
               $sedeCobertura['cod_sede'] = $row['cod_sede'];
               $sedeCobertura['grupo1'] = $row["Cobertura_G1"];
               $sedeCobertura['grupo2'] = $row["Cobertura_G2"];
               $sedeCobertura['grupo3'] = $row["Cobertura_G3"];
               $sedeCobertura['total'] = $row["Cobertura_G1"] + $row["Cobertura_G2"] + $row["Cobertura_G3"];
               $sedesCobertura[] = $sedeCobertura;
               if($banderaTotales == 0) {
                  $total1 = $total1 + $row["Cobertura_G1"];
                  $total2 = $total2 + $row["Cobertura_G2"];
                  $total3 = $total3 + $row["Cobertura_G3"];
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

      // Vamos a buscar los alimentos de los despachos
      $alimentos = array();
      for ($i=0; $i < count($despachos) ; $i++) {
         $despacho = $despachos[$i];
         $numero = $despacho['num_doc'];
         $consulta = "SELECT  dd.*, 
                              pmd.CantU1,  
                              pmd.CantU2, 
                              pmd.CantU3, 
                              pmd.CantU4, 
                              pmd.CantU5, 
                              pmd.CanTotalPresentacion
                           FROM despachos_det$mesAnno dd
                           LEFT JOIN productosmovdet$mesAnno pmd ON dd.Tipo_Doc = pmd.Documento AND dd.Num_Doc = pmd.Numero AND dd.cod_Alimento = pmd.CodigoProducto
                           WHERE dd.Tipo_Doc = 'DES' AND dd.Num_Doc = $numero";

         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
         if($resultado->num_rows >= 1) {
            while($row = $resultado->fetch_assoc()) {
               $alimento = array();
               $alimento['codigo'] = $row['cod_Alimento'];
               $auxGrupo = $row['Id_GrupoEtario'];
               $alimento['grupo'.$auxGrupo] = $row['Cantidad'];
               $alimento['cantotalpresentacion'] = $row['CanTotalPresentacion'];
               $alimento['cantu2'] = $row['CantU2'];
               $alimento['cantu3'] = $row['CantU3'];
               $alimento['cantu4'] = $row['CantU4'];
               $alimento['cantu5'] = $row['CantU5'];
               $alimento['d1'] = $row['D1'] + $row['D6'] + $row['D11'] + $row['D16'] + $row['D21'];
               $alimento['d2'] = $row['D2'] + $row['D7'] + $row['D12'] + $row['D17'] + $row['D22'];
               $alimento['d3'] = $row['D3'] + $row['D8'] + $row['D13'] + $row['D18'] + $row['D23'];
               $alimento['d4'] = $row['D4'] + $row['D9'] + $row['D14'] + $row['D19'] + $row['D24'];
               $alimento['d5'] = $row['D5'] + $row['D10'] + $row['D15'] + $row['D20'] + $row['D25'];

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
            if($alimento['codigo'] == $alimentoTotal['codigo']) {
               $encontrado++;
               if($alimentoTotal['Num_Doc'] != $alimento['Num_Doc']) {
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
         if($encontrado == 0) { $alimentosTotales[] = $alimento; }
      }

      // Vamos a traer los datos que faltan para mostrar en la tabla
      for ($i=0; $i < count($alimentosTotales) ; $i++) {
         $alimentoTotal = $alimentosTotales[$i];
         $auxCodigo = $alimentoTotal['codigo'];
         $auxDespacho = $alimentoTotal["Num_Doc"];
         $consulta = "SELECT  DISTINCT p.Codigo, 
                              p.Descripcion AS Componente, 
                              p.nombreunidad2 presentacion,
                              m.grupo_alim,
                              m.orden_grupo_alim, 
                              p.NombreUnidad2, 
                              p.NombreUnidad3, 
                              p.NombreUnidad4, 
                              p.NombreUnidad5,
                              (SELECT Umedida FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $auxDespacho AND CodigoProducto = $auxCodigo limit 1 ) AS Umedida
                           FROM productos$anno p
                           LEFT JOIN fichatecnicadet ftd ON ftd.codigo=p.Codigo
                           INNER JOIN menu_aportes_calynut m ON p.Codigo=m.cod_prod
                           WHERE p.Codigo = $auxCodigo";

         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
         if($resultado->num_rows >= 1) {
            $row = $resultado->fetch_assoc();
            $alimentoTotal['componente'] = $row['Componente'];
            $alimentoTotal['presentacion'] = $row['Umedida'];
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

      array_multisort($sort['grupo_alim'], SORT_ASC, $alimentosTotales);
      sort($grupo);

      $alimentos_por_grupos_alimentos = [];
      for ($i=0; $i < count($alimentosTotales); $i++) {
         $alimento = $alimentosTotales[$i];
         $alimentos_por_grupos_alimentos[$alimento["grupo_alim"]][] = $alimento;
      }

      $pdf->AddPage();
      $pdf->SetTextColor(0 ,0, 0);
      $pdf->SetFillColor(255, 255, 255);
      $pdf->SetDrawColor(0, 0, 0);
      $pdf->SetLineWidth(.05);
      $pdf->SetFont('Arial','',$tamannoFuente);

      $tamano_carta = TRUE;
      include 'despacho_consolidado_footer.php';
      include 'despacho_kardex4_multiple_header.php';

      $filas = 0;
      $grupoAlimActual = '';
      $pdf->SetFont('Arial','',$tamannoFuente);
      $grupoAlimActual = '';
      $altura = 4;

      $posicion_X_celda_grupo_alimenticio = $pdf->GetX();
      $posicion_Y_celda_alimento = $pdf->GetY();
      foreach ($alimentos_por_grupos_alimentos as $nombre_grupo_alimenticio => $alimentos) {
         $altura_celda_grupo_alimenticio = count($alimentos) * 4;
         $altura_pagina = (isset($posicion_Y_celda_alimento) ? $posicion_Y_celda_alimento : 0) + $altura_celda_grupo_alimenticio;
         if ($altura_pagina > 204.5) {
            $pdf->AddPage();
            include 'despacho_kardex4_multiple_header.php';
         }

         $posicion_Y_celda_grupo_alimenticio = $pdf->GetY();
         foreach ($alimentos as $alimento) {
            $pdf->Cell(42.4, 4, "", 0, 0, 'L');
            $pdf->Cell(45.2, 4, utf8_decode($alimento['componente']), 1, 0, 'L');

            // Unidad de medida
            $pdf->Cell(13.141, 4, utf8_decode($alimento['presentacion']), 1, 0, 'C');

            // Cantidad entregada
            $aux = $alimento['grupo1'] + $alimento['grupo2'] + $alimento['grupo3'];

            // Existencias
            if($alimento['presentacion'] == 'u') {
               if (strpos($alimento['componente'], "HUEVO") !== FALSE) { 
                  $aux = ceil(0+$aux); }
               else { 
                  $aux = round(0+$aux); 
               }
            }
            else { 
               $aux = number_format($aux, 2, '.', ''); 
            }
            $pdf->Cell(17.471, 4, $aux, 1, 0, 'C',False);

            //Imprimiendo TOTAL
            if($alimento['cantotalpresentacion'] > 0 ) {
               $aux = $alimento['cantotalpresentacion'];
               if($alimento['presentacion'] == 'u') { 
                  $aux = round(0+$aux); 
               }
               else { 
                  $aux = number_format($aux, 2, '.', ''); 
               }
            }
            $pdf->Cell(16, 4, '', 1, 0,'C',False);

            // Días de la semana
            for($k = 1; $k <= 5; $k++) {
               $consumoDia = 0;
               $consumoDia = $alimento['d'.$k];
               if ($alimento['presentacion'] == "u") {
                  $consumoDia = number_format($consumoDia, $digitosDecimales);
                  $pdf->Cell(22, 4, $consumoDia, '1', 0, 'C');
               }
               else {
                  $consumoDia = number_format($consumoDia, $digitosDecimales);
                  $pdf->Cell(22, 4, $consumoDia, '1', 0, 'C');
               }
            }
            $pdf->Cell(19.7,4,'','1',0,'C',False);
            $pdf->Ln();
            $posicion_Y_celda_alimento = $pdf->GetY();
         }

         $pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
         $pdf->MultiCell(42.4, 4, strtoupper(utf8_decode($nombre_grupo_alimenticio)), 0, "C");
         $pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
         $pdf->MultiCell(42.4, $altura_celda_grupo_alimenticio, "", 1);
      }

      for ($s=1; $s <=5 ; $s++) {
         $current_y = $pdf->GetY();
         if($current_y > 204.5) {
            $filas = 0;
            $pdf->AddPage();
            include 'despacho_kardex4_multiple_header.php';
         }
         $pdf->Cell(42.4, $altura, "", 1, 0, 'C', False);//GRUPO ALIMENTO
         $pdf->Cell(45.2, $altura, '', 1, 0, 'C', False);//ALIMENTO
         $pdf->Cell(13.141, $altura, '', 1, 0, 'C', False);//U MEDIDA
         $pdf->Cell(17.5, $altura, '', 1, 0, 'C', False);//CANT ENTREGADA
         $pdf->Cell(16, $altura, '', 1, 0, 'C', False); //EXISTENCIAS
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False);
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False);
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False);
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False); 
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False);
         $pdf->Cell(19.7, $altura, '', 1, 0, 'C', False);
         $pdf->Ln();
      }

      $pdf->Ln(4);
      $current_y = $pdf->GetY();
      if($current_y > 171) {
         $pdf->AddPage();
         include 'despacho_kardex4_multiple_header.php';
         include 'despacho_firma_planilla_kardex3.php';
      } else {
         include 'despacho_firma_planilla_kardex3.php';
      }
   }
   mysqli_close ( $Link );
   $pdf->Output();
}

if ($cantGruposEtarios == 5) {
   //CREACION DEL PDF
   class PDF extends FPDF{
      function Header(){}
      function Footer(){}
      var $angle=0;
      function Rotate($angle, $x=-1, $y=-1) {
         if($x==-1)
            $x=$this->x;
         if($y==-1)
            $y=$this->y;
         if($this->angle!=0)
            $this->_out('Q');
         $this->angle=$angle;
         if($angle!=0) {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
         }
      }

      function Rotate_text($x, $y, $txt, $angle) {
         //Text rotated around its origin
         $this->Rotate($angle, $x, $y);
         $this->Text($x, $y, $txt);
         $this->Rotate(0);
      }
   }

   $pdf= new PDF('L','mm',array(279, 216));
   $pdf->SetMargins(8, 6.31, 8);
   $pdf->SetAutoPageBreak(TRUE, 5);
   $pdf->AliasNbPages();

   // Se va a hacer una cossulta pare cojer los datos de cada movimiento, entre ellos el municipio que lo usaremos en los encabezados de la tabla.
   foreach ($codesedes as $sedecod => $isset) {
      $despachos = array();
      $mes = '';
      $sede = '';
      $dias = '';
      $ciclo = '';
      $nomSede = '';
      $sedes = array();
      $tipos = array();
      $ciclos = array();
      $semanas = array();
      $nomInstitucion = '';
      $municipios = array();
      $diasMostrar = array();
      $semanasMostrar = array();
     
      foreach ($despachosRecibidos as &$valor) {
         $consulta = " SELECT de.*, 
                              tc.descripcion, 
                              u.Ciudad, 
                              tc.jornada, 
                              s.nom_inst, 
                              s.nom_sede 
                           FROM despachos_enc$mesAnno de
                           INNER JOIN sedes$anno  s ON de.cod_Sede = s.cod_sede
                           INNER JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE
                           LEFT JOIN tipo_complemento tc ON de.Tipo_Complem = tc.CODIGO
                           WHERE Tipo_Doc = 'DES' AND de.Num_Doc = '".$valor."'  AND s.cod_sede = '".$sedecod."'";

         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta);
         if($resultado->num_rows >= 1) {
            $row = $resultado->fetch_assoc();
            if($nomInstitucion == '') { $nomInstitucion = $row['nom_inst']; }
            if($nomSede == '') { $nomSede = $row['nom_sede']; }
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
            if($fechaDespacho < $aux) { $fechaDespacho = date("d/m/Y",$aux); }

            // Agregando elementos a los demas array_walk_recursive
            $sedes[] = $row['cod_Sede'];
            $tipos[] = $row['Tipo_Complem'];
            $semanas[] = $row['Semana'];
            $municipios[] = $row['Ciudad'];

            //TRATAMIENTO DE LOS DIAS
            // Buscar el mes de la semana a la que pertenecen los despachos
            $auxDias = $row['Dias'];
            $auxDias2 = explode(",", $auxDias);
            $diasMostrar[] = $auxDias;
            $auxMenus = $row['Menus'];
            $menusMostrar[] = $auxMenus;
            $arreglo_meses = [];
            $semanaString = '';

            if(!in_array($row['Semana'], $semanasMostrar, true)) {
               $semanasMostrar[] =  $row['Semana'];
               $semana = $row['Semana'];
               $semanaArray = explode(',', $semana);

               foreach ($semanaArray as $key1 => $value1) {
                  $aux = trim($value1);
                  $semanaString .=   "'$aux'".',';
               }
               $semanaString = trim($semanaString, ",");
               $consulta = "SELECT * FROM planilla_semanas WHERE SEMANA IN ($semanaString) "; 
               $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
               if($resultado->num_rows >= 1) {
                  while($datos_semanas = $resultado->fetch_assoc()) {
                     foreach ($auxDias2 as $dia) {
                        if ($dia == $datos_semanas["DIA"]) {
                           $mes_actual = mesEnLetras($datos_semanas['MES']);
                           if (! in_array($mes_actual, $arreglo_meses)) { $arreglo_meses[] = $mes_actual;}
                        }
                     }
                  }
                  $meses = implode($arreglo_meses, " / ");
                  $row = $resultado->fetch_assoc();
                  $mes = $row['MES'];
                  $ciclo = $row['CICLO'];
                  $ciclos[] = $ciclo;
                  $mes = mesEnLetras($mes);
               }
               else { echo "<br>N°o se obtuvo més de la semana de despacho.<br>"; }
            } // Termina la busqueda del mes al que pertenecen los despachos 
            $despachos[] = $despacho;
            unset($despacho);
         }
         else { 
            continue; 
         }
      }// Termina el For Each de los despachos recibidos

      // Algoritmo para recorrer los dias de la semana.
      $auxDias = '';
      for ($i=0; $i < count($diasMostrar) ; $i++) {
         if($i > 0) { $auxDias = $auxDias.","; }
         $auxDias = $auxDias.$diasMostrar[$i];
      }

      $auxDias = explode(',', $auxDias);
      $auxDias = array_unique ($auxDias);
      // sort($auxDias);
      $cantDias = count($auxDias);
      $auxDias = implode(", ",$auxDias);
      sort($semanasMostrar);
      sort($ciclos);

      $auxDias = "X ".$cantDias." DíAS ".$auxDias." ".$meses;
      $auxDias = strtoupper($auxDias);
      $auxSemana = '';

      for ($i=0; $i < count($semanasMostrar) ; $i++) {
         if($i > 0) { $auxSemana = $auxSemana.", "; }
         $auxSemana = $auxSemana.$semanasMostrar[$i];
      }

      $auxCiclos = '';
      for ($i=0; $i < count($ciclos) ; $i++) {
         if($i > 0) { $auxCiclos = $auxCiclos.", "; }
         $auxCiclos = $auxCiclos.$ciclos[$i];
      }

      $auxMenus = '';
      for ($i=0; $i < count($menusMostrar) ; $i++) {
         if($i > 0) { $auxMenus = $auxMenus.","; }
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

      // Se armara un array con las coverturas de las sedes para cada uno de los grupos etarios y al final se creara un array con los totales de las sedes.
      $total1 = 0;
      $total2 = 0;
      $total3 = 0;
      $total4 = 0;
      $total5 = 0;
      $totalTotal = 0;
      $banderaTotales = 0;

      for ($i=0; $i < count($sedes) ; $i++) {
         $auxSede = $sedes[$i];

         // Consulta que busca las coberturas de las diferentes sedes.
         $consulta = "SELECT  Cobertura_G1, 
                              Cobertura_G2, 
                              Cobertura_G3, 
                              Cobertura_G4, 
                              Cobertura_G5, 
                              cod_sede, 
                              (Cobertura_G1 + Cobertura_G2 + Cobertura_G3 + Cobertura_G4 + Cobertura_G5 ) AS SumaCoberturas 
                           FROM despachos_enc$mesAnno 
                           WHERE semana = '$semana' AND cod_sede = $auxSede AND Tipo_Complem = '$tipo' and estado <> 0  
                           ORDER BY SumaCoberturas DESC 
                           LIMIT 1";
         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
         if($resultado->num_rows >= 1) {
            while($row = $resultado->fetch_assoc()) {
               $sedeCobertura['cod_sede'] = $row['cod_sede'];
               $sedeCobertura['grupo1'] = $row["Cobertura_G1"];
               $sedeCobertura['grupo2'] = $row["Cobertura_G2"];
               $sedeCobertura['grupo3'] = $row["Cobertura_G3"];
               $sedeCobertura['grupo4'] = $row["Cobertura_G4"];
               $sedeCobertura['grupo5'] = $row["Cobertura_G5"];
               $sedeCobertura['total'] = $row["Cobertura_G1"] + $row["Cobertura_G2"] + $row["Cobertura_G3"] + $row['Cobertura_G4'] + $row['Cobertura_G5'];
               $sedesCobertura[] = $sedeCobertura;
               if($banderaTotales == 0) {
                  $total1 = $total1 + $row["Cobertura_G1"];
                  $total2 = $total2 + $row["Cobertura_G2"];
                  $total3 = $total3 + $row["Cobertura_G3"];
                  $total4 = $total4 + $row["Cobertura_G4"];
                  $total5 = $total5 + $row["Cobertura_G5"];
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
         "grupo4" => $total4,
         "grupo5" => $total5,
         "total"  => $totalTotal
      );
      // Termina el tema de las coberturas por sede

      $totalGrupo1 = 0;
      $totalGrupo2 = 0;
      $totalGrupo3 = 0;
      $totalGrupo4 = 0;
      $totalGrupo5 = 0;

      // Vamos a buscar los alimentos de los despachos
      $alimentos = array();
      for ($i=0; $i < count($despachos) ; $i++) {
         $despacho = $despachos[$i];
         $numero = $despacho['num_doc'];
         $consulta = "SELECT  dd.*, 
                              pmd.CantU1,  
                              pmd.CantU2, 
                              pmd.CantU3, 
                              pmd.CantU4, 
                              pmd.CantU5, 
                              pmd.CanTotalPresentacion
                           FROM despachos_det$mesAnno dd
                           LEFT JOIN productosmovdet$mesAnno pmd ON dd.Tipo_Doc = pmd.Documento AND dd.Num_Doc = pmd.Numero AND dd.cod_Alimento = pmd.CodigoProducto
                           WHERE dd.Tipo_Doc = 'DES' AND dd.Num_Doc = $numero";

         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
         if($resultado->num_rows >= 1) {
            while($row = $resultado->fetch_assoc()) {
               $alimento = array();
               $alimento['codigo'] = $row['cod_Alimento'];
               $auxGrupo = $row['Id_GrupoEtario'];
               $alimento['grupo'.$auxGrupo] = $row['Cantidad'];
               $alimento['cantotalpresentacion'] = $row['CanTotalPresentacion'];
               $alimento['cantu2'] = $row['CantU2'];
               $alimento['cantu3'] = $row['CantU3'];
               $alimento['cantu4'] = $row['CantU4'];
               $alimento['cantu5'] = $row['CantU5'];
               $alimento['d1'] = $row['D1'] + $row['D6'] + $row['D11'] + $row['D16'] + $row['D21'];
               $alimento['d2'] = $row['D2'] + $row['D7'] + $row['D12'] + $row['D17'] + $row['D22'];
               $alimento['d3'] = $row['D3'] + $row['D8'] + $row['D13'] + $row['D18'] + $row['D23'];
               $alimento['d4'] = $row['D4'] + $row['D9'] + $row['D14'] + $row['D19'] + $row['D24'];
               $alimento['d5'] = $row['D5'] + $row['D10'] + $row['D15'] + $row['D20'] + $row['D25'];

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
      if(!isset($alimento['grupo4'])){ $alimento['grupo4'] = 0;}else{ $totalGrupo4 = $totalesSedeCobertura['grupo4']; }
      if(!isset($alimento['grupo5'])){ $alimento['grupo5'] = 0;}else{ $totalGrupo5 = $totalesSedeCobertura['grupo5']; }
      $alimentosTotales = array();
      $alimentosTotales[] = $alimento;

      for ($i=1; $i < count($alimentos) ; $i++) {
         $alimento = $alimentos[$i];
         if(!isset($alimento['grupo1'])){ $alimento['grupo1'] = 0;}else{ $totalGrupo1 = $totalesSedeCobertura['grupo1']; }
         if(!isset($alimento['grupo2'])){ $alimento['grupo2'] = 0;}else{ $totalGrupo2 = $totalesSedeCobertura['grupo2']; }
         if(!isset($alimento['grupo3'])){ $alimento['grupo3'] = 0;}else{ $totalGrupo3 = $totalesSedeCobertura['grupo3']; }
         if(!isset($alimento['grupo4'])){ $alimento['grupo4'] = 0;}else{ $totalGrupo4 = $totalesSedeCobertura['grupo4']; }
         if(!isset($alimento['grupo5'])){ $alimento['grupo5'] = 0;}else{ $totalGrupo5 = $totalesSedeCobertura['grupo5']; }
         $encontrado = 0;

         for ($j=0; $j < count($alimentosTotales) ; $j++) {
            $alimentoTotal = $alimentosTotales[$j];
            if($alimento['codigo'] == $alimentoTotal['codigo']) {
               $encontrado++;
               if($alimentoTotal['Num_Doc'] != $alimento['Num_Doc']) {
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
               $alimentoTotal['grupo4'] = $alimentoTotal['grupo4'] + $alimento['grupo4'];
               $alimentoTotal['grupo5'] = $alimentoTotal['grupo5'] + $alimento['grupo5'];
               $alimentoTotal['d1'] = $alimentoTotal['d1'] + $alimento['d1'];
               $alimentoTotal['d2'] = $alimentoTotal['d2'] + $alimento['d2'];
               $alimentoTotal['d3'] = $alimentoTotal['d3'] + $alimento['d3'];
               $alimentoTotal['d4'] = $alimentoTotal['d4'] + $alimento['d4'];
               $alimentoTotal['d5'] = $alimentoTotal['d5'] + $alimento['d5'];
               $alimentosTotales[$j] = $alimentoTotal;
               break;
            }
         }
         if($encontrado == 0) { $alimentosTotales[] = $alimento; }
      }

      // Vamos a traer los datos que faltan para mostrar en la tabla
      for ($i=0; $i < count($alimentosTotales) ; $i++) {
         $alimentoTotal = $alimentosTotales[$i];
         $auxCodigo = $alimentoTotal['codigo'];
         $auxDespacho = $alimentoTotal["Num_Doc"];
         $consulta = "SELECT  DISTINCT p.Codigo, 
                              p.Descripcion AS Componente, 
                              p.nombreunidad2 presentacion,
                              m.grupo_alim,
                              m.orden_grupo_alim, 
                              p.NombreUnidad2, 
                              p.NombreUnidad3, 
                              p.NombreUnidad4, 
                              p.NombreUnidad5,
                              (SELECT Umedida FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $auxDespacho AND CodigoProducto = $auxCodigo limit 1 ) AS Umedida
                           FROM productos$anno p
                           LEFT JOIN fichatecnicadet ftd ON ftd.codigo=p.Codigo
                           INNER JOIN menu_aportes_calynut m ON p.Codigo=m.cod_prod
                           WHERE p.Codigo = $auxCodigo";

         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
         if($resultado->num_rows >= 1) {
            $row = $resultado->fetch_assoc();
            $alimentoTotal['componente'] = $row['Componente'];
            $alimentoTotal['presentacion'] = $row['Umedida'];
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

      array_multisort($sort['grupo_alim'], SORT_ASC, $alimentosTotales);
      sort($grupo);

      $alimentos_por_grupos_alimentos = [];
      for ($i=0; $i < count($alimentosTotales); $i++) {
         $alimento = $alimentosTotales[$i];
         $alimentos_por_grupos_alimentos[$alimento["grupo_alim"]][] = $alimento;
      }

      $pdf->AddPage();
      $pdf->SetTextColor(0 ,0, 0);
      $pdf->SetFillColor(255, 255, 255);
      $pdf->SetDrawColor(0, 0, 0);
      $pdf->SetLineWidth(.05);
      $pdf->SetFont('Arial','',$tamannoFuente);

      $tamano_carta = TRUE;
      include 'despacho_consolidado_footer.php';
      include 'despacho_kardex4_multiple_header.php';

      $filas = 0;
      $grupoAlimActual = '';
      $pdf->SetFont('Arial','',$tamannoFuente);
      $grupoAlimActual = '';
      $altura = 4;

      $posicion_X_celda_grupo_alimenticio = $pdf->GetX();
      $posicion_Y_celda_alimento = $pdf->GetY();
      foreach ($alimentos_por_grupos_alimentos as $nombre_grupo_alimenticio => $alimentos) {
         $altura_celda_grupo_alimenticio = count($alimentos) * 4;
         $altura_pagina = (isset($posicion_Y_celda_alimento) ? $posicion_Y_celda_alimento : 0) + $altura_celda_grupo_alimenticio;
         if ($altura_pagina > 204.5) {
            $pdf->AddPage();
            include 'despacho_kardex4_multiple_header.php';
         }

         $posicion_Y_celda_grupo_alimenticio = $pdf->GetY();
         foreach ($alimentos as $alimento) {
            $pdf->Cell(42.4, 4, "", 0, 0, 'L');
            $pdf->Cell(45.2, 4, utf8_decode($alimento['componente']), 1, 0, 'L');

            // Unidad de medida
            $pdf->Cell(13.141, 4, utf8_decode($alimento['presentacion']), 1, 0, 'C');

            // Cantidad entregada
            $aux = $alimento['grupo1'] + $alimento['grupo2'] + $alimento['grupo3'] + $alimento['grupo4'] + $alimento['grupo5'];

            // Existencias
            if($alimento['presentacion'] == 'u') {
               if (strpos($alimento['componente'], "HUEVO") !== FALSE) { 
                  $aux = ceil(0+$aux); }
               else { 
                  $aux = round(0+$aux); 
               }
            }
            else { 
               $aux = number_format($aux, 2, '.', ''); 
            }
            $pdf->Cell(17.471, 4, $aux, 1, 0, 'C',False);

            //Imprimiendo TOTAL
            if($alimento['cantotalpresentacion'] > 0 ) {
               $aux = $alimento['cantotalpresentacion'];
               if($alimento['presentacion'] == 'u') { 
                  $aux = round(0+$aux); 
               }
               else { 
                  $aux = number_format($aux, 2, '.', ''); 
               }
            }
            $pdf->Cell(16, 4, '', 1, 0,'C',False);

            // Días de la semana
            for($k = 1; $k <= 5; $k++) {
               $consumoDia = 0;
               $consumoDia = $alimento['d'.$k];
               if ($alimento['presentacion'] == "u") {
                  $consumoDia = number_format($consumoDia, $digitosDecimales);
                  $pdf->Cell(22, 4, $consumoDia, '1', 0, 'C');
               }
               else {
                  $consumoDia = number_format($consumoDia, $digitosDecimales);
                  $pdf->Cell(22, 4, $consumoDia, '1', 0, 'C');
               }
            }
            $pdf->Cell(19.7,4,'','1',0,'C',False);
            $pdf->Ln();
            $posicion_Y_celda_alimento = $pdf->GetY();
         }

         $pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
         $pdf->MultiCell(42.4, 4, strtoupper(utf8_decode($nombre_grupo_alimenticio)), 0, "C");
         $pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
         $pdf->MultiCell(42.4, $altura_celda_grupo_alimenticio, "", 1);
      }

      for ($s=1; $s <=5 ; $s++) {
         $current_y = $pdf->GetY();
         if($current_y > 204.5) {
            $filas = 0;
            $pdf->AddPage();
            include 'despacho_kardex4_multiple_header.php';
         }
         $pdf->Cell(42.4, $altura, "", 1, 0, 'C', False);//GRUPO ALIMENTO
         $pdf->Cell(45.2, $altura, '', 1, 0, 'C', False);//ALIMENTO
         $pdf->Cell(13.141, $altura, '', 1, 0, 'C', False);//U MEDIDA
         $pdf->Cell(17.5, $altura, '', 1, 0, 'C', False);//CANT ENTREGADA
         $pdf->Cell(16, $altura, '', 1, 0, 'C', False); //EXISTENCIAS
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False);
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False);
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False);
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False); 
         $pdf->Cell(22, $altura, '', 1, 0, 'C', False);
         $pdf->Cell(19.7, $altura, '', 1, 0, 'C', False);
         $pdf->Ln();
      }

      $pdf->Ln(4);
      $current_y = $pdf->GetY();
      if($current_y > 171) {
         $pdf->AddPage();
         include 'despacho_kardex4_multiple_header.php';
         include 'despacho_firma_planilla_kardex3.php';
      } else {
         include 'despacho_firma_planilla_kardex3.php';
      }
   }
   mysqli_close ( $Link );
   $pdf->Output();
}
