<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

    $mes = $_POST['mestabla'];

  	$tabla = $mes.$_SESSION['periodoActual'];

$consulta = "SELECT 
              pmovdet.CodigoProducto, pmovdet.Descripcion
          FROM
              insumosmovdet$tabla AS pmovdet
          GROUP BY CodigoProducto";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
 while ($producto = $resultado->fetch_assoc()) { ?>
   <option value="<?php echo $producto['CodigoProducto'] ?>"><?php echo $producto['Descripcion'] ?></option>
 <?php }
}
 ?>