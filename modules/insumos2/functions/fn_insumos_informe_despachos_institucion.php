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



$despachos_seleccionados = $_POST['despachos_seleccionados'];
$despachos_seleccionados = trim($despachos_seleccionados, ", ");
$ds = explode(", ", $despachos_seleccionados);
$ds_mes = [];

$meses_involucrados = [];
$id_despachos = [];
$id_in = '(';

foreach ($ds as $id => $value) {
	$sd = explode("_", $value);

	$ds_mes[$id]['id_despacho'] = $sd[0];
	$ds_mes[$id]['mes_despacho'] = $sd[1];

	$id_in .= $sd[0].', ';

	if (!isset($id_despachos[$sd[0]])) {
		$id_despachos[(Int) $sd[0]] = 1;
	}

	if (!isset($meses_involucrados[$sd[1]])) {
		$meses_involucrados[(Int) $sd[1]] = 1;
	}

}

$id_in = trim($id_in, ', ');
$id_in .= ')';

// exit(var_dump($id_in));

if (count($meses_involucrados) > 1) {
	echo "<script>alert('No puede escoger despachos de diferentes meses para este informe.');</script>";
}

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

		function setData($fecha, $dpto, $sedes, $productos, $fontSize, $alturaRenglon, $maxEstudiantes, $maxManipuladoras, $nom_inst, $ciudad_despacho, $mes, $coberturaComplemento){
			$this->fecha = $fecha;
			$this->mes = mesNombre($mes);
			$this->dpto = $dpto;
			$this->sedes = $sedes;
			$this->productos = $productos;
			$this->fontSize = $fontSize;
			$this->alturaRenglon = $alturaRenglon;
			$this->maxEstudiantes = $maxEstudiantes;
			$this->maxManipuladoras = $maxManipuladoras;
			$this->nom_inst = $nom_inst;
			$this->ciudad_despacho = $ciudad_despacho;
			$this->coberturaComplemento = $coberturaComplemento;
		}

		function Header()
		{
			$logoInfopae = '../'.$_SESSION['p_Logo ETC'];
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Image($logoInfopae,28,8,100, 15.92,'jpg', '');
		    $this->Cell(171,17,'','TBRL',0,'C');
		    $this->Cell(171,8.5,utf8_decode('PROGRAMA DE ALIMENTACIÓN ESCOLAR'),'TRL',1,'C');
		    $this->Cell(171,8.5,utf8_decode(''),'',0,'C');
		    $this->Cell(171,8.5,utf8_decode('REMISIÓN ENTREGA DE INSUMOS EN INSTITUCIÓN EDUCATIVA'),'BRL',1,'C');
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(25,$this->alturaRenglon,utf8_decode('OPERADOR: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    $this->Cell(120,$this->alturaRenglon,utf8_decode($_SESSION['p_Operador']),'BR',0,'L');
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(40,$this->alturaRenglon,utf8_decode('MES: '),'BL',0,'C');
		    $this->SetFont('Arial','',$this->fontSize);
		    $this->Cell(58,$this->alturaRenglon,utf8_decode($this->mes),'BR',0,'L'); //FECHA DE ELABORACIÓN DEL DESPACHO - REEMPLAZAR
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(19,$this->alturaRenglon,utf8_decode('ETC: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    $this->Cell(80,$this->alturaRenglon,utf8_decode(mb_strtoupper($this->dpto)),'BR',1,'L'); //DPTO PARAMETRO - REEMPLAZAR

		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(32,$this->alturaRenglon,utf8_decode('MUNICIPIO O VEREDA: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    $this->Cell(70,$this->alturaRenglon,utf8_decode($this->ciudad_despacho),'BR',0,'L'); //MUNICIPIO DE SEDE - REEMPLAZAR
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(55,$this->alturaRenglon,utf8_decode('INSTITUCIÓN O CENTRO EDUCATIVO: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    $this->Cell(100,$this->alturaRenglon,utf8_decode($this->nom_inst),'BR',0,'L'); //INSTITUCIÓN - REEMPLAZZAR
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(32.5,$this->alturaRenglon,utf8_decode('N° MANIPULADORAS: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    $this->Cell(10,$this->alturaRenglon,utf8_decode($this->maxManipuladoras),'BR',0,'L');
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(32.5,$this->alturaRenglon,utf8_decode('TOTAL COBERTURA: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    $maxEstudiantes = (isset($this->coberturaComplemento['APS']) ? $this->coberturaComplemento['APS'] : 0) +
		    				  (isset($this->coberturaComplemento['CAJMPS']) ? $this->coberturaComplemento['CAJMPS'] : 0) +
		    				  (isset($this->coberturaComplemento['CAJMRI']) ? $this->coberturaComplemento['CAJMRI'] : 0) +
		    				  (isset($this->coberturaComplemento['CAJTRI']) ? $this->coberturaComplemento['CAJTRI'] : 0) +
		    				  (isset($this->coberturaComplemento['CAJTPS']) ? $this->coberturaComplemento['CAJTPS'] : 0) +
		    				  (isset($this->coberturaComplemento['RPC']) ? $this->coberturaComplemento['RPC'] : 0) +
		    				  (isset($this->coberturaComplemento['Total cobertura']) ? $this->coberturaComplemento['Total cobertura'] : 0);

		    $this->Cell(10,$this->alturaRenglon,utf8_decode($maxEstudiantes),'BR',1,'L');
    		//Salto de línea

		    //ANCHO MÁXIMO 342

		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode('APS'),'LBR',0,'C');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['APS']) ? $this->coberturaComplemento['APS'] : '-')),'BR',0,'L');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode('CAJMPS'),'BR',0,'C');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['CAJMPS']) ? $this->coberturaComplemento['CAJMPS'] : '-')),'BR',0,'L');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode('CAJMRI'),'BR',0,'C');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['CAJMRI']) ? $this->coberturaComplemento['CAJMRI'] : '-')),'BR',0,'L');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode('CAJTRI'),'BR',0,'C');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['CAJTRI']) ? $this->coberturaComplemento['CAJTRI'] : '-')),'BR',0,'L');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode('CAJTPS'),'BR',0,'C');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['CAJTPS']) ? $this->coberturaComplemento['CAJTPS'] : '-')),'BR',0,'L');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode('RPC'),'BR',0,'C');
		    $this->Cell(28.5,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['RPC']) ? $this->coberturaComplemento['RPC'] : '-')),'BR',1,'L');  


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


		    $this->Cell(36,15,utf8_decode("FIRMA QUIÉN RECIBE"),'TBR',0,'C');


		    $this->Ln();

		}

	}

	$dataInst = [];
	$productos = [];
	$productos_sede = [];
	$maxEstudiantes = [];
	$maxManipuladoras = [];
	$coberturaComplemento = [];

	$pdf = new PDF('L', 'mm', 'Legal');
	$pdf->AliasNbPages();
	$pdf->SetMargins(7, 7);



	$inst_cobertura_sede_sumada = [];
	$inst_estudiante_sede_sumada = [];

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
		$ds = $resultadoSede->fetch_assoc();

		$codigo_inst = $ds['cod_inst'];

		$dataInst[$codigo_inst][$sede] = $ds;

		if (!isset($maxManipuladoras[$codigo_inst])) {
			$maxManipuladoras[$codigo_inst] = $dataInst[$codigo_inst][$sede]['cantidad_Manipuladora'];
		} else {
			$maxManipuladoras[$codigo_inst] += $dataInst[$codigo_inst][$sede]['cantidad_Manipuladora'];
		}

		if(!isset($nom_inst[$codigo_inst])){
			$nom_inst[$codigo_inst] = $dataInst[$codigo_inst][$sede]['nom_inst'];
		}

		if(!isset($ciudad_despacho[$codigo_inst])){
			$ciudad_despacho[$codigo_inst] = $dataInst[$codigo_inst][$sede]['Ciudad'];
		}
	}

	$dataInst[$codigo_inst][$sede]['complemento'] = [];

	$consultaDespacho = "SELECT * FROM $insumosmov WHERE BodegaDestino = '".$sede."' AND ID IN ".$id_in;
	$resultadoDespacho = $Link->query($consultaDespacho);
	if ($resultadoDespacho->num_rows > 0) {
		while ($Despacho = $resultadoDespacho->fetch_assoc()) {

			if (!isset($maxEstudiantes[$codigo_inst])) {
				$maxEstudiantes[$codigo_inst] = $Despacho["Cobertura"];
				$inst_estudiante_sede_sumada[$codigo_inst][$sede] = 1;
			} else {
				if (!isset($inst_estudiante_sede_sumada[$codigo_inst][$sede])) {
					$maxEstudiantes[$codigo_inst] += $Despacho["Cobertura"];
				}
			}

			if (!isset($coberturaComplemento[$codigo_inst][$Despacho['Complemento']])) {
				$coberturaComplemento[$codigo_inst][$Despacho['Complemento']] = $Despacho["Cobertura"];
				$inst_cobertura_sede_sumada[$codigo_inst][$sede] = 1;
			} else {
				if (!isset($inst_cobertura_sede_sumada[$codigo_inst][$sede])) {
					$coberturaComplemento[$codigo_inst][$Despacho['Complemento']] += $Despacho["Cobertura"];
				}
			}

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

		    		$productos_sede[$sede][$detalles['pId']][$Despacho['Complemento']] = $detalles;
		    	}
		    }

			$dataInst[$codigo_inst][$sede]['complemento'][] = $Despacho['Complemento'];
		}
	}
}

