<div class="row">
  <div class="col-sm-12 form-group">
		<h2>Resultado de la consulta</h2>
	</div> <!-- /.col-sm-12 form-group -->
</div> <!-- /.row -->



<?php
// var_dump($mesinicialConsulta);
// var_dump($periodoactual);
$vsql = " SELECT COUNT(DISTINCT ent.id) as cantidad FROM entregas".$mesinicialConsulta.$periodoactual." ent ";

if(isset($_POST["municipio"]) && $_POST["municipio"] != "" ){
  $vsql = $vsql." left join sedes".$periodoactual." sed on sed.cod_sede = ent.cod_sede ";
  $vsql = $vsql." left join ubicacion ubi on ubi.CodigoDANE = sed.cod_mun_sede and ubi.ETC = 0 ";
}

if(isset($_POST["estudiante"]) && $_POST["estudiante"] != ""){
  $vsql = $vsql." join estudiantes".$periodoactual." est on est.num_doc = ent.id_estudiante ";
}

$vsql = $vsql." where 1=1 ";

    $parametrosDeConsulta = 'Se ejecuto una consulta, parametros de la consulta: ';

    if(isset($_POST["municipio"]) && $_POST["municipio"] != "" ){
        $municipio = $_POST["municipio"];
        $municipio_nm = $_POST["municipio_nm"];
        $vsql = $vsql." and ubi.CodigoDANE = '$municipio' ";
        $parametrosDeConsulta = $parametrosDeConsulta.' municipio = '.$municipio_nm;
    }

    if(isset($_POST["institucion"]) && $_POST["institucion"] != ""){
        $institucion = $_POST["institucion"];
        $institucion_nm = $_POST["institucion_nm"];
        $vsql = $vsql." and sed.cod_inst = '$institucion' ";
        $parametrosDeConsulta = $parametrosDeConsulta.', institución = '.$institucion_nm;
    }

    if(isset($_POST["sede"]) && $_POST["sede"]!=""){
        $sede = $_POST["sede"];
        $sede_nm = $_POST["sede_nm"];
        $vsql = $vsql." and sed.cod_sede = '$sede' ";
        $parametrosDeConsulta = $parametrosDeConsulta.', sede = '.$sede_nm;
    }

    if(isset($_POST["estudiante"]) && $_POST["estudiante"] != ""){
        $estudiante = $_POST["estudiante"];
        $estudiante_nm = $_POST["estudiante_nm"];
        $vsql = $vsql." and est.num_doc = '$estudiante' ";
        $parametrosDeConsulta = $parametrosDeConsulta.', estudiante = '.$estudiante_nm;
    }




   if(isset($_POST["diainicial"]) && $_POST["diainicial"] != "" ){
      $diainicial = $_POST["diainicial"];
      $vsql = $vsql." and DAYOFMONTH(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) >= ".$diainicial." ";
   }

   if(isset($_POST["mesinicial"]) && $_POST["mesinicial"] != "" ){
      $mesinicial = $_POST["mesinicial"];
      $vsql = $vsql." and MONTH(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) >= ".$mesinicial." ";
   }

   if(isset($_POST["annoinicial"]) && $_POST["annoinicial"] != "" ){
      $annoinicial = $_POST["annoinicial"];
      $vsql = $vsql." and YEAR(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) >= ".$annoinicial." ";
   }


    if(isset($_POST["diafinal"]) && $_POST["diafinal"] != "" ){
      $diafinal = $_POST["diafinal"];
      $vsql = $vsql." and DAYOFMONTH(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) <= ".$diafinal." ";
   }

   if(isset($_POST["mesfinal"]) && $_POST["mesfinal"] != "" ){
      $mesfinal = $_POST["mesfinal"];
      $vsql = $vsql." and MONTH(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) <= ".$mesfinal." ";
   }

   if(isset($_POST["annoinicial"]) && $_POST["annoinicial"] != "" ){
      $annofinal = $_POST["annofinal"];
      $vsql = $vsql." and YEAR(STR_TO_DATE(ent.fecha_hora, '%d/%m/%Y %H:%i:%s')) <= ".$annoinicial." ";
   }
  //echo "<br>La consulta:<br>".$vsql."<br>";

  //'Unable to execute query. '. mysqli_error($Link)
  $result = $Link->query($vsql) or die ('No se encontró la información solicitada');

  // Haciendo registro en el log
  $logIdUsr = $_SESSION['id_usuario'];
  date_default_timezone_set('America/Bogota');
  $fecha = date('Y-m-d H:i:s');

  $consulta = " insert into log (id_usuario,fecha,descripcion)
  values ('$logIdUsr','$fecha','$parametrosDeConsulta') ";
  //echo '<br>Consulta para el log: '.$consulta;

  $Link->query($consulta);
  // Termina hacer registro en el log

  $row = $result->fetch_assoc();
  $Resultados = $row['cantidad'];

  //var_dump($Resultados);
?>

<div class="row">
  <div class="col-sm-12 form-group">
    <div class="table-responsive">
      <table class="resultado">


      <tr><td></td><td align="center"><strong>Cantidad</strong></td></tr>



  <?php
      if (isset($municipio)){
  ?>
      <tr> <td><strong>Municipio:</strong> <?php echo $municipio_nm; ?></td> <td></td> </tr>
  <?php
  }
  else{
      ?>
  <tr> <td><strong>Municipio:</strong> Todos</td> <td></td> </tr>
      <?php
      }

  ?>

  <?php
      if (isset($institucion)){
  ?>
      <tr> <td><strong>Institución:</strong> <?php echo $institucion_nm; ?></td> <td></td> </tr>
  <?php
      }
      else{
      ?>
  <tr> <td><strong>Institución:</strong> Todos</td> <td></td> </tr>
      <?php
      }
  ?>

  <?php
      if (isset($sede)){
  ?>
      <tr> <td><strong>Sede:</strong> <?php echo $sede_nm; ?></td> <td></td> </tr>
  <?php
      }
      else{
      ?>
  <tr> <td><strong>Sede:</strong> Todos</td> <td></td> </tr>
      <?php
      }
  ?>

  <?php
      if (isset($estudiante)){
  ?>
      <tr> <td><strong>Estudiante:</strong> <?php echo $estudiante_nm; ?></td> <td></td> </tr>
  <?php
      }
      else{
      ?>
  <tr> <td><strong>Estudiante:</strong> Todos</td> <td></td> </tr>
      <?php
      }
  ?>




   <tr>
          <td></td>

          <td align="center" class="total">Total de entregas: <?php echo $Resultados; ?></td>
      </tr>
  </table>
    </div><!-- /.table-responsive -->
  </div><!-- /.col -->
</div><!-- /.row -->
