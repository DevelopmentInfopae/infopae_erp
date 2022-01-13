<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $numSemana = $_POST['numSemana'];
  $mes = $_POST['mes'];

?>

<tr id="semana_<?php echo $numSemana ?>">
  <td>
    <select name="semana[<?php echo $numSemana ?>]" id="semana<?php echo $numSemana ?>" onchange="validaCompSemana(this, 1)" class="form-control semana" required>
      <option value="">Seleccione...</option>
        <?php 
          $consultarSemanas = "SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE mes = $mes;";
		      $resultadoSemanas = $Link->query($consultarSemanas);
		      if ($resultadoSemanas->num_rows > 0) {
		        while ($dataSemanas = $resultadoSemanas->fetch_assoc()) { ?>
		    	    <option value="<?= "focalizacion".$dataSemanas['semana']; ?>">Semana <?= $dataSemanas['semana']; ?></option>
		        <?php }
		      } ?>
      </select>
      <label for="#semana<?php echo $numSemana ?>" class="error"></label>
    </td>
    <td>
      <select name="tipo_complemento[<?php echo $numSemana ?>]" id="tipo_complemento<?php echo $numSemana ?>" onchange="validaCompSemana(this, 2)" class="form-control tipo_complemento" required>
        <option value="">Seleccione...</option>
        <?php 
        $consultarGrados = "SELECT * FROM tipo_complemento ORDER BY ID ASC";
        $resultadoGrados = $Link->query($consultarGrados);
        if ($resultadoGrados->num_rows > 0) {
          while ($grado = $resultadoGrados->fetch_assoc()) { ?>
            <option value="<?php echo $grado['CODIGO'] ?>"><?php echo $grado['CODIGO']." (".$grado['DESCRIPCION'].")" ?></option>
          <?php }
        }
         ?>
      </select>
      <br>
      <label for="#tipo_complemento<?php echo $numSemana ?>" class="error"></label>
    </td>
  </tr>