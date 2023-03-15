<?php
include '../../config.php';
require_once '../../autentication.php';
require('../../fpdf181/fpdf.php');
require_once '../../db/conexion.php';
// exit(var_dump($_POST));
set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

$ciclo = '';
$mesAnno = '';
$sangria = " * ";
$largoNombre = 30;
$tamannoFuente = 5;
$totalBeneficiarios = 0;
$paginasObservaciones = 1;

if (isset($_POST['despachoAnnoI']) && isset($_POST['despachoMesI']) && isset($_POST['despacho'])) {
	$mes = $_POST['despachoMesI'];
	if($mes < 10){
	  $mes = '0'.$mes;
	}
	if(isset($_POST['paginasObservaciones'])){
		$paginasObservaciones = $_POST['paginasObservaciones'];
	}
	$mes = trim($mes);
	$anno = $_POST['despachoAnnoI'];
	$anno = substr($anno, -2);
	$anno = trim($anno);
	$mesAnno = $mes.$anno;

	$_SESSION['observacionesDespachos'] = "";
	if(isset($_POST['observaciones'])){
		if($_POST['observaciones'] != ""){
			$_SESSION['observacionesDespachos'] = $_POST['observaciones'];
		}
	}

	$_POST = array_slice($_POST, 3);
	$_POST = array_values($_POST);
} else {
	$mes = $_POST['mesiConsulta'];
	if($mes < 10){
	  $mes = '0'.$mes;
	}
	$mes = trim($mes);
	$anno = $_POST['annoi'];
	$anno = substr($anno, -2);
	$anno = trim($anno);
	$mesAnno = $mes.$anno;
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
	if(isset($_POST['paginasObservaciones'])){
		$paginasObservaciones = $_POST['paginasObservaciones'];
		$corteDeVariables++;
	}
	$imprimirMes = 0;
	if(isset($_POST['imprimirMes'])){
		if($_POST['imprimirMes'] == 'on'){
			$imprimirMes = 1;	
		}
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
		if($angle!=0){
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
		}
	}

	function Rotate_text($x, $y, $txt, $angle){
	  $this->Rotate($angle, $x, $y);
	  $this->Text($x, $y, $txt);
	  $this->Rotate(0);
	}
}

$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];

