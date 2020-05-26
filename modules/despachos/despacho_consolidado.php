<?php
error_reporting(E_ALL);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

include '../../config.php';
require_once '../../autentication.php';
require('../../fpdf181/fpdf.php');
require_once '../../db/conexion.php';
include '../../php/funciones.php';


$largoNombre = 30;
$sangria = " - ";
$tamannoFuente = 6;

$tablaAnno = $_SESSION['periodoActual'];
$tablaAnnoCompleto = $_SESSION['periodoActualCompleto'];

$hoy = date("d/m/Y");
$fechaDespacho = $hoy;

// Se va a recuperar el mes y el año para las tablaMesAnno
$mesAnno = '';
$mes = $_POST['mesiConsulta'];
if($mes < 10){ $mes = '0'.$mes; }

$mes = trim($mes);
$anno = $_POST['annoi'];
$anno = substr($anno, -2);
$anno = trim($anno);
$mesAnno = $mes.$anno;

$cget = "SELECT * FROM grupo_etario";
$resGrupoEtario = $Link->query($cget);
if ($resGrupoEtario->num_rows > 0) { while ($ge = $resGrupoEtario->fetch_assoc()) { $get[] = $ge['DESCRIPCION']; } }

$ruta = '';
if(isset($_POST['rutaNm']) && $_POST['rutaNm']!= ''){ $ruta = $_POST['rutaNm']; }

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
$_POST = array_slice($_POST, $corteDeVariables);
$_POST = array_values($_POST);

$annoActual = $tablaAnnoCompleto;
$despachosRecibidos = $_POST;

//var_dump($despachosRecibidos);

// Se va a hacer una cossulta pare cojer los datos de cada movimiento, entre ellos el municipio que lo usaremos en los encabezados de la tabla.

$mes = '';
$sede = '';
$dias = '';
$ciclo = '';
$sedes = array();
$tipos = array();
$ciclos = array();
$semanas = array();
$despachos = array();
$municipios = array();
$diasMostrar = array();
$semanasMostrar = array();

foreach ($despachosRecibidos as &$valor){
	$consulta = "SELECT de.*, tc.descripcion, u.Ciudad, tc.jornada, pm.Nombre AS nombre_proveedor FROM despachos_enc$mesAnno de INNER JOIN productosmov$mesAnno pm ON de.Num_Doc = pm.Numero INNER JOIN sedes$anno s ON de.cod_Sede = s.cod_sede INNER JOIN ubicacion u ON s.cod_mun_sede = u.CodigoDANE LEFT JOIN tipo_complemento tc ON de.Tipo_Complem = tc.CODIGO WHERE Tipo_Doc = 'DES' AND de.Num_Doc = $valor ";
	//echo "<br><br>$consulta<br><br>";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$despacho['num_doc'] = $row['Num_Doc'];
		$despacho['cod_sede'] = $row['cod_Sede'];
		$despacho['tipo_complem'] = $row['Tipo_Complem'];
		$modalidad = $despacho['tipo_complem'];
		$despacho['semana'] = $row['Semana'];
		$despacho['cobertura'] = $row['Cobertura'];
		$despacho['ciudad'] = $row['Ciudad'];
		$descripcionTipo = $row['descripcion'];
		$jornada = $row['jornada'];
		$nombre_proveedor =$row["nombre_proveedor"];

		$aux = $row['FechaHora_Elab'];
		$aux = strtotime($aux);
		if($fechaDespacho < $aux) { $fechaDespacho = date("d/m/Y",$aux); }

		// Agregando elementos a los demas array_walk_recursive
		$sedes[] = $row['cod_Sede'];
		$tipos[] = $row['Tipo_Complem'];
		$semanas[] = $row['Semana'];
		$municipios[] = $row['Ciudad'];

		//TRATAMIENTO DE LOS DIAS
		// Buscar el mes de la semana a la que pertenecen los despachos
		$auxDias = $row['Dias'];
		$diasMostrar[] = $auxDias;
		$auxMenus = $row['Menus'];
		$diasDespacho = $row['Dias'];
		$menusMostrar[] = trim($auxMenus, ", ");

		$arrayDiasDespacho = explode(',', $diasDespacho);

		if (!in_array($row['Semana'], $semanasMostrar, true)) {
			$semanasMostrar[] =  $row['Semana'];
			$semana = $row['Semana'];
			$consulta = " select * from planilla_semanas where SEMANA = '$semana' ";
			//echo "<br><br>$consulta<br><br>";
			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			$cantDias = $resultado->num_rows;
			if($resultado->num_rows >= 1)
			{
				$mesInicial = '';
				$mesesIniciales = 0;

				while($row = $resultado->fetch_assoc()){
					$clave = array_search(intval($row['DIA']), $arrayDiasDespacho);
					if($clave !== FALSE)
					{
						$ciclo = $row['CICLO'];
						$ciclos[] = $ciclo;

						if($mesInicial != $row['MES'])
						{
							$mesesIniciales++;
							if($mesesIniciales > 1) { $dias .= " de  $mes "; }
							$mesInicial = $row['MES'];
							$mes = $row['MES'];
							$mes = mesEnLetras($mes);
						}
						else
						{
							if($dias != '') { $dias .= ', '; }
						}

						$dias = $dias.intval($row['DIA']);
					}
				}

				$dias .= " de  $mes";
			}
		}
	}
	$despachos[] = $despacho;
}

