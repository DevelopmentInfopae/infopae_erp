<option value="">Todas</option>
<?php
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $cod_municipio = $_POST['cod_municipio'];


	$consultaInstParametros = "SELECT DISTINCT codigo_inst, nom_inst, cod_mun FROM instituciones WHERE cod_mun = '".$cod_municipio."' AND EXISTS (SELECT cod_inst FROM sedes".$_SESSION['periodoActual']." WHERE cod_inst = codigo_inst) ORDER BY nom_inst ASC ";
	$resultado = $Link->query($consultaInstParametros);
	if ($resultado->num_rows > 0) {
		while ($institucion = $resultado->fetch_assoc()) { ?>
		  <option value="<?php echo $institucion['codigo_inst'] ?>"><?php echo $institucion['nom_inst'] ?></option>
		<?php }
	} else { ?>
		<option value="">Sin instituciones</option>
	<?php }

 ?>