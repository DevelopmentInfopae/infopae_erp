<div class="row">
  <div class="col-sm-12 form-group">
    <h2>Resultado Detallado</h2>
	</div> <!-- /.col-sm-12 form-group -->
</div> <!-- /.row -->





<?php
$vsql = "select sed.nom_sede , sed.nom_inst, ubi.Ciudad, ubi.Departamento, ubi.CodigoDANE, sed.cod_inst, sed.cod_sede, ent.fecha_hora,
CONCAT(est.nom1,' ',est.nom2,' ',est.ape1,' ',est.ape2) as estudiante_nm, est.num_doc,

 est.cod_estrato,estr.nombre as estrato_nm,    est.sisben, est.cod_discap, disc.nombre as discapacidad_nm,est.etnia, est.resguardo, est.cod_pob_victima, pob.nombre as poblacion_victima_nm, est.cod_grado, gra.nombre as grado_nm,
est.cod_jorn_est, jor.nombre as jornada_nm, est.zona_res_est as zona, ent.fecha_hora,
ent.tipo_registro, ent.menu, ent.tipo_validacion



from entregas".$mesinicialConsulta.$periodoactual." ent
join estudiantes".$periodoactual." est on est.num_doc = ent.id_estudiante
left join sedes".$periodoactual." sed on sed.cod_sede = est.cod_sede
left join ubicacion ubi on ubi.CodigoDANE = sed.cod_mun_sede and ubi.ETC = 0
left join discapacidades disc on est.cod_discap = disc.id
left join estrato estr on est.cod_estrato = estr.id
left join pobvictima pob on pob.id = est.cod_pob_victima
left join grados gra on gra.id = est.cod_grado
left join jornada jor on jor.id = est.cod_jorn_est
where 1=1";

    if(isset($_POST["municipio"]) && $_POST["municipio"] != "" ){
        $municipio = $_POST["municipio"];
        $vsql = $vsql." and ubi.CodigoDANE = '$municipio' ";
    }

    if(isset($_POST["institucion"]) && $_POST["institucion"] != ""){
        $institucion = $_POST["institucion"];
        $vsql = $vsql." and sed.cod_inst = '$institucion' ";
    }

    if(isset($_POST["sede"]) && $_POST["sede"]!=""){
        $sede = $_POST["sede"];
        $vsql = $vsql." and sed.cod_sede = '$sede' ";
    }

    if(isset($_POST["estudiante"]) && $_POST["estudiante"] != ""){
        $estudiante = $_POST["estudiante"];
        $vsql = $vsql." and est.num_doc = '$estudiante' ";
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










//Imprimiendo Consulta Detallada
//echo "<br><br>".$vsql."<br><br>";
$result = $Link->query($vsql) or die ('No se encontró la información solicitada');
?>

















<div class="row">
  <div class="col-sm-12 form-group">
    <div class="table-responsive">

<table width="100%" id="box-table-d" class="table table-striped table-bordered table-hover">
       <thead>
         <tr>





             <th>DEPARTAMENTO</th>
           <th>MUNICIPIO</th>
           <th>INSTITUCIÓN</th>
           <th>SEDE</th>
           <th>ESTUDIANTE</th>
           <th>DOCUMENTO</th>



             <th>ESTRATO</th>
             <th>SISBEN</th>
             <th>DISCAPACIDAD</th>
             <th>ETNIA</th>
             <th>REGUARDO</th>
             <th>POBLACION VICTIMA DE DESPLAZAMIENTO</th>
             <th>GRADO</th>
             <th>JORNADA ESCOLAR</th>
             <th>ZONA RESIDENCIA ESTUDIANTE</th>
             <th>FECHA Y HORA ENTREGA</th>
             <th>MENU</th>
             <th>TIPO REGISTRO</th>

             <th>VALIDACIÓN</th>

         </tr>
       </thead>
       <tbody>

         <?php









           while($row = $result->fetch_assoc()) {



               ?>


               <tr>


                         <td><?php echo $row['Departamento']; ?></td>
                         <td><?php echo $row['Ciudad']; ?></td>
                         <td><?php echo $row['nom_inst']; ?></td>
                         <td><?php echo $row['nom_sede']; ?></td>
                         <td><?php echo $row['estudiante_nm'];   ?></td>
                         <td><?php echo $row['num_doc']; ?></td>

                         <td><?php echo $row['estrato_nm']; ?></td>
                         <td align="center"><?php echo $row['sisben']; ?></td>
                         <td><?php echo $row['discapacidad_nm']; ?></td>
                         <td><?php echo $row['etnia']; ?></td>
                         <td><?php echo $row['resguardo']; ?></td>
                         <td><?php echo $row['poblacion_victima_nm']; ?></td>
                         <td><?php echo $row['grado_nm']; ?></td>
                         <td><?php echo $row['jornada_nm']; ?></td>

                           <!-- 1=Urbana y 2=Rural -->
                           <?php

                             $auxZona = $row['zona'];

                             if ($auxZona == 1) {
                               $auxZona = 'URBANA';
                             }
                             else if ($auxZona == 2) {
                               $auxZona = 'RURAL';
                             }



                           ?>


                         <td align="center"><?php echo $auxZona; ?></td>
                          <td align="center"><?php echo $row['fecha_hora']; ?></td>
                          <td><?php echo $row['menu']; ?></td>
                          <td><?php echo $row['tipo_registro']; ?></td>
                          <td>
                             <?php
                               $aux = '';
                               $aux = $row['tipo_validacion'];
                               if($aux == 'Formulario Fisico'){
                                 $aux = 'Formulario';
                               }
                               else if($aux == 'Lector de Huellas Dactilar'){
                                 $aux = 'Huella';
                               }
                               echo $aux;
                             ?>
                           </td>
                     </tr>
               <?php



           }






?>
       </tbody>
     </table>

		</div> <!-- /.table-responsive -->
	</div> <!-- /.col-sm-12 form-group -->
</div> <!-- /.row -->
