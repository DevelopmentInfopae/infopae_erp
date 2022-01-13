<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$focalizacion = $_POST['semana_turno'];
$cod_sede = $_POST['cod_sede'];
$grado = $_POST['grado'];
$grupo = $_POST['grupo'];

$concatGrupo = " ";

if ($grupo != "" ) {
	$concatGrupo = "AND " .$focalizacion. ".nom_grupo = " .$grupo."";  
}

$sql = "";
$sql.="(SELECT nom_grupo, tdoc.nombre AS tdocnom, convert(grados.id, signed) as grado, grados.nombre AS grado_nombre,tipo_doc, num_doc, nom1, ape1, cod_sede, cod_grado FROM ".$focalizacion." INNER JOIN tipodocumento as tdoc ON tdoc.id = tipo_doc INNER JOIN grados ON grados.id = ".$focalizacion.".cod_grado WHERE cod_sede = '".$cod_sede."' AND ".$focalizacion.".cod_grado = ".$grado."  " .$concatGrupo."  AND NOT EXISTS (SELECT num_doc FROM biometria WHERE biometria.num_doc = ".$focalizacion.".num_doc))";
// echo $sql;	

$resultado = $Link->query($sql) or die('Error al consultar la focalizacion' . mysqli_error($Link));
$table="";
	if ($resultado->num_rows > 0) {
		$cnt = 0;
		while ($datos = $resultado->fetch_assoc()) { 
			$cnt++;
			$table.= "<tr>"; 
			$table.= "<td>".$datos['tdocnom']."<input type='hidden' name='tipo_doc[".$datos['num_doc']."]' value='".$datos['tipo_doc']."'></td>";
			// $table.= "<input type='hidden' name='idBiometria[" .$datos['num_doc']."]' value=" .$datos['num_doc']."></td>";
			$table.= "<td>".$datos['num_doc']."<input type='hidden' name='num_doc[]' value='".$datos['num_doc']."'></td>";
			$table.= "<td>".$datos['nom1']." ".$datos['ape1']."</td>";
			// $table.= "<td>".$datos['grado']."</td>";
			$table.= "<td>".$datos['grado_nombre']."</td>";
			$table.= "<td>".$datos['nom_grupo']."</td>";
			$table.= "<td><input type='number' min='1' step = '1' name='id_bioest[".$datos['num_doc']."]' id='id_bioest' data-type= 2 onchange='validaBioEst(this, ".$cnt.", 1, this.value)' class='form-control'><br><em style='color: #cc5965; font-size: 13px; display: none;' id='existeBioEst".$cnt."'>Este id ya ha sido asignado.</em></td>";
			$table.= "</tr>";
		 }
		 echo $table;
	}
 ?>