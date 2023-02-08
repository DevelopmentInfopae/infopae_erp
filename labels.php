<?php 
// $labels = [];
$consultaLabels = " SELECT  nombre, 
                            nombre_submodulo,
                            label_submodulo
                        FROM menu_sidebar
                        WHERE label_submodulo != '' ";

$respuestaLabels = $Link->query($consultaLabels) or die ('Error al consultar los labels. ' . mysqli_error($Link));						
if ($respuestaLabels->num_rows > 0 ) {
    while ($dataLabels = $respuestaLabels->fetch_assoc()) {
        $labels[] = $dataLabels;
    }
	$dataLabels = $respuestaLabels->fetch_assoc();
}

function get_titles($name, $nameSub, $data){
    foreach ($data as $keyLabel => $valueLabel) {
		if ($valueLabel['nombre'] == $name) {
			if ($valueLabel['nombre_submodulo'] == $nameSub) {
				$nameLabel = $valueLabel['label_submodulo'];
			}
		}
	}
    return $nameLabel;
}
