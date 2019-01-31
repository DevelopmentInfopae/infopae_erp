<option value="">Seleccione...</option>
<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$municipio = $_POST['cod_mun'];

	$consulta = "SELECT * FROM instituciones WHERE cod_mun = '".$municipio."' ORDER BY nom_inst ASC;";
	$resultado = $Link->query($consulta);
	if ($resultado->num_rows > 0) {
		while ($mun = $resultado->fetch_assoc()) { ?>
			<option value="<?php echo $mun['codigo_inst']; ?>"><?php echo $mun['nom_inst']; ?></option>
	<?php
		}
	}