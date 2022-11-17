<?php
  require_once '../../../db/conexion.php';

  $semana = $_POST['semana'];
  $mes = $_POST['mes'];
  
  $semanaParametro = '';
  if ($semana != '') {
    $semanaParametro = " AND SEMANA = '$semana' ";
  }

  $consulta = "SELECT * FROM planilla_semanas WHERE MES = '$mes' $semanaParametro";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $aux = 0;
    while($row = $resultado->fetch_assoc()) {
      $dia = $row['DIA'];
      $semana = $row['SEMANA'];
      $mes = $row['MES'];
?>
    <div class="dia">
      <label>
        <input type="hidden" name="semanaDia" id="semanaDia" value="<?= $semana.",".$dia ?>">
        <input type="checkbox" class="dia i-checks" id="dia<?= $aux; ?>" name="dia<?= $aux; ?>" value="<?= $dia; ?>" style="margin-bottom: 5px;" checked>
        <?= $dia." de ". mesEnLetras($mes); ?>
      </label>
    </div>
<?php
      $aux++;
    }
  }

  function mesEnLetras($mes){
    switch ($mes) {
      case '01':
        return 'Enero';
        break;
      case '01':
        return 'Enero';
        break;
      case '02':
        return 'Febrero';
        break;
      case '03':
        return 'Marzo';
        break;
      case '04':
        return 'Abril';
        break;
      case '05':
        return 'Mayo';
        break;
      case '06':
        return 'Junio';
        break;
      case '07':
        return 'Julio';
        break;
      case '08':
        return 'Agosto';
        break;
      case '09':
        return 'Septiembre';
        break;
      case '10':
        return 'Octubre';
        break;
      case '11':
        return 'Noviembre';
        break;
      case '12':
        return 'Diciembre';
        break;

      default:
        # code...
        break;
    }
  }
