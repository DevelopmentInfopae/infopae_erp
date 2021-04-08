<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$horasMes = (isset($_POST['horasMes']) && $_POST['horasMes'] != '') ? mysqli_real_escape_string($Link, $_POST['horasMes']) : '';
$salarioMinimo = (isset($_POST['salarioMinimo']) && $_POST['salarioMinimo'] != '') ? mysqli_real_escape_string($Link, $_POST['salarioMinimo']) : '';
$auxilioTransporte = (isset($_POST['auxilioTransporte']) && $_POST['auxilioTransporte'] != '') ? mysqli_real_escape_string($Link, $_POST['auxilioTransporte']) : '';
$descuentoEps = (isset($_POST['descuentoEps']) && $_POST['descuentoEps'] != '') ? mysqli_real_escape_string($Link, $_POST['descuentoEps']) : '';
$descuentoAfp = (isset($_POST['descuentoAfp']) && $_POST['descuentoAfp'] != '') ? mysqli_real_escape_string($Link, $_POST['descuentoAfp']) : '';
$entidadArl = (isset($_POST['entidadArl']) && $_POST['entidadArl'] != '') ? mysqli_real_escape_string($Link, $_POST['entidadArl']) : '';
$entidadCaja = (isset($_POST['entidadCaja']) && $_POST['entidadCaja'] != '') ? mysqli_real_escape_string($Link, $_POST['entidadCaja']) : '';
$porcentajeCaja = (isset($_POST['porcentajeCaja']) && $_POST['porcentajeCaja'] != '') ? mysqli_real_escape_string($Link, $_POST['porcentajeCaja']) : '';
$porcentajeIcbf = (isset($_POST['porcentajeIcbf']) && $_POST['porcentajeIcbf'] != '') ? mysqli_real_escape_string($Link, $_POST['porcentajeIcbf']) : '';
$porcentajeSena = (isset($_POST['porcentajeSena']) && $_POST['porcentajeSena'] != '') ? mysqli_real_escape_string($Link, $_POST['porcentajeSena']) : '';
$retefuenteServicios = (isset($_POST['retefuenteServicios']) && $_POST['retefuenteServicios'] != '') ? mysqli_real_escape_string($Link, $_POST['retefuenteServicios']) : '';
$retefuenteHonorarios = (isset($_POST['retefuenteHonorarios']) && $_POST['retefuenteHonorarios'] != '') ? mysqli_real_escape_string($Link, $_POST['retefuenteHonorarios']) : '';
$reteica = (isset($_POST['reteica']) && $_POST['reteica'] != '') ? mysqli_real_escape_string($Link, $_POST['reteica']) : '';

		
$sentenciaEditar = "UPDATE parametros_nomina SET salario_minimo = '$salarioMinimo', auxilio_trans = '$auxilioTransporte', arl_entidad ='$entidadArl', caja_entidad = '$entidadCaja' WHERE hora_mes = '".$horasMes."';";

// exit(var_dump($sentenciaEditar));
$repuestaEditar = $Link->query($sentenciaEditar) or die('Error al actualizar'. mysqli_error($Link));

if($repuestaEditar){
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '67', 'Se actualizó el parámetro nómina de horas: <strong>".$horasMes."</strong>')";
   	$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Parámetro nómina se actualizó exitosamente.'
  	];
  	exit (json_encode($respuestaAJAX));
}
