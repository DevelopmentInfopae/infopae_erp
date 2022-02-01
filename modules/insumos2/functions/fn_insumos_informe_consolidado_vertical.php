<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';
require_once '../../../fpdf181/fpdf.php';

if (isset($_POST['sedes'])) { $sedes = $_POST['sedes']; } 
else { echo "<script>alert('No se ha definido sede.');</script>"; }
if (isset($_POST['tablaMesInicio'])) { $tablaMes = $_POST['tablaMesInicio']; } 
else { echo "<script>alert('No se ha definido mes.');</script>"; }
$ruta = $_POST['ruta'];

$paginasObservaciones = "";
if(isset($_POST['paginasObservaciones']) && $_POST['paginasObservaciones'] != ""){ $paginasObservaciones = $_POST['paginasObservaciones']; }
if (isset($_POST['mesImprimir'])) { $boleanMes = $_POST['mesImprimir']; }
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
	if (!isset($meses_involucrados[$sd[1]])) { $meses_involucrados[(Int) $sd[1]] = 1; }
	if (!isset($ids_despachos[$sd[0]])) { $ids_despachos[(Int) $sd[0]] = 1; }
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


class PDF extends FPDF{
	function setData($fecha, $dpto, $dataSede, $coberturaEtarios, $maxEstudiantes, $gruposEtarios, $mes, $tipoComplemento, $entrega, $Rpc, $contrato, $numManipuladoras, $nombreMesTabla, $zona){
		$this->fecha = $fecha;
		// $this->mes = mesNombre($mes);
		$this->dpto = $dpto;
		$this->dataSede = $dataSede;
		$this->Ciudad = $zona;
		$this->nom_inst = $this->dataSede['nom_inst'];
		$this->nom_sede = $this->dataSede['nom_sede'];
		$this->coberturaEtarios = $coberturaEtarios;
		$this->maxEstudiantes = $maxEstudiantes;
		$this->gruposEtarios = $gruposEtarios;
		$this->tipoComplemento = $tipoComplemento;
		$this->entrega = $entrega;
		$this->Rpc = $Rpc;
		$this->contrato = $contrato;
		$this->manipuladoras = $numManipuladoras;
		$this->nombreMesTabla = $nombreMesTabla;
	}

