<div class="row">
  <div class="col-sm-12 form-group">
		<h2>Resultados de consulta</h2>
	</div>
</div>

<?php
// exit(var_dump($_POST));
  $vsql = "SELECT cod_sede AS cod_sede, u.Ciudad, nom_inst, nom_sede, SUM(er.D1) as D1, SUM(er.D2) as D2, SUM(er.D3) as D3, SUM(er.D4) as D4, SUM(er.D5) as D5, SUM(er.D6) as D6, SUM(er.D7) as D7, SUM(er.D8) as D8, SUM(er.D9) as D9, SUM(er.D10) as D10, SUM(er.D11) as D11, SUM(er.D12) as D12, SUM(er.D13) as D13, SUM(er.D14) as D14, SUM(er.D15) as D15, SUM(er.D16) as D16, SUM(er.D17) as D17, SUM(er.D18) as D18, SUM(er.D19) as D19, SUM(er.D20) as D20,
SUM(er.D21) as D21, SUM(er.D22) as D22, SUM(er.D1 + er.D2 + er.D3 + er.D4 + er.D5 + er.D6 + er.D7 + er.D8 + er.D9 + er.D10 + er.D11 + er.D12 + er.D13 + er.D14 + er.D15 + er.D16 + er.D17 + er.D18 + er.D19 + er.D20 + er.D21 + er.D22 ) AS total
          FROM
	          entregas_res_".$mes.$annoinicial." er
          LEFT JOIN ubicacion u ON u.CodigoDANE = er.cod_mun_sede AND u.ETC = 0
          WHERE 1 = 1";
  
  if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != "") {
    if ($sede == '') {
      $codigoSedes = "";
      $documentoCoordinador = $_SESSION['num_doc'];
      $consultaCodigoSedes = "SELECT cod_sede FROM sedes$periodoActual WHERE id_coordinador = $documentoCoordinador;";
      $respuestaCodigoSedes = $Link->query($consultaCodigoSedes) or die('Error al consultar el código de la sede ' . mysqli_error($Link));
      if ($respuestaCodigoSedes->num_rows > 0) {
        $codigoInstitucion = '';
        while ($dataCodigoSedes = $respuestaCodigoSedes->fetch_assoc()) {
          $codigoSedeRow = $dataCodigoSedes['cod_sede'];
          $consultaCodigoInstitucion = "SELECT cod_inst FROM sedes$periodoActual WHERE cod_sede = $codigoSedeRow;";
          $respuestaCodigoInstitucion = $Link->query($consultaCodigoInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
          if ($respuestaCodigoInstitucion->num_rows > 0) {
            $dataCodigoInstitucion = $respuestaCodigoInstitucion->fetch_assoc();
            $codigoInstitucionRow = $dataCodigoInstitucion['cod_inst'];
            if ($codigoInstitucionRow == $codigoInstitucion || $codigoInstitucion == '') {
              $codigoSedes .= "'$codigoSedeRow'".",";
              $codigoInstitucion = $codigoInstitucionRow; 
            }
          }
        }
      }
      $codigoSedes = substr($codigoSedes, 0 , -1);
      $vsql .= " AND er.cod_sede IN ($codigoSedes) ";
    }
  }        

  if($municipio != ''){ $vsql = $vsql." AND er.cod_mun_sede = '$municipio' "; }
  if($institucion != ''){ $vsql = $vsql." AND er.cod_inst = '$institucion' "; }
  if($sede != ''){ $vsql = $vsql." AND er.cod_sede = '$sede' "; }

  $vsql = $vsql." group by (cod_sede) ";
?>

<div class="row">
  <div class="col-sm-12 form-group">
    <div class="table-responsive">
    <?php $columnas = 3;  ?>
      <table width="100%" id="box-table-dr" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>Municipio</th>
            <th>Institución</th>
            <th>Sede</th>
            <?php if ($mes == date("m")) { ?>
              <?php if($rowDias['D1'] >= 1 && $rowDias['D1'] <= date("d")){ ?> <th> <?php echo $rowDias['D1']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D2'] >= 1 && $rowDias['D2'] <= date("d")){ ?> <th> <?php echo $rowDias['D2']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D3'] >= 1 && $rowDias['D3'] <= date("d")){ ?> <th> <?php echo $rowDias['D3']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D4'] >= 1 && $rowDias['D4'] <= date("d")){ ?> <th> <?php echo $rowDias['D4']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D5'] >= 1 && $rowDias['D5'] <= date("d")){ ?> <th> <?php echo $rowDias['D5']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D6'] >= 1 && $rowDias['D6'] <= date("d")){ ?> <th> <?php echo $rowDias['D6']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D7'] >= 1 && $rowDias['D7'] <= date("d")){ ?> <th> <?php echo $rowDias['D7']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D8'] >= 1 && $rowDias['D8'] <= date("d")){ ?> <th> <?php echo $rowDias['D8']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D9'] >= 1 && $rowDias['D9'] <= date("d")){ ?> <th> <?php echo $rowDias['D9']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D10'] >= 1 && $rowDias['D10'] <= date("d")){ ?> <th> <?php echo $rowDias['D10']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D11'] >= 1 && $rowDias['D11'] <= date("d")){ ?> <th> <?php echo $rowDias['D11']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D12'] >= 1 && $rowDias['D12'] <= date("d")){ ?> <th> <?php echo $rowDias['D12']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D13'] >= 1 && $rowDias['D13'] <= date("d")){ ?> <th> <?php echo $rowDias['D13']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D14'] >= 1 && $rowDias['D14'] <= date("d")){ ?> <th> <?php echo $rowDias['D14']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D15'] >= 1 && $rowDias['D15'] <= date("d")){ ?> <th> <?php echo $rowDias['D15']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D16'] >= 1 && $rowDias['D16'] <= date("d")){ ?> <th> <?php echo $rowDias['D16']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D17'] >= 1 && $rowDias['D17'] <= date("d")){ ?> <th> <?php echo $rowDias['D17']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D18'] >= 1 && $rowDias['D18'] <= date("d")){ ?> <th> <?php echo $rowDias['D18']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D19'] >= 1 && $rowDias['D19'] <= date("d")){ ?> <th> <?php echo $rowDias['D19']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D20'] >= 1 && $rowDias['D20'] <= date("d")){ ?> <th> <?php echo $rowDias['D20']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D21'] >= 1 && $rowDias['D21'] <= date("d")){ ?> <th> <?php echo $rowDias['D21']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D22'] >= 1 && $rowDias['D22'] <= date("d")){ ?> <th> <?php echo $rowDias['D22']; ?> </th> <?php  $columnas++; } ?>
            <?php } else { ?>
              <?php if($rowDias['D1'] >= 1){ ?> <th> <?php echo $rowDias['D1']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D2'] >= 1){ ?> <th> <?php echo $rowDias['D2']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D3'] >= 1){ ?> <th> <?php echo $rowDias['D3']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D4'] >= 1){ ?> <th> <?php echo $rowDias['D4']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D5'] >= 1){ ?> <th> <?php echo $rowDias['D5']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D6'] >= 1){ ?> <th> <?php echo $rowDias['D6']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D7'] >= 1){ ?> <th> <?php echo $rowDias['D7']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D8'] >= 1){ ?> <th> <?php echo $rowDias['D8']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D9'] >= 1){ ?> <th> <?php echo $rowDias['D9']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D10'] >= 1){ ?> <th> <?php echo $rowDias['D10']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D11'] >= 1){ ?> <th> <?php echo $rowDias['D11']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D12'] >= 1){ ?> <th> <?php echo $rowDias['D12']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D13'] >= 1){ ?> <th> <?php echo $rowDias['D13']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D14'] >= 1){ ?> <th> <?php echo $rowDias['D14']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D15'] >= 1){ ?> <th> <?php echo $rowDias['D15']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D16'] >= 1){ ?> <th> <?php echo $rowDias['D16']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D17'] >= 1){ ?> <th> <?php echo $rowDias['D17']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D18'] >= 1){ ?> <th> <?php echo $rowDias['D18']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D19'] >= 1){ ?> <th> <?php echo $rowDias['D19']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D20'] >= 1){ ?> <th> <?php echo $rowDias['D20']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D21'] >= 1){ ?> <th> <?php echo $rowDias['D21']; ?> </th> <?php  $columnas++; } ?>
              <?php if($rowDias['D22'] >= 1){ ?> <th> <?php echo $rowDias['D22']; ?> </th> <?php  $columnas++; } ?>
            <?php } ?>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link));
          $total = 0;
          while($row = $result->fetch_assoc()) {
        ?>
          <tr>
            <td><?php echo $row['Ciudad']; ?></td>
            <td><?php echo $row['nom_inst']; ?></td>
            <td><?php echo $row['nom_sede']; ?></td>
            <?php if ($mes == date("m")) { ?>
            <?php if($rowDias['D1'] > 0 && $rowDias['D1'] <= date("d")) { ?> <td align="center"> <?php if($row['D1'] > 0) { echo $row['D1']; } ?></td> <?php } ?>
            <?php if($rowDias['D2'] > 0 && $rowDias['D2'] <= date("d")) { ?> <td align="center"> <?php if($row['D2'] > 0 && $rowDias['D2'] <= date("d")) { echo $row['D2']; } ?></td> <?php } ?>
            <?php if($rowDias['D3'] > 0 && $rowDias['D3'] <= date("d")) { ?> <td align="center"> <?php if($row['D3'] > 0 && $rowDias['D3'] <= date("d")) { echo $row['D3']; } ?></td> <?php } ?>
            <?php if($rowDias['D4'] > 0 && $rowDias['D4'] <= date("d")) { ?> <td align="center"> <?php if($row['D4'] > 0 && $rowDias['D4'] <= date("d")) { echo $row['D4']; } ?></td> <?php } ?>
            <?php if($rowDias['D5'] > 0 && $rowDias['D5'] <= date("d")) { ?> <td align="center"> <?php if($row['D5'] > 0 && $rowDias['D5'] <= date("d")) { echo $row['D5']; } ?></td> <?php } ?>
            <?php if($rowDias['D6'] > 0 && $rowDias['D6'] <= date("d")) { ?> <td align="center"> <?php if($row['D6'] > 0 && $rowDias['D6'] <= date("d")) { echo $row['D6']; } ?></td> <?php } ?>
            <?php if($rowDias['D7'] > 0 && $rowDias['D7'] <= date("d")) { ?> <td align="center"> <?php if($row['D7'] > 0 && $rowDias['D7'] <= date("d")) { echo $row['D7']; } ?></td> <?php } ?>
            <?php if($rowDias['D8'] > 0 && $rowDias['D8'] <= date("d")) { ?> <td align="center"> <?php if($row['D8'] > 0 && $rowDias['D8'] <= date("d")) { echo $row['D8']; } ?></td> <?php } ?>
            <?php if($rowDias['D9'] > 0 && $rowDias['D9'] <= date("d")) { ?> <td align="center"> <?php if($row['D9'] > 0 && $rowDias['D9'] <= date("d")) { echo $row['D9']; } ?></td> <?php } ?>
            <?php if($rowDias['D10'] > 0 && $rowDias['D10'] <= date("d")) { ?> <td align="center"> <?php if($row['D10'] > 0 && $rowDias['D10'] <= date("d")) { echo $row['D10']; } ?></td> <?php } ?>
            <?php if($rowDias['D11'] > 0 && $rowDias['D11'] <= date("d")) { ?> <td align="center"> <?php if($row['D11'] > 0 && $rowDias['D11'] <= date("d")) { echo $row['D11']; } ?></td> <?php } ?>
            <?php if($rowDias['D12'] > 0 && $rowDias['D12'] <= date("d")) { ?> <td align="center"> <?php if($row['D12'] > 0 && $rowDias['D12'] <= date("d")) { echo $row['D12']; } ?></td> <?php } ?>
            <?php if($rowDias['D13'] > 0 && $rowDias['D13'] <= date("d")) { ?> <td align="center"> <?php if($row['D13'] > 0 && $rowDias['D13'] <= date("d")) { echo $row['D13']; } ?></td> <?php } ?>
            <?php if($rowDias['D14'] > 0 && $rowDias['D14'] <= date("d")) { ?> <td align="center"> <?php if($row['D14'] > 0 && $rowDias['D14'] <= date("d")) { echo $row['D14']; } ?></td> <?php } ?>
            <?php if($rowDias['D15'] > 0 && $rowDias['D15'] <= date("d")) { ?> <td align="center"> <?php if($row['D15'] > 0 && $rowDias['D15'] <= date("d")) { echo $row['D15']; } ?></td> <?php } ?>
            <?php if($rowDias['D16'] > 0 && $rowDias['D16'] <= date("d")) { ?> <td align="center"> <?php if($row['D16'] > 0 && $rowDias['D16'] <= date("d")) { echo $row['D16']; } ?></td> <?php } ?>
            <?php if($rowDias['D17'] > 0 && $rowDias['D17'] <= date("d")) { ?> <td align="center"> <?php if($row['D17'] > 0 && $rowDias['D17'] <= date("d")) { echo $row['D17']; } ?></td> <?php } ?>
            <?php if($rowDias['D18'] > 0 && $rowDias['D18'] <= date("d")) { ?> <td align="center"> <?php if($row['D18'] > 0 && $rowDias['D18'] <= date("d")) { echo $row['D18']; } ?></td> <?php } ?>
            <?php if($rowDias['D19'] > 0 && $rowDias['D19'] <= date("d")) { ?> <td align="center"> <?php if($row['D19'] > 0 && $rowDias['D19'] <= date("d")) { echo $row['D19']; } ?></td> <?php } ?>
            <?php if($rowDias['D20'] > 0 && $rowDias['D20'] <= date("d")) { ?> <td align="center"> <?php if($row['D20'] > 0 && $rowDias['D20'] <= date("d")) { echo $row['D20']; } ?></td> <?php } ?>
            <?php if($rowDias['D21'] > 0 && $rowDias['D21'] <= date("d")) { ?> <td align="center"> <?php if($row['D21'] > 0 && $rowDias['D21'] <= date("d")) { echo $row['D21']; } ?></td> <?php } ?>
            <?php if($rowDias['D22'] > 0 && $rowDias['D22'] <= date("d")) { ?> <td align="center"> <?php if($row['D22'] > 0 && $rowDias['D22'] <= date("d")) { echo $row['D22']; } ?></td> <?php } ?>
          <?php } else { ?>
            <?php if($rowDias['D1'] > 0) { ?> <td align="center"> <?php if($row['D1'] > 0) { echo $row['D1']; } ?></td> <?php } ?>
            <?php if($rowDias['D2'] > 0) { ?> <td align="center"> <?php if($row['D2'] > 0) { echo $row['D2']; } ?></td> <?php } ?>
            <?php if($rowDias['D3'] > 0) { ?> <td align="center"> <?php if($row['D3'] > 0) { echo $row['D3']; } ?></td> <?php } ?>
            <?php if($rowDias['D4'] > 0) { ?> <td align="center"> <?php if($row['D4'] > 0) { echo $row['D4']; } ?></td> <?php } ?>
            <?php if($rowDias['D5'] > 0) { ?> <td align="center"> <?php if($row['D5'] > 0) { echo $row['D5']; } ?></td> <?php } ?>
            <?php if($rowDias['D6'] > 0) { ?> <td align="center"> <?php if($row['D6'] > 0) { echo $row['D6']; } ?></td> <?php } ?>
            <?php if($rowDias['D7'] > 0) { ?> <td align="center"> <?php if($row['D7'] > 0) { echo $row['D7']; } ?></td> <?php } ?>
            <?php if($rowDias['D8'] > 0) { ?> <td align="center"> <?php if($row['D8'] > 0) { echo $row['D8']; } ?></td> <?php } ?>
            <?php if($rowDias['D9'] > 0) { ?> <td align="center"> <?php if($row['D9'] > 0) { echo $row['D9']; } ?></td> <?php } ?>
            <?php if($rowDias['D10'] > 0) { ?> <td align="center"> <?php if($row['D10'] > 0) { echo $row['D10']; } ?></td> <?php } ?>
            <?php if($rowDias['D11'] > 0) { ?> <td align="center"> <?php if($row['D11'] > 0) { echo $row['D11']; } ?></td> <?php } ?>
            <?php if($rowDias['D12'] > 0) { ?> <td align="center"> <?php if($row['D12'] > 0) { echo $row['D12']; } ?></td> <?php } ?>
            <?php if($rowDias['D13'] > 0) { ?> <td align="center"> <?php if($row['D13'] > 0) { echo $row['D13']; } ?></td> <?php } ?>
            <?php if($rowDias['D14'] > 0) { ?> <td align="center"> <?php if($row['D14'] > 0) { echo $row['D14']; } ?></td> <?php } ?>
            <?php if($rowDias['D15'] > 0) { ?> <td align="center"> <?php if($row['D15'] > 0) { echo $row['D15']; } ?></td> <?php } ?>
            <?php if($rowDias['D16'] > 0) { ?> <td align="center"> <?php if($row['D16'] > 0) { echo $row['D16']; } ?></td> <?php } ?>
            <?php if($rowDias['D17'] > 0) { ?> <td align="center"> <?php if($row['D17'] > 0) { echo $row['D17']; } ?></td> <?php } ?>
            <?php if($rowDias['D18'] > 0) { ?> <td align="center"> <?php if($row['D18'] > 0) { echo $row['D18']; } ?></td> <?php } ?>
            <?php if($rowDias['D19'] > 0) { ?> <td align="center"> <?php if($row['D19'] > 0) { echo $row['D19']; } ?></td> <?php } ?>
            <?php if($rowDias['D20'] > 0) { ?> <td align="center"> <?php if($row['D20'] > 0) { echo $row['D20']; } ?></td> <?php } ?>
            <?php if($rowDias['D21'] > 0) { ?> <td align="center"> <?php if($row['D21'] > 0) { echo $row['D21']; } ?></td> <?php } ?>
            <?php if($rowDias['D22'] > 0) { ?> <td align="center"> <?php if($row['D22'] > 0) { echo $row['D22']; } ?></td> <?php } ?>
          <?php } ?>
            <td align="center"><?php echo $row['total']; ?></td>
          </tr>
        <?php
          $total = $total + $row['total'];
          }
        ?>
        </tbody>
        <tfoot style="font-weight: bold;">
        	<tr>
        		<td colspan="<?php echo $columnas; ?>" align="right">Gran Total</td>
        		<td align="center"><?php echo $total; ?></td>
        	</tr>
        </tfoot>
      </table>
		</div>
	</div>
</div>
