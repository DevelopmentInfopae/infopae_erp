<div class="row">
  <div class="col-sm-12 form-group">
    <h2>Información de titulares de derecho</h2>
	</div> <!-- /.col-sm-12 form-group -->
</div> <!-- /.row -->





<?php
/*Debo pedir Año y mes*/
$vsql = "SELECT
  er.id,
  er.tipo_doc_nom,
  er.num_doc,
  er.nom1,
  er.nom2,
  er.ape1,
  er.ape2,
  er.fecha_nac,
  er.genero,
  er.cod_grado,
  er.tipo_complem,

  u.Ciudad,
  er.nom_inst,
  er.nom_sede,

  er.D1,
  er.D2,
  er.D3,
  er.D4,
  er.D5,
  er.D6,
  er.D7,
  er.D8,
  er.D9,
  er.D10,
  er.D11,
  er.D12,
  er.D13,
  er.D14,
  er.D15,
  er.D16,
  er.D17,
  er.D18,
  er.D19,
  er.D20,
  er.D21,
  er.D22
FROM
  entregas_res_".$mes.$annoinicial." er

LEFT JOIN ubicacion u on u.CodigoDANE = er.cod_mun_sede and u.ETC = 0


WHERE 1=1 ";

if($municipio != ''){
  $vsql = $vsql." and er.cod_mun_sede = '$municipio' ";
}
if($institucion != ''){
  $vsql = $vsql." AND er.cod_inst = '$institucion' ";
}
if($sede != ''){
  $vsql = $vsql." AND er.cod_sede = '$sede' ";
}
$vsql = $vsql." ORDER BY er.id ASC ";
?>


<?php //echo "<br>La consulta:<br>".$vsql."<br>"; ?>

















<div class="row">
  <div class="col-sm-12 form-group">
    <div class="table-responsive">

<table width="100%" id="box-table-d" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>N°</th>
                <th>Tipo Dcto</th>
                <th>N° Documento de Identidad</th>
                <th>Primer Nombre del Titular</th>
                <th>Segundo Nombre del Titular</th>
                <th>Primer Apellido del Titular</th>
                <th>Segundo Apellido del Titular</th>
                <th>Fecha de Nacimiento</th>
                <th>1. Sexo</th>
                <th>2. Grado Educ</th>
                <th>3. Tipo complemento</th>
                <th>Municipio</th>
                <th>Institución</th>
                <th>Sede</th>



    <?php if($rowDias['D1'] >= 1){  ?> <th> <?php echo $rowDias['D1']; ?> </th> <?php } ?>
    <?php if($rowDias['D2'] >= 1){  ?> <th> <?php echo $rowDias['D2']; ?> </th> <?php } ?>
    <?php if($rowDias['D3'] >= 1){  ?> <th> <?php echo $rowDias['D3']; ?> </th> <?php } ?>
    <?php if($rowDias['D4'] >= 1){  ?> <th> <?php echo $rowDias['D4']; ?> </th> <?php } ?>
    <?php if($rowDias['D5'] >= 1){  ?> <th> <?php echo $rowDias['D5']; ?> </th> <?php } ?>
    <?php if($rowDias['D6'] >= 1){  ?> <th> <?php echo $rowDias['D6']; ?> </th> <?php } ?>
    <?php if($rowDias['D7'] >= 1){  ?> <th> <?php echo $rowDias['D7']; ?> </th> <?php } ?>
    <?php if($rowDias['D8'] >= 1){  ?> <th> <?php echo $rowDias['D8']; ?> </th> <?php } ?>
    <?php if($rowDias['D9'] >= 1){  ?> <th> <?php echo $rowDias['D9']; ?> </th> <?php } ?>
    <?php if($rowDias['D10'] >= 1){  ?> <th> <?php echo $rowDias['D10']; ?> </th> <?php } ?>
    <?php if($rowDias['D11'] >= 1){  ?> <th> <?php echo $rowDias['D11']; ?> </th> <?php } ?>
    <?php if($rowDias['D12'] >= 1){  ?> <th> <?php echo $rowDias['D12']; ?> </th> <?php } ?>
    <?php if($rowDias['D13'] >= 1){  ?> <th> <?php echo $rowDias['D13']; ?> </th> <?php } ?>
    <?php if($rowDias['D14'] >= 1){  ?> <th> <?php echo $rowDias['D14']; ?> </th> <?php } ?>
    <?php if($rowDias['D15'] >= 1){  ?> <th> <?php echo $rowDias['D15']; ?> </th> <?php } ?>
    <?php if($rowDias['D16'] >= 1){  ?> <th> <?php echo $rowDias['D16']; ?> </th> <?php } ?>
    <?php if($rowDias['D17'] >= 1){  ?> <th> <?php echo $rowDias['D17']; ?> </th> <?php } ?>
    <?php if($rowDias['D18'] >= 1){  ?> <th> <?php echo $rowDias['D18']; ?> </th> <?php } ?>
    <?php if($rowDias['D19'] >= 1){  ?> <th> <?php echo $rowDias['D19']; ?> </th> <?php } ?>
    <?php if($rowDias['D20'] >= 1){  ?> <th> <?php echo $rowDias['D20']; ?> </th> <?php } ?>
    <?php if($rowDias['D21'] >= 1){  ?> <th> <?php echo $rowDias['D21']; ?> </th> <?php } ?>
    <?php if($rowDias['D22'] >= 1){  ?> <th> <?php echo $rowDias['D22']; ?> </th> <?php } ?>