// var_dump($semanasMostrar);
// var_dump($ciclos);





$auxDias = '';
for ($i=0; $i < count($diasMostrar) ; $i++)
{
	if($i > 0) { $auxDias = $auxDias.","; }
	$auxDias = $auxDias.$diasMostrar[$i];
}

$auxDias = explode(',', $auxDias);
$auxDias = array_unique ($auxDias);
sort($auxDias);
$cantDias = count($auxDias);
$auxDias = implode(", ",$auxDias);
sort($semanasMostrar);
$ciclos = array_unique ($ciclos);
sort($ciclos);

$auxDias = "X ".$cantDias." DIAS ".$auxDias." ".$mes;
$auxDias = strtoupper($auxDias);

$auxSemana = '';
for ($i=0; $i < count($semanasMostrar) ; $i++)
{
	if($i > 0){ $auxSemana = $auxSemana.", "; }
	$auxSemana = $auxSemana."'$semanasMostrar[$i]'";
}

$auxCiclos = '';
//$auxCiclos = $ciclos[0];
$auxCiclos = implode(',', $ciclos);

$auxMenus = '';
$menus_id_unique_array = [];

for ($i=0; $i < count($menusMostrar) ; $i++)
{
	$menus_id_array = explode(',', $menusMostrar[$i]);
	foreach ($menus_id_array as $menu_id)
	{
		if (! in_array(trim($menu_id), $menus_id_unique_array))
		{
			$menus_id_unique_array[] = trim($menu_id);
		}
	}
}

sort($menus_id_unique_array);

$auxMenus = implode(", ",$menus_id_unique_array);
$municipios = array_unique($municipios);
$municipios = array_values($municipios);
$tipo = $tipos[0];
$semana = $auxSemana;
//$semana = $semanas[0];

// Se armara un array con las coverturas de las sedes para cada uno de los grupos etarios y al final se creara un array con los totales de las sedes.

$total1 = 0;
$total2 = 0;
$total3 = 0;
$totalTotal = 0;

