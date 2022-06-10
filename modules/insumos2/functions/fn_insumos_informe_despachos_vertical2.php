<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';
require_once '../../../fpdf181/fpdf.php';

if (isset($_POST['sedes'])) {
	$sedes = $_POST['sedes'];
} else {
	echo "<script>alert('No se ha definido sede.');</script>";
}

if (isset($_POST['tablaMesInicio'])) {
	$tablaMes = $_POST['tablaMesInicio'];
} else {
	echo "<script>alert('No se ha definido mes.');</script>";
}


$paginasObservaciones = "";
if(isset($_POST['paginasObservaciones']) && $_POST['paginasObservaciones'] != ""){
	$paginasObservaciones = $_POST['paginasObservaciones'];
}

if (isset($_POST['mesImprimir'])) {
	$boleanMes = $_POST['mesImprimir'];
}
// var_dump($boleanMes);

$sedes = array_unique($sedes);

$despachos_seleccionados = $_POST['despachos_seleccionados'];
$despachos_seleccionados = trim($despachos_seleccionados, ", ");
$ds = explode(", ", $despachos_seleccionados);
$ds_mes = [];

$meses_involucrados = [];
$ids_despachos = [];

foreach ($ds as $id => $value) {
	$sd = explode("_", $value);

	$ds_mes[$id]['id_despacho'] = $sd[0];
	$ds_mes[$id]['mes_despacho'] = $sd[1];

	if (!isset($meses_involucrados[$sd[1]])) {
		$meses_involucrados[(Int) $sd[1]] = 1;
	}

	if (!isset($ids_despachos[$sd[0]])) {
		$ids_despachos[(Int) $sd[0]] = 1;
	}

}

if (count($meses_involucrados) > 1) {
	echo "<script>alert('No puede escoger despachos de diferentes meses para este informe.');</script>";
}


$sedeTabla = "sedes".$_SESSION['periodoActual'];
$insumosmov = "insumosmov".$tablaMes.$_SESSION['periodoActual'];

// mes que se va a utilizar para traer el numero de la entrega
$mesEntrega = 0;
if (isset($_POST["tablaMesInicio"])) {
	$mesEntrega = $_POST['tablaMesInicio'];
	if($mesEntrega < 10){ $mesEntrega = '0'.$mesEntrega; }
	$mesEntrega = trim($mesEntrega);
}

// MODIFICACIONES JERSON 
// validacion para saber si los despachos que se seleccionaron son todos de tipo RPC

$mesImprimir = "";
$tipoComplementoDespacho = [];
$idDespachos = [];
$numeroDespachosRpc = 0;
$Rpc = '';
$entrega = '';
$nombreMesEntrega = '';



// vamos a buscar el numero del contrato 
$consultaContrato = "SELECT NumContrato FROM parametros;";
$respuestaContrato = $Link->query($consultaContrato) or die('Error al consultar el contrato' .mysqli_error($Link));
if ($respuestaContrato->num_rows > 0) {
	$dataContrato = $respuestaContrato->fetch_assoc();
	$contrato = $dataContrato['NumContrato'];
}

// END MODIFICACIONES JERSON

$insumosmovdet = "insumosmovdet".$tablaMes.$_SESSION['periodoActual'];

$consultaDpto = "SELECT NombreETC FROM parametros";
$resultadoDpto = $Link->query($consultaDpto);
if ($resultadoDpto->num_rows > 0) {
	$dpto = $resultadoDpto->fetch_assoc();
	$dpto = $dpto['NombreETC'];
}

$gruposEtarios = [];
$consultaGruposEtarios = "SELECT * FROM grupo_etario";
$resultadoGruposEtarios = $Link->query($consultaGruposEtarios);
if ($resultadoGruposEtarios->num_rows > 0) {
	while ($grupoEta = $resultadoGruposEtarios->fetch_assoc()) {
		$gruposEtarios[$grupoEta['ID']] = $grupoEta['DESCRIPCION'];
	}
}


