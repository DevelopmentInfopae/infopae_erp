<?php
error_reporting(E_ALL);
include '../../config.php';
require_once '../../autentication.php';
require_once '../../db/conexion.php';
include '../../php/funciones.php';
require('../../fpdf181/fpdf.php');
set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

// Declaración de variables
$diaInicialSemanaInicial = $_POST["diaInicialSemanaInicial"];
$diaFinalSemanaInicial = $_POST["diaFinalSemanaInicial"];
$diaInicialSemanaFinal = $_POST["diaInicialSemanaFinal"];
$diaFinalSemanaFinal = $_POST["diaFinalSemanaFinal"];

$tamannoFuente = 8;
class PDF extends FPDF
{
	function Header() {
		$logoInfopae = $_SESSION['p_Logo ETC'];
		$this->Image($logoInfopae, 12, 7, 95, 18.1,'jpg', '');

		$tamannoFuente = 11;
		$this->SetFont('Arial','B',$tamannoFuente);
		$this->SetTextColor(0,0,0);

		$this->Cell(91.9);
		$this->MultiCell(100,6,"CERTIFICADO DE ENTREGA DE RACIONES A INSTITUCIONES EDUCATIVAS" ,0,'C',false);

		$this->Ln(8);
	}

  	// Pie de página
	function Footer() {
		$tamannoFuente = 8;
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
	}
}

//CREACION DEL PDF
// Creación del objeto de la clase heredada
$pdf= new PDF('P','mm',array(215.9,279.4));
$pdf->SetMargins(12, 12, 12, 12);
$pdf->SetAutoPageBreak(false,5);
$pdf->AliasNbPages();


$anno = $_SESSION['p_ano'];
$anno2d = substr($anno,2);
$mes = $_POST['mes'];
if($mes < 10){
	$mes = '0'.$mes;
}
$municipio = $_POST['municipio'];


//Dias Semanas
$consulta = "SELECT * FROM planilla_semanas WHERE ano='$anno' AND mes='$mes' AND DIA BETWEEN $diaInicialSemanaInicial AND $diaFinalSemanaFinal";
$resultado = $Link->query($consulta) or die ('Unable to execute query. Linea 62:'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$diasSemanas[] = $row;
	}
}

//Instituciones
$consulta = "SELECT DISTINCT s.cod_inst,s.nom_inst,s.cod_mun_sede,u.ciudad,u.Departamento, usu.nombre AS nombre_rector, usu.num_doc AS documento_rector
FROM sedes$anno2d s
INNER JOIN sedes_cobertura AS sc ON (s.cod_inst=sc.cod_inst AND s.cod_Sede=sc.cod_Sede)
INNER JOIN ubicacion u ON(s.cod_mun_sede=u.codigoDANE) and u.ETC = 0
INNER JOIN instituciones ins ON ins.codigo_inst = s.cod_inst
LEFT JOIN usuarios usu ON usu.num_doc = ins.cc_rector
WHERE sc.ano='$anno' AND sc.mes='$mes' AND s.cod_mun_sede='$municipio'";
$consulta .= (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? " AND s.cod_inst = '".$_POST["institucion"]."'" : "";
$resultado = $Link->query($consulta) or die ('Unable to execute query. Linea 78: '. mysqli_error($Link));

if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$instituciones[$row['cod_inst']] = $row;
	}
}

//diasEntregas
$consulta = "SELECT ID ,ANO, MES, D1 AS 'D01',D2 AS D02,D3 AS D03,D4 AS D04,D5 AS D05,D6 AS D06,D7 AS D07,D8 AS D08,D9 AS D09, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22 FROM planilla_dias WHERE ano='$anno' and mes='$mes'";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$diasEntregas = $row;
	}
}

$iDias = $indiceDiaInicial = 0;
$columnasDiasEntregasDias = $camposDiasEntregasDias = $columnasCondicionEntregasDias = "";
foreach ($diasEntregas as $indiceDia => $dias) {
	if ($iDias >= 3) {
		if ($dias >= $diaInicialSemanaInicial && $dias <= $diaFinalSemanaFinal) {
			if ($indiceDiaInicial == 0) {
				$indiceDiaInicial = str_replace("D0", "", $indiceDia);
			}
			$numeroIndiceDia = intval(str_replace("D", "", $indiceDia));
			$dia  = ($numeroIndiceDia > 9) ? $indiceDia : str_replace("0", "", $indiceDia);

			$columnasDiasEntregasDias .= "SUM(". $dia .") AS ". strtolower($indiceDia) .", ";
			$columnasCondicionEntregasDias .= $dia ."+ ";
			$camposDiasEntregasDias .= $dia ." + ";
		}
	}
	$iDias++;
}

