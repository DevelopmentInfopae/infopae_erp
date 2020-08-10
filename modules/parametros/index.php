<?php
  $titulo = 'Parámetros Iniciales';
  include '../../header.php';

  $periodoActual = $_SESSION['periodoActual'];
  $consulta1 = "SELECT * FROM parametros;";
  $resultado1 = $Link->query($consulta1) or die ("Unable to execute query.". mysql_error($Link));
  if ($resultado1->num_rows > 0){
    $datos = $resultado1->fetch_assoc();
  }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" onclick="guardarParametros();">
        <i class="fa <?php if ($resultado1->num_rows > 0){ echo "fa-pencil"; } else { echo "fa-plus"; } ?>"></i> Guardar
      </a>
    </div>
  </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formParametros" action="" method="post">
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group col-xs-12">
                  <label for="nombre">Logotipo</label>
                  <div class="fileinput fileinput-new" data-provides="fileinput" style="width: 100%;">
                    <div class="fileinput-preview thumbnail img-banner" data-trigger="fileinput" style="width: inherit;">
                      <img class="img-responsive" <?php if (isset($datos['LogoETC']) && $datos['LogoETC'] != "") { ?> src="<?php echo $datos['LogoETC']; ?>" <?php } ?> alt="">
                    </div>
                    <div class="text-center">
                      <span class="btn btn-default btn-file"><span class="fileinput-new">seleccionar</span><span class="fileinput-exists">Cambiar</span><input type="file" name="foto" id="foto" accept="image/jpg, image/jpeg"></span>
                      <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
                    </div>
                  </div>
                </div>
                <div class="form-group col-xs-12">
                  <label for="nombre">Logo operador</label>
                  <div class="fileinput fileinput-new" data-provides="fileinput" style="width: 100%;">
                    <div class="fileinput-preview thumbnail img-banner" data-trigger="fileinput" style="width: inherit;">
                      <img class="img-responsive" <?php if (isset($datos['LogoOperador']) && $datos['LogoOperador'] != "") { ?> src="<?php echo $datos['LogoOperador']; ?>" <?php } ?> alt="">
                    </div>
                    <div class="text-center">
                      <span class="btn btn-default btn-file"><span class="fileinput-new">seleccionar</span><span class="fileinput-exists">Cambiar</span><input type="file" name="LogoOperador" id="LogoOperador" accept="image/jpg, image/jpeg, image/png"></span>
                      <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-8">
                <div class="row">
                  <div class="form-group col-sm-6">
                    <label for="nombre">Razón social</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?php if(isset($datos['Operador']) && $datos['Operador'] != '') { echo $datos['Operador']; }?>" required>
                  </div>

                  <div class="form-group col-sm-6">
                    <label for="numeroContrato">Número de Contrato</label>
                    <input type="text" class="form-control" name="numeroContrato" id="numeroContrato" value="<?php if(isset($datos['NumContrato']) && $datos['NumContrato'] != '') { echo $datos['NumContrato']; }?>" required>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-sm-6">
                    <label for="anio">Año de contrato</label>
                    <input type="text" class="form-control" value="<?php if(isset($datos['ano']) && $datos['ano'] != '') { echo $datos['ano']; } else { echo date('Y'); } ?>" readOnly disabled>
                  </div>

                  <div class="form-group col-sm-6">
                    <label for="departamento">Departamento</label>
                    <select class="form-control" name="departamento" id="departamento" required>
                      <option value="">Seleccione uno</option>
                      <?php
                        $consulta1= " SELECT * FROM departamentos WHERE id <> 0 ORDER BY nombre ASC ";
                        $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                        if($result1->num_rows > 0){
                          while($row1 = $result1->fetch_assoc()){ ?>
                            <option value="<?php echo $row1['id']; ?>"
                            <?php if(isset($datos['CodDepartamento']) && $datos['CodDepartamento'] == $row1['id']){ echo ' selected '; } ?>
                            ><?php echo $row1['nombre']; ?></option><?php
                          }
                        }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-sm-6">
                    <label for="municipio">Municipio</label>
                    <select class="form-control" name="municipio" id="municipio">
                      <option value="">Seleccione uno</option>
                      <?php
                        $municipios= "SELECT * FROM `ubicacion` WHERE CodigoDANE LIKE '%".$datos["CodDepartamento"]."%'";
                        $datos_municipios = $Link->query($municipios) or die ('Unable to execute query. '. mysqli_error($Link));
                        if($datos_municipios->num_rows > 0){
                          while($municipio = $datos_municipios->fetch_assoc()){ ?>
                            <option value="<?php echo $municipio['CodigoDANE']; ?>"
                            <?php if(isset($datos['CodMunicipio']) && $datos['CodMunicipio'] == $municipio['CodigoDANE']){ echo ' selected '; } ?>
                            ><?php echo $municipio['Ciudad']; ?></option><?php
                          }
                        }
                      ?>
                    </select>
                  </div>

                  <div class="form-group col-sm-6">
                    <label for="nombreEtc">ETC</label>
                    <input type="text" class="form-control" name="nombreEtc" id="nombreEtc" value="<?php if(isset($datos['NombreETC']) && $datos['NombreETC'] != '') { echo $datos['NombreETC']; }?>" placeholder="Ente territorial certificado" required>
                    <input type="hidden" name="id" id="id" value="<?php if(isset($datos['id']) && $datos['id']) { echo $datos['id']; } ?>">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-sm-4">
                <label for="cantidadCupos">Cantidad por cupos <i class="fa fa-question-circle" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Indique el número de cupos con el cuál se calculará los despachos de insumios."></i></label>
                <input type="number" class="form-control" name="cantidadCupos" id="cantidadCupos" min="1" value="<?php if(isset($datos['CantidadCupos']) && $datos['CantidadCupos'] != '') { echo $datos['CantidadCupos']; }?>" required>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="mesContrato">Mes de contrato</label>
                  <input type="number" id="mesContrato" class="form-control" name="mesContrato" value="<?php if (isset($datos['MesContrato']) && $datos['MesContrato'] != '') { echo $datos['MesContrato']; } ?>" <?php if (isset($datos['MesContrato']) && $datos['MesContrato'] != '') { echo 'readOnly'; } ?> required>
                </div>
              </div>
              <div class="form-group col-sm-4">
                <label for="nombre_representante_legal">Representate legal</label>
                <input type="text" name="nombre_representante_legal" id="nombre_representante_legal" class="form-control" value="<?php if (isset($datos['nombre_representante_legal']) && $datos['nombre_representante_legal'] != '') { echo $datos['nombre_representante_legal']; } ?>">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-sm-4">
                <label for="documento_representante_legal">Documento representante legal</label>
                <input type="text" name="documento_representante_legal" id="documento_representante_legal" class="form-control" value="<?php if (isset($datos['documento_representante_legal']) && $datos['documento_representante_legal'] != '') { echo $datos['documento_representante_legal']; } ?>">
              </div>
              <div class="form-group col-sm-4">
                <label for="NIT">NIT Operador</label>
                <input type="text" name="NIT" id="NIT" class="form-control" value="<?php if (isset($datos['NIT']) && $datos['NIT'] != '') { echo $datos['NIT']; } ?>">
              </div>
              <div class="form-group col-sm-4">
                <label for="ValorContrato">ValorContrato</label>
                <input type="number" name="ValorContrato" id="ValorContrato" class="form-control" value="<?php if (isset($datos['ValorContrato']) && $datos['ValorContrato'] != '') { echo $datos['ValorContrato']; } ?>">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-sm-4">
                <label>Permitir repitentes</label><br>
                <label>
                  <input type="radio" name="PermitirRepitentes" value="1" <?= $datos['PermitirRepitentes'] == 1 ? 'checked="checked"' : '' ?>>
                  Si
                </label>
                <label>
                  <input type="radio" name="PermitirRepitentes" value="0" <?= $datos['PermitirRepitentes'] == 0 ? 'checked="checked"' : '' ?>>
                  No
                </label>
              </div>
              <div class="form-group col-sm-4">
                <label for="email">E-mail</label>
                <input type="text" name="email" id="email" class="form-control" value="<?php if (isset($datos['email']) && $datos['email'] != '') { echo $datos['email']; } ?>">
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/parametros/js/parametros.js"></script>
<script type="text/javascript">
  jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

  $('.fileinput').fileinput();

   $('[data-toggle="tooltip"]').tooltip();

   $(document).ready(function(){
      $('input').iCheck({
         radioClass: 'iradio_square-green'
      });
   });

</script>
<?php mysqli_close($Link); ?>

</body>
</html>