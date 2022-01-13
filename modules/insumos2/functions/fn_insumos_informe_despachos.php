<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';
require_once '../../../fpdf181/fpdf.php';

// exit(var_dump($_POST));
//283 de ancho total.

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

//var_dump($_POST);
$paginasObservaciones = "";
if(isset($_POST['paginasObservaciones']) && $_POST['paginasObservaciones'] != ""){
	$paginasObservaciones = $_POST['paginasObservaciones'];
}





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

		function setData($fecha, $dpto, $dataSede, $coberturaEtarios, $maxEstudiantes, $gruposEtarios, $mes, $tipoComplemento){
			$this->fecha = $fecha;
			// setlocale(LC_TIME, 'es_CO');
			// $fecha = DateTime::createFromFormat('!m', $mes);
			// $mes = ucfirst(strftime("%B", $fecha->getTimestamp())); // marzo
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
		}

		function Header()
		{
			$logoInfopae = '../'.$_SESSION['p_Logo ETC'];
		    $this->SetFont('Arial','B',10);
		    $this->Image($logoInfopae,28,8,100, 15.92,'jpg', '');
		    $this->Cell(141.5,17,'','TBRL',0,'C');
		    $this->Cell(141.5,8.5,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),'TRL',1,'C');
		    $this->Cell(141.5,8.5,utf8_decode(''),'',0,'C');
		    $this->Cell(141.5,8.5,utf8_decode('REMISIÓN ENTREGA DE INSUMOS EN INSTITUCIÓN EDUCATIVA'),'BRL',1,'C');
		    $this->SetFont('Arial','B',8);
		    $this->Cell(25,4,utf8_decode('OPERADOR: '),'BL',0,'L');
		    $this->SetFont('Arial','',8);
		    $this->Cell(160,4,utf8_decode($_SESSION['p_Operador']),'BR',0,'L');
		    $this->SetFont('Arial','B',8);
		    $this->Cell(40,4,utf8_decode('MES: '),'BL',0,'C');
		    $this->SetFont('Arial','',8);
		    $this->Cell(58,4,utf8_decode($this->mes),'BR',1,'L'); //FECHA DE ELABORACIÓN DEL DESPACHO - REEMPLAZAR
		    $this->SetFont('Arial','B',8);
		    $this->Cell(14.1,4,utf8_decode('ETC: '),'BL',0,'L');
		    $this->SetFont('Arial','',8);
		    $this->Cell(127.4,4,utf8_decode($this->dpto),'BR',0,'L'); //DPTO PARAMETRO - REEMPLAZAR
		    $this->SetFont('Arial','B',8);
		    $this->Cell(35,4,utf8_decode('MUNICIPIO O VEREDA: '),'BL',0,'L');
		    $this->SetFont('Arial','',8);
		    $this->Cell(106.5,4,utf8_decode($this->Ciudad),'BR',1,'L'); //MUNICIPIO DE SEDE - REEMPLAZAR
		    $this->SetFont('Arial','B',8);
		    $this->Cell(53,4,utf8_decode('INSTITUCIÓN O CENTRO EDUCATIVO: '),'BL',0,'L');
		    $this->SetFont('Arial','',8);
		    $this->Cell(100,4,utf8_decode($this->nom_inst),'BR',0,'L'); //INSTITUCIÓN - REEMPLAZZAR
		    $this->SetFont('Arial','B',8);
		    $this->Cell(30,4,utf8_decode('SEDE EDUCATIVA: '),'BL',0,'L');
		    $this->SetFont('Arial','',8);
		    $this->Cell(100,4,utf8_decode($this->nom_sede),'BR',1,'L'); //SEDE REEMPLAZAR
		    // Salto de línea
		    $this->Ln(1);
		    $this->SetFont('Arial','B',8);
		    $this->Cell(61.94,10,utf8_decode('RANGO DE EDAD'),'TBLR',0,'C');
		    $this->Cell(61.94,10,utf8_decode('N° DE RACIONES ADJUDICADAS'),'TBLR',0,'C');
		    $this->Cell(61.94,10,utf8_decode('N° DE RACIONES ATENDIDAS'),'TBLR',0,'C');

		    $cx = $this->getX();
		    $cy = $this->getY();
		    $this->Cell(32.4,10,utf8_decode(''),'TBR',0,'C');
		    $this->setXY($cx, $cy);
		    $this->MultiCell(32.4, 5, utf8_decode('N° DE MANIPULADORAS'), 0, 'C');
		    $this->setXY($cx+32.4, $cy);

		    $cx = $this->getX();
		    $cy = $this->getY();
		    $this->Cell(32.4,10,utf8_decode(''),'TBR',0,'C');
		    $this->setXY($cx, $cy);
		    $this->MultiCell(32.4, 5, utf8_decode('TOTAL COBERTURA'), 0, 'C');
		    $this->setXY($cx+32.4, $cy);

			$cx = $this->getX();
		    $cy = $this->getY();
		    $this->Cell(32.4,10,utf8_decode(''),'TBR',0,'C');
		    $this->setXY($cx, $cy);
		    $this->MultiCell(32.4, 5, utf8_decode('TIPO COMPLEMENTO'), 0, 'C');
		    // $this->setXY($cx+32.4, $cy);
		    $this->Ln();

		    $this->SetFont('Arial','',8);

		    foreach ($this->gruposEtarios as $ID => $DESCRIPCION) {
		    	$this->Cell(61.94,6,utf8_decode($DESCRIPCION),'BLR',0,'C');
		    	$this->Cell(61.94,6,utf8_decode(isset($this->coberturaEtarios['Etario'.$ID]) ? $this->coberturaEtarios['Etario'.$ID] : 0),'BLR',0,'C');//SEDES COBERTURA POR GRUPO ETARIO
		    	$this->Cell(61.94,6,utf8_decode(isset($this->coberturaEtarios['Etario'.$ID]) ? $this->coberturaEtarios['Etario'.$ID] : 0),'BLR',1,'C');//SEDES COBERTURA POR GRUPO ETARIO
    		}
    		$this->SetXY(192.8, 47);

    		if ($this->tipoComplemento == 'APS') {
    			$manipuladoras = $this->dataSede['Manipuladora_APS'];
    		} else if ($this->tipoComplemento == 'CAJMPS') {
    			$manipuladoras = $this->dataSede['Manipuladora_CAJMPS'];
    		} else if ($this->tipoComplemento == 'CAJMRI') {
    			$manipuladoras = $this->dataSede['Manipuladora_CAJMRI'];
    		} else if ($this->tipoComplemento == 'CAJTRI') {
    			$manipuladoras = $this->dataSede['Manipuladora_CAJTRI'];
    		} else {
    			$manipuladoras = $this->dataSede['cantidad_Manipuladora'];
    		}

    		$this->Cell(32.4,18,utf8_decode($manipuladoras),'TBR',0,'C');//MANIPULADORAS DE LA SEDE
    		$this->Cell(32.4,18,utf8_decode($this->maxEstudiantes),'TBR',0,'C');//CANTIDAD COBERTURA
    		$this->Cell(32.4,18,utf8_decode($this->tipoComplemento),'TBR',1,'C');//CANTIDAD COBERTURA
    		//Salto de línea
		    $this->Ln(1);
		    $this->SetFont('Arial','B',8);
		    $this->Cell(70.75,12,utf8_decode("INSUMO"),'TBLR',0,'C');
		    $this->Cell(30,12,utf8_decode("UNIDAD MEDIDA"),'TBR',0,'C');
		    $this->Cell(30,12,utf8_decode("TOTAL REQUERIDO"),'TBR',0,'C');
		    $this->Cell(38,6,utf8_decode("CANTIDAD ENTREGADA"),'TBLR',0,'C');
		    $this->Cell(39.2,6,utf8_decode("ESPECIFICACIÓN CALIDAD"),'TBR',0,'C');
		    $this->Cell(37,6,utf8_decode("FALTANTES"),'TBR',0,'C');
		    $this->Cell(38,6,utf8_decode("DEVOLUCIÓN"),'TBR',1,'C');
		    $this->SetXY(137.95, 72);
		    $this->Cell(12.6,6,utf8_decode("TOTAL"),'BR',0,'C');
		    $this->Cell(12.6,6,utf8_decode("C"),'BR',0,'C');
		    $this->Cell(12.6,6,utf8_decode("NC"),'BR',0,'C');
		    $this->Cell(19.6,6,utf8_decode("C"),'BR',0,'C');
		    $this->Cell(19.6,6,utf8_decode("NC"),'BR',0,'C');
		    $this->Cell(12.3,6,utf8_decode("SI"),'BR',0,'C');
		    $this->Cell(12.35,6,utf8_decode("NO"),'BR',0,'C');
		    $this->Cell(12.3,6,utf8_decode("CANT"),'BR',0,'C');
		    $this->Cell(12.67,6,utf8_decode("SI"),'BR',0,'C');
		    $this->Cell(12.67,6,utf8_decode("NO"),'BR',0,'C');
		    $this->Cell(12.67,6,utf8_decode("CANT"),'BR',1,'C');
		}

	}

	$pdf = new PDF('L', 'mm', 'A4');
	$pdf->AliasNbPages();
	$pdf->SetMargins(7, 7);

	// exit(var_dump($sedes));


