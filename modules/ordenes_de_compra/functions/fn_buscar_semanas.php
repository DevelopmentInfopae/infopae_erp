<option value="">Seleccione</option>
<?php
  include '../../../config.php';
  require_once '../../../autentication.php';
  require_once '../../../db/conexion.php';

  // Variables
  $mes = "";
  $periodoActual = $_SESSION['periodoActual'];

  if (isset($_POST['mes'])) {
    if ($_POST['mes'] > 9) {
      $mes = $_POST['mes'];
    } else {
      $mes = "0". $_POST['mes'];
    }
  };

  // Consulta que retorna las semana del mes seleccionado.
  $consulta = "SELECT DISTINCT SEMANA FROM `planilla_semanas` WHERE MES = '$mes'";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows > 0){
    while($row = $resultado->fetch_assoc()) {
?>
      <option value="<?= $row["SEMANA"]; ?>"><?= "SEMANA ". $row["SEMANA"]; ?></option>
<?php
    }
  }
