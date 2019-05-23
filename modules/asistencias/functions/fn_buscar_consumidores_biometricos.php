<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// Declaración de variables.
$data = [];
$semanaActual = "";
$sede = "";
$grado = "";
$grupo = "";
$fecha = date("Y-m-d H:i:s");
$anno = date("y"); 
$anno4d = date("Y");

$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);


if(isset($_POST["mes"]) && $_POST["mes"] != ""){
	$mes = mysqli_real_escape_string($Link, $_POST["mes"]);
}else{
	$mes = date("m");
}
if(isset($_POST["dia"]) && $_POST["dia"] != ""){
	$dia = mysqli_real_escape_string($Link, $_POST["dia"]);
}else{
	$dia = intval(date("d"));
}
$semanaActual = (isset($_POST["semanaActual"]) && $_POST["semanaActual"] != "") ? mysqli_real_escape_string($Link, $_POST["semanaActual"]) : "";

$sede = (isset($_POST["sede"]) && $_POST["sede"] != "") ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$nivel = (isset($_POST["nivel"]) && $_POST["nivel"] != "") ? mysqli_real_escape_string($Link, $_POST["nivel"]) : "";
$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);
$grado = (isset($_POST["grado"]) && $_POST["grado"] != "") ? mysqli_real_escape_string($Link, $_POST["grado"]) : "";
$grupo = (isset($_POST["grupo"]) && $_POST["grupo"] != "") ? mysqli_real_escape_string($Link, $_POST["grupo"]) : "";

// Actualizando la tabla de asistencia_det si el registro no esta insertar.

// Recorriendo las entregas de sipositivo para ver si están en la tabla de asistencia:
$consulta = " SELECT if(COUNT(f2.id)>2,2,COUNT(f2.id)) as entregas, f2.tipo_doc, f2.num_doc FROM focalizacion$semanaActual f2 LEFT JOIN biometria b ON f2.tipo_doc = b.tipo_doc AND f2.num_doc = b.num_doc LEFT JOIN biometria_reg br ON b.id_dispositivo = br.dispositivo_id AND b.id_bioest = br.usr_dispositivo_id WHERE id_dispositivo IS NOT NULL AND id_bioest IS NOT NULL AND year(br.fecha) = $anno4d AND month(br.fecha) = $mes AND day(br.fecha) = $dia AND f2.cod_sede = \"$sede\"GROUP BY f2.num_doc ";

// echo $consulta;
// echo "<br><br>";
$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
	while($row = $resultado->fetch_assoc()){
		$tipoDoc = $row["tipo_doc"];	
		$numDoc = $row["num_doc"];
		$entregas = $row["entregas"];
		$consulta2 = " SELECT * FROM asistencia_det$mes$anno WHERE tipo_doc = $tipoDoc AND num_doc = $numDoc AND mes = $mes AND semana = $semanaActual AND dia = $dia "; 
		$resultado2 = $Link->query($consulta2);
		if($resultado2->num_rows == 0){
			

			$consulta3 = " insert into asistencia_det$mes$anno (tipo_doc, num_doc, fecha, mes, semana, dia, asistencia, id_usuario, repite, consumio, repitio) values ( ";
			$consulta3 .= " \"$tipoDoc\", ";
			$consulta3 .= " \"$numDoc\", ";
			$consulta3 .= " \"$fecha\", ";
			$consulta3 .= " \"$mes\", ";
			$consulta3 .= " \"$semanaActual\", ";
			$consulta3 .= " \"$dia\", ";
			$consulta3 .= " \"1\", ";
			$consulta3 .= " \"$id_usuario\", ";
			$consulta3 .= " \"0\", ";
			$consulta3 .= " \"1\", ";
			if($entregas > 1){
				$consulta3 .= " \"1\" ";
			}else{
				$consulta3 .= " \"0\" ";
			}
			$consulta3 .= " ) "; 
			$resultado3 = $Link->query($consulta3);
			// echo $consulta3;
			// echo "<br><br>";
		}
	}
}








































// $consulta = "SELECT f.tipo_doc, f.num_doc, CONCAT(f.ape1, ' ', f.ape2, ' ', f.nom1, ' ', f.nom2) AS nombre, g.nombre AS grado, f.nom_grupo AS grupo, a.* FROM focalizacion$semanaActual f left join grados g on g.id = f.cod_grado left join Asistencia_det$mes$anno a on a.tipo_doc = f.tipo_doc and a.num_doc = f.num_doc WHERE 1 = 1 AND f.cod_sede = $sede and a.dia = \"$dia\" and a.mes = \"$mes\"ORDER BY f.cod_grado , f.nom_grupo , f.ape1 ";





$consulta = "SELECT f.tipo_doc, f.num_doc, CONCAT(f.ape1, ' ', f.ape2, ' ', f.nom1, ' ', f.nom2) AS nombre, g.nombre AS grado, f.nom_grupo AS grupo, a.asistencia, a.repite, a.consumio, a.repitio FROM focalizacion$semanaActual f LEFT JOIN grados g ON g.id = f.cod_grado left join Asistencia_det$mes$anno a on f.tipo_doc = a.tipo_doc and f.num_doc = a.num_doc and a.dia = $dia WHERE 1 = 1  ";

if($sede != "" ){
	$consulta .= " and f.cod_sede = $sede ";
}

if($nivel == 1 ){
	$consulta .= " and f.cod_grado < \"6\" ";
} else if($nivel == 2 ){
	$consulta .= " and f.cod_grado > \"5\" ";
}

if($grado != "" ){
	$consulta .= " and f.cod_grado = $grado ";
}

if($grupo != "" ){
	$consulta .= " and f.nom_grupo = $grupo ";
}

$consulta .= " order by f.cod_grado, f.nom_grupo, f.ape1 ";









//echo $consulta;

$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
  while($row = $resultado->fetch_assoc()) {
	$data[] = $row;
  }
}

$output = [
  'sEcho' => 1,
  'iTotalRecords' => count($data),
  'iTotalDisplayRecords' => count($data),
  'aaData' => $data
];

echo json_encode($output);