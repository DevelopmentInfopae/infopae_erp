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


// validacion para que no tenga mas de un registro la misma cantidad de horas 
$validacionHoras = "SELECT hora_mes FROM parametros_nomina WHERE hora_mes = '$horasMes';";
$resValidacionHoras = $Link->query($validacionHoras) or die('Error al consultar las horas '. mysqli_error($Link));
if ($resValidacionHoras->num_rows == 1) {
	$respuestaAJAX = [
       'estado' => 0,
       'mensaje' => 'Ya existe un registro para esa cantidad de horas en un mes'
      ];
      exit (json_encode($respuestaAJAX));
}

$sentenciaInsert = "INSERT INTO  parametros_nomina (hora_mes, salario_minimo, auxilio_trans, desc_eps, desc_afp, arl_entidad, caja_entidad, caja_porc, icbf_porc, sena_porc, retefuente_servicios, retefuente_honorarios, reteica) VALUES ('$horasMes', '$salarioMinimo', '$auxilioTransporte', '$descuentoEps', '$descuentoAfp', '$entidadArl', '$entidadCaja', '$porcentajeCaja','$porcentajeIcbf',$porcentajeSena, '$retefuenteServicios', '$retefuenteHonorarios', '$reteica');";
$respuestaInsert = $Link->query($sentenciaInsert) or die('Error al insertar el parámetro nómina'. mysqli_error($Link));
  if($respuestaInsert) {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '66', 'Se creó el parámetro nómina de horas: <strong>".$horasMes."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

      $respuestaAJAX = [
            'estado' => 1,
            'mensaje' => 'El Parámetro Nómina se creó exitosamente.'
      ];
  }
  else
  {
      $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El Parámetro Nómina NO se creó exitosamente.'
      ];
  }

  echo json_encode($respuestaAJAX);


