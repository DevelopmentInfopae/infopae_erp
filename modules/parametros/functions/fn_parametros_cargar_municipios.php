<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	$id_departamento = (isset($_POST["Id_departamento"]) && $_POST["Id_departamento"] != '') ? mysqli_real_escape_string($Link, $_POST["Id_departamento"]) : '';
?>
	<option value="">Seleccione uno</option>
<?php
	if ($id_departamento != '') {
		$consulta1 = "SELECT * FROM `ubicacion` WHERE CodigoDANE LIKE '%".$id_departamento."%'";
		$resultado1 = $Link->query($consulta1) or die ("Unable to execute query.". mysql_error($Link));
		if ($resultado1->num_rows > 0) {
    		while($row = $resultado1->fetch_assoc()) {
?>
      		<option value="<?php echo $row['CodigoDANE']; ?>"><?php echo $row['Ciudad']; ?></option>
<?php
			}
		}
	}