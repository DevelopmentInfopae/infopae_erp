<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';
require_once '../../../fpdf181/fpdf.php';

//283 de ancho total.

$despachos_seleccionados = $_POST['despachos_seleccionados'];
$despachos_seleccionados = trim($despachos_seleccionados, ", ");
$ds = explode(", ", $despachos_seleccionados);
$ds_mes = [];

$meses_involucrados = [];

foreach ($ds as $id => $value) {
	$sd = explode("_", $value);

	$ds_mes[$id]['id_despacho'] = $sd[0];
	$ds_mes[$id]['mes_despacho'] = $sd[1];

	if (!isset($meses_involucrados[$sd[1]])) {
		$meses_involucrados[(Int) $sd[1]] = 1;
	}

}

// exit(var_dump($meses_involucrados));

if (isset($_POST['sedes'])) {
	$sedes_post = $_POST['sedes'];
	$sdp = [];

	foreach ($sedes_post as $key => $sede) {
		if (!isset($sdp[$sede])) {
			$sedes[] = $sede;
			$sdp[$sede] = 1;
		}
	}

} else {
	echo "<script>alert('No se ha definido sede.');</script>";
}

// exit(var_dump($sedes));

if (isset($_POST['tablaMesInicio'])) {
	$tablaMesInicio = str_replace("0", "", $_POST['tablaMesInicio']);
} else {
	echo "<script>alert('No se ha definido mes.');</script>";
}

if (isset($_POST['tablaMesFin'])) {
	$tablaMesFin = str_replace("0", "", $_POST['tablaMesFin']);
} else {
	echo "<script>alert('No se ha definido mes.');</script>";
}

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

		function setData($fecha, $dpto, $sedes, $productos, $fontSize, $alturaRenglon, $maxEstudiantes, $maxManipuladoras, $nom_inst, $ciudad_despacho, $mesInicio, $mesFin, $coberturaComplemento){
			$this->fecha = $fecha;
			// setlocale(LC_TIME, 'es');
			// $fecha = DateTime::createFromFormat('!m', $mes);
			// $mes = ucfirst(strftime("%B", $fecha->getTimestamp())); // marzo
			$this->mes = "De ".mesNombre($mesInicio)." a ".mesNombre($mesFin);
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
		    $this->Cell(171,8.5,utf8_decode('PROGRAMA DE ATENCIÓN ESCOLAR'),'TRL',1,'C');
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
		    // $this->Cell(32.5,$this->alturaRenglon,utf8_decode($this->dataInst['cantidad_Manipuladora']),'BR',0,'L');
		    $this->Cell(10,$this->alturaRenglon,utf8_decode($this->maxManipuladoras),'BR',0,'L');
		    $this->SetFont('Arial','B',$this->fontSize);
		    $this->Cell(32.5,$this->alturaRenglon,utf8_decode('TOTAL COBERTURA: '),'BL',0,'L');
		    $this->SetFont('Arial','',$this->fontSize);
		    // $this->Cell(32.5,$this->alturaRenglon,utf8_decode($this->maxEstudiantes),'BR',1,'L');
		    $this->Cell(10,$this->alturaRenglon,utf8_decode($this->maxEstudiantes),'BR',1,'L');
    		//Salto de línea

    		//ANCHO MÁXIMO 342

		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode('APS'),'LBR',0,'C');
		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['APS']) ? $this->coberturaComplemento['APS'] : '-')),'BR',0,'L');
		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode('CAJMPS'),'BR',0,'C');
		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['CAJMPS']) ? $this->coberturaComplemento['CAJMPS'] : '-')),'BR',0,'L');
		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode('CAJMRI'),'BR',0,'C');
		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['CAJMRI']) ? $this->coberturaComplemento['CAJMRI'] : '-')),'BR',0,'L');
		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode('CAJTRI'),'BR',0,'C');
		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['CAJTRI']) ? $this->coberturaComplemento['CAJTRI'] : '-')),'BR',0,'L');
		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode('CAJTPS'),'BR',0,'C');
		    $this->Cell(34.2,$this->alturaRenglon,utf8_decode((isset($this->coberturaComplemento['CAJTPS']) ? $this->coberturaComplemento['CAJTPS'] : '-')),'BR',1,'L');

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


		    $this->Cell(36,15,utf8_decode("FIRMA MANIPULADORA"),'TBR',0,'C');


		    $this->Ln();

		}

	}

	$dataInst = [];
	$productos = [];
	$productos_sede = [];
	$maxEstudiantes = [];
	$EstudiantesContados = [];
	$maxManipuladoras = [];
	$ManipuladorasContadas = [];
	$coberturaComplemento = [];

	$pdf = new PDF('L', 'mm', 'Legal');
	$pdf->AliasNbPages();
	$pdf->SetMargins(7, 7);


$sedeTabla = "sedes".$_SESSION['periodoActual'];

