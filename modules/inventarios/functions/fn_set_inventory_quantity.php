<?php 
set_time_limit (0);
require_once '../../../db/conexion.php';
require_once '../../../config.php';
require_once "../../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Declaración de variables.
$municipio = (isset($_POST["municipio"]) && $_POST["municipio"] != "") ? $_POST["municipio"] : "";
$bodega = (isset($_POST["bodega"]) && $_POST["bodega"] != "") ? $_POST["bodega"] : "";
$complemento = (isset($_POST["complemento"]) && $_POST["complemento"] != "") ? $_POST["complemento"] : "";

// Validacion que no haya un inventario inicial
$validacionComplementoI = '';
if ($_SESSION['p_inventory'] == 2) { // en caso que el inventario sea por complemento 
    $validacionComplementoI = " AND enc.complemento = '$complemento' ";
}

$consultaInventarioInicial = "SELECT id,  
                                     inventario_inicial,
                                     bodega  
                                FROM inventarios_bodegas_enc enc 
                                WHERE  enc.bodega = '$bodega' AND enc.municipio = '$municipio' $validacionComplementoI ";

$respuestaInventarioInicial = $Link->query($consultaInventarioInicial);
if($respuestaInventarioInicial->num_rows > 0){
    $dataInventarioInicial = $respuestaInventarioInicial->fetch_assoc();
    if ($dataInventarioInicial['inventario_inicial'] == 1) {
        $resultadoAJAX = [
            "estado" => 0,
            "mensaje" => "No es posible agregar inventario, debido a que ya existe un <STRONG>inventario inicial</STRONG> en la bodega seleccionada."
        ];
        echo json_encode($resultadoAJAX);
        exit;
    }else if($dataInventarioInicial['inventario_inicial'] == 0){
        if (isset($_FILES["archivoInventario"]["name"]) && $_FILES["archivoInventario"]["name"] != "") {
            $rutaArchivo = $_FILES["archivoInventario"]["tmp_name"];
            $tipoArchivo = str_replace("application/", "", $_FILES["archivoInventario"]["type"]);

            // Validamos si el archivo es .CSV
            if($tipoArchivo == "vnd.ms-excel" || $tipoArchivo == "text/csv"){
                $fila=0;
                //Abrimos nuestro archivo
                $archivo=fopen($rutaArchivo, "r");
                $separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";
                //Recorremos para validar instituciones existentes.

                $deleteInventarioDetalle = " DELETE FROM inventarios_bodegas_det WHERE id_bodega = '" .$dataInventarioInicial['id']. "'";
                $Link->query($deleteInventarioDetalle);
                // Consulta para la creacion de sedes_cobertura
                $consultaCrearInventarioDet="INSERT INTO inventarios_bodegas_det (
                                                                id_bodega,
                                                                codigo,
                                                                cantidad,
                                                                fecha_entrada ) 
                                                            VALUES ";

                //Abrimos nuestro archivo
                $archivo=fopen($rutaArchivo, "r");
                $separador = (count(fgetcsv($archivo, null, ",")) > 1) ? "," : ";";
                
                while(($datos = fgetcsv($archivo, null, $separador))==true) {
                    // Valores para la consulta de creación de sedes_cobertura
                    $consultaCrearInventarioDet.="(	'" .$dataInventarioInicial['id']. "', 
                                                        '0" .$datos[0]. "', 
                                                        '" .$datos[2]. "', 
                                                        '" .date("Y-m-d H:i:s"). "',";

                    $consultaCrearInventarioDet = trim($consultaCrearInventarioDet,',');
                                                        $consultaCrearInventarioDet .= "),";
                }
                
                // Ejecutamos la consulta para sedes cobertura
                $resultadoCrearSedeCobertura = $Link->query(trim($consultaCrearInventarioDet, ",")) or die("Error insertar el inventario: ". $consultaCrearInventarioDet);
                if($resultadoCrearSedeCobertura) {
                    $updateInventario = " UPDATE inventarios_bodegas_enc SET inventario_inicial = 1 
                                            WHERE bodega = '"  .$dataInventarioInicial['bodega']. "'";
                    if ($_SESSION['p_inventory'] == 2) {
                        $updateInventario .= " AND complemento = '$complemento' ";
                    }
                    $resultadoUpdate = $Link->query($updateInventario) or die ('Unable to execute query. '. mysqli_error($Link));
                    if($resultadoUpdate) {
                        $resultadoAJAX = [
                            "estado" => 1,
                            "mensaje" => "La importación fue realizada con éxito!"
                        ];
                    }  
                    else {
                        $resultadoAJAX = [
                            "estado" => 0,
                            "mensaje" => "La importación NO fue realizada con éxito."
                        ];
                    }  
                }
            } else {
                $resultadoAJAX = [
                    "estado" => 0,
                    "mensaje" => "La importación NO fue realizada con éxito."
                ];
            }
        } 
    } 
}else{
    $resultadoAJAX = [
        "estado" => 0,
        "mensaje" => "Actualmente no esta creada la bodega de inventario"
    ];
}

echo json_encode($resultadoAJAX);
exit;





