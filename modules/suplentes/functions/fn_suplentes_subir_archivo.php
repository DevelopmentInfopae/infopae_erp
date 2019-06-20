<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$mes = isset($_POST['mes']) ? $Link->real_escape_string($_POST['mes']) : '';
$semana = isset($_POST['semana']) ? $Link->real_escape_string($_POST['semana']) : '';

// Consulta que valida si ya existe la tabla y datos de suplentes para la semana seleccionada.
$consulta_validar_semana = "SHOW TABLES LIKE 'suplentes$semana'";
$respuesta_validar_semana = $Link->query($consulta_validar_semana) or die("Error al consultar tabla suplentes$semana: ". $Link->error);
if ($respuesta_validar_semana->num_rows > 0)
{
	$respuesta_ajax = [
		'success' => 0,
		'message' => 'Ya se encuentran registrado datos de suplentes para la semana seleccionada.'
	];
	echo json_encode($respuesta_ajax);
	exit();
}

// Condición que verifica si existe el archivo de suplentes.
if (isset($_FILES['archivo_suplentes']) && empty($_FILES['archivo_suplentes']['tmp_name']))
{
	$respuesta_ajax = [
		'success' => 2,
		'message' => 'No se adjunto el archivo de suplentes. Por favor seleccione un archivo para continuar.'
	];
	echo json_encode($respuesta_ajax);
	exit();
}

// Consulta para obtener los estudiantes focalizados.
$consulta_estudiantes_focalizados = "SELECT num_doc FROM focalizacion$semana";
$respuesta_estudiantes_focalizados = $Link->query($consulta_estudiantes_focalizados) or die($Link->error);
if($respuesta_estudiantes_focalizados->num_rows > 0)
{
	while($estudiantes_focalizado = $respuesta_estudiantes_focalizados->fetch_assoc())
	{
		$focalizados[] = $estudiantes_focalizado["num_doc"];
	}
}

$archivo_suplentes = fopen($_FILES['archivo_suplentes']['tmp_name'], 'r');
$separador_registros = (count(fgetcsv($archivo_suplentes, null, ",")) > 1) ? "," : ";";

