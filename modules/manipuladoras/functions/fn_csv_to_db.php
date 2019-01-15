<?php
$respuesta = '';
//var_dump($_POST);
include("../db/conexion.php");
$mysqli = new mysqli($Hostname , $Username,   $Password,  $Database);
if ($mysqli->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$mysqli->set_charset("utf8");

$dispositivo = mysqli_real_escape_string($mysqli, $_POST['dispositivo']);
$nombre = mysqli_real_escape_string($mysqli, $_POST['nombre']);



// $dispositivo = $_POST['dispositivo'];
// $nombre = $_POST['nombre'];

$ruta = "../usb_bak/$dispositivo/$nombre.csv";




$consulta = " insert into biometria_reg (dispositivo_id, usr_dispositivo_id, fecha) values ";

$fila = 1;
if (($gestor = fopen($ruta, "r")) !== FALSE) {
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
        $numero = count($datos);
        if($fila > 1){

            if($fila == 2){
                $consulta .= " ( '$dispositivo' ";
            }else{
                $consulta .= " , ( '$dispositivo' ";
            }
           


            for ($c=0; $c < $numero-1; $c++) {
                $aux = $datos[$c];
                if($c == 1){
                 



                // Windows 1900 Calendar
                // $unixTimestamp = ($excelTimestamp - 25569) * 86400;
                // Mac 1904 Calendar
                // $unixTimestamp = ($excelTimestamp - 24107) * 86400;


                $aux = ($aux - 25569) * 86400;
                $aux = date('Y-m-d H:i:s', $aux);








                }            
                $consulta .= " , '$aux' ";
            }





            $consulta .= " ) ";



        }
        $fila++;
    }
    fclose($gestor);
}

$mysqli ->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($mysqli->affected_rows > 0){
    $respuesta = "1"; 
}



if($respuesta == "1"){
    $ruta = "../usb_bak/$dispositivo/$nombre.csv";
    unlink($ruta);
    $ruta = "../usb_bak/$dispositivo/$nombre.xlsx";
    unlink($ruta);
    $nombre = strtoupper($nombre);
    $ruta = "../usb_bak/$dispositivo/$nombre.KQ";
    unlink($ruta);
} 





echo json_encode(array("respuesta"=>$respuesta));