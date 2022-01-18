<?php
include '../../../config.php';
include  '../../../db/conexion.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

$tablaAnno = $_SESSION['periodoActual'];
$nombre = '';
$documento = '';
$tipoDocumento = '';
$despacho = $_POST['despacho'];
$annoActual = $_SESSION['periodoActualCompleto'];
$tipo = '';
$tipoDespacho = '';
$semana = '';
$dias = '';
$items = array();
$menus = array();
$menusReg = array();
$menusEtarios = array();
$sedes = array();
$bodegaOrigen = '';
$usuario = '';
$login = '';
$tipoTransporte = '';
$conductor = '';
$placa = '';
$idUsuario = '';

if(isset($_POST['proveedorEmpleado']) && $_POST['proveedorEmpleado'] != ''){ $documento = $_POST['proveedorEmpleado']; }
if(isset($_POST['proveedorEmpleadoNm']) && $_POST['proveedorEmpleadoNm'] != ''){ $nombre = $_POST['proveedorEmpleadoNm']; }
if(isset($_POST['subtipoNm']) && $_POST['subtipoNm'] != ''){$tipoDocumento = $_POST['subtipoNm']; }
if(isset($_POST['tipo']) && $_POST['tipo'] != ''){ $tipo = $_POST['tipo']; $_SESSION['tipo'] = $tipo; }
if(isset($_POST['tipoDespacho']) && $_POST['tipoDespacho'] != ''){ $tipoDespacho = $_POST['tipoDespacho']; $_SESSION['tipoDespacho'] = $tipoDespacho; }
if($tipoDespacho == ''){ $tipoDespacho = 99; }
if(isset($_POST['semana']) && $_POST['semana'] != ''){ $semana = $_POST['semana']; $_SESSION['semana'] = $semana; }
if(isset($_POST['dias']) && $_POST['dias'] != ''){ $dias = $_POST['dias']; }
if(isset($_POST['bodegaOrigen']) && $_POST['bodegaOrigen'] != ''){ $bodegaOrigen = $_POST['bodegaOrigen']; }
if(isset($_POST['tipoTransporte']) && $_POST['tipoTransporte'] != ''){ $tipoTransporte = $_POST['tipoTransporte']; }
if(isset($_POST['conductor']) && $_POST['conductor'] != ''){ $conductor = $_POST['conductor']; }
if(isset($_POST['placa']) && $_POST['placa'] != ''){ $placa = $_POST['placa']; }
if(isset($_POST['itemsDespacho']) && $_POST['itemsDespacho'] != ''){ $sedes = $_POST['itemsDespacho']; $_SESSION['sedes'] = $sedes; }
if(isset($_POST['itemsDespachoVariacion']) && $_POST['itemsDespachoVariacion'] != ''){ $sedes_variacion = $_POST['itemsDespachoVariacion'][0]; }
if(isset($_SESSION['usuario']) && $_SESSION['usuario'] != ''){ $usuario = $_SESSION['usuario']; }
if(isset($_SESSION['login']) && $_SESSION['login'] != ''){ $login = $_SESSION['login']; }
if(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != ''){ $idUsuario = $_SESSION['id_usuario']; }
$sedes = $_POST['itemsDespacho'];

$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
	echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

// Se van a buscar el mes y el año a partir de la tabla de planilla semana
$consulta = " select ano, mes, semana from planilla_semanas where semana = '$semana' limit 1 ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)." consulta : ".$consulta);
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$semanaMes = $row['mes'];
		$semanaAnno = $row['ano'];
	}
}
$semanaAnno = substr($semanaAnno, -2);
$annoMes = $semanaMes.$semanaAnno;

