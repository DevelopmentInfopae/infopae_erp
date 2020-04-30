<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];

//var_dump($_POST);

$ultimoRegistro = 0;
$anno = '';
$mes = '';
$dia = '';
$semana = '';
$municipio = '';
$institucion = '';
$sede = '';
$registrosNuevos = array();
$cantRegistrosNuevos = 0;

if(isset($_POST['ultimoRegistro']) && $_POST['ultimoRegistro'] != ''){
	$ultimoRegistro = mysqli_real_escape_string($Link, $_POST['ultimoRegistro']);
}
if(isset($_POST['anno']) && $_POST['anno'] != ''){
	$anno = mysqli_real_escape_string($Link, $_POST['anno']);
}
if(isset($_POST['mes']) && $_POST['mes'] != ''){
	$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}
if(isset($_POST['dia']) && $_POST['dia'] != ''){
	$dia = mysqli_real_escape_string($Link, $_POST['dia']);
}
if(isset($_POST['semana']) && $_POST['semana'] != ''){
	$semana = mysqli_real_escape_string($Link, $_POST['semana']);
}
if(isset($_POST['municipio']) && $_POST['municipio'] != ''){
	$municipio = mysqli_real_escape_string($Link, $_POST['municipio']);
}
if(isset($_POST['institucion']) && $_POST['institucion'] != ''){
	$institucion = mysqli_real_escape_string($Link, $_POST['institucion']);
}
if(isset($_POST['sede']) && $_POST['sede'] != ''){
	$sede = mysqli_real_escape_string($Link, $_POST['sede']);
}

/* Busqueda del ultimo registro, para compararlo con el que tenmos de parametro */

$consulta = " SELECT MAX(br.id) AS ultimo_registro FROM biometria_reg br
LEFT JOIN dispositivos d ON br.dispositivo_id = d.id
LEFT JOIN sedes$periodoActual  s ON d.cod_sede = s.cod_sede
WHERE DAY(br.fecha) = $dia AND MONTH(br.fecha) = $mes AND YEAR(br.fecha) = $anno AND br.id > $ultimoRegistro ";


if($institucion != "" && $institucion != "null"){
	$consulta .= " AND s.cod_inst = $institucion ";
}
if($sede != "" && $sede != "null"){
	$consulta .= " AND d.cod_sede = $sede ";
}

//echo "<br><br>$consulta<br><br>";
//echo "<br>$consulta<br>";

$resultado = $Link->query($consulta) or die ('Error al buscar el ultimo registro.'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$registrosNuevos[] = $row["ultimo_registro"];
	}
}

//var_dump($registrosNuevos);

if($registrosNuevos[0] != null){
	//echo "<br><br>$consulta<br><br>";
	$idEntrega = $registrosNuevos[0];

	$consulta = " SELECT
	br.fecha AS fecha,
	f.nom1 AS nombre,
	f.ape1 AS apellido,
	f.Tipo_complemento AS complemento,
	s.nom_sede,
	d.tipo AS validacion,
	b.cod_sede
	
	FROM biometria_reg br 
	LEFT JOIN biometria b ON b.id_bioest = br.usr_dispositivo_id AND b.id_dispositivo = br.dispositivo_id
	LEFT JOIN dispositivos d ON br.dispositivo_id = d.id
	LEFT JOIN focalizacion$semana f ON f.tipo_doc = b.tipo_doc AND f.num_doc = b.num_doc
	LEFT JOIN sedes$periodoActual s ON b.cod_sede = s.cod_sede
	WHERE br.id = $idEntrega ";

	//echo "<br>$consulta<br>";




	$resultado = $Link->query($consulta) or die ('Error al buscar información del ultimo registro.'. mysqli_error($Link));
	$cantRegistrosNuevos = $resultado->num_rows;
	if($resultado->num_rows >= 1){
		while($row = $resultado->fetch_assoc()){
			$nombre = $row['nombre'];
			$apellido = $row['apellido'];
			$complemento = $row['complemento'];
			$nomSede = $row['nom_sede'];
			$validacion = $row['validacion'];
			$codSede = $row['cod_sede'];
			$fecha = $row['fecha'];
			$date = new DateTime($fecha);
			$fecha = date_format($date, 'H:i:s');


			$claseValidacion = "";
			if($validacion == "Lector Huella Dactilar" || $validacion == "Huella Dactilar"){
				$claseValidacion = "huellaDactilar";	
			} else if($validacion == "Lector Radiofrecuencia" || $validacion == "Radiofrecuencia" || $validacion == "Lector RFID"){
				$claseValidacion = "radiofrecuencia";	
			} else if($validacion == "Lector Reconocimiento facial" || $validacion == "Reconocimiento facial" || $validacion == "Lector Facial"){
				$claseValidacion = "reconocimientoFacial";	
			}

			

			// Armado de cuerpo información de la entrega.
			
			$cuerpo = " <div class=\"entrega\"> <i class=\"fa fa-check-circle\"></i> <span class=\"hora-estudiante\">$fecha</span> <div class=\"estudiante\"> <h2><span class=\"estudiante--nombre\">$nombre $apellido</span> </h2> <span class=\"estudiante--sede\">$nomSede</span> </div> <div class=\"estudiante--validacion $claseValidacion\">$validacion</div> </div> ";










		}
	}
}

if($cantRegistrosNuevos > 0){
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"cantRegistrosNuevos" => $cantRegistrosNuevos,
		"cuerpo" => $cuerpo,
		"codSede" => $codSede,
		"ultimoRegistro" => $idEntrega
	);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha cargado con exito.",
		"cantRegistrosNuevos" => $cantRegistrosNuevos
	);
}
echo json_encode($resultadoAJAX);