// Fechas inicial y fecha final del mes seleccionado.
$consulta_fechas = "(SELECT ANO, MES, DIA FROM planilla_semanas WHERE MES = '$mes' AND DIA BETWEEN $diaInicialSemanaInicial AND $diaFinalSemanaFinal ORDER BY SEMANA ASC, DIA ASC LIMIT 1) UNION ALL (SELECT ANO, MES, DIA FROM `planilla_semanas` WHERE MES = '$mes' AND DIA BETWEEN $diaInicialSemanaInicial AND $diaFinalSemanaFinal ORDER BY SEMANA DESC, DIA DESC LIMIT 1)";
$resultado_fechas = $Link->query($consulta_fechas) or die ('Unable to execute query. '. mysqli_error($Link));
if ($resultado_fechas->num_rows > 0) {
	while ($registros_fechas = $resultado_fechas->fetch_assoc()) {
		$fechas[] = $registros_fechas;
	}
}



//EntregasSedes
$entregasSedes = array();
$consulta = "SELECT cod_inst,cod_sede,nom_sede, tipo_complem, ". trim($columnasDiasEntregasDias, ", ") .", (". trim($camposDiasEntregasDias, " + ") .") AS numdias FROM entregas_res_$mes$anno2d WHERE (". trim($columnasCondicionEntregasDias, "+ ") .") > 0 AND cod_mun_sede = $municipio";
$consulta .= (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? " AND cod_inst = '". $_POST["institucion"] ."'" : "";
$consulta .= " GROUP BY cod_sede,tipo_complem";

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$codigoInicial = 0;
	while($row = $resultado->fetch_assoc()){
		if($codigoInicial != $row['cod_inst']){
			$codigoInicial = $row['cod_inst'];
		}
		$entregasSedes[$codigoInicial][] = $row;
	}
}

