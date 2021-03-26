<?php 
// require_once 'fn_estadisticas_head_functions.php';
require_once '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];

$valores = [];
$conValores = "SELECT ID, tipo_complem, tipo, LimiteInferior, LimiteSuperior, valor FROM manipuladoras_valoresnomina";
$resValores = $Link->query($conValores);
if ($resValores->num_rows > 0) {
	while ($dataResValores = $resValores->fetch_assoc()) {
		$valores[$dataResValores['ID']] = $dataResValores;
	}
}

$tHeadValores = '<tr>
    				<th>Tipo complemento</th>
					<th>Tipo</th>
					<th>Limite inferior</th>
					<th>Limite superior</th>
					<th>Valor</th>
					<th align="center">Acciones</th>
  				</tr>';

$tBodyValores = '';
$tipoPago = '';
foreach ($valores as $valor => $valorManipuladora) {
	if ($valorManipuladora['tipo'] == 1) {
		$tipoPago = 'Pago por día';
	}elseif ($valorManipuladora['tipo'] == 2) {
		$tipoPago = 'Pago por titular';
	}
 		$tBodyValores.= '<tr>
 							<td>'.$valorManipuladora['tipo_complem'].'</td>
 							<td>'.$tipoPago.'</td>
 							<td>'.$valorManipuladora['LimiteInferior'].'</td>
 							<td>'.$valorManipuladora['LimiteSuperior'].'</td>
 							<td>'.$valorManipuladora['valor'].'</td>
 							<td align="left">
 							 	<div class="btn-group">
 									<div class="dropdown">
	 									<button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Acciones <span class="caret"></span>
	 									</button>
	 									<ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
	                        				<li><a href="#" class="editarValorManipuladoraNomina" data-codigoValorManipuladoraNomina = '. $valorManipuladora["ID"].'" data-idvalormanipuladora ="'.$valorManipuladora["ID"].'"><i class="fas fa-pencil-alt"></i> Editar</a></li>
	                        				<li><a data-toggle="modal" data-target="#modalEliminarValorManipuladora"  data-idvalormanipuladora="'.$valorManipuladora['ID'].'"><span class="fa fa-trash"></span>  Eliminar</a></li>
	                      				</ul>
	                      			</div>
	                      		</div>
	                      	</td>			
 					   	</tr>';		
}

$tFootValores = '<tr>
    				<th>Tipo complemento</th>
					<th>Tipo</th>
					<th>Limite inferior</th>
					<th>Limite superior</th>
					<th>Valor</th>
					<th align="center">Acciones</th>
  				</tr>';			

$data['thead'] = $tHeadValores;
$data['tbody'] = $tBodyValores;
$data['tfoot'] = $tFootValores;

// exit(var_dump($data));

echo json_encode($data);
