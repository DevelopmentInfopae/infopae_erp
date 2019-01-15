<?php
  //var_dump($_POST);
  $codigoProducto = '';
  $bodegaOrigen = '';
  $bodegaDestino = '';
  $descripcionProducto = '';
  session_start();
  require_once '../../../db/conexion.php';
  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");


  if((isset($_POST['codigoProducto']) && $_POST['codigoProducto'] != '') || (isset($_POST['descripcionProducto']) && $_POST['descripcionProducto'] != '')){

    if (isset($_POST['codigoProducto']) && $_POST['codigoProducto'] != '' ) {
      $codigoProducto = mysqli_real_escape_string($Link,$_POST['codigoProducto']);
    }

    if (isset($_POST['bodegaOrigen']) && $_POST['bodegaOrigen'] != '' ) {
      $bodegaOrigen = mysqli_real_escape_string($Link,$_POST['bodegaOrigen']);
    }

    if (isset($_POST['bodegaDestino']) && $_POST['bodegaDestino'] != '' ) {
      $bodegaDestino = mysqli_real_escape_string($Link,$_POST['bodegaDestino']);
    }

    if (isset($_POST['descripcionProducto']) && $_POST['descripcionProducto'] != '' ) {
      $descripcionProducto = mysqli_real_escape_string($Link,$_POST['descripcionProducto']);
    }



    $periodoActual = $_SESSION['periodoActual'];
    $consulta = " SELECT p.Codigo, p.CodigoBarras, p.Descripcion, p.Tipo ";
    if($bodegaOrigen != ''){
        //$consulta = $consulta." ,(SELECT pe.cantidad FROM productosexistencias$periodoActual pe WHERE pe.CODPRODUCTO = p.Codigo AND pe.CODBODEGA = '$bodegaOrigen') AS existenciaOrigen ";
    }
    if($bodegaDestino != ''){
        //$consulta = $consulta." ,(SELECT pe.cantidad FROM productosexistencias$periodoActual pe WHERE pe.CODPRODUCTO = p.Codigo AND pe.CODBODEGA = '2') AS existenciaDestino ";
    }
    $consulta = $consulta." FROM productos$periodoActual p where 1=1 AND (TipodeProducto = 'Preparación' || TipodeProducto = 'Alimento') ";
    if($codigoProducto != ''){
        $consulta = $consulta." AND ( p.Codigo like '%$codigoProducto%' ";
        $consulta = $consulta." OR p.CodigoBarras like '%$codigoProducto%' )";
    }else if($descripcionProducto != ''){
        $consulta = $consulta." AND p.Descripcion like '%$descripcionProducto%' ";
    }



    //echo $consulta;

    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){ ?>
      <table class="table table-striped table-bordered table-hover selectableRows">
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
        else {
          ?><span>No se encontraron resultados.</span>  <div class="cerrarSugerencia"><i class="fa fa-times" aria-hidden="true"></i> </div><?php
        }




  }// Termina el if que valida la variable POST


?>



<div class="cerrarSugerencia"><i class="fa fa-times" aria-hidden="true"></i> </div>
