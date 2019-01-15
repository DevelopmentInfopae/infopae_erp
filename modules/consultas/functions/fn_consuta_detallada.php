<?php

    require_once 'db/conexion.php';


    $vsql = "select sed.nom_sede , sed.nom_inst, ubi.Ciudad, ubi.Departamento, ubi.CodigoDANE, sed.cod_inst, sed.cod_sede, ent.fecha_hora,
CONCAT(est.nom1,' ',est.nom2,' ',est.ape1,' ',est.ape2) as estudiante_nm,

 est.cod_estrato,estr.nombre as estrato_nm,    est.sisben, est.cod_discap, disc.nombre as discapacidad_nm,est.etnia, est.resguardo, est.cod_pob_victima, pob.nombre as poblacion_victima_nm, est.cod_grado, gra.nombre as grado_nm,
est.cod_jorn_est, jor.nombre as jornada_nm, est.zona_res_est as zona, ent.fecha_hora,
ent.tipo_registro, ent.menu



from entregas ent
join estudiantes est on est.num_doc = ent.id_estudiante
left join sedes sed on sed.cod_sede = est.cod_sede
left join ubicacion ubi on ubi.CodigoDANE = sed.cod_mun_sede and ubi.ETC = 0
left join discapacidades disc on est.cod_discap = disc.id
left join estrato estr on est.cod_estrato = estr.id
left join pobvictima pob on pob.id = est.cod_pob_victima
left join grados gra on gra.id = est.cod_grado
left join jornada jor on jor.id = est.cod_jorn_est
where 1=1";

// echo "<br><br>".$vsql."<br><br>";

// SIN TENER EN CUENTA LAS ENTREGAS

// $vsql ="select sed.nom_sede , sed.nom_inst, ubi.Ciudad, ubi.Departamento, ubi.CodigoDANE, sed.cod_inst, sed.cod_sede,
// CONCAT(est.nom1,' ',est.nom2,' ',est.ape1,' ',est.ape2) as estudiante_nm,

//  est.cod_estrato,estr.nombre as estrato_nm,    est.sisben, est.cod_discap, disc.nombre as discapacidad_nm,est.etnia, est.resguardo, est.cob_pob_victima, pob.nombre as poblacion_victima_nm, est.cod_grado, gra.nombre as grado_nm,
// est.cod_jorn_est, jor.nombre as jornada_nm, est.zona_rest_est as zona



// from estudiantes est
// left join sedes sed on sed.cod_sede = est.cod_sede
// left join ubicacion ubi on ubi.CodigoDANE = sed.cod_mun_sede
// left join discapacidades disc on est.cod_discap = disc.id
// left join estrato estr on est.cod_estrato = estr.id
// left join pobvictima pob on pob.id = est.cob_pob_victima
// left join grados gra on gra.id = est.cod_grado
// left join jornada jor on jor.id = est.cod_jorn_est
// where 1=1";



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

    if(isset($_POST["fechaInicial"]) && $_POST["fechaInicial"] != "" ){
        $fechaInicial = $_POST["fechaInicial"];
        $vsql = $vsql." and ent.fecha_hora >= '$fechaInicial' ";
    }

    if(isset($_POST["fechaFinal"]) && $_POST["fechaFinal"] != "" ){
        $fechaFinal = $_POST["fechaFinal"];
        $fechaFinal = $fechaFinal." 24:59:59";
        $vsql = $vsql." and ent.fecha_hora <= '$fechaFinal' ";
    }





/*echo "<br><br>".$vsql."<br><br>";*/


    $Link = new mysqli($Hostname, $Username, $Password, $Database);
    $result = $Link->query($vsql);
    $Link->close();









                while($row = $result->fetch_assoc()) {



                    ?>


                    <tr>


                              <td><?php echo utf8_encode($row['Departamento']); ?></td>
                              <td><?php echo utf8_encode($row['Ciudad']); ?></td>
                              <td><?php echo utf8_encode($row['nom_inst']); ?></td>
                              <td><?php echo utf8_encode($row['nom_sede']); ?></td>
                              <td><?php echo utf8_encode($row['estudiante_nm']);   ?></td>

                              <td><?php echo utf8_encode($row['estrato_nm']); ?></td>
                              <td align="center"><?php echo utf8_encode($row['sisben']); ?></td>
                              <td><?php echo utf8_encode($row['discapacidad_nm']); ?></td>
                              <td><?php echo utf8_encode($row['etnia']); ?></td>
                              <td><?php echo utf8_encode($row['resguardo']); ?></td>
                              <td><?php echo utf8_encode($row['poblacion_victima_nm']); ?></td>
                              <td><?php echo utf8_encode($row['grado_nm']); ?></td>
                              <td><?php echo utf8_encode($row['jornada_nm']); ?></td>
                              <td align="center"><?php echo utf8_encode($row['zona']); ?></td>
                               <td align="center"><?php echo $row['fecha_hora']; ?></td>
                               <td><?php echo $row['menu']; ?></td>
                               <td><?php echo $row['tipo_registro']; ?></td>
                          </tr>
                    <?php



                }






?>
