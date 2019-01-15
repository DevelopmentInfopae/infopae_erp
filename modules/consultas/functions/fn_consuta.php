<?php
    require_once 'db/conexion.php';
?>










<?php
    

    $vsql = "select ent.id, sed.nom_sede , sed.nom_inst, ubi.Ciudad, ubi.Departamento, ubi.CodigoDANE, sed.cod_inst, sed.cod_sede, CONCAT(est.nom1,' ',est.nom2,' ',est.ape1,' ',est.ape2) as nombre 
from entregas ent 
join estudiantes est on est.num_doc = ent.id_estudiante 
left join sedes sed on sed.cod_sede = est.cod_sede 
left join ubicacion ubi on ubi.CodigoDANE = sed.cod_mun_sede and ubi.ETC = 0 
where 1=1";



    if(isset($_POST["municipio"]) && $_POST["municipio"] != "" ){
        $municipio = $_POST["municipio"];
        $municipio_nm = $_POST["municipio_nm"];
        $vsql = $vsql." and ubi.CodigoDANE = '$municipio' ";
    }

    if(isset($_POST["institucion"]) && $_POST["institucion"] != ""){
        $institucion = $_POST["institucion"];
        $institucion_nm = $_POST["institucion_nm"];
        $vsql = $vsql." and sed.cod_inst = '$institucion' ";
    }

    if(isset($_POST["sede"]) && $_POST["sede"]!=""){
        $sede = $_POST["sede"];
        $sede_nm = $_POST["sede_nm"];
        $vsql = $vsql." and sed.cod_sede = '$sede' ";
    }

    if(isset($_POST["estudiante"]) && $_POST["estudiante"] != ""){
        $estudiante = $_POST["estudiante"];
        $estudiante_nm = $_POST["estudiante_nm"];
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
    



 

   //echo "<br><br>".$vsql."<br><br>"; 




    $Link = new mysqli($Hostname, $Username, $Password, $Database);
    $result = $Link->query($vsql);
    $Link->close();
    $Resultados = $result->num_rows;
   
?>

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
        
        <td align="center"><strong><?php echo $Resultados; ?></strong></td>
    </tr>   
</table>
<hr>