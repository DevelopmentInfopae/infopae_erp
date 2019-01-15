<?php 

  require_once '../../../config.php';
  require_once '../../../db/conexion.php';
  
  $proveedor = $_POST['proveedor'];
  $tipodespacho = $_POST['tipodespacho'];

  $consTipoDespacho = "SELECT * FROM tipomovimiento WHERE Id = '".$tipodespacho."'";
  $resTipoDespacho = $Link->query($consTipoDespacho);
  if ($resTipoDespacho->num_rows > 0) {
    while ($dataTipoDespacho = $resTipoDespacho->fetch_assoc()) {
      $nombre = $dataTipoDespacho['Movimiento'];
      $documento = $dataTipoDespacho['Documento'];
    }
  }


  if (strpos($nombre, "Proveedor") && $documento == "DESI") {
  	$consulta = "SELECT bodegas.ID, bodegas.NOMBRE FROM bodegas
  					INNER JOIN proveedores ON proveedores.Nitcc = '".$proveedor."'
  					INNER JOIN usuarios ON usuarios.num_doc = proveedores.Nitcc
  					INNER JOIN usuarios_bodegas AS ub ON ub.USUARIO = usuarios.id
  					AND bodegas.ID = ub.COD_BODEGA_SALIDA
  					GROUP BY bodegas.ID
  	";
  } else if (strpos($nombre, "Operador") && $documento == "DESI") {
    $consulta = "SELECT bodegas.ID, bodegas.NOMBRE FROM bodegas
            INNER JOIN empleados ON empleados.Nitcc = '".$proveedor."'
            INNER JOIN usuarios ON usuarios.num_doc = empleados.Nitcc
            INNER JOIN usuarios_bodegas AS ub ON ub.USUARIO = usuarios.id
            AND bodegas.ID = ub.COD_BODEGA_SALIDA
            GROUP BY bodegas.ID
    ";
  }

$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
 while ($bodega = $resultado->fetch_assoc()) { ?>
   <option value="<?php echo $bodega['ID'] ?>"><?php echo $bodega['NOMBRE'] ?></option>
 <?php }
} else { 
  ?>
<option value="">Sin bodegas para el proveedor seleccionado</option>
<?php } ?>
