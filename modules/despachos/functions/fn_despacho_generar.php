<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
include 'fn_funciones.php';
set_time_limit (0);
// exit(var_dump($_POST));
// Vamos a usar una veriable bandera para determinar si finalmente se pueden hacer las inserciónes.
$tipo = '';
$dias = '';
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

// POST 
if(isset($_POST['proveedorEmpleado']) && $_POST['proveedorEmpleado'] != ''){ $documento = $_POST['proveedorEmpleado']; } 
if(isset($_POST['proveedorEmpleadoNm']) && $_POST['proveedorEmpleadoNm'] != ''){ $nombre = $_POST['proveedorEmpleadoNm']; }
if(isset($_POST['subtipoNm']) && $_POST['subtipoNm'] != ''){ $tipoDocumento = $_POST['subtipoNm']; }
if(isset($_POST['tipo']) && $_POST['tipo'] != ''){ $tipo = $_POST['tipo']; $_SESSION['tipo'] = $tipo; }
if(isset($_POST['tipoDespacho']) && $_POST['tipoDespacho'] != ''){ $tipoDespacho = $_POST['tipoDespacho']; $_SESSION['tipoDespacho'] = $tipoDespacho; }
if($tipoDespacho == ''){ $tipoDespacho = 99; }
if (isset($_POST['mes']) && $_POST['mes'] != "") { $mes = $_POST['mes']; }
if(isset($_POST['semana']) && $_POST['semana'] != ''){ $semana = $_POST['semana']; $_SESSION['semana'] = $semana; }
if(isset($_POST['dias']) && $_POST['dias'] != ''){ $dias = $_POST['dias']; }
if(isset($_POST['bodegaOrigen']) && $_POST['bodegaOrigen'] != ''){ $bodegaOrigen = $_POST['bodegaOrigen']; }
if(isset($_POST['tipoTransporte']) && $_POST['tipoTransporte'] != ''){ $tipoTransporte = $_POST['tipoTransporte']; }
if(isset($_POST['conductor']) && $_POST['conductor'] != ''){ $conductor = $_POST['conductor']; }
if(isset($_POST['placa']) && $_POST['placa'] != ''){ $placa = $_POST['placa']; }
if(isset($_POST['itemsDespacho']) && $_POST['itemsDespacho'] != ''){ $sedes = $_POST['itemsDespacho']; $_SESSION['sedes'] = $sedes; }
if(isset($_POST['itemsDespachoVariaciones']) && $_POST['itemsDespachoVariaciones'] != ''){
	$sv = $_POST['itemsDespachoVariaciones'];
	$sv = trim($sv, ", ");
	$sedesv1 = explode(', ', $sv);
	$sedes_variaciones = [];
	foreach ($sedesv1 as $row => $sedesv1_item) {
		$sedesv2 = explode('-', $sedesv1_item);
		$sedes_variaciones[$sedesv2[0]] = $sedesv2[1];
	}
}
if(isset($_SESSION['usuario']) && $_SESSION['usuario'] != ''){ $usuario = $_SESSION['usuario']; }
if(isset($_SESSION['login']) && $_SESSION['login'] != ''){ $login = $_SESSION['login']; }
if(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != ''){ $idUsuario = $_SESSION['id_usuario']; }
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

// contar el numero de dias para posteriores operaciones 
$cantidadDias = count($dias); 

// variable importante para saber la cantidad de grupos etarios con la que esta trabajando la plataforma
$cantGruposEtarios = $_SESSION['cant_gruposEtarios']; 

// Se van a buscar el mes y el año a partir de la tabla de planilla semana y se va a verificar la existencia de las tablas.
$consulta = " select ano, mes, semana from planilla_semanas where MES = '$mes' limit 1 ";
$resultado = $Link->query($consulta) or die ('Error al cosultar planillas_semanas: '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$semanaMes = $row['mes'];
		$semanaAnno = $row['ano'];
	}
}
$semanaAnno = substr($semanaAnno, -2);
$annoMes = $semanaMes.$semanaAnno;

