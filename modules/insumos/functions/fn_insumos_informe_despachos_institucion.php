<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';
require_once '../../../fpdf181/fpdf.php';

//283 de ancho total.

if (isset($_POST['sedes'])) {
	$sedes = $_POST['sedes'];
} else {
	echo "<script>alert('No se ha definido sede.');</script>";
}

if (isset($_POST['tablaMes'])) {
	$tablaMes = $_POST['tablaMes'];
} else {
	echo "<script>alert('No se ha definido mes.');</script>";
}

// $tablaMes = "01";
// $sedes[0] = '16801300001601';
// $sedes[1] = '16801300001602';

$sedeTabla = "sedes".$_SESSION['periodoActual'];
$insumosmov = "insumosmov".$tablaMes.$_SESSION['periodoActual'];
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

		// $fecha=""; 
		// $dpto=""; 
		// $Ciudad=""; 
		// $nom_inst=""; 
		// $nom_sede="";

		function setData($fecha, $dpto, $sedes, $productos, $fontSize, $alturaRenglon, $maxEstudiantes, $maxManipuladoras, $nom_inst, $ciudad_despacho){
			$this->fecha = $fecha;
			$this->dpto = $dpto;
			$this->sedes = $sedes;
			$this->productos = $productos;
			$this->fontSize = $fontSize;
			$this->alturaRenglon = $alturaRenglon;
			$this->maxEstudiantes = $maxEstudiantes;
			$this->maxManipuladoras = $maxManipuladoras;
			$this->nom_inst = $nom_inst;
			$this->ciudad_despacho = $ciudad_despacho;
		}

		function Header()
		{
			$logoInfopae = '../'.$_SESSION['p_Logo ETC'];
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Image($logoInfopae,28,8,100, 15.92,'jpg', '');
		    $this->Cell(171,17,'','TBRL',0,'C');
		    $this->Cell(171,8.5,utf8_decode('PROGRAMA DE ATENCIÓN ESCOLAR'),'TRL',1,'C');
		    $this->Cell(171,8.5,utf8_decode(''),'',0,'C');
		    $this->Cell(171,8.5,utf8_decode('REMISIÓN ENTREGA DE INSUMOS EN INSTITUCIÓN EDUCATIVA'),'BRL',1,'C');
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(25,$this->alturaRenglon,utf8_decode('OPERADOR: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    $this->Cell(120,$this->alturaRenglon,utf8_decode($_SESSION['p_Operador']),'BR',0,'L');
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(40,$this->alturaRenglon,utf8_decode('FECHA DE ELABORACIÓN: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    $this->Cell(58,$this->alturaRenglon,utf8_decode($this->fecha),'BR',0,'L'); //FECHA DE ELABORACIÓN DEL DESPACHO - REEMPLAZAR
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(19,$this->alturaRenglon,utf8_decode('ETC: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    $this->Cell(80,$this->alturaRenglon,utf8_decode($this->dpto),'BR',1,'L'); //DPTO PARAMETRO - REEMPLAZAR

		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(32,$this->alturaRenglon,utf8_decode('MUNICIPIO O VEREDA: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    // $this->Cell(106.5,$this->alturaRenglon,utf8_decode($this->Ciudad),'BR',1,'L'); //MUNICIPIO DE SEDE - REEMPLAZAR
		    $this->Cell(70,$this->alturaRenglon,utf8_decode($this->ciudad_despacho),'BR',0,'L'); //MUNICIPIO DE SEDE - REEMPLAZAR
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(55,$this->alturaRenglon,utf8_decode('INSTITUCIÓN O CENTRO EDUCATIVO: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    // $this->Cell(100,$this->alturaRenglon,utf8_decode($this->nom_inst),'BR',0,'L'); //INSTITUCIÓN - REEMPLAZZAR
		    $this->Cell(100,$this->alturaRenglon,utf8_decode($this->nom_inst),'BR',0,'L'); //INSTITUCIÓN - REEMPLAZZAR
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(32.5,$this->alturaRenglon,utf8_decode('N° MANIPULADORAS: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    // $this->Cell(32.5,$this->alturaRenglon,utf8_decode($this->dataSede['cantidad_Manipuladora']),'BR',0,'L');
		    $this->Cell(10,$this->alturaRenglon,utf8_decode($this->maxManipuladoras),'BR',0,'L');
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(32.5,$this->alturaRenglon,utf8_decode('TOTAL COBERTURA: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    // $this->Cell(32.5,$this->alturaRenglon,utf8_decode($this->maxEstudiantes),'BR',1,'L');
		    $this->Cell(10,$this->alturaRenglon,utf8_decode($this->maxEstudiantes),'BR',1,'L');
    		//Salto de línea
		    $this->Ln(1);
		    $this->SetFont('Arial','B',$this->fontSize);


		    $this->Cell(60.75,15,utf8_decode("NOMBRE DE SEDE "),'TBLR',0,'C');

		    $cntProductos = count($this->productos);
		    $espacio_cu = 245.25 / $cntProductos;

		    $this->SetFont('Arial','B',5);

		    $cy = $this->getY();
		    $cy2 = $this->getY();
		    $cx = $this->getX();
		    $cx2 = $this->getX();
			$maxLength = 30;

		    foreach ($this->productos as $id => $producto) {

		    	$pName = $producto['Descripcion'];

		    	if (strlen($pName) > $maxLength) {
		    		$pName = substr( $pName, 0, $maxLength );
		    		$pName.="...";
		    		$cy = $cy2;
		    	}

		    	$this->SetXY($cx, $cy+2);
		    	$this->MultiCell($espacio_cu,3,utf8_decode($pName),'','C');
		    	$cx += $espacio_cu;
		    	$cy = $cy2;
		    }

		    $this->SetXY($cx2, $cy);

		    foreach ($this->productos as $id => $producto) {
		    	$this->Cell($espacio_cu,15,utf8_decode(""),'TBR',0,'C');
		    }

		    $this->SetFont('Arial','B',$this->fontSize);


		    $this->Cell(36,15,utf8_decode("Firma"),'TBR',0,'C');


		    $this->Ln();

		}

	}

	$dataSede = [];
	$productos = [];
	$productos_sede = [];
	$maxEstudiantes = 0;
	$maxManipuladoras = 0;

	$pdf = new PDF('L', 'mm', 'Legal');
	$pdf->AliasNbPages();
	$pdf->SetMargins(7, 7);

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
		$dataSede[$sede] = $resultadoSede->fetch_assoc();
		$maxManipuladoras += $dataSede[$sede]['cantidad_Manipuladora'];
		if(!isset($nom_inst)){
			$nom_inst = $dataSede[$sede]['nom_inst'];
		}

		if(!isset($ciudad_despacho)){
			$ciudad_despacho = $dataSede[$sede]['Ciudad'];
		}
	}

	$sumaCoberturasEtario = "";

	foreach ($gruposEtarios as $ID => $DESCRIPCION) {
		$sumaCoberturasEtario.=" MAX(Etario".$ID."_APS+Etario".$ID."_CAJMRI+Etario".$ID."_CAJTRI+Etario".$ID."_CAJMPS) as Etario".$ID." ,";
    }

    $sumaCoberturasEtario = trim($sumaCoberturasEtario, " ,");


	$coberturaEtarios = [];
	$consultaEtariosCobertura = "SELECT MAX(APS) + MAX(CAJMPS)+ MAX(CAJMRI)+ MAX(CAJTRI) AS cant_Estudiantes, $sumaCoberturasEtario FROM sedes_cobertura WHERE cod_sede = '".$sede."' and mes= '".$tablaMes."' GROUP BY cod_sede";
	$resultadoEtariosCobertura = $Link->query($consultaEtariosCobertura);
	if ($resultadoEtariosCobertura->num_rows > 0) {
		if ($EtariosCobertura = $resultadoEtariosCobertura->fetch_assoc()) {
			$coberturaEtarios["Etario1"] = $EtariosCobertura["Etario1"];
			$coberturaEtarios["Etario2"] = $EtariosCobertura["Etario2"];
			$coberturaEtarios["Etario3"] = $EtariosCobertura["Etario3"];
			$maxEstudiantes += $EtariosCobertura["cant_Estudiantes"];
		}
	}

	$consultaDespacho = "SELECT * FROM $insumosmov WHERE BodegaDestino = '".$sede."'";
	$resultadoDespacho = $Link->query($consultaDespacho);
	if ($resultadoDespacho->num_rows > 0) {
		while ($Despacho = $resultadoDespacho->fetch_assoc()) {
		    $consultaDetalles = "SELECT producto.id as pId, producto.NombreUnidad1, producto.NombreUnidad2, producto.NombreUnidad3, producto.NombreUnidad4, producto.NombreUnidad5, producto.CantidadUnd2, insmovdet.* FROM $insumosmovdet AS insmovdet
		    					INNER JOIN productos".$_SESSION['periodoActual']." as producto ON producto.Codigo = insmovdet.CodigoProducto
		     					WHERE insmovdet.Numero = '".$Despacho['Numero']."'";
		    $resultadoDetalles = $Link->query($consultaDetalles);
		    if ($resultadoDetalles->num_rows > 0) {
		    	while ($detalles = $resultadoDetalles->fetch_assoc()) {
		    		if (!isset($productos[$detalles['pId']])) {
		    			$productos[$detalles['pId']] = $detalles;
		    		}

		    		if (!isset($fecha_despacho)) {
		    			$fecha_despacho = $Despacho['FechaMYSQL'];
		    		}

		    		$productos_sede[$sede][$detalles['pId']] = $detalles;
		    	}
		    }
		}
	}	
}

$fontSize = 7;
$alturaRenglon = 6;

$pdf->setData($fecha_despacho, $dpto, $dataSede, $productos, $fontSize, $alturaRenglon, $maxEstudiantes, $maxManipuladoras, $nom_inst, $ciudad_despacho);
$pdf->AddPage();
$pdf->SetFont('Arial','',$fontSize);

$cntProductos = count($productos);
$espacio_cu = 245.25 / $cntProductos;

foreach ($dataSede as $cod_sede => $sede) {

	$sName = $sede['nom_sede'];

	if (strlen($sName) > 35) {
		$sName = substr( $sName, 0, 35);
		$sName.="...";
	}

	$pdf->Cell(60.75,$alturaRenglon,utf8_decode($sName),'LBR',0,'C',False);

	foreach ($productos as $id => $producto) {
		if (isset($productos_sede[$cod_sede][$id])) {
			$pdf->Cell($espacio_cu,$alturaRenglon,utf8_decode(
			(number_format($productos_sede[$cod_sede][$id]['NombreUnidad1'] == 'u' ? ceil($productos_sede[$cod_sede][$id]['CanTotalPresentacion']) : strpos($productos_sede[$cod_sede][$id]['NombreUnidad2'], 'kg') || strpos($productos_sede[$cod_sede][$id]['NombreUnidad2'], 'lt') ? ceil($productos_sede[$cod_sede][$id]['CanTotalPresentacion']) : $productos_sede[$cod_sede][$id]['CanTotalPresentacion'], 2, '.', ','))
			) ,'BR',0,'C');
		} else {
			$pdf->Cell($espacio_cu,$alturaRenglon,utf8_decode("-") ,'BR',0,'C');
		}
	}

	$pdf->Cell(36,$alturaRenglon,"",'BR',0,'C',False);
	$pdf->ln();
}

$pdf->ln();

$cy = $pdf->GetY();

if($cy > 155){
	$pdf->AddPage();
}

// $pdf->ln();

// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();

// $pdf->SetXY($current_x, $current_y);


// $pdf->Cell(94.3,4,'',1,0,'L',False);
// $pdf->ln();

// $pdf->Cell(94.3,12,'',1,0,'L',False);
// $pdf->ln();

// $cy = $pdf->GetY();
// $cx = $pdf->GetX();
// $pdf->Cell(94.3,8,'',1,0,'L',False);
// $pdf->SetXY($cx, $cy+8);
// $pdf->Cell(94.3,8,'',1,0,'L',False);
// $pdf->ln();

// $pdf->SetXY($current_x, $current_y);

// $pdf->Cell(94.3,4,utf8_decode('INSTITUCIÓN EDUCATIVA'),0,0,'C',False);
// $pdf->ln();

// $pdf->SetXY($current_x, $current_y-0.2);

// $pdf->Cell(94.3,12,'NOMBRE RESPONSABLE INSTITUCION O CENTRO EDUCATIVO:',0,0,'L',False);
// $pdf->ln();

// $cy = $pdf->GetY();
// $cx = $pdf->GetX();

// $pdf->SetXY($cx, $cy+2);
// $pdf->Cell(94.3,8,'CARGO:',0,0,'L',False);
// $pdf->SetXY($cx, $cy+10);
// $pdf->Cell(94.3,8,'FIRMA:',0,0,'L',False);

// $pdf->ln();

$current_x = $pdf->getX();
$current_y = $pdf->getY();

$pdf->Cell(150,4,"","TBLR",1,'C',False);
$pdf->Cell(150,12,"","BLR",1,'C',False);
$pdf->Cell(150,8,"","BLR",1,'C',False);
$pdf->Cell(150,8,"","BLR",1,'C',False);

$pdf->SetXY($current_x, $current_y);
$pdf->SetFont('Arial','',7);
$pdf->Cell(150,4,utf8_decode("INSTITUCIÓN EDUCATIVA"),0,1,'L',False);
$pdf->Cell(150,4,utf8_decode("NOMBRE RESPONSABLE INSTITUCION O CENTRO EDUCATIVO:"),0,1,'L',False);
$pdf->Cell(150,4,utf8_decode(""),0,1,'L',False);
$pdf->Cell(150,4,utf8_decode("DOCUMENTO:"),0,1,'L',False);
$pdf->Cell(150,4,utf8_decode("CARGO:"),0,1,'L',False);
$pdf->Cell(150,4,utf8_decode(""),0,1,'L',False);
$pdf->Cell(150,4,utf8_decode("FIRMA:"),0,1,'L',False);
$pdf->Cell(150,4,utf8_decode(""),0,1,'L',False);





$pdf->Output("INFORME_DESPACHOS_INSUMOS.pdf", "I");