	function Header() {
		$tamannoFuente = 8;
		$logoInfopae = '../'.$_SESSION['p_Logo ETC'];
		$this->SetFont('Arial','B',10);
		$this->Image($logoInfopae,8,10,85,12.1,'jpg', '');
		$this->Cell(85,18,'','TBRL',0,'C');
		$this->Cell(0,9,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),'TR',1,'C');
		$this->Cell(85,9,utf8_decode(''),'',0,'C');
		$this->MultiCell(0,4.5,utf8_decode('REMISIÓN DE ENTREGA DE INSUMOS DE ASEO Y BIOSEGURIDAD' ."\n". 'EN INSTITUCIÓN EDUCATIVA'),'BR','C',false);
		$anchoOperador = 84;
		$this->SetFont('Arial','B',8);
		$this->Cell(25,4,utf8_decode('OPERADOR: '),'BL',0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell($anchoOperador,4,utf8_decode($_SESSION['p_Operador']),'BR',0,'L');
		$this->SetFont('Arial','B',8);
		$this->Cell(19,4,utf8_decode('CONTRATO:'),'B',0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(40,4,utf8_decode($this->contrato),'BR',0,'L'); 
		$this->SetFont('Arial','B',8);
		$this->Cell(9,4,utf8_decode('MES:'),'B',0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(0,4,utf8_decode($this->nombreMesTabla),'BR',1,'L'); //FECHA DE ELABORACIÓN DEL DESPACHO - REEMPLAZAR	
		$this->SetFont('Arial','B',8);
		$this->Cell(14,4,utf8_decode('ETC: '),'BL',0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(71,4,utf8_decode(strtoupper($this->dpto)),'BR',0,'L'); //DPTO PARAMETRO - REEMPLAZAR
		$this->SetFont('Arial','B',8);
		$this->Cell(32,4,utf8_decode('MUNICIPIO O RUTA: '),'BL',0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(0,4,utf8_decode($this->Ciudad),'BR',1,'L'); //MUNICIPIO DE SEDE - REEMPLAZAR
			
		// Salto de línea
		$this->Ln(2);
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
		$this->MultiCell(34, 5, utf8_decode('N° DE MANIPULADORAS'), 0, 'C');
		$this->setXY($cx, $cy);
		$this->Cell(34,10,'','TBL',0,'C');

		$cx = $this->getX();
		$cy = $this->getY();
		$this->MultiCell(32,10,utf8_decode('TOTAL COBERTURA'),1,'C');
		$this->setXY($cx, $cy);
		$this->Cell(32,10,'','TBL',0,'C');

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
		$this->Cell(34,18,utf8_decode($this->manipuladoras),'TBLR',0,'C');//MANIPULADORAS DE LA SEDE
		$this->Cell(32,18,utf8_decode($this->maxEstudiantes),'TBR',0,'C');//CANTIDAD COBERTURA
		$this->Cell(25.9,18,utf8_decode($this->tipoComplemento),'TBR',0,'C',FALSE);//CANTIDAD COBERTURA

		//Salto de línea
		$this->Ln();
		$this->SetFont('Arial','B',$tamannoFuente);
		$this->Cell(75,5,utf8_decode("INSUMO"),'TBLR',0,'C');
		$this->Cell(40,5,utf8_decode("UNIDAD MEDIDA"),'TBR',0,'C');
		$this->Cell(40,5,utf8_decode("TOTAL REQUERIDO"),'TBR',0,'C');

		$cx2 = $this->getX();
		$cy2 = $this->getY();
		
		$this->Cell(0,5,utf8_decode("CANTIDAD ENTREGADA TOTAL"),'TBLR',0,'C');
		$this->Ln();
	}
}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(7, 7);

$alturaFilasItems = 4;
$fuenteFilasItems = 7.8;

$numeroManipuladoras = 0;
$coberturaEtarios["Etario1"] = 0;
$coberturaEtarios["Etario2"] = 0;
$coberturaEtarios["Etario3"] = 0;
$maxEstudiantes = 0;
$numeroIn = '(';
$sedesIn = '(';

foreach ($sedes as $key => $sede) {
	$consultaSede = "SELECT
						instituciones.nom_inst, sede.*
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
	$consultaDespacho = "SELECT * FROM $insumosmov WHERE BodegaDestino = '".$sede."'";
	$resultadoDespacho = $Link->query($consultaDespacho);
	if ($resultadoDespacho->num_rows > 0) {

		while ($Despacho = $resultadoDespacho->fetch_assoc()) {
			if (isset($ids_despachos[$Despacho['Id']])) {
				$coberturaEtarios["Etario1"] += $Despacho["Cobertura_G1"];
				$coberturaEtarios["Etario2"] += $Despacho["Cobertura_G2"];
				$coberturaEtarios["Etario3"] += $Despacho["Cobertura_G3"];
				$maxEstudiantes += $Despacho["Cobertura"];
				$tipoComplemento = $Despacho['Complemento'];

				if ($boleanMes == "true" ) {
					$mesImprimir = $nombreMesEntrega;
				} else if ($boleanMes == "false") {
					$mesImprimir = "";
				}

				// consulta numero manipuladoras desde la tabla insumosmov
				$consultaNumeroManipuladoras = "SELECT NumManipuladoras FROM $insumosmov WHERE id = " .$Despacho['Id'].";";
				$respuestaNumeroManipuladoras = $Link->query($consultaNumeroManipuladoras) or die ('Error al consultar el numero de manipuladoras' . mysqli_error($Link));
				if ($respuestaNumeroManipuladoras->num_rows > 0) {
					$dataRespuestaManipuladoras = $respuestaNumeroManipuladoras->fetch_assoc();
					$numeroManipuladoras += $dataRespuestaManipuladoras['NumManipuladoras'];
				}

				$consultaDiaPlanilla = " SELECT NombreMes FROM planilla_dias WHERE mes = $mesEntrega ";
				$respuestaDiaPlanilla = $Link->query($consultaDiaPlanilla) or die ('Error al consultar el nombre del mes ' .mysqli_error($Link));
				if ($respuestaDiaPlanilla->num_rows > 0) {
					$dataDiaPlanilla = $respuestaDiaPlanilla->fetch_assoc();
					$nombreMesTabla = $dataDiaPlanilla['NombreMes'];
				}
				$numeroIn .= "'" .$Despacho['Numero']. "', ";
			} // if id despacho
		} // while
	}  // respuesta detalle
	$sedesIn .= "'" .$sede. "', ";
} // for sedes
$numeroIn = trim($numeroIn, ", ");
$numeroIn .= ")"; 
$sedesIn = trim($sedesIn, ", ");
$sedesIn .= ')';

$zona = '';
if ($ruta !== "") {
	$consultaZona = " SELECT Nombre FROM rutas WHERE ID = '$ruta' ";
	$respuestaZona = $Link->query($consultaZona) or die ('Error al consultar el nombre de la ruta ' .mysqli_error($Link));
	if ($respuestaZona->num_rows > 0) {
		$dataZona = $respuestaZona->fetch_assoc();
		$zona = $dataZona['Nombre'];
	}
}else if ($ruta == '') {
	$consultaZona = " 	SELECT DISTINCT(u.Ciudad) as ciudad 
						FROM  $sedeTabla s	
						JOIN  ubicacion u ON u.CodigoDANE = s.cod_mun_sede
						WHERE s.cod_sede IN $sedesIn ";
	$respuestaZona = $Link->query($consultaZona) or die ('Erro al consultar las ciudades ' . mysqli_error($Link));
	if ($respuestaZona->num_rows > 0) {
		while ($dataZona = $respuestaZona->fetch_assoc()) {
			$zona .= $dataZona['ciudad'] . ", ";
		}
	}
	$zona = trim($zona, ", ");	
	$zona = substr($zona, 0, 50);									
}

$complemento = '';
$consultaComplemento = " SELECT DISTINCT(Complemento) AS complemento FROM $insumosmov WHERE BodegaDestino IN $sedesIn ";
$respuestaComplemento = $Link->query($consultaComplemento) or die ('Error al consultar el tipo de complemento ' . mysqli_error($Link));
if ($respuestaComplemento->num_rows > 0) {
	while ($dataComplemento = $respuestaComplemento->fetch_assoc()) {
		$complemento .= $dataComplemento['complemento']. ", ";
	}
}
$complemento = trim($complemento, ", ");

$filasMostradas = 0;
$pdf->setData($Despacho['FechaMYSQL'], $dpto, $dataSede, $coberturaEtarios, $maxEstudiantes, $gruposEtarios, $mesImprimir, $complemento, $entrega, $tipoComplementoDespacho, $contrato, $numeroManipuladoras, $nombreMesTabla, $zona);
$pdf->AddPage();
$pdf->SetFont('Arial','',$fuenteFilasItems);
$consultaConsolidado = "SELECT 	Descripcion, 
								SUM(CantU2) AS total, 
								Umedida FROM $insumosmovdet 
						WHERE numero IN $numeroIn 
						GROUP BY CodigoProducto"; 
$respuestaConsolidado = $Link->query($consultaConsolidado) or die ('Error al consultar el consolidado ' . mysqli_error($Link));
if ($respuestaConsolidado->num_rows > 0) {
	while ($dataConsolidado = $respuestaConsolidado->fetch_assoc()) {
		$filasMostradas++;
		$pdf->Cell(75,$alturaFilasItems,utf8_decode($dataConsolidado['Descripcion']),'BLR',0,'L');
		$pdf->Cell(40,$alturaFilasItems,utf8_decode($dataConsolidado['Umedida']),'BR',0,'C');
		$pdf->Cell(40,$alturaFilasItems,number_format($dataConsolidado['total'],0,',','.'),'BR',0,'C');
		$pdf->Cell(0,$alturaFilasItems,number_format($dataConsolidado['total'],0,',','.' ) ,'BR',1,'C');	
		$cy = $pdf->GetY();
		if($cy > 199){
			$pdf->AddPage();
			$filasMostradas = 0;
		}
	}
}	

$pdf->Ln();
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
$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','B',$fuenteFilasItems);
$pdf->Cell(0,4,utf8_decode('QUIEN RECIBE'),1,0,'C',False);
$pdf->ln();
$pdf->SetXY($current_x, $current_y-0.2);
$pdf->Cell(0,12,'NOMBRE:','LBR',0,'L',False);
$pdf->ln();
				
$cy = $pdf->GetY();
$cx = $pdf->GetX();
				
$pdf->Cell(0,8,'CARGO:','LBR',0,'L',False);
$pdf->ln();
$pdf->Cell(0,8,'FIRMA:','LBR',0,'L',False);

$pdf->Output("INFORME_DESPACHOS_INSUMOS.pdf", "I");