// Ciclo para validar los datos del archivo a importar.
$fila = 1;
while(($registro = fgetcsv($archivo_suplentes, null, $separador_registros)) == TRUE)
{
	if(empty($registro[0]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Tipo de documento no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[1]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Número de documento no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if (in_array($registro[1], $focalizados))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Número de documento ingresado ya se encuentra focalizado.<br><strong>Registro número: '. $registro[1]
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[7]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Género no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if($registro[13] == '')
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El campo código de estrato no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[15]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El campo discapacidad no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[16]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El campo étnia no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[17]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El campo Resguardo no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[18]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El campo población victima no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[21]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Código de la institución no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[22]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Código de la sede no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[23]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Código del municipio de la institución no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[24]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Código del municipio de la sede no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if($registro[27] == '')
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Código del grado no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila . empty($registro[27])
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[29]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El Código de jornada no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[32]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El campo edad no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	if(empty($registro[33]))
	{
		$respuesta_ajax = [
			'success' => 0,
			'message' => 'El campo residencia no puede estar <strong>vacío</strong>.<br><strong>Registro número: '. $fila
		];
		echo json_encode($respuesta_ajax);
		exit();
	}

	$fila++;
}

$consulta_crear_tabla_suplente = "CREATE TABLE `suplentes$semana` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`tipo_doc` INT(11) NULL DEFAULT '0',
`num_doc` VARCHAR(24) NULL DEFAULT '-',
`tipo_doc_nom` VARCHAR(10) NULL DEFAULT NULL,
`ape1` VARCHAR(50) NULL DEFAULT '-',
`ape2` VARCHAR(50) NULL DEFAULT '-',
`nom1` VARCHAR(50) NULL DEFAULT '-',
`nom2` VARCHAR(50) NULL DEFAULT '-',
`genero` VARCHAR(1) NULL DEFAULT '-',
`dir_res` VARCHAR(200) NULL DEFAULT NULL,
`cod_mun_res` INT(11) NULL DEFAULT NULL,
`telefono` VARCHAR(50) NULL DEFAULT NULL,
`cod_mun_nac` INT(11) NULL DEFAULT '0',
`fecha_nac` DATE NULL DEFAULT NULL,
`cod_estrato` INT(11) NULL DEFAULT '0',
`sisben` DECIMAL(5,3) NULL DEFAULT '0.000',
`cod_discap` INT(11) NULL DEFAULT '0',
`etnia` INT(11) NULL DEFAULT '0',
`resguardo` INT(11) NULL DEFAULT '0',
`cod_pob_victima` INT(11) NULL DEFAULT '0',
`des_dept_nom` INT(11) NULL DEFAULT NULL,
`nom_mun_desp` INT(11) NULL DEFAULT NULL,
`cod_sede` BIGINT(20) NULL DEFAULT '0',
`cod_inst` BIGINT(20) NULL DEFAULT '0',
`cod_mun_inst` INT(11) NULL DEFAULT NULL,
`cod_mun_sede` INT(11) NULL DEFAULT NULL,
`nom_sede` VARCHAR(200) NULL DEFAULT NULL,
`nom_inst` VARCHAR(200) NULL DEFAULT NULL,
`cod_grado` INT(11) NULL DEFAULT '0',
`nom_grupo` INT(11) NULL DEFAULT NULL,
`cod_jorn_est` INT(11) UNSIGNED NULL DEFAULT '0',
`estado_est` VARCHAR(20) NULL DEFAULT NULL,
`repitente` VARCHAR(2) NULL DEFAULT NULL,
`edad` VARCHAR(4) NULL DEFAULT '0',
`zona_res_est` INT(11) NULL DEFAULT '0',
`id_disp_est` INT(11) UNSIGNED NULL DEFAULT '0',
`TipoValidacion` VARCHAR(50) NULL DEFAULT '',
`activo` TINYINT(1) UNSIGNED NULL DEFAULT '0',
PRIMARY KEY (`id`),
INDEX `Acel_est1` (`num_doc`, `cod_jorn_est`, `cod_grado`, `cod_pob_victima`, `cod_inst`, `cod_discap`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;";
$respuesta_crear_tabla_suplente = $Link->query($consulta_crear_tabla_suplente) or die("Error al crear tabla de suplente$semana: ". $Link->error);
if (! $respuesta_crear_tabla_suplente)
{
	$respuesta_ajax = [
		'success' => 0,
		'message' => 'No fue posible crear la tabla para la importación de datos.'
	];
	echo json_encode($respuesta_ajax);
	exit();
}

// Ciclo para generar la cadena de texto para la consulta de inserción.
$consulta_insertar_suplentes = "INSERT INTO suplentes$semana (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente, edad, zona_res_est, id_disp_est, TipoValidacion, activo) VALUES ";

$archivo_suplentes = fopen($_FILES['archivo_suplentes']['tmp_name'], 'r');
$separador_registros = (count(fgetcsv($archivo_suplentes, null, ",")) > 1) ? "," : ";";
while(($registro = fgetcsv($archivo_suplentes, null, $separador_registros)) == TRUE)
{
	$tipo_documento = $registro[0];
	$numero_documento = $registro[1];
	$tipo_documento_nombre = $registro[2];
	$primer_apellido = utf8_decode($registro[3]);
	$segundo_apellido = utf8_decode($registro[4]);
	$primer_nombre = utf8_decode($registro[5]);
	$segundo_nombre = utf8_decode($registro[6]);
	$genero = $registro[7];
	$direccion_residencia = $registro[8];
	$codigo_municipio_residencia = $registro[9];
	$telefono = $registro[10];
	$codigo_municipio_nacimiento= $registro[11];
	$fecha_nacimiento = $registro[12];
	$codigo_estrato = $registro[13];
	$sisben = $registro[14];
	$codigo_discapacidad = $registro[15];
	$etnia = $registro[16];
	$resguardo = $registro[17];
	$poblacion_victima = $registro[18];
	$nombre_departamento = $registro[19];
	$nombre_municipio = $registro[20];
	$codigo_institucion = $registro[21];
	$codigo_sede = $registro[22];
	$codigo_municipio_institucion = $registro[23];
	$codigo_municipio_sede = $registro[24];
	$nombre_sede = utf8_decode($registro[25]);
	$nombre_institucion = utf8_decode($registro[26]);
	$grado = $registro[27];
	$grupo = $registro[28];
	$jornada = $registro[29];
	$estado = $registro[30];
	$repitente = $registro[31];
	$edad = $registro[32];
	$zona_residencia = $registro[33];
	$discapacidad = $registro[34];
	$tipo_validacion = $registro[35];
	$activo = $registro[36];

	$consulta_insertar_suplentes .= "('$tipo_documento', '$numero_documento', '$tipo_documento_nombre', '$primer_apellido', '$segundo_apellido', '$primer_nombre', '$segundo_nombre', '$genero', '$direccion_residencia', '$codigo_municipio_residencia', '$telefono', '$codigo_municipio_nacimiento', '$fecha_nacimiento', '$codigo_estrato', '$sisben', '$codigo_discapacidad', '$etnia', '$resguardo', '$poblacion_victima', '$nombre_departamento', '$nombre_municipio', '$codigo_sede', '$codigo_institucion', '$codigo_municipio_institucion', '$codigo_municipio_sede', '$nombre_sede', '$nombre_institucion', '$grado', '$grupo', '$jornada', '$estado', '$repitente', '$edad', '$zona_residencia', '$discapacidad', '$tipo_validacion', '$activo'), ";
}

$respuesta_insertar_suplentes = $Link->query(trim($consulta_insertar_suplentes, ', ')) or die('Error al insertar datos de suplentes: '. $Link->error);
if ($respuesta_insertar_suplentes === FALSE)
{
	// Si los datos no se insertaron entonces se elimina nuevamente la tabla creada.
	$consulta_borrar_tabla_suplente = "DROP TABLE suplentes$semana";
	$respuesta_borrar_tabla_suplente = $Link->query($consulta_borrar_tabla_suplente);

	$respuesta_ajax = [
		'success' => 1,
		'message' => 'No fue posible importar los datos. Por favor intente nuevamente.'
	];
	echo json_encode($respuesta_ajax);
	exit();
}

$respuesta_ajax = [
	'success' => 1,
	'message' => 'Los datos fueron importados exitosamente.'
];
echo json_encode($respuesta_ajax);
