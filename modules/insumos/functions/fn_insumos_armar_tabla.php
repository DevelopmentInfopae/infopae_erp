<?php 


  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $ruta = $_POST['ruta'];
  $nom_municipio = $_POST['nom_municipio'];
  $municipio = $_POST['municipio'];
  $cod_sede = $_POST['cod_sede'];
  $cod_inst = $_POST['cod_inst'];
  $sedes = "sedes".$_SESSION['periodoActual'];

  if ($ruta == "") {
  	
  	if ($cod_inst == "") {
  		$consultarSedes = "SELECT *, inst.nom_inst FROM $sedes as sede 
      INNER JOIN sedes_cobertura ON sedes_cobertura.cod_sede = sede.cod_sede
      INNER JOIN instituciones AS inst ON inst.cod_mun = '".$municipio."' AND inst.codigo_inst = sede.cod_inst GROUP BY sede.cod_sede ORDER BY inst.nom_inst ASC";
  		$resultadoSedes = $Link->query($consultarSedes);

  		if ($resultadoSedes->num_rows > 0) {
  			while ($sede = $resultadoSedes->fetch_assoc()) { ?>
  				<tr>
  					<td><input type="checkbox" name="sede[]" class="checkInst " value="<?php echo $sede['cod_sede']; ?>"></td>
  					<td><?php echo $nom_municipio; ?></td>
  					<td><?php echo $sede['nom_inst']; ?></td>
  					<td><?php echo $sede['nom_sede']; ?></td>
  				</tr>
  			<?php  }
  		}
  	} else if ($cod_inst != "") {
  		if ($cod_sede == "") {
	  		$consultarSedes = "SELECT *, inst.nom_inst FROM $sedes as sede INNER JOIN instituciones AS inst ON inst.codigo_inst = '".$cod_inst."' AND sede.cod_inst = inst.codigo_inst GROUP BY sede.cod_sede  ORDER BY inst.nom_inst ASC";
	  		$resultadoSedes = $Link->query($consultarSedes);

	  		if ($resultadoSedes->num_rows > 0) {
	  			while ($sede = $resultadoSedes->fetch_assoc()) { ?>
	  				<tr>
	  					<td><input type="checkbox" name="sede[]" class="checkInst " value="<?php echo $sede['cod_sede']; ?>"></td>
	  					<td><?php echo $nom_municipio; ?></td>
	  					<td><?php echo $sede['nom_inst']; ?></td>
	  					<td><?php echo $sede['nom_sede']; ?></td>
	  				</tr>
	  			<?php  }
	  		}
  		} else if ($cod_sede != "") {
	  		$consultarSedes = "SELECT * FROM $sedes WHERE cod_sede = ".$cod_sede;
	  		$resultadoSedes = $Link->query($consultarSedes);

	  		if ($resultadoSedes->num_rows > 0) {
	  			if ($sede = $resultadoSedes->fetch_assoc()) { ?>
	  				<tr>
	  					<td><input type="checkbox" name="sede[]" class="checkInst " value="<?php echo $sede['cod_sede']; ?>"></td>
	  					<td><?php echo $nom_municipio; ?></td>
	  					<td><?php echo $sede['nom_inst']; ?></td>
	  					<td><?php echo $sede['nom_sede']; ?></td>
	  				</tr>
	  			<?php  }
	  		}
  		}
  	}

  } else if ($ruta != "") {
    $consultarSedes = "SELECT *, inst.nom_inst, ubicacion.Ciudad FROM rutasedes 
        INNER JOIN $sedes AS sede ON sede.cod_sede = rutasedes.cod_Sede
        INNER JOIN instituciones AS inst ON inst.codigo_inst = sede.cod_inst
        INNER JOIN ubicacion ON ubicacion.codigoDANE = inst.cod_mun
        INNER JOIN parametros ON ubicacion.codigoDANE LIKE CONCAT( parametros.CodDepartamento, '%' )
     AND rutasedes.IDRUTA = '".$ruta."' ORDER BY ubicacion.Ciudad";

    $resultadoSedes = $Link->query($consultarSedes);
    if ($resultadoSedes->num_rows > 0) {
      while ($sede = $resultadoSedes->fetch_assoc()) { ?>
        <tr>
          <td><input type="checkbox" name="sede[]" class="checkInst " value="<?php echo $sede['cod_sede']; ?>"></td>
          <td><?php echo $sede['Ciudad']; ?></td>
          <td><?php echo $sede['nom_inst']; ?></td>
          <td><?php echo $sede['nom_sede']; ?></td>
        </tr>
      <?php  }
    }
  }