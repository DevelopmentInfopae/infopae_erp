<option value="">Seleccione uno</option>
<?php
  $tipoDocumento = $_POST['tipoDocumento'];
  $tipotercero = '';

  require_once '../../../autentication.php';
  require_once '../../../db/conexion.php';

  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $consulta = " select TipoTercero from tipomovimiento where Movimiento = '$tipoDocumento' ";


  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) {
      $tipotercero = $row['TipoTercero'];
    }
  }
  if($tipotercero != ''){
    if($tipotercero == 'Proveedor'){
      $consulta = " select Nitcc, Nombrecomercial as Nombre from proveedores  order by Nombre asc";
    }elseif($tipotercero == 'Empleado'){
      $consulta = " select Nitcc, Nombre from empleados order by Nombre asc ";
    }
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
      while($row = $resultado->fetch_assoc()) { ?>
        <option value="<?php echo $row['Nitcc']; ?>"><?php echo $row['Nombre']; ?></option>
        <?php }// Termina el while
    }//Termina el if que valida que si existan resultados
  }
