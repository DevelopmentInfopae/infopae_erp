<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $mes = $_POST['mestabla'];

  $tabla = $mes.$_SESSION['periodoActual'];

  $consulta = "SELECT 
                pmov.ResponsableRecibe
            FROM
                productosmov$tabla AS pmov
            GROUP BY ResponsableRecibe";
  $resultado = $Link->query($consulta);
  if ($resultado->num_rows > 0) {
   while ($conductor = $resultado->fetch_assoc()) { ?>
     <option value="<?php echo $conductor['ResponsableRecibe'] ?>"><?php echo $conductor['ResponsableRecibe'] ?></option>
   <?php }
  }
?>