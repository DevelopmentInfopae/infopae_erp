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
  $numeroDocumento = (isset($_POST['numeroDocumento']) && $_POST['numeroDocumento'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroDocumento']) : '';
  $departamentoResidencia = (isset($_POST['departamentoResidencia']) && $_POST['departamentoResidencia'] != '') ? mysqli_real_escape_string($Link, $_POST['departamentoResidencia']) : '';
  $departamentoNacimiento = (isset($_POST['departamentoNacimiento']) && $_POST['departamentoNacimiento'] != '') ? mysqli_real_escape_string($Link, $_POST['departamentoNacimiento']) : '';

    // Nuevos campos
  $tipoEmpleado = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : 0;

  $TipoContrato = (isset($_POST['TipoContrato']) && $_POST['TipoContrato'] != '') ? mysqli_real_escape_string($Link, $_POST['TipoContrato']) : '';
  $ValorBaseMes = (isset($_POST['ValorBaseMes']) && $_POST['ValorBaseMes'] != '') ? mysqli_real_escape_string($Link, $_POST['ValorBaseMes']) : '';
  $FechaInicalContrato = (isset($_POST['FechaInicalContrato']) && $_POST['FechaInicalContrato'] != '') ? mysqli_real_escape_string($Link, $_POST['FechaInicalContrato']) : '';
  $FechaFinalContrato = (isset($_POST['FechaFinalContrato']) && $_POST['FechaFinalContrato'] != '') ? mysqli_real_escape_string($Link, $_POST['FechaFinalContrato']) : '';

$TipoServicio = (isset($_POST['TipoServicio']) && $_POST['TipoServicio'] != '') ? mysqli_real_escape_string($Link, $_POST['TipoServicio']) : null;
$SalarioIntegral = (isset($_POST['SalarioIntegral']) && $_POST['SalarioIntegral'] != '') ? mysqli_real_escape_string($Link, $_POST['SalarioIntegral']) : null;  
$DuracionDias = (isset($_POST['DuracionDias']) && $_POST['DuracionDias'] != '') ? mysqli_real_escape_string($Link, $_POST['DuracionDias']) : null;
$auxilio_transporte = (isset($_POST['auxilio_transporte']) && $_POST['auxilio_transporte'] != '') ? mysqli_real_escape_string($Link, $_POST['auxilio_transporte']) : null; 
$auxilio_extra = (isset($_POST['auxilio_extra']) && $_POST['auxilio_extra'] != '') ? mysqli_real_escape_string($Link, $_POST['auxilio_extra']) : 0; 
$afp_entidad = (isset($_POST['afp_entidad']) && $_POST['afp_entidad'] != '') ? mysqli_real_escape_string($Link, $_POST['afp_entidad']) : 0; 
$eps_entidad = (isset($_POST['eps_entidad']) && $_POST['eps_entidad'] != '') ? mysqli_real_escape_string($Link, $_POST['eps_entidad']) : 0; 
$arl_riesgo = (isset($_POST['arl_riesgo']) && $_POST['arl_riesgo'] != '') ? mysqli_real_escape_string($Link, $_POST['arl_riesgo']) : 0; 
$caja = (isset($_POST['caja']) && $_POST['caja'] != '') ? mysqli_real_escape_string($Link, $_POST['caja']) : 0;
$icbf = (isset($_POST['icbf']) && $_POST['icbf'] != '') ? mysqli_real_escape_string($Link, $_POST['icbf']) : 0; 
$sena = (isset($_POST['sena']) && $_POST['sena'] != '') ? mysqli_real_escape_string($Link, $_POST['sena']) : 0; 
$Forma_pago = (isset($_POST['Forma_pago']) && $_POST['Forma_pago'] != '') ? mysqli_real_escape_string($Link, $_POST['Forma_pago']) : null; 
$Banco = (isset($_POST['Banco']) && $_POST['Banco'] != '') ? mysqli_real_escape_string($Link, $_POST['Banco']) : null; 
$Tipo_Cuenta = (isset($_POST['Tipo_cuenta']) && $_POST['Tipo_cuenta'] != '') ? mysqli_real_escape_string($Link, $_POST['Tipo_cuenta']) : null; 
$Numero_Cuenta = (isset($_POST['Numero_Cuenta']) && $_POST['Numero_Cuenta'] != '') ? mysqli_real_escape_string($Link, $_POST['Numero_Cuenta']) : null;

  $manipulador_tipo_complemento = (isset($_POST['manipulador_tipo_complemento']) && $_POST['manipulador_tipo_complemento'] != '') ? ($_POST['manipulador_tipo_complemento']) : '';
  $manipulador_municipio = (isset($_POST['manipulador_municipio']) && $_POST['manipulador_municipio'] != '') ? ($_POST['manipulador_municipio']) : '';
  $manipulador_institucion = (isset($_POST['manipulador_institucion']) && $_POST['manipulador_institucion'] != '') ? ($_POST['manipulador_institucion']) : '';
  $manipulador_sede = (isset($_POST['manipulador_sede']) && $_POST['manipulador_sede'] != '') ? ($_POST['manipulador_sede']) : '';
  $manipulador_id = (isset($_POST['manipulador_id']) && $_POST['manipulador_id'] != '') ? ($_POST['manipulador_id']) : '';
  $manipulador_estado = (isset($_POST['manipulador_estado']) && $_POST['manipulador_estado'] != '') ? ($_POST['manipulador_estado']) : '';

  $estado = (isset($_POST['estado']) && $_POST['estado'] != '') ? mysqli_real_escape_string($Link, $_POST['estado']) : 0;

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
                Contrato = '$numeroContrato',
                tipo = '$tipoEmpleado',
                TipoContrato = '$TipoContrato',
                ValorBaseMes = '$ValorBaseMes',
                FechaInicalContrato = '$FechaInicalContrato',
                FechaFinalContrato = '$FechaFinalContrato',
                TipoContrato = '$TipoContrato',
                ValorBaseMes = '$ValorBaseMes',
                FechaInicalContrato = '$FechaInicalContrato',
                FechaFinalContrato = '$FechaFinalContrato',
                TipoServicio = '$TipoServicio',
                SalarioIntegral = '$SalarioIntegral',
                DuracionDias = '$DuracionDias',
                auxilio_transporte = '$auxilio_transporte',
                auxilio_extra = '$auxilio_extra',
                afp_entidad = '$afp_entidad',
                eps_entidad = '$eps_entidad',
                arl_riesgo = '$arl_riesgo',
                caja = '$caja',
                icbf = '$icbf',
                sena = '$sena',
                Forma_pago = '$Forma_pago',
                Banco = '$Banco',
                Tipo_cuenta = '$Tipo_Cuenta',
                Numero_Cuenta = '$Numero_Cuenta',
                estado = '$estado'
              WHERE ID = '$idEmpleado';";
  $resultado = $Link->query($consulta) or die ('Error al actualizar empleados: '. mysqli_error($Link));
  if ($resultado)
  {
    // Se actualiza los datos de usuario.
    $consulta2 = "UPDATE usuarios SET nombre = '$nombreCompleto', direccion = '$direccion', telefono = '$telefono' WHERE num_doc = '$numeroDocumento';";
    $resultado2 = $Link->query($consulta2) or die ('Error al actualizar datos de usuario: '. mysqli_error($Link));

    $rutaFoto = NULL;
    if (isset($_FILES["foto"])) {
      $rutaFoto = "../../upload/usuarios/E" . $idEmpleado . ".jpg";
      $subido = move_uploaded_file($_FILES["foto"]["tmp_name"], "../" . $rutaFoto);
      if ($subido){
        $consulta2 = " UPDATE empleados SET foto = '$rutaFoto' WHERE id = '$idEmpleado' ";
        $resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
      }
    }

    if ($tipoEmpleado == 2) {
      $manipulador_tipo_complemento   = json_decode($manipulador_tipo_complemento);
      $manipulador_municipio          = json_decode($manipulador_municipio);
      $manipulador_institucion        = json_decode($manipulador_institucion);
      $manipulador_sede               = json_decode($manipulador_sede);
      $manipulador_id                 = json_decode($manipulador_id);
      $manipulador_estado             = json_decode($manipulador_estado);
      foreach ($manipulador_tipo_complemento as $index => $mtc) {
        if (isset($manipulador_id[$index]->{$index})) {
          $consulta = "UPDATE `manipuladoras_sedes`
                        SET
                        `tipo_complem` = '".$mtc->{$index}."',
                        `cod_sede` = '".$manipulador_sede[$index]->{$index}."',
                        `estado` = '".$manipulador_estado[$index]->{$index}."'
                        WHERE `ID` = '".$manipulador_id[$index]->{$index}."';
                        ";
          $resultado = $Link->query($consulta) or die ('Error al insertar manipuladoras_sedes : '. mysqli_error($Link));
        } else {
          $consulta = "INSERT INTO `manipuladoras_sedes`
                      (
                        `documento`,
                        `tipo_complem`,
                        `cod_sede`,
                        `estado`
                      )
                      VALUES
                      (
                        '$numeroDocumento',
                        '".$mtc->{$index}."',
                        '".$manipulador_sede[$index]->{$index}."',
                        '1'
                      );
                      ";
          $resultado = $Link->query($consulta) or die ('Error al insertar manipuladoras_sedes : '. mysqli_error($Link));
        }
      }
    }


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
