<?php
    require_once '../../../db/conexion.php';

    $semana = $_POST['semana'];
    $mes = $_POST['mes'];
  
    $semanaParametro = '';
    if ($semana != '') {
        $semanaParametro = " AND SEMANA_DESPACHO = '$semana' ";
    }

    $consulta = "SELECT * FROM planilla_semanas WHERE MES_DESPACHO = '$mes' $semanaParametro";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
        $aux = 0;
        while($row = $resultado->fetch_assoc()) {
            $dia = $row['DIA'];
            $semana = $row['SEMANA_DESPACHO'];
            $mes = $row['MES'];
?>
            <div class="dia">
                <label>
                    <!-- <input type="hidden" name="semanaDia" id="semanaDia" value="<?= $semana.",".$dia ?>"> -->
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
                return 'ENERO';
            break;
            case '02':
                return 'FEBRERO';
            break;
            case '03':
                return 'MARZO';
            break;
            case '04':
                return 'ABRIL';
            break;
            case '05':
                return 'MAYO';
            break;
            case '06':
                return 'JUNIO';
            break;
            case '07':
                return 'JULIO';
            break;
            case '08':
                return 'AGOSTO';
            break;
            case '09':
                return 'SEPTIEMBRE';
            break;
            case '10':
                return 'OCTUBRE';
            break;
            case '11':
                return 'NOVIEMBRE';
            break;
            case '12':
                return 'DICIEMBRE';
            break;
        }
    }

  