// Se va ha buscar que existan cada una de las tablas, de lo contrario se crearan. Productos Mov
$consulta = " show tables like 'productosmov$annoMes' ";
$result = $Link->query($consulta) or die ('Error al consultar existencia de tablas productosmov: '. mysqli_error($Link));
$existe = $result->num_rows;
if($existe <= 0){
	$consulta = " CREATE TABLE `productosmov$annoMes` ( 
						`Documento` varchar(10) DEFAULT '', 
						`Numero` int(10) unsigned DEFAULT '0', 
						`Tipo` varchar(100) DEFAULT '', 
						`FechaDoc` varchar(45) DEFAULT '', 
						`BodegaOrigen` bigint(20) unsigned DEFAULT '0', 
						`BodegaDestino` bigint(20) unsigned DEFAULT '0', 
						`Nombre` varchar(200) DEFAULT '', 
						`Nitcc` varchar(20) DEFAULT '', 
						`Concepto` text, 
						`ValorTotal` decimal(20,2) DEFAULT '0.00', 
						`Aprobado` tinyint(1) DEFAULT '0', 
						`NombreResponsable` varchar(60) DEFAULT '', 
						`LoginResponsable` varchar(30) DEFAULT '', 
						`GeneraCompra` tinyint(1) DEFAULT '0', 
						`DocOrigen` varchar(10) DEFAULT '', 
						`NumDocOrigen` int(10) unsigned DEFAULT '0', 
						`NombreRED` varchar(45) DEFAULT '', 
						`Id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
						`FechaMYSQL` datetime DEFAULT NULL, 
						`Anulado` tinyint(1) DEFAULT '0', 
						`TipoTransporte` varchar(50) NOT NULL DEFAULT '', 
						`Placa` varchar(10) NOT NULL DEFAULT '', 
						`ResponsableRecibe` varchar(45) NOT NULL DEFAULT '', 
						`NumCompra` int(10) unsigned DEFAULT '0', 
						`fecha_despacho` date, PRIMARY KEY (`Id`) ) ";
	$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
}

// Productos Mov Det
$consulta = " show tables like 'productosmovdet$annoMes' ";
$result = $Link->query($consulta) or die ('Error al consultar existencia de tablas productosmovdet: '. mysqli_error($Link));
$existe = $result->num_rows;
if($existe <= 0){
	$consulta = " CREATE TABLE `productosmovdet$annoMes` ( 
						`Documento` varchar(10) DEFAULT '', 
						`Numero` int(10) DEFAULT '0', 
						`Item` int(10) unsigned DEFAULT '0', 
						`CodigoProducto` varchar(20) DEFAULT '', 
						`Descripcion` text NOT NULL, 
						`Cantidad` decimal(28,8) DEFAULT '0.00000000', 
						`CantFacturada` decimal(28,8) DEFAULT '0.00000000', 
						`ValorUnitario` decimal(18,2) DEFAULT '0.00', 
						`CuentaInventario` varchar(30) DEFAULT '', 
						`CuentaContraPartida` varchar(30) DEFAULT '', 
						`Facturado` tinyint(1) DEFAULT '0', 
						`CentroCosto` varchar(10) DEFAULT '', 
						`BodegaOrigen` bigint(20) unsigned DEFAULT '0', 
						`BodegaDestino` bigint(20) unsigned DEFAULT '0', 
						`CantBodOrg` decimal(28,8) DEFAULT '0.00000000', 
						`CantBodDest` decimal(28,8) DEFAULT '0.00000000', 
						`Id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
						`Talla` varchar(5) DEFAULT '', 
						`Color` varchar(45) DEFAULT '', 
						`CostoUnitario` decimal(10,2) DEFAULT '0.00', 
						`NombreRED` varchar(45) DEFAULT '', 
						`Umedida` varchar(255) DEFAULT '', 
						`CantUmedida` decimal(20,4) DEFAULT '0.0000', 
						`Factor` decimal(28,8) DEFAULT '0.00000000', 
						`Id_Usuario` int(10) unsigned DEFAULT '0',
						`CostoTotal` decimal(20,2) DEFAULT '0.00',
						`Lote` varchar(45) NOT NULL DEFAULT '',
						`FechaVencimiento` date DEFAULT NULL,
						`CantU1` decimal(28,8) DEFAULT '0.00000000',
						`CantU2` decimal(28,8) DEFAULT '0.00000000',
						`CantU3` decimal(28,8) DEFAULT '0.00000000',
						`CantU4` decimal(28,8) DEFAULT '0.00000000',
						`CantU5` decimal(28,8) DEFAULT '0.00000000',
						`CanTotalPresentacion` decimal(28,8) DEFAULT '0.00000000',
						`Marca` VARCHAR(100) DEFAULT NULL,
				PRIMARY KEY (`Id`) ) ";
	$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
}

// ***********************************AJUSTES PARA QUE SE PUEDAN CREAR LOS DESPACHOS SEGUN EL NUMERO DE GRUPOS CREADOS ACTUALMENTE *********************************************

$n = 1;
$concatGruposEtarios = '';
while ($cantGruposEtarios > 0) {
	$concatGruposEtarios .= " `Cobertura_G".$n. "` int(10) unsigned DEFAULT '0', ";	
	$cantGruposEtarios--;
	$n++;
}

// Consulta que valida si existe la tabla Despachos_encMESAÑO NO existe para crearla.
$consulta = "SHOW TABLES LIKE 'despachos_enc$annoMes'";
$result = $Link->query($consulta) or die ('Error al consultar las tablas despachos_enc: '. mysqli_error($Link));
$existe = $result->num_rows;
if($existe == 0) {
	// Consulta para crear la tabla despachos_encMESAÑO.
	$consulta = "CREATE TABLE `despachos_enc$annoMes` (
								`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
								`Tipo_Doc` varchar(10) NOT NULL DEFAULT '',
								`Num_Doc` int(10) unsigned NOT NULL,
								`Tipo` varchar(100) NOT NULL DEFAULT '',
								`Nombre` varchar(200) NOT NULL DEFAULT '',
								`Nit` varchar(20) NOT NULL DEFAULT '',
								`Concepto` varchar(500) NOT NULL,
								`FechaHora_Elab` datetime NOT NULL,
								`Id_Usuario` int(10) unsigned NOT NULL,
								`cod_Sede` bigint(20) unsigned NOT NULL,
								`Tipo_Complem` varchar(10) NOT NULL,
								`Semana` varchar(20) NOT NULL DEFAULT '0',
								`Dias` varchar(100) NOT NULL DEFAULT '',
								`Menus` varchar(100) NOT NULL DEFAULT '',
								`Cobertura` int(10) unsigned DEFAULT '0',
								`Estado` smallint(1) unsigned zerofill NOT NULL DEFAULT '1' COMMENT '1= DESPACHADO  0=ELIMINADO   2=PENDIENTE',
								`TipoDespacho` smallint(1) unsigned NOT NULL DEFAULT '5',
								$concatGruposEtarios
								PRIMARY KEY (`ID`)
							)";
	$result = $Link->query($consulta) or die ('Unable to execute query: Crear tabla despachos_enc '. mysqli_error($Link)); 
}

// Despachos det
$consulta = " show tables like 'despachos_det$annoMes' ";
$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
$existe = $result->num_rows;
if($existe <= 0){
	$consulta = " CREATE TABLE `despachos_det$annoMes` ( 
								`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
								`Tipo_Doc` varchar(10) NOT NULL DEFAULT '', 
								`Num_Doc` int(10) unsigned NOT NULL, 
								`cod_Alimento` varchar(20) NOT NULL, 
								`Id_GrupoEtario` int(10) unsigned NOT NULL, 
								`Cantidad` decimal(28,8) NOT NULL DEFAULT '0.00000000', 
								`D1` decimal(28,8) DEFAULT '0.00000000', 
								`D2` decimal(28,8) DEFAULT '0.00000000', 
								`D3` decimal(28,8) DEFAULT '0.00000000',
								`D4` decimal(28,8) DEFAULT '0.00000000',
								`D5` decimal(28,8) DEFAULT '0.00000000',
								`D6` decimal(28,8) DEFAULT '0.00000000',
								`D7` decimal(28,8) DEFAULT '0.00000000',
								`D8` decimal(28,8) DEFAULT '0.00000000',
								`D9` decimal(28,8) DEFAULT '0.00000000',
								`D10` decimal(28,8) DEFAULT '0.00000000',
								`D11` decimal(28,8) DEFAULT '0.00000000',
								`D12` decimal(28,8) DEFAULT '0.00000000',
								`D13` decimal(28,8) DEFAULT '0.00000000',
								`D14` decimal(28,8) DEFAULT '0.00000000',
								`D15` decimal(28,8) DEFAULT '0.00000000',
								`D16` decimal(28,8) DEFAULT '0.00000000',
								`D17` decimal(28,8) DEFAULT '0.00000000',
								`D18` decimal(28,8) DEFAULT '0.00000000',
								`D19` decimal(28,8) DEFAULT '0.00000000',
								`D20` decimal(28,8) DEFAULT '0.00000000',
								`D21` decimal(28,8) DEFAULT '0.00000000',
								`D22` decimal(28,8) DEFAULT '0.00000000',
								`D23` decimal(28,8) DEFAULT '0.00000000',
								`D24` decimal(28,8) DEFAULT '0.00000000',
								`D25` decimal(28,8) DEFAULT '0.00000000',
								PRIMARY KEY (`id`) ) ";
	$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
}

