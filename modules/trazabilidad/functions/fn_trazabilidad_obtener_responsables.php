<option value="">Seleccione...</option>
<?php 
   require_once '../../../config.php';
   require_once '../../../db/conexion.php';

   $tdoc = $_POST['tipo_documento'];
   $proveedor = (isset($_POST['proveedorActual']) && $_POST['proveedorActual'] != '') ? mysqli_real_escape_string($Link, $_POST['proveedorActual']) : '';
   $tipotercero = '';
   $consulta = " SELECT TipoTercero FROM tipomovimiento WHERE Movimiento = '".$tdoc."' ";

   $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
   if($resultado->num_rows >= 1){
      while($row = $resultado->fetch_assoc()) {
         $tipotercero = $row['TipoTercero'];
      }
   }
   if($tipotercero != ''){
      if($tipotercero == 'Proveedor'){
         $consulta = " SELECT Nitcc, Nombrecomercial as Nombre FROM proveedores ORDER BY Nombre ASC ";
      }elseif($tipotercero == 'Empleado'){
         $consulta = " SELECT Nitcc, Nombre FROM empleados ORDER BY Nombre ASC ";
      }
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
      if($resultado->num_rows >= 1){
         while($row = $resultado->fetch_assoc()) { ?>
            <option value="<?php echo $row['Nitcc']; ?>" <?= (isset($proveedor) && $proveedor == $row['Nitcc']) ? 'selected' : '' ?> ><?php echo $row['Nombre']; ?></option>
        <?php 
         }// Termina el while
      }//Termina el if que valida que si existan resultados
   } else {
      echo $consulta;
   }