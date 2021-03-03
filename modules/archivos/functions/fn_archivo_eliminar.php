<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';
include_once 'funciones.php';

//var_dump($_POST);

if(isset($_POST['id']) && $_POST['id'] != ''){
	$id = $_POST['id'];

	$Link = new mysqli($Hostname, $Username, $Password, $Database);
	if ($Link->connect_errno) {
		echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$Link->set_charset("utf8");

	$consulta = " select * from mod_archivos where id = '$id' ";
	$resultado = $Link->query($consulta) or die ('Error en la consulta que busca el archivo por el id. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();

		//$rutaFisica = $rootUrl.'/'.$row['ruta'];
		$rutaFisica = $infopaeData.$row['ruta'];

		if(file_exists($rutaFisica)){
			if(unlink($rutaFisica)){
				$consulta = " delete from mod_archivos where id = '$id' ";
				$Link->query($consulta) or die ('Error en la consulta para borrar un registro de la tabla archivo. '. mysqli_error($Link));
				if($Link->affected_rows > 0){
					echo '1';
				}
			}
		}
		else{
			$consulta = " delete from mod_archivos where id = '$id' ";
			$Link->query($consulta) or die ('Error en la consulta para borrar un registro de la tabla archivo. '. mysqli_error($Link));
			if($Link->affected_rows > 0){
				echo '1';
			}
		}









	}
}