if ($tipo == "APS") {
	$consultaVariaciones = " SELECT distinct(cod_variacion_menu) as cod_variacion_menu FROM sedes$tablaAnno";
	$respuestaVariaciones = $Link->query($consultaVariaciones) or die ('Error al consultar las variaciones ' . mysqli_error($Link));
	if ($respuestaVariaciones->num_rows > 0) {
		while ($dataVariaciones = $respuestaVariaciones->fetch_assoc()) {
			$variaciones[] = $dataVariaciones['cod_variacion_menu'] == 0 ? 3 : $dataVariaciones['cod_variacion_menu'] ;
		}
	}
}else if ($tipo == 'CAJMPS') {
	$consultaVariaciones = " SELECT distinct(cod_variacion_menu_cajmps) as cod_variacion_menu FROM sedes$tablaAnno";
	$respuestaVariaciones = $Link->query($consultaVariaciones) or die ('Error al consultar las variaciones ' . mysqli_error($Link));
	if ($respuestaVariaciones->num_rows > 0) {
		while ($dataVariaciones = $respuestaVariaciones->fetch_assoc()) {
			$variaciones[] = $dataVariaciones['cod_variacion_menu'] == 0 ? 3 : $dataVariaciones['cod_variacion_menu'] ;
		}
	}
}else if ($tipo == 'CAJMRI') {
	$consultaVariaciones = " SELECT distinct(cod_variacion_menu_cajmri) as cod_variacion_menu FROM sedes$tablaAnno";
	$respuestaVariaciones = $Link->query($consultaVariaciones) or die ('Error al consultar las variaciones ' . mysqli_error($Link));
	if ($respuestaVariaciones->num_rows > 0) {
		while ($dataVariaciones = $respuestaVariaciones->fetch_assoc()) {
			$variaciones[] = $dataVariaciones['cod_variacion_menu'] == 0 ? 3 : $dataVariaciones['cod_variacion_menu'] ;
		}
	}
}else {
	$variaciones = array(3); 
}