// partimos la funcion para siga funcionando con tres grupos etarios y tambien pueda funcionar con 5 grupos etarios
if ($cantGruposEtarios == 3) {
	//CREACION DEL PDF
	// Creación del objeto de la clase heredada
	$pdf= new PDF('L','mm',array(279.4, 215.9));
	$pdf->SetMargins(8, 6.31, 8);
	$pdf->SetAutoPageBreak(FALSE, 5); 
	$pdf->AliasNbPages();
	include '../../php/funciones.php';

	for ($k=0; $k < count($_POST) ; $k++){ 
  		// Borrando variables array para usarlas en cada uno de los despachos
  		unset($sedes);
  		unset($items);
  		unset($menus);
  		unset($sedesCobertura);
  		unset($complementosCantidades);
  		$ciclo = '';
  		$claves = array_keys($_POST);
  		$aux = $claves[$k];
  		$despacho = $_POST[$aux];
  		$consulta = "	SELECT de.*, 
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

	  	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	  	if($resultado->num_rows >= 1){
			$row = $resultado->fetch_assoc();
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
	  	}

	  	$semanasIn = explode(',',$semana);
	  	$semanaIn = '';
	  	foreach ($semanasIn as $key => $value) {
	  		$semanaIn .= "'".trim($value)."',";
	  	}
	  	$semanaIn = trim($semanaIn,','); 

	 	// Iniciando la busqueda de los días que corresponden a esta semana de contrato.
	 	$arrayDiasDespacho = explode(',', $diasDespacho);
	 	$dias = '';
	 	$consulta = " SELECT * FROM planilla_semanas WHERE SEMANA_DESPACHO IN ($semanaIn) "; 
	 	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	 	$cantDias = $resultado->num_rows;
	 	if($resultado->num_rows >= 1){
	   	$mesInicial = '';
	   	$mesesIniciales = 0;
	   	$bandera = 0;
	   	while($row = $resultado->fetch_assoc()){
		 		$clave = array_search(intval($row['DIA']), $arrayDiasDespacho);
		 		if($clave !== false){
		 			if($bandera != $row['CICLO']){
		 				$ciclo .= $row['CICLO'] .', ';
		 				$bandera = $row['CICLO'];
		 			}
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
	  		$ciclo = trim($ciclo, ', ');
	   	$dias .= " de  $mes";
	 	}else {
	 		$dias = $diasDespacho;
	 		$nombreTabla = 'despachos_enc'.$mesAnno;
	 		$mesTabla = substr($nombreTabla,13,-2); 
	 		$arrayMeseNombre = ["01" => "ENERO", "02" => "FEBRERO", "03" => "MARZO", "04" => "ABRIL", "05" => "MAYO", "06" => "JUNIO", "07" => "JULIO", "08" => "AGOSTO", "09" => "SEPTIEMBRE", "10" => "OCTUBRE", "11" => "NOVIEMBRE", "12" => "DICIEMBRE"];
	 		$dias .= " DE " . $arrayMeseNombre[$mesTabla];
	 	}

  		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  		// Modificación para cambiar los datos de los 3 campos agregados a la tabla despachos_encMESAÑO
  		// Bucando la cobertura para la sede en esa semana para el tipo de complementosCantidades
  		$cantSedeGrupo1 = 0;
  		$cantSedeGrupo2 = 0;
  		$cantSedeGrupo3 = 0;
  		$consulta = "SELECT Cobertura_G1, Cobertura_G2, Cobertura_G3 FROM despachos_enc$mesAnno WHERE Num_doc = '$despacho';";
  		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  		if($resultado->num_rows >= 1){
			$row = $resultado->fetch_assoc();
			$cantSedeGrupo1 = $row['Cobertura_G1'];
			$cantSedeGrupo2 = $row['Cobertura_G2'];
			$cantSedeGrupo3 = $row['Cobertura_G3'];
  		}
  		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  		// A medida que se recoja la información de los aliemntos se determianra si todos los grupos etarios fueron beneficiados y usaremos las cantidades de las siguientes variables.
  		$sedeGrupo1 = 0;
  		$sedeGrupo2 = 0;
  		$sedeGrupo3 = 0;

  		// Se van a buscar los alimentos de este despacho.
  		$alimentos = array();
  		$consulta = " 	SELECT DISTINCT cod_alimento
  							FROM despachos_det$mesAnno
  							WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho
  							ORDER BY cod_alimento ASC ";

  		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  		if($resultado->num_rows >= 1){
			while($row = $resultado->fetch_assoc()){
	  			$alimento = array();
	  			$alimento['codigo'] = $row['cod_alimento'];
	  			$alimentos[] = $alimento;
			}
  		}

  		for ($i=0; $i < count($alimentos) ; $i++) {
			$auxCodigo = $alimentos[$i]['codigo'];
			$consulta = " SELECT distinct p.Codigo,
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
										(SELECT Redondeo FROM tipo_despacho WHERE Id = p.TipoDespacho) AS redondeo,
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
			
			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			if($resultado->num_rows >= 1){
	  			$alimento['cant_total'] = 0;
	  			$alimento['cant_grupo1'] = 0;
	  			$alimento['cant_grupo2'] = 0;
	  			$alimento['cant_grupo3'] = 0;
	  			$alimento['grupo_alim'] = '';
	  			$alimento['componente'] = '';
	  			$alimento['presentacion'] = '';
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
					$alimento['redondeo'] = $row['redondeo'];
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

		/*************************************************************/
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
		array_multisort($sort['grupo_alim'], SORT_ASC,$alimentos);
		sort($grupo);
		/*************************************************************/

  		$pdf->AddPage();
  		$pdf->SetTextColor(0, 0, 0);
  		$pdf->SetFillColor(255, 255, 255);
  		$pdf->SetDrawColor(0, 0, 0);
  		$pdf->SetLineWidth(.05);
  		$pdf->SetFont('Arial', '', $tamannoFuente);

  		// Primer Header
  		$tamano_carta = TRUE;
  		include 'despacho_por_sede_footer.php';
  		include 'despacho_por_sede_header.php';

  		$filas = 0;
  		$grupoAlimActual = '';
  		for ($i=0; $i < count($alimentos ) ; $i++) {
			$pdf->SetFont('Arial', '', $tamannoFuente);
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

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFillColor(255, 255, 255);

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
		  				if($aux['cantu2'] > 0 || $aux['cantu3'] > 0 || $aux['cantu4'] > 0 || $aux['cantu5'] > 0){ }
					}
					//Termina la busqueda de las filas adicionales debido a presetaciones,

					if(($current_y + (4*$filas)) > 177){
			  			$pdf->AddPage();
			  			include 'despacho_por_sede_footer.php';
			  			include 'despacho_por_sede_header.php';
			  			$pdf->SetFont('Arial', '', $tamannoFuente);
					}
					
					$current_y = $pdf->GetY();
					$current_x = $pdf->GetX();
					$altura = 4 * $filas;
					$pdf->Cell($anchoCelda, $altura, '', 1, 0, 'C', False);
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
					if( $long_nombre <= 10 && $filas > 2 ){
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
					$pdf->MultiCell(23.788, $altura, utf8_decode($aux), 0, 'C', False);
					$pdf->SetXY($current_x + $anchoCelda, $current_y);
				}
				else{
			 		$pdf->SetX($current_x+$anchoCelda);
				}
				// Termina la impresión de grupo alimenticio
			
				if(1==1){
					$aux = $alimento['componente'];
					$largoNombre = 37;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
						$aux = substr($aux, 0, $largoNombre);
					}

		  			//Alimento
		  			$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);
					if($alimento['presentacion'] == 'u'){
		  				$aux = number_format($alimento['cant_grupo1'], 2, '.', '');
					}else{
		  				$aux = 0+$alimento['cant_grupo1'];
		  				$aux = number_format($aux, 2, '.', '');
					}
					if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
					$pdf->Cell(13.1, 4, utf8_decode($aux), 1, 0, 'C', False);
					if($alimento['presentacion'] == 'u'){
		  				$aux = number_format($alimento['cant_grupo2'], 2, '.', '');
					}else{
		  				$aux = 0+$alimento['cant_grupo2'];
		  				$aux = number_format($aux, 2, '.', '');
					}
					if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
					$pdf->Cell(13.1, 4, utf8_decode($aux), 1, 0, 'C', False);
					if($alimento['presentacion'] == 'u'){
		  				$aux = number_format($alimento['cant_grupo3'], 2, '.', '');
					}else{
		  				$aux = 0 + $alimento['cant_grupo3'];
		  				$aux = number_format($aux, 2, '.', '');
					}
					if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
					$pdf->Cell(13.1, 4, utf8_decode($aux), 1, 0, 'C', False);

					//UNIDAD DE MEDIDA
					$aux = $alimento['presentacion'];
					$pdf->Cell(13.141, 4, $aux, 1, 0, 'C', False);

					if ($alimento['presentacion'] == 'u') {
						$aux = number_format($alimento['cant_total'], 2, '.', '');
					} else {
						$aux = number_format($alimento['cant_total'], 2, '.', '');
					}

					$pdf->Cell(13.141, 4, $aux, 1, 0, 'C', False);
					if($alimento['cantotalpresentacion'] > 0){
						$aux = 0+$alimento['cantotalpresentacion'];
						$aux = number_format($aux, 2, '.', '');
					}

					$aux = number_format($alimento['cant_total'], 2, '.', '');
					if ($alimento['redondeo'] == 1) {
						if ($aux <= 0.5) {
							$aux = ceil($aux);
						}else if ($aux > 0.5) {
							$aux = round($aux,0);
						}	
					}

					if ($alimento['presentacion'] == 'u') {
	  					if (strpos($alimento['componente'], "HUEVO") !== FALSE) {
							$aux = ceil(0+$aux);
	  					} else {
							$aux = round(0+$aux);
	  					}
					}
					$aux = number_format($aux, 2, '.', '');

					// CANTIDAD ENTREGADA TOTAL
					if( $alimento['cantu2'] > 0 || $alimento['cantu3'] > 0 || $alimento['cantu4'] > 0 || $alimento['cantu5'] > 0 ){ $aux = ""; }
					$pdf->Cell(10.7, 4, $aux, 1, 0, 'C', False);
		
					//total entregado
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					// ESPECIFICACIÓN DE CALIDAD
					$pdf->Cell(13.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.7, 4, '', 1, 0, 'C', False);
					// FALTANTES
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8.9, 4, '', 1, 0, 'C', False);
					$pdf->Cell(14, 4, '', 1, 0, 'C', False);
					//DEVOLUCIÓN
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(9.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(15.4, 4, '', 1, 0, 'C', False);
					$pdf->Ln(4);
				}//Termina el if que validad si hay cantidades en las unidades con el fin de ocultar la fila inicial.

				$pdf->SetFont('Arial','I',$tamannoFuente);
				$unidad = 2;
				if($alimento['cantu'.$unidad] > 0){
					$pdf->SetX($current_x+$anchoCelda);
					$presentacion = " ".$alimento['nombreunidad'.$unidad];
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);
					$aux = $sangria.$alimento['componente'].$presentacion;
					$largoNombre = 30;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
				  		$aux = substr($aux,0,$largoNombre);
					}
					$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.141, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.141, 4, '', 1, 0, 'C', False);
					$aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
					// CANTIDAD ENTREGADA
					$pdf->Cell(10.7, 4, $aux, 1, 0, 'C', False);
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					// ESPECIFICACIÓN DE CALIDAD
					$pdf->Cell(13.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.7, 4, '', 1, 0, 'C', False);
					// FALTANTES
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8.9, 4, '', 1, 0, 'C', False);
					$pdf->Cell(14, 4, '', 1, 0, 'C', False);
					//DEVOLUCIÓN
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(9.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(15.4, 4, '', 1, 0, 'C', False);
					$pdf->Ln(4);
				}

				$unidad = 3;
				if($alimento['cantu'.$unidad] > 0){
					$pdf->SetX($current_x+$anchoCelda);
					$presentacion = " ".$alimento['nombreunidad'.$unidad];
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);
					$aux = $sangria.$alimento['componente'].$presentacion;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
				  		$aux = substr($aux,0,$largoNombre);
					}
					$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.141, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.141, 4, '', 1, 0, 'C', False);
					$aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
					// CANTIDAD ENTREGADA
					$pdf->Cell(10.7, 4, $aux, 1, 0, 'C', False);
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					// ESPECIFICACIÓN DE CALIDAD
					$pdf->Cell(13.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.7, 4, '', 1, 0, 'C', False);
					// FALTANTES	
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8.9, 4, '', 1, 0, 'C', False);
					$pdf->Cell(14, 4, '', 1, 0, 'C', False);
					//DEVOLUCIÓN
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(9.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(15.4, 4, '', 1, 0, 'C', False);
					$pdf->Ln(4);
				}

				$unidad = 4;
				if($alimento['cantu'.$unidad] > 0){
					$pdf->SetX($current_x+$anchoCelda);
					$presentacion = " ".$alimento['nombreunidad'.$unidad];
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);
					$aux = $sangria.$alimento['componente'].$presentacion;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
				  		$aux = substr($aux, 0, $largoNombre);
					}
					$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.141, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.141, 4, '', 1, 0, 'C', False);
					$aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
					// CANTIDAD ENTREGADA
					$pdf->Cell(10.7, 4, $aux, 1, 0, 'C', False);
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					// ESPECIFICACIÓN DE CALIDAD
					$pdf->Cell(13.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.7, 4, '', 1, 0, 'C', False);
					// FALTANTES
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8.9, 4, '', 1, 0, 'C', False);
					$pdf->Cell(14, 4, '', 1, 0, 'C', False);
					//DEVOLUCIÓN
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(9.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(15.4, 4, '', 1, 0, 'C', False);
					$pdf->Ln(4);
				}

				$unidad = 5;
				if($alimento['cantu'.$unidad] > 0){
					$pdf->SetX($current_x+$anchoCelda);
					$presentacion = " ".$alimento['nombreunidad'.$unidad];
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);
					$aux = $sangria.$alimento['componente'].$presentacion;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
				  		$aux = substr($aux, 0, $largoNombre);
					}
					$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.141, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.141, 4, '', 1, 0, 'C', False);
					$aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
					// CANTIDAD ENTREGADA
					$pdf->Cell(10.7, 4, $aux, 1, 0, 'C', False);
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(10.6, 4, '', 1, 0, 'C', False);
					// ESPECIFICACIÓN DE CALIDAD
					$pdf->Cell(13.6, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13.7, 4, '', 1, 0, 'C', False);
					// FALTANTES
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8.9, 4, '', 1, 0, 'C', False);
					$pdf->Cell(14, 4, '', 1, 0, 'C', False);
					//DEVOLUCIÓN
					$pdf->Cell(9.3, 4, '', 1, 0, 'C', False);
					$pdf->Cell(9.1, 4, '', 1, 0, 'C', False);
					$pdf->Cell(15.4, 4, '', 1, 0, 'C', False);
					$pdf->Ln(4);
				}
				$pdf->SetFont('Arial','',$tamannoFuente);
			}	//Termina el for de los alimentos
	  	}
	  	$current_y = $pdf->GetY();
  		if($current_y > 160){
			$filas = 0;
			$pdf->AddPage();
			include 'despacho_por_sede_footer.php';
			include 'despacho_por_sede_header.php';
  		}
  		include 'despacho_firma_planilla.php';
	}
	mysqli_close ( $Link );
	$pdf->Output();
}

