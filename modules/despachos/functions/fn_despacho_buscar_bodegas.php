<?php
require_once '../../../db/conexion.php';
echo "<option value=''>Seleccione uno</option>";
$usuario = '';
if(isset($_POST['usuario'])){
	$usuario = $_POST['usuario'];
}

if($usuario != ''){
	$consulta=" SELECT ID, NOMBRE 
				FROM bodegas 
				WHERE ID IN (
							SELECT ub.COD_BODEGA_SALIDA 
							FROM usuarios_bodegas ub 
							INNER JOIN usuarios u ON u.id = ub.USUARIO 
							WHERE u.num_doc = '$usuario' ) 
				ORDER BY NOMBRE ASC ";

  	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  	if($resultado->num_rows >= 1){
  		while($row = $resultado->fetch_assoc()){
  			$id = $row['ID'];
  			$nombre = $row['NOMBRE'];
  			echo "<option value='$id'>$nombre</option>";
  		}
  	}
}