//1. Armar array con los componentes Primera columna, la que trae los diferentes alimentos de los menu.
$consulta = "	SELECT  ps.MENU, 
						ps.NOMDIAS, 
						ft.Nombre, 
						p.Cod_Grupo_Etario, 
						ft.Codigo AS codigo_menu, 
						ftd.codigo, 
						ftd.Componente, 
						ftd.Cantidad, 
						ftd.UnidadMedida 
				FROM planilla_semanas ps 
				INNER JOIN productos$tablaAnno p ON ps.menu = p.orden_ciclo 
				INNER JOIN fichatecnica ft ON p.Codigo = ft.Codigo 
				INNER JOIN fichatecnicadet ftd ON ftd.IdFT = ft.Id 
				WHERE ps.SEMANA = '$semana'
				AND ft.Nombre IS NOT NULL
				AND p.Cod_Tipo_complemento = '$tipo' AND p.cod_variacion_menu = '$sedes_variacion'";
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
$consulta = $consulta." order by ftd.codigo asc, ft.Codigo "; 
$resultado = $Link->query($consulta) or die ('Unable to execute query - Line 137 <br> '. mysqli_error($Link)." consulta : ".$consulta);
if($resultado->num_rows >= 1){
	$aux = 0;
	while($row = $resultado->fetch_assoc()) {
		$items[] = $row;
		$menus[] = $row['codigo_menu'];
		$menusReg[] = $row['MENU'];
	}// Termina el while
}//Termina el if que valida que si existan resultados
$resultado->close();

$menusReg = array_unique($menusReg);
sort($menusReg);
$menusReg = implode(",",$menusReg);

// Debemos buscar el codigo del alimento sin preparar para obtener el codigo que es y las unidades que le afectan
$itemsIngredientes = array();
for ($i=0; $i < count($items); $i++) {
	$item = $items[$i];
	$codigo = $item['codigo'];
	$consulta = " 	SELECT 	ft.Codigo AS codigo_preparado,
							ftd.codigo,
							ftd.Componente,
							p.nombreunidad2 presentacion,
							p.cantidadund1 cantidadPresentacion,
							p.cantidadund2 factor,
							p.cantidadund3,
							p.cantidadund4,
							p.cantidadund5,
							m.grupo_alim,
							ftd.Cantidad,
							ftd.UnidadMedida,
							ftd.PesoNeto,
							ftd.PesoBruto
					FROM fichatecnica ft
					INNER JOIN fichatecnicadet ftd ON ft.id=ftd.idft
					INNER JOIN productos$tablaAnno p ON ftd.codigo=p.codigo
					INNER JOIN menu_aportes_calynut m ON ftd.codigo=m.cod_prod
					WHERE ft.codigo = $codigo  AND ftd.tipo = 'Alimento' ";
	if($tipoDespacho != 99){ $consulta = $consulta." and p.tipodespacho = $tipoDespacho "; } 
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)." consulta : ".$consulta);
	if($resultado->num_rows >= 1){
		$ingredientes = 1;
		while($row = $resultado->fetch_assoc()){
			$item['codigoPreparado'] = $codigo;
			$item['codigo'] = $row['codigo'];
			$item['Componente'] = $row['Componente'];
			$item['presentacion'] = $row['presentacion'];
			$item['cantidadPresentacion'] = $row['cantidadPresentacion'];
			$item['factor'] = $row['factor'];
			$item['cantidadund3'] = $row['cantidadund3'];
			$item['cantidadund4'] = $row['cantidadund4'];
			$item['cantidadund5'] = $row['cantidadund5'];
			if($tipo == 'CAJMRI'){$item['cantidad'] = $row['Cantidad']; }
			else{$item['cantidad'] = $row['PesoBruto']; }
			$item['grupo_alim'] = $row['grupo_alim'];
			$item['unidadMedida'] = $row['UnidadMedida'];
			$itemsIngredientes[] = $item;
			$ingredientes++;
		}
	}	
	else{
		if($tipoDespacho == 99){
			// Si no se encontraron ingredientes vamos a mostrar un mensaje.
			echo "No se encontraron alimentos sin preparar para el codigo: $codigo";
			$bandera++;
			break;
		}
	}
}
$items = $itemsIngredientes;

