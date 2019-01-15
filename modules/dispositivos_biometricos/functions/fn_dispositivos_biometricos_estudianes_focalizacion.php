<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $focalizacion = $_POST['focalizacion'];
  $cod_sede = $_POST['cod_sede'];
  $sql = "";
  foreach ($focalizacion as $foc) {
  		$sql.="(SELECT tdoc.nombre AS tdocnom, grados.nombre AS grado_nombre,tipo_doc, num_doc, nom1, ape1, cod_sede, cod_grado FROM ".$foc." INNER JOIN tipodocumento as tdoc ON tdoc.id = tipo_doc INNER JOIN grados ON grados.id = ".$foc.".cod_grado WHERE cod_sede = '".$cod_sede."' AND NOT EXISTS (SELECT num_doc FROM biometria WHERE biometria.num_doc = ".$foc.".num_doc))";
  		$sql.=" UNION ";
  }

  $sql = trim($sql, " UNION");

  echo $sql;

	$resultado = $Link->query(trim($sql, " UNION"));
	$table="";
	if ($resultado->num_rows > 0) {
		$cnt = 0;
		while ($datos = $resultado->fetch_assoc()) { 
			$cnt++;
			$table.= "<tr>";
			$table.= "<td>".$datos['tdocnom']."<input type='hidden' name='tipo_doc[".$datos['num_doc']."]' value='".$datos['tipo_doc']."'></td>";
			$table.= "<td>".$datos['num_doc']."<input type='hidden' name='num_doc[]' value='".$datos['num_doc']."'></td>";
			$table.= "<td>".$datos['nom1']." ".$datos['ape1']."</td>";
			$table.= "<td>".$datos['grado_nombre']."</td>";
			$table.= "<td><input type='number' name='id_bioest[".$datos['num_doc']."]' id='id_bioest' onchange='validaBioEst(this, ".$cnt.", 1)' class='form-control'><br><em style='color: #cc5965; font-size: 13px; display: none;' id='existeBioEst".$cnt."'>Este id ya ha sido asignado.</em></td>";
			$table.= "</tr>";
		 }
		 echo $table;
	}
 ?>