<option value="">Seleccione Grupo</option>
<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$codSede = $_POST['codSede'];
$semana = $_POST['semana'];
$grado = $_POST['grado'];

$consultaGrupos = "";

$consultaGrupos = "SELECT DISTINCT (nom_grupo) AS grupo FROM " .$semana. " WHERE cod_sede = " . $codSede . " AND cod_grado = " .$grado. " ;";

$resultado = $Link->query($consultaGrupos);
	if ($resultado->num_rows > 0) {
		while ($grupos = $resultado->fetch_assoc()) { ?>
		  <option value="<?php echo $grupos['grupo'] ?>"><?php echo $grupos['grupo'] ?></option>
		<?php }
	} else { ?>
		<option value="">Sin Grupos</option>
	<?php }

 ?>