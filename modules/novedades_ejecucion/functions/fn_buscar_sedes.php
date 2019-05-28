<option value="">seleccione</option>
<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$periodo_actual = $_SESSION['periodoActual'];
$institucion = (isset($_POST['institucion']) && ! empty($_POST['institucion'])) ? $Link->real_escape_string($_POST["institucion"]) : "";

$consulta_sedes = "SELECT cod_sede AS codigo, nom_sede AS nombre FROM sedes$periodo_actual WHERE cod_inst = '$institucion' ORDER BY nom_sede ASC;";
$respuesta_consulta_sedes = $Link->query($consulta_sedes) or die('Error al consultar sedes: '. $Link->error);
if (! empty($respuesta_consulta_sedes->num_rows))
{
	while($sede = $respuesta_consulta_sedes->fetch_object())
	{
		$codigo = $sede->codigo;
		$nombre = $sede->nombre;
		echo '<option value="'. $codigo .'">'. $nombre .'</option>';
	}
}
