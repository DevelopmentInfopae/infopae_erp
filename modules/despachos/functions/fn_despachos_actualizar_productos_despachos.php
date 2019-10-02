<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require_once "../../../autentication.php";


$periodo_actual = $_SESSION['periodoActual'];
$mes = (isset($_POST["mes_edicion"]) && !empty($_POST["mes_edicion"])) ? $Link->real_escape_string($_POST["mes_edicion"]) : "";
$sede = (isset($_POST["sede_edicion"]) && !empty($_POST["sede_edicion"])) ? $Link->real_escape_string($_POST["sede_edicion"]) : "";
$semana = (isset($_POST["semana_edicion"]) && !empty($_POST["semana_edicion"])) ? $Link->real_escape_string($_POST["semana_edicion"]) : "";
$municipio = (isset($_POST["municipio_edicion"]) && !empty($_POST["municipio_edicion"])) ? $Link->real_escape_string($_POST["municipio_edicion"]) : "";
$complemento = (isset($_POST["complemento_edicion"]) && !empty($_POST["complemento_edicion"])) ? $Link->real_escape_string($_POST["complemento_edicion"]) : "";
$institucion = (isset($_POST["institucion_edicion"]) && !empty($_POST["institucion_edicion"])) ? $Link->real_escape_string($_POST["institucion_edicion"]) : "";
$placa = (isset($_POST["placa"]) && !empty($_POST["placa"])) ? $Link->real_escape_string($_POST["placa"]) : "";
$conductor = (isset($_POST["conductor"]) && !empty($_POST["conductor"])) ? $Link->real_escape_string($_POST["conductor"]) : "";
$tipo_vehiculo = (isset($_POST["tipo_vehiculo"]) && !empty($_POST["tipo_vehiculo"])) ? $Link->real_escape_string($_POST["tipo_vehiculo"]) : "";
$lotes = (isset($_POST["lote"]) && !empty($_POST["lote"])) ? $_POST["lote"] : "";
$marcas = (isset($_POST["marca"]) && !empty($_POST["marca"])) ? $_POST["marca"] : "";
$productos = (isset($_POST["producto"]) && !empty($_POST["producto"])) ? $_POST["producto"] : "";
$fecha_vencimientos = (isset($_POST["fecha_vencimiento"]) && !empty($_POST["fecha_vencimiento"])) ? $_POST["fecha_vencimiento"] : "";

/**
 * Consulta que retorna los número de los depachos segun los filtros seleccionados.
 */
$condicion_sede = (isset($sede) && ! empty($sede)) ? " AND d.cod_Sede = '".$sede."'" : "";
$condicion_municipio = (isset($municipio) && ! empty($municipio)) ? " AND s.cod_mun_sede = '".$municipio."'" : "";
$condicion_intitucion = (isset($institucion) && ! empty($institucion)) ? " AND s.cod_inst = '".$institucion."'" : "";

$consulta_numeros_despachos = "SELECT
                                  p.Numero AS numero
                                FROM
                                  productosmov$mes$periodo_actual p
                                    INNER JOIN
                                  despachos_enc$mes$periodo_actual d ON (p.numero = d.Num_Doc)
                                    INNER JOIN
                                  sedes$periodo_actual s ON (p.BodegaDestino = s.cod_sede)
                                WHERE
                                  d.Semana = '$semana'
                                  AND d.Tipo_complem = '$complemento'
                                  $condicion_municipio
                                  $condicion_intitucion
                                  $condicion_sede
                                ORDER BY p.Numero;";
$respuesta_numeros_despachos = $Link->query($consulta_numeros_despachos) or die("Error al consultar los Número de los despachos: ". $Link->error);
if ($respuesta_numeros_despachos->num_rows > 0) {
	while ($resgistro_numeros_despachos = $respuesta_numeros_despachos->fetch_object()) {
		$numeros_despachos[] = $resgistro_numeros_despachos->numero;
	}
}

/**
 * Algoritmo para actualizar los datos de transporte del despacho
 */
