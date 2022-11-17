<option value="">Seleccione...</option>
<?php 
   require_once '../../../config.php';
   require_once '../../../db/conexion.php';
   $mes = $_POST['mes'];
  	$tabla = $mes.$_SESSION['periodoActual'];
   $semanaCache = (isset($_POST['semanaCache']) && $_POST['semanaCache'] != '') ? mysqli_real_escape_string($Link, $_POST['semanaCache']) : '';
   $semanaCacheFinal = (isset($_POST['semanaCacheFinal']) && $_POST['semanaCacheFinal'] != '') ? mysqli_real_escape_string($Link, $_POST['semanaCacheFinal']) : '';

   $condicionSemanaInicial = '';
   $condicionConsecutivo = '';
   if(isset($semanaCache) && $semanaCache != '') {
      $condicionSemanaInicial = 'AND semana = '. "'" .$semanaCache. "'"; 
      $consultaConsecutivo = "SELECT CONSECUTIVO FROM planilla_semanas WHERE 1=1 $condicionSemanaInicial LIMIT 1 "; 
      $respuestaSemanaInicial = $Link->query($consultaConsecutivo) or die ('Error al consultar la semana inicial LN 15');
      if ($respuestaSemanaInicial->num_rows > 0) {
         $dataSemanaInicial = $respuestaSemanaInicial->fetch_assoc();
         $condicionConsecutivo = " AND CONSECUTIVO >= " .$dataSemanaInicial['CONSECUTIVO']. " ";
      }
   }  

   $consulta = "  SELECT DISTINCT(semana) as semana           
                     FROM planilla_semanas 
                     WHERE MES = '$mes' $condicionConsecutivo
                     ORDER BY CONSECUTIVO ";  
   $resultado = $Link->query($consulta); 
   if ($resultado->num_rows > 0) {
      while ($data = $resultado->fetch_assoc()) { ?>
         <option id="sf<?= $data['semana'] ?>" value="<?= $data['semana'] ?>" <?= (isset($semanaCacheFinal) && $semanaCacheFinal == $data['semana']) ? 'selected' : '' ?> > <?= $data['semana'] ?></option>
<?php }
   }
?>