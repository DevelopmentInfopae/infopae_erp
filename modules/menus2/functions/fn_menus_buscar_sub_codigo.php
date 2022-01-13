<option value="">Seleccione...</option>
<?php

  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

    $tipoProducto = $_POST['tipoProducto'];

  $consulta = " select * from productos".$_SESSION['periodoActual']." WHERE nivel = '2' AND Codigo like '".$tipoProducto."%' AND inactivo = '0'";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
      <option value="<?php echo $row['Codigo']; ?>"><?php echo $row['Descripcion']; ?></option>
      <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
