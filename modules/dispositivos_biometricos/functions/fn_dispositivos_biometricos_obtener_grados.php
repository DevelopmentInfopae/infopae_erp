<option value="">Seleccione Grado</option>
<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$codSede = $_POST['codSede'];
$nivel = $_POST['nivel'];
$semana = $_POST['semana'];
$consultaGrados = "";
$nivelValor = "";

if ($nivel == 'p') {
	$nivelValor .= "<= 5";
}else if ($nivel == 's') {
	$nivelValor .= ">= 6";
}

$consultaGrados = "SELECT DISTINCT (CONVERT(g.id, SIGNED)) AS id, g.nombre FROM grados as g INNER JOIN " .$semana. " AS f ON f.cod_grado = g.id WHERE g.id " . $nivelValor . " ORDER BY convert(g.id, SIGNED);";
// exit(var_dump($consultaGrados));

$resultado = $Link->query($consultaGrados);
	if ($resultado->num_rows > 0) {
		while ($grados = $resultado->fetch_assoc()) { ?>
		  <option value="<?php echo $grados['id'] ?>"><?php echo $grados['nombre'] ?></option>
		<?php }
	} else { ?>
		<option value="">Sin Grados</option>
	<?php }

 ?>