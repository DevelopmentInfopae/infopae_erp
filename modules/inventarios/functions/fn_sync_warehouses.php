<?php
set_time_limit (0);
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$tipoInventario = $_SESSION['p_inventory'];
if ($tipoInventario == 1) {
    // primero insertamos las bodegas en el encabezado
    $insercionesEncabezado = " INSERT INTO inventarios_bodegas_enc ( 
                                                municipio, 
                                                bodega, 
                                                inventario_inicial)  
                                        (SELECT IF( LENGTH(ID)<12, ID, SUBSTRING(ID,2,5)), 
                                                ID, 
                                                '0' 
                                                FROM bodegas 
                                                WHERE ID NOT IN (SELECT bodega FROM inventarios_bodegas_enc )) ";
    $respuestaEncabezado = $Link->query($insercionesEncabezado) or die ('Error Ln 12');
    $respuesta = [  'estado' => '1',
                    'mensaje' => 'Exito'
                    ];
    echo json_encode($respuesta);
    exit();
}else if($tipoInventario == 2){
    $consultaComplemento = " SELECT CODIGO FROM tipo_complemento WHERE ValorRacion > 0 ";
    $respuestaComplemento = $Link->query($consultaComplemento) or die ('Error al consultar los complemento LN 15');
    if ($respuestaComplemento->num_rows > 0) {
        while ($dataComplemento = $respuestaComplemento->fetch_assoc()) {
            $compt = $dataComplemento['CODIGO'];
            $insercionesEncabezado = " INSERT INTO inventarios_bodegas_enc ( 
                                                        municipio, 
                                                        bodega, 
                                                        complemento, 
                                                        inventario_inicial)  
                                                    (SELECT IF( LENGTH(ID)<12, ID, SUBSTRING(ID,2,5)), 
                                                            ID,
                                                            '$compt', 
                                                            '0' 
                                                            FROM bodegas WHERE ID NOT IN (SELECT bodega FROM inventarios_bodegas_enc WHERE complemento = '$compt' ))  ";
            $respuestaEncabezado = $Link->query($insercionesEncabezado) or die ('Error Ln 35');
        }   
    }
    $respuesta = [  'estado' => '1',
                    'mensaje' => 'Bodegas Inicializadas Exitosamente. '
                ];
    echo json_encode($respuesta);
    exit();
}



