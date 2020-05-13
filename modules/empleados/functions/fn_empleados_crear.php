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
  // Nuevos campos
  $tipoEmpleado = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : 0;

  $manipulador_tipo_complemento = (isset($_POST['manipulador_tipo_complemento']) && $_POST['manipulador_tipo_complemento'] != '') ? ($_POST['manipulador_tipo_complemento']) : '';
  $manipulador_municipio = (isset($_POST['manipulador_municipio']) && $_POST['manipulador_municipio'] != '') ? ($_POST['manipulador_municipio']) : '';
  $manipulador_institucion = (isset($_POST['manipulador_institucion']) && $_POST['manipulador_institucion'] != '') ? ($_POST['manipulador_institucion']) : '';
  $manipulador_sede = (isset($_POST['manipulador_sede']) && $_POST['manipulador_sede'] != '') ? ($_POST['manipulador_sede']) : '';

  $estado = (isset($_POST['estado']) && $_POST['estado'] != '') ? mysqli_real_escape_string($Link, $_POST['estado']) : 0;
  $crear_usuario = (isset($_POST['crear_usuario']) && $_POST['crear_usuario'] != 'false') ? mysqli_real_escape_string($Link, $_POST['crear_usuario']) : false;

  $TipoContrato = (isset($_POST['TipoContrato']) && $_POST['TipoContrato'] != '') ? mysqli_real_escape_string($Link, $_POST['TipoContrato']) : '';
  $ValorBaseMes = (isset($_POST['ValorBaseMes']) && $_POST['ValorBaseMes'] != '') ? mysqli_real_escape_string($Link, $_POST['ValorBaseMes']) : '';
  $FechaInicalContrato = (isset($_POST['FechaInicalContrato']) && $_POST['FechaInicalContrato'] != '') ? mysqli_real_escape_string($Link, $_POST['FechaInicalContrato']) : '';
  $FechaFinalContrato = (isset($_POST['FechaFinalContrato']) && $_POST['FechaFinalContrato'] != '') ? mysqli_real_escape_string($Link, $_POST['FechaFinalContrato']) : '';

$TipoServicio = (isset($_POST['TipoServicio']) && $_POST['TipoServicio'] != '') ? mysqli_real_escape_string($Link, $_POST['TipoServicio']) : null;
$SalarioIntegral = (isset($_POST['SalarioIntegral']) && $_POST['SalarioIntegral'] != '') ? mysqli_real_escape_string($Link, $_POST['SalarioIntegral']) : null;  
$DuracionDias = (isset($_POST['DuracionDias']) && $_POST['DuracionDias'] != '') ? mysqli_real_escape_string($Link, $_POST['DuracionDias']) : null;
$auxilio_transporte = (isset($_POST['auxilio_transporte']) && $_POST['auxilio_transporte'] != '') ? mysqli_real_escape_string($Link, $_POST['auxilio_transporte']) : null; 
$auxilio_extra = (isset($_POST['auxilio_extra']) && $_POST['auxilio_extra'] != '') ? mysqli_real_escape_string($Link, $_POST['auxilio_extra']) : 0; 
$afp_entidad = (isset($_POST['afp_entidad']) && $_POST['afp_entidad'] != '') ? mysqli_real_escape_string($Link, $_POST['afp_entidad']) : null; 
$eps_entidad = (isset($_POST['eps_entidad']) && $_POST['eps_entidad'] != '') ? mysqli_real_escape_string($Link, $_POST['eps_entidad']) : null; 
$arl_riesgo = (isset($_POST['arl_riesgo']) && $_POST['arl_riesgo'] != '') ? mysqli_real_escape_string($Link, $_POST['arl_riesgo']) : null; 
$caja = (isset($_POST['caja']) && $_POST['caja'] != '') ? mysqli_real_escape_string($Link, $_POST['caja']) : null;
$icbf = (isset($_POST['icbf']) && $_POST['icbf'] != '') ? mysqli_real_escape_string($Link, $_POST['icbf']) : null; 
$sena = (isset($_POST['sena']) && $_POST['sena'] != '') ? mysqli_real_escape_string($Link, $_POST['sena']) : null; 
$Forma_pago = (isset($_POST['Forma_pago']) && $_POST['Forma_pago'] != '') ? mysqli_real_escape_string($Link, $_POST['Forma_pago']) : 0; 
$Banco = (isset($_POST['Banco']) && $_POST['Banco'] != '') ? mysqli_real_escape_string($Link, $_POST['Banco']) : null; 
$Tipo_Cuenta = (isset($_POST['Tipo_cuenta']) && $_POST['Tipo_cuenta'] != '') ? mysqli_real_escape_string($Link, $_POST['Tipo_cuenta']) : 0; 
$Numero_Cuenta = (isset($_POST['Numero_Cuenta']) && $_POST['Numero_Cuenta'] != '') ? mysqli_real_escape_string($Link, $_POST['Numero_Cuenta']) : null;

  $clave = sha1(strtoupper(substr($primerNombre, 0, 1)) . $numeroDocumento);
  // exit(var_dump($crear_usuario));
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

