<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $idbiometria = $_POST['idbiometria'];

	$eliminar = "DELETE FROM biometria WHERE id = ".$idbiometria;
	if ($Link->query($eliminar) === true) {
		echo "1";
	} else {
		echo "0";
	}

 ?>