class PDF extends FPDF
	{

	function setData($fecha, $dpto, $dataSede, $coberturaEtarios, $maxEstudiantes, $gruposEtarios, $mes, $tipoComplemento, $entrega, $Rpc, $contrato, $numDias, $numManipuladoras){
		$this->fecha = $fecha;
		$this->mes = mesNombre($mes);
		$this->dpto = $dpto;
		$this->dataSede = $dataSede;
		$this->Ciudad = $this->dataSede['Ciudad'];
		$this->nom_inst = $this->dataSede['nom_inst'];
		$this->nom_sede = $this->dataSede['nom_sede'];
		$this->coberturaEtarios = $coberturaEtarios;
		$this->maxEstudiantes = $maxEstudiantes;
		$this->gruposEtarios = $gruposEtarios;
		$this->tipoComplemento = $tipoComplemento;
		$this->entrega = $entrega;
		$this->Rpc = $Rpc;
		$this->contrato = $contrato;
		$this->dias = $numDias;
		$this->manipuladoras = $numManipuladoras;
	}

		function Header()
		{
			$tamannoFuente = 8;

			$logoInfopae = '../'.$_SESSION['p_Logo ETC'];
			$this->SetFont('Arial','B',10);
			$this->Image($logoInfopae,8,10,85,12.1,'jpg', '');
			$this->Cell(85,18,'','TBRL',0,'C');
			$this->Cell(0,9,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),'TR',1,'C');
			$this->Cell(85,9,utf8_decode(''),'',0,'C');
			$this->MultiCell(0,4.5,utf8_decode('REMISIÓN DE ENTREGA DE INSUMOS DE ASEO Y BIOSEGURIDAD' ."\n". 'EN INSTITUCIÓN EDUCATIVA'),'BR','C',false);
			if ($this->Rpc == 'RPC') {
				$anchoOperador = 65;
			}else if($this->Rpc != 'RPC'){$anchoOperador = 84;}
			$this->SetFont('Arial','B',8);
			$this->Cell(25,4,utf8_decode('OPERADOR: '),'BL',0,'L');
			$this->SetFont('Arial','',8);
			$this->Cell($anchoOperador,4,utf8_decode($_SESSION['p_Operador']),'BR',0,'L');
			$this->SetFont('Arial','B',8);
			$this->Cell(19,4,utf8_decode('CONTRATO:'),'B',0,'L');
			$this->SetFont('Arial','',8);
			$this->Cell(40,4,utf8_decode($this->contrato),'BR',0,'L'); 
			// si las entregas los despachos son todos RPC se dibujara el campo en el pdf de lo contrario no
			if ($this->Rpc == 'RPC') {
				$this->SetFont('Arial','B',8);
				$this->Cell(9,4,utf8_decode('MES:'),'B',0,'L');
				$this->SetFont('Arial','',8);
				$this->Cell(22,4,utf8_decode($this->mes),'BR',0,'L'); //FECHA DE ELABORACIÓN DEL DESPACHO - REEMPLAZAR	
				$this->SetFont('Arial','B',8);
				$this->Cell(16,4,utf8_decode('ENTREGA: '),'B',0,'L');
				$this->SetFont('Arial','',8);
				$entregaString = '';
				if ($this->entrega < 10) {
					$entregaString = "0".$this->entrega;
				}else{$entregaString = $this->entrega;}
				$this->Cell(0,4,utf8_decode($entregaString),'BR',1,'L'); //Entregas
			}else if($this->Rpc != 'RPC'){
				$this->SetFont('Arial','B',8);
				$this->Cell(9,4,utf8_decode('MES:'),'B',0,'L');
				$this->SetFont('Arial','',8);
				$this->Cell(0,4,utf8_decode($this->mes),'BR',1,'L'); //FECHA DE ELABORACIÓN DEL DESPACHO - REEMPLAZAR	
			}
			$this->SetFont('Arial','B',8);
			$this->Cell(14,4,utf8_decode('ETC: '),'BL',0,'L');
			$this->SetFont('Arial','',8);
			$this->Cell(71,4,utf8_decode(strtoupper($this->dpto)),'BR',0,'L'); //DPTO PARAMETRO - REEMPLAZAR
			$this->SetFont('Arial','B',8);
			$this->Cell(35,4,utf8_decode('MUNICIPIO O VEREDA: '),'BL',0,'L');
			$this->SetFont('Arial','',8);
			$this->Cell(0,4,utf8_decode($this->Ciudad),'BR',1,'L'); //MUNICIPIO DE SEDE - REEMPLAZAR
			
			$this->SetFont('Arial','B',8);
			$this->Cell(53,4,utf8_decode('INSTITUCIÓN O CENTRO EDUCATIVO: '),'BL',0,'L');
			$this->SetFont('Arial','',8);
			$this->Cell(0,4,utf8_decode($this->nom_inst),'BR',1,'L'); //INSTITUCIÓN - REEMPLAZZAR
			$this->SetFont('Arial','B',8);
			$this->Cell(27,4,utf8_decode('SEDE EDUCATIVA: '),'BL',0,'L');
			$this->SetFont('Arial','',8);
			$this->Cell(0,4,utf8_decode($this->nom_sede),'BR',1,'L'); //SEDE REEMPLAZAR
			
			// Salto de línea
			$this->Ln(1);
			$this->SetFont('Arial','B',8);
			$this->Cell(40,10,utf8_decode('RANGO DE EDAD'),'TBL',0,'C');

			$cx = $this->getX();
			$cy = $this->getY();
			$this->MultiCell(35, 5, utf8_decode('N° DE RACIONES ADJUDICADAS'), 0, 'C');
			$this->setXY($cx, $cy);
			$this->Cell(35,10,'','TBL',0,'C');

			$cx = $this->getX();
			$cy = $this->getY();
			$this->MultiCell(35, 5, utf8_decode('N° DE RACIONES ATENDIDAS'), 0, 'C');
			$this->setXY($cx, $cy);
			$this->Cell(35,10,'','TBL',0,'C');

			$cx = $this->getX();
			$cy = $this->getY();
			$this->MultiCell(28, 5, utf8_decode('N° DE MANIPULADORAS'), 0, 'C');
			$this->setXY($cx, $cy);
			$this->Cell(28,10,'','TBL',0,'C');

			$cx = $this->getX();
			$cy = $this->getY();
			$this->MultiCell(12, 5, utf8_decode('N° DÍAS'), 0, 'C');
			$this->setXY($cx, $cy);
			$this->Cell(12,10,'','TBL',0,'C');

			$cx = $this->getX();
			$cy = $this->getY();
			$this->MultiCell(26,5,utf8_decode('TOTAL COBERTURA'),1,'C');
			$this->setXY($cx, $cy);
			$this->Cell(26,10,'','TBL',0,'C');

			$cx = $this->getX();
			$cy = $this->getY();
			$this->MultiCell(28, 5, utf8_decode('TIPO COMPLEMENTO'), 0, 'C');
			$this->setXY($cx, $cy);
			$this->Cell(0,10,'','TBLR',0,'C');

			$this->Ln();

			$this->SetFont('Arial','',8);


			$cx = $this->getX();
			$cy = $this->getY();
			foreach ($this->gruposEtarios as $ID => $DESCRIPCION) {
				$this->Cell(40,6,utf8_decode($DESCRIPCION),'BL',0,'C');
				$this->Cell(35,6,utf8_decode(isset($this->coberturaEtarios['Etario'.$ID]) ? $this->coberturaEtarios['Etario'.$ID] : 0),'BL',0,'C');//SEDES COBERTURA POR GRUPO ETARIO
				$this->Cell(35,6,utf8_decode(isset($this->coberturaEtarios['Etario'.$ID]) ? $this->coberturaEtarios['Etario'.$ID] : 0),'BL',1,'C');//SEDES COBERTURA POR GRUPO ETARIO
			}
			$this->setXY($cx+(35*2+40), $cy);

			// if ($this->tipoComplemento == 'APS') {
			// 	$manipuladoras = $this->dataSede['Manipuladora_APS'];
			// } else if ($this->tipoComplemento == 'CAJMPS') {
			// 	$manipuladoras = $this->dataSede['Manipuladora_CAJMPS'];
			// } else if ($this->tipoComplemento == 'CAJMRI') {
			// 	$manipuladoras = $this->dataSede['Manipuladora_CAJMRI'];
			// } else if ($this->tipoComplemento == 'CAJTRI') {
			// 	$manipuladoras = $this->dataSede['Manipuladora_CAJTRI'];
			// } else {
			// 	$manipuladoras = $this->dataSede['cantidad_Manipuladora'];
			// }

			$this->Cell(28,18,utf8_decode($this->manipuladoras),'TBLR',0,'C');//MANIPULADORAS DE LA SEDE
			$this->Cell(12,18,utf8_decode($this->dias),'TBLR',0,'C'); // numero dias
			$this->Cell(26,18,utf8_decode($this->maxEstudiantes),'TBR',0,'C');//CANTIDAD COBERTURA
			$this->Cell(25.9,18,utf8_decode($this->tipoComplemento),'TBR',0,'C');//CANTIDAD COBERTURA

			//Salto de línea
			$this->Ln();
			$this->SetFont('Arial','B',$tamannoFuente);
			$this->Cell(75,5,utf8_decode("INSUMO"),'TBLR',0,'C');
			$this->Cell(40,5,utf8_decode("UNIDAD MEDIDA"),'TBR',0,'C');
			$this->Cell(40,5,utf8_decode("TOTAL REQUERIDO"),'TBR',0,'C');

			$cx2 = $this->getX();
			$cy2 = $this->getY();

			//$this->Cell(30,10,utf8_decode("TOTAL REQUERIDO"),'TBR',0,'C');
			
			
			//
			
			
			
			$this->Cell(0,5,utf8_decode("CANTIDAD ENTREGADA TOTAL"),'TBLR',0,'C');


			// $cx = $this->getX();
			// $cy = $this->getY();
			// $this->MultiCell(0, 5, utf8_decode('CANTIDAD ENTREGADA TOTAL'), 0, 'C');
			// $this->setXY($cx, $cy);
			// $this->Cell(0,10,'','TBLR',0,'C');
			
			
			
			
			//$this->Cell(27,5,utf8_decode("ESPECIFICACIÓN CALIDAD"),'TBR',0,'C');
			//$this->Cell(30,5,utf8_decode("FALTANTES"),'TBR',0,'C');
			//$this->Cell(0,5,utf8_decode("DEVOLUCIÓN"),'TBR',1,'C');
			
			//$this->setXY($cx2, $cy2+5000);

			// $this->Ln(200);


			// $this->setXY($cx2, $cy2+5);

			// $this->Cell(11,5,utf8_decode("TOTAL"),'BR',0,'C');
			// $this->Cell(10,5,utf8_decode("C"),'BR',0,'C');
			// $this->Cell(10,5,utf8_decode("NC"),'BR',0,'C');

			// $this->Cell(13,5,utf8_decode("C"),'BR',0,'C');
			// $this->Cell(14,5,utf8_decode("NC"),'BR',0,'C');

			// $this->Cell(10,5,utf8_decode("SI"),'BR',0,'C');
			// $this->Cell(10,5,utf8_decode("NO"),'BR',0,'C');
			// $this->Cell(10,5,utf8_decode("CANT"),'BR',0,'C');

			// $this->Cell(12.67,5,utf8_decode("SI"),'BR',0,'C');
			// $this->Cell(12.67,5,utf8_decode("NO"),'BR',0,'C');
			// $this->Cell(0,5,utf8_decode("CANT"),'BR',0,'C');

			$this->Ln();
		}

	}

	$pdf = new PDF('P', 'mm', 'Letter');
	$pdf->AliasNbPages();
	$pdf->SetMargins(7, 7);

	// exit(var_dump($sedes));


