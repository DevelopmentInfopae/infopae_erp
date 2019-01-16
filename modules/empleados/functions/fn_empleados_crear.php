<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $sexo = (isset($_POST['sexo']) && $_POST['sexo'] != '') ? $_POST['sexo'] : '';
  $email = (isset($_POST['email']) && $_POST['email'] != '') ? mysqli_real_escape_string($Link, $_POST['email']) : '';
  $cargo = (isset($_POST['cargo']) && $_POST['cargo'] != '') ? mysqli_real_escape_string($Link, $_POST['cargo']) : '';
  $barrio = (isset($_POST['barrio']) && $_POST['barrio'] != '') ? mysqli_real_escape_string($Link, $_POST['barrio']) : '';
  $telefono = (isset($_POST['telefono']) && $_POST['telefono'] != '') ? mysqli_real_escape_string($Link, $_POST['telefono']) : '';
  $direccion = (isset($_POST['direccion']) && $_POST['direccion'] != '') ? mysqli_real_escape_string($Link, $_POST['direccion']) : '';
  $telefono2 = (isset($_POST['telefono2']) && $_POST['telefono2'] != '') ? mysqli_real_escape_string($Link, $_POST['telefono2']) : '';
  $profesion = (isset($_POST['profesion']) && $_POST['profesion'] != '') ? mysqli_real_escape_string($Link, $_POST['profesion']) : '';
  $tipoSangre = (isset($_POST['tipoSangre']) && $_POST['tipoSangre'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoSangre']) : '';
  $estadoCivil = (isset($_POST['estadoCivil']) && $_POST['estadoCivil'] != '') ? mysqli_real_escape_string($Link, $_POST['estadoCivil']) : '';
  $estadoCivil = (isset($_POST['estadoCivil']) && $_POST['estadoCivil'] != '') ? mysqli_real_escape_string($Link, $_POST['estadoCivil']) : '';
  $tallaCamisa = (isset($_POST['tallaCamisa']) && $_POST['tallaCamisa'] != '') ? mysqli_real_escape_string($Link, $_POST['tallaCamisa']) : '';
  $primerNombre = (isset($_POST['primerNombre']) && $_POST['primerNombre'] != '') ? mysqli_real_escape_string($Link, $_POST['primerNombre']) : '';
  $nivelEstudio = (isset($_POST['nivelEstudio']) && $_POST['nivelEstudio'] != '') ? mysqli_real_escape_string($Link, $_POST['nivelEstudio']) : '';
  $tipoDocumento = (isset($_POST['tipoDocumento']) && $_POST['tipoDocumento'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoDocumento']) : '';
  $numeroCalzado = (isset($_POST['numeroCalzado']) && $_POST['numeroCalzado'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroCalzado']) : '';
  $segundoNombre = (isset($_POST['segundoNombre']) && $_POST['segundoNombre'] != '') ? mysqli_real_escape_string($Link, $_POST['segundoNombre']) : '';
  $tallaPantalon = (isset($_POST['tallaPantalon']) && $_POST['tallaPantalon'] != '') ? mysqli_real_escape_string($Link, $_POST['tallaPantalon']) : '';
  $libretaMilitar = (isset($_POST['libretaMilitar']) && $_POST['libretaMilitar'] != '') ? mysqli_real_escape_string($Link, $_POST['libretaMilitar']) : '';
  $primerApellido = (isset($_POST['primerApellido']) && $_POST['primerApellido'] != '') ? mysqli_real_escape_string($Link, $_POST['primerApellido']) : '';
  $numeroContrato = (isset($_POST['numeroContrato']) && $_POST['numeroContrato'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroContrato']) : '';
  $numeroDocumento = (isset($_POST['numeroDocumento']) && $_POST['numeroDocumento'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroDocumento']) : '';
  $segundoApellido = (isset($_POST['segundoApellido']) && $_POST['segundoApellido'] != '') ? mysqli_real_escape_string($Link, $_POST['segundoApellido']) : '';
  $fechaNacimiento = (isset($_POST['fechaNacimiento']) && $_POST['fechaNacimiento'] != '') ? mysqli_real_escape_string($Link, $_POST['fechaNacimiento']) : '';
  $nombreCompleto = $primerNombre . ' ' . (($segundoNombre != '') ? $segundoNombre.' ' : '') . $primerApellido . ' ' . (($segundoApellido != '') ? $segundoApellido : '');
  $municipioResidencia = (isset($_POST['municipioResidencia']) && $_POST['municipioResidencia'] != '') ? mysqli_real_escape_string($Link, $_POST['municipioResidencia']) : '';
  $municipioNacimiento = (isset($_POST['municipioNacimiento']) && $_POST['municipioNacimiento'] != '') ? mysqli_real_escape_string($Link, $_POST['municipioNacimiento']) : '';
  $departamentoResidencia = (isset($_POST['departamentoResidencia']) && $_POST['departamentoResidencia'] != '') ? mysqli_real_escape_string($Link, $_POST['departamentoResidencia']) : '';
  $departamentoNacimiento = (isset($_POST['departamentoNacimiento']) && $_POST['departamentoNacimiento'] != '') ? mysqli_real_escape_string($Link, $_POST['departamentoNacimiento']) : '';

  // Validar que el código del empleado no exista en empleados.
  $consulta0 = "SELECT * FROM empleados WHERE Nitcc = '$numeroDocumento';";
  $resultado0 = $Link->query($consulta0) or die('Error al consultar el número de documento de empleado: '. mysqli_error($Link));
  if ($resultado0->num_rows > 0)
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No es posible crear el empleado debido a que ya se encuentra registrado.'
    ];
    exit(json_encode($respuestaAJAX));
  }

  // Validar que el email del empleado no exista.
  $consulta1 = "SELECT * FROM empleados WHERE Email = '$email';";
  $resultado1 = $Link->query($consulta1) or die('Error al consultar el email del empleado: '. mysqli_error($Link));
  if ($resultado1->num_rows > 0)
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No es posible crear el empleado debido a que el correo ya se encuentra registrado.'
    ];
    exit(json_encode($respuestaAJAX));
  }


  $consulta = "INSERT INTO empleados  (Nitcc, Nombre, Direccion, Telefono1, Telefono2, FechaNacimiento, LugarNacimiento, Sexo, LibretaMilitar, TipoSangre, EstadoCivil, Ciudad, Profesion, Barrio, Email, NivelEstudio, PrimerNombre, SegundoNombre, PrimerApellido, SegundoApellido, Cargo, TallaPantalon, TallaCamisa, NumeroCalzado, TipoDoc, FechaCreacion, Contrato)
              VALUES (
                '$numeroDocumento',
                '$nombreCompleto',
                '$direccion',
                '$telefono',
                '$telefono2',
                '$fechaNacimiento',
                '$municipioNacimiento',
                '$sexo',
                '$libretaMilitar',
                '$tipoSangre',
                '$estadoCivil',
                '$municipioResidencia',
                '$profesion',
                '$barrio',
                '$email',
                '$nivelEstudio',
                '$primerNombre',
                '$segundoNombre',
                '$primerApellido',
                '$segundoApellido',
                '$cargo',
                '$tallaPantalon',
                '$tallaCamisa',
                '$numeroCalzado',
                '$tipoDocumento',
                '". date('Y-m-d H-i-s') ."',
                '$numeroContrato')";
  $resultado = $Link->query($consulta) or die ('Error al insertar empleados: '. mysqli_error($Link));
  if ($resultado)
  {
    // Se actualiza los datos de usuario.
    $consulta2 = "UPDATE usuarios SET nombre = '$nombreCompleto', direccion = '$direccion', telefono = '$telefono' WHERE num_doc = '$numeroDocumento';";
    $resultado2 = $Link->query($consulta2) or die ('Error al actualizar datos de usuario: '. mysqli_error($Link));

    // Registro de la bitácora
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '62', 'Creó el empleado <strong>$nombreCompleto</strong>')";
    $Link->query($consultaBitacora) or die (mysqli_error($Link));

    $respuestaAJAX = [
      'estado' => 1,
      'mensaje' => 'El empleado ha sido creado exitosamente'
    ];
  }
  else
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'El empleado NO ha sido creado.'
    ];
  }

  echo json_encode($respuestaAJAX);
