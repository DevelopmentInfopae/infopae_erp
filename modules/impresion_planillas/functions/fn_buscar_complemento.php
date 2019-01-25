<option value="">Seleccion una</option>
<?php
  include '../../../config.php';
  require_once '../../../autentication.php';
  require_once '../../../db/conexion.php';

  $periodoActual = $_SESSION['periodoActual'];
  $sede = (isset($_POST['sede'])) ? $_POST['sede'] : "";
  $institucion = (isset($_POST['institucion'])) ? $_POST['institucion'] : "";

  $consulta = "SELECT DISTINCT tipo_complem from entregas_res_0119 WHERE 1";
  if($institucion != '') { $consulta = $consulta." AND cod_inst = '$institucion'"; }
  if($sede != '') { $consulta = $consulta." AND cod_sede = '$sede'"; }

  echo $consulta;

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) {
?>
      <option value="<?php echo $row['tipo_complem']; ?>"><?php echo $row['tipo_complem']; ?></option>
<?php
    }
  }
