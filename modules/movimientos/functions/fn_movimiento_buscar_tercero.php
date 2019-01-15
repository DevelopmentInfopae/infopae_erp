<?php
  //var_dump($_POST);
  $tabla = '';
  $tipo='';
  $tipoTercero = '';
  $nombre = '';
  $nitcc = '';
  if (isset($_POST['tipo']) && $_POST['tipo'] != '' ) {
    $tipo = $_POST['tipo'];

    if (isset($_POST['nombre']) && $_POST['nombre'] != '' ) {
      $nombre = $_POST['nombre'];
    }

    if (isset($_POST['nitcc']) && $_POST['nitcc'] != '' ) {
      $nitcc = $_POST['nitcc'];
    }

    session_start();
    require_once '../../../db/conexion.php';
    $Link = new mysqli($Hostname, $Username, $Password, $Database);
    if ($Link->connect_errno) {
      echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $Link->set_charset("utf8");
    $consulta = " SELECT TipoTercero FROM tipomovimiento where Id = $tipo ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
      $row = $resultado->fetch_assoc();
      $tipoTercero =  $row['TipoTercero'];
    }//Termina el if que valida que si existan resultados
    if ($tipoTercero != '') {


      if ($tipoTercero == 'Empleado') {
        if($nombre != ''){
          $consulta = " SELECT * from empleados where Nombre like '%$nombre%' ";
        }else{
          $consulta = " SELECT * from empleados where Nitcc like '%$nitcc%' ";
        }
        //echo $consulta;

        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        if($resultado->num_rows >= 1){


          ?>

<table>
  <thead>
    <tr>
      <th>Cedula</th>
      <th>Nombre</th>
      <th>Cargo</th>
      <th>Correo</th>
      <th>Teléfono</th>
    </tr>

  </thead>
  <tbody>





          <?php




          while($row = $resultado->fetch_assoc()){ ?>
            <tr>



              <td class=""> <?php echo $row['Nitcc']; ?>
                <input type="hidden" name="idTercero" class="idTercero" value="<?php echo $row['Nitcc']; ?>">
                <input type="hidden" name="nombreTercero" class="nombreTercero" value="<?php echo $row['Nombre']; ?>">
              </td>
              <td class=""> <?php echo $row['Nombre']; ?> </td>
              <td class=""> <?php echo $row['Cargo']; ?> </td>
              <td class=""> <?php echo $row['Email']; ?> </td>
              <td class=""> <?php echo $row['Telefono1']; ?> <?php echo $row['Telefono2']; ?> </td>

              </tr>

            <?php

          }





           ?>

</tbody>
</table>
<div class="cerrarSugerencia"><i class="fa fa-times" aria-hidden="true"></i>

</div>
<?php





        }
        else {
          ?><span>No se encontraron resultados.</span>  <div class="cerrarSugerencia"><i class="fa fa-times" aria-hidden="true"></i> </div><?php
        }





      }else if ($tipoTercero == 'Proveedor') {
        if($nombre != ''){
          $consulta = " SELECT * from proveedores where Nombrecomercial like '%$nombre%' OR RazonSocial like '%$nombre%' ";
        }else{
          $consulta = " SELECT * from proveedores where Nitcc like '%$nitcc%' ";
        }
        //echo $consulta;


        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        if($resultado->num_rows >= 1){


          ?>

  <table>
  <thead>
    <tr>
      <th>Nit/CC</th>
      <th>Razón Social</th>
      <th>Nombre Comercial</th>
      <th>Ciudad</th>
    </tr>

  </thead>
  <tbody>





          <?php




          while($row = $resultado->fetch_assoc()){ ?>
            <tr>



              <td class="">
                <?php echo $row['Nitcc']; ?>
                <input type="hidden" name="idTercero" class="idTercero" value="<?php echo $row['Nitcc']; ?>">
                <input type="hidden" name="nombreTercero" class="nombreTercero" value="<?php echo $row['RazonSocial']; ?>">
              </td>
              <td class=""> <?php echo $row['RazonSocial']; ?> </td>
              <td class=""> <?php echo $row['Nombrecomercial']; ?> </td>
              <td class=""> <?php echo $row['NombreCiudad']; ?> <?php echo $row['Telefono2']; ?> </td>

              </tr>

            <?php

          }





           ?>

  </tbody>
  </table>
  <div class="cerrarSugerencia"><i class="fa fa-times" aria-hidden="true"></i> </div>
  <?php





        }
        else {
          ?><span>No se encontraron resultados.</span>  <div class="cerrarSugerencia"><i class="fa fa-times" aria-hidden="true"></i> </div><?php
        }

      }// Termina el if para el tipo de tercero proveedor
    }
  }// Termina el if que valida la variable POST


?>
