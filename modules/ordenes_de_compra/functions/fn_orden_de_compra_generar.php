<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
include 'fn_funciones.php';





//var_dump($_POST);













$annoActual = $_SESSION['periodoActual'];
date_default_timezone_set('America/Bogota');
$fecha = date("Y-m-d H:i:s");

/* Consecutivo orden de compra general */
$consecutivoGeneral = '';
$consulta = " select Consecutivo from documentos where Tipo = 'OCO' ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$consecutivoGeneral = $row['Consecutivo'];
}//Termina el if que valida que si existan resultados
$resultado->close();

$aux = (int)$consecutivoGeneral+1;
$consulta = " update documentos set consecutivo = $aux where Tipo = 'OCO' ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
/* Termina consecutivo orden de compra general general */















// Vamos a usar una veriable bandera para determinar si finalmente se pueden hacer las inserciónes.
$tipo = '';
$dias = '';
$items = [];
$menus = [];
$nombre = '';
$semana = '';
$bandera = 0;
$documento = '';
$tipoDespacho = '';
$tipoDocumento = '';
$tablaAnno = $_SESSION['periodoActual'];
$annoActual = $_SESSION['periodoActualCompleto'];

// Son los menus que se van a mostrar en la planilla x,x,x,x,

$sedes = [];
$login = '';
$placa = '';
$usuario = '';
$menusReg = [];
$idUsuario = '';
$conductor = '';
$bodegaOrigen = '';
$menusEtarios = [];
$tipoTransporte = '';



	$rutaMunicipio = $_POST['rutaMunicipio'];












	if(isset($_POST['proveedorEmpleado']) && $_POST['proveedorEmpleado'] != ''){
		$documento = $_POST['proveedorEmpleado'];
	}

	if(isset($_POST['proveedorEmpleadoNm']) && $_POST['proveedorEmpleadoNm'] != ''){
		$nombre = $_POST['proveedorEmpleadoNm'];
	}

	if(isset($_POST['subtipoNm']) && $_POST['subtipoNm'] != ''){
		$tipoDocumento = $_POST['subtipoNm'];
	}

	if(isset($_POST['tipo']) && $_POST['tipo'] != ''){
		$tipo = $_POST['tipo'];
		$_SESSION['tipo'] = $tipo;
	}

	if(isset($_POST['tipoDespacho']) && $_POST['tipoDespacho'] != ''){
		$tipoDespacho = $_POST['tipoDespacho'];
		$_SESSION['tipoDespacho'] = $tipoDespacho;
	}

	if($tipoDespacho == ''){
		$tipoDespacho = 99;
	}

	if(isset($_POST['semana']) && $_POST['semana'] != ''){
		$semana = $_POST['semana'];
		$_SESSION['semana'] = $semana;
	}

	if(isset($_POST['dias']) && $_POST['dias'] != ''){
		$dias = $_POST['dias'];
	}

	if(isset($_POST['bodegaOrigen']) && $_POST['bodegaOrigen'] != ''){
		$bodegaOrigen = $_POST['bodegaOrigen'];
	}

	if(isset($_POST['tipoTransporte']) && $_POST['tipoTransporte'] != ''){
		$tipoTransporte = $_POST['tipoTransporte'];
	}

	if(isset($_POST['conductor']) && $_POST['conductor'] != ''){
		$conductor = $_POST['conductor'];
	}

	if(isset($_POST['placa']) && $_POST['placa'] != ''){
		$placa = $_POST['placa'];
	}

	if(isset($_POST['itemsDespacho']) && $_POST['itemsDespacho'] != ''){
		$sedes = $_POST['itemsDespacho'];
		$_SESSION['sedes'] = $sedes;
	}

	if(isset($_POST['itemsDespachoVariaciones']) && $_POST['itemsDespachoVariaciones'] != ''){
		$sv = $_POST['itemsDespachoVariaciones'];
		$sv = trim($sv, ", ");
		$sedesv1 = explode(', ', $sv);

		$sedes_variaciones = [];

		foreach ($sedesv1 as $row => $sedesv1_item) {
			$sedesv2 = explode('-', $sedesv1_item);

			$sedes_variaciones[$sedesv2[0]] = $sedesv2[1];
		}

		// exit(var_dump($sedes_variaciones));

	}

	if(isset($_SESSION['usuario']) && $_SESSION['usuario'] != ''){
		$usuario = $_SESSION['usuario'];
	}

	if(isset($_SESSION['login']) && $_SESSION['login'] != ''){
		$login = $_SESSION['login'];
	}

	if(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != ''){
		$idUsuario = $_SESSION['id_usuario'];
	}

	$sedes = $_POST['itemsDespacho'];

// Tipos de despacho, para prevenir que despues de un despacho especifico se haga un despacho general
$consulta = "SELECT * FROM tipo_despacho ";
$resultado = $Link->query($consulta) or die ('Error al consultar tipos de despachos: '. mysqli_error($Link));
$tiposDespacho = array();
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$tiposDespacho[] = $row['Id'];
	}
}

// Se van a buscar el mes y el año a partir de la tabla de planilla semana y se va a verificar la existencia de las tablas.
$consulta = " select ano, mes, semana from planilla_semanas where semana = '$semana' limit 1 ";
$resultado = $Link->query($consulta) or die ('Error al cosultar planillas_semanas: '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$semanaMes = $row['mes'];
		$semanaAnno = $row['ano'];
	}
}
$semanaAnno = substr($semanaAnno, -2);
$annoMes = $semanaMes.$semanaAnno;






