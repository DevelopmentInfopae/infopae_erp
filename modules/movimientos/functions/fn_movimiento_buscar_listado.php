<?php
//var_dump($_POST);
// a = 1 => Terceros
// a = 2 => Productos
session_start();
require_once '../../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

if (isset($_POST['busqueda']) && $_POST['busqueda'] != '') {
  $busqueda = $Link->real_escape_string($_POST['busqueda']);
  if($busqueda == 1){
    $tipo = $Link->real_escape_string($_POST['tipo']);
    $consulta = " SELECT TipoTercero FROM tipomovimiento where Id = $tipo ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
      $row = $resultado->fetch_assoc();
      $tipoTercero =  $row['TipoTercero'];
    }//Termina el if que valida que si existan resultados
    if ($tipoTercero != '') {


      if ($tipoTercero == 'Empleado') {

        $consulta = " SELECT * from empleados ";
        //echo $consulta;

        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        if($resultado->num_rows >= 1){ ?>


          <table id="box-table-listado" class="table table-striped table-bordered table-hover selectableRows">
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


        <?php }
      }//Termina el if que busca tipos de tercero empleado
      else if($tipoTercero == 'Proveedor'){




            $consulta = " SELECT * from proveedores ";

          //echo $consulta;


          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
          if($resultado->num_rows >= 1){


            ?>

    <table id="box-table-listado" class="table table-striped table-bordered table-hover selectableRows">
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

<?php

}

      }// termina el if que busca tipos de tercero proveedor
}



  }else if($busqueda == 2){
    // Busqueda == 2 entonces vamos a listar productos
    $bodegaOrigen = '';
    $bodegaDestino = '';



    $periodoActual = $_SESSION['periodoActual'];
    $consulta = " SELECT p.Codigo, p.CodigoBarras, p.Descripcion, p.Tipo ";
    /*
    if($bodegaOrigen != ''){
        $consulta = $consulta." ,(SELECT pe.cantidad FROM productosexistencias16 pe WHERE pe.CODPRODUCTO = p.Codigo AND pe.CODBODEGA = '$bodegaOrigen') AS existenciaOrigen ";
    }
    if($bodegaDestino != ''){
        $consulta = $consulta." ,(SELECT pe.cantidad FROM productosexistencias16 pe WHERE pe.CODPRODUCTO = p.Codigo AND pe.CODBODEGA = '2') AS existenciaDestino ";
    }
    */
    $consulta = $consulta." FROM productos$periodoActual p where 1=1 AND  (TipodeProducto = 'Preparación' || TipodeProducto = 'Alimento') ";
    //echo $consulta;
    
    $resultado = $Link->query($consulta) or die ('Unable to execute query Buscando Alimentos para listado '. mysqli_error($Link));
    if($resultado->num_rows >= 1){ ?>
      <table id='box-table-listado' class="table table-striped table-bordered table-hover selectableRows">
        <thead>
          <tr>
            <th>Codigo</th>
            <th>Codigo Barras</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <?php if($bodegaOrigen != ''){ ?>
            <th>Existencias Origen</th>
            <?php } ?>
            <?php if($bodegaDestino != ''){ ?>
            <th>Existencias Destino</th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php




          while($row = $resultado->fetch_assoc()){ ?>
            <tr>



              <td class=""> <?php echo $row['Codigo']; ?>
                <input type="hidden" name="codigoS" class="codigoS" value="<?php echo $row['Codigo']; ?>">
                <input type="hidden" name="codigoBarrasS" class="codigoBarrasS" value="<?php echo $row['CodigoBarras']; ?>">
                <input type="hidden" name="descripcionS" class="descripcionS" value="<?php echo $row['Descripcion']; ?>">
              </td>
              <td class=""> <?php echo $row['CodigoBarras']; ?> </td>
              <td class=""> <?php echo $row['Descripcion']; ?> </td>
              <td class=""> <?php echo $row['Tipo']; ?> </td>

              <?php if($bodegaOrigen != ''){ ?>
              <td class=""> <?php echo $row['existenciaOrigen']; ?> </td>
              <?php } ?>
              <?php if($bodegaDestino != ''){ ?>
              <td class=""> <?php echo $row['existenciaDestino']; ?> </td>
              <?php } ?>

              </tr>

            <?php

          }





           ?>

</tbody>
</table>



<?php





        }





  }
}




mysqli_close($Link);
?>
<span class="listadoCerrar"><i class="fa fa-times" aria-hidden="true"></i></span>
