<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';
  $semana = $_POST['semana'];
  $consultarDias = " SELECT DISTINCT DIA
                          FROM 
                        planilla_semanas
                          where 
                        SEMANA_DESPACHO ='$semana' ";
$resultadoDias = $Link->query($consultarDias);
if ($resultadoDias->num_rows > 0) {
  while ($dias = $resultadoDias->fetch_assoc()) { ?>
    <option value="<?php echo $dias['DIA'] ?>"><?php echo $dias['DIA'] ?></option>
  <?php }
}