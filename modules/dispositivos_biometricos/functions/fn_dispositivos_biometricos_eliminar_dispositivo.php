<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $iddispositivo = $_POST['iddispositivo'];

  $consultaDatos = "SELECT sedes".$_SESSION['periodoActual'].".nom_sede, dispositivos.* FROM dispositivos INNER JOIN sedes".$_SESSION['periodoActual']." ON sedes".$_SESSION['periodoActual'].".cod_sede = dispositivos.cod_sede WHERE dispositivos.id = ".$iddispositivo;
  $resultadoDatos = $Link->query($consultaDatos);
  if ($resultadoDatos->num_rows > 0) {
  	$info = $resultadoDatos->fetch_assoc();
  }

	$eliminar = "DELETE FROM dispositivos WHERE id = ".$iddispositivo;
	if ($Link->query($eliminar) === true) {
		$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '45', 'Eliminó dispositivo biométrico de la sede <strong>".$info['nom_sede']."</strong> con número de serial <strong>".$info['num_serial']."</strong>')";
		$Link->query($sqlBitacora);

		echo "1";
	} else {
		echo "0";
	}

 ?>