// Se arma un array con las coverturas de las sedes para cada uno de los grupos etarios  y al final se creara un array con los totales de las sedes.
$cantGruposEtarios = $_SESSION['cant_gruposEtarios']; 
$n = 1;
$concatGruposEtarios = '';
while ($cantGruposEtarios > 0) {
	$total[$n] = 0;
	$concatGruposEtarios .= "Etario".$n."_".$tipo.", ";
	$cantGruposEtarios--;
	$n++;
}
$concatGruposEtarios = trim($concatGruposEtarios, ", "); 
$totalTotal = 0;

for ($i=0; $i < count($sedes) ; $i++) {
	$auxSede = $sedes[$i];
	$consulta = " select cod_sede, $concatGruposEtarios from sedes_cobertura where semana = '$semana' and cod_sede = $auxSede and Ano = $annoActual ";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)." consulta : ".$consulta);
	if($resultado->num_rows >= 1){
		while($row = $resultado->fetch_assoc()) {
			$sedeCobertura['cod_sede'] = $row['cod_sede'];
			$sedeCobertura['total'] = 0;
			$cantGruposEtarios = $_SESSION['cant_gruposEtarios']; 
			for ($j=1; $j <= $cantGruposEtarios ; $j++) { 
				$aux = "Etario".$j.'_'.$tipo;
				$sedeCobertura['grupo'.$j] = $row[$aux];
				$sedeCobertura['total'] = $sedeCobertura['total'] + $row[$aux]; 
			}
			$sedesCobertura[] = $sedeCobertura;
			$totalTotal = $totalTotal +  $sedeCobertura['total'];
		}
	}
}

// Se va a revisar que no ewxistan despachos despachados (1) o pendiente (2) para el mismo complemento, semana sede
$bandera = 0;
for ($i=0; $i < count($sedesCobertura) ; $i++) {
	$auxSede = $sedesCobertura[$i];
	$auxSede = $auxSede['cod_sede'];
	$consulta = " 	SELECT de.*,s.nom_sede
					FROM despachos_enc$annoMes de
					INNER JOIN sedes$tablaAnno s on s.cod_sede = de.cod_Sede
					INNER JOIN tipo_despacho td on de.TipoDespacho = td.Id
					WHERE de.Semana = '$semana' AND Dias = '$diasDespacho' AND de.cod_sede = '$auxSede' AND de.Tipo_Complem = '$tipo' AND (de.Estado = 1 OR de.Estado = 2) AND de.Tipo_Doc = 'DES' AND de.Num_Doc != '$despacho' ";
	if($tipoDespacho != 99){
			$consulta = $consulta." and TipoDespacho = $tipoDespacho ";
	 }
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)." consulta : ".$consulta);
	if($resultado->num_rows >= 1){
		$bandera++;
		$row = $resultado->fetch_assoc();
		$nomSede = $row['nom_sede'];
		echo " Ya existe despacho para la sede $nomSede en la semana $semana de tipo $tipo ";
		break;
	}
}

