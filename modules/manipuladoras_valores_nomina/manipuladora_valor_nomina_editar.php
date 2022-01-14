<?php
include '../../config.php';
require_once '../../db/conexion.php';

// consulta para traer los datos por el id que estamos enviando 
$idValorManipuladora = (isset($_GET['idValorManipuladora']) && $_GET['idValorManipuladora'] != '') ? mysqli_real_escape_string($Link, $_GET['idValorManipuladora']) : '';

$sentenciaBuscar = "SELECT ID, tipo_complem, tipo, LimiteInferior, LimiteSuperior, valor FROM manipuladoras_valoresnomina WHERE ID = '$idValorManipuladora';";
$respuestaSentencia = $Link->query($sentenciaBuscar) or die('Error al consultar el valor de manipuladora'. mysqli_error($Link));
if($respuestaSentencia->num_rows > 0)
  {
    $dataValoresManipuladora = $respuestaSentencia->fetch_assoc();
    $id = $dataValoresManipuladora['ID'];
    $tipoComplemento = $dataValoresManipuladora['tipo_complem'];
    $tipo = $dataValoresManipuladora['tipo'];
    $limiteInferior = $dataValoresManipuladora['LimiteInferior'];
    $limiteSuperior = $dataValoresManipuladora['LimiteSuperior'];
    $valor = $dataValoresManipuladora['valor'];
  }
?>

<div class="modal fade in" id="modal_actualizar_valor" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="" method="post" id="formActualizarManipuladoraValorNomina">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edición Manipuladora Valor Nómina</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="tipoComplemento">Tipo de complemento</label>
                <input type="text" class="form-control" name="tipoComplemento" id="tipoComplemento"  value="<?= $tipoComplemento; ?>" required readonly>
              </div> <!-- form-group -->
            </div> <!-- col-sm-4 -->
            <div class="col-sm-4">
              <div class="form-group">
                <label for="tipo">Tipo</label>
                <select class="form-control" name="tipo" id="tipo" required>
                <!-- <option value="">Seleccione una</option> -->
                <?php 
                  $consultaTipo = 'SELECT distinct(tipo) FROM manipuladoras_valoresnomina';
                  $respuestaTipo = $Link->query($consultaTipo) or die('Error al consultar el tipo de pago: '. mysqli_error($Link));
                  $dataTipoNombre;
                  if($respuestaTipo->num_rows > 0) {
                    while($dataTipo = $respuestaTipo->fetch_assoc()) {
                      if ($dataTipo['tipo'] == 1) {
                        $dataTipoNombre = 'Pago por día';
                      }elseif ($dataTipo['tipo'] == 2) {
                        $dataTipoNombre = 'Pago por titular';
                      }
                    ?>
                    <option value="<?= $dataTipo["tipo"]; ?>" <?= (isset($tipo) && $tipo == $dataTipo["tipo"]) ? "selected" : ""; ?>><?= $dataTipoNombre; ?></option>
                    <?php 
                      }
                    }
                    ?>
                </select>
              </div> <!-- form-group -->
            </div> <!-- col-sm-4 -->
            <div class="col-sm-4">
              <div class="form-group">
                <label for="limiteInferior">LímiteInferior</label>
                <input type="number" class="form-control" name="limiteInferior" id="limiteInferior" step="1" min="1" value="<?= $limiteInferior; ?>" required>
              </div> <!-- form-group -->
            </div> <!-- col-sm-4 -->
          </div> <!-- row -->
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="limiteSuperior">LímiteSuperior</label>
                <input type="number" class="form-control" name="limiteSuperior" id="limiteSuperior" step="1" min="1" value="<?= $limiteSuperior; ?>" required>
              </div> <!-- form-group -->
            </div> <!-- col-sm-4 -->
            <div class="col-sm-4">
              <div class="form-group">
                <label for="valor">Valor</label>
                <input type="number" class="form-control" name="valor" id="valor" min="1" value="<?= $valor; ?>" required>
              </div> <!-- form-group -->
            </div> <!-- col-sm-4 -->
          </div> <!-- row -->
        </div> <!-- modal-body -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
          <button type="button" class="btn btn-primary" id="editar_valor"><i class="fa fa-check"></i> Guardar</button>
        </div>
      </div> <!-- modal-content -->
      <input type="hidden" name="id" id="id" value="<?= $id; ?> ">
    </form> <!-- form -->
  </div> <!-- modal-dialog -->
</div><!--  fade in -->

<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script type="text/javascript">
    jQuery.extend(jQuery.validator.messages, {step: "Por favor ingresa un número entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#modal_actualizar_valor').modal('show');
  });

</script>

<?php mysqli_close($Link); ?>