if (! empty($placa) || ! empty($conductor) || ! empty($tipo_vehiculo)) {
	$condicion_placa = (! empty($placa)) ? " Placa='".$placa."', " : "";
	$condicion_conductor = (! empty($conductor)) ? " ResponsableRecibe='".$conductor."', " : "";
	$condicion_tipo_vehiculo = (! empty($tipo_vehiculo)) ? " TipoTransporte='".$tipo_vehiculo."', " : "";

	$condiciones = trim("$condicion_tipo_vehiculo $condicion_placa $condicion_conductor", ", ");

	foreach ($numeros_despachos as $numero_despacho) {
		$consulta_editar_datos_transporte = "UPDATE productosmov$mes$periodo_actual
																				SET
																					$condiciones
																				WHERE Numero = '".$numero_despacho."';";
		$respuesta_editar_datos_transporte = $Link->query($consulta_editar_datos_transporte) or die("Error no fue posible actualizar los datos de transporte:". $Link->error);
	}
}

/**
 * Algoritmo para actualizar los datos de cada producto del despacho
 */
foreach ($productos as $indice => $codigo_producto) {
	$lote = "Lote='".$lotes[$indice]."', ";
	$fecha_vencimiento = "FechaVencimiento='".$fecha_vencimientos[$indice]."', ";
	$marca = (! empty($marcas[$indice])) ? "marca='".$marcas[$indice]."', " : "";

	$condiciones_datos_productos = trim("$lote $fecha_vencimiento $marca", ", ");

	$consulta_editar_datos_productos = "UPDATE productosmovdet$mes$periodo_actual pmd
											INNER JOIN despachos_enc$mes$periodo_actual de ON  pmd.Numero=de.Num_doc
											INNER JOIN sedes$periodo_actual s ON (pmd.BodegaDestino = s.cod_sede)
										SET
											$condiciones_datos_productos
										WHERE
											pmd.CodigoProducto = '$codigo_producto'
											AND de.Num_doc = pmd.Numero
											AND de.Semana = '$semana'
											AND de.Tipo_complem = '$complemento'";
	$respuesta_editar_datos_productos = $Link->query($consulta_editar_datos_productos) or die("Error al actualizar los datos de los productos: ". $Link->error);
}

/**
 * Algoritmo para validar los productos y cambiar el estado del despacho
 */
foreach ($numeros_despachos as $numero_despacho) {
	$estado_despacho = 2;
	$consulta_datos_productos = "SELECT
								    Lote as lote,
								    FechaVencimiento as fecha_vencimiento,
								    marca
								FROM
								    productosmovdet$mes$periodo_actual
								WHERE
								    Numero = '".$numero_despacho."';";
	$respuesta_datos_consulta = $Link->query($consulta_datos_productos) or die("Error al consultar datos de productos: ". $Link->error);
	if ($respuesta_datos_consulta->num_rows > 0) {
		$datos_vacios = 0;
		while ($registro_datos_productos = $respuesta_datos_consulta->fetch_object()) {
			if (empty($registro_datos_productos->lote) || is_null($registro_datos_productos->lote)) { $datos_vacios ++; }

			if (empty($registro_datos_productos->fecha_vencimiento) || is_null($registro_datos_productos->fecha_vencimiento)) { $datos_vacios ++; }

			if (empty($registro_datos_productos->marca) || is_null($registro_datos_productos->marca)) { $datos_vacios ++; }
		}
	}

	if ($datos_vacios == 0) {
		$consulta_editar_estado_despacho = "UPDATE despachos_enc$mes$periodo_actual
																				SET Estado='1'
																				WHERE Num_Doc = '".$numero_despacho."'";
		$respuesta_editar_estado_despacho = $Link->query($consulta_editar_estado_despacho) or die("Error al actualizar el estado del despacho: ". $Link->error);
	}
}

/**
 * Algoritmo para registrar los datos en bitácora de acciones
 */
$consulta_registrar_bitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('".date("Y-m-d H:i:s")."', '".$_SESSION["id_usuario"]."', '54', 'Actualizó datos de Despachos');";
$respuesta_registrar_bitacora = $Link->query($consulta_registrar_bitacora) or die("Error al guardar datos de bitácora: ". $Link->error);


$respuesta_ajax =  [
	'estado'=>1,
	'mensaje'=>"Datos guardados exitosamente."
];

echo json_encode($respuesta_ajax);