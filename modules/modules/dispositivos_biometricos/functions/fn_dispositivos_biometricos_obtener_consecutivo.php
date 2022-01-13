<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $iddispositivo = $_POST['iddispositivo'];

	$consulta = "SELECT id_bioest FROM biometria WHERE id_dispositivo = ".$iddispositivo." order by id_bioest desc limit 1";
	$resultado = $Link->query($consulta);
	if ($resultado->num_rows > 0) {
		if ($cons = $resultado->fetch_assoc() ) {
			$consecutivo = $cons['id_bioest']+1;
		}
		echo $consecutivo;
	}
 ?>