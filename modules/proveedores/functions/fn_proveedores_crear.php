<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';


  $email = (isset($_POST['email']) && $_POST['email'] != '') ? mysqli_real_escape_string($Link, $_POST['email']) : '';
  $telefono = (isset($_POST['telefono']) && $_POST['telefono'] != '') ? mysqli_real_escape_string($Link, $_POST['telefono']) : '';
  $direccion = (isset($_POST['direccion']) && $_POST['direccion'] != '') ? mysqli_real_escape_string($Link, $_POST['direccion']) : '';
  $telefono2 = (isset($_POST['telefono2']) && $_POST['telefono2'] != '') ? mysqli_real_escape_string($Link, $_POST['telefono2']) : '';
  $primerNombre = (isset($_POST['primerNombre']) && $_POST['primerNombre'] != '') ? mysqli_real_escape_string($Link, $_POST['primerNombre']) : '';
  $tipoJuridico = (isset($_POST['tipoJuridico']) && $_POST['tipoJuridico'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoJuridico']) : '';
  $tipoDocumento = (isset($_POST['tipoDocumento']) && $_POST['tipoDocumento'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoDocumento']) : '';
  $segundoNombre = (isset($_POST['segundoNombre']) && $_POST['segundoNombre'] != '') ? mysqli_real_escape_string($Link, $_POST['segundoNombre']) : '';
  $primerApellido = (isset($_POST['primerApellido']) && $_POST['primerApellido'] != '') ? mysqli_real_escape_string($Link, $_POST['primerApellido']) : '';
  $nombreComercial = (isset($_POST['nombreComercial']) && $_POST['nombreComercial'] != '') ? mysqli_real_escape_string($Link, $_POST['nombreComercial']) : '';
  $numeroDocumento = (isset($_POST['numeroDocumento']) && $_POST['numeroDocumento'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroDocumento']) : '';
  $segundoApellido = (isset($_POST['segundoApellido']) && $_POST['segundoApellido'] != '') ? mysqli_real_escape_string($Link, $_POST['segundoApellido']) : '';
  $digitoVerificacion = (isset($_POST['digitoVerificacion']) && $_POST['digitoVerificacion'] != '') ? mysqli_real_escape_string($Link, $_POST['digitoVerificacion']) : '';
  $nombreCompleto = $primerNombre . ' ' . (($segundoNombre != '') ? $segundoNombre.' ' : '') . $primerApellido . ' ' . (($segundoApellido != '') ? $segundoApellido : '');

  // Validar que el código del proveedores no exista en proveedores.
  $consulta0 = "SELECT * FROM proveedores WHERE Nitcc = '$numeroDocumento';";
  $resultado0 = $Link->query($consulta0) or die('Error al consultar el número de documento de proveedor: '. mysqli_error($Link));
  if ($resultado0->num_rows > 0)
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No es posible crear el proveedor debido a que ya se encuentra registrado.'
    ];
    exit(json_encode($respuestaAJAX));
  }

  // Validar que el email del proveedor no exista.
  $consulta1 = "SELECT * FROM proveedores WHERE Email = '$email';";
  $resultado1 = $Link->query($consulta1) or die('Error al consultar el email del proveedores: '. mysqli_error($Link));
  if ($resultado1->num_rows > 0)
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No es posible crear el proveedor debido a que el correo ya se encuentra registrado.'
    ];
    exit(json_encode($respuestaAJAX));
  }


  $consulta = "INSERT INTO proveedores (Nitcc, Nombrecomercial, RazonSocial, Direccion, Telefono1, Telefono2, Email, TipoJuridico, PrimerNombre, SegundoNombre, PrimerApellido, SegundoApellido, TipoDocumento, DigitoVerificacion, FechaCreacion)
              VALUES (
                '$numeroDocumento',
                '$nombreComercial',
                '$nombreCompleto',
                '$direccion',
                '$telefono',
                '$telefono2',
                '$email',
                '$tipoJuridico',
                '$primerNombre',
                '$segundoNombre',
                '$primerApellido',
                '$segundoApellido',
                '$tipoDocumento',
                '$digitoVerificacion',
                '". date('Y-m-d H-i-s') ."')";
  $resultado = $Link->query($consulta) or die ('Error al insertar proveedores: '. mysqli_error($Link));
  if ($resultado)
  {
    // Se actualiza los datos de usuario.
    $consulta2 = "UPDATE usuarios SET nombre = '$nombreCompleto', direccion = '$direccion', telefono = '$telefono' WHERE num_doc = '$numeroDocumento';";
    $resultado2 = $Link->query($consulta2) or die ('Error al actualizar datos de usuario: '. mysqli_error($Link));

    // Registro de la bitácora
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '64', 'Creó el proveedor <strong>$nombreCompleto</strong>')";
    $Link->query($consultaBitacora) or die (mysqli_error($Link));

    $respuestaAJAX = [
      'estado' => 1,
      'mensaje' => 'El proveedor ha sido creado exitosamente'
    ];
  }
  else
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'El proveedor NO ha sido creado.'
    ];
  }

  echo json_encode($respuestaAJAX);
