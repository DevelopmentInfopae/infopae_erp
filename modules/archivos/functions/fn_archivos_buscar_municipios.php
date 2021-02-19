<option value="">Todos</option>
<?php
include '../../../config.php';
$periodoActual = $_SESSION['periodoActual'];
require_once '../../../db/conexion.php';

$consulta = " select DISTINCT codigoDANE, ciudad from ubicacion where 1=1 and ETC = 0 order by ciudad asc ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()) { ?>
    <option value="<?php echo $row['codigoDANE']; ?>"><?php echo $row['ciudad']; ?></option>
  <?php }// Termina el while
}//Termina el if que valida que si existan resultados





