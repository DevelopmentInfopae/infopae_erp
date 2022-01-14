<?php 

  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

$consultarFocalizacion = "SELECT table_name AS tabla FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name like 'focalizacion%' ";
$resultadoFocalizacion = $Link->query($consultarFocalizacion);
if ($resultadoFocalizacion->num_rows > 0) {
    while ($focalizacion = $resultadoFocalizacion->fetch_assoc()) { ?>
    	<option value="<?php echo $focalizacion['tabla']; ?>">Semana <?php echo substr($focalizacion['tabla'], 12, 2); ?></option>
    <?php }
} ?>