<option value="">Todos</option>
<?php
  $proveedor = $_POST['proveedor'];

  require_once '../../../autentication.php';
  require_once '../../../db/conexion.php';

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $consulta = " select distinct ub.COD_BODEGA_SALIDA as codigo, b.NOMBRE as nombre from usuarios u
  inner join usuarios_bodegas ub on u.id = ub.USUARIO
  inner join bodegas b on b.ID = ub.COD_BODEGA_SALIDA
  where u.num_doc = '$proveedor' ";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
      <option value="<?php echo $row['codigo']; ?>"><?php echo $row['nombre']; ?></option>
      <?php }// Termina el while
  }//Termina el if que valida que si existan resultados
