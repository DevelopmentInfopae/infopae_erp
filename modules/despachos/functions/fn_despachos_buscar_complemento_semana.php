<option value="">Seleccione</option>
<?php
  include '../../../config.php';
  require_once '../../../autentication.php';
  require_once '../../../db/conexion.php';

  // Variables
  $periodo_actual = $_SESSION['periodoActual'];

  $mes = (isset($_POST['mes'])) ? $Link->real_escape_string($_POST['mes']) : "";
  $semana = (isset($_POST['semana'])) ? $Link->real_escape_string($_POST['semana']) : "";

  // Consulta que retorna las semana del mes seleccionado.
  $consulta_complementos = "SELECT DISTINCT Tipo_Complem AS tipo_complemento FROM despachos_enc$mes$periodo_actual WHERE Semana='$semana' ORDER BY tipo_complemento;";
  $respuesta_consulta_complementos = $Link->query($consulta_complementos) or die ('Error al cargar los complementos: '. $Link->error);
  if($respuesta_consulta_complementos->num_rows > 0)
  {
    while($complemento = $respuesta_consulta_complementos->fetch_object())
    { ?>
      <option value="<?= $complemento->tipo_complemento; ?>"><?= $complemento->tipo_complemento; ?></option>
<?php
    }
  }
