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














    if(isset($_POST["sede"]) && $_POST["sede"] != "" ){

    	$sede = $_POST["sede"];
      
    $vsql = "select distinct num_doc, CONCAT(nom1,' ',nom2,' ',ape1,' ',ape2) as nombre from estudiantes".$periodoactual." where cod_sede = '$sede' ORDER BY nombre asc";    
    


    $Link = new mysqli($Hostname, $Username, $Password, $Database);
    $Link->set_charset("utf8");
    


    $result = $Link->query($vsql);
               



                ?>
                <option value="">TODOS</option>

                <?php




                while($row = $result->fetch_assoc()) {  ?>

						<option value="<?php echo $row["num_doc"]; ?>"><?php echo $row["nombre"]; ?></option>

                <?php }
    }
    else{
    	?>
<option value="">TODOS</option>
    	<?php
    }

 $Link->close();
?>