for ($i=$tablaMesInicio; $i <= $tablaMesFin ; $i++) {

	if (!isset($meses_involucrados[$i])) {
		continue;
	}

	$insumosmov = "insumosmov".($i < 10 ? "0".$i : $i).$_SESSION['periodoActual'];
	$insumosmovdet = "insumosmovdet".($i < 10 ? "0".$i : $i).$_SESSION['periodoActual'];

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

			if (count($ManipuladorasContadas) == 0) {
				$ManipuladorasContadas[$i][$codigo_inst] = 1;
			}

			if (count($ManipuladorasContadas) > 0 && !isset($ManipuladorasContadas[$i])) {
			} else {

				if (!isset($maxManipuladoras[$codigo_inst])) {
					$maxManipuladoras[$codigo_inst] = $dataInst[$codigo_inst][$sede]['cantidad_Manipuladora'];
				} else {
					$maxManipuladoras[$codigo_inst] += $dataInst[$codigo_inst][$sede]['cantidad_Manipuladora'];
				}

			}

			if(!isset($nom_inst[$codigo_inst])){
				$nom_inst[$codigo_inst] = $dataInst[$codigo_inst][$sede]['nom_inst'];
			}

			if(!isset($ciudad_despacho[$codigo_inst])){
				$ciudad_despacho[$codigo_inst] = $dataInst[$codigo_inst][$sede]['Ciudad'];
			}
		}


		// if (count($EstudiantesContados) == 0) {
		// 	$EstudiantesContados[$i][$codigo_inst] = 1;
		// }

		// if (count($EstudiantesContados) > 0 && !isset($EstudiantesContados[$i])) {
		// } else {

		// 	$sumaCoberturasEtario = "";

		// 	foreach ($gruposEtarios as $ID => $DESCRIPCION) {
		// 		$sumaCoberturasEtario.=" MAX(Etario".$ID."_APS+Etario".$ID."_CAJMRI+Etario".$ID."_CAJTRI+Etario".$ID."_CAJMPS) as Etario".$ID." ,";
		//     }

		//     $sumaCoberturasEtario = trim($sumaCoberturasEtario, " ,");

		// 	$consultaEtariosCobertura = "SELECT MAX(APS) + MAX(CAJMPS)+ MAX(CAJMRI)+ MAX(CAJTRI) AS cant_Estudiantes, $sumaCoberturasEtario FROM sedes_cobertura WHERE cod_sede = '".$sede."' and mes= '".($i < 10 ? "0".$i : $i)."' GROUP BY cod_sede";
		// 	$resultadoEtariosCobertura = $Link->query($consultaEtariosCobertura);
		// 	if ($resultadoEtariosCobertura->num_rows > 0) {
		// 		if ($EtariosCobertura = $resultadoEtariosCobertura->fetch_assoc()) {
		// 			if (!isset($maxEstudiantes[$codigo_inst])) {
		// 				$maxEstudiantes[$codigo_inst] = $EtariosCobertura["cant_Estudiantes"];
		// 			} else {
		// 				$maxEstudiantes[$codigo_inst] += $EtariosCobertura["cant_Estudiantes"];
		// 			}

		// 			// $maxEstudiantes += $EtariosCobertura["cant_Estudiantes"];
		// 		}
		// 	}

		// }


		$consultaDespacho = "SELECT * FROM $insumosmov WHERE BodegaDestino = '".$sede."'";
		$resultadoDespacho = $Link->query($consultaDespacho);
		if ($resultadoDespacho->num_rows > 0) {
			while ($Despacho = $resultadoDespacho->fetch_assoc()) {

				if (!isset($maxEstudiantes[$codigo_inst])) {
					$maxEstudiantes[$codigo_inst] = $Despacho["Cobertura"];
				} else {
					$maxEstudiantes[$codigo_inst] += $Despacho["Cobertura"];
				}

				if (!isset($coberturaComplemento[$Despacho['Complemento']])) {
					$coberturaComplemento[$codigo_inst][$Despacho['Complemento']] = $Despacho["Cobertura"];
				} else {
					$coberturaComplemento[$codigo_inst][$Despacho['Complemento']] += $Despacho["Cobertura"];
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

			    		$productos_sede[$sede][$detalles['pId']][($i < 10 ? "0".$i : $i)] = $detalles;
			    	}
			    }
			}
		}
	}

}


	// var_dump($dataInst);
	// var_dump($nom_inst);



$fontSize = 7;
$alturaRenglon = 6;

// exit(var_dump($productos_sede));

// for ($i=$tablaMesInicio; $i <= $tablaMesFin ; $i++) {

	foreach ($dataInst as $cod_inst => $sedes) {

		$pdf->setData($fecha_despacho, $dpto, $dataInst, $productos, $fontSize, $alturaRenglon, (isset($maxEstudiantes[$cod_inst]) ? $maxEstudiantes[$cod_inst] : 0), $maxManipuladoras[$cod_inst], $nom_inst[$cod_inst], $ciudad_despacho[$cod_inst], $tablaMesInicio, $tablaMesFin, $coberturaComplemento[$cod_inst]);
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
				if (isset($productos_sede[$cod_sede][$id])) {

					$cantidad = 0;

					foreach ($productos_sede[$cod_sede][$id] as $mes => $detalles) {
						$cantidad += number_format($detalles['NombreUnidad1'] == 'u' ?
							ceil($detalles['CanTotalPresentacion']) :
							strpos($detalles['NombreUnidad2'], 'kg') || strpos($detalles['NombreUnidad2'], 'lt') ?
							ceil($detalles['CanTotalPresentacion']) : $detalles['CanTotalPresentacion'], 2, '.', ',');
						$uP = " (".str_replace(" ", "", $detalles['Umedida']).")";
					}

					$cantidad.=$uP;

				} else {
					$cantidad = "-";
				}
				$pdf->Cell($espacio_cu,$alturaRenglon,utf8_decode($cantidad) ,'BR',0,'C');
			}

			$pdf->Cell(36,$alturaRenglon,"",'BR',0,'C',False);
			$pdf->ln();
		}


		$pdf->ln();

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

// }

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