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
  $idEmpleado = (isset($_POST['idEmpleado']) && $_POST['idEmpleado'] != '') ? mysqli_real_escape_string($Link, $_POST['idEmpleado']) : '';
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
  $segundoApellido = (isset($_POST['segundoApellido']) && $_POST['segundoApellido'] != '') ? mysqli_real_escape_string($Link, $_POST['segundoApellido']) : '';
  $fechaNacimiento = (isset($_POST['fechaNacimiento']) && $_POST['fechaNacimiento'] != '') ? mysqli_real_escape_string($Link, $_POST['fechaNacimiento']) : '';
  $nombreCompleto = $primerNombre . ' ' . (($segundoNombre != '') ? $segundoNombre.' ' : '') . $primerApellido . ' ' . (($segundoApellido != '') ? $segundoApellido : '');
  $municipioResidencia = (isset($_POST['municipioResidencia']) && $_POST['municipioResidencia'] != '') ? mysqli_real_escape_string($Link, $_POST['municipioResidencia']) : '';
  $municipioNacimiento = (isset($_POST['municipioNacimiento']) && $_POST['municipioNacimiento'] != '') ? mysqli_real_escape_string($Link, $_POST['municipioNacimiento']) : '';
  $numeroDocumentohidden = (isset($_POST['numeroDocumentohidden']) && $_POST['numeroDocumentohidden'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroDocumentohidden']) : '';
  $departamentoResidencia = (isset($_POST['departamentoResidencia']) && $_POST['departamentoResidencia'] != '') ? mysqli_real_escape_string($Link, $_POST['departamentoResidencia']) : '';
  $departamentoNacimiento = (isset($_POST['departamentoNacimiento']) && $_POST['departamentoNacimiento'] != '') ? mysqli_real_escape_string($Link, $_POST['departamentoNacimiento']) : '';

  $consulta = "UPDATE empleados SET
                Nombre = '$nombreCompleto',
                Direccion = '$direccion',
                Telefono1 = '$telefono',
                Telefono2 = '$telefono2',
                FechaNacimiento = '$fechaNacimiento',
                LugarNacimiento = '$municipioNacimiento',
                Sexo = '$sexo',
                LibretaMilitar = '$libretaMilitar',
                TipoSangre = '$tipoSangre',
                EstadoCivil = '$estadoCivil',
                Ciudad = '$municipioResidencia',
                Profesion = '$profesion',
                Barrio = '$barrio',
                NivelEstudio = '$nivelEstudio',
                PrimerNombre = '$primerNombre',
                SegundoNombre = '$segundoNombre',
                PrimerApellido = '$primerApellido',
                SegundoApellido = '$segundoApellido',
                Cargo = '$cargo',
                TallaPantalon = '$tallaPantalon',
                TallaCamisa = '$tallaCamisa',
                NumeroCalzado = '$numeroCalzado',
                Contrato = '$numeroContrato'
              WHERE ID = '$idEmpleado';";
  $resultado = $Link->query($consulta) or die ('Error al actualizar empleados: '. mysqli_error($Link));
  if ($resultado)
  {
    // Se actualiza los datos de usuario.
    $consulta2 = "UPDATE usuarios SET nombre = '$nombreCompleto', direccion = '$direccion', telefono = '$telefono' WHERE num_doc = '$numeroDocumentohidden';";
    $resultado2 = $Link->query($consulta2) or die ('Error al actualizar datos de usuario: '. mysqli_error($Link));

    // Registro de la bitácora
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '63', 'Actualizó el empleado <strong>$nombreCompleto</strong>')";
    $Link->query($consultaBitacora) or die (mysqli_error($Link));

    $respuestaAJAX = [
      'estado' => 1,
      'mensaje' => 'El empleado ha sido actualizado exitosamente'
    ];
  }
  else
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'El empleado NO ha sido actualizado.'
    ];
  }

  echo json_encode($respuestaAJAX);