$sedes_POST = $sedes;
$despachado = false;
foreach ($variaciones as $id => $variacion) {
	$items = [];
	$menus = [];
	$semanaString = '';	
	$semanas = []; 
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

	$parametroSemana = '';
	if ($semana !== '') {
		$parametroSemana = " AND ps.SEMANA = '$semana' ";
	}

	// 1. Armar array con los componentes  Primera consulta, la que trae los diferentes alimentos de los menu.
	$consulta = "   SELECT 	ps.DIA,
							ps.MENU,
						 	CONCAT(ps.NOMDIAS, ps.CICLO) AS NOMDIAS,
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
					INNER JOIN fichatecnicadet ftd ON ftd.IdFT = ft.Id 
	 				WHERE ps.MES = '$mes' $parametroSemana
					AND ft.Nombre IS NOT NULL
					AND p.Cod_Tipo_complemento = '$tipo' 
					AND p.cod_variacion_menu = '$variacion'"; 
	$diasDespacho = '';
	for ($i=0; $i < count($dias) ; $i++) {
		if($i == 0){ $consulta = $consulta." AND ( "; }
		else{ $consulta = $consulta." OR "; $diasDespacho = $diasDespacho.','; }
		$diasDespacho = $diasDespacho.$dias[$i];
		$consulta = $consulta." ps.DIA = ".$dias[$i]." ";
	} 
	if(count($dias) > 0){ $consulta = $consulta." ) "; }
	$consulta = $consulta." order by ftd.codigo asc, ft.Codigo ASC "; 
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$aux = 0;
		while($row = $resultado->fetch_assoc()) {
			$items[] = $row;
			$menus[] = $row['codigo_menu'];
		}
	} 
	$resultado->close(); 
	

	/***************************************************************************************************************************************************/
	// Consulta que retorna los menus de los días seleccionados.
	$condicionDias = $menus = "";
	for ($i=0; $i < count($dias) ; $i++) {
		$condicionDias .= "dia = '". $dias[$i] ."' OR ";
	}
	$consultaMenusDias = "SELECT * FROM planilla_semanas ps WHERE ps.MES = '$mes' $parametroSemana AND (". trim($condicionDias, " OR ") .");";
	$resultadoMenusDias = $Link->query($consultaMenusDias) or die("Error al consultar planilla_semanas. Linea 248: ". $Link->error);
	if ($resultadoMenusDias->num_rows > 0) {
		while ($resgistroMenusDias = $resultadoMenusDias->fetch_assoc()) {
			$menus .= $resgistroMenusDias["MENU"] .", ";
		}
	}
	$menusReg = $menus;

	/**********************************************************************************************************************************************/
	// Debemos buscar el codigo del alimento sin preparar para obtener el codigo que es y las unidades que le afectan
	
	$itemsIngredientes = array(); 
	for ($i=0; $i < count($items); $i++) {
		$item = $items[$i];
		$codigo = $item['codigo'];
		$variacion_menu = $item['variacion_menu'];
			
		// segunda consulta
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
		} else {
			if ($tipoDespacho == 99) {
				// Si no se encontraron ingredientes vamos a mostrar un mensaje.
				echo "No se encontraron alimentos sin preparar para el codigo: $codigo";
				$bandera++;
				break;
			}
		}
	}
	$items = $itemsIngredientes; 

	// Se armara un array con las coverturas de las sedes para cada uno de los grupos etarios  y al final se creara un array con los totales de las sedes.
	$n = 1;
	$concatGruposEtarios = '';
	$cantGruposEtarios = $_SESSION['cant_gruposEtarios']; 
	while ($cantGruposEtarios > 0) {
		$total[$n] = 0;
		$concatGruposEtarios .= "Etario".$n."_".$tipo.", ";
		$cantGruposEtarios--;
		$n++;
	}

	$parametroSemana2 = '';
	if ($semana !== '') {
		$parametroSemana2 = " AND semana = '$semana' ";
	}

	$concatGruposEtarios = trim($concatGruposEtarios, ", "); 
	$totalTotal = 0; 
	$sedesCobertura = array();
	$sedes_variacion = [];
	for ($i=0; $i < count($sedes) ; $i++) {
		$auxSede = $sedes[$i];
		$consulta = " SELECT distinct cod_sede, $concatGruposEtarios from sedes_cobertura where mes = '$mes' $parametroSemana2 and cod_sede = $auxSede and Ano = $annoActual ORDER BY SEMANA DESC LIMIT 1 "; 
		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
		if($resultado->num_rows >= 1){
			while($row = $resultado->fetch_assoc()) { 
				$sedeCobertura['cod_sede'] = $row['cod_sede'];
				$cantGruposEtarios = $_SESSION['cant_gruposEtarios']; 
				$sedeCobertura['total'] = 0;
				for ($j=1; $j <= $cantGruposEtarios ; $j++) { 
					$aux = "Etario".$j.'_'.$tipo;
					$sedeCobertura['grupo'.$j] = $row[$aux];
					$sedeCobertura['total'] = $sedeCobertura['total'] + $row[$aux]; 
				}
				$sedesCobertura[] = $sedeCobertura;
			}
		}
	}


	// Se va a revisar que no existan despachos despachados (1) o pendiente (2) para el mismo complemento, en la misma semana dias sede
	for ($i=0; $i < count($sedesCobertura) ; $i++) {
		$auxSede = $sedesCobertura[$i];
		$auxSede = $auxSede['cod_sede'];

		if ($semana == '') {
			$consultaSemanasMes = " SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE MES = $mes ";
			$respuestaSemanasMes = $Link->query($consultaSemanasMes) or die ('Error al consultar las semanas del mes ');
			if ($respuestaSemanasMes->num_rows > 0) {
				while ($dataSemanasMes = $respuestaSemanasMes->fetch_assoc()) {
				 	$semanas[] = $dataSemanasMes['semana'];
				} 
			} 
		}

		$consulta = " 	SELECT 	de.*,
								s.nom_sede, 
								td.Descripcion as tipoDespachoNm
			 			FROM despachos_enc$annoMes de
			 			LEFT JOIN sedes$tablaAnno s on s.cod_sede = de.cod_Sede
			 			LEFT JOIN tipo_despacho td on de.TipoDespacho = td.Id
			 			WHERE  de.cod_sede = '$auxSede' AND de.Tipo_Complem = '$tipo' AND (de.Estado = 1 OR de.Estado = 2) ";
		$consulta = $consulta." and (TipoDespacho = $tipoDespacho) ";

		// en caso que solo se envie un mes vamos a buscar en el campo det semanas que no exista un despacho con esa semana ya creada
		if ($semana == '') {
			$consulta = $consulta." and ( ";
			for ($j=0; $j < count($semanas) ; $j++) {
				if($j > 0){
					$consulta = $consulta." or ";
				}
				$aux = $semanas[$j];
				$consulta = $consulta." FIND_IN_SET('$aux', de.Semana) ";
			}
			$consulta = $consulta." ) "; 
		}
		if ($semana != ''){
			//Vamos a recorrer el vector de los dias para agregarlos a la consulta e identificar los despachos de un mismo día de la semana.
			$consulta = $consulta." and ( ";
			for ($j=0; $j < count($dias) ; $j++) {
				if($j > 0){
					$consulta = $consulta." or ";
				}
				$aux = $dias[$j];
				$consulta = $consulta." FIND_IN_SET('$aux', Dias) ";
			}
			$consulta = $consulta." ) "; 
			$consulta .= " AND de.semana = '$semana' "; 
		}

		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
		if($resultado->num_rows >= 1){
			$bandera++;
			$row = $resultado->fetch_assoc();
			$nomSede = $row['nom_sede'];
			$diasEncontrados = $row['Dias'];
			$semanasNom = $row['Semana'];
			$tipoDespachoNm = $row['tipoDespachoNm'];
			echo "Se ha encontrado un despacho para la sede: $nomSede en la semana: $semanasNom de tipo de ración: $tipo de tipo de despacho: $tipoDespachoNm para los días: $diasEncontrados";
			break;
		}
	}

	// Controlamos la existencia de items para hacer el despacho dado que si no encontro ningun alimento no deberia hacer el despacho
	$catidadItems = 0;
	if(isset($items)){
		$cantidadItems = count($items);
	}

	// ***************************************************************************************************************************************************//
	// Si la bandera es igual a cero entonces no existen registros para el esa sede en esa semana y podemos proceder a hacer la inserción.
	if($bandera == 0 && $cantidadItems > 0){
		$complementoNuevo = [];
		$complemento = [];
		$complementosCantidades = [];
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
			"total"   => 0,
		);

		for ($j=1; $j <= $cantGruposEtarios ; $j++) { 
			$cantidadGrupoArray = "cantidadGrupo".$j;
			$complementoCantidades[$cantidadGrupoArray] = 0;
		}
		for ($j=1; $j <= $cantGruposEtarios ; $j++) { 
			$grupoArray = "grupo".$j;
			$complementoCantidades[$grupoArray] = 0;
		}			
		for ($j=1; $j <= $cantGruposEtarios ; $j++) {
			for ($i=1; $i <= 25 ; $i++) { 
				$grupodiasArray = "grupo".$j."_d".$i;
				$complementoCantidades[$grupodiasArray] = 0;
			} 
		} 
		for ($i=1; $i <= $cantGruposEtarios ; $i++) { 	 // en este ciclo entra solo el primer item 
			if($item["Cod_Grupo_Etario"] == $i){ 
				$cantidadGrupoIndex = "cantidadGrupo".$i;
				$grupoIndex = "grupo".$i;
				$complementoCantidades[$cantidadGrupoIndex]++;
				$complementoCantidades[$grupoIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
				$complementoCantidades["total"] = $complementoCantidades[$grupoIndex];
				$auxDias = $item["NOMDIAS"]; 
				switch ($auxDias) {
					// primer ciclo
					case ("lunes1"):
						$grupoDiaIndex = "grupo".$i."_d".'1';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("martes1"):
						$grupoDiaIndex = "grupo".$i."_d".'2';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("miércoles1"):
						$grupoDiaIndex = "grupo".$i."_d".'3';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("jueves1"):
						$grupoDiaIndex = "grupo".$i."_d".'4';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("viernes1"):
						$grupoDiaIndex = "grupo".$i."_d".'5';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					// segundo ciclo
					case ("lunes2"):
						$grupoDiaIndex = "grupo".$i."_d".'6';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("martes2"):
						$grupoDiaIndex = "grupo".$i."_d".'7';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("miércoles2"):
						$grupoDiaIndex = "grupo".$i."_d".'8';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("jueves2"):
						$grupoDiaIndex = "grupo".$i."_d".'9';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("viernes2"):
						$grupoDiaIndex = "grupo".$i."_d".'10';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					// tercer ciclo
					case ("lunes3"):
						$grupoDiaIndex = "grupo".$i."_d".'11';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("martes3"):
						$grupoDiaIndex = "grupo".$i."_d".'12';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("miércoles3"):
						$grupoDiaIndex = "grupo".$i."_d".'13';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("jueves3"):
						$grupoDiaIndex = "grupo".$i."_d".'14';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("viernes3"):
						$grupoDiaIndex = "grupo".$i."_d".'15';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					// cuarto ciclo
					case ("lunes4"):
						$grupoDiaIndex = "grupo".$i."_d".'16';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("martes4"):
						$grupoDiaIndex = "grupo".$i."_d".'17';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("miércoles4"):
						$grupoDiaIndex = "grupo".$i."_d".'18';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("jueves4"):
						$grupoDiaIndex = "grupo".$i."_d".'19';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("viernes4"):
						$grupoDiaIndex = "grupo".$i."_d".'20';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					// quinto ciclo
					case ("lunes5"):
						$grupoDiaIndex = "grupo".$i."_d".'21';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("martes5"):
						$grupoDiaIndex = "grupo".$i."_d".'22';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("miércoles5"):
						$grupoDiaIndex = "grupo".$i."_d".'23';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("jueves5"):
						$grupoDiaIndex = "grupo".$i."_d".'24';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("viernes5"):
						$grupoDiaIndex = "grupo".$i."_d".'25';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
				}
			}
		}

		// Agregando el primer complemento al Array de complementosCantidades, a
		// continuación se agregaran los demas elementos buscando cuales se repiten
		// para poder determinar las cantidades de cada complemento.
		$complementosCantidades[] = $complementoCantidades; 
		for ($i=1; $i <  count($items); $i++) {
			$item = $items[$i]; 
			$encontrado = 0;
			for ($j=0; $j < count($complementosCantidades) ; $j++) { 
				$complemento = $complementosCantidades[$j]; 
				if($item["codigo"] == $complemento['codigo']){ 
					$encontrado++; 
					for ($n=1; $n <= $cantGruposEtarios; $n++) { 	
						if($item["Cod_Grupo_Etario"] == $n){
							$cantidadGrupoIndex = "cantidadGrupo".$n;
							$grupoIndex = "grupo".$n;
							$complemento[$cantidadGrupoIndex]++;
							if (isset($complemento[$grupoIndex])) {
								$complemento[$grupoIndex] = $complemento[$grupoIndex] + ($item['cantidad'] * $item['cantidadPresentacion']);
								$complemento["total"] = $complemento["total"] + $complemento[$grupoIndex];
							}
							$auxDias = $item["NOMDIAS"];
							switch ($auxDias) { 
								// primer ciclo
								case ("lunes1"):
									$grupoDiaIndex = "grupo".$n."_d".'1';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("martes1"):
									$grupoDiaIndex = "grupo".$n."_d".'2';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("miércoles1"):
									$grupoDiaIndex = "grupo".$n."_d".'3';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("jueves1"):
									$grupoDiaIndex = "grupo".$n."_d".'4';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("viernes1"):
									$grupoDiaIndex = "grupo".$n."_d".'5';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								// segundo ciclo
								case ("lunes2"):
									$grupoDiaIndex = "grupo".$n."_d".'6';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("martes2"):
									$grupoDiaIndex = "grupo".$n."_d".'7';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("miércoles2"):
									$grupoDiaIndex = "grupo".$n."_d".'8';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("jueves2"):
									$grupoDiaIndex = "grupo".$n."_d".'9';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("viernes2"):
									$grupoDiaIndex = "grupo".$n."_d".'10';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								// tercer ciclo
								case ("lunes3"):
									$grupoDiaIndex = "grupo".$n."_d".'11';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("martes3"):
									$grupoDiaIndex = "grupo".$n."_d".'12';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("miércoles3"):
									$grupoDiaIndex = "grupo".$n."_d".'13';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("jueves3"):
									$grupoDiaIndex = "grupo".$n."_d".'14';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("viernes3"):
									$grupoDiaIndex = "grupo".$n."_d".'15';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								// cuarto ciclo
								case ("lunes4"):
									$grupoDiaIndex = "grupo".$n."_d".'16';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("martes4"):
									$grupoDiaIndex = "grupo".$n."_d".'17';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("miércoles4"):
									$grupoDiaIndex = "grupo".$n."_d".'18';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("jueves4"):
									$grupoDiaIndex = "grupo".$n."_d".'19';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("viernes4"):
									$grupoDiaIndex = "grupo".$n."_d".'20';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								// quinto ciclo
								case ("lunes5"):
									$grupoDiaIndex = "grupo".$n."_d".'21';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("martes5"):
									$grupoDiaIndex = "grupo".$n."_d".'22';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("miércoles5"):
									$grupoDiaIndex = "grupo".$n."_d".'23';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("jueves5"):
									$grupoDiaIndex = "grupo".$n."_d".'24';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("viernes5"):
									$grupoDiaIndex = "grupo".$n."_d".'25';
									$complemento[$grupoDiaIndex] += $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							}
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

				for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
					$cantidadGrupoArray = "cantidadGrupo".$m;
					$complementoNuevo[$cantidadGrupoArray] = 0;
				}

				for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
					$grupoArrayIndex = "grupo".$m;
					$complementoNuevo[$grupoArrayIndex] = 0;
				}

				for ($m=1; $m <= $cantGruposEtarios; $m++) {
					for ($n=1; $n <= 25 ; $n++) { 
				 		$grupodiasArray = "grupo".$m."_d".$n;
				 		$complementoNuevo[$grupodiasArray] = 0;
				 	} 
				}

				for ($m=1; $m <= $cantGruposEtarios; $m++) { 
					if($item["Cod_Grupo_Etario"] == $m){
						$cantidadGrupoIndex = "cantidadGrupo".$m;
						$grupoIndex = "grupo".$m;
						$complementoNuevo[$cantidadGrupoIndex]=1;
						if (isset($complementoNuevo[$grupoIndex])) {
							$complementoNuevo[$grupoIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							$complementoNuevo["total"] = $complementoNuevo[$grupoIndex];
						}
						$auxDias = $item["NOMDIAS"];
						switch ($auxDias) {
							// primer ciclo
							case ("lunes1"):
								$grupoDiaIndex = "grupo".$m."_d".'1';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("martes1"):
								$grupoDiaIndex = "grupo".$m."_d".'2';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("miércoles1"):
								$grupoDiaIndex = "grupo".$m."_d".'3';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("jueves1"):
								$grupoDiaIndex = "grupo".$m."_d".'4';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("viernes1"):
								$grupoDiaIndex = "grupo".$m."_d".'5';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							// segundo ciclo
							case ("lunes2"):
								$grupoDiaIndex = "grupo".$m."_d".'6';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("martes2"):
								$grupoDiaIndex = "grupo".$m."_d".'7';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("miércoles2"):
								$grupoDiaIndex = "grupo".$m."_d".'8';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("jueves2"):
								$grupoDiaIndex = "grupo".$m."_d".'9';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("viernes2"):
								$grupoDiaIndex = "grupo".$m."_d".'10';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							// tercer ciclo
							case ("lunes3"):
								$grupoDiaIndex = "grupo".$m."_d".'11';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("martes3"):
								$grupoDiaIndex = "grupo".$m."_d".'12';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("miércoles3"):
								$grupoDiaIndex = "grupo".$m."_d".'13';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("jueves3"):
								$grupoDiaIndex = "grupo".$m."_d".'14';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("viernes3"):
								$grupoDiaIndex = "grupo".$m."_d".'15';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							// cuarto ciclo
							case ("lunes4"):
								$grupoDiaIndex = "grupo".$m."_d".'16';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("martes4"):
								$grupoDiaIndex = "grupo".$m."_d".'17';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("miércoles4"):
								$grupoDiaIndex = "grupo".$m."_d".'18';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("jueves4"):
								$grupoDiaIndex = "grupo".$m."_d".'19';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("viernes4"):
								$grupoDiaIndex = "grupo".$m."_d".'20';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							// quinto ciclo
							case ("lunes5"):
								$grupoDiaIndex = "grupo".$m."_d".'21';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("martes5"):
								$grupoDiaIndex = "grupo".$m."_d".'22';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("miércoles5"):
								$grupoDiaIndex = "grupo".$m."_d".'23';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("jueves5"):
								$grupoDiaIndex = "grupo".$m."_d".'24';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("viernes5"):
								$grupoDiaIndex = "grupo".$m."_d".'25';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
						}
					}
				} 
				$complementosCantidades[] = $complementoNuevo; 
			} // encontrado  
		}// Termina el for externo el de los items

// ***************************************************************************************************************************************************//

		/* Insertando movimiento */
		/* Leyendo el consecutivo */
		$consecutivo = '';
		$consulta = " select Consecutivo from documentos where Tipo = 'DES' ";
		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
		if($resultado->num_rows >= 1){
			$row = $resultado->fetch_assoc();
			$consecutivo = $row['Consecutivo'];
		}//Termina el if que valida que si existan resultados
		$resultado->close();

		/* Se va a armar un array con los consecutivos de los movimientos, luego se actualizara el consecutivo */
		$consecutivos = array();
		$aux = (int)$consecutivo;
		for ($i=0; $i < count($sedesCobertura); $i++){
			$consecutivos[$i] = $aux;
			$aux = $aux + 1;
		}

		/* Actualizando el valor del consecutivo en la base de datos para futuros documentos */
		$consulta = " update documentos set consecutivo = $aux where Tipo = 'DES' ";
		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

		/* Insertando en productosmov$tablaAnno */
		$annoActual = $_SESSION['periodoActual'];
		date_default_timezone_set('America/Bogota');
		$fecha = date("Y-m-d H:i:s");

// ************************************* INSERCIONES TABLAS PRODUCTOS MOV   **************************************************************************//
		
		for ($i=0; $i < count($sedes); $i++){
			$bodegaDestino = $sedes[$i];
			$consecutivo = $consecutivos[$i];
			$consulta = " insert into productosmov$annoMes (Nombre, Nitcc, Tipo, Documento, Numero, BodegaOrigen, BodegaDestino, Aprobado, NombreResponsable, LoginResponsable, FechaMYSQL, TipoTransporte, Placa, ResponsableRecibe) values ('$nombre','$documento','$tipoDocumento',   'DES', $consecutivo, $bodegaOrigen, $bodegaDestino, 1, '$usuario', '$login', '$fecha', $tipoTransporte, '$placa', '$conductor') ";
				$resultado = $Link->query($consulta) or die ('Unable to execute query. - Inserción productosmov '. mysqli_error($Link)." ".$consulta);
		}

		/* Insertando en productosmovdet$annoMes */
		$consulta = " insert into productosmovdet$annoMes (Documento, Numero, Item, CodigoProducto, Descripcion, Cantidad, BodegaOrigen,BodegaDestino ,Umedida, CantUmedida, Factor, cantu2, cantu3, cantu4, cantu5, cantotalpresentacion ) values ";
		$banderaPrimero = 0;
		for ($i=0; $i < count($sedesCobertura) ; $i++) {
			$auxItem = 1;
			for ($j=0; $j < count($complementosCantidades) ; $j++){
				$cantidad = 0;
				$auxConsulta = '';
				$auxConsulta = $auxConsulta." ( ";
				$auxAlimento = $complementosCantidades[$j]; 
				$sede = $sedesCobertura[$i]; 
				if ($auxAlimento['variacion_menu'] != $sedes_variaciones[$sede['cod_sede']]) {
					continue;
				}
				$codigo = $auxAlimento['codigo'];
				$bodegaDestino = $sede['cod_sede'];
				$componente = $auxAlimento['Componente'];

				for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
					$grupoIndex = "grupo".$m;
					if (isset($auxAlimento[$grupoIndex])) {
						$t[$m] = $auxAlimento[$grupoIndex] * $sede[$grupoIndex];
					}
				}

				// Inicio ajuste contramuestra
				if($auxAlimento["grupo_alim"] == "Contramuestra"){
					for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
						$grupoIndex = "grupo".$m;
						$t[$m] = 0;
						$t1 = $t1 + $auxAlimento[$grupoIndex];
					}
				}
					
				// Termina ajuste contramuestra
				for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
					$cantidad = $cantidad + $t[$m];
				}

				$unidad = $auxAlimento['unidadMedida'];
				$factor = $auxAlimento['factor'];
				$presentacion = $auxAlimento['presentacion'];
				$consecutivo = $consecutivos[$i];

				// se hacen movimientos tambien en este archivo para generar las remisiones con cualquier cantidad de grupos etarios
				include 'fn_despacho_generar_presentaciones.php';

				$auxConsulta = $auxConsulta." 'DES', $consecutivo, $auxItem, '$codigo', '$componente', $cantidad, $bodegaOrigen, $bodegaDestino, '$presentacion', $cantidad, $factor, $necesario2, $necesario3, $necesario4, $necesario5, $tomadoTotal ";
				$auxItem++;
				$auxConsulta = $auxConsulta." ) ";

				if($cantidad > 0){
					if($banderaPrimero == 0){
						$banderaPrimero++;
					}else{
						$consulta = $consulta." , ";
					}
					$consulta = $consulta.$auxConsulta; 
				} 
			}
		}
		$resultado = $Link->query($consulta) or die ('Unable to execute query - Inserción en productosmovdet '. mysqli_error($Link));