/************************** Modificación dónde se agregan 3 nuevos campos (Cobertura_G1, Cobertura_G2, Cobertura_G3) **************************/
// Consulta que valida si existe la tabla orden_compra_encMESAÑO NO existe para crearla.
$consulta = "SHOW TABLES LIKE 'orden_compra_enc$annoMes'";
$result = $Link->query($consulta) or die ('Error al consultar las tablas orden_compra_enc: '. mysqli_error($Link));
$existe = $result->num_rows;
if($existe == 0) {
	// Consulta para crear la tabla orden_compra_encMESAÑO.
	$consulta = "CREATE TABLE `orden_compra_enc$annoMes` (
								`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
								`Tipo_Doc` varchar(10) NOT NULL DEFAULT '',
								`Num_Doc` int(10) unsigned NOT NULL,
								`Num_OCO` int(10) unsigned NOT NULL,
								`proveedor` varchar(100) NOT NULL DEFAULT '',
								`rutaMunicipio` varchar(100) NOT NULL DEFAULT '',
								`Tipo` varchar(100) NOT NULL DEFAULT '',
								`Nombre` varchar(200) NOT NULL DEFAULT '',
								`Nit` varchar(20) NOT NULL DEFAULT '',
								`Concepto` varchar(500) NOT NULL,
								`FechaHora_Elab` datetime NOT NULL,
								`Id_Usuario` int(10) unsigned NOT NULL,
								`cod_Sede` bigint(20) unsigned NOT NULL,
								`Tipo_Complem` varchar(10) NOT NULL,
								`Semana` varchar(5) NOT NULL DEFAULT '0',
								`Dias` varchar(20) NOT NULL DEFAULT '',
								`Menus` varchar(20) NOT NULL DEFAULT '',
								`Cobertura` int(10) unsigned DEFAULT '0',
								`Estado` smallint(1) unsigned zerofill NOT NULL DEFAULT '1' COMMENT '1= DESPACHADO  0=ELIMINADO   2=PENDIENTE',
								`TipoDespacho` smallint(1) unsigned NOT NULL DEFAULT '5',
								`Cobertura_G1` int(10) unsigned DEFAULT '0',
								`Cobertura_G2` int(10) unsigned DEFAULT '0',
								`Cobertura_G3` int(10) unsigned DEFAULT '0',
								PRIMARY KEY (`ID`)
								

							)";
	$result = $Link->query($consulta) or die ('Unable to execute query: Crear tabla orden_compra_enc '. mysqli_error($Link));
}

// Despachos det
$consulta = " show tables like 'orden_compra_det$annoMes' ";
$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
$existe = $result->num_rows;
if($existe <= 0){
	$consulta = " CREATE TABLE `orden_compra_det$annoMes` ( `id` int(10) unsigned NOT NULL AUTO_INCREMENT, `Tipo_Doc` varchar(10) NOT NULL DEFAULT '', `Num_Doc` int(10) unsigned NOT NULL, `cod_Alimento` varchar(20) NOT NULL, `Id_GrupoEtario` int(10) unsigned NOT NULL, `Cantidad` decimal(28,8) NOT NULL DEFAULT '0.00000000', `D1` decimal(28,8) DEFAULT '0.00000000', `D2` decimal(28,8) DEFAULT '0.00000000', `D3` decimal(28,8) DEFAULT '0.00000000',
	`D4` decimal(28,8) DEFAULT '0.00000000',
	`D5` decimal(28,8) DEFAULT '0.00000000',
	PRIMARY KEY (`id`) ) ";
	$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
}

$variaciones = array('1', '3');

$sedes_POST = $sedes;

$despachado = false;

//exit(json_encode($sedes_POST));

foreach ($variaciones as $id => $variacion) {


	$annoActual = $_SESSION['periodoActualCompleto'];

	unset($sedes);
	$sedes = [];

	foreach ($sedes_POST as $id_sede => $sede) {
		if ($sedes_variaciones[$sede] == $variacion) {
			$sedes[] = $sede;
		}
	}

	if (count($sedes) == 0) {
		continue;
	}


		// 1. Armar array con los componentes
		// Primera consulta, la que trae los diferentes alimentos de los menu.

			$consulta = " SELECT
												ps.MENU,
												ps.NOMDIAS,
												ft.Nombre,
												p.Cod_Grupo_Etario,
												p.cod_variacion_menu as variacion_menu,
												ft.Codigo AS codigo_menu,
												ftd.codigo,
												ftd.Componente,
												ftd.Cantidad,
												ftd.UnidadMedida
										FROM planilla_semanas ps
										INNER JOIN productos$tablaAnno p ON ps.menu = p.orden_ciclo
										INNER JOIN fichatecnica ft ON p.Codigo = ft.Codigo
										INNER JOIN fichatecnicadet ftd ON ftd.IdFT = ft.Id ";
			$consulta = $consulta." where ps.SEMANA = '$semana'
			and ft.Nombre IS NOT NULL
			and p.Cod_Tipo_complemento = '$tipo' AND p.cod_variacion_menu = '$variacion'";
			//echo "<br>$consulta<br>";



			$diasDespacho = '';
			for ($i=0; $i < count($dias) ; $i++) {
				if($i == 0){ $consulta = $consulta." AND ( "; }
					else{
					 $consulta = $consulta." OR ";
					 $diasDespacho = $diasDespacho.',';
					}
					$diasDespacho = $diasDespacho.$dias[$i];

				 $consulta = $consulta." ps.DIA = ".$dias[$i]." ";
			}
			if(count($dias) > 0){
				$consulta = $consulta." ) ";
			}
			$consulta = $consulta." order by ftd.codigo asc ";

		// Imprimir primera consulta
		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
		if($resultado->num_rows >= 1){
			$aux = 0;
			while($row = $resultado->fetch_assoc()) {
				$items[] = $row;
				$menus[] = $row['codigo_menu'];
				// $menusReg[] = $row['MENU'];
			}// Termina el while
		}//Termina el if que valida que si existan resultados
		$resultado->close();


		// $menusReg = array_unique($menusReg);
		// sort($menusReg);
		// $menusReg = implode(",",$menusReg);


		/*****************************************************************************/
		// Consulta que retorna los menus de los días seleccionados.
		$condicionDias = $menus = "";
		for ($i=0; $i < count($dias) ; $i++) {
			$condicionDias .= "dia = '". $dias[$i] ."' OR ";
		}
		$consultaMenusDias = "SELECT * FROM planilla_semanas WHERE semana = '$semana' AND (". trim($condicionDias, " OR ") .");";
		$resultadoMenusDias = $Link->query($consultaMenusDias) or die("Error al consultar planilla_semanas. Linea 248: ". $Link->error);
		if ($resultadoMenusDias->num_rows > 0) {
			while ($resgistroMenusDias = $resultadoMenusDias->fetch_assoc()) {
				$menus .= $resgistroMenusDias["MENU"] .", ";
			}
		}

		$menusReg = $menus;
		// exit($menus);
		/*****************************************************************************/


		// Debemos buscar el codigo del alimento sin preparar para obtener el
		// codigo que es y las unidades que le afectan

		$itemsIngredientes = array();

		// exit(var_dump($items));
		for ($i=0; $i < count($items); $i++) {
			$item = $items[$i];
			$codigo = $item['codigo'];
			$variacion_menu = $item['variacion_menu'];

			// Consulta 2 o segunda consulta
			$consulta = "SELECT
					ft.Codigo AS codigo_preparado,
					ftd.codigo,
					ftd.Componente,
					p.nombreunidad2 AS presentacion,
					p.cantidadund1 AS cantidadPresentacion,
					p.cantidadund2 AS factor,
					p.cantidadund3,
					p.cantidadund4,
					p.cantidadund5,
					p.cod_variacion_menu as variacion,
					m.grupo_alim,
					ftd.Cantidad,
					ftd.UnidadMedida,
					ftd.PesoNeto,
					ftd.PesoBruto,
					td.Redondeo
				FROM fichatecnica ft
					INNER JOIN fichatecnicadet ftd ON ft.id=ftd.idft
					INNER JOIN productos$tablaAnno p ON ftd.codigo=p.codigo
					INNER JOIN menu_aportes_calynut m ON ftd.codigo=m.cod_prod
					INNER JOIN tipo_despacho td ON td.Id = p.TipoDespacho
				WHERE ft.codigo = $codigo AND ftd.tipo = 'Alimento'";
			if ($tipoDespacho != 99) {
				$consulta = $consulta." and p.tipodespacho = $tipoDespacho ";
			}
			//echo "<br>$consulta<br>";
		
			// Impresión de la segunda consulta
			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			if($resultado->num_rows >= 1) {
				$ingredientes = 1;
				while($row = $resultado->fetch_assoc()){
					$item['factor'] = $row['factor'];
					$item['codigo'] = $row['codigo'];
					$item['variacion'] = $row['variacion'];
					$item['variacion_menu'] = $variacion_menu;
					$item['codigoPreparado'] = $codigo;
					$item['Componente'] = $row['Componente'];
					$item['grupo_alim'] = $row['grupo_alim'];
					$item['presentacion'] = $row['presentacion'];
					$item['cantidadund3'] = $row['cantidadund3'];
					$item['cantidadund4'] = $row['cantidadund4'];
					$item['cantidadund5'] = $row['cantidadund5'];
					$item['unidadMedida'] = $row['UnidadMedida'];
					$item['cantidadPresentacion'] = $row['cantidadPresentacion'];
					$item['redondeo'] = $row['Redondeo'];

					//IMPORTANTE los calculos se hacen con peso bruto con ecepcion de los RI
					if($tipo == 'CAJMRI'){
						$item['cantidad'] = $row['Cantidad'];
					} else {
						$item['cantidad'] = $row['PesoBruto'];
					}

					$itemsIngredientes[] = $item;
					$ingredientes++;
				}
				//var_dump($item);
			} else {
				if ($tipoDespacho == 99) {
					// Si no se encontraron ingredientes vamos a mostrar un mensaje.
					echo "No se encontraron alimentos sin preparar para el codigo: $codigo";
					//echo " <div class='error'>No se encontraron alimentos sin preparar para el codigo: $codigo <br> $consulta </div> ";
					$bandera++;
					break;
				}
			}
		}
		$items = $itemsIngredientes;

		// Se armara un array con las coverturas de las sedes para cada uno de los grupos etarios
		// y al final se creara un array con los totales de las sedes.

		$total1 = 0;
		$total2 = 0;
		$total3 = 0;
		$totalTotal = 0;


		$sedesCobertura = array();
		$sedes_variacion = [];

		for ($i=0; $i < count($sedes) ; $i++) {

			$auxSede = $sedes[$i];

			$consulta = " SELECT distinct cod_sede, Etario1_$tipo, Etario2_$tipo, Etario3_$tipo from sedes_cobertura where semana = '$semana' and cod_sede = $auxSede and Ano = $annoActual ";

			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			if($resultado->num_rows >= 1){
				while($row = $resultado->fetch_assoc()) {
					$sedeCobertura['cod_sede'] = $row['cod_sede'];
					$aux1 = "Etario1_$tipo";
					$sedeCobertura['grupo1'] = $row[$aux1];
					$aux2 = "Etario2_$tipo";
					$sedeCobertura['grupo2'] = $row[$aux2];
					$aux3 = "Etario3_$tipo";
					$sedeCobertura['grupo3'] = $row[$aux3];
					$sedeCobertura['total'] = $row[$aux1] + $row[$aux2] + $row[$aux3];
					$sedesCobertura[] = $sedeCobertura;
					$total1 = $total1 + $row[$aux1];
					$total2 = $total2 + $row[$aux2];
					$total3 = $total3 + $row[$aux3];
					$totalTotal = $totalTotal +  $sedeCobertura['total'];
				}

			}
		}

		$totalesSedeCobertura  = array(
				"grupo1" => $total1,
				"grupo2" => $total2,
				"grupo3" => $total3,
				"total"  => $totalTotal
		);

		$_SESSION['sedesCobertura'] = $sedesCobertura;
		$_SESSION['totalesSedeCobertura'] = $totalesSedeCobertura;


		// Se va a revisar que no existan despachos despachados (1) o pendiente (2) para el mismo complemento, en la misma semana dias sede
		for ($i=0; $i < count($sedesCobertura) ; $i++) {
			 $auxSede = $sedesCobertura[$i];
			 $auxSede = $auxSede['cod_sede'];
			 $consulta = " SELECT de.*,s.nom_sede, td.Descripcion as tipoDespachoNm
			 FROM orden_compra_enc$annoMes de
			 LEFT JOIN sedes$tablaAnno s on s.cod_sede = de.cod_Sede
			 LEFT JOIN tipo_despacho td on de.TipoDespacho = td.Id
			 WHERE de.Semana = '$semana' AND de.cod_sede = '$auxSede' AND de.Tipo_Complem = '$tipo' AND (de.Estado = 1 OR de.Estado = 2) ";


			if($tipoDespacho != 99){
				$consulta = $consulta." and (TipoDespacho = $tipoDespacho or TipoDespacho = 99) ";
			} else {
				$consulta = $consulta." and ( TipoDespacho = 99 ";
				foreach ($tiposDespacho as $tiposDespachoItem) {
					$consulta = $consulta." or TipoDespacho = $tiposDespachoItem ";
				}
				$consulta = $consulta." ) ";
			}

			 //Vamos a recorrer el vector de los dias para agregarlos a la consulta e identificar los despachos
			 //de un mismo día de la semana.
			 $consulta = $consulta." and ( ";
			 for ($j=0; $j < count($dias) ; $j++) {
					if($j > 0){
						 $consulta = $consulta." or ";
					}
					$aux = $dias[$j];
					//$consulta = $consulta." Dias like '%$aux%' ";
					$consulta = $consulta." FIND_IN_SET('$aux', Dias) ";

			 }
			 $consulta = $consulta." ) ";

			 //echo "<br><br>$dias<br><br>";
			 //echo "<br><br>$consulta<br><br>";

			 $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			 if($resultado->num_rows >= 1){
					$bandera++;
					$row = $resultado->fetch_assoc();
					$nomSede = $row['nom_sede'];
					$diasEncontrados = $row['Dias'];
					$tipoDespachoNm = $row['tipoDespachoNm'];
					echo "Se ha encontrado un despacho para la sede: $nomSede en la semana: $semana de tipo de ración: $tipo de tipo de despacho: $tipoDespachoNm para los días: $diasEncontrados";
					break;
			 }
		}

		// Controlamos la existencia de items para hacer el despacho dado que si no
		// encontro ningun alimento no deberia hacer el despacho
		$catidadItems = 0;
		if(isset($items)){
			$cantidadItems = count($items);
		}


		// Si la bandera es igual a cero entonces no existen registros para el esa sede en esa semana y podemos proceder a hacer la inserción.
		if($bandera == 0 && $cantidadItems > 0){
			// Vamos a crear el Array de complementosCantidades para hacer las
			// inserciones con las cantidades precisas
			// Para poder contabilizar que cantidad de un alimento para un grupo etario para un día determinado de la semana, se hace necesario la inclusion de 5 variables d1,d2,d3,d4,d5 que representan los días de actividad escolar para cada uno de los grupos etarios, en total serian 15 variables que se agregarian al array.
			$item = $items[0];

			$complementoCantidades = array(
				"nomDias" => $item["NOMDIAS"],
				"codigoPreparado" => $item["codigoPreparado"],
				"codigo" => $item["codigo"],
				"variacion" => $item["variacion"],
				"variacion_menu" => $item["variacion_menu"],
				"Componente" => $item["Componente"],
				"presentacion" => $item["presentacion"],
				"cantidadPresentacion" => $item["cantidadPresentacion"],
				"grupo_alim" => $item["grupo_alim"],
				"cantidad" => $item["cantidad"],
				"unidadMedida" => $item["unidadMedida"],
				"factor" => $item["factor"],

				"cantidadund3" => $item['cantidadund3'],
				"cantidadund4" => $item['cantidadund4'],
				"cantidadund5" => $item['cantidadund5'],
				
				"redondeo" => $item['redondeo'],

				"cantidadGrupo1"  => 0,
				"cantidadGrupo2"  => 0,
				"cantidadGrupo3"  => 0,
				"grupo1"  => 0,
				"grupo2"  => 0,
				"grupo3"  => 0,
				"total"   => 0,
				"grupo1_d1" => 0,
				"grupo1_d2" => 0,
				"grupo1_d3" => 0,
				"grupo1_d4" => 0,
				"grupo1_d5" => 0,
				"grupo2_d1" => 0,
				"grupo2_d2" => 0,
				"grupo2_d3" => 0,
				"grupo2_d4" => 0,
				"grupo2_d5" => 0,
				"grupo3_d1" => 0,
				"grupo3_d2" => 0,
				"grupo3_d3" => 0,
				"grupo3_d4" => 0,
				"grupo3_d5" => 0
			);
			if($item["Cod_Grupo_Etario"] == 1){
				$complementoCantidades["cantidadGrupo1"]++;
				$complementoCantidades["grupo1"] = $item['cantidad'] * $item['cantidadPresentacion'];
				//$totalesSedeCobertura
				$complementoCantidades["grupo2"] = 0;
				$complementoCantidades["grupo3"] = 0;
				$complementoCantidades["total"] = $complementoCantidades["grupo1"];

				$auxDias = $item["NOMDIAS"];
				switch ($auxDias) {
					case "lunes":
						$complementoCantidades["grupo1_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "martes":
						$complementoCantidades["grupo1_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "miércoles":
						$complementoCantidades["grupo1_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "jueves":
						$complementoCantidades["grupo1_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "viernes":
						$complementoCantidades["grupo1_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
				}
			}
			else if($item["Cod_Grupo_Etario"] == 2){
				$complementoCantidades["cantidadGrupo2"]++;
				$complementoCantidades["grupo1"] = 0;
				$complementoCantidades["grupo2"] = $item['cantidad'] * $item['cantidadPresentacion'];
				$complementoCantidades["grupo3"] = 0;
				$complementoCantidades["total"] = $complementoCantidades["grupo2"];

				$auxDias = $item["NOMDIAS"];
				switch ($auxDias) {
					case "lunes":
						$complementoCantidades["grupo2_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "martes":
						$complementoCantidades["grupo2_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "miércoles":
						$complementoCantidades["grupo2_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "jueves":
						$complementoCantidades["grupo2_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "viernes":
						$complementoCantidades["grupo2_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
				}

			}
			else if($item["Cod_Grupo_Etario"] == 3){
				$complementoCantidades["cantidadGrupo3"]++;
				$complementoCantidades["grupo1"] = 0;
				$complementoCantidades["grupo2"] = 0;
				$complementoCantidades["grupo3"] = $item['cantidad'] * $item['cantidadPresentacion'];
				$complementoCantidades["total"] = $complementoCantidades["grupo3"];

				$auxDias = $item["NOMDIAS"];
				switch ($auxDias){
					case "lunes":
						$complementoCantidades["grupo3_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "martes":
						$complementoCantidades["grupo3_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "miércoles":
						$complementoCantidades["grupo3_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "jueves":
						$complementoCantidades["grupo3_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
					case "viernes":
						$complementoCantidades["grupo3_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
						break;
				}

			}

			// Agregando el primer complemento al Array de complementosCantidades, a
			// continuación se agregaran los demas elementos buscando cuales se repiten
			// para poder determinar las cantidades de cada complemento.
			$complementosCantidades[] = $complementoCantidades;

			for ($i=1; $i <  count($items); $i++) {
				$item = $items[$i];
				//var_dump($item);
				$encontrado = 0;

				for ($j=0; $j < count($complementosCantidades) ; $j++) {
					$complemento = $complementosCantidades[$j];
					if($item["codigo"] == $complemento['codigo']){
							$encontrado++;

							if($item["Cod_Grupo_Etario"] == 1){
								$complemento["cantidadGrupo1"]++;
								$complemento["grupo1"] = $complemento["grupo1"] + $item['cantidad'] * $item['cantidadPresentacion'];
								$complemento["total"] = $complemento["grupo1"] + $complemento["grupo2"] + $complemento["grupo3"];

								$auxDias = $item["NOMDIAS"];
								switch ($auxDias) {
									case "lunes":
										$complemento["grupo1_d1"] = $complemento["grupo1_d1"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "martes":
										$complemento["grupo1_d2"] = $complemento["grupo1_d2"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "miércoles":
										$complemento["grupo1_d3"] = $complemento["grupo1_d3"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "jueves":
										$complemento["grupo1_d4"] = $complemento["grupo1_d4"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "viernes":
										$complemento["grupo1_d5"] = $complemento["grupo1_d5"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
								}
							}
							else if($item["Cod_Grupo_Etario"] == 2){
								$complemento["cantidadGrupo2"]++;
								$complemento["grupo2"] = $complemento["grupo2"] + $item['cantidad'] * $item['cantidadPresentacion'];
								$complemento["total"] = $complemento["grupo1"] + $complemento["grupo2"] + $complemento["grupo3"];

								$auxDias = $item["NOMDIAS"];
								switch ($auxDias) {
									case "lunes":
										$complemento["grupo2_d1"] = $complemento["grupo2_d1"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "martes":
										$complemento["grupo2_d2"] = $complemento["grupo2_d2"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "miércoles":
										$complemento["grupo2_d3"] = $complemento["grupo2_d3"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "jueves":
										$complemento["grupo2_d4"] = $complemento["grupo2_d4"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "viernes":
										$complemento["grupo2_d5"] = $complemento["grupo2_d5"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
								}

							}
							else if($item["Cod_Grupo_Etario"] == 3){
								$complemento["cantidadGrupo3"]++;
								$complemento["grupo3"] = $complemento["grupo3"] + $item['cantidad'] * $item['cantidadPresentacion'];
								$complemento["total"] = $complemento["grupo1"] + $complemento["grupo2"] + $complemento["grupo3"];

								$auxDias = $item["NOMDIAS"];
								switch ($auxDias) {
									case "lunes":
										$complemento["grupo3_d1"] = $complemento["grupo3_d1"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "martes":
										$complemento["grupo3_d2"] = $complemento["grupo3_d2"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "miércoles":
										$complemento["grupo3_d3"] = $complemento["grupo3_d3"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "jueves":
										$complemento["grupo3_d4"] = $complemento["grupo3_d4"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
									case "viernes":
										$complemento["grupo3_d5"] = $complemento["grupo3_d5"] + $item['cantidad'] * $item['cantidadPresentacion'];
										break;
								}

							}

							$complementosCantidades[$j] = $complemento;
							break;
					}
				}
				if($encontrado == 0) {
					$complementoNuevo["nomDias"] = $item["NOMDIAS"];
					$complementoNuevo["codigoPreparado"] = $item["codigoPreparado"];
					$complementoNuevo["codigo"] = $item["codigo"];
					$complementoNuevo["variacion"] = $item["variacion"];
					$complementoNuevo["variacion_menu"] = $item["variacion_menu"];
					$complementoNuevo["Componente"] =$item["Componente"];
					$complementoNuevo["presentacion"] = $item["presentacion"];
					$complementoNuevo["cantidadPresentacion"] = $item["cantidadPresentacion"];
					$complementoNuevo["grupo_alim"] = $item["grupo_alim"];
					$complementoNuevo["cantidad"] = $item["cantidad"];
					$complementoNuevo["unidadMedida"] = $item["unidadMedida"];
					$complementoNuevo["factor"] = $item["factor"];

					$complementoNuevo["cantidadund3"] = $item["cantidadund3"];
					$complementoNuevo["cantidadund4"] = $item["cantidadund4"];
					$complementoNuevo["cantidadund5"] = $item["cantidadund5"];
					
					$complementoNuevo["redondeo"] = $item["redondeo"];

					$complementoNuevo["cantidadGrupo1"] = 0;
					$complementoNuevo["cantidadGrupo2"] = 0;
					$complementoNuevo["cantidadGrupo3"] = 0;

					$complementoNuevo["grupo1_d1"] = 0;
					$complementoNuevo["grupo1_d2"] = 0;
					$complementoNuevo["grupo1_d3"] = 0;
					$complementoNuevo["grupo1_d4"] = 0;
					$complementoNuevo["grupo1_d5"] = 0;

					$complementoNuevo["grupo2_d1"] = 0;
					$complementoNuevo["grupo2_d2"] = 0;
					$complementoNuevo["grupo2_d3"] = 0;
					$complementoNuevo["grupo2_d4"] = 0;
					$complementoNuevo["grupo2_d5"] = 0;

					$complementoNuevo["grupo3_d1"] = 0;
					$complementoNuevo["grupo3_d2"] = 0;
					$complementoNuevo["grupo3_d3"] = 0;
					$complementoNuevo["grupo3_d4"] = 0;
					$complementoNuevo["grupo3_d5"] = 0;

					if($item["Cod_Grupo_Etario"] == 1){
						$complementoNuevo["cantidadGrupo1"]=1;
						$complementoNuevo["grupo1"] = $item['cantidad'] * $item['cantidadPresentacion'];
						$complementoNuevo["grupo2"] = 0;
						$complementoNuevo["grupo3"] = 0;
						$complementoNuevo["total"] = $complementoNuevo["grupo1"];

						$auxDias = $item["NOMDIAS"];
						switch ($auxDias) {
							case "lunes":
								$complementoNuevo["grupo1_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "martes":
								$complementoNuevo["grupo1_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "miércoles":
								$complementoNuevo["grupo1_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "jueves":
								$complementoNuevo["grupo1_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "viernes":
								$complementoNuevo["grupo1_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
						}


					}
					else if($item["Cod_Grupo_Etario"] == 2){
						$complementoNuevo["cantidadGrupo2"]=1;
						$complementoNuevo["grupo1"] = 0;
						$complementoNuevo["grupo2"] = $item['cantidad'] * $item['cantidadPresentacion'];
						$complementoNuevo["grupo3"] = 0;
						$complementoNuevo["total"] = $complementoNuevo["grupo2"];

						$auxDias = $item["NOMDIAS"];
						switch ($auxDias) {
							case "lunes":
								$complementoNuevo["grupo2_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "martes":
								$complementoNuevo["grupo2_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "miércoles":
								$complementoNuevo["grupo2_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "jueves":
								$complementoNuevo["grupo2_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "viernes":
								$complementoNuevo["grupo2_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
						}

					}
					else if($item["Cod_Grupo_Etario"] == 3){
						$complementoNuevo["cantidadGrupo3"]=1;
						$complementoNuevo["grupo1"] = 0;
						$complementoNuevo["grupo2"] = 0;
						$complementoNuevo["grupo3"] = $item['cantidad'] * $item['cantidadPresentacion'];
						$complementoNuevo["total"] = $complementoNuevo["grupo3"];

						$auxDias = $item["NOMDIAS"];
						switch ($auxDias) {
							case "lunes":
								$complementoNuevo["grupo3_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "martes":
								$complementoNuevo["grupo3_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "miércoles":
								$complementoNuevo["grupo3_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "jueves":
								$complementoNuevo["grupo3_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							case "viernes":
								$complementoNuevo["grupo3_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
						}

					}
					$complementosCantidades[] = $complementoNuevo;
				}
			}// Termina el if externo el de los items
			//var_dump($complementosCantidades);

			$_SESSION['complementosCantidades'] = $complementosCantidades;

			/* Insertando movimiento */

			/* Leyendo el consecutivo */
			$consecutivo = '';
			$consulta = " select Consecutivo from documentos where Tipo = 'OCOS' ";
			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			if($resultado->num_rows >= 1){
				$row = $resultado->fetch_assoc();
				$consecutivo = $row['Consecutivo'];
			}//Termina el if que valida que si existan resultados
			$resultado->close();

			/* Se va a armar un array con los consecutivos de los movimientos, luego se
			actualizara el consecutivo */
			$consecutivos = array();
			$aux = (int)$consecutivo;

			for ($i=0; $i < count($sedesCobertura); $i++){
				$consecutivos[$i] = $aux;
				$aux = $aux + 1;
			}

			/* Actualizando el valor del consecutivo en la base de datos para futuros documentos */
			$consulta = " update documentos set consecutivo = $aux where Tipo = 'OCOS' ";
			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

		








			/************************* Modificación que agregar 3 campos mas para la tabla orden_compra_encMESAÑO *************************/
			// Consulta que inserta los registros en la tabla orden_compra_encMESAÑO.
			$consulta = " INSERT INTO orden_compra_enc$annoMes (Tipo_Doc, Num_Doc, Num_OCO, proveedor, FechaHora_Elab, Id_usuario, cod_Sede, Tipo_Complem, Semana, Cobertura, estado, concepto, Dias,Menus, TipoDespacho, Cobertura_G1, Cobertura_G2, Cobertura_G3, rutaMunicipio ) VALUES ";

			for ($i=0; $i < count($sedesCobertura); $i++) {
				if($i > 0){$consulta = $consulta." , "; }

				$consulta = $consulta." ( ";
				$sede = $sedesCobertura[$i];
				$consecutivo = $consecutivos[$i];


				// Variables asignadas para agregar los 3 campos de cobertura.
				$grupo1 = $sede["grupo1"];
				$grupo2 = $sede["grupo2"];
				$grupo3 = $sede["grupo3"];

				// Se va a actualizar el array de total sedes cobertura, dependiendo de si tenemos menus para esos grupos.
				$auxGrupo1 = 0;
				$auxGrupo2 = 0;
				$auxGrupo3 = 0;

				for ($j=0; $j < count($complementosCantidades) ; $j++) {
					$complemento = $complementosCantidades[$j];
					if($complemento['grupo1']>0){$auxGrupo1++; }
					if($complemento['grupo2']>0){$auxGrupo2++; }
					if($complemento['grupo3']>0){$auxGrupo3++; }
				}

				if($auxGrupo1 == 0){
					$sede['grupo1']=0;
					$sede['total'] = $sede['grupo1'] + $sede['grupo2'] + $sede['grupo3'];
				}

				if($auxGrupo2 == 0){
					$sede['grupo2']=0;
					$sede['total'] = $sede['grupo1'] + $sede['grupo2'] + $sede['grupo3'];
				}

				if($auxGrupo3 == 0){
					$sede['grupo3']=0;
					$sede['total'] = $sede['grupo1'] + $sede['grupo2'] + $sede['grupo3'];
				}

				$cobertura = $sede['total'];
				$sede = $sede['cod_sede'];

				$consulta = $consulta." 'OCOS',$consecutivo, $consecutivoGeneral, $documento ,'$fecha',$idUsuario,$sede,'$tipo','$semana',$cobertura,2,'', '$diasDespacho', '$menusReg', '$tipoDespacho', '$grupo1', '$grupo2', '$grupo3', '$rutaMunicipio'";
				$consulta = $consulta." ) ";
			}

			//Ejecutando inserción en orden_compra_enc
			$resultado = $Link->query($consulta) or die ('Unable to execute query: Insertando datos tabla: orden_compra_enc$annoMes '. mysqli_error($Link));

			// Insertando en orden_compra_det
			//echo "<br><br>Insertando en orden_compra_det<br><br>";




			$consulta = " insert into orden_compra_det$annoMes (tipo_doc, num_doc, cod_alimento, id_grupoetario, cantidad, d1, d2, d3, d4, d5) values ";



			// banderaPrimero: es una variable bandera que nos controla si el primer elelemnto ya fue agregado y a partir de hay colocar las comas que separan los valores de cada inserción.
			$banderaPrimero = 0;

			for ($i=0; $i < count($sedesCobertura) ; $i++) {
				$sede = $sedesCobertura[$i];
				for ($j=0; $j < count($complementosCantidades) ; $j++){

					// Se toman los datos del alimento en una cadena auxiliar para despues
					// validar si la cantidad es mayor a cero y agregar a la cadena que va
					// a hacer la insersión
					$auxAlimento = $complementosCantidades[$j];
					if ($auxAlimento['variacion_menu'] != $sedes_variaciones[$sede['cod_sede']]) {
						continue;
					}
					$gruposRegistrar = 0;
					//include 'fn_despacho_generar_presentaciones.php';
					if($j > 0 &&( ($auxAlimento['grupo1']>0 && $sede["grupo1"]>0) || ($auxAlimento['grupo2']>0 && $sede["grupo2"]>0) || ($auxAlimento['grupo3']>0  && $sede["grupo3"]>0) ) ){
						//  $consulta = $consulta." , ";
					}
					if($auxAlimento['grupo1']>0 && $sede["grupo1"]>0){
						if($banderaPrimero == 0){
							$banderaPrimero++;
						}else{
							$consulta = $consulta." , ";
						}
						$gruposRegistrar++;
						$idGrupoEtario = 1;
						$consulta = $consulta." ( ";
						$codigo = $auxAlimento['codigo'];
						$componente = $auxAlimento['Componente'];
						
						$cantidad = $auxAlimento["grupo1"] * $sede["grupo1"];



						$unidad = $auxAlimento['unidadMedida'];
						$consecutivo = $consecutivos[$i];
						$d1 = $auxAlimento["grupo1_d1"] * $sede["grupo1"];
						$d2 = $auxAlimento["grupo1_d2"] * $sede["grupo1"];
						$d3 = $auxAlimento["grupo1_d3"] * $sede["grupo1"];
						$d4 = $auxAlimento["grupo1_d4"] * $sede["grupo1"];
						$d5 = $auxAlimento["grupo1_d5"] * $sede["grupo1"];


		



						$consulta = $consulta." 'OCOS',$consecutivo, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5";
						$consulta = $consulta." ) ";
					}
					if($auxAlimento['grupo2']>0 && $sede["grupo2"]>0){
						if($banderaPrimero == 0){
							$banderaPrimero++;
						}else{
							$consulta = $consulta." , ";
						}
						$gruposRegistrar++;
						$idGrupoEtario = 2;
						$consulta = $consulta." ( ";
						$codigo = $auxAlimento['codigo'];
						$componente = $auxAlimento['Componente'];
						//$cantidad = $auxAlimento['grupo2']*$auxAlimento['cantidad'];
						$cantidad = $auxAlimento["grupo2"] * $sede["grupo2"];



						$unidad = $auxAlimento['unidadMedida'];
						$consecutivo = $consecutivos[$i];
						$d1 = $auxAlimento["grupo2_d1"] * $sede["grupo2"];
						$d2 = $auxAlimento["grupo2_d2"] * $sede["grupo2"];
						$d3 = $auxAlimento["grupo2_d3"] * $sede["grupo2"];
						$d4 = $auxAlimento["grupo2_d4"] * $sede["grupo2"];
						$d5 = $auxAlimento["grupo2_d5"] * $sede["grupo2"];



						$consulta = $consulta." 'OCOS',$consecutivo, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5";
						$consulta = $consulta." ) ";
					}
					if($auxAlimento['grupo3']>0  && $sede["grupo3"]>0){
						//$consulta = $consulta."<br><br>j = $j y i = $i<br><br>";
						if($banderaPrimero == 0){
							$banderaPrimero++;
						}else{
							$consulta = $consulta." , ";
						}
						$gruposRegistrar++;
						$idGrupoEtario = 3;
						$consulta = $consulta." ( ";
						$codigo = $auxAlimento['codigo'];
						$componente = $auxAlimento['Componente'];
						//$cantidad = $auxAlimento['grupo3']*$auxAlimento['cantidad'];
						$cantidad = $auxAlimento["grupo3"] * $sede["grupo3"];

	

						$unidad = $auxAlimento['unidadMedida'];
						$consecutivo = $consecutivos[$i];
						$d1 = $auxAlimento["grupo3_d1"] * $sede["grupo3"];
						$d2 = $auxAlimento["grupo3_d2"] * $sede["grupo3"];
						$d3 = $auxAlimento["grupo3_d3"] * $sede["grupo3"];
						$d4 = $auxAlimento["grupo3_d4"] * $sede["grupo3"];
						$d5 = $auxAlimento["grupo3_d5"] * $sede["grupo3"];



						$consulta = $consulta." 'OCOS',$consecutivo, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5";
						$consulta = $consulta." ) ";
					}
				}// Termina el for de los alimentos
			}
			//echo "<br>CONSULTA DE DESPACHO DETALLADO<br>".$consulta."<br>";
			//Ejecutando inserción en orden_compra_det


		

			$resultado = $Link->query($consulta) or die ('Inserción orden_compra_det - Unable to execute query. '. mysqli_error($Link));
			$Link->close();



			$despachado = true;
		}//Termina el if de bandera igual a 0

		//if($cantidadItems <= 0){
			//echo "No se encontraron alimentos sin preparar para este despacho.";
		//}

}

if ($despachado) {
	echo "1";
} else {
	echo "No se encontraron alimentos sin preparar para este despacho.";
}