$alturaFilasItems = 4;
$fuenteFilasItems = 6;


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

				$pdf->setData($Despacho['FechaMYSQL'], $dpto, $dataSede, $coberturaEtarios, $maxEstudiantes, $gruposEtarios, $tablaMes, $tipoComplemento);
				$pdf->AddPage();
			    $pdf->SetFont('Arial','',$fuenteFilasItems);
			    //PRODUCTOS
			    $consultaDetalles = "SELECT producto.NombreUnidad1, producto.NombreUnidad2, producto.NombreUnidad3, producto.NombreUnidad4, producto.NombreUnidad5, producto.CantidadUnd2, insmovdet.* FROM $insumosmovdet AS insmovdet
			    					INNER JOIN productos".$_SESSION['periodoActual']." as producto ON producto.Codigo = insmovdet.CodigoProducto
			     					WHERE insmovdet.Numero = '".$Despacho['Numero']."'";
			    $resultadoDetalles = $Link->query($consultaDetalles);
			    if ($resultadoDetalles->num_rows > 0) {
			    	while ($detalles = $resultadoDetalles->fetch_assoc()) {

			    		if ($detalles['CantU3'] != 0 || $detalles['CantU4'] != 0 || $detalles['CantU5'] != 0) { //SI SE DIERON MÁS PRESENTACIONES
			    			$pdf->Cell(70.75,$alturaFilasItems,utf8_decode($detalles['Descripcion']),'BLR',0,'L');
				    		$pdf->Cell(30,$alturaFilasItems,utf8_decode($detalles['Umedida']),'BR',0,'C');
				    		$pdf->Cell(30,$alturaFilasItems,utf8_decode(round($detalles['Cantidad']/1000, 2)),'BR',0,'C');
				    		$pdf->Cell(12.8,$alturaFilasItems,utf8_decode(number_format($detalles['CanTotalPresentacion'], 3, '.', ',')),'BR',0,'C');
				    		$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.35,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',1,'C');

			    			if ($detalles['CantU2'] != 0 ) {
			    				$pdf->Cell(70.75,$alturaFilasItems,utf8_decode("    ".$detalles['Descripcion']." ".$detalles['NombreUnidad2']),'BLR',0,'L');
				    			$pdf->Cell(30,$alturaFilasItems,'','BR',0,'C');
				    			$pdf->Cell(30,$alturaFilasItems,'','BR',0,'C');
				    			$pdf->Cell(12.8,$alturaFilasItems,utf8_decode(number_format($detalles['CantU2'], 0)),'BR',0,'C');
				    			$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.35,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',1,'C');
			    			}
			    			if ($detalles['CantU3'] != 0 ) {
			    				$pdf->Cell(70.75,$alturaFilasItems,utf8_decode("    ".$detalles['Descripcion']." ".$detalles['NombreUnidad3']),'BLR',0,'L');
				    			$pdf->Cell(30,$alturaFilasItems,'','BR',0,'C');
				    			$pdf->Cell(30,$alturaFilasItems,'','BR',0,'C');
				    			$pdf->Cell(12.8,$alturaFilasItems,utf8_decode(number_format($detalles['CantU3'], 0)),'BR',0,'C');
				    			$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.35,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',1,'C');
			    			}

			    			if ($detalles['CantU4'] != 0 ) {
			    				$pdf->Cell(70.75,$alturaFilasItems,utf8_decode("    ".$detalles['Descripcion']." ".$detalles['NombreUnidad4']),'BLR',0,'L');
				    			$pdf->Cell(30,$alturaFilasItems,'','BR',0,'C');
				    			$pdf->Cell(30,$alturaFilasItems,'','BR',0,'C');
				    			$pdf->Cell(12.8,$alturaFilasItems,utf8_decode(number_format($detalles['CantU4'], 0)),'BR',0,'C');
				    			$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.35,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',1,'C');
			    			}

			    			if ($detalles['CantU5'] != 0 ) {
			    				$pdf->Cell(70.75,$alturaFilasItems,utf8_decode("    ".$detalles['Descripcion']." ".$detalles['NombreUnidad5']),'BLR',0,'L');
				    			$pdf->Cell(30,$alturaFilasItems,'','BR',0,'C');
				    			$pdf->Cell(30,$alturaFilasItems,'','BR',0,'C');
				    			$pdf->Cell(12.8,$alturaFilasItems,utf8_decode(number_format($detalles['CantU5'], 0)),'BR',0,'C');
				    			$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.35,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
					    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',1,'C');
			    			}
			    		} else {
			    			$pdf->Cell(70.75,$alturaFilasItems,utf8_decode($detalles['Descripcion']),'BLR',0,'L');
				    		$pdf->Cell(30,$alturaFilasItems,utf8_decode($detalles['Umedida']),'BR',0,'C');
				    		$pdf->Cell(30,$alturaFilasItems,utf8_decode(number_format($detalles['CanTotalPresentacion'], 2, '.', ',')),'BR',0,'C');
				    		$pdf->Cell(12.8,$alturaFilasItems,utf8_decode((number_format($detalles['NombreUnidad1'] == 'u' ? ceil($detalles['CanTotalPresentacion']) : strpos($detalles['NombreUnidad2'], 'kg') || strpos($detalles['NombreUnidad2'], 'lt') ? ceil($detalles['CanTotalPresentacion']) : $detalles['CanTotalPresentacion'], 2, '.', ','))) ,'BR',0,'C');
				    		$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(19.6,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.35,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.3,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
				    		$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',0,'C');
							$pdf->Cell(12.67,$alturaFilasItems,utf8_decode(''),'BR',1,'C');
			    		}
			    	}
				}
				// $cy = $pdf->GetY();
				// $pdf->Cell(0,$alturaFilasItems,utf8_decode($cy),'LRB',1,'L');
				// $cy = $pdf->GetY();
				// $pdf->Cell(0,$alturaFilasItems,utf8_decode($cy),'LRB',1,'L');
				// $cy = $pdf->GetY();
				// $pdf->Cell(0,$alturaFilasItems,utf8_decode($cy),'LRB',1,'L');


				// $cy = $pdf->GetY();
				// $pdf->Cell(0,$alturaFilasItems,utf8_decode($cy),'LRB',1,'L');

				$cy = $pdf->GetY();
				if($cy > 155){
					$pdf->AddPage();
				}

				$pdf->ln();



				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(0,5,'C: Cumple  NC: No Cumple',0,5,'L',False);
				$pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(0,5,$paginasObservaciones,'B',5,'L',False);
				$pdf->Ln(2);










				$current_y = $pdf->GetY();
				$current_x = $pdf->GetX();

				$pdf->SetXY($current_x, $current_y);


				$pdf->Cell(94.3,4,'',1,0,'L',False);
				$pdf->Cell(94.3,4,'',1,0,'L',False);
				$pdf->Cell(94.3,4,'',1,0,'L',False);
				$pdf->ln();

				$pdf->Cell(94.3,12,'',1,0,'L',False);
				$pdf->Cell(94.3,12,'',1,0,'L',False);
				$pdf->Cell(94.3,12,'',1,0,'L',False);
				$pdf->ln();

















				$pdf->Cell(94.3,16,'',1,0,'L',False);
				$pdf->Cell(94.3,16,'',1,0,'L',False);
				$cy = $pdf->GetY();
				$cx = $pdf->GetX();
				$pdf->Cell(94.3,8,'',1,0,'L',False);
				$pdf->SetXY($cx, $cy+8);
				$pdf->Cell(94.3,8,'',1,0,'L',False);
				$pdf->ln();

				$pdf->SetXY($current_x, $current_y);








				$pdf->Cell(94.3,4,'MANIPULADOR',0,0,'C',False);
				$pdf->Cell(94.3,4,'TRANSPORTADOR',0,0,'C',False);
				$pdf->Cell(94.3,4,utf8_decode('INSTITUCIÓN EDUCATIVA'),0,0,'C',False);
				$pdf->ln();

				$pdf->SetXY($current_x, $current_y-0.2);

				$pdf->Cell(94.3,12,'NOMBRE MANIPULADOR (Operador):',0,0,'L',False);
				$pdf->Cell(94.3,12,'NOMBRE RECIBE (Operador):',0,0,'L',False);
				$pdf->Cell(94.3,12,'NOMBRE RESPONSABLE INSTITUCION O CENTRO EDUCATIVO:',0,0,'L',False);
				$pdf->ln();

				$pdf->Cell(94.3,16,'FIRMA:',0,0,'L',False);
				$pdf->Cell(94.3,16,'FIRMA:',0,0,'L',False);
				$cy = $pdf->GetY();
				$cx = $pdf->GetX();

				$pdf->SetXY($cx, $cy+2);
				$pdf->Cell(94.3,8,'CARGO:',0,0,'L',False);
				$pdf->SetXY($cx, $cy+10);
				$pdf->Cell(94.3,8,'FIRMA:',0,0,'L',False);

				// $pdf->ln();
			}



		}
	}
}




$pdf->Output("INFORME_DESPACHOS_INSUMOS.pdf", "I");


function mesNombre($mes)
{
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
}