$sede_unicas = array_unique($sedes);
foreach ($sede_unicas as $key => $sede_unica)
{
	$auxSede = $sede_unica;

	$consulta = "SELECT DISTINCT max(Cobertura_G1) AS Cobertura_G1, max(Cobertura_G2) as Cobertura_G2, max(Cobertura_G3) AS Cobertura_G3, cod_sede, (max(Cobertura_G1) + max(Cobertura_G2) + max(Cobertura_G3)) sumaCoberturas FROM despachos_enc$mesAnno WHERE semana in ($semana) AND cod_sede = $auxSede AND Tipo_Complem = '". $tipo ."' ORDER BY sumaCoberturas DESC LIMIT 1";
	//echo "<br><br>$consulta<br><br>";

	// Consulta que busca las coberturas de las diferentes sedes.
	$resultado = $Link->query($consulta) or die ("Error en la consulta: Catidades coberturas<br><br>$consulta<br><br>". mysqli_error($Link));
	if($resultado->num_rows >= 1)
	{
		while($row = $resultado->fetch_assoc())
		{
			$sedeCobertura['cod_sede'] = $row['cod_sede'];
			$sedeCobertura['grupo1'] = $row["Cobertura_G1"];
			$sedeCobertura['grupo2'] = $row["Cobertura_G2"];
			$sedeCobertura['grupo3'] = $row["Cobertura_G3"];
			$sedeCobertura['total'] = $row["Cobertura_G1"] + $row["Cobertura_G2"] + $row["Cobertura_G3"];
			$sedesCobertura[] = $sedeCobertura;

			$total1 += $row["Cobertura_G1"];
			$total2 += $row["Cobertura_G2"];
			$total3 += $row["Cobertura_G3"];
			$totalTotal = $totalTotal +  $sedeCobertura['total'];
		}
	}
}

$totalesSedeCobertura  = [
	"grupo1" => $total1,
	"grupo2" => $total2,
	"grupo3" => $total3,
	"total"  => $totalTotal
];

// Termina el tema de las coberturas por sede
$totalGrupo1 = 0;
$totalGrupo2 = 0;
$totalGrupo3 = 0;

$totalcons = "";

// Vamos a buscar los alimentos de los depachos
$alimentos = array();
$tg = [];
for ($i=0; $i < count($despachos) ; $i++)
{
	$despacho = $despachos[$i];
	$numero = $despacho['num_doc'];
	
	
	
	
	// SELECT DISTINCT dd.id, dd.*, pmd.CantU1,  pmd.CantU2, pmd.CantU3, pmd.CantU4, pmd.CantU5, pmd.CanTotalPresentacion FROM despachos_det$mesAnno dd LEFT JOIN productosmovdet$mesAnno pmd ON dd.Tipo_Doc = pmd.Documento AND dd.Num_Doc = pmd.Numero AND dd.cod_Alimento = pmd.CodigoProducto WHERE dd.Tipo_Doc = 'DES' AND dd.Num_Doc = $numero  
	
	
	
	
	
	$consulta = " SELECT DISTINCT dd.id, dd.*, pmd.CantU1, CEILING(pmd.CantU2) as CantU2, CEILING(pmd.CantU3) as CantU3, CEILING(pmd.CantU4) as CantU4, CEILING(pmd.CantU5) as CantU5, pmd.CanTotalPresentacion, p.cantidadund2, p.cantidadund3, p.cantidadund4, p.cantidadund5, p.nombreunidad2, p.nombreunidad3, p.nombreunidad4, p.nombreunidad5 FROM despachos_det$mesAnno dd LEFT JOIN productosmovdet$mesAnno pmd ON dd.Tipo_Doc = pmd.Documento AND dd.Num_Doc = pmd.Numero AND dd.cod_Alimento = pmd.CodigoProducto LEFT JOIN productos$anno p ON dd.cod_Alimento = p.Codigo WHERE dd.Tipo_Doc = 'DES' AND dd.Num_Doc = $numero ";
	//echo "<br><br>$consulta<br><br>";






	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	if($resultado->num_rows >= 1)
	{
		while($row = $resultado->fetch_assoc())
		{
			$alimento = array();

			if(isset($tg[$row['cod_Alimento']][$numero]))
			{
				$tg[$row['cod_Alimento']][$numero] += $row['Cantidad'];
			}
			else
			{
				$tg[$row['cod_Alimento']][$numero] = $row['Cantidad'];
			}

			$alimento['codigo'] = $row['cod_Alimento'];
			$auxGrupo = $row['Id_GrupoEtario'];
			$alimento['grupo'.$auxGrupo] = $row['Cantidad'];
			
			
			
			
			
			
			
			$alimento['cantotalpresentacion'] = ($row['CantU2'] * $row['cantidadund2']) + ($row['CantU3'] * $row['cantidadund3']) + ($row['CantU4'] * $row['cantidadund4']) + ($row['CantU5'] * $row['cantidadund5']);




			$alimento['cantu2'] = $row['CantU2'];
			$alimento['cantu3'] = $row['CantU3'];
			$alimento['cantu4'] = $row['CantU4'];
			$alimento['cantu5'] = $row['CantU5'];
			// Guardamos el numero de documento para discriminar la unidades de cada despacho
			$alimento['Num_Doc'] = $row['Num_Doc'];
			$alimento['componente'] = '';
			$alimento['presentacion'] = '';
			$alimento['grupo_alim'] = '';
			$alimento['nombreunidad2'] = '';
			$alimento['nombreunidad3'] = '';
			$alimento['nombreunidad4'] = '';
			$alimento['nombreunidad5'] = '';
			
			$alimento['cantidadund2'] = $row['cantidadund2'];;
			$alimento['cantidadund3'] = $row['cantidadund3'];;
			$alimento['cantidadund4'] = $row['cantidadund4'];;
			$alimento['cantidadund5'] = $row['cantidadund5'];;
	

			$alimentos[] = $alimento;
		}
	}
}
//var_dump($alimentos);


