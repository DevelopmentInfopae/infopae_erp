<option value="">Seleccione...</option>
<?php 
   require_once '../../../config.php';
   require_once '../../../db/conexion.php';
   $mes = $_POST['mes'];
  	$tabla = $mes.$_SESSION['periodoActual'];

   $consulta = "SELECT DISTINCT(semana) as semana           
                  FROM despachos_enc$tabla ";
   $resultado = $Link->query($consulta);
   if ($resultado->num_rows > 0) {
      while ($data = $resultado->fetch_assoc()) { ?>
         <option value="<?= $data['semana'] ?>"><?= $data['semana'] ?></option>
 <?php }
}
 ?>