$alturaFilasItems = 4;
$fuenteFilasItems = 7.8;


foreach ($sedes as $key => $sede) {
	$consultaSede = "SELECT
						ubicacion.Ciudad, instituciones.nom_inst, sede.*
					FROM
						$sedeTabla AS sede
							INNER JOIN
						ubicacion ON ubicacion.CodigoDANE = sede.cod_mun_sede
							INNER JOIN
						instituciones ON instituciones.codigo_inst = sede.cod_inst
					WHERE
						sede.cod_sede = '".$sede."';";
	$resultadoSede = $Link->query($consultaSede);
	if ($resultadoSede->num_rows > 0) {
		$dataSede = $resultadoSede->fetch_assoc();
	}

	$sumaCoberturasEtario = "";

	foreach ($gruposEtarios as $ID => $DESCRIPCION) {
		$sumaCoberturasEtario.=" MAX(Etario".$ID."_APS+Etario".$ID."_CAJMRI+Etario".$ID."_CAJTRI+Etario".$ID."_CAJMPS) as Etario".$ID." ,";
	}

	$sumaCoberturasEtario = trim($sumaCoberturasEtario, " ,");


	// $coberturaEtarios = [];$maxEstudiantes=0;
	// $consultaEtariosCobertura = "SELECT MAX(APS) + MAX(CAJMPS)+ MAX(CAJMRI)+ MAX(CAJTRI) AS cant_Estudiantes, $sumaCoberturasEtario FROM sedes_cobertura WHERE cod_sede = '".$sede."' and mes= '".$tablaMes."' GROUP BY cod_sede";
	// $resultadoEtariosCobertura = $Link->query($consultaEtariosCobertura);
	// if ($resultadoEtariosCobertura->num_rows > 0) {
	// 	if ($EtariosCobertura = $resultadoEtariosCobertura->fetch_assoc()) {
	// 		$coberturaEtarios["Etario1"] = $EtariosCobertura["Etario1"];
	// 		$coberturaEtarios["Etario2"] = $EtariosCobertura["Etario2"];
	// 		$coberturaEtarios["Etario3"] = $EtariosCobertura["Etario3"];
	// 		$maxEstudiantes = $EtariosCobertura["cant_Estudiantes"];
	// 	}
	// } else {

	// }

	// exit(var_dump($ids_despachos));

	$consultaDespacho = "SELECT * FROM $insumosmov WHERE BodegaDestino = '".$sede."'";
	// exit($consultaDespacho);
	$resultadoDespacho = $Link->query($consultaDespacho);
	if ($resultadoDespacho->num_rows > 0) {
		while ($Despacho = $resultadoDespacho->fetch_assoc()) {
			if (isset($ids_despachos[$Despacho['Id']])) {
				if ($Despacho['Id'] == '100') {
				}

				$coberturaEtarios["Etario1"] = $Despacho["Cobertura_G1"];
				$coberturaEtarios["Etario2"] = $Despacho["Cobertura_G2"];
				$coberturaEtarios["Etario3"] = $Despacho["Cobertura_G3"];
				$maxEstudiantes = $Despacho["Cobertura"];
				$tipoComplemento = $Despacho['Complemento'];

				// INICIO CODIGO JERSON

				// var_dump($Despacho['Id']);
				$consultaComplemento = "SELECT Complemento FROM $insumosmov WHERE id = " .$Despacho['Id'].";";
				$respuestaComplemento = $Link->query($consultaComplemento) or die ('Error al consultar los complementos'. mysqli_error($Link));
				if ($respuestaComplemento->num_rows > 0) {
					while ($dataComplemento = $respuestaComplemento->fetch_assoc()) {
						$tipoComplementoDespacho = $dataComplemento['Complemento'];
					}
				}
				if ($tipoComplementoDespacho == 'RPC') {				
					// vamos a buscar el numero de la entrega que va a estar junto al mes 
					$consultaEntrega = "SELECT NumeroEntrega FROM planilla_dias WHERE mes = $mesEntrega;";
					$respuestaEntrega = $Link->query($consultaEntrega) or die('Error al consultar el numero de la entrega' . mysqli_error($Link));
					if ($respuestaEntrega->num_rows>0) {
						$dataEntrega = $respuestaEntrega->fetch_assoc();
						$entrega = $dataEntrega['NumeroEntrega'];
					}

					// vamos a buscar el nombre del mes de la entrega
					$consultaNombreMes = "SELECT NombreMes FROM planilla_dias WHERE mes = $mesEntrega;";
					$respuestaNombreMes = $Link->query($consultaNombreMes) or die ('Error al consultar el nombre del mes de la entrega' . mysqli_error($Link));
					if ($respuestaNombreMes->num_rows > 0) {
						$dataNombreMes = $respuestaNombreMes->fetch_assoc();
						$nombreMesEntrega = $dataNombreMes['NombreMes'];
					}
				}
				else {
					$nombreMesEntrega = $tablaMes;
				}


				if ($boleanMes == "true" ) {
					$mesImprimir = $nombreMesEntrega;
				} else if ($boleanMes == "false") {
					$mesImprimir = "";
				}

				$consultaNumeroDias = "SELECT CantDias FROM $insumosmov WHERE id = " .$Despacho['Id'].";";
				$respuestaNumeroDias = $Link->query($consultaNumeroDias) or die ('Error al consultar el numero de dias' . mysqli_error($Link));
				if ($respuestaNumeroDias->num_rows > 0) {
					$dataRespuestaDias = $respuestaNumeroDias->fetch_assoc();
					$numeroDias = $dataRespuestaDias['CantDias'];
				}

				// consulta numero manipuladoras desde la tabla insumosmov
				$consultaNumeroManipuladoras = "SELECT NumManipuladoras FROM $insumosmov WHERE id = " .$Despacho['Id'].";";
				$respuestaNumeroManipuladoras = $Link->query($consultaNumeroManipuladoras) or die ('Error al consultar el numero de manipuladoras' . mysqli_error($Link));
				if ($respuestaNumeroManipuladoras->num_rows > 0) {
					$dataRespuestaManipuladoras = $respuestaNumeroManipuladoras->fetch_assoc();
					$numeroManipuladoras = $dataRespuestaManipuladoras['NumManipuladoras'];
				}

				// FIN CODIGO JERSON

				$pdf->setData($Despacho['FechaMYSQL'], $dpto, $dataSede, $coberturaEtarios, $maxEstudiantes, $gruposEtarios, $mesImprimir, $tipoComplemento, $entrega, $tipoComplementoDespacho, $contrato, $numeroDias, $numeroManipuladoras);
				$pdf->AddPage();
				$pdf->SetFont('Arial','',$fuenteFilasItems);
				//PRODUCTOS
				$consultaDetalles = "SELECT producto.NombreUnidad1, producto.NombreUnidad2, producto.NombreUnidad3, producto.NombreUnidad4, producto.NombreUnidad5, producto.CantidadUnd2, insmovdet.* FROM $insumosmovdet AS insmovdet
									INNER JOIN productos".$_SESSION['periodoActual']." as producto ON producto.Codigo = insmovdet.CodigoProducto
				 					WHERE insmovdet.Numero = '".$Despacho['Numero']."'";
				$resultadoDetalles = $Link->query($consultaDetalles);
				
				
				$maximoDeFilas = 32;
				$filasMostradas = 0;
				
				
				if ($resultadoDetalles->num_rows > 0) {
					while ($detalles = $resultadoDetalles->fetch_assoc()) {
						
						$filasMostradas++;

						if ($detalles['CantU3'] != 0 || $detalles['CantU4'] != 0 || $detalles['CantU5'] != 0) { //SI SE DIERON MÁS PRESENTACIONES
							$pdf->Cell(75,$alturaFilasItems,utf8_decode($detalles['Descripcion']),'BLR',0,'L');
							$pdf->Cell(40,$alturaFilasItems,utf8_decode($detalles['Umedida']),'BR',0,'C');
							$pdf->Cell(40,$alturaFilasItems,utf8_decode(round($detalles['Cantidad']/1000, 2)),'BR',0,'C');
							$pdf->Cell(0,$alturaFilasItems,utf8_decode(number_format($detalles['CanTotalPresentacion'], 3, '.', ',')),'BR',1,'C');

							if ($detalles['CantU2'] != 0 ) {
								$pdf->Cell(75,$alturaFilasItems,utf8_decode("    ".$detalles['Descripcion']." ".$detalles['NombreUnidad2']),'BLR',0,'L');
								$pdf->Cell(40,$alturaFilasItems,'','BR',0,'C');
								$pdf->Cell(40,$alturaFilasItems,'','BR',0,'C');
								$pdf->Cell(0,$alturaFilasItems,utf8_decode(number_format($detalles['CantU2'], 0)),'BR',1,'C');
							}
							if ($detalles['CantU3'] != 0 ) {
								$pdf->Cell(75,$alturaFilasItems,utf8_decode("    ".$detalles['Descripcion']." ".$detalles['NombreUnidad3']),'BLR',0,'L');
								$pdf->Cell(40,$alturaFilasItems,'','BR',0,'C');
								$pdf->Cell(40,$alturaFilasItems,'','BR',0,'C');
								$pdf->Cell(0,$alturaFilasItems,utf8_decode(number_format($detalles['CantU3'], 0)),'BR',1,'C');
							}

							if ($detalles['CantU4'] != 0 ) {
								$pdf->Cell(75,$alturaFilasItems,utf8_decode("    ".$detalles['Descripcion']." ".$detalles['NombreUnidad4']),'BLR',0,'L');
								$pdf->Cell(40,$alturaFilasItems,'','BR',0,'C');
								$pdf->Cell(40,$alturaFilasItems,'','BR',0,'C');
								$pdf->Cell(0,$alturaFilasItems,utf8_decode(number_format($detalles['CantU4'], 0)),'BR',1,'C');
							}

							if ($detalles['CantU5'] != 0 ) {
								$pdf->Cell(75,$alturaFilasItems,utf8_decode("    ".$detalles['Descripcion']." ".$detalles['NombreUnidad5']),'BLR',0,'L');
								$pdf->Cell(40,$alturaFilasItems,'','BR',0,'C');
								$pdf->Cell(40,$alturaFilasItems,'','BR',0,'C');
								$pdf->Cell(0,$alturaFilasItems,utf8_decode(number_format($detalles['CantU5'], 0)),'BR',1,'C');
							}
						} else {
							$pdf->Cell(75,$alturaFilasItems,utf8_decode($detalles['Descripcion']),'BLR',0,'L');
							$pdf->Cell(40,$alturaFilasItems,utf8_decode($detalles['Umedida']),'BR',0,'C');
							$pdf->Cell(40,$alturaFilasItems,utf8_decode(number_format($detalles['CanTotalPresentacion'], 1, '.', ',')),'BR',0,'C');
							$pdf->Cell(0,$alturaFilasItems,utf8_decode((number_format($detalles['NombreUnidad1'] == 'u' ? ceil($detalles['CanTotalPresentacion']) : $detalles['CanTotalPresentacion'], 1, '.', ','))) ,'BR',1,'C');
						}
					}
				}

				$cy = $pdf->GetY();
				if($cy > 199){
					$pdf->AddPage();
					$filasMostradas = 0;
				}

				$pdf->ln();


				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5,$paginasObservaciones,'B',5,'L',False);
				$pdf->Cell(0,5,'','B',5,'L',False);
				$pdf->Cell(0,5,'','B',5,'L',False);
				$pdf->Cell(0,5,'','B',5,'L',False);
				$pdf->Ln(2);


				$current_y = $pdf->GetY();
				$current_x = $pdf->GetX();

				$pdf->SetXY($current_x, $current_y);
				$pdf->SetFont('Arial','B',$fuenteFilasItems);
				$pdf->Cell(100,4,utf8_decode('MANIPULADOR'),1,0,'C',False);
				$pdf->Cell(102,4,utf8_decode('INSTITUCIÓN EDUCATIVA'),1,0,'C',false);
				$pdf->ln();

				$cordenadaY = $pdf->GetY();
				$cordenadaX = $pdf->GetX();
				$pdf->MultiCell(100,5, utf8_decode('NOMBRE MANIPULADOR (Operador):' ."\n". ' ' ."\n". ' '),'LBR','L',False);
				
				$pdf->setXY($cordenadaX+100, $cordenadaY);
				$pdf->MultiCell(102,5, utf8_decode('NOMBRE RESPONSABLE INSTITUCIÓN O CENTRO EDUCATIVO, RECTOR' . "\n". 'O REPRESENTANTE CAE:' ."\n". ' '),'LBR','L',false);
				
				$pdf->Cell(100,8,'CARGO:','LBR',0,'L',False);
				$pdf->Cell(102,8, 'CARGO:', 'LBR',0,'L',false);
				$pdf->ln();

				$pdf->Cell(100,8,'FIRMA:','LBR',0,'L',False);
				$pdf->Cell(102,8,'FIRMA:','LBR',0,'L',False);
				// $pdf->ln();
			}



		}
	}
}




$pdf->Output("INFORME_DESPACHOS_INSUMOS.pdf", "I");

function mesNombre($mes){

$meses  = ['01','02','03','04','05','06','07','08','09','10','11','12'];
	if (in_array($mes, $meses)) {
	 	if($mes == 1){
			return 'Enero';
	  	}
	  	else if($mes == 2){
			return 'Febrero';
	  	}
	  	else if($mes == 3){
			return 'Marzo';
	  	}
	  	else if($mes == 4){
			return 'Abril';
	 	}
	  	else if($mes == 5){
			return 'Mayo';
	  	}
	  	else if($mes == 6){
			return 'Junio';
	  	}
	  	else if($mes == 7){
			return 'Julio';
	  	}
	  	else if($mes == 8){
			return 'Agosto';
	  	}
	  	else if($mes == 9){
			return 'Septiembre';
	  	}
	  	else if($mes == 10){
			return 'Octubre';
	  	}
	  	else if($mes == 11){
			return 'Noviembre';
	  	}
	  	else if($mes == 12){
			return 'Diciembre';
	  	}
	}else{
		return $mes;
	}	  
}
