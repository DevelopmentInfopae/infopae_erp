<?php
include '../../config.php';
require_once '../../autentication.php';
require('../../fpdf181/fpdf.php');
require_once '../../db/conexion.php';
include '../../php/funciones.php';

set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

$mesAnno = '';
$sangria = " - ";
$largoNombre = 28;
$tamannoFuente = 7;
$digitosDecimales = 2;
$paginasObservaciones = 1;

if( isset($_POST['despachoAnnoI']) && isset($_POST['despachoMesI']) && isset($_POST['despacho']) ){
   // Se va a recuperar el mes y el año para las tablaMesAnno
   $mes = $_POST['despachoMesI'];
   if($mes < 10){ $mes = '0'.$mes; }
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
   if($mes < 10){ $mes = '0'.$mes; }
   $mes = trim($mes);
   $anno = $_POST['annoi'];
   $anno = substr($anno, -2);
   $anno = trim($anno);
   $mesAnno = $mes.$anno;

   $corteDeVariables = 15;
   if(isset($_POST['seleccionarVarios'])){ $corteDeVariables++; }
   if(isset($_POST['informeRuta'])){ $corteDeVariables++; }
   if(isset($_POST['ruta'])){ $corteDeVariables++; }
   if(isset($_POST['rutaNm'])){ $corteDeVariables++; }
   if(isset($_POST['paginasObservaciones'])){ $paginasObservaciones = $_POST['paginasObservaciones']; $corteDeVariables++; }
   $imprimirMes = 0;
	if(isset($_POST['imprimirMes'])){
		if($_POST['imprimirMes'] == 'on'){ $imprimirMes = 1;	}
		$corteDeVariables++;
  }
  $_SESSION['observacionesDespachos'] = "";
  if(isset($_POST['observaciones'])){
      if($_POST['observaciones'] != ""){
         $_SESSION['observacionesDespachos'] = $_POST['observaciones'];
      }
      $corteDeVariables++;
   }  
   $_POST = array_slice($_POST, $corteDeVariables);
   $_POST = array_values($_POST);
}

$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];

