<?php
  session_start();
  require_once '../../../db/conexion.php';
  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) { echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error; }
  $Link->set_charset("utf8");

  $item = mysqli_real_escape_string($Link,$_POST['items']);
  $codigo = mysqli_real_escape_string($Link,$_POST['codigoProducto']);
  $documento = mysqli_real_escape_string($Link,$_POST['documento']);
  $descripcion = mysqli_real_escape_string($Link,$_POST['descripcionProducto']);
  //$id = mysqli_real_escape_string($Link,$_POST['id']);
  $bodegaOrigen = mysqli_real_escape_string($Link,$_POST['bodegaOrigen']);
  $bodegaDestino = mysqli_real_escape_string($Link,$_POST['bodegaDestino']);

  $item = htmlspecialchars($item);
  $codigo = htmlspecialchars($codigo);
  $documento = htmlspecialchars($documento);
  $descripcion = htmlspecialchars($descripcion);
  //$id = htmlspecialchars($id);
  $bodegaOrigen = htmlspecialchars($bodegaOrigen);
  $bodegaDestino = htmlspecialchars($bodegaDestino);

  $estadoBodegaOrigen = '';
  $estadoBodegaDestino = '';

  // cuando el documento es una entrada
  if($documento == 3){
    $bodegaOrigen = '';
    $estadoBodegaOrigen = ' disabled="disabled" ';
    $estadoBodegaDestino = '';
  }

  // cuando el documento es una salida
  if($documento == 9){
    $bodegaOrigen = '';
    $estadoBodegaOrigen = '';
    $estadoBodegaDestino = ' disabled="disabled" ';
  }












?>


<tr>
  <td class="consecutivo">

  </td>
  <td>
    <input class="form-control" type="hidden" name="item" id="item" value="<?php echo $item; ?>">
    <?php //var_dump($_POST); ?>
    <input type="text" name="codigo<?php echo $item; ?>" id="codigo<?php echo $item; ?>" value="<?php echo $codigo; ?>" readonly="" class="form-control codigoItem campoNoVisible">
  </td>



  <td>
    <input type="hidden" name="descripcion<?php echo $item; ?>" id="descripcion<?php echo $item; ?>" value="<?php echo $descripcion; ?>">
    <?php echo $descripcion; ?>
  </td>








  <td>
    <select name="bodegaOrigen<?php echo $item; ?>" id="bodegaOrigen<?php echo $item; ?>" class="form-control bodegaOrigen" <?php echo $estadoBodegaOrigen; ?>>
      <option value="">Seleccione una</option>
      <?php
      $consulta = " select * from bodegas ";
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
      if($resultado->num_rows >= 1){
        while($row = $resultado->fetch_assoc()) { ?>
          <option value="<?php echo $row["ID"]; ?>" <?php  if (isset($bodegaOrigen) && ($bodegaOrigen == $row["ID"]) ) { echo ' selected '; } ?>   ><?php echo $row["NOMBRE"]; ?></option>
          <?php
        }// Termina el while
      }//Termina el if que valida que si existan resultados
      ?>
    </select>
  </td>



  <td>
    <select name="bodegaDestino<?php echo $item; ?>" id="bodegaDestino<?php echo $item; ?>" class="form-control bodegaDestino" <?php echo $estadoBodegaDestino; ?>>
      <option value="">Seleccione una</option>
      <?php
      $consulta = " select * from bodegas ";
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
      if($resultado->num_rows >= 1){
        while($row = $resultado->fetch_assoc()) { ?>
          <option value="<?php echo $row["ID"]; ?>" <?php  if (isset($bodegaDestino) && ($bodegaDestino == $row["ID"]) ) { echo ' selected '; } ?>   ><?php echo $row["NOMBRE"]; ?></option>
          <?php
        }// Termina el while
      }//Termina el if que valida que si existan resultados
      ?>
    </select>
  </td>

  <td>


    <select class="form-control unidadesM" name="unidades<?php echo $item; ?>" id="unidades<?php echo $item; ?>" onchange="buscar_cant_unidad('<?php echo $item; ?>','<?php echo $codigo; ?>')">
      <option value="">Seleccione una</option>
      <?php


      $periodoActual = $_SESSION['periodoActual'];
      $consulta = "   SELECT
            NombreUnidad1 as unidad, CantidadUnd1 as cantidad, 1 as consecutivoUnidad
        FROM
            productos$periodoActual
        WHERE
            Codigo = $codigo
        UNION SELECT
            NombreUnidad2 as unidad, CantidadUnd2 as cantidad, 2 as consecutivoUnidad
        FROM
            productos$periodoActual
        WHERE
            Codigo = $codigo
        UNION SELECT
            NombreUnidad3 as unidad, CantidadUnd3 as cantidad, 3 as consecutivoUnidad
        FROM
            productos$periodoActual
        WHERE
            Codigo = $codigo
            UNION SELECT
            NombreUnidad4 as unidad, CantidadUnd4 as cantidad, 4 as consecutivoUnidad
        FROM
            productos$periodoActual
        WHERE
            Codigo = $codigo
            UNION SELECT
            NombreUnidad5 as unidad, CantidadUnd5 as cantidad, 5 as consecutivoUnidad
        FROM
            productos$periodoActual
        WHERE
            Codigo = $codigo ";


      //echo $consulta;
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
      if($resultado->num_rows >= 1){
        while($row = $resultado->fetch_assoc()) {
          if($row["unidad"] != ''){?>

            <option value="<?php echo $row["consecutivoUnidad"]; ?>"><?php echo $row["unidad"]; ?></option>
        <?php  }



        }// Termina el while
      }//Termina el if que valida que si existan resultados
      ?>
    </select>
  </td>







  <td>
    <input class="form-control unidades txtAlignCenter" type="text" name="unidades<?php echo $item; ?>" id="unidades<?php echo $item; ?>" value="0">
    <input type="hidden" name="unidadesnm<?php echo $item; ?>" id="unidadesnm<?php echo $item; ?>">
    <input type="hidden" name="factor<?php echo $item; ?>" id="factor<?php echo $item; ?>" value="">
  </td>

  <td> <input class="form-control costounitario" type="text" name="costoUnitario<?php echo $item; ?>" id="costoUnitario<?php echo $item; ?>" value="0" class="txtAlignCenter"> </td>
  <td> <input class="form-control costototal" type="text" name="costoTotal<?php echo $item; ?>" id="costoTotal<?php echo $item; ?>" value="0" readonly="" class="campoNoVisible txtAlignCenter"> </td>


  <td> <input type="text" name="lote<?php echo $item; ?>" id="lote<?php echo $item; ?>" value="0" class="form-control txtAlignCenter loteItem"> </td>
  <td> <input type="text" class="form-control datepick txtAlignCenter fechaItem" name="fechaV<?php echo $item; ?>" id="fechaV<?php echo $item; ?>"> </td>

  <td align="center"> <span class="quitarItem"><i class="fa fa-minus-circle" aria-hidden="true"></i></span> </td>






</tr>
