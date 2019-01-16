<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';


  $email = (isset($_POST['email']) && $_POST['email'] != '') ? mysqli_real_escape_string($Link, $_POST['email']) : '';
  $telefono = (isset($_POST['telefono']) && $_POST['telefono'] != '') ? mysqli_real_escape_string($Link, $_POST['telefono']) : '';
  $direccion = (isset($_POST['direccion']) && $_POST['direccion'] != '') ? mysqli_real_escape_string($Link, $_POST['direccion']) : '';
  $telefono2 = (isset($_POST['telefono2']) && $_POST['telefono2'] != '') ? mysqli_real_escape_string($Link, $_POST['telefono2']) : '';
  $idProveedor = (isset($_POST['idProveedor']) && $_POST['idProveedor'] != '') ? mysqli_real_escape_string($Link, $_POST['idProveedor']) : '';
  $primerNombre = (isset($_POST['primerNombre']) && $_POST['primerNombre'] != '') ? mysqli_real_escape_string($Link, $_POST['primerNombre']) : '';
  $tipoDocumento = (isset($_POST['tipoDocumento']) && $_POST['tipoDocumento'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoDocumento']) : '';
  $tipoJuridico = (isset($_POST['tipoJuridico']) && $_POST['tipoJuridico'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoJuridico']) : '';
  $segundoNombre = (isset($_POST['segundoNombre']) && $_POST['segundoNombre'] != '') ? mysqli_real_escape_string($Link, $_POST['segundoNombre']) : '';
  $primerApellido = (isset($_POST['primerApellido']) && $_POST['primerApellido'] != '') ? mysqli_real_escape_string($Link, $_POST['primerApellido']) : '';
  $nombreComercial = (isset($_POST['nombreComercial']) && $_POST['nombreComercial'] != '') ? mysqli_real_escape_string($Link, $_POST['nombreComercial']) : '';
  $segundoApellido = (isset($_POST['segundoApellido']) && $_POST['segundoApellido'] != '') ? mysqli_real_escape_string($Link, $_POST['segundoApellido']) : '';
  $nombreCompleto = $primerNombre . ' ' . (($segundoNombre != '') ? $segundoNombre.' ' : '') . $primerApellido . ' ' . (($segundoApellido != '') ? $segundoApellido : '');
  $digitoVerificacion = (isset($_POST['digitoVerificacion']) && $_POST['digitoVerificacion'] != '') ? mysqli_real_escape_string($Link, $_POST['digitoVerificacion']) : '';
  $numeroDocumentohidden = (isset($_POST['numeroDocumentohidden']) && $_POST['numeroDocumentohidden'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroDocumentohidden']) : '';

  $consulta = "UPDATE proveedores SET
                Nombrecomercial = '$nombreComercial',
                RazonSocial = '$nombreCompleto',
                Direccion = '$direccion',
                Telefono1 = '$telefono',
                Telefono2 = '$telefono2',
                Email = '$email',
                TipoJuridico = '$tipoJuridico',
                PrimerNombre = '$primerNombre',
                SegundoNombre = '$segundoNombre',
                PrimerApellido = '$primerApellido',
                SegundoApellido = '$segundoApellido',
                TipoDocumento = '$tipoDocumento',
                DigitoVerificacion = '$digitoVerificacion'
              WHERE Id = '$idProveedor';";
  $resultado = $Link->query($consulta) or die ('Error al actualizar proveedor: '. mysqli_error($Link));
  if ($resultado)
  {
    // Se actualiza los datos de usuario.
    $consulta2 = "UPDATE usuarios SET nombre = '$nombreCompleto', direccion = '$direccion', telefono = '$telefono' WHERE num_doc = '$numeroDocumentohidden';";
    $resultado2 = $Link->query($consulta2) or die ('Error al actualizar datos de usuario: '. mysqli_error($Link));

    // Registro de la bitácora
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '66', 'Actualizó el proveedor <strong>$nombreCompleto</strong>')";
    $Link->query($consultaBitacora) or die (mysqli_error($Link));

    $respuestaAJAX = [
      'estado' => 1,
      'mensaje' => 'El proveedor ha sido actualizado exitosamente'
    ];
  }
  else
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'El proveedor NO ha sido actualizado.'
    ];
  }

  echo json_encode($respuestaAJAX);