<th>Total días</th>


              </tr>
            </thead>
            <tbody>

            <?php
            $result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link));
while($row = $result->fetch_assoc()) {
  $total = 0;
  ?>
  <tr>
    <td align="center"><?php echo $row['id']; ?></td>
    <td align="center"><?php echo $row['tipo_doc_nom']; ?></td>
    <td><?php echo $row['num_doc']; ?></td>
    <td><?php echo $row['nom1']; ?></td>
    <td><?php echo $row['nom2']; ?></td>
    <td><?php echo $row['ape1']; ?></td>
    <td><?php echo $row['ape2']; ?></td>
    <td><?php echo $row['fecha_nac']; ?></td>
    <td align="center"><?php echo $row['genero']; ?></td>
    <td align="center"><?php echo $row['cod_grado']; ?></td>
    <td align="center"><?php echo $row['tipo_complem']; ?></td>



    <td align="center"><?php echo $row['Ciudad']; ?></td>
    <td align="center"><?php echo $row['nom_inst']; ?></td>
    <td align="center"><?php echo $row['nom_sede']; ?></td>





    <?php if($rowDias['D1']  >= 1) { ?>            <td align="center">   <?php  if($row['D1'] == '1') {   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D2']  >= 1) { ?>            <td align="center">   <?php  if($row['D2'] == '1') {   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D3']  >= 1) { ?>            <td align="center">   <?php  if($row['D3'] == '1') {   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D4']  >= 1) { ?>            <td align="center">   <?php  if($row['D4'] == '1') {   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D5']  >= 1) { ?>            <td align="center">   <?php  if($row['D5'] == '1') {   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D6']  >= 1) { ?>            <td align="center">   <?php  if($row['D6'] == '1') {   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D7']  >= 1) { ?>            <td align="center">   <?php  if($row['D7'] == '1') {   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D8']  >= 1) { ?>            <td align="center">   <?php  if($row['D8'] == '1') {   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D9']  >= 1) { ?>            <td align="center">   <?php  if($row['D9'] == '1') {   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D10'] >= 1) { ?>            <td align="center">   <?php  if($row['D10'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D11'] >= 1) { ?>            <td align="center">   <?php  if($row['D11'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D12'] >= 1) { ?>            <td align="center">   <?php  if($row['D12'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D13'] >= 1) { ?>            <td align="center">   <?php  if($row['D13'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D14'] >= 1) { ?>            <td align="center">   <?php  if($row['D14'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D15'] >= 1) { ?>            <td align="center">   <?php  if($row['D15'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D16'] >= 1) { ?>            <td align="center">   <?php  if($row['D16'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D17'] >= 1) { ?>            <td align="center">   <?php  if($row['D17'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D18'] >= 1) { ?>            <td align="center">   <?php  if($row['D18'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D19'] >= 1) { ?>            <td align="center">   <?php  if($row['D19'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D20'] >= 1) { ?>            <td align="center">   <?php  if($row['D20'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D21'] >= 1) { ?>            <td align="center">   <?php  if($row['D21'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>
    <?php if($rowDias['D22'] >= 1) { ?>            <td align="center">   <?php  if($row['D22'] == '1'){   $total++; echo 'X';  }   ?>                    </td> <?php   } ?>







    <td align="center"><?php echo $total; ?></td>
  </tr>



<?php } ?>
            </tbody>
          </table>

		</div> <!-- /.table-responsive -->
	</div> <!-- /.col-sm-12 form-group -->
</div> <!-- /.row -->
