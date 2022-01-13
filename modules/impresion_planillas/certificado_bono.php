<?php 
error_reporting(E_ALL);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

include '../../config.php';
require_once '../../autentication.php';
require 'pagegroup.php';
require_once '../../db/conexion.php';
include '../../php/funciones.php';

$tablaAnno = $_SESSION['periodoActual'];
$tablaAnnoCompleto = $_SESSION['periodoActualCompleto'];
$sangria = " - ";
$tamannoFuente = 6;

$hoy = date("d/m/Y");
$fechaDespacho = $hoy;

// Se va a recuperar el mes y el año para las tablaMesAnno
$mesAnno = '';
$mes = $_POST['meses'];
$sede = $_POST['sedes'];

$anno = $tablaAnnoCompleto;
$anno = substr($anno, -2);
$anno = trim($anno);
$mesAnno = $mes.$anno;

$corteDeVariables = 16;
if(isset($_POST['observaciones'])){
	$paginasObservaciones = $_POST['observaciones'];
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

if(isset($_POST['hojaNovedades'])){
	$hojaNovedades = $_POST['hojaNovedades'];
	$corteDeVariables++;
}

$annoActual = $tablaAnnoCompleto;
$codigoSedes = [];
$semanas = [];
$inner = '';
$beneficiarios = [];
$sedes = [];
$nombreMesEntrega = '';
$entrega = '';
$sedesFalse = [];
$sedesBono = '';


if ($sede == "0") {
	$institucion = $_POST['instituciones'];
	$consultaSedes = "SELECT cod_sede FROM sedes".$_SESSION['periodoActual']. " WHERE cod_inst = " .$institucion. ";"; 
	$respuestaSedes = $Link->query($consultaSedes) or die('Error al consultar el codigo de las sedes' . mysqli_error($Link));
	if ($respuestaSedes->num_rows > 0) {
		while ($dataSedes = $respuestaSedes->fetch_assoc()) {
			$codigoSedes[] = $dataSedes['cod_sede'];
		}
	}
} elseif($sede != 0){
	$codigoSedes[] = $sede;
}

$consultaSemanas = "SELECT DISTINCT(semana) as semana FROM planilla_semanas WHERE mes = ".$mes.";";
$respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar las semanas ' . mysqli_error($Link));
if ($respuestaSemanas->num_rows > 0) {
	while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
		$semanas[] = $dataSemanas['semana'];
	}
}

$semanas2 = array_reverse($semanas);
foreach ($semanas2 as $key => $semana) {
	// exit(var_dump(strpos($semana, "b")));
	if (strpos($semana, "b")) {
		$semanaB = substr($semana,0,2);
		$semanaAnterior = $semanaB;
		if ($semanaAnterior < 10) {
			$semanaAnteriorString = "0".$semanaAnterior;
		}else if ($semanaAnterior >= 10) {
			$semanaAnteriorString = $semanaAnterior;
		}
		
	}else if (strpos($semana, "b") === false) {
		$semanaAnterior = $semana-1;
		if ($semanaAnterior < 10) {
			$semanaAnteriorString = "0".$semanaAnterior;
		}
		else if($semanaAnterior >= 10){
			$semanaAnteriorString = $semanaAnterior;
		}		
	}
	if (in_array($semanaAnteriorString, $semanas2)) {
		$inner .= ' INNER JOIN focalizacion'.$semanaAnteriorString. ' ON focalizacion'.$semanaAnteriorString. '.num_doc = focalizacion'.$semana.".num_doc ";  
	}
}
$semanas3 = $semanas;
$ultimaSemana = array_pop($semanas);	

// vamos a buscar el numero de la entrega que va a estar junto al mes 
$consultaEntrega = "SELECT NumeroEntrega FROM planilla_dias WHERE mes = $mes;";
$respuestaEntrega = $Link->query($consultaEntrega) or die('Error al consultar el numero de la entrega' . mysqli_error($Link));
if ($respuestaEntrega->num_rows>0) {
	$dataEntrega = $respuestaEntrega->fetch_assoc();
	$entrega = $dataEntrega['NumeroEntrega'];
}

// vamos a consultar el nombre de la mes de entrega de planilla dias
$consultaMesEntrega = "SELECT NombreMes FROM planilla_dias WHERE mes = $mes;";
$respuestaMesEntrega = $Link->query($consultaMesEntrega) or die('Error al consultar el mes de la entrega' . mysqli_error($Link));
if ($respuestaMesEntrega->num_rows > 0) {
 	$dataMesEntrega = $respuestaMesEntrega->fetch_assoc();
 	$nombreMesEntrega = $dataMesEntrega['NombreMes'];
 } 