// creacion de pdf cuando son 5 grupos etarios
if ($cantGruposEtarios == 5) {
	//CREACION DEL PDF
	// Creación del objeto de la clase heredada
	$pdf= new PDF('L','mm',array(279.4, 215.9));
	$pdf->SetMargins(8, 6.31, 8);
	$pdf->SetAutoPageBreak(FALSE, 5); 
	$pdf->AliasNbPages();
	include '../../php/funciones.php';
	// exit(var_dump($_POST));
	for ($k=0; $k < count($_POST) ; $k++){ 
  		// Borrando variables array para usarlas en cada uno de los despachos
  		unset($sedes);
  		unset($items);
  		unset($menus);
  		unset($sedesCobertura);
  		unset($complementosCantidades);
  		$ciclo = '';
  		$claves = array_keys($_POST);
  		$aux = $claves[$k];
  		$despacho = $_POST[$aux];
  		$consulta = "	SELECT de.*, 
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

	  	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	  	if($resultado->num_rows >= 1){
			$row = $resultado->fetch_assoc();
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
	  	}

	  	$semanasIn = explode(',',$semana);
	  	$semanaIn = '';
	  	foreach ($semanasIn as $key => $value) {
	  		$semanaIn .= "'".trim($value)."',";
	  	}
	  	$semanaIn = trim($semanaIn,','); 

	 	// Iniciando la busqueda de los días que corresponden a esta semana de contrato.
	  	$arrayDiasDespacho2 = explode(',', $diasDespacho);
	  	$arraySemana = explode(',', $semana);

	  	$concatConsecutivo = '';
	  	$consecutivoActual = 0;
	  	foreach ($arraySemana as $arraySemanaKey => $arraySemanaValue) {
	  		foreach ($arrayDiasDespacho2 as $arrayDiasDespachoKey => $arrayDiasDespachoValue) {
	  			$consultaConsecutivo = "SELECT CONSECUTIVO FROM planilla_semanas WHERE SEMANA_DESPACHO = '" .$arraySemanaValue. "' AND DIA = '" .$arrayDiasDespachoValue. "'";
	 			$respuestaConsecutivo = $Link->query($consultaConsecutivo) or die ('Error al consultar los consetivo LN 825' . mysqli_error());
	 			if ($respuestaConsecutivo->num_rows > 0 ) {
	 				$dataConsetivo = $respuestaConsecutivo->fetch_assoc();
	 				if ($dataConsetivo['CONSECUTIVO'] > $consecutivoActual) {
	 					unset($arrayDiasDespacho2[$arrayDiasDespachoKey]);
	 					$concatConsecutivo .= "'" .$dataConsetivo['CONSECUTIVO']. "',";
	 					$consecutivoActual = $dataConsetivo['CONSECUTIVO'];
	 				}
	 			}
	  		}
	  	}
	  	$concatConsecutivo = trim($concatConsecutivo, ',');

	  	$arrayDiasDespacho = explode(',', $diasDespacho);
	 	$dias = ''; 
	 	$consulta = " SELECT * FROM planilla_semanas WHERE SEMANA_DESPACHO IN ($semanaIn) AND CONSECUTIVO IN ( $concatConsecutivo ) ";  
		// exit(var_dump($consulta));
	 	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	 	$cantDias = $resultado->num_rows;
	 	if($resultado->num_rows >= 1){
	   	$mesInicial = '';
	   	$mesesIniciales = 0;
	   	$bandera = 0;
	   	while($row = $resultado->fetch_assoc()){
		 	$clave = array_search(intval($row['DIA']), $arrayDiasDespacho);
		 	if($clave !== false){
		 		$key = array_search($row['DIA'] , $arrayDiasDespacho); 
		 		unset($arrayDiasDespacho[$key]);
		 		if($bandera != $row['CICLO']){
		 			$ciclo .= $row['CICLO'] .', ';
		 			$bandera = $row['CICLO'];
		 		}
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
	  		$ciclo = trim($ciclo, ', ');
	   	$dias .= " de  $mes";
	 	}else {
	 		$dias = $diasDespacho;
	 		$nombreTabla = 'despachos_enc'.$mesAnno;
	 		$mesTabla = substr($nombreTabla,13,-2); 
	 		$arrayMeseNombre = ["01" => "ENERO", "02" => "FEBRERO", "03" => "MARZO", "04" => "ABRIL", "05" => "MAYO", "06" => "JUNIO", "07" => "JULIO", "08" => "AGOSTO", "09" => "SEPTIEMBRE", "10" => "OCTUBRE", "11" => "NOVIEMBRE", "12" => "DICIEMBRE"];
	 		$dias .= " DE " . $arrayMeseNombre[$mesTabla];
	 	}

  		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  		// Modificación para cambiar los datos de los 3 campos agregados a la tabla despachos_encMESAÑO
  		// Bucando la cobertura para la sede en esa semana para el tipo de complementosCantidades
  		$cantSedeGrupo1 = 0;
  		$cantSedeGrupo2 = 0;
  		$cantSedeGrupo3 = 0;
  		$cantSedeGrupo4 = 0;
  		$cantSedeGrupo5 = 0;
  		$consulta = "SELECT Cobertura_G1, Cobertura_G2, Cobertura_G3, Cobertura_G4, Cobertura_G5 FROM despachos_enc$mesAnno WHERE Num_doc = '$despacho';";
  		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  		if($resultado->num_rows >= 1){
			$row = $resultado->fetch_assoc();
			$cantSedeGrupo1 = $row['Cobertura_G1'];
			$cantSedeGrupo2 = $row['Cobertura_G2'];
			$cantSedeGrupo3 = $row['Cobertura_G3'];
			$cantSedeGrupo4 = $row['Cobertura_G4'];
			$cantSedeGrupo5 = $row['Cobertura_G5'];
  		}
  		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  		// A medida que se recoja la información de los aliemntos se determianra si todos los grupos etarios fueron beneficiados y usaremos las cantidades de las siguientes variables.
  		$sedeGrupo1 = 0;
  		$sedeGrupo2 = 0;
  		$sedeGrupo3 = 0;
  		$sedeGrupo4 = 0;
  		$sedeGrupo5 = 0;

  		// Se van a buscar los alimentos de este despacho.
  		$alimentos = array();
  		$consulta = " 	SELECT DISTINCT cod_alimento
  							FROM despachos_det$mesAnno
  							WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho
  							ORDER BY cod_alimento ASC ";

  		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  		if($resultado->num_rows >= 1){
			while($row = $resultado->fetch_assoc()){
	  			$alimento = array();
	  			$alimento['codigo'] = $row['cod_alimento'];
	  			$alimentos[] = $alimento;
			}
  		}

  		for ($i=0; $i < count($alimentos) ; $i++) {
			$auxCodigo = $alimentos[$i]['codigo'];
			$consulta = " SELECT distinct p.Codigo,
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
										(SELECT Redondeo FROM tipo_despacho WHERE Id = p.TipoDespacho) AS redondeo,
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

			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			if($resultado->num_rows >= 1){
	  			$alimento['cant_total'] = 0;
	  			$alimento['cant_grupo1'] = 0;
	  			$alimento['cant_grupo2'] = 0;
	  			$alimento['cant_grupo3'] = 0;
	  			$alimento['cant_grupo4'] = 0;
	  			$alimento['cant_grupo5'] = 0;
	  			$alimento['grupo_alim'] = '';
	  			$alimento['componente'] = '';
	  			$alimento['presentacion'] = '';
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
					$alimento['redondeo'] = $row['redondeo'];
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

		foreach($alimentos as $kOrden=>$vOrden) {
			$sort['componente'][$kOrden] = $vOrden['componente'];
			$sort['grupo_alim'][$kOrden] = $vOrden['orden_grupo_alim']; //Se cambia el orden de acuerdo al orden por grupo de alimento
			$sort['cantidadpresentacion'][$kOrden] = $vOrden['cantidadpresentacion'];
			$grupo[$kOrden] = $vOrden['grupo_alim'];
		}
		array_multisort($sort['grupo_alim'], SORT_ASC,$alimentos);
		sort($grupo);
		/*************************************************************/
		// exit(var_dump($alimentos));
  		$pdf->AddPage();
  		$pdf->SetTextColor(0, 0, 0);
  		$pdf->SetFillColor(255, 255, 255);
  		$pdf->SetDrawColor(0, 0, 0);
  		$pdf->SetLineWidth(.05);
  		$pdf->SetFont('Arial', '', $tamannoFuente);

  		// Primer Header
  		$tamano_carta = TRUE;
  		include 'despacho_por_sede_footer.php';
  		include 'despacho_por_sede_header.php';

  		$filas = 0;
  		$grupoAlimActual = '';
		// exit(var_dump($alimentos));
  		for ($i=0; $i < count($alimentos ) ; $i++) {
			$pdf->SetFont('Arial', '', $tamannoFuente);
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

				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFillColor(255, 255, 255);

				//Grupo Alimenticio
				$largoNombreGrupo = 25;
	 			$caracteresPorLinea = 10;
				$anchoCelda = 23.788;
				if($alimento['grupo_alim'] != $grupoAlimActual){
					$grupoAlimActual = $alimento['grupo_alim'];
					$filas = array_count_values($grupo)[$grupoAlimActual];
					// echo $filas;
					$cantAlimentosGrupo = $filas;
					// exit(var_dump($alimentos));
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
		  				if($aux['cantu2'] > 0 || $aux['cantu3'] > 0 || $aux['cantu4'] > 0 || $aux['cantu5'] > 0){ }
					}
					//Termina la busqueda de las filas adicionales debido a presetaciones,
					// echo $current_y + (4*$filas) ."<BR>";	
					// echo + (4*$filas). "<BR>";
					if(($current_y + (4*$filas)) > 170){
			  			$pdf->AddPage();
			  			include 'despacho_por_sede_footer.php';
			  			include 'despacho_por_sede_header.php';
			  			$pdf->SetFont('Arial', '', $tamannoFuente);
					}
					
					$current_y = $pdf->GetY();
					$current_x = $pdf->GetX();
					$altura = 4 * $filas;
					$pdf->Cell($anchoCelda, $altura, '', 1, 0, 'C', False);
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
					if( $long_nombre <= 10 && $filas > 2 ){
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
					$pdf->MultiCell(23.788, $altura, utf8_decode($aux), 0, 'C', False);
					$pdf->SetXY($current_x + $anchoCelda, $current_y);
				}
				else{
			 		$pdf->SetX($current_x+$anchoCelda);
				}
				// Termina la impresión de grupo alimenticio
			
				if(1==1){
					$aux = $alimento['componente'];
					$largoNombre = 37;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
						$aux = substr($aux, 0, $largoNombre);
					}

		  			//Alimento
		  			$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);

		  			// grupo1
					if($alimento['presentacion'] == 'u'){
		  				$aux = number_format($alimento['cant_grupo1'], 2, '.', '');
					}else{
		  				$aux = 0+$alimento['cant_grupo1'];
		  				$aux = number_format($aux, 2, '.', '');
					}
					// if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
					$pdf->Cell(13, 4, utf8_decode($aux), 1, 0, 'C', False);

					// grupo2
					if($alimento['presentacion'] == 'u'){
		  				$aux = number_format($alimento['cant_grupo2'], 2, '.', '');
					}else{
		  				$aux = 0+$alimento['cant_grupo2'];
		  				$aux = number_format($aux, 2, '.', '');
					}
					// if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
					$pdf->Cell(13, 4, utf8_decode($aux), 1, 0, 'C', False);

					// grupo3
					if($alimento['presentacion'] == 'u'){
		  				$aux = number_format($alimento['cant_grupo3'], 2, '.', '');
					}else{
		  				$aux = 0 + $alimento['cant_grupo3'];
		  				$aux = number_format($aux, 2, '.', '');
					}
					// if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
					$pdf->Cell(13, 4, utf8_decode($aux), 1, 0, 'C', False);

					// grupo4
					if($alimento['presentacion'] == 'u'){
		  				$aux = number_format($alimento['cant_grupo4'], 2, '.', '');
					}else{
		  				$aux = 0 + $alimento['cant_grupo4'];
		  				$aux = number_format($aux, 2, '.', '');
					}
					// if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
					$pdf->Cell(13, 4, utf8_decode($aux), 1, 0, 'C', False);

					// grupo5
					if($alimento['presentacion'] == 'u'){
		  				$aux = number_format($alimento['cant_grupo5'], 2, '.', '');
					}else{
		  				$aux = 0 + $alimento['cant_grupo5'];
		  				$aux = number_format($aux, 2, '.', '');
					}
					// if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
					$pdf->Cell(13, 4, utf8_decode($aux), 1, 0, 'C', False);

					//UNIDAD DE MEDIDA
					$aux = $alimento['presentacion'];
					$pdf->Cell(12, 4, $aux, 1, 0, 'C', False);

					if ($alimento['presentacion'] == 'u') {
						$aux = number_format($alimento['cant_total'], 2, '.', '');
					} else {
						$aux = number_format($alimento['cant_total'], 2, '.', '');
					}

					$pdf->Cell(12, 4, $aux, 1, 0, 'C', False);
					if($alimento['cantotalpresentacion'] > 0){
						$aux = 0+$alimento['cantotalpresentacion'];
						$aux = number_format($aux, 2, '.', '');
					}

					$aux = number_format($alimento['cant_total'], 2, '.', '');
					if ($alimento['redondeo'] == 1) {
						if ($aux <= 0.5) {
							$aux = ceil($aux);
						}else if ($aux > 0.5) {
							$aux = round($aux,0);
						}	
					}

					if ($alimento['presentacion'] == 'u') {
	  					if (strpos($alimento['componente'], "HUEVO") !== FALSE) {
							$aux = ceil(0+$aux);
	  					} else {
							$aux = round(0+$aux);
	  					}
					}
					$aux = number_format($aux, 2, '.', '');

					// CANTIDAD ENTREGADA TOTAL
					if( $alimento['cantu2'] > 0 || $alimento['cantu3'] > 0 || $alimento['cantu4'] > 0 || $alimento['cantu5'] > 0 ){ $aux = ""; }

					if(stristr($alimento['grupo_alim'], 'Contramuestra') !== FALSE){ 

						$pdf->Cell(12, 4, '', 0, 0, 'C', False);
						//total entregado
						$pdf->Cell(7.5, 4, '', 0, 0, 'C', False);
						$pdf->Cell(7.5, 4, '', 0, 0, 'C', False);
						// ESPECIFICACIÓN DE CALIDAD
						$pdf->Cell(11, 4, '', 0, 0, 'C', False);
						$pdf->Cell(11, 4, '', 0, 0, 'C', False);
						// FALTANTES
						$pdf->Cell(8, 4, '', 0, 0, 'C', False);
						$pdf->Cell(8, 4, '', 0, 0, 'C', False);
						$pdf->Cell(10, 4, '', 0, 0, 'C', False);
						//DEVOLUCIÓN
						$pdf->Cell(8, 4, '', 0, 0, 'C', False);
						$pdf->Cell(8, 4, '', 0, 0, 'C', False);
						$pdf->Cell(0, 4, '', 0, 0, 'C', False);
						$pdf->Ln(4);
					}
					else{
						//  ($alimento['grupo_alim'] != 'Contramuestra') {
						$pdf->Cell(12, 4, $aux, 1, 0, 'C', False);
		
						//total entregado
						$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
						$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
						// ESPECIFICACIÓN DE CALIDAD
						$pdf->Cell(11, 4, '', 1, 0, 'C', False);
						$pdf->Cell(11, 4, '', 1, 0, 'C', False);
						// FALTANTES
						$pdf->Cell(8, 4, '', 1, 0, 'C', False);
						$pdf->Cell(8, 4, '', 1, 0, 'C', False);
						$pdf->Cell(10, 4, '', 1, 0, 'C', False);
						//DEVOLUCIÓN
						$pdf->Cell(8, 4, '', 1, 0, 'C', False);
						$pdf->Cell(8, 4, '', 1, 0, 'C', False);
						$pdf->Cell(0, 4, '', 1, 0, 'C', False);
						$pdf->Ln(4);
					}
				}//Termina el if que validad si hay cantidades en las unidades con el fin de ocultar la fila inicial.

				$pdf->SetFont('Arial','I',$tamannoFuente);
				$unidad = 2;
				if($alimento['cantu'.$unidad] > 0){
					$pdf->SetX($current_x+$anchoCelda);
					$presentacion = " ".$alimento['nombreunidad'.$unidad];
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);
					$aux = $sangria.$alimento['componente'].$presentacion;
					$largoNombre = 30;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
				  		$aux = substr($aux,0,$largoNombre);
					}
					$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(12, 4, '', 1, 0, 'C', False);
					$pdf->Cell(12, 4, '', 1, 0, 'C', False);
					$aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
					// CANTIDAD ENTREGADA
					$pdf->Cell(12, 4, $aux, 1, 0, 'C', False);
					$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
					$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
					// ESPECIFICACIÓN DE CALIDAD
					$pdf->Cell(11, 4, '', 1, 0, 'C', False);
					$pdf->Cell(11, 4, '', 1, 0, 'C', False);
					// FALTANTES
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(10, 4, '', 1, 0, 'C', False);
					//DEVOLUCIÓN
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(0, 4, '', 1, 0, 'C', False);
					$pdf->Ln(4);
				}

				$unidad = 3;
				if($alimento['cantu'.$unidad] > 0){
					$pdf->SetX($current_x+$anchoCelda);
					$presentacion = " ".$alimento['nombreunidad'.$unidad];
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);
					$aux = $sangria.$alimento['componente'].$presentacion;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
				  		$aux = substr($aux,0,$largoNombre);
					}
					$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(12, 4, '', 1, 0, 'C', False);
					$pdf->Cell(12, 4, '', 1, 0, 'C', False);
					$aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
					// CANTIDAD ENTREGADA
					$pdf->Cell(12, 4, $aux, 1, 0, 'C', False);
					$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
					$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
					// ESPECIFICACIÓN DE CALIDAD
					$pdf->Cell(11, 4, '', 1, 0, 'C', False);
					$pdf->Cell(11, 4, '', 1, 0, 'C', False);
					// FALTANTES	
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(10, 4, '', 1, 0, 'C', False);
					//DEVOLUCIÓN
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(0, 4, '', 1, 0, 'C', False);
					$pdf->Ln(4);
				}

				$unidad = 4;
				if($alimento['cantu'.$unidad] > 0){
					$pdf->SetX($current_x+$anchoCelda);
					$presentacion = " ".$alimento['nombreunidad'.$unidad];
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);
					$aux = $sangria.$alimento['componente'].$presentacion;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
				  		$aux = substr($aux, 0, $largoNombre);
					}
					$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(12, 4, '', 1, 0, 'C', False);
					$pdf->Cell(12, 4, '', 1, 0, 'C', False);
					$aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
					// CANTIDAD ENTREGADA
					$pdf->Cell(12, 4, $aux, 1, 0, 'C', False);
					$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
					$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
					// ESPECIFICACIÓN DE CALIDAD
					$pdf->Cell(11, 4, '', 1, 0, 'C', False);
					$pdf->Cell(11, 4, '', 1, 0, 'C', False);
					// FALTANTES
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(10, 4, '', 1, 0, 'C', False);
					//DEVOLUCIÓN
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(0.4, 4, '', 1, 0, 'C', False);
					$pdf->Ln(4);
				}

				$unidad = 5;
				if($alimento['cantu'.$unidad] > 0){
					$pdf->SetX($current_x+$anchoCelda);
					$presentacion = " ".$alimento['nombreunidad'.$unidad];
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);
					$aux = $sangria.$alimento['componente'].$presentacion;
					$long_nombre=strlen($aux);
					if($long_nombre > $largoNombre){
				  		$aux = substr($aux, 0, $largoNombre);
					}
					$pdf->Cell(48.9, 4, utf8_decode($aux), 1, 0, 'L', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(13, 4, '', 1, 0, 'C', False);
					$pdf->Cell(12, 4, '', 1, 0, 'C', False);
					$pdf->Cell(12, 4, '', 1, 0, 'C', False);
					$aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');
					// CANTIDAD ENTREGADA
					$pdf->Cell(12, 4, $aux, 1, 0, 'C', False);
					$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
					$pdf->Cell(7.5, 4, '', 1, 0, 'C', False);
					// ESPECIFICACIÓN DE CALIDAD
					$pdf->Cell(11, 4, '', 1, 0, 'C', False);
					$pdf->Cell(11, 4, '', 1, 0, 'C', False);
					// FALTANTES
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(10, 4, '', 1, 0, 'C', False);
					//DEVOLUCIÓN
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(8, 4, '', 1, 0, 'C', False);
					$pdf->Cell(0, 4, '', 1, 0, 'C', False);
					$pdf->Ln(4);
				}
				$pdf->SetFont('Arial','',$tamannoFuente);
			}	//Termina el for de los alimentos
	  	}
	  	$current_y = $pdf->GetY();
  		if($current_y > 160){
			$filas = 0;
			$pdf->AddPage();
			include 'despacho_por_sede_footer.php';
			include 'despacho_por_sede_header.php';
  		}
  		include 'despacho_firma_planilla.php';
	}
	mysqli_close ( $Link );
	$pdf->Output();
}