if(count($entregasSedes)>0)
{
	$consultaSemanasMes = "SELECT DISTINCT SEMANA FROM planilla_semanas WHERE MES = '$mes'";
	$resultadoSemanasMes = $Link->query($consultaSemanasMes) or die ("Error al consultar planillas_semanas: ". $Link->error);
	if ($resultadoSemanasMes->num_rows > 0) {
		while ($registrosSemanasMes = $resultadoSemanasMes->fetch_assoc()) {
			$semanasMes[] = $registrosSemanasMes;
		}
	}

	$consultaSemanasMesSeleccion = "SELECT DISTINCT SEMANA FROM planilla_semanas WHERE MES = '$mes' AND DIA BETWEEN $diaInicialSemanaInicial AND $diaFinalSemanaFinal";
	$resultadoSemanasMesSeleccion = $Link->query($consultaSemanasMesSeleccion) or die ("Error al consultar planillas_semanas: ". $Link->error);
	if ($resultadoSemanasMesSeleccion->num_rows > 0) {
		while ($registrosSemanasMesSeleccion = $resultadoSemanasMesSeleccion->fetch_assoc()) {
			$semanasMesSeleccion[] = $registrosSemanasMesSeleccion;
		}
	}

	for ($i=0; $i < count($semanasMes); $i++) {
		foreach ($semanasMesSeleccion as $semanaSeleccion) {
			if ($semanasMes[$i]["SEMANA"] == $semanaSeleccion["SEMANA"]) {
				$posicionesSemanaMes[] = $i+1;
			}
		}
	}

	// Se van a separar los dias que corresponden a cada semana
	$semanaIndice = 0;
	$numeroSemana = -1;

	foreach ($diasSemanas as $diaSemana)
	{
		if($semanaIndice != $diaSemana['SEMANA'])
		{
			$semanaIndice = $diaSemana['SEMANA'];
			$numeroSemana++;
		}

		$diasSemana[$posicionesSemanaMes[$numeroSemana]][] = $diaSemana;
	}

	foreach ($instituciones as $institucion)
	{
		if (array_key_exists($institucion['cod_inst'], $entregasSedes))
		{
			$pdf->AddPage();
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(192,192,192);
			$pdf->SetDrawColor(0,0,0);

			$tamannoFuente = 8;
			$pdf->SetFont('Arial','B',$tamannoFuente);

			$pdf->Cell(0,6,'DATOS GENERALES ',0,0,'C',true);
			$pdf->Ln(10);

			$x = $pdf->GetX();
			$y = $pdf->GetY();

			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(32,5,utf8_decode('OPERADOR:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(110,5,utf8_decode($_SESSION['p_Operador']),'R',0,'L',false);
			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(25,5,utf8_decode('CONTRATO N°:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(30,5,$_SESSION['p_Contrato'],0,0,'L',false);
			$pdf->SetX($x);
			$pdf->Cell(0,5,'','B',5,'C',false);

			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(32,5,utf8_decode('INSTITUCIÓN:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(110,5,utf8_decode($institucion['nom_inst']),'R',0,'L',false);
			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(25,5,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(30,5,$institucion['cod_inst'],0,0,'L',false);
			$pdf->SetX($x);
			$pdf->Cell(0,5,'','B',5,'C',false);

			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(32,5,utf8_decode('DEPARTAMENTO:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(110,5,utf8_decode(strtoupper($institucion['Departamento'])),'R',0,'L',false);
			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(25,5,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(30,5,$_SESSION['p_CodDepartamento'],0,0,'L',false);
			$pdf->SetX($x);
			$pdf->Cell(0,5,'','B',5,'C',false);

			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(32,5,utf8_decode('MUNICIPIO:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(110,5,utf8_decode($institucion['ciudad']),'R',0,'L',false);
			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(25,5,utf8_decode('CÓDIGO DANE:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(30,5,$institucion['cod_mun_sede'],0,0,'L',false);
			$pdf->SetX($x);
			$pdf->Cell(0,5,'','B',5,'C',false);

			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(32,5,utf8_decode('FECHA EJECUCIÓN:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(15,5,'Desde:','R',0,'L',false);
			$pdf->Cell(40,5,utf8_decode($fechas[0]["DIA"]." de ".mesNombre($fechas[0]["MES"])." ". $fechas[0]["ANO"]),'R',0,'L',false);
			$pdf->Cell(15,5,'Hasta:','R',0,'L',false);
			$pdf->Cell(40,5,utf8_decode($fechas[1]["DIA"]." de ".mesNombre($fechas[1]["MES"])." ". $fechas[1]["ANO"]),'R',0,'L',false);
			$pdf->SetX($x);
			$pdf->Cell(0,5,'','B',5,'C',false);

			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(32,5,utf8_decode('NOMBRE RECTOR:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(110,5,utf8_decode($institucion["nombre_rector"]),'R',0,'L',false);
			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(25,5,utf8_decode('DOC. RECTOR:'),'R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->Cell(0,5,$institucion["documento_rector"],0,5,'L',false);
			$pdf->SetX($x);

			$pdf->SetXY($x, $y);
			$pdf->Cell(0,30,'',1,0,'C',false);
			$pdf->Ln(35);

			$pdf->SetFont('Arial','B',$tamannoFuente);
			$pdf->Cell(0,6,utf8_decode('CERTIFICACIÓN'),0,0,'C',true);
			$pdf->Ln(8);
			$pdf->SetFont('Arial','',$tamannoFuente);
			$pdf->MultiCell(0,4,utf8_decode("El suscrito Rector de la Institución Educativa citada en el encabezado, certifica que se entregaron las siguientes raciones,    en las fechas señaladas y de acuerdo con la siguiente distribución:"),0,'C',false);
			$pdf->Ln(3);

			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->SetXY($x, $y);
			$pdf->Cell(0,11,'',0,0,'C',true);

			$pdf->SetXY($x, $y+2);
			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->MultiCell(45,4,utf8_decode("NOMBRE DEL ESTABLECIMIENTO U CENTRO EDUCATIVO"),0,'C',false);
			$pdf->SetXY($x, $y);
			$pdf->Cell(45,11,'','R',0,'C',false);

			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();
			$pdf->SetXY($aux_x, $aux_y+2.5);
			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->MultiCell(15,3,utf8_decode("TIPO RACIÓN"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->Cell(15,11,'','R',0,'C',false);

			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->Cell(23,4,'SEMANA 1','B',0,'C',false);
			$pdf->SetFont('Arial','B',$tamannoFuente-2);
			$pdf->SetXY($aux_x, $aux_y+6);
			$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y+4);
			$pdf->Cell(13,7,'','R',0,'C',false);
			$pdf->SetXY($aux_x+13, $aux_y+6);
			$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->Cell(23,11,'','R',0,'C',false);

			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->Cell(23,4,'SEMANA 2','B',0,'C',false);
			$pdf->SetFont('Arial','B',$tamannoFuente-2);
			$pdf->SetXY($aux_x, $aux_y+6);
			$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y+4);
			$pdf->Cell(13,7,'','R',0,'C',false);
			$pdf->SetXY($aux_x+13, $aux_y+6);
			$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->Cell(23,11,'','R',0,'C',false);

			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->Cell(23,4,'SEMANA 3','B',0,'C',false);
			$pdf->SetFont('Arial','B',$tamannoFuente-2);
			$pdf->SetXY($aux_x, $aux_y+6);
			$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y+4);
			$pdf->Cell(13,7,'','R',0,'C',false);
			$pdf->SetXY($aux_x+13, $aux_y+6);
			$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->Cell(23,11,'','R',0,'C',false);

			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->Cell(23,4,'SEMANA 4','B',0,'C',false);
			$pdf->SetFont('Arial','B',$tamannoFuente-2);
			$pdf->SetXY($aux_x, $aux_y+6);
			$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y+4);
			$pdf->Cell(13,7,'','R',0,'C',false);
			$pdf->SetXY($aux_x+13, $aux_y+6);
			$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->Cell(23,11,'','R',0,'C',false);

			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->Cell(23,4,'SEMANA 5','B',0,'C',false);
			$pdf->SetFont('Arial','B',$tamannoFuente-2);
			$pdf->SetXY($aux_x, $aux_y+6);
			$pdf->MultiCell(13,2,utf8_decode("N°RACION DIA"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y+4);
			$pdf->Cell(13,7,'','R',0,'C',false);
			$pdf->SetXY($aux_x+13, $aux_y+6);
			$pdf->MultiCell(10,2,utf8_decode("N° DIAS"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->Cell(23,11,'','R',0,'C',false);

			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();
			$pdf->SetXY($aux_x, $aux_y+2.5);
			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->MultiCell(0,3,utf8_decode("TOTAL RACIONES"),0,'C',false);

			$pdf->SetXY($x, $y);
			$pdf->Cell(0,11,'','B',0,'C',false);
			$pdf->Ln(11);

			$totalesSemanas = array(0,0,0,0,0);

			// Impresión de datos de la tabla.
			if (array_key_exists($institucion['cod_inst'], $entregasSedes))
			{
				$entregasSedesInstitucion =  $entregasSedes[$institucion['cod_inst']];
			}
			else
			{
				$entregasSedesInstitucion = [];
			}

			$banderaNombres = 0;
			$lineas = 0;
			$lineasTotales = 0;
			$maxCaracteres = 27;

			foreach ($entregasSedesInstitucion as $entregasSedeInstitucion)
			{
				$lineasTotales++;
				$aux_x = $pdf->GetX();
				$aux_y = $pdf->GetY();
				if($banderaNombres == 0)
				{
					$nombre = $entregasSedeInstitucion['nom_sede'];
					$banderaNombres++;
				}
				else
				{
					if($nombre != $entregasSedeInstitucion['nom_sede'])
					{
						$linea = $lineas * 4;
						if($lineas<= 1)
						{
							$nombre = substr($nombre,0,$maxCaracteres);
						}
						$pdf->SetXY($aux_x, $aux_y-$linea);
						$pdf->MultiCell(45,4,utf8_decode($nombre),0,'L',false);
						$pdf->SetXY($aux_x, $aux_y-$linea);
						$pdf->Cell(45,$linea,'','B',0,'C',false);

						$nombre = $entregasSedeInstitucion['nom_sede'];
						$lineas = 0;
						$pdf->SetXY($aux_x, $aux_y);
					}
				}

				$lineas++;

				$pdf->SetFont('Arial','',$tamannoFuente-1);
				$pdf->Cell(45,4,'','R',0,'C',false);
				$pdf->Cell(15,4,$entregasSedeInstitucion['tipo_complem'],'R',0,'C',false);

				$indice = $indiceDiaInicial-1;
				$totalSemana = 0;

				for($i = 1; $i <= 5 ; $i++)
				{
					if(isset($diasSemana[$i]))
					{
						$total = 0;
						$indicePrint = 0;
						foreach ($diasSemana[$i] as $diaSemana)
						{
							$indice++;
							$res_can_comp = $Link->query("SELECT SUM(D$indice) AS cantidadComplemento FROM entregas_res_$mes$anno2d WHERE cod_inst = '". $entregasSedeInstitucion["cod_inst"] ."' AND cod_sede = '". $entregasSedeInstitucion["cod_sede"] ."' AND tipo_complem = '". $entregasSedeInstitucion["tipo_complem"] ."';") or die (mysqli_error($Link));
							if ($res_can_comp->num_rows > 0)
							{
								$reg_can_comp = $res_can_comp->fetch_assoc();

								if ($reg_can_comp["cantidadComplemento"] > 0)
								{
									$indicePrint++;
								}
							}

							if($indice < 10)
							{
								$aux = 'd0'.$indice;
							}
							else
							{
								$aux = 'd'.$indice;
							}

							$total = $total + $entregasSedeInstitucion[$aux];
							$totalSemana = $totalSemana + $entregasSedeInstitucion[$aux];
							$totalesSemanas[$i-1] = $totalesSemanas[$i-1] + $entregasSedeInstitucion[$aux];
						}
						$pdf->Cell(13,4,$total,'R',0,'C',false);
						$pdf->Cell(10,4,$indicePrint,'R',0,'C',false);
					}
					else
					{
						$pdf->Cell(13,4,'','R',0,'C',false);
						$pdf->Cell(10,4,'','R',0,'C',false);
					}
				}

				$pdf->Cell(0,4,$totalSemana,0,0,'C',false);
				$pdf->SetX($aux_x);
				$pdf->Cell(45);
				$pdf->Cell(0,4,'','B',0,'C',false);
				$pdf->Ln(4);
			}

			$linea = $lineas * 4;

			$pdf->SetXY($aux_x, $aux_y-$linea+4);

			if($lineas<= 1) {
				$nombre = substr($nombre,0,$maxCaracteres-3);
				$nombre.="...";
			}

			$pdf->MultiCell(45,4,utf8_decode(isset($nombre) ? $nombre: ""),0,'L',false);
			$pdf->SetXY($aux_x, $aux_y-$linea);
			$pdf->Cell(45,$linea+4,'','B',0,'C',false);
			$pdf->Ln($linea+4);

			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->Cell(60,4,'TOTAL:','R',0,'L',false);
			$pdf->SetFont('Arial','',$tamannoFuente-1);

			$granTotal = 0;
			for($i = 0; $i < 5 ; $i++) {
				$pdf->Cell(23,4, $totalesSemanas[$i],'R',0,'C',false);
				$granTotal = $granTotal + $totalesSemanas[$i];
			}


			$pdf->Cell(0,4,$granTotal,0,0,'C',false);

			// Cuadro  exterior tabla
			$pdf->SetXY($x, $y);
			//var_dump($lineasTotales);
			$pdf->Cell(0,$lineasTotales*4+15,'',1,0,'C',false);
			//Termina la tabla de sedes

			$pdf->Ln(70);
			$pdf->SetFont('Arial','',$tamannoFuente-1);
			/*********************************************************/
			// Consulta que retorna los tipo de complementos.
			$texto_convenciones = "";
			$consulta_tipo_complementos = "SELECT CODIGO AS codigo, DESCRIPCION as descripcion FROM tipo_complemento ORDER BY CODIGO";
			$respuesta_tipo_complementos = $Link->query($consulta_tipo_complementos) or die("Error al consultar tipo_complemento: ". $Link->error);
			if ($respuesta_tipo_complementos->num_rows > 0) {
				while($registros_tipo_complementos = $respuesta_tipo_complementos->fetch_assoc()) {
					$pdf->Cell(0,3,utf8_decode($registros_tipo_complementos["codigo"] .": ". $registros_tipo_complementos["descripcion"] .". "),0,4,'L',false);
				}
			}
			/**********************************************************/

			// Tebla tipos de caracterización.
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->Cell(0,8,utf8_decode(''),0,0,'L',true);
			$pdf->SetXY($x, $y);
			$pdf->SetFont('Arial','B',$tamannoFuente-1);
			$pdf->Cell(60,8,utf8_decode('DESCRIPCIÓN'),'R',0,'C',false);

			/////////////////////////////////////////////////////////////////////////

			// Consulta que retorna los complementos que han sido asignados en el transcurso del mes.
			$complementos = [];
			$resultadoComplementos = $Link->query("SELECT DISTINCT tipo_complem FROM entregas_res_".$mes.$_SESSION['periodoActual']." WHERE tipo_complem IS NOT NULL;") or die (mysqli_error($Link));
			if ($resultadoComplementos->num_rows > 0)
			{
				while ($registrosComplementos = $resultadoComplementos->fetch_assoc())
				{
					$complementos[] = $registrosComplementos["tipo_complem"];
				}
			}
			foreach ($complementos as $complemento)
			{
				$aux_x = $pdf->GetX();
				$aux_y = $pdf->GetY();
				$pdf->SetFont('Arial','B',$tamannoFuente-1.5);
				$pdf->MultiCell((104/count($complementos)),4,utf8_decode("TOTAL RACIONES\n". $complemento),0,'C',false);
				$pdf->SetXY($aux_x, $aux_y);
				$pdf->Cell((104/count($complementos)),8,utf8_decode(''),'R',0,'C',false);
			}
			/////////////////////////////////////////////////////////////////////////


			$aux_x = $pdf->GetX();
			$aux_y = $pdf->GetY();
			$pdf->MultiCell(0,4,utf8_decode("No. DE TITULARES DE\nDERECHO"),0,'C',false);
			$pdf->SetXY($aux_x, $aux_y);
			$pdf->Cell(0,8,utf8_decode(''),'R',0,'C',false);

			$pdf->SetXY($x, $y);
			$pdf->Cell(0,8,utf8_decode(''),'B',0,'L',false);
			$pdf->SetXY($x, $y);
			$pdf->Cell(0,28,utf8_decode(''),1,0,'L',false);

			$pdf->SetXY($x, $y);
			$pdf->Ln(8);

			// Consulta que retorna el orden de la prioridad del tipo de pocblación.
			$prioridades = [];
			$resultadoPrioridad = $Link->query("SELECT * FROM prioridad_caracterizacion ORDER BY orden") or die(mysql_error($Link));
			if ($resultadoPrioridad->num_rows > 0)
			{
				while ($registrosPrioridad = $resultadoPrioridad->fetch_assoc())
				{
					$prioridades[] = $registrosPrioridad;
				}
			}

			$totalEstudiantesEntregas = 0; //Variable para sacar el total de estudiantes contados en todas las características.
			$totalComplementos1 = $totalComplementos2 = $totalComplementos3 = 0;
			$condicionInstitucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? " AND cod_inst = '".$_POST["institucion"]."'" : " AND cod_inst = '". $institucion['cod_inst'] ."'";
			for ($i=0; $i < count($prioridades); $i++)
			{
				$pdf->SetFont('Arial','',$tamannoFuente-1);
				$aux_x = $pdf->GetX();
				$aux_y = $pdf->GetY();
				$pdf->SetXY($aux_x, $aux_y);
				$pdf->Cell(60,4,utf8_decode(strtoupper($prioridades[$i]["descripcion"])),'R',0,'L',false);
				$columna = 1;
				foreach ($complementos as $complemento)
				{
					$condicion = "";
					if ($i == 1) {
						$condicion .= " AND " . $prioridades[0]["campo_entregas_res"] . " = " . $prioridades[0]["valor_NA"];
					} else if ($i == 2) {
						$condicion .= " AND " . $prioridades[1]["campo_entregas_res"] . " = " . $prioridades[1]["valor_NA"] . " AND " . $prioridades[0]["campo_entregas_res"] . " = " . $prioridades[0]["valor_NA"];
					}

					$consultaCantidadComplemento = "SELECT IFNULL(SUM((". trim($camposDiasEntregasDias, "+ ") .")),0) AS cantidadComplemento FROM entregas_res_". $mes.$_SESSION['periodoActual'] ." WHERE 1 " . $condicionInstitucion ." AND tipo_complem = '" . $complemento . "' AND ". $prioridades[$i]["campo_entregas_res"] ." != " . $prioridades[$i]["valor_NA"] . $condicion;
					$resultadoCantidadComplemento = $Link->query($consultaCantidadComplemento) or die (mysqli_error($Link));
					if ($resultadoCantidadComplemento->num_rows > 0)
					{
						while ($registrosCantidadComplemento = $resultadoCantidadComplemento->fetch_assoc())
						{
							$cantidadComplemento = $registrosCantidadComplemento["cantidadComplemento"];
						}
					}
					else
					{
						$cantidadComplemento = 0;
					}

					$pdf->Cell((104/count($complementos)),4,$cantidadComplemento,'R',0,'C',false);

					if ($columna == 1)
					{
						$totalComplementos1 += $cantidadComplemento;
					}
					else if ($columna == 2)
					{
						$totalComplementos2 += $cantidadComplemento;
					}
					else
					{
						$totalComplementos3 += $cantidadComplemento;
					}

					$columna++;
				}

				$con_can_est_com = "SELECT COUNT(*) cantidad FROM entregas_res_" . $mes.$_SESSION['periodoActual'] . " WHERE 1 " . $condicionInstitucion . " AND " . $prioridades[$i]["campo_entregas_res"] . " != " . $prioridades[$i]["valor_NA"] . $condicion ." AND ". trim($camposDiasEntregasDias, "+ ") ." > 0";
				$res_can_est_com = $Link->query($con_can_est_com) or die (mysql_error($Link));
				if ($res_can_est_com->num_rows > 0)
				{
					while ($reg_can_est_com = $res_can_est_com->fetch_assoc())
					{
						$can_est_com = $reg_can_est_com["cantidad"];
						$totalEstudiantesEntregas += $can_est_com;
					}
				}
				else
				{
					$can_est_com = 0;
				}

				$pdf->Cell(0,4,$can_est_com,'R',0,'C',false);
				$pdf->SetXY($aux_x, $aux_y);
				$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);
				$pdf->Ln(4);
			}



		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(60,4,utf8_decode('POBLACIÓN MAYORITARIA'),'R',0,'L',false);
		$columna = 1;
		foreach ($complementos as $complemento)
		{
			$condicion = "";
			foreach ($prioridades as $prioridad)
			{
				$condicion .= " AND ". $prioridad["campo_entregas_res"] ." = " . $prioridad["valor_NA"];
			}
			$con_can_may = "SELECT IFNULL(SUM((". trim($camposDiasEntregasDias, "+ ") .")),0) AS cantidadComplemento FROM entregas_res_". $mes.$_SESSION['periodoActual'] ." WHERE 1 " . $condicionInstitucion ." AND tipo_complem = '" . $complemento . "'" . $condicion;
			$res_can_may = $Link->query($con_can_may) or die (mysqli_error($Link));
			if ($res_can_may->num_rows > 0) {
				while ($reg_can_may = $res_can_may->fetch_assoc())
				{
					$cantidadMayoritaria = $reg_can_may["cantidadComplemento"];
				}
			}
			else
			{
				$cantidadMayoritaria = 0;
			}

			$pdf->Cell((104/count($complementos)),4,$cantidadMayoritaria,'R',0,'C',false);

			if ($columna == 1)
			{
				$totalComplementos1 += $cantidadMayoritaria;
			}
			else if ($columna == 2)
			{
				$totalComplementos2 += $cantidadMayoritaria;
			}
			else
			{
				$totalComplementos3 += $cantidadMayoritaria;
			}

			$columna++;
		}

		$condicionMayoritaria = " AND " . $prioridades[0]["campo_entregas_res"] . " = " . $prioridades[0]["valor_NA"]." AND " . $prioridades[1]["campo_entregas_res"] . " = " . $prioridades[1]["valor_NA"]." AND " . $prioridades[2]["campo_entregas_res"] . " = " . $prioridades[2]["valor_NA"]." "; //Variable para la condición de búsqueda de estudiantes de población mayoritaria, que no cumplen con ninguna caracterización.

		$con_can_est_total = "SELECT COUNT(DISTINCT num_doc) cantidad FROM entregas_res_" . $mes.$_SESSION['periodoActual'] . " WHERE 1 " . $condicionInstitucion.$condicionMayoritaria ." AND ". trim($camposDiasEntregasDias, "+ ") ." > 0";
		$res_can_est_total = $Link->query($con_can_est_total) or die (mysql_error($Link));
		if ($res_can_est_total->num_rows > 0) {
			while ($reg_can_est_total = $res_can_est_total->fetch_assoc()) {
				$can_est_total = $reg_can_est_total["cantidad"];
				$totalEstudiantesEntregas += $can_est_total;
			}
		} else {
			$can_est_total = 0;
		}
		$pdf->Cell(0,4,$can_est_total,'R',0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);
		$pdf->Ln(4);

		$pdf->SetFont('Arial','B',$tamannoFuente-1);
		$aux_x = $pdf->GetX();
		$aux_y = $pdf->GetY();
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(60,4,utf8_decode('TOTAL'),'R',0,'L',false);
		$columna = 1;
		foreach ($complementos as $complemento) {
			if ($columna == 1) {
				$pdf->Cell((104/count($complementos)),4,$totalComplementos1,'R',0,'C',false);
			} else if ($columna == 2) {
				$pdf->Cell((104/count($complementos)),4,$totalComplementos2,'R',0,'C',false);
			} else {
				$pdf->Cell((104/count($complementos)),4,$totalComplementos3,'R',0,'C',false);
			}
			$columna++;
		}
		$pdf->Cell(0,4,utf8_decode($totalEstudiantesEntregas),'R',0,'C',false);
		$pdf->SetXY($aux_x, $aux_y);
		$pdf->Cell(0,4,utf8_decode(''),'B',0,'L',false);


		$pdf->Ln(8);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->Cell(0,4,utf8_decode('OBSERVACIONES'),'B',0,'C',true);
		$pdf->SetFont('Arial','',$tamannoFuente-1);
		$pdf->SetXY($x, $y+4);
		$pdf->MultiCell(0,4,"",0,'L',false);
		$pdf->SetXY($x, $y);
		$pdf->Cell(0,12,utf8_decode(''),1,0,'C',false);


		$pdf->Ln(16);
		$pdf->SetFont('Arial','',$tamannoFuente);
		$pdf->Cell(0,4,utf8_decode('La presente certificación se expide como soporte de pago y con base en el registro diario de titulares de derecho, que se diligencia en cada institución'),0,4,'L',false);
		$pdf->Cell(0,4,utf8_decode('educativa atendida. Decreto 1852 de 2015 capitulo 4 artículo 2.3.1.4.4, Resolución 29452 / 2017 capítulo 4 numeral 4.1.2 Aplicación de protocolo de'),0,4,'L',false);
		$pdf->Cell(0,4,utf8_decode('Suplencia reportados en documento adicional a las planillas.'),0,4,'L',false);


		$pdf->Ln(4);
		$pdf->SetFont('Arial','B',$tamannoFuente);
		$pdf->Cell(50,4,utf8_decode('PARA CONSTANCIA SE FIRMA EN:'),0,0,'L',false);
		$pdf->Cell(30,4,utf8_decode(''),'B',0,'L',false);
		$pdf->Cell(20,4,utf8_decode(' FECHA: DIA'),0,0,'L',false);
		$pdf->Cell(35,4,utf8_decode(''),'B',0,'L',false);
		$pdf->Cell(15,4,utf8_decode('DEL AÑO'),0,0,'L',false);
		$pdf->Cell(15,4,utf8_decode(''),'B',0,'L',false);


		$pdf->Ln(8);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->SetXY($x, $y);
		$pdf->Cell(0,12,utf8_decode(''),1,0,'C',false);

		$pdf->Ln(14);
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$pdf->SetFont('Arial','',$tamannoFuente-1);
		$pdf->Cell(0,4,utf8_decode('Impreso por Software InfoPae'),0,0,'L',false);
		$link = 'http://www.infopae.com.co';
		$pdf->SetXY($x+45, $y);
		$pdf->Write(4,'www.infopae.com.co',$link);
	}
	}

	$pdf->Output();
}
else
{
	echo "<script>alert('No se han encontrado entregas en el mes correspondiente.'); window.close()</script>";
}

function mesNombre($mes) {
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