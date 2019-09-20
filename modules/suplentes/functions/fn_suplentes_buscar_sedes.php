<option value="">seleccione</option>
<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$periodo_actual = $_SESSION['periodoActual'];
	$codigo_municipio = $Link->real_escape_string($_POST['municipio']);

	$consulta_sedes = "SELECT DISTINCT cod_sede AS codigo, nom_sede AS nombre FROM sedes$periodo_actual WHERE cod_inst = '$codigo_municipio' ORDER BY nom_sede ASC";
	$respuesta_sedes = $Link->query($consulta_sedes) or die('Error al consultar las sedes: '. $Link->error);
	if ($respuesta_sedes->num_rows > 0)
	{
		while ($sede = $respuesta_sedes->fetch_assoc())
		{
?>
	  	<option value="<?= $sede['codigo'] ?>"><?= $sede['nombre'] ?></option>
<?php
		}
	}