// Declaración de caracteristicas del PDF
//class PDF extends FPDF{
class PDF extends PDF_PageGroup{
	function Header(){}
	function Footer(){

		$tamannoFuente = 6;
		$this->Ln(2);
		$this->SetFont('Arial','B',$tamannoFuente);
		$this->Cell(18,9,utf8_decode("Observaciones:"),'BLT',0,'L',False);
		
		$this->SetFont('Arial','',$tamannoFuente);
		$cx = $this->getX();
		$cy = $this->getY();
		$this->Cell(0,9,utf8_decode(''),'BLTR',0,'L',False);
		$this->setXY($cx, $cy);
		$this->Cell(0,3,utf8_decode($_SESSION['observacionesDespachos']),0,0,'L',False);

		$this->SetFont('Arial','B',$tamannoFuente);
		$this->Ln(11);
		$this->Cell(46,4,utf8_decode("Firma de quien entrega el Bono Alimentario:"),0,0,'L',False);
		$this->Cell(54,4,utf8_decode(""),'B',0,'C',False);
		$this->Cell(43,4,utf8_decode(""),0,0,'C',False);
		$this->Cell(38,4,utf8_decode("Firma Rector o Representante CAE:"),0,0,'L',False);
		$this->Cell(93,4,utf8_decode(""),'B',0,'C',False);
		
		$this->Ln(7);
		$this->Cell(35,4,utf8_decode("Nombre legible de quien entrega:"),0,0,'L',False);
		$this->Cell(65,4,utf8_decode(""),'B',0,'C',False);
		$this->Cell(43,4,utf8_decode(""),0,0,'C',False);
		$this->Cell(47,4,utf8_decode("Nombre legible Rector o Representante CAE:"),0,0,'L',False);
		$this->Cell(84,4,utf8_decode(""),'B',0,'C',False);
		
		$this->Ln(7);
		$this->Cell(18,4,utf8_decode("Cargo / función:"),0,0,'L',False);
		$this->Cell(25,4,utf8_decode(""),'B',0,'C',False);
		$this->Cell(3,4,utf8_decode(""),0,0,'C',False);
		$this->Cell(21,4,utf8_decode("Número telefónico:"),0,0,'L',False);
		$this->Cell(33,4,utf8_decode(""),'B',0,'C',False);
		$this->Cell(43,4,utf8_decode(""),0,0,'C',False);
				
		$this->Cell(18,4,utf8_decode("Cargo / función:"),0,0,'L',False);
		$this->Cell(41,4,utf8_decode(""),'B',0,'C',False);
		
		$this->Cell(13,4,utf8_decode(""),0,0,'C',False);
		
		$this->Cell(23,4,utf8_decode("Número telefónico:"),0,0,'L',False);
		$this->Cell(36,4,utf8_decode(""),'B',0,'C',False);
			
		$this->Ln(3.9);
		$this->Cell(125,4,utf8_decode(""),0,0,'C',False);
		$this->Cell(0,10,utf8_decode("Impreso por: InfoPAE Versión ".$_SESSION['periodoActualCompleto']." www.infopae.com.co"),0,0,'L',False);

	}

	var $angle=0;
	function Rotate($angle, $x=-1, $y=-1)
	{
			if($x==-1)
					$x=$this->x;
			if($y==-1)
					$y=$this->y;
			if($this->angle!=0)
					$this->_out('Q');
			$this->angle=$angle;
			if($angle!=0)
			{
					$angle*=M_PI/180;
					$c=cos($angle);
					$s=sin($angle);
					$cx=$x*$this->k;
					$cy=($this->h-$y)*$this->k;
					$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
			}
	}

	function Rotate_text($x, $y, $txt, $angle)
	{
		//Text rotated around its origin
		$this->Rotate($angle, $x, $y);
		$this->Text($x, $y, $txt);
		$this->Rotate(0);
	}
}

//CREACION DEL PDF
// Creación del objeto de la clase heredada
$pdf= new PDF('L','mm',array(330, 216));
$pdf->StartPageGroup();

$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(TRUE, 30);
$pdf->AliasNbPages();
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(.05);
$pdf->SetFont('Arial','',$tamannoFuente);