// Vamos unificar los alimentos para que no se repitan
$alimento = $alimentos[0];


if(!isset($alimento['grupo1'])){ $alimento['grupo1'] = 0;}else{ $totalGrupo1 = $totalesSedeCobertura['grupo1']; }
if(!isset($alimento['grupo2'])){ $alimento['grupo2'] = 0;}else{ $totalGrupo2 = $totalesSedeCobertura['grupo2']; }
if(!isset($alimento['grupo3'])){ $alimento['grupo3'] = 0;}else{ $totalGrupo3 = $totalesSedeCobertura['grupo3']; }
$alimentosTotales = array();
$alimentosTotales[] = $alimento;
for ($i=1; $i < count($alimentos) ; $i++){
	$alimento = $alimentos[$i];
	if(!isset($alimento['grupo1'])){ $alimento['grupo1'] = 0;}else{ $totalGrupo1 = $totalesSedeCobertura['grupo1']; }
	if(!isset($alimento['grupo2'])){ $alimento['grupo2'] = 0;}else{ $totalGrupo2 = $totalesSedeCobertura['grupo2']; }
	if(!isset($alimento['grupo3'])){ $alimento['grupo3'] = 0;}else{ $totalGrupo3 = $totalesSedeCobertura['grupo3']; }
	$encontrado = 0;

	for ($j=0; $j < count($alimentosTotales) ; $j++){
		$alimentoTotal = $alimentosTotales[$j];
		if($alimento['codigo'] == $alimentoTotal['codigo']){
			$encontrado++;
			
			
			/*
			CONSULTA DE CONTROL DE LA SUMA DE CANTIDADES
			0307001 ACEITE
			0303007 ARROZ BLANCO
			*/
			// if($alimento['codigo'] == '0307001'){
			// 	echo "<br>Tenia:<br>";
			// 	var_dump($alimentoTotal['cantotalpresentacion']);
			// }



			
			if($alimentoTotal['Num_Doc'] != $alimento['Num_Doc']){
				$alimentoTotal['cantotalpresentacion'] = $alimentoTotal['cantotalpresentacion'] + $alimento['cantotalpresentacion'];
				$alimentoTotal['cantu2'] = $alimentoTotal['cantu2'] + $alimento['cantu2'];
				$alimentoTotal['cantu3'] = $alimentoTotal['cantu3'] + $alimento['cantu3'];
				$alimentoTotal['cantu4'] = $alimentoTotal['cantu4'] + $alimento['cantu4'];
				$alimentoTotal['cantu5'] = $alimentoTotal['cantu5'] + $alimento['cantu5'];
				$alimentoTotal['Num_Doc'] = $alimento['Num_Doc'];
			}
				
				
				$alimentoTotal['grupo1'] = $alimentoTotal['grupo1'] + $alimento['grupo1'];
				$alimentoTotal['grupo2'] = $alimentoTotal['grupo2'] + $alimento['grupo2'];
				$alimentoTotal['grupo3'] = $alimentoTotal['grupo3'] + $alimento['grupo3'];
				
				$alimentosTotales[$j] = $alimentoTotal;
				/*
				CONSULTA DE CONTROL DE LA SUMA DE CANTIDADES
				0307001 ACEITE
				0303007 ARROZ BLANCO
				*/
				// if($alimento['codigo'] == '0307001'){
				// 	var_dump($alimento['Num_Doc']);
				// 	var_dump($alimento['cantotalpresentacion']);
				// 	var_dump($alimentoTotal['cantotalpresentacion']);
				// }




			break;
		}
	}

	if($encontrado == 0) { $alimentosTotales[] = $alimento; }
}
//var_dump($alimentosTotales);







