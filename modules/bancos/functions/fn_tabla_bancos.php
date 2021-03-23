<?php 
// require_once 'fn_estadisticas_head_functions.php';
require_once '../../../config.php';
require_once '../../../db/conexion.php';
$periodoActual = $_SESSION['periodoActual'];

$bancos = [];
$consBancos = "SELECT id, Descripcion FROM bancos";
$resBancos = $Link->query($consBancos);
if ($resBancos->num_rows > 0) {
	while ($dataResBancos = $resBancos->fetch_assoc()) {
		$bancos[$dataResBancos['id']] = $dataResBancos['Descripcion'];
	}
}

$tHeadBanco = 	'<tr>
    				<th>Identificador</th>
					<th>Banco</th>
  				</tr>';

$tBodyBanco = '';
foreach ($bancos as $banco => $valorBanco) {
 		$tBodyBanco.= 	'<tr>
 							<td>'.$banco.'</td>
 							<td>'.$valorBanco.'</td>
 					   	</tr>';		
}

$tFootBanco = 	'<tr>
    				<th>Identificador</th>
					<th>Banco</th>
  				</tr>';			

$data['thead'] = $tHeadBanco;
$data['tbody'] = $tBodyBanco;
$data['tfoot'] = $tFootBanco;

echo json_encode($data);

