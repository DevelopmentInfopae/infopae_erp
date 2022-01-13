<option value="">Seleccione...</option>
<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $consulta = "select * from tipo_despacho";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
        <option value="<?php echo $row['Id']; ?>"><?php echo $row['Descripcion']; ?></option>
     <?php }// Termina el while
  } else {
    echo 0;
  }//Termina el if que valida que si existan resultados