// Vamos a traer los datos que faltan para mostrar en la tabla
for ($i=0; $i < count($alimentosTotales) ; $i++)
{
	$alimentoTotal = $alimentosTotales[$i];
	$auxCodigo = $alimentoTotal['codigo'];
	$auxDespacho = $alimentoTotal["Num_Doc"];
	$consulta = "SELECT DISTINCT p.Codigo, p.Descripcion AS Componente, p.nombreunidad2 presentacion,m.grupo_alim,m.orden_grupo_alim, p.NombreUnidad2, p.NombreUnidad3, p.NombreUnidad4, p.NombreUnidad5,
								(SELECT Umedida FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $auxDespacho AND CodigoProducto = $auxCodigo limit 1 ) AS Umedida
							FROM productos$anno p
							LEFT JOIN fichatecnicadet ftd ON ftd.codigo=p.Codigo
							INNER JOIN menu_aportes_calynut m ON p.Codigo=m.cod_prod
							WHERE p.Codigo = $auxCodigo";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

	if($resultado->num_rows >= 1)
	{
		$row = $resultado->fetch_assoc();
		$alimentoTotal['componente'] = $row['Componente'];
		$alimentoTotal['presentacion'] = $row['Umedida'];
		$alimentoTotal['grupo_alim'] = $row['grupo_alim'];
		$alimentoTotal['orden_grupo_alim'] = $row['orden_grupo_alim'];

		$alimentoTotal['nombreunidad2'] = $row['NombreUnidad2'];
		$alimentoTotal['nombreunidad3'] = $row['NombreUnidad3'];
		$alimentoTotal['nombreunidad4'] = $row['NombreUnidad4'];
		$alimentoTotal['nombreunidad5'] = $row['NombreUnidad5'];

		$alimentosTotales[$i] = $alimentoTotal;
	}
}

mysqli_close($Link);

unset($sort);
unset($grupo);
$sort = array();
$grupo = array();
foreach($alimentosTotales as $kOrden=>$vOrden)
{
	$sort['componente'][$kOrden] = $vOrden['componente'];
	$sort['grupo_alim'][$kOrden] = $vOrden['orden_grupo_alim']; //Se cambia el orden de acuerdo al orden por grupo de alimento
	$grupo[$kOrden] = $vOrden['grupo_alim'];
}

array_multisort($sort['grupo_alim'], SORT_ASC,$alimentosTotales);

sort($grupo);


