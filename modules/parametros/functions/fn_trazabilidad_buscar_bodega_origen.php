<option value="">Todos</option>
<?php
  $proveedor = $_POST['proveedor'];

  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $consulta = " select distinct ub.COD_BODEGA_ENTRADA as codigo, b.NOMBRE as nombre from usuarios u
  inner join usuarios_bodegas ub on u.id = ub.USUARIO
  inner join bodegas b on b.ID = ub.COD_BODEGA_ENTRADA
  where u.num_doc = '$proveedor' ";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
      <option value="<?php echo $row['codigo']; ?>"><?php echo $row['nombre']; ?></option>
      <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
