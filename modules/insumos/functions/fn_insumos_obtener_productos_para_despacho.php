<?php 

  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $num = $_POST['num_producto'];

  ?>
  <div class="col-sm-3" id="producto_<?php echo $num; ?>">
      <select class="form-control productodesp" onchange="validaProductos(this, '<?php echo $num; ?>')" name="productoDespacho[]" id="producto_<?php echo $num; ?>"
        required>

  <?php

  $consulta = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo LIKE '05%' AND Nivel = '3'";
  $resultado = $Link->query($consulta);
  if ($resultado->num_rows > 0) { ?>
  	<option value="">Seleccione...</option>
<?php while ($producto = $resultado->fetch_assoc()) { ?>
	<option value="<?php echo $producto['Codigo'] ?>"><?php echo $producto['Descripcion'] ?></option>
<?php }
} else { ?>
	<option value="">Sin insumos registrados.</option>
<?php } ?>
  </select>
  <input type="hidden" name="DescInsumo[]" id="descIns_<?php echo $num; ?>" value="">
</div>
