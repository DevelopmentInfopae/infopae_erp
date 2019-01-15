<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$codDepartamento = mysqli_real_escape_string($Link, $_SESSION["p_CodDepartamento"]);
$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = "SELECT u.Ciudad, u.CodigoDANE FROM ubicacion u WHERE u.ETC = 0 AND CodigoDANE LIKE '$codDepartamento%' ORDER BY u.Ciudad asc";
$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
	while($row = $resultado->fetch_assoc()) {
		$codigo = $row['CodigoDANE'];
		$ciudad = $row['Ciudad'];
		$opciones .= " <option value=\"$codigo\">$ciudad</option> ";
	}
	$respuestaAJAX = [
		"estado" => 1,
		"opciones" => $opciones,
		"mensaje" => "Municipios cargados correctamente."
	];
}
// // Registro de la Bitácora
// $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '39', 'Actualizó la sede: <strong>". $nombre ."</strong>')";
// $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
//
// $respuestaAJAX = [
// 	"estado" => 1,
// 	"mensaje" => "La sede ha sido actualizó con éxito!"
// ];
echo json_encode($respuestaAJAX);
