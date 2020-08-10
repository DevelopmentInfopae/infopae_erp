<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

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
  $numeroDocumentohidden = (isset($_POST['numeroDocumentohidden']) && $_POST['numeroDocumentohidden'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroDocumentohidden']) : '';
  $idProveedor = (isset($_POST['idProveedor']) && $_POST['idProveedor'] != '') ? mysqli_real_escape_string($Link, $_POST['idProveedor']) : '';
  $estado = (isset($_POST['estado']) && $_POST['estado'] != '') ? mysqli_real_escape_string($Link, $_POST['estado']) : '';


  $tipoalimento = (isset($_POST['tipoalimento']) && $_POST['tipoalimento'] != '') ? implode($_POST['tipoalimento'], ",") : '';
  foreach ($_POST['tipoalimento'] as $tipo_alimento) {
    if ($tipo_alimento == 99) {
      $tipoalimento = 99;
      break;
    }
  }

  $consulta = "UPDATE proveedores SET
                Nombrecomercial = '$nombreComercial',
                RazonSocial = '$razonSocial',
                Direccion = '$direccion',
                Telefono1 = '$telefonofijo',
                Telefono2 = '$telefonomovil',
                PrimerNombre = '$primerNombre',
                SegundoNombre = '$segundoNombre',
                PrimerApellido = '$primerApellido',
                SegundoApellido = '$segundoApellido',
                TipoAlimento = '$tipoalimento',
                cod_municipio = '$municipio',
                compraslocales = '$compraslocales',
                estado = '$estado'
              WHERE Id = '$idProveedor';";
  $resultado = $Link->query($consulta) or die ('Error al actualizar proveedor: '. mysqli_error($Link));
  if ($resultado) {
    // Se actualiza los datos de usuario.
    $consulta2 = "UPDATE usuarios SET nombre = '$nombreCompleto', direccion = '$direccion', telefono = '$telefonofijo', cod_mun = '$municipio' WHERE num_doc = '$numeroDocumentohidden';";
    $resultado2 = $Link->query($consulta2) or die ('Error al actualizar datos de usuario: '. mysqli_error($Link));

    // Registro de la bitácora
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '66', 'Actualizó el proveedor <strong>$nombreCompleto</strong>')";
    $Link->query($consultaBitacora) or die (mysqli_error($Link));

    $respuestaAJAX = [
      'estado' => 1,
      'mensaje' => 'El proveedor ha sido actualizado exitosamente'
    ];
  } else {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'El proveedor NO ha sido actualizado.'
    ];
  }

  echo json_encode($respuestaAJAX);