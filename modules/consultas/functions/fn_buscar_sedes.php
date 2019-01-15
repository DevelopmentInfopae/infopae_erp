<?php

    require_once '../../../db/conexion.php';



   //Se va a capturar el año actual para sacar la subcadena del periodo 
   //que permitira saber que tablas consultar de acuerdo al año. 

   if (isset($_SESSION['annoactual']) && $_SESSION['annoactual']!= '') {
      $annoactual = $_SESSION['annoactual'];
   } 
   else{
      $annoactual = date('Y');
   }
   $_SESSION['annoactual'] = $annoactual;
   $periodoactual = substr($annoactual, 2, 2); 










    if(isset($_POST["institucion"]) && $_POST["institucion"] != "" ){

    	$institucion = $_POST["institucion"];
      
    $vsql = "select distinct cod_sede, nom_sede from sedes".$periodoactual." where cod_inst = '$institucion' order by nom_sede asc";    
    $Link = new mysqli($Hostname, $Username, $Password, $Database);
      $Link->set_charset("utf8");
                $result = $Link->query($vsql);
                $Link->close();



                ?>
                <option value="">TODOS</option>

                <?php




                while($row = $result->fetch_assoc()) {  ?>

						<option value="<?php echo $row["cod_sede"]; ?>"><?php echo $row["nom_sede"]; ?></option>

                <?php }
    }
    else{
    	?>
<option value="">TODOS</option>
    	<?php
    }


?>