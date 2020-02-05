<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];

//var_dump($_POST);

$anno = '';
$mes = '';
$dia = '';
$semana = '';
$municipio = '';
$institucion = '';
$sede = '';

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


// Busar el listado sedes para las que vamos a mostrar los totles.
$consulta = " SELECT cod_sede FROM sedes$periodoActual WHERE cod_mun_sede = $municipio ";
if($institucion != 'null'){
	$consulta .= " and cod_inst = $institucion ";
}
if($sede != 'null'){
	$consulta .= " and cod_sede = $sede ";
}
//echo $consulta;
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
$sedes = array();
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$sedes[] = $row["cod_sede"];
	}
}
//var_dump($sedes);

$cuerpo = "";
$ultimo_registro = 0;

foreach ($sedes as $codSede) {
	//var_dump($codSede);
	$consulta = "SELECT DISTINCT(s.cod_sede), s.nom_sede, s.cod_inst, s.nom_inst , (select count(DISTINCT f.num_doc) AS total from focalizacion$semana f WHERE f.cod_sede = s.cod_sede ) AS total ,(SELECT SUM(t.entregas) AS entregado FROM ( select IF(COUNT(f2.id)>2,1, COUNT(f2.id)) AS entregas, f2.* from biometria_reg br left join  biometria b on (br.usr_dispositivo_id=b.id_bioest and br.dispositivo_id=b.id_dispositivo) inner join focalizacion$semana f2 on (b.num_doc=f2.num_doc) WHERE b.cod_sede = \"$codSede\" AND  YEAR(br.fecha) = $anno AND MONTH(br.fecha) = $mes AND DAY(br.fecha) = $dia GROUP BY f2.id ) AS t WHERE t.cod_sede = s.cod_sede GROUP BY t.cod_sede ) AS entregado, ( SELECT min(br.fecha) FROM biometria_reg br LEFT JOIN biometria b ON (br.usr_dispositivo_id=b.id_bioest AND br.dispositivo_id=b.id_dispositivo) INNER JOIN focalizacion$semana f2 ON (b.num_doc=f2.num_doc) WHERE b.cod_sede = \"$codSede\" AND YEAR(br.fecha) = $anno AND MONTH(br.fecha) = $mes AND DAY(br.fecha) = $dia ) AS primer_registro FROM sedes$periodoActual s WHERE s.cod_sede = \"$codSede\"";

	$resultado = $Link->query($consulta) or die ('No se pudieron cargar los totales. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$t_cod_sede = $row["cod_sede"];
		$t_nom_sede = $row["nom_sede"];
		$t_cod_inst = $row["cod_inst"];
		$t_nom_inst = $row["nom_inst"];
		$t_total = $row["total"];
		$t_entregado = $row["entregado"];
		$t_primer_registro = $row["primer_registro"];
		$date = new DateTime($t_primer_registro);
		$t_primer_registro = date_format($date, 'g:i a');




		
		if($t_entregado == null || $t_entregado == ""){
			$t_entregado = 0;
		}

		if($t_total > 0){
			$porcentaje = ($t_entregado / $t_total) * 100;
		}
		else{
			$porcentaje = 0;
		}

		// Armado del html de cada sede
		$cuerpoSede = "<div class=\"sede sede-$t_cod_sede\"> <div class=\"sede-top\"> <div class=\"sede-left\"> <i class=\"fa fa-circle\"></i> </div> <h5>$t_nom_sede</h5> <h2 class=\"no-margins\"> <span class=\"entregado entregado-$t_cod_sede\">$t_entregado</span> / <span class=\"total\">$t_total</span></h2> </div> <div class=\"sede-bottom\"> <div class=\"sede-left\"> <div class=\"sede-hora-inicio\"> $t_primer_registro </div> </div> <div class=\"progress progress-mini\"> <div style=\"width: $porcentaje%;\" class=\"progress-bar\"></div> </div> </div> </div>";

		$cuerpo .= $cuerpoSede;
	}
	break;
}


/* Busqueda del ultimo registro, para tener una referencia de los registros nuevos. */


$consulta = " SELECT MAX(br.id) AS ultimo_registro FROM biometria_reg br
LEFT JOIN dispositivos d ON br.dispositivo_id = d.id
LEFT JOIN sedes19  s ON d.cod_sede = s.cod_sede
WHERE DAY(br.fecha) = $dia AND MONTH(br.fecha) = $mes AND YEAR(br.fecha) = $anno ";

if($institucion != "" && $institucion != "null"){
	$consulta .= " AND s.cod_inst = $institucion ";
}
if($sede != "" && $sede != "null"){
	$consulta .= " AND d.cod_sede = $sede ";
}

//echo "<br>$consulta<br>";

$resultado = $Link->query($consulta) or die ('Error al buscar el ultimo registro.'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$ultimo_registro = $row["ultimo_registro"];
}








if($cuerpo != ''){
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"cuerpo" => $cuerpo,
		"ultimo_registro" => $ultimo_registro
	);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha presentado un error."
	);
}
echo json_encode($resultadoAJAX);