<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $cod_inst = $_POST['cod_inst'];

	$consultaInstParametros = "SELECT DISTINCT cod_sede, nom_sede FROM sedes".$_SESSION['periodoActual']." WHERE cod_inst = '".$cod_inst."' AND NOT EXISTS(SELECT cod_sede FROM Infraestructura WHERE cod_sede = sedes".$_SESSION['periodoActual'].".cod_sede) ORDER BY nom_sede ASC";
	$resultado = $Link->query($consultaInstParametros);
	if ($resultado->num_rows > 0) {
		while ($institucion = $resultado->fetch_assoc()) { ?>
		  <option value="<?php echo $institucion['cod_sede'] ?>"><?php echo $institucion['nom_sede'] ?></option>
		<?php }
	} else { ?>
		<option value="">Sin sedes</option>
	<?php }

 ?>