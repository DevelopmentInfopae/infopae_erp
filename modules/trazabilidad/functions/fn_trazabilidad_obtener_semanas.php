<option value="">Seleccione...</option>
<?php 
   require_once '../../../config.php';
   require_once '../../../db/conexion.php';
   $mes = $_POST['mes'];
  	$tabla = $mes.$_SESSION['periodoActual'];
   $semanaCache = (isset($_POST['semanaCache']) && $_POST['semanaCache'] != '') ? mysqli_real_escape_string($Link, $_POST['semanaCache']) : '';
   // exit(var_dump($_POST));
   $consulta = "SELECT DISTINCT(semana) as semana           
                  FROM planilla_semanas
                  WHERE MES = '$mes' ";
   $resultado = $Link->query($consulta);
   if ($resultado->num_rows > 0) {
      while ($data = $resultado->fetch_assoc()) { ?>
         <option value="<?= $data['semana'] ?>" <?= (isset($semanaCache) && $semanaCache == $data['semana']) ? 'selected' : '' ?> > <?= $data['semana'] ?></option>
 <?php }
}
 ?>