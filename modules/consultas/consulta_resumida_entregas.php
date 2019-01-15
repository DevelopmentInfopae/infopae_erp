<?php
include "../../config.php";
$titulo = 'Consulta resumida de entregas';
include '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
?>



<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Consulta resumida de entregas</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong>Consulta resumida de entregas</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!--
      <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
      <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
      <a href="#" onclick="actualizarDespacho()" target="_self" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar Cambios </a>
      -->
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form action="consulta_resumida_entregas.php" method="post" name="parametros" id="parametros">
            <?php
              //Se va a capturar el año actual para sacar la subcadena del periodo
              //que permitira saber que tablas consultar de acuerdo al año.

              if (isset($_POST['annoinicial']) && $_POST['annoinicial']!= '') {
                $annoactual = $_POST['annoinicial'];
              }
              else{
                $annoactual = $_SESSION['periodoActualCompleto'];
              }
              $_SESSION['annoactual'] = $annoactual;


              $periodoactual = substr($annoactual, 2, 2);

              $mesinicial = 'mm';
              if(isset($_POST["mesinicial"]) && $_POST["mesinicial"] != "" ){
                $mesinicial = $_POST["mesinicial"];
                if($mesinicial < 10){
                  $mesinicial = '0'.$mesinicial;
                }
              }
              $mesinicialConsulta = $mesinicial;

              $_SESSION['mesinicialConsulta'] = $mesinicialConsulta;
              $departamento = $_SESSION['p_CodDepartamento'];
            ?>


            <div class="row">

              <div class="col-sm-3 form-group">
                <label for="departamento">Departamento</label>
                <?php
                  $consulta = "select distinct departamento from ubicacion where ETC = 0 and codigodane like '$departamento%' order by departamento asc";
                  $result = $Link->query($consulta);
                ?>
                <select class="form-control" name="departamento" id="departamento" onchange="buscar_municipios();">
                  <?php while($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo $row["departamento"]; ?>"><?php echo $row["departamento"]; ?></option>
                  <?php } ?>
                </select>
              </div><!-- /.col -->

              <div class="col-sm-3 form-group">
                <label for="municipio">Municipio</label>
                <select id="municipio" name="municipio" onchange="buscar_instituciones();" class="form-control" required>
                  <?php
            if (isset($departamento) && $departamento != "") {

              $vsql = "select distinct ciudad, codigodane from ubicacion where ETC = 0 and codigodane like '$departamento%' order by ciudad asc";
							if($_SESSION['perfil'] == 6){
								$rectorDocumento = $_SESSION['num_doc'];
								$vsql = "SELECT ubicacion.ciudad as ciudad, ubicacion.CodigoDANE as codigodane from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where cc_rector = $rectorDocumento limit 1";
							}

              $result = $Link->query($vsql);

            ?>
              <option value="">TODOS</option>
            <?php
                while($row = $result->fetch_assoc()) {  ?>
                  <option value="<?php echo $row["codigodane"]; ?>"


                  <?php if (isset($_POST['municipio']) && ($_POST['municipio'] == $row["codigodane"]) ) {
                    echo ' selected ';
                  }  ?>
                  ><?php echo $row["ciudad"]; ?></option>
          <?php }
          } else{?> <option value="">TODOS</option> <?php }  ?>

            </select>


              </div><!-- /.col -->


              <div class="col-sm-3 form-group">
                <label for="departamento">Institución</label>
                <!-- onchange="buscar_sedes();" -->
                <select id="institucion" name="institucion" onchange="buscar_sedes();" class="form-control">
                             <?php
                          if (isset($_POST["municipio"]) && $_POST["municipio"] != "") {

                            $municipio = $_POST["municipio"];

                            $vsql = "select distinct cod_inst, nom_inst from sedes".$periodoactual." where cod_mun_sede = '$municipio' order by nom_inst asc";
                            $result = $Link->query($vsql);



                          ?>
                            <option value="">TODOS</option>
                          <?php
                               while($row = $result->fetch_assoc()) {  ?>

                          <option value="<?php echo $row["cod_inst"]; ?>" <?php if (isset($_POST['institucion']) && ($_POST['institucion'] == $row["cod_inst"]) ) {
                            echo 'selected';
                          }  ?>   ><?php echo $row["nom_inst"]; ?></option>

                              <?php }}

                          else{?> <option value="">TODOS</option> <?php }  ?>
                </select>
              </div><!-- /.col -->




							<div class="col-sm-3 form-group">
								<label for="sede">Sede</label>
								<select id="sede" name="sede" onchange="buscar_estudiantes();" class="form-control">
									<?php
									if (isset($_POST["institucion"]) && $_POST["institucion"] != "") {

										$institucion = $_POST["institucion"];

										$vsql = "select distinct cod_sede, nom_sede from sedes".$periodoactual." where cod_inst = '$institucion' order by nom_sede asc";

										$result = $Link->query($vsql);




										?>
										<option value="">TODOS</option>

										<?php




										while($row = $result->fetch_assoc()) {  ?>

											<option value="<?php echo $row["cod_sede"]; ?>" <?php  if (isset($_POST['sede']) && ($_POST['sede'] == $row["cod_sede"]) ) {
												echo ' selected ';
											} ?>   ><?php echo utf8_encode($row["nom_sede"]); ?></option>

										<?php }
									}

									else{?> <option value="">TODOS</option> <?php }  ?>
								</select>
							</div><!-- /.col -->





						</div>
						<div class="row">






