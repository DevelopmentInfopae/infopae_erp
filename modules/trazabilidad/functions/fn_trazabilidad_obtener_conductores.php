<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $mes = $_POST['mestabla'];
  $tabla = $mes.$_SESSION['periodoActual'];
  $coductorCache = (isset($_POST['coductorCache']) && $_POST['coductorCache'] != '') ? mysqli_real_escape_string($Link, $_POST['coductorCache']) : '';

  $consulta = "SELECT 
                pmov.ResponsableRecibe
            FROM
                productosmov$tabla AS pmov
            WHERE 
                pmov.ResponsableRecibe != ''    
            GROUP BY ResponsableRecibe";  
  $resultado = $Link->query($consulta);
  if ($resultado->num_rows > 0) {
   while ($conductor = $resultado->fetch_assoc()) { ?>
     <option value="<?php echo $conductor['ResponsableRecibe'] ?>" <?= (isset($coductorCache) && $coductorCache == $conductor['ResponsableRecibe']) ? 'selected' : '' ?> ><?php echo $conductor['ResponsableRecibe'] ?></option>
   <?php }
  }
?>