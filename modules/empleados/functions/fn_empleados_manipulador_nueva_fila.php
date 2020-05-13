<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

$dpto_residencia = isset($_POST['dpto_residencia']) ? $_POST['dpto_residencia'] : NULL;

$tr_html = '
<tr class="row_manipulador">
	<td>
		<select name="manipulador_tipo_complemento[]" class="form-control" required>
			<option value="">Seleccione...</option>';

			$consulta = "SELECT * FROM tipo_complemento";
			$result = $Link->query($consulta);
			if ($result->num_rows > 0) {
				while($tcom = $result->fetch_assoc()){
					$tr_html .= '<option value="'.$tcom['CODIGO'].'">'.$tcom['CODIGO'].'</option>';
				}
			}
$tr_html .= '</select>
	</td>
	<td>
		<select class="form-control manipulador_municipio" name="manipulador_municipio[]" required>
			<option value="">Seleccione uno</option>';
			$consulta = "SELECT ubi.CodigoDANE AS codigoMunicipio, ubi.Ciudad AS nombreMunicipio FROM ubicacion ubi WHERE ubi.CodigoDANE LIKE '$dpto_residencia%';";
			$resultado = $Link->query($consulta) or die ('Error al consultar ubicaciones: '. mysqli_error($Link));

			if ($resultado->num_rows > 0)
			{
				while ($registros = $resultado->fetch_assoc())
				{

					$tr_html .= '<option value="'.$registros['codigoMunicipio'].'">'.$registros['nombreMunicipio'].'</option>';

				}
			}
$tr_html.='</select>
	</td>
	<td>
		<select class="form-control manipulador_institucion" name="manipulador_institucion[]" required>
			<option value="">Seleccione uno</option>
		</select>
	</td>
	<td>
		<select class="form-control manipulador_sede" name="manipulador_sede[]" required>
			<option value="">Seleccione uno</option>
		</select>
	</td>
	<td><button type="button" class="btn-sm btn-danger delete_row"><span class="fa fa-trash"></span></button></td>
</tr>';

echo $tr_html;