<div class="col-sm-3 form-group">
  <label for="">Periodo Consulta</label>
   <div class="row compositeDate">
    <div class="col-sm-6 nopadding">



			<?php

			$vsql="SELECT TABLE_NAME as mes FROM information_schema.TABLES WHERE  table_schema = '$Database' AND   TABLE_NAME LIKE 'entregas_res_%'";
			?>
      <select name="mesinicial" id="mesinicial" onchange="actualizarmes();" class="form-control">
				<?php


				$result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link));
				while($row = $result->fetch_assoc()) {
					$aux = $row['mes'];
					$aux = substr($aux, 13, -2);

					?>


					<option value="<?php echo $aux; ?>" <?php if (isset($_POST['mesinicial']) && $_POST['mesinicial'] == $aux ) {echo " selected "; } ?>><?php



					switch ($aux) {
						case "01":
						echo "Enero";
						break;
						case "02":
						echo "Febrero";
						break;
						case "03":
						echo "Marzo";
						break;
						case "04":
						echo "Abril";
						break;
						case "05":
						echo "Mayo";
						break;
						case "06":
						echo "Junio";
						break;
						case "07":
						echo "Julio";
						break;
						case "08":
						echo "Agosto";
						break;
						case "09":
						echo "Septiembre";
						break;
						case "10":
						echo "Octubre";
						break;
						case "11":
						echo "Noviembre";
						break;
						case "12":
						echo "Diciembre";
						break;
					}







					?></option>

				<?php  } ?>









                       </select></div><!-- /.col -->


    <div class="col-sm-6 nopadding">
      <?php
$periodoActualCompleto = $_SESSION['periodoActualCompleto'];
?>
                 <select name="annoinicial" id="annoinicial" onchange="actualizaranno();" class="form-control">



                   <option value="<?php echo $periodoActualCompleto; ?>" selected="selected"> <?php echo $periodoActualCompleto; ?> </option>
















                      <?php
                      /*
                      $vsql="select distinct ano from planilla_dias";
                      $result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link));
                      while($row = $result->fetch_assoc()) { ?>

                        <option value="<?php echo $row['ano']; ?>" <?php if ($annoactual == $row['ano'] ) {echo " selected "; } ?>>
                      <?php


                      echo $row['ano']; ?>




                      </option>

                     <?php  }
                     */ ?>







                 </select>
    </div><!-- /.col -->




  </div>
</div><!-- /.col -->
  </div><!-- /.row -->


<div class="row">
  <div class="col-sm-6 form-group">
    <div class="row">
      <div class="col-sm-6 nopadding">

				  <div class="i-checks"><label> <input type="checkbox" value="" name="graficar" id="graficar" <?php if (isset($_POST['graficar'])) { echo ' checked '; } ?>> Graficar resultados </label></div>




<!-- <input type="checkbox" name="graficar" id="graficar" <?php if (isset($_POST['graficar'])) { echo ' checked '; } ?> /> Graficar resultados -->


      </div><!-- .col -->
      <div class="col-sm-6 nopadding">

<div class="i-checks"><label> <input type="checkbox" value="" name="detallar" id="detallar" <?php if (isset($_POST['detallar'])) { echo ' checked '; } ?>> Detallar información de titulares de derecho </label></div>
        <!-- <input type="checkbox" name="detallar" id="detallar" <?php if (isset($_POST['detallar'])) { echo ' checked '; } ?> /> Detallar información de titulares de derecho -->





      </div><!-- .col -->
    </div><!-- /.row -->
  </div><!-- /.col -->
