<option value="">Seleccione uno</option>
<?php
  $documento = '';
  if (isset($_POST['documento']) && $_POST['documento'] != '') {
    $documento = $_POST['documento'];
    session_start();
    require_once '../../../db/conexion.php';
    $Link = new mysqli($Hostname, $Username, $Password, $Database);
    if ($Link->connect_errno) {
      echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $Link->set_charset("utf8");
    $consulta = " SELECT * FROM tipomovimiento WHERE Documento IN (SELECT Tipo FROM documentos WHERE Id = $documento) ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
      while($row = $resultado->fetch_assoc()) { ?>
        <option value="<?php echo $row["Id"]; ?>"><?php echo $row["Movimiento"]; ?></option>
        <?php
      }// Termina el while
    }//Termina el if que valida que si existan resultados
  }//Termina el if que valida la recepción de la variable post
?>
