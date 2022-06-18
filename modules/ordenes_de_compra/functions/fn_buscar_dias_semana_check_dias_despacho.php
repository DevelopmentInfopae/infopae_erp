<?php
 
$semana = $_POST['semana'];
if(isset($_POST['diasDespacho'])){
   $diasDespacho = $_POST['diasDespacho'];
}

require_once '../../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
   echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
$semanas = explode(',', $semana);
$auxSemana = '';
foreach ($semanas as $key => $value) {
   $value = trim($value);
   $auxSemana .= "'" . $value . "',";
}
$auxSemana = trim($auxSemana,',');

$consulta = " SELECT * FROM planilla_semanas WHERE SEMANA IN ($auxSemana) "; 
   $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
   if($resultado->num_rows >= 1){
      $aux = 0;
      while($row = $resultado->fetch_assoc()) {
         $dia = $row['DIA'];
         $mes = $row['MES'];
?>
         <div class="dia">
            <input type="checkbox" class="dia" id="dia<?php echo $aux; ?>" name="dia<?php echo $aux; ?>" value="<?php echo $dia; ?>"
               <?php
                  if(isset($_POST['diasDespacho'])){
                     $pos = strpos($diasDespacho, $dia);
                     if ($pos === false) {}else{echo 'checked ';}
                  }
                  else{
                     echo 'checked';
                  }
               ?>
            >
            <label><?php $mes = mesEnLetras($mes); echo $dia." de ".$mes; ?></label>
         </div>
<?php
         $aux++;

      }// Termina el while
   }//Termina el if que valida que si existan resultados

function mesEnLetras($mes){
   switch ($mes) {
      case '01':
         return 'Enero';
         break;
      case '01':
         return 'Enero';
         break;
      case '02':
         return 'Febrero';
         break;
      case '03':
         return 'Marzo';
         break;
      case '04':
         return 'Abril';
         break;
      case '05':
         return 'Mayo';
         break;
      case '06':
         return 'Junio';
         break;
      case '07':
         return 'Julio';
         break;
      case '08':
         return 'Agosto';
         break;
      case '09':
         return 'Septiembre';
         break;
      case '10':
         return 'Octubre';
         break;
      case '11':
         return 'Nomviembre';
         break;
      case '12':
         return 'Diciembre';
         break;
      default:
          # code...
         break;
   }
}