class PDF extends FPDF
{
	function Header(){}
	function Footer(){}

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
$pdf= new PDF('L','mm',array(279.4, 216));
$pdf->SetMargins(8, 6.31, 8);
$pdf->SetAutoPageBreak(TRUE, 5);
$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(.05);
$pdf->SetFont('Arial','',$tamannoFuente);

$tamano_carta = TRUE;
include 'despacho_consolidado_footer.php';
include 'despacho_consolidado_header.php';

$filas = 0;
$grupoAlimActual = '';

$pdf->SetFont('Arial','',$tamannoFuente);

$grupos_alimentarios = [];
for ($i=0; $i < count($alimentosTotales) ; $i++)
{
	$alimento = $alimentosTotales[$i];
	$grupos_alimentarios[$alimento['grupo_alim']][] = $alimento;
}

$posicion_X_celda_grupo_alimenticio = $pdf->GetX();

foreach ($grupos_alimentarios as $nombre_grupo => $grupo_alimentario)
{

	if (isset($posicion_Y_celda_alimento)) { $pdf->SetY($posicion_Y_celda_alimento); }

	$altura_celda_grupo_alimenticio = 0;
	$altura_celda_grupo_alimenticio = count($grupo_alimentario) * 4;
	if (($altura_celda_grupo_alimenticio + (isset($posicion_Y_celda_alimento) ? $posicion_Y_celda_alimento : 0)) > 210.9)
	{
		$pdf->AddPage();
		include 'despacho_consolidado_header.php';
	}

	$posicion_Y_celda_grupo_alimenticio = $pdf->GetY();



	foreach ($grupo_alimentario as $alimento){

		//var_dump($alimento);


		$pdf->Cell(23.788, 4, "", 0);



		$aux = $alimento['componente'];
		$long_nombre=strlen($aux);
		//var_dump($largoNombre);
		$largoNombre =37;
		if($long_nombre > $largoNombre){
			$aux = substr($aux,0,$largoNombre);
		}
		// Impresión de Alimento (Nombre del alimento)
		$pdf->Cell(49,4,utf8_decode($aux),1,0,'L',FALSE);


		if($alimento['grupo_alim'] == "Contramuestra"){ 
			$aux = ""; 
		}else{ 
			$aux = $alimento['grupo1']; 
			$aux = number_format($aux, 2, '.', '');
		}
		if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
		$pdf->Cell(13.1, 4, utf8_decode($aux), 1, 0, 'C', FALSE);
		
		
		if($alimento['grupo_alim'] == "Contramuestra"){ 
			$aux = ""; 
		}else{ 
			$aux = $alimento['grupo2']; 
			$aux = number_format($aux, 2, '.', '');
		}
		if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
		$pdf->Cell(13.1, 4, utf8_decode($aux), 1, 0, 'C', FALSE);
		
		
		if($alimento['grupo_alim'] == "Contramuestra"){ 
			$aux = ""; 
		}else{ 
			$aux = $alimento['grupo3']; 
			$aux = number_format($aux, 2, '.', '');
		}
		if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
		$pdf->Cell(13.1, 4, utf8_decode($aux), 1, 0, 'C', FALSE);
		
	




		$pdf->Cell(13.141, 4, $alimento['presentacion'], 1, 0, 'C', FALSE);

		if (strpos($alimento['componente'], "HUEVO9999") !== FALSE)
		{
			$total = 0;
			foreach ($tg[$alimento['codigo']] as $key => $value) { $total += ceil($value); }

			$aux = $total;
		}
		else
		{ $aux = $alimento['grupo1']+$alimento['grupo2']+$alimento['grupo3']; }

		
		
		if($alimento['grupo_alim'] == "Contramuestra"){ $aux = $alimento['cantidadund2'] + $alimento['cantidadund3'] + $alimento['cantidadund4'] + $alimento['cantidadund5']; }
		$pdf->Cell(13.141, 4, number_format($aux, 2, '.', ''), 1, 0, 'C', FALSE);















		if($alimento['presentacion'] == 'u')
		{
			if (strpos($alimento['componente'], "HUEVO9999") !== FALSE)
			{ $aux = ceil(0+$aux); }
			else
			{ $aux = round(0+$aux); }
		}
		else
		{ $aux = number_format($aux, 2, '.', ''); }




		if($alimento['cantotalpresentacion'] > 0 )
		{
			$aux = $alimento['cantotalpresentacion'];
			$aux2 = $aux;

			if($alimento['presentacion'] == 'u')
			{ $aux = round(0+$aux);           }
			else
			{ $aux = number_format($aux, 2, '.', ''); }
		}
		if($alimento['grupo_alim'] == "Contramuestra"){ $aux = $alimento['cantidadund2'] + $alimento['cantidadund3'] + $alimento['cantidadund4'] + $alimento['cantidadund5']; }
		$pdf->Cell(10.6,4,$aux,1,0,'C',FALSE);

		//total entregado
		$pdf->Cell(10.6,4,'',1,0,'C',FALSE);
		$pdf->Cell(10.6,4,'',1,0,'C',FALSE);

		// ESPECIFICACIÓN DE CALIDAD
		$pdf->Cell(13.7,4,'',1,0,'C',FALSE);
		$pdf->Cell(13.7,4,'',1,0,'C',FALSE);

		// FALTANTES
		$pdf->Cell(9.2,4,'',1,0,'C',FALSE);
		$pdf->Cell(8.9,4,'',1,0,'C',FALSE);
		$pdf->Cell(13.9,4,'',1,0,'C',FALSE);

		//DEVOLUCIÓN
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		$pdf->Cell(15.2,4,'',1,0,'C',FALSE);

		$pdf->Ln();

		$posicion_Y_celda_alimento = $pdf->GetY();
	}


	$pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
	$pdf->MultiCell(23.788, 4, utf8_decode($nombre_grupo), 0, "C");
	$pdf->SetXY($posicion_X_celda_grupo_alimenticio, $posicion_Y_celda_grupo_alimenticio);
	$pdf->MultiCell(23.788, $altura_celda_grupo_alimenticio, "", 1);
}

$pdf->Ln(4);

for ($i = 2; $i <= 5; $i++)
{
	$unidad = $i;

	if($alimento['cantu'.$unidad] > 0)
	{
		$pdf->SetX($current_x+$anchoCelda);
		$presentacion = " ".$alimento['nombreunidad'.$unidad];

		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);

		$aux = $sangria.$alimento['componente'].$presentacion;

		$long_nombre=strlen($aux);
		if($long_nombre > $largoNombre)
		{
			$aux = substr($aux,0,$largoNombre);
		}

		$pdf->Cell(68.3,4,utf8_decode($aux),1,0,'L',FALSE);
		$pdf->Cell(13.1,4,'',1,0,'C',FALSE);
		$pdf->Cell(13.1,4,'',1,0,'C',FALSE);
		$pdf->Cell(13.1,4,'',1,0,'C',FALSE);
		$pdf->Cell(13.141,4,'',1,0,'C',FALSE);
		$pdf->Cell(13.141,4,'',1,0,'C',FALSE);
		$aux = number_format($alimento['cantu'.$unidad] , 0, '.', '');

		// CANTIDAD ENTREGADA
		$pdf->Cell(9.3,4,$aux,1,0,'C',FALSE);
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		// ESPECIFICACIÓN DE CALIDAD
		$pdf->Cell(11,4,'',1,0,'C',FALSE);
		$pdf->Cell(11,4,'',1,0,'C',FALSE);
		// FALTANTES
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		//DEVOLUCIÓN
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		$pdf->Cell(9.3,4,'',1,0,'C',FALSE);
		$pdf->Ln(4);
	}
}

$current_y = $pdf->GetY();
if($current_y > 162)
{
	$pdf->AddPage();
	include 'despacho_consolidado_footer.php';
	include 'despacho_consolidado_header.php';
}

include 'despacho_firma_planilla.php';

$pdf->Output();