<option value="">Seleccione...</option>
<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$tabla = "focalizacion".$_POST['semana'];
$sedes = "sedes".$_SESSION['periodoActual'];

$consulta = "SELECT ubicacion.CodigoDANE, ubicacion.Ciudad FROM $tabla as F 
				INNER JOIN $sedes as sede ON sede.cod_sede = F.cod_sede
			    INNER JOIN ubicacion ON ubicacion.CodigoDANE = sede.cod_mun_sede
			GROUP BY ubicacion.CodigoDANE;";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
	while ($mun = $resultado->fetch_assoc()) { ?>
		<option value="<?php echo $mun['CodigoDANE']; ?>"><?php echo $mun['Ciudad']; ?></option>
	<?php }
}