// exit(var_dump($coberturaComplemento));

$fontSize = 7;
$alturaRenglon = 6;

foreach ($dataInst as $cod_inst => $sedes) {

	$pdf->setData($fecha_despacho, $dpto, $dataInst, $productos, $fontSize, $alturaRenglon, (isset($maxEstudiantes[$cod_inst]) ? $maxEstudiantes[$cod_inst] : 0), $maxManipuladoras[$cod_inst], $nom_inst[$cod_inst], $ciudad_despacho[$cod_inst], $tablaMes, $coberturaComplemento[$cod_inst]);
	$pdf->AddPage();
	$pdf->SetFont('Arial','',$fontSize);

	$cntProductos = count($productos);
	$espacio_cu = 245.25 / $cntProductos;

	foreach ($sedes as $cod_sede => $sede) {

		$sName = $sede['nom_sede'];

		if (strlen($sName) > 35) {
			$sName = substr( $sName, 0, 35);
			$sName.="...";
		}

		$pdf->Cell(60.75,$alturaRenglon,utf8_decode($sName),'LBR',0,'C',False);


		foreach ($productos as $id => $producto) {
			$cantidad = 0;
			if (isset($productos_sede[$cod_sede][$id])) {
				foreach ($productos_sede[$cod_sede][$id] as $complemento => $producto_2) {
					$cantidad += number_format($producto_2['NombreUnidad1'] == 'u' ? ceil($producto_2['CanTotalPresentacion']) : strpos($producto_2['NombreUnidad2'], 'kg') || strpos($producto_2['NombreUnidad2'], 'lt') ? ceil($producto_2['CanTotalPresentacion']) : $producto_2['CanTotalPresentacion'], 2, '.', ',');

					$uP = " (".str_replace(" ", "", $producto_2['Umedida']).")";

					$cantidad.=$uP;
				}

			} else {
				$cantidad = "-";
			}
			$pdf->Cell($espacio_cu,$alturaRenglon,utf8_decode($cantidad) ,'BR',0,'C');
		}

		$pdf->Cell(36,$alturaRenglon,"",'BR',0,'C',False);
		$pdf->ln();
	}


	$pdf->ln();


	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(0,5,'OBSERVACIONES:','B',5,'L',False);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(0,5,$paginasObservaciones,'B',5,'L',False);
	$pdf->Ln(2);

	$cy = $pdf->GetY();

	if($cy > 155){
		$pdf->AddPage();
	}

	$current_x = $pdf->getX();
	$current_y = $pdf->getY();

	$pdf->Cell(342,4,"","TBLR",1,'C',False);
	$pdf->Cell(171,8,"","BLR",0,'C',False);
	$pdf->Cell(171,8,"","BR",1,'C',False);
	$pdf->Cell(171,8,"","BLR",0,'C',False);
	$pdf->Cell(171,8,"","BR",1,'C',False);

	$pdf->SetXY($current_x, $current_y);
	$pdf->SetFont('Arial','B',7);
	$pdf->Cell(342,4,utf8_decode("INSTITUCIÓN EDUCATIVA"),0,1,'C',False);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(171,4,utf8_decode("NOMBRE RESPONSABLE INSTITUCION O CENTRO EDUCATIVO:"),0,0,'L',False);
	$pdf->Cell(171,4,utf8_decode("DOCUMENTO:"),0,1,'L',False);
	$pdf->Cell(171,4,utf8_decode(""),0,1,'L',False);
	$pdf->Cell(171,4,utf8_decode("CARGO:"),0,0,'L',False);
	$pdf->Cell(171,4,utf8_decode("FIRMA:"),0,1,'L',False);

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