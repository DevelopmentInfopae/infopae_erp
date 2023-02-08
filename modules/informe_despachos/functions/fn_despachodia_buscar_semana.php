<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';
  $mes = $_POST['mes'];
  $consultarSemanas = " SELECT DISTINCT SEMANA
                          FROM 
                        planilla_semanas
                          where 
                        MES ='$mes' ";

$resultadoSemanas = $Link->query($consultarSemanas);
if ($resultadoSemanas->num_rows > 0) {
   while ($semanas = $resultadoSemanas->fetch_assoc()) { ?>
    <option value="<?php echo $semanas['SEMANA'] ?>"><?php echo $semanas['SEMANA'] ?></option>

  <?php }
}