if ($cantGruposEtarios == '3') {
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

   // Creación del objeto de la clase heredada
   $pdf= new PDF('L','mm',array(330,215.9));
   $pdf->SetMargins(8, 6.31, 8);
   $pdf->SetAutoPageBreak(false,5);
   $pdf->AliasNbPages();

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
      $consulta = " SELECT    de.*, 
                              tc.descripcion, 
                              s.nom_sede, 
                              s.nom_inst, 
                              u.Ciudad, 
                              td.Descripcion as tipoDespachoNm, 
                              tc.jornada
                           FROM despachos_enc$mesAnno de
                           LEFT JOIN sedes$anno s on de.cod_sede = s.cod_sede
                           LEFT JOIN ubicacion u on s.cod_mun_sede = u.CodigoDANE
                           LEFT JOIN tipo_complemento tc on de.Tipo_Complem = tc.CODIGO
                           LEFT JOIN tipo_despacho td on de.TipoDespacho = td.Id
                           WHERE de.Num_Doc = $despacho ";

      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
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

      $semanasIn = explode(',',$semana);
      $semanaIn = '';
      foreach ($semanasIn as $key => $value) {
         $semanaIn .= "'".trim($value)."',";
      }
      $semanaIn = trim($semanaIn,','); 
      $consulta = " SELECT * FROM planilla_semanas WHERE SEMANA_DESPACHO IN ($semanaIn) ";
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
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
      $consulta = "SELECT Cobertura_G1 AS grupo1, Cobertura_G2 AS grupo2, Cobertura_G3 AS grupo3 FROM despachos_enc$mesAnno WHERE Num_Doc = '$despacho';";
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
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
      $consulta = " SELECT DISTINCT cod_alimento
                     FROM despachos_det$mesAnno
                     WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho
                     ORDER BY cod_alimento ASC ";

      //CONSULTA LOS CODIGOS DE ALIMENTOS DE ESTE DESPACHO
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
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

         $consulta = "SELECT DISTINCT  p.Codigo,
                                       p.Descripcion AS Componente,
                                       p.nombreunidad2 presentacion,
                                       p.cantidadund1 cantidadPresentacion,
                                       m.grupo_alim, m.orden_grupo_alim, ftd.UnidadMedida,
                                       (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 1 ) as cant_grupo1,
                                       (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 2 ) as cant_grupo2,
                                       (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 3) as cant_grupo3,
                                       (SELECT cantu2 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu2,
                                       (SELECT cantu3 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu3,
                                       (SELECT cantu4 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu4,
                                       (SELECT cantu5 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu5,
                                       (SELECT Umedida FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS Umedida,
                                       (SELECT cantotalpresentacion FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantotalpresentacion,
                                       ( SELECT sum(D1) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D1,
                                       ( SELECT sum(D2) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D2,
                                       ( SELECT sum(D3) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D3,
                                       ( SELECT sum(D4) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D4,
                                       ( SELECT sum(D5) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D5,
                                       ( SELECT sum(D6) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D6,
                                       ( SELECT sum(D7) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D7,
                                       ( SELECT sum(D8) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D8,
                                       ( SELECT sum(D9) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D9,
                                       ( SELECT sum(D10) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D10,
                                       ( SELECT sum(D11) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D11,
                                       ( SELECT sum(D12) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D12,
                                       ( SELECT sum(D13) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D13,
                                       ( SELECT sum(D14) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D14,
                                       ( SELECT sum(D15) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D15,
                                       ( SELECT sum(D16) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D16,
                                       ( SELECT sum(D17) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D17,
                                       ( SELECT sum(D18) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D18,
                                       ( SELECT sum(D19) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D19,
                                       ( SELECT sum(D20) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D20,
                                       ( SELECT sum(D21) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D21,
                                       ( SELECT sum(D22) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D22,
                                       ( SELECT sum(D23) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D23,
                                       ( SELECT sum(D24) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D24,
                                       ( SELECT sum(D25) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D25,
                                       p.cantidadund2,
                                       p.cantidadund3,
                                       p.cantidadund4,
                                       p.cantidadund5,
                                       p.nombreunidad2,
                                       p.nombreunidad3,
                                       p.nombreunidad4,
                                       p.nombreunidad5
                                    FROM productos$anno p
                                    LEFT JOIN fichatecnicadet ftd ON ftd.codigo=p.Codigo
                                    INNER JOIN menu_aportes_calynut m ON p.Codigo = m.cod_prod
                                    WHERE p.Codigo = $auxCodigo
                                    ORDER BY m.orden_grupo_alim ASC, p.Descripcion DESC ";

         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
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
               $alimento['presentacion'] = $row['Umedida'];
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
               $alimento['D1'] = $row['D1'] + $row['D6'] + $row['D11'] + $row['D16'] + $row['D21'];
               $alimento['D2'] = $row['D2'] + $row['D7'] + $row['D12'] + $row['D17'] + $row['D22'];
               $alimento['D3'] = $row['D3'] + $row['D8'] + $row['D13'] + $row['D18'] + $row['D23'];
               $alimento['D4'] = $row['D4'] + $row['D9'] + $row['D14'] + $row['D19'] + $row['D24'];
               $alimento['D5'] = $row['D5'] + $row['D10'] + $row['D15'] + $row['D20'] + $row['D25'];
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
      array_multisort($sort['grupo_alim'], SORT_ASC,$alimentos);
      sort($grupo);
      /*************************************************************/

      $pdf->AddPage();
      $pdf->SetTextColor(0,0,0);
      $pdf->SetFillColor(255, 255, 255);
      $pdf->SetDrawColor(0, 0, 0);
      $pdf->SetLineWidth(.05);
      $pdf->SetFont('Arial','',$tamannoFuente);

      $tamano_carta = FALSE;
      include 'despacho_por_sede_footer.php';
      include 'despacho_kardex3_header.php';
      $grupoAlimActual = '';
    
      for ($i=0; $i < count($alimentos ) ; $i++) {
         $pdf->SetFont('Arial','',$tamannoFuente);
         $alimento = $alimentos[$i];

         // Se toma la distancia en y para determinar si se hace un salto de pagina.
         $current_y = $pdf->GetY();
         if($current_y > 205){
            $pdf->AddPage();
            include 'despacho_por_sede_footer.php';
            include 'despacho_kardex3_header.php';
         }
         $pdf->SetTextColor(0, 0, 0);
         $pdf->SetFillColor(255, 255, 255);

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
               include 'despacho_kardex3_header.php';
            }
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $current_y = $pdf->GetY();
            $current_x = $pdf->GetX();
            $altura = 4 * $filas;
            $pdf->Cell(44.388, $altura, '', 1, 0, 'C', False);
            $aux = $alimento['grupo_alim'];
            $aux = mb_strtoupper($aux, 'UTF-8');
            $long_nombre=strlen($aux);
            if($long_nombre > $largoNombreGrupo){
               $margenSuperiorCelda = ((4 * $filas)/2)-4;
               $altura = 4;
            }
            else{
               $margenSuperiorCelda = 0;
            }
            $pdf->SetXY($current_x, $current_y+$margenSuperiorCelda);
            $pdf->MultiCell(44.388, $altura, utf8_decode($aux), 0, 'C', False);
            $pdf->SetXY($current_x+44.388, $current_y);
         }
         else{
            $pdf->SetX(52.388);
         }

         $aux = $alimento['componente'];
         $long_nombre=strlen($aux);
         $largoNombre =26;
         if($long_nombre > $largoNombre){
            $aux = substr($aux,0,$largoNombre);
         }
         // Impresión de Alimento (Nombre del alimento)
         $pdf->Cell(44, 4, utf8_decode($aux), 1, 0, 'L', False);

         // Unidad de presentación
         $pdf->Cell(13.141, 4, $alimento['presentacion'], 1, 0, 'C', False);

         $cantTotal = $alimento['cant_grupo1'] + $alimento['cant_grupo2'] + $alimento['cant_grupo3'];
         $auxCantotal = $cantTotal;
         if ($alimento['presentacion'] == "u") {
            if (strpos($alimento['componente'], "HUEVO") !== FALSE) {
               $cantTotal = ceil($cantTotal);
            } else {
               $cantTotal = round($cantTotal);
            }
         } 
         else {
            $cantTotal = number_format( $cantTotal, $digitosDecimales);
         }
         // exit(var_dump($cantTotal));
         $aux = $cantTotal;
         //TOTAL REQUERIDO
         $pdf->Cell(17.471,4,$aux,1,0,'C',False);

         if($alimento['cantotalpresentacion'] > 0){
            $aux = 0+$alimento['cantotalpresentacion'];
         }

         // Nueva columna existencias
         $pdf->Cell(18,4,'',1,0,'C',False);
         $banderaResta = 0;

         $alimento['cant_total'] = round_up($alimento['cant_grupo1'], $digitosDecimales) + round_up($alimento['cant_grupo2'], $digitosDecimales) + round_up($alimento['cant_grupo3'], $digitosDecimales);
         $saldo = $auxCantotal;
         for( $k = 1; $k <= 5; $k++ ){
            $consumoDia = 0;
            for( $m = 1; $m <= $k; $m++ ){
               $consumoDia = $consumoDia +  $alimento['D'.$m];
     
            }
            $saldo = $auxCantotal;
            $saldo = $saldo - $consumoDia;
            $consumoDia = number_format($consumoDia, $digitosDecimales);
            // echo "$saldo" . "-" . $consumoDia;
            

            if ($alimento['presentacion'] == "u") {
               $consumoDia = $alimento['D'.$k];
               $consumoDia = number_format($consumoDia, $digitosDecimales);
               $pdf->Cell(15.9,4,$consumoDia,'1',0,'C',False);
            } else {
               $consumoDia = $alimento['D'.$k];
               $consumoDia = number_format($consumoDia, $digitosDecimales);
               $pdf->Cell(15.9,4,$consumoDia,'1',0,'C',False);
            }
            $pdf->Cell(15.9,4,'','1',0,'C',False);
         }
         $pdf->Cell(18,4,'','1',0,'C',False);
         $pdf->Ln(4);
      }//Termina el for de los alimentos

      $current_y = $pdf->GetY();
      if($current_y > 160){
         $pdf->AddPage();
         include 'despacho_por_sede_footer.php';
         include 'despacho_kardex3_header.php';
      }
      include 'despacho_firma_planilla_kardex3.php';
      
      mysqli_close ( $Link );
      $pdf->Output();
   }
}

if ($cantGruposEtarios == '5') {
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

   // Creación del objeto de la clase heredada
   $pdf= new PDF('L','mm',array(330,215.9));
   $pdf->SetMargins(8, 6.31, 8);
   $pdf->SetAutoPageBreak(false,5);
   $pdf->AliasNbPages();

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
      $consulta = " SELECT    de.*, 
                              tc.descripcion, 
                              s.nom_sede, 
                              s.nom_inst, 
                              u.Ciudad, 
                              td.Descripcion as tipoDespachoNm, 
                              tc.jornada
                           FROM despachos_enc$mesAnno de
                           LEFT JOIN sedes$anno s on de.cod_sede = s.cod_sede
                           LEFT JOIN ubicacion u on s.cod_mun_sede = u.CodigoDANE
                           LEFT JOIN tipo_complemento tc on de.Tipo_Complem = tc.CODIGO
                           LEFT JOIN tipo_despacho td on de.TipoDespacho = td.Id
                           WHERE de.Num_Doc = $despacho ";

      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
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

      $semanasIn = explode(',',$semana);
      $semanaIn = '';
      foreach ($semanasIn as $key => $value) {
         $semanaIn .= "'".trim($value)."',";
      }
      $semanaIn = trim($semanaIn,','); 
      $consulta = " SELECT * FROM planilla_semanas WHERE SEMANA_DESPACHO IN ($semanaIn) ";
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
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
      $cantSedeGrupo4 = 0;
      $cantSedeGrupo5 = 0;
      $consulta = "SELECT  Cobertura_G1 AS grupo1, 
                           Cobertura_G2 AS grupo2, 
                           Cobertura_G3 AS grupo3, 
                           Cobertura_G4 AS grupo4, 
                           Cobertura_G5 AS grupo5 
                        FROM despachos_enc$mesAnno 
                        WHERE Num_Doc = '$despacho';";
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
      if($resultado->num_rows >= 1){
         $row = $resultado->fetch_assoc();
         $cantSedeGrupo1 = $row['grupo1'];
         $cantSedeGrupo2 = $row['grupo2'];
         $cantSedeGrupo3 = $row['grupo3'];
         $cantSedeGrupo4 = $row['grupo4'];
         $cantSedeGrupo5 = $row['grupo5'];
      }

      // A medida que se recoja la información de los aliemntos se
      // determianra si todos los grupos etarios fueron beneficiados
      // y usaremos las cantidades de las siguientes variables.

      $sedeGrupo1 = 0;
      $sedeGrupo2 = 0;
      $sedeGrupo3 = 0;
      $sedeGrupo4 = 0;
      $sedeGrupo5 = 0;

      // Se van a buscar los alimentos de este despacho.
      $alimentos = array();
      $consulta = " SELECT DISTINCT cod_alimento
                     FROM despachos_det$mesAnno
                     WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho
                     ORDER BY cod_alimento ASC ";

      //CONSULTA LOS CODIGOS DE ALIMENTOS DE ESTE DESPACHO
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
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

         $consulta = "SELECT DISTINCT  p.Codigo,
                                       p.Descripcion AS Componente,
                                       p.nombreunidad2 presentacion,
                                       p.cantidadund1 cantidadPresentacion,
                                       m.grupo_alim, m.orden_grupo_alim, ftd.UnidadMedida,
                                       (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 1 ) as cant_grupo1,
                                       (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 2 ) as cant_grupo2,
                                       (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 3) as cant_grupo3,
                                       (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 4) as cant_grupo4,
                                       (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 5) as cant_grupo5,
                                       (SELECT cantu2 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu2,
                                       (SELECT cantu3 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu3,
                                       (SELECT cantu4 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu4,
                                       (SELECT cantu5 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu5,
                                       (SELECT Umedida FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS Umedida,
                                       (SELECT cantotalpresentacion FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantotalpresentacion,
                                       ( SELECT sum(D1) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D1,
                                       ( SELECT sum(D2) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D2,
                                       ( SELECT sum(D3) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D3,
                                       ( SELECT sum(D4) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D4,
                                       ( SELECT sum(D5) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D5,
                                       ( SELECT sum(D6) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D6,
                                       ( SELECT sum(D7) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D7,
                                       ( SELECT sum(D8) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D8,
                                       ( SELECT sum(D9) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D9,
                                       ( SELECT sum(D10) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D10,
                                       ( SELECT sum(D11) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D11,
                                       ( SELECT sum(D12) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D12,
                                       ( SELECT sum(D13) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D13,
                                       ( SELECT sum(D14) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D14,
                                       ( SELECT sum(D15) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D15,
                                       ( SELECT sum(D16) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D16,
                                       ( SELECT sum(D17) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D17,
                                       ( SELECT sum(D18) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D18,
                                       ( SELECT sum(D19) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D19,
                                       ( SELECT sum(D20) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D20,
                                       ( SELECT sum(D21) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D21,
                                       ( SELECT sum(D22) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D22,
                                       ( SELECT sum(D23) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D23,
                                       ( SELECT sum(D24) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D24,
                                       ( SELECT sum(D25) FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho AND cod_Alimento = $auxCodigo ) AS D25,
                                       p.cantidadund2,
                                       p.cantidadund3,
                                       p.cantidadund4,
                                       p.cantidadund5,
                                       p.nombreunidad2,
                                       p.nombreunidad3,
                                       p.nombreunidad4,
                                       p.nombreunidad5
                                    FROM productos$anno p
                                    LEFT JOIN fichatecnicadet ftd ON ftd.codigo=p.Codigo
                                    INNER JOIN menu_aportes_calynut m ON p.Codigo = m.cod_prod
                                    WHERE p.Codigo = $auxCodigo
                                    ORDER BY m.orden_grupo_alim ASC, p.Descripcion DESC ";

         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. $consulta . mysqli_error($Link));
         $alimento['componente'] = '';
         $alimento['presentacion'] = '';
         $alimento['grupo_alim'] = '';
         $alimento['cant_grupo1'] = 0;
         $alimento['cant_grupo2'] = 0;
         $alimento['cant_grupo3'] = 0;
         $alimento['cant_grupo4'] = 0;
         $alimento['cant_grupo5'] = 0;
         $alimento['cant_total'] = 0;

         if($resultado->num_rows >= 1){
            while($row = $resultado->fetch_assoc()){
               $alimento['componente'] = $row['Componente'];
               $alimento['presentacion'] = $row['Umedida'];
               $alimento['cantidadpresentacion'] = $row['cantidadPresentacion'];
               $alimento['grupo_alim'] = $row['grupo_alim'];
               $alimento['orden_grupo_alim'] = $row['orden_grupo_alim'];
               $alimento['cant_grupo1'] = $row['cant_grupo1'];
               $alimento['cant_grupo2'] = $row['cant_grupo2'];
               $alimento['cant_grupo3'] = $row['cant_grupo3'];
               $alimento['cant_grupo4'] = $row['cant_grupo4'];
               $alimento['cant_grupo5'] = $row['cant_grupo5'];
               $alimento['cant_total'] = $row['cant_grupo1'] + $row['cant_grupo2'] + $row['cant_grupo3'] + $row['cant_grupo4'] + $row['cant_grupo5'];
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
               $alimento['D1'] = $row['D1'] + $row['D6'] + $row['D11'] + $row['D16'] + $row['D21'];
               $alimento['D2'] = $row['D2'] + $row['D7'] + $row['D12'] + $row['D17'] + $row['D22'];
               $alimento['D3'] = $row['D3'] + $row['D8'] + $row['D13'] + $row['D18'] + $row['D23'];
               $alimento['D4'] = $row['D4'] + $row['D9'] + $row['D14'] + $row['D19'] + $row['D24'];
               $alimento['D5'] = $row['D5'] + $row['D10'] + $row['D15'] + $row['D20'] + $row['D25'];
               if($row['cant_grupo1'] > 0){  
                  $sedeGrupo1 = $cantSedeGrupo1;
               }
               if($row['cant_grupo2'] > 0){
                  $sedeGrupo2 = $cantSedeGrupo2;
               }
               if($row['cant_grupo3'] > 0){
                  $sedeGrupo3 = $cantSedeGrupo3;
               }
               if($row['cant_grupo4'] > 0){
                  $sedeGrupo4 = $cantSedeGrupo4;
               }
               if($row['cant_grupo5'] > 0){
                  $sedeGrupo5 = $cantSedeGrupo5;
               }
            }
         }
         $alimentos[$i] = $alimento;
      }

      /*************************************************************/
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
      array_multisort($sort['grupo_alim'], SORT_ASC,$alimentos);
      sort($grupo);
      /*************************************************************/

      $pdf->AddPage();
      $pdf->SetTextColor(0,0,0);
      $pdf->SetFillColor(255, 255, 255);
      $pdf->SetDrawColor(0, 0, 0);
      $pdf->SetLineWidth(.05);
      $pdf->SetFont('Arial','',$tamannoFuente);

      $tamano_carta = FALSE;
      include 'despacho_por_sede_footer.php';
      include 'despacho_kardex3_header.php';
      $grupoAlimActual = '';
    
      for ($i=0; $i < count($alimentos ) ; $i++) {
         $pdf->SetFont('Arial','',$tamannoFuente);
         $alimento = $alimentos[$i];

         // Se toma la distancia en y para determinar si se hace un salto de pagina.
         $current_y = $pdf->GetY();
         if($current_y > 205){
            $pdf->AddPage();
            include 'despacho_por_sede_footer.php';
            include 'despacho_kardex3_header.php';
         }
         $pdf->SetTextColor(0, 0, 0);
         $pdf->SetFillColor(255, 255, 255);

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
               include 'despacho_kardex3_header.php';
            }
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $current_y = $pdf->GetY();
            $current_x = $pdf->GetX();
            $altura = 4 * $filas;
            $pdf->Cell(44.388, $altura, '', 1, 0, 'C', False);
            $aux = $alimento['grupo_alim'];
            $aux = mb_strtoupper($aux, 'UTF-8');
            $long_nombre=strlen($aux);
            if($long_nombre > $largoNombreGrupo){
               $margenSuperiorCelda = ((4 * $filas)/2)-4;
               $altura = 4;
            }
            else{
               $margenSuperiorCelda = 0;
            }
            $pdf->SetXY($current_x, $current_y+$margenSuperiorCelda);
            $pdf->MultiCell(44.388, $altura, utf8_decode($aux), 0, 'C', False);
            $pdf->SetXY($current_x+44.388, $current_y);
         }
         else{
            $pdf->SetX(52.388);
         }

         $aux = $alimento['componente'];
         $long_nombre=strlen($aux);
         $largoNombre =26;
         if($long_nombre > $largoNombre){
            $aux = substr($aux,0,$largoNombre);
         }
         // Impresión de Alimento (Nombre del alimento)
         $pdf->Cell(44, 4, utf8_decode($aux), 1, 0, 'L', False);

         // Unidad de presentación
         $pdf->Cell(13.141, 4, $alimento['presentacion'], 1, 0, 'C', False);

         $cantTotal = $alimento['cant_grupo1'] + $alimento['cant_grupo2'] + $alimento['cant_grupo3'] + $alimento['cant_grupo4'] + $alimento['cant_grupo5'];
         $auxCantotal = $cantTotal;
         if ($alimento['presentacion'] == "u") {
            if (strpos($alimento['componente'], "HUEVO") !== FALSE) {
               $cantTotal = ceil($cantTotal);
            } else {
               $cantTotal = round($cantTotal);
            }
         } 
         else {
            $cantTotal = number_format( $cantTotal, $digitosDecimales);
         }
         // exit(var_dump($cantTotal));
         $aux = $cantTotal;
         //TOTAL REQUERIDO
         $pdf->Cell(17.471,4,$aux,1,0,'C',False);

         if($alimento['cantotalpresentacion'] > 0){
            $aux = 0+$alimento['cantotalpresentacion'];
         }

         // Nueva columna existencias
         $pdf->Cell(18,4,'',1,0,'C',False);
         $banderaResta = 0;

         $alimento['cant_total'] = round_up($alimento['cant_grupo1'], $digitosDecimales) + round_up($alimento['cant_grupo2'], $digitosDecimales) + round_up($alimento['cant_grupo3'], $digitosDecimales) + round_up($alimento['cant_grupo4'], $digitosDecimales) + round_up($alimento['cant_grupo5'], $digitosDecimales) ;
         $saldo = $auxCantotal;
         for( $k = 1; $k <= 5; $k++ ){
            $consumoDia = 0;
            for( $m = 1; $m <= $k; $m++ ){
               $consumoDia = $consumoDia +  $alimento['D'.$m];
            }
            $saldo = $auxCantotal;
            $saldo = $saldo - $consumoDia;
            $consumoDia = number_format($consumoDia, $digitosDecimales);
            if ($alimento['presentacion'] == "u") {
               $consumoDia = $alimento['D'.$k];
               $consumoDia = number_format($consumoDia, $digitosDecimales);
               $pdf->Cell(15.9,4,$consumoDia,'1',0,'C',False);
            } else {
               $consumoDia = $alimento['D'.$k];
               $consumoDia = number_format($consumoDia, $digitosDecimales);
               $pdf->Cell(15.9,4,$consumoDia,'1',0,'C',False);
            }
            $pdf->Cell(15.9,4,'','1',0,'C',False);
         }
         $pdf->Cell(18,4,'','1',0,'C',False);
         $pdf->Ln(4);
      }//Termina el for de los alimentos

      $current_y = $pdf->GetY();
      if($current_y > 160){
         $pdf->AddPage();
         include 'despacho_por_sede_footer.php';
         include 'despacho_kardex3_header.php';
      }
      include 'despacho_firma_planilla_kardex3.php';

   }    
   mysqli_close ( $Link );
   $pdf->Output();
}
