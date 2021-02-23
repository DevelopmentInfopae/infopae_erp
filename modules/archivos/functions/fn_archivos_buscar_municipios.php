<option value="">Todos</option>
<?php
include '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];
require_once '../../../db/conexion.php';

$CodDepartamento = "";
$CodMunicipio = "";

$consulta = " select * from parametros ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()) { 
		$CodDepartamento = $row['CodDepartamento'];
		$CodMunicipio = $row['CodMunicipio'];
	}
}

$consulta = " SELECT * FROM ubicacion WHERE 1 = 1 ";

if($CodDepartamento != ""){
	$consulta .= " AND CodigoDANE LIKE '$CodDepartamento%' ";
}

if($CodMunicipio != ""){
	$consulta .= " AND  CodigoDANE = $CodMunicipio ";
}

//echo "<br>$consulta<br>";

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()) { ?>
	  <option value="<?php echo $row['CodigoDANE']; ?>"><?php echo $row['Ciudad']; ?></option>
	<?php }
  }
