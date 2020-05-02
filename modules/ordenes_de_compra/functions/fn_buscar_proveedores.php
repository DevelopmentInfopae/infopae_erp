<option value="">Seleccione Uno</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
require_once '../../../autentication.php';

$periodoActual = $_SESSION['periodoActual'];
$tipoAlimento = (isset($_POST['tipoAlimento'])) ? $_POST['tipoAlimento'] : '';
$consulta = "SELECT * FROM proveedores p WHERE FIND_IN_SET($tipoAlimento, p.TipoAlimento)";
$consulta .= " ORDER BY p.Nombrecomercial ASC";


//echo "<br><br>$consulta<br><br>";

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
while($row = $resultado->fetch_assoc()) {
?>
	<option value="<?php echo $row['Nitcc']; ?>"><?php echo $row['Nombrecomercial']; ?></option>
<?php
}
}