if (isset($_FILES["foto"]["name"])){
  $dimensiones = getimagesize($_FILES["foto"]["tmp_name"]);

  // Valida el ratio/aspecto permitido
  if ($dimensiones[0] != $dimensiones[1])
  {
    $resultadoAJAX = array(
      "estado" => 0,
      "mensaje" => "Por favor ingresar una imagen de ratio aspecto 1:1 o cuadrada tipo documento."
    );
    exit(json_encode($respuestaAJAX));
  } else if($_FILES["foto"]["size"] > 5120000){ // Valida el tamaño permitido
    $resultadoAJAX = array(
      "estado" => 0,
      "mensaje" => "La imagen supera el tamaño permitido 5 MegaBytes. Por favor ingresar una imagen de igual o menor tamaño"
    );
    exit(json_encode($respuestaAJAX));
  } else if($_FILES["foto"]["type"] != "image/jpg" && $_FILES["foto"]["type"] != "image/jpeg" && $_FILES["foto"]["type"] != "image/png"){ // Valida tipo de imagen permitido
    // Se ejecuta la consulta para guardar el usuario.
    $resultadoAJAX = array(
      "estado" => 0,
      "mensaje" => "La extensión del la imagen no es la permitida. Tipo de archivo permitido: .jpg, .jpeg"
    );
    exit(json_encode($respuestaAJAX));
  }
}

  $consulta = "INSERT INTO empleados  (Nitcc, Nombre, Direccion, Telefono1, Telefono2, FechaNacimiento, LugarNacimiento, Sexo, LibretaMilitar, TipoSangre, EstadoCivil, Ciudad, Profesion, Barrio, Email, NivelEstudio, PrimerNombre, SegundoNombre, PrimerApellido, SegundoApellido, Cargo, TallaPantalon, TallaCamisa, NumeroCalzado, TipoDoc, FechaCreacion, Contrato, Tipo, TipoContrato, ValorBaseMes, FechaInicalContrato, FechaFinalContrato, TipoServicio, SalarioIntegral, DuracionDias, auxilio_transporte, auxilio_extra, afp_entidad, eps_entidad, arl_riesgo, caja, icbf, sena, Forma_pago, Banco, Tipo_cuenta, Numero_Cuenta, Estado)
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
                '$numeroContrato',
                '$tipoEmpleado',
                '$TipoContrato',
                '$ValorBaseMes',
                '$FechaInicalContrato',
                '$FechaFinalContrato',
                '$TipoServicio',
                '$SalarioIntegral',
                '$DuracionDias',
                '$auxilio_transporte',
                '$auxilio_extra',
                '$afp_entidad',
                '$eps_entidad',
                '$arl_riesgo',
                '$caja',
                '$icbf',
                '$sena',
                '$Forma_pago',
                '$Banco',
                '$Tipo_Cuenta',
                '$Numero_Cuenta',
                '$estado'
              )";
  $resultado = $Link->query($consulta) or die ('Error al insertar empleados: '. mysqli_error($Link));
  if ($resultado)
  {
    $id = $Link->insert_id;
    $rutaFoto = NULL;
    if (isset($_FILES["foto"])) {
      $rutaFoto = "../../upload/usuarios/E" . $id . ".jpg";
      $subido = move_uploaded_file($_FILES["foto"]["tmp_name"], "../" . $rutaFoto);
      if ($subido){
        $consulta2 = " UPDATE empleados SET foto = '$rutaFoto' WHERE id = '$id' ";
        $resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
      }
    }

    if ($tipoEmpleado == 2) {
      $manipulador_tipo_complemento   = json_decode($manipulador_tipo_complemento);
      $manipulador_municipio          = json_decode($manipulador_municipio);
      $manipulador_institucion        = json_decode($manipulador_institucion);
      $manipulador_sede               = json_decode($manipulador_sede);
      foreach ($manipulador_tipo_complemento as $index => $mtc) {
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

    if ($crear_usuario) {
      $consulta = "INSERT INTO `usuarios`
                  (
                    `nombre`,
                    `clave`,
                    `direccion`,
                    `cod_mun`,
                    `telefono`,
                    `foto`,
                    `email`,
                    `id_perfil`,
                    `nueva_clave`,
                    `num_doc`,
                    `Tipo_Usuario`,
                    `Estado`
                  )
                  VALUES
                  (
                    '$nombreCompleto',
                    '$clave',
                    '$direccion',
                    '$municipioResidencia',
                    '$telefono',
                    '$rutaFoto',
                    '$email',
                    '2',
                    '0',
                    '$numeroDocumento',
                    'Empleado',
                    '1'
                  );
                  ";
      $resultado = $Link->query($consulta) or die ('Error al insertar usuario: '. mysqli_error($Link));
    }

    // Se actualiza los datos de usuario.
    // $consulta2 = "UPDATE usuarios SET nombre = '$nombreCompleto', direccion = '$direccion', telefono = '$telefono' WHERE num_doc = '$numeroDocumento';";
    // $resultado2 = $Link->query($consulta2) or die ('Error al actualizar datos de usuario: '. mysqli_error($Link));

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