// empezamos a recorrer todas las sedes 
foreach ($codigoSedes as $key => $codigoSede) {
	$where = 'WHERE ';
	$pdf->StartPageGroup();
	$filaActual = 1;
	$consultaSedes = "SELECT u.Ciudad as municipio, s.nom_inst as institucion, s.nom_sede as sede, s.cod_sede as codigo, s.direccion as direccion, s.sector as sector FROM sedes".$_SESSION['periodoActual']." s INNER JOIN ubicacion u ON u.codigoDANE = s.cod_mun_sede WHERE cod_sede = " .$codigoSede.";";
	$respuestaSedes = $Link->query($consultaSedes) or die ('Error al consultar las sedes educativas ' . mysqli_error($Link));
	if ($respuestaSedes->num_rows > 0) {
		$dataSedes = $respuestaSedes->fetch_assoc();
		$sedes = $dataSedes;
	}

	foreach ($semanas3 as $key => $semana) {
		if ($semana != $ultimaSemana) {
			$where .= "focalizacion".$semana. ".cod_sede = " .$codigoSede. " AND focalizacion".$semana.".Tipo_complemento = 'BONO' OR ";
		}
		else if ($semana == $ultimaSemana){
			$where .= "focalizacion".$semana. ".cod_sede = " .$codigoSede. " AND focalizacion".$semana.".Tipo_complemento = 'BONO'";
		}
	}

	$consultaFocalizacion = "SELECT CONCAT(focalizacion".$ultimaSemana.".ape1,\" \", focalizacion".$ultimaSemana.".ape2, \" \", focalizacion".$ultimaSemana.".nom1,\" \", focalizacion".$ultimaSemana.".nom2) as nombre, focalizacion".$ultimaSemana.".num_doc, focalizacion".$ultimaSemana.".cod_grado, focalizacion".$ultimaSemana.".nom_acudiente, focalizacion".$ultimaSemana.".doc_acudiente, focalizacion".$ultimaSemana.".parentesco_acudiente, focalizacion".$ultimaSemana.".tel_acudiente FROM focalizacion".$ultimaSemana .$inner. $where . " ORDER BY focalizacion".$ultimaSemana.".cod_grado ASC, focalizacion".$ultimaSemana.".nom_grupo ASC, focalizacion".$ultimaSemana.".ape1 ASC, focalizacion".$ultimaSemana.".ape2 ASC, focalizacion".$ultimaSemana.".nom1 ASC, focalizacion".$ultimaSemana.".nom2 ASC;";
	// exit(var_dump($consultaFocalizacion));
	include 'certificado_bono_beneficiarios.php';

		/* INICIA PAGINA ADICIONAL */
	//var_dump($paginasObservaciones);
	if($hojaNovedades > 0 && $sedesBono == 'True'){
		for ($aaa=0; $aaa < $hojaNovedades; $aaa++) { 
			$pdf->StartPageGroup();
			$pdf->AddPage();
			$tamannoFuente = 6;
			include 'certificado_bono_header_adicional.php';
			for ($jj=0; $jj < 15; $jj++) { 
				$pdf->Cell(4,$altoFila,'','BL',0,'C',False);
				$pdf->Cell(42,$altoFila,'','BL',0,'L',False);
				$pdf->Cell(17,$altoFila,'','BL',0,'C',False);
				$pdf->Cell(20,$altoFila,'','BL',0,'C',False);
				$pdf->Cell(3.25,$altoFila,'','BL',0,'C',False);
				$pdf->Cell(23,$altoFila,utf8_decode(""),'BL',0,'C',False);
				$pdf->Cell(23,$altoFila,utf8_decode(""),'BL',0,'C',False);
				$pdf->Cell(23,$altoFila,utf8_decode(""),'BL',0,'C',False);
				$pdf->Cell(46,$altoFila,utf8_decode(""),'BL',0,'C',False);
				$pdf->Cell(30,$altoFila,utf8_decode(""),'BLR',0,'C',False);
				$pdf->Cell(20,$altoFila,utf8_decode(""),'BLR',0,'C',False);
				$pdf->Cell(30,$altoFila,utf8_decode(""),'BLR',0,'C',False);
				$pdf->Cell(0,$altoFila,utf8_decode(""),'BLR',0,'C',False);

				$pdf->Ln($altoFila);
			}
		}
	}
	/* TERMINA PAGINA ADICIONAL */
}

if (in_array('True', $sedesFalse) === false) {
	echo "<script>alert('Las sedes educativas No tienen registro de BONO Alimentario');</script>";
	exit();
}

$pdf->Output();