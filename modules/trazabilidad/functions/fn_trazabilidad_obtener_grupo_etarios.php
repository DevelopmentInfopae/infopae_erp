<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $grupoEtarioCache = (isset($_POST['grupoEtarioCache']) && $_POST['grupoEtarioCache'] != '') ? mysqli_real_escape_string($Link, $_POST['grupoEtarioCache']) : '';

   $consulta = "SELECT *  FROM grupo_etario";
   $resultado = $Link->query($consulta);
   if ($resultado->num_rows > 0) {
      while ($grupoEtario = $resultado->fetch_assoc()) { ?>
         <option value="<?php echo $grupoEtario['ID'] ?>" <?= (isset($grupoEtarioCache) && $grupoEtarioCache == $grupoEtario['ID'] ) ? 'selected' : '' ?> ><?php echo $grupoEtario['DESCRIPCION'] ?></option>
<?php }
   }
?>