if($bandera == 0){
	// Vamos a crear el Array de complementosCantidades para hacer las  inserciones con las cantidades precisas
	$item = $items[0];
	$complementoCantidades = array(
		"nomDias" 				=> $item["NOMDIAS"],
		"codigoPreparado" 		=> $item["codigoPreparado"],
		"codigo" 				=> $item["codigo"],
		"Componente" 			=> $item["Componente"],
		"presentacion" 			=> $item["presentacion"],
		"cantidadPresentacion" 	=> $item["cantidadPresentacion"],
		"grupo_alim" 			=> $item["grupo_alim"],
		"cantidad" 				=> $item["cantidad"],
		"unidadMedida" 			=> $item["unidadMedida"],
		"factor" 				=> $item["factor"],
		"cantidadund3" 			=> $item['cantidadund3'],
		"cantidadund4" 			=> $item['cantidadund4'],
		"cantidadund5" 			=> $item['cantidadund5'],
		"total"  				=> 0,
	);

	$cantGruposEtarios = $_SESSION['cant_gruposEtarios']; 
	for ($j=1; $j <= $cantGruposEtarios ; $j++) { 
		$cantidadGrupoArray = "cantidadGrupo".$j;
		$complementoCantidades[$cantidadGrupoArray] = 0;
	}
	for ($j=1; $j <= $cantGruposEtarios ; $j++) { 
		$grupoArray = "grupo".$j;
		$complementoCantidades[$grupoArray] = 0;
	}			
	for ($j=1; $j <= $cantGruposEtarios ; $j++) {
		for ($i=1; $i <= 5 ; $i++) { 
			$grupodiasArray = "grupo".$j."_d".$i;
			$complementoCantidades[$grupodiasArray] = 0;
		} 
	}

	for ($i=1; $i <= $cantGruposEtarios  ; $i++) { 
		if($item["Cod_Grupo_Etario"] == $i){
			$cantidadGrupoIndex = "cantidadGrupo".$i;
			$grupoIndex = "grupo".$i;
			$complementoCantidades[$cantidadGrupoIndex]++;
			$complementoCantidades[$grupoIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
			$complementoCantidades["total"] = $complementoCantidades[$grupoIndex];
			for ($j=1; $j <=5 ; $j++) { 
				// $grupoDiaIndex = "grupo".$i."_d".$j;
				$auxDias = $item["NOMDIAS"];
				switch ($auxDias) {
					case ("lunes"):
					 	$grupoDiaIndex = "grupo".$i."_d".'1';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("martes"):
						$grupoDiaIndex = "grupo".$i."_d".'2';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("miércoles"):
						$grupoDiaIndex = "grupo".$i."_d".'3';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("jueves"):
						$grupoDiaIndex = "grupo".$i."_d".'4';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
					case ("viernes"):
						$grupoDiaIndex = "grupo".$i."_d".'5';
						$complementoCantidades[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
					break;
				}
			}
		}
	}

	// Agregando el primer complemento al Array de complementosCantidades, a continuación se agregaran los demas elementos buscando cuales se repiten  para poder determinar las cantidades de cada complemento.
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
						for ($m=1; $m <=5 ; $m++) { 
							// $grupoDiaIndex = "grupo".$n."_d".$m;
							switch ($auxDias) {
								case ("lunes"):
									$grupoDiaIndex = "grupo".$n."_d".'1';
									$complemento[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("martes"):
									$grupoDiaIndex = "grupo".$n."_d".'2';
									$complemento[$grupoDiaIndex] =  $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("miércoles"):
									$grupoDiaIndex = "grupo".$n."_d".'3';
									$complemento[$grupoDiaIndex] =  $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("jueves"):
									$grupoDiaIndex = "grupo".$n."_d".'4';
									$complemento[$grupoDiaIndex] =  $item['cantidad'] * $item['cantidadPresentacion'];
								break;
								case ("viernes"):
									$grupoDiaIndex = "grupo".$n."_d".'5';
									$complemento[$grupoDiaIndex] =  $item['cantidad'] * $item['cantidadPresentacion'];
								break;
							}
						}
					}
				}
				$complementosCantidades[$j] = $complemento;
				break;
			}
		}
		if($encontrado == 0){
			$complementoNuevo["nomDias"] = $item["NOMDIAS"];
			$complementoNuevo["codigoPreparado"] = $item["codigoPreparado"];
			$complementoNuevo["codigo"] = $item["codigo"];
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
			for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
				$cantidadGrupoArray = "cantidadGrupo".$m;
				$complementoNuevo[$cantidadGrupoArray] = 0;
			}
			for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
				$grupoArrayIndex = "grupo".$m;
				$complementoNuevo[$grupoArrayIndex] = 0;
			}
			for ($m=1; $m <= $cantGruposEtarios; $m++) {
				for ($n=1; $n <= 5 ; $n++) { 
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
					for ($n=1; $n <=5 ; $n++) { 
						// $grupoDiaIndex = "grupo".$m."_d".$n;
						$auxDias = $item["NOMDIAS"];
						switch ($auxDias) {
							case ("lunes"):
								$grupoDiaIndex = "grupo".$m."_d".'1';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("martes"):
								$grupoDiaIndex = "grupo".$m."_d".'2';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("miércoles"):
								$grupoDiaIndex = "grupo".$m."_d".'3';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("jueves"):
								$grupoDiaIndex = "grupo".$m."_d".'4';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
							case ("viernes"):
								$grupoDiaIndex = "grupo".$m."_d".'5';
								$complementoNuevo[$grupoDiaIndex] = $item['cantidad'] * $item['cantidadPresentacion'];
							break;
						}
					}
				}
			}
			$complementosCantidades[] = $complementoNuevo;
		}
	}// Termina el if externo el de los items

	$_SESSION['complementosCantidades'] = $complementosCantidades;


