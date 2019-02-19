<?php



require_once '../../../db/conexion.php';
echo "<option value=''>Seleccione uno</option>";
$usuario = '';
$bodegaOrigen = '';
if(isset($_POST['usuario'])){
	$usuario = $_POST['usuario'];
}
if(isset($_POST['bodegaOrigen'])){
	$bodegaOrigen = $_POST['bodegaOrigen'];
}

if($usuario != ''){
	$consulta=" select ID, NOMBRE from bodegas where ID in (select ub.COD_BODEGA_SALIDA from usuarios_bodegas ub inner join usuarios u on u.id = ub.USUARIO where u.num_doc = '$usuario' ) order by NOMBRE asc ";




  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
  	while($row = $resultado->fetch_assoc()){
  			$id = $row['ID'];
  			$nombre = $row['NOMBRE'];

  			if($bodegaOrigen == $id){
					echo "<option value='$id' selected >$nombre</option>";
  			}
  			else{
  				echo "<option value='$id'>$nombre</option>";
  			}

  	}
  }
}
