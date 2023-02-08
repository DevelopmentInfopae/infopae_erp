<option value="">Todos</option>
<?php
include '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];
require_once '../../../db/conexion.php';

$CodDepartamento = "";
$CodMunicipio = "";

$consulta = " SELECT CodDepartamento, CodMunicipio FROM parametros ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()) { 
		$CodDepartamento = $row['CodDepartamento'];
		$CodMunicipio = $row['CodMunicipio'];
	}
}

$consulta = " SELECT * FROM ubicacion WHERE 1 = 1";
if($CodDepartamento != 0){
	$consulta .= " AND ETC != 1 AND CodigoDANE LIKE '$CodDepartamento%' ";
}
if($CodMunicipio != 0){
	$consulta .= " AND  CodigoDANE = $CodMunicipio ";
}

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()) { ?>
	  	<option value="<?php echo $row['CodigoDANE']; ?>"><?php echo $row['Ciudad']; ?></option>
	<?php 
	}
}