// ***************************************************** FIN INSERCIONES TABLAS PRODUCTOS *********************************************************//

// ************************************************ INSERCIONES TABLAS DESPACHOS ******************************************************************//

		/************************* Modificación que agregar 3 campos mas para la tabla despachos_encMESAÑO *************************/
		// Consulta que inserta los registros en la tabla despachos_encMESAÑO.
		$concatCobertura_G = '';
		$cantGruposEtarios = $_SESSION['cant_gruposEtarios']; 
		for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
			$concatCobertura_G .= "Cobertura_G".$i.", ";
		}

		if ($semana == '') {
			$diasIn = '';
			$semanaString = '';
			$arrayDiasSemanas = explode(",",$diasDespacho);
			foreach ($arrayDiasSemanas as $key => $value) {
				$diasIn .= "'" .$value. "', ";	
			}
			$diasIn = trim($diasIn,", "); 
			$consultaSemanaString = " SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE mes = $mes AND DIA IN ($diasIn)"; 
			$respuestaSemanasString = $Link->query($consultaSemanaString) or die ('Error al consultar las semanas relacionadas' . mysqli_error($Link));
			if ($respuestaSemanasString->num_rows > 0) {
				while($dataSemanasString = $respuestaSemanasString->fetch_assoc()){
					$semanaString .= $dataSemanasString['semana']. ", ";
				}
				$semanaString = trim($semanaString, ", "); 
			} 

		}else if($semana !== ""){
			$semanaString = $semana;
		}

		$concatCobertura_G = trim($concatCobertura_G, ", ");
		$consulta = " INSERT INTO despachos_enc$annoMes (Tipo_Doc, Num_Doc, FechaHora_Elab, Id_usuario, cod_Sede, Tipo_Complem, Semana, Cobertura, estado, concepto, Dias,Menus, TipoDespacho, $concatCobertura_G ) VALUES ";

		for ($i=0; $i < count($sedesCobertura); $i++) {
			if($i > 0){$consulta = $consulta." , "; }
			$consulta = $consulta." ( ";
			$sede = $sedesCobertura[$i];
			$consecutivo = $consecutivos[$i];
					
			for ($j=1; $j <= $cantGruposEtarios ; $j++) { 
				$grupoIndex = "grupo".$j;
				if (isset($sede[$grupoIndex])) {
					$grupo[$j] = $sede[$grupoIndex];
					$auxGrupo[$j] = 0;
				}	
			}

			for ($j=0; $j < count($complementosCantidades) ; $j++) {
				$complemento = $complementosCantidades[$j]; 
				for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
					$grupoIndex = "grupo".$m;
					if (isset($complemento[$grupoIndex])) {
						if($complemento[$grupoIndex]>0){$auxGrupo[$m] ++; }
					}
				}
			}

			for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
				$indexGrupo = "grupo".$m;
				if ($auxGrupo[$m] == 0) {
					$sede[$indexGrupo]=0;
					$sede['total'] = $sede['total'] + $sede[$indexGrupo];
				}
			}

			$valoresGrupos = '';
			for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
				$valoresGrupos .= " '".$grupo[$m]."', ";
			}


			$valoresGrupos = trim($valoresGrupos, ", ");
			$cobertura = $sede['total'];
			$sede = $sede['cod_sede'];

			$consulta = $consulta." 'DES',$consecutivo,'$fecha',$idUsuario,$sede,'$tipo','$semanaString', $cobertura,2,'', '$diasDespacho', '$menusReg', '$tipoDespacho', $valoresGrupos ";
			$consulta = $consulta." ) ";
		}
		$resultado = $Link->query($consulta) or die ('Unable to execute query: Insertando datos tabla: despachos_enc$annoMes '. mysqli_error($Link));

		$consulta = " insert into despachos_det$annoMes (tipo_doc, num_doc, cod_alimento, id_grupoetario, cantidad, d1, d2, d3, d4, d5, d6, d7, d8, d9, d10, d11, d12, d13, d14, d15, d16, d17, d18, d19, d20, d21, d22, d23, d24, d25) values ";
		$banderaPrimero = 0;
		for ($i=0; $i < count($sedesCobertura) ; $i++) {  // ciclo recorrer las sedes
			$sede = $sedesCobertura[$i];
			for ($j=0; $j < count($complementosCantidades) ; $j++){  // cilo recorre los alimentos

				// Se toman los datos del alimento en una cadena auxiliar para despues
				// validar si la cantidad es mayor a cero y agregar a la cadena que va
				// a hacer la insersión
				$auxAlimento = $complementosCantidades[$j]; 
				if ($auxAlimento['variacion_menu'] != $sedes_variaciones[$sede['cod_sede']]) {
					continue;
				}
				$gruposRegistrar = 0;
				if($auxAlimento["grupo_alim"] == "Contramuestra"){
					$cantidadAlimentos = 0;
					for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
						$grupoIndex = "grupo".$m;
						$gruposArray[] = $sede[$grupoIndex];
						if (isset($auxAlimento[$grupoIndex])) {
							$cantidadAlimentos = $cantidadAlimentos + $auxAlimento[$grupoIndex];
						}					
					}
					$idGrupoEtario = max($gruposArray); 
					for ($n=1; $n <=25 ; $n++) { 
						$d[$n] = 0;
						for ($m=1; $ $m<= $cantGruposEtarios ; $m++) { 
							$grupoDiaIndex = "grupo".$m."_d".$n;
							$d[$n] = $d[$n] + $auxAlimento[$grupoDiaIndex];
						}
					}
					if($banderaPrimero == 0){
						$banderaPrimero++;
					}else{
						$consulta = $consulta." , ";
					}
					$gruposRegistrar++;
					$consulta = $consulta." ( ";
					$codigo = $auxAlimento['codigo'];
					$componente = $auxAlimento['Componente'];
					$cantidad = $cantidadAlimentos;
					$unidad = $auxAlimento['unidadMedida'];
					$consecutivo = $consecutivos[$i];
					$d1 = $d[1];
					$d2 = $d[2];
					$d3 = $d[3];
					$d4 = $d[4];
					$d5 = $d[5];
					$d6 = $d[6];
					$d7 = $d[7];
					$d8 = $d[8];
					$d9 = $d[9];
					$d10 = $d[10];
					$d11 = $d[11];
					$d12 = $d[12];
					$d13 = $d[13];
					$d14 = $d[14];
					$d15 = $d[15];
					$d16 = $d[16];
					$d17 = $d[17];
					$d18 = $d[18];
					$d19 = $d[19];
					$d20 = $d[20];
					$d21 = $d[21];
					$d22 = $d[22];
					$d23 = $d[23];
					$d24 = $d[24];
					$d25 = $d[25];
					$consulta = $consulta." 'DES',$consecutivo, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5, $d6, $d7, $d8, $d9, $d10, $d11, $d12, $d13, $d14, $d15, $d16, $d17, $d18, $d19, $d20, $d21, $d22, $d23, $d24, $d25";
					$consulta = $consulta." ) ";

				}else{ 
					for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
						$indexGrupo = "grupo".$m; 
						$auxAlimentoCodicional = $auxAlimento[$indexGrupo];
						$auxSedeCondicional = $sede[$indexGrupo];
						if ( $auxAlimentoCodicional > 0   &&  $auxSedeCondicional > 0  ) {
							if($banderaPrimero == 0){
								$banderaPrimero++;
							}else{
								$consulta = $consulta." , ";
							}
							$gruposRegistrar++;
							$idGrupoEtario = $m; 
							$consulta = $consulta." ( ";
							$codigo = $auxAlimento['codigo'];
							$componente = $auxAlimento['Componente'];							
							$unidad = $auxAlimento['unidadMedida'];
							$consecutivo = $consecutivos[$i];
							for ($n=1; $n <= 25 ; $n++) { 
								$grupoDiaIndex = "grupo".$m."_d".$n;
								$d[$n] = $auxAlimento[$grupoDiaIndex] * ($sede[$indexGrupo]);
							}
							$d1 = $d[1];
							$d2 = $d[2];
							$d3 = $d[3];
							$d4 = $d[4];
							$d5 = $d[5];
							$d6 = $d[6];
							$d7 = $d[7];
							$d8 = $d[8];
							$d9 = $d[9];
							$d10 = $d[10];
							$d11 = $d[11];
							$d12 = $d[12];
							$d13 = $d[13];
							$d14 = $d[14];
							$d15 = $d[15];
							$d16 = $d[16];
							$d17 = $d[17];
							$d18 = $d[18];
							$d19 = $d[19];
							$d20 = $d[20];
							$d21 = $d[21];
							$d22 = $d[22];
							$d23 = $d[23];
							$d24 = $d[24];
							$d25 = $d[25];
							$cantidad = $d1 + $d2 + $d3 + $d4 + $d5 + $d6 + $d7 + $d8 + $d9 + $d10 + $d11 + $d12 + $d13 + $d14 + $d15 + $d16 + $d17 + $d18 + $d19 + $d20 + $d21 + $d22 + $d23 + $d24 +$d25;
							$consulta = $consulta." 'DES',$consecutivo, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5, $d6, $d7, $d8, $d9, $d10, $d11, $d12, $d13, $d14, $d15, $d16, $d17, $d18, $d19, $d20, $d21, $d22, $d23, $d24, $d25";
							$consulta = $consulta." ) ";
						}
					}
				}
			}// Termina el for de los alimentos
		}// termina el for de las sedes 

		$resultado = $Link->query($consulta) or die ('Inserción despachos_det - Unable to execute query. '. mysqli_error($Link));
		// $Link->close();
		$despachado = true;
	}//Termina el if de bandera igual a 0
} // foreach variaciones

if ($despachado) {
	echo "1";
} else {
	echo "No se encontraron alimentos sin preparar para este despacho.";
}