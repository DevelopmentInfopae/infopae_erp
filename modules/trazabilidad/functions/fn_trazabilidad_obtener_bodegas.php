<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

$consulta = "SELECT * FROM bodegas ORDER BY Nombre ASC";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
 while ($bodega = $resultado->fetch_assoc()) { ?>
   <option value="<?php echo $bodega['ID'] ?>"><?php echo $bodega['NOMBRE'] ?></option>
 <?php }
}
 ?>