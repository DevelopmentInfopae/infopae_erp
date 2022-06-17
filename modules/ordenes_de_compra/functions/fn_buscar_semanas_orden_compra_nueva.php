
<option value="">Seleccione</option>
<?php
  include '../../../config.php';
  require_once '../../../autentication.php';
  require_once '../../../db/conexion.php';

  // Variables
  $mes = $_POST['mes'];
  $periodoActual = $_SESSION['periodoActual'];


  // Consulta que retorna las semana del mes seleccionado.
  $consulta = "SELECT DISTINCT SEMANA FROM `planilla_semanas` WHERE MES = '$mes'"; echo "$consulta";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows > 0){
    while($row = $resultado->fetch_assoc()) {
?>
      <option value="<?= $row["SEMANA"]; ?>"><?= "SEMANA ". $row["SEMANA"]; ?></option>
<?php
    }
  }