/* ************************************************************************************************************************************************************************** */

	// Para la edición no necesitamos armar el array de consecutivos dado que tenemos un despacho especifico sobre el cual actuar.

	/* Insertando en productosmov$tablaAnno */
	$annoActual = $_SESSION['periodoActual'];
	date_default_timezone_set('America/Bogota');
	$fecha = date("Y-m-d H:i:s");

	for ($i=0; $i < count($sedes); $i++){
		$bodegaDestino = $sedes[$i];
		$consulta = " UPDATE productosmov$annoMes SET 	Nombre = '$nombre', 
														Nitcc = '$documento', 
														Tipo = '$tipoDocumento', 
														BodegaOrigen = $bodegaOrigen, 
														BodegaDestino = $bodegaDestino, 
														NombreResponsable = '$usuario', 
														LoginResponsable = '$login', 
														FechaMYSQL = '$fecha' , 
														TipoTransporte = $tipoTransporte, 
														Placa = '$placa', 
														ResponsableRecibe = '$conductor'
														WHERE Documento = 'DES' AND Numero = $despacho ";
		$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)." consulta : ".$consulta);
	}


	/* Insertando en productosmovdet$tablaAnno */
	// se va borrar los movimientos detallados para registrar los nuevos.
	$consulta = " DELETE FROM productosmovdet$annoMes WHERE Documento = 'DES' AND Numero = $despacho ";
	$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)." consulta : ".$consulta);

	// INICIA LA INSERCIÓN EN PRODUCTOS MOV_DET
	$consulta = " INSERT INTO productosmovdet$annoMes (Documento, Numero, Item, CodigoProducto, Descripcion, Cantidad, BodegaOrigen,BodegaDestino ,Umedida, CantUmedida, Factor, cantu2, cantu3, cantu4, cantu5, cantotalpresentacion ) VALUES ";
	$banderaPrimero = 0;
	for ($i=0; $i < count($sedesCobertura) ; $i++) {
		$auxItem = 1;
		for ($j=0; $j < count($complementosCantidades) ; $j++){
			$cantidad = 0;
			$auxConsulta = '';
			$auxConsulta = $auxConsulta." ( ";
			$auxAlimento = $complementosCantidades[$j];
			$codigo = $auxAlimento['codigo'];
			$sede = $sedesCobertura[$i];
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
				}// Termina ajuste contramuestra
			}else if ($auxAlimento['grupo_alim'] !== "Contramuestra") {
				for ($m=1; $m <= $cantGruposEtarios ; $m++) { 
					$cantidad = $cantidad + $t[$m];
				}
			}

			$unidad = $auxAlimento['unidadMedida'];
			$factor = $auxAlimento['factor'];
			$presentacion = $auxAlimento['presentacion'];
			include 'fn_despacho_generar_presentaciones.php';
			$auxConsulta = $auxConsulta." 'DES',$despacho,$auxItem,'$codigo','$componente',$cantidad,$bodegaOrigen,$bodegaDestino,'$presentacion',$cantidad,$factor, $necesario2, $necesario3, $necesario4, $necesario5, $tomadoTotal ";
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
/*	************************************************************* TERMINA LA INSERCIÓN EN PRODUCTOS MOV_DET ************************************************************  */

	$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)." consulta : ".$consulta);

	// Insertando en despachos_enc
	for ($i=0; $i < count($sedesCobertura); $i++) {
		if($i > 0){$consulta = $consulta." , "; }
		$sede = $sedesCobertura[$i];
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
			$valoresGrupos .= "Cobertura_G" .$m. "=" . "'" .$grupo[$m]. "', "; 
		}
		$valoresGrupos = trim($valoresGrupos, ", ");
		$cobertura = $sede['total'];
		$sede = $sede['cod_sede'];

		$consulta = "UPDATE despachos_enc$annoMes SET 	FechaHora_Elab = '$fecha', 
														Id_usuario = $idUsuario, 
														cod_Sede = $sede, 
														Tipo_Complem = '$tipo', 
														Semana = '$semana', 
														Cobertura = $cobertura, 
														Dias = '$diasDespacho', 
														Menus = '$menusReg', $valoresGrupos 
						WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho";
	}
	$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)." consulta : ".$consulta);
	
	// Insertando en despachos_det se van a borrar los despacho detallados para poder actualizar los registros.
	$consulta = " DELETE FROM despachos_det$annoMes WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho ";
	$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)." consulta : ".$consulta);

	// INICIA CONSULTA DE INSERCIÓN EN DESPACHOS DET
	$consulta = " insert into despachos_det$annoMes (Tipo_Doc, Num_Doc, cod_Alimento, id_GrupoEtario, Cantidad, D1, D2, D3, D4, D5) values ";

	// banderaPrimero: es una variable bandera que nos controla si el primer elelemnto ya fue agregado y a partir de hay colocar las comas que separan los valores de cada inserción.
	$banderaPrimero = 0;
	for ($i=0; $i < count($sedesCobertura) ; $i++) {
		$sede = $sedesCobertura[$i];
		for ($j=0; $j < count($complementosCantidades) ; $j++){

			// Se toman los datos del alimento en una cadena auxiliar para despues
			// validar si la cantidad es mayor a cero y agregar a la cadena que va
			// a hacer la insersión
			$auxAlimento = $complementosCantidades[$j];
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
				for ($n=1; $n <=5 ; $n++) { 
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
				// $consecutivo = $consecutivos[$i];
				$d1 = $d[1];
				$d2 = $d[2];
				$d3 = $d[3];
				$d4 = $d[4];
				$d5 = $d[5];
				$consulta = $consulta." 'DES',$consecutivo, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5";
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
						// $consecutivo = $consecutivos[$i];
						for ($n=1; $n <= 5 ; $n++) { 
							$grupoDiaIndex = "grupo".$m."_d".$n;
							$d[$n] = $auxAlimento[$grupoDiaIndex] * ($sede[$indexGrupo]);
						}
						$d1 = $d[1];
						$d2 = $d[2];
						$d3 = $d[3];
						$d4 = $d[4];
						$d5 = $d[5];
						$cantidad = $d1 + $d2 + $d3 + $d4 + $d5;
						$consulta = $consulta." 'DES',$despacho, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5";
						$consulta = $consulta." ) ";
					}
				}
			}
		}// Termina el for de los alimentos
	}
	// TERMINA CONSULTA DE INSERCIÓN EN DESPACHOS DET
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link)."<br><br> consulta : <br><br>".$consulta);
	$Link->close();
	echo "1";
}// Termina el if de bandera igual a cero
