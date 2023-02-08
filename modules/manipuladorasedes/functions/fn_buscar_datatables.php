<?php 
include '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];

$tHead = '';
$tFoot = '';	
$tBody = '';
$filas = 3;

$compString = '';
$consultaComplementos = " SELECT CODIGO FROM tipo_complemento ";
$respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complemento');
if ($respuestaComplementos->num_rows > 0) {
    while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
        $compString .= $dataComplementos['CODIGO'] . ", ";
        $complementos[] = $dataComplementos['CODIGO'];
    }
}
$compString = trim($compString, ', ');

// primero vamos a buscar la ultima focalizacion del mes que enviamos a consultar
$consultaSemanas = "SELECT DISTINCT semana AS semana FROM sedes_cobertura ";
$respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar las semanas ln 7');
if ($respuestaSemanas->num_rows > 0) {
    $num = 1;
    while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
        $semanas[$num] = $dataSemanas['semana'];
        $num++;
    }

    end($semanas);
    $ultimaKey = key($semanas);
    reset($semanas);
    // empezamos a montar la tabla
    $tdiv = " <div class='clients-list'> ";
        $tdiv .=  " <ul class='nav nav-tabs'>";
            $auxIndice = 1;
            foreach ($semanas as $key => $value) {
                $tdiv .= " <li class= "; 
                $tdiv .= ($auxIndice == $ultimaKey) ? "'active'" : "' '"; 
                $tdiv .= " >";
                $tdiv .= " <a data-toggle='tab' href= '#tab-" .$value. "'>";
                $tdiv .= " <i class='far fa-user'></i>Semana " .$value. " ";
                $tdiv .= " </a> ";
                $tdiv .= " </li>";
                $auxIndice++;
            }
        $tdiv .= "</ul><br>";        
        $tdiv .= "<div class='tab-content'>";

        $auxIndice = 1;
        $tabla = '';
        $daysExecute = 0;
       
        foreach ($semanas as $key => $value){             
            $tHead = '';
            $tHead .= " <div id= 'tab-" .$value. "' class='tab-pane"; 
            if($auxIndice == $ultimaKey){ $tHead .=  " active'";} else { $tHead .= "'";}
            $tHead .= " >";
                $tHead .= "<div class='table-responsive'>";
                    $tHead .= "<table tabindex='$value' class='table table-striped table-hover box-table-$value' id='box-table-movimientos' >";
                        $tHead .= "<thead>";
                            $tHead .= "<tr>";
                                $tHead .= "<th> Municipio </th>";
                                $tHead .= "<th> Institución </th>";
                                $tHead .= "<th> Sede </th>";

                                foreach ($complementos as $keyC => $valueC) {
                                    $tHead .= "<th> Cobertura $valueC </th>";
                                    $tHead .= "<th> N° Manipuladoras $valueC </th>";
                                }
                            $tHead .= "</tr>";
                        $tHead .= "</thead>";
                        
                        $consulta = " SELECT    u.Ciudad, 
                                                s.nom_inst,
                                                s.nom_sede,
                                                $compString
                                            FROM sedes$periodoActual s
                                            INNER JOIN priorizacion$value p ON p.cod_sede = s.cod_sede 
                                            INNER JOIN ubicacion u ON s.cod_mun_sede = u.codigoDANE
                                            ORDER BY u.ciudad, s.cod_inst, s.cod_sede ";
                        $respuesta = $Link->query($consulta) or die ('Error al consultar la priorizacion');    
                        if ($respuesta->num_rows > 0) {
                            $tBody = '';
                            $tBody .= "<tbody>";
                            while ($dataPriorizacion = $respuesta->fetch_assoc()) {
                                $tBody .= "<tr>";
                                $tBody .= "<td>" .$dataPriorizacion['Ciudad']. "</td>";
                                $tBody .= "<td>" .$dataPriorizacion['nom_inst']. "</td>";
                                $tBody .= "<td>" .$dataPriorizacion['nom_sede']. "</td>";
                                foreach ($complementos as $keyC => $valueC) {
                                    $tBody .= "<td> $dataPriorizacion[$valueC] </td>";
                                    
                                    $consultaManipuladora = " SELECT cant_manipuladora 
                                                                FROM parametros_manipuladoras 
                                                                WHERE tipo_complem = '$valueC' 
                                                                    AND limite_inferior < $dataPriorizacion[$valueC]
                                                                    AND limite_superior > $dataPriorizacion[$valueC] ";
                                    $respuestaManipuladora = $Link->query($consultaManipuladora) or die('error al consultar ln 96');                             
                                    if ($respuestaManipuladora->num_rows > 0) {
                                        $dataManipuladora = $respuestaManipuladora->fetch_assoc();
                                        $tBody .= "<td>". $dataManipuladora['cant_manipuladora'] ."</td>"; 
                                    }else{
                                        $tBody .= "<td> 0 </td>"; 
                                    }
                                }
                                $tBody .= "</tr>";
                            }
                            $tBody .= "</tbody>";
                        }
   
                        $tFoot = '';
                        $tFoot .= " <tfoot> ";
                            $tFoot .= " <tr> ";
                                $tFoot .= " <th> Municipio </th> ";
                                $tFoot .= " <th> Institución </th> ";
                                $tFoot .= " <th> Sede </th> ";
                                foreach ($complementos as $keyC => $valueC) {
                                    $tFoot .= "<th> Cobertura $valueC </th>";
                                    $tFoot .= "<th> N° Manipuladoras $valueC </th>";
                                }
                            $tFoot .= " </tr> ";
                        $tFoot .= " </tfoot> ";
                    $tFoot .= " </table> ";
                $tFoot .= " </div> ";
            $tFoot .= " </div> ";
            $tabla .= $tHead." ".$tBody." ".$tFoot; 
            $auxIndice++;		
        }				
		$tdiv .= $tabla." </div> </div> ";	
        $tabla = $tdiv; 
}
$data['semanas'] = $semanas;
$data['tabla'] = $tabla;
echo json_encode($data);
