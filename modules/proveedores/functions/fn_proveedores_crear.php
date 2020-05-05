<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $tipoJuridico = (isset($_POST['tipoJuridico']) && $_POST['tipoJuridico'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoJuridico']) : '';
  $tipoRegimen = (isset($_POST['tipoRegimen']) && $_POST['tipoRegimen'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoRegimen']) : '';
  $tipoDocumento = (isset($_POST['tipoDocumento']) && $_POST['tipoDocumento'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoDocumento']) : '';
  $numeroDocumento = (isset($_POST['numeroDocumento']) && $_POST['numeroDocumento'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroDocumento']) : '';
  $digitoVerificacion = (isset($_POST['digitoVerificacion']) && $_POST['digitoVerificacion'] != '') ? mysqli_real_escape_string($Link, $_POST['digitoVerificacion']) : '';
  $razonSocial = (isset($_POST['razonSocial']) && $_POST['razonSocial'] != '') ? mysqli_real_escape_string($Link, $_POST['razonSocial']) : '';
  $nombreComercial = (isset($_POST['nombreComercial']) && $_POST['nombreComercial'] != '') ? mysqli_real_escape_string($Link, $_POST['nombreComercial']) : '';
  $primerNombre = (isset($_POST['primerNombre']) && $_POST['primerNombre'] != '') ? mysqli_real_escape_string($Link, $_POST['primerNombre']) : '';
  $segundoNombre = (isset($_POST['segundoNombre']) && $_POST['segundoNombre'] != '') ? mysqli_real_escape_string($Link, $_POST['segundoNombre']) : '';
  $primerApellido = (isset($_POST['primerApellido']) && $_POST['primerApellido'] != '') ? mysqli_real_escape_string($Link, $_POST['primerApellido']) : '';
  $segundoApellido = (isset($_POST['segundoApellido']) && $_POST['segundoApellido'] != '') ? mysqli_real_escape_string($Link, $_POST['segundoApellido']) : '';
  $email = (isset($_POST['email']) && $_POST['email'] != '') ? mysqli_real_escape_string($Link, $_POST['email']) : '';
  $telefonofijo = (isset($_POST['telefonofijo']) && $_POST['telefonofijo'] != '') ? mysqli_real_escape_string($Link, $_POST['telefonofijo']) : '';
  $telefonomovil = (isset($_POST['telefonomovil']) && $_POST['telefonomovil'] != '') ? mysqli_real_escape_string($Link, $_POST['telefonomovil']) : '';
  $direccion = (isset($_POST['direccion']) && $_POST['direccion'] != '') ? mysqli_real_escape_string($Link, $_POST['direccion']) : '';
  $municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST['municipio']) : '';
  $compraslocales = (isset($_POST['compraslocales']) && $_POST['compraslocales'] != '') ? mysqli_real_escape_string($Link, $_POST['compraslocales']) : '';
  $nombreCompleto = $primerNombre . ' ' . (($segundoNombre != '') ? $segundoNombre.' ' : '') . $primerApellido . ' ' . (($segundoApellido != '') ? $segundoApellido : '');

  $tipoalimento = (isset($_POST['tipoalimento']) && $_POST['tipoalimento'] != '') ? implode($_POST['tipoalimento'], ",") : '';
  foreach ($_POST['tipoalimento'] as $tipo_alimento) {
    if ($tipo_alimento == 99) {
      $tipoalimento = 99;
      break;
    }
  }

  $tipoalimento = (isset($_POST['tipoalimento']) && $_POST['tipoalimento'] != '') ? implode($_POST['tipoalimento'], ",") : '';

  // Validar que el código del proveedores no exista en proveedores.
  $consulta0 = "SELECT * FROM proveedores WHERE Nitcc = '$numeroDocumento';";
  $resultado0 = $Link->query($consulta0) or die('Error al consultar el número de documento de proveedor: '. mysqli_error($Link));
  if ($resultado0->num_rows > 0) {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No es posible crear el proveedor debido a que ya se encuentra registrado.'
    ];
    exit(json_encode($respuestaAJAX));
  }

  // Validar que el email del proveedor no exista.
  $consulta1 = "SELECT * FROM proveedores WHERE Email = '$email';";
  $resultado1 = $Link->query($consulta1) or die('Error al consultar el email del proveedores: '. mysqli_error($Link));
  if ($resultado1->num_rows > 0) {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No es posible crear el proveedor debido a que el correo ya se encuentra registrado.'
    ];
    exit(json_encode($respuestaAJAX));
  }

  $consulta = "INSERT INTO proveedores (TipoDocumento, Nitcc, DigitoVerificacion, TipoJuridico, TipoRegimen, Nombrecomercial, RazonSocial, Direccion, Telefono1, Telefono2, Email, PrimerNombre, SegundoNombre, PrimerApellido, SegundoApellido, TipoAlimento, cod_municipio, compraslocales, FechaCreacion)
              VALUES (
                '$tipoDocumento',
                '$numeroDocumento',
                '$digitoVerificacion',
                '$tipoJuridico',
                '$tipoRegimen',
                '$nombreComercial',
                '$razonSocial',
                '$direccion',
                '$telefonofijo',
                '$telefonomovil',
                '$email',
                '$primerNombre',
                '$segundoNombre',
                '$primerApellido',
                '$segundoApellido',
                '$tipoalimento',
                '$municipio',
                '$compraslocales',
                '". date('Y-m-d H-i-s') ."')";
  $resultado = $Link->query($consulta) or die ('Error al insertar proveedores: '. mysqli_error($Link));
  if ($resultado) {
    $clave = sha1(strtoupper(substr($primerNombre, 0, 1)) . $numeroDocumento);

    $consulta2 = " INSERT INTO usuarios (nombre, clave, direccion, cod_mun, telefono, email, id_perfil, nueva_clave, num_doc, Tipo_Usuario) VALUES ('$nombreCompleto ', '$clave', '$direccion', '$municipio', '$telefonofijo', '$email', '2', '0', '$numeroDocumento', 'Proveedor')";

    // Se actualiza los datos de usuario.
    $resultado2 = $Link->query($consulta2) or die ('Error al crear el usuario: '. mysqli_error($Link));

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