</div><!-- /.row -->

  <div class="row">
    <div class="col-sm-3 form-group">
      <input type="hidden" id="municipio_nm" name="municipio_nm" value="">
      <input type="hidden" id="institucion_nm" name="institucion_nm" value="">
      <input type="hidden" id="sede_nm" name="sede_nm" value="">
      <input type="hidden" id="estudiante_nm" name="estudiante_nm" value="">
      <input type="hidden" id="resultado" name="resultado" value="mostrar">
      <button type="button" name="enviar" id="enviar" onclick="enviarForm();" class="btn btn-primary">Buscar</button>
    </div><!-- /.col -->
  </div><!-- /.row -->
</form>

<script>
  function enviarForm(){
    console.log('Validando el form para hacer la consulta.');
    var municipio = '';
    var institucion = '';
    var sede = '';
    municipio = $('#municipio').val();
    institucion = $('#institucion').val();
    sede = $('#sede').val();
    var bandera = 0;
    if(bandera == 0){
      $('#parametros').submit();
      console.log('Enviando el form para hacer la consulta.');
    }
  } //Termina la función para envíar el formulario
</script>

<?php //var_dump($_POST); ?>
<?php
  if (count($_POST)>0) {
    $mes = $_POST['mesinicial'];
    $annoinicial = $_POST['annoinicial'];
    $municipio = $_POST['municipio'];
    $institucion = $_POST['institucion'];
    $sede = $_POST['sede'];

    $dias="select * from planilla_dias where ano = '$annoinicial' and mes = '$mes'";
    $result = $Link->query($dias) or die ('Unable to execute query. '. mysqli_error($Link));
    $rowDias = $result->fetch_assoc();

    $annoinicial = substr($annoinicial, 2, 2);
    include 'res_resumen.php';
    if (isset($_POST['graficar'])) { include 'res_grafica.php'; }
    if (isset($_POST['detallar'])) {include 'res_titulares.php'; }

  }// Termina el if que valida que se reciban los parametros post
?>
















        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>


<!-- iCheck -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script> <script> $(document).ready(function () { $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', }); }); </script>



<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/consultas/js/select_anidados.js"></script>

<!-- Page-Level Scripts -->
<script>
function actualizaranno(){
	aux = $('#annoinicial').val();
	$('#annofinal').val(aux);
}

function actualizarmes(){
	aux = $('#mesinicial').val();
	if(aux < 10){aux='0'+aux;}
	$('#mesfinal').val(aux);
}

jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

function enviarForm(){
	if($('#parametros').valid()){
		$('#parametros').submit();
	}
}
</script>

<?php  if (count($_POST)>0) { ?>
	<script type="text/javascript">
	$(document).ready( function () {
		$('#box-table-dr').DataTable({
			order: [ 1, 'desc' ],
			pageLength: 25,
			responsive: true,
			oLanguage: {
				sLengthMenu: 'Mostrando _MENU_ registros por página',
				sZeroRecords: 'No se encontraron registros',
				sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
				sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
				sInfoFiltered: '(Filtrado desde _MAX_ registros)',
				sSearch:         'Buscar: ',
				oPaginate:{
					sFirst:    'Primero',
					sLast:     'Último',
					sNext:     'Siguiente',
					sPrevious: 'Anterior'
				}
			}
		});


		var anchoTabla = $('#box-table-dr').width();


	});
	</script>


  <?php if (isset($_POST['graficar'])) { ?>
    <!-- ChartJS-->
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/chartJs/Chart.min.js"></script>
    <script src="<?php echo $baseUrl; ?>/modules/consultas/js/consultas_graficos.js"></script> <?php
  } if (isset($_POST['detallar'])) { ?>
    <script type="text/javascript">
      $(document).ready( function () {
        $('#box-table-d').DataTable({
          order: [ 1, 'desc' ],
          pageLength: 25,
          responsive: true,
          oLanguage: {
           sLengthMenu: 'Mostrando _MENU_ registros por página',
           sZeroRecords: 'No se encontraron registros',
           sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
           sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
           sInfoFiltered: '(Filtrado desde _MAX_ registros)',
           sSearch:         'Buscar: ',
           oPaginate:{
             sFirst:    'Primero',
             sLast:     'Último',
             sNext:     'Siguiente',
             sPrevious: 'Anterior'
           }
          }
        });
      });
    </script>
    <?php
  }
} ?>















<?php mysqli_close($Link); ?>

</body>
</html>
