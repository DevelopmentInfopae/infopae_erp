<?php
  include '../../header.php';

  if ($permisos['entrega_complementos'] == "0") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit(); }

  set_time_limit (0);
  ini_set('memory_limit','6000M');
  $periodoActual = $_SESSION['periodoActual'];
  $titulo = 'Consulta resumida de entregas';
?>



<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Consulta resumida de entregas</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li class="active">
        <strong><?= $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" name="boton_abri_ventana_exportar_entregas" id="boton_abri_ventana_exportar_entregas"><i class="fa fa-file-excel-o"></i> Exportar</a>
    </div>
  </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form action="consulta_resumida_entregas.php" method="post" name="parametros" id="parametros">
            <?php
              //Se va a capturar el año actual para sacar la subcadena del periodo que permitira saber que tablas consultar de acuerdo al año.
              if (isset($_POST['annoinicial']) && $_POST['annoinicial']!= '') {
                $annoactual = $_POST['annoinicial'];
              } else {
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
              </div>

              <div class="col-sm-3 form-group">
                  <label for="municipio">Municipio</label>
                  <select id="municipio" name="municipio" onchange="buscar_instituciones();" class="form-control" required>
                  <?php
                    if (isset($departamento) && $departamento != "") {
                      $vsql = "select distinct ciudad, codigodane from ubicacion where ETC = 0 and codigodane like '$departamento%' order by ciudad asc";
			                if($_SESSION['perfil'] == "6"){
				               $rectorDocumento = $_SESSION['num_doc'];
				               $vsql = "SELECT ubicacion.ciudad as ciudad, ubicacion.CodigoDANE as codigodane from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where cc_rector = $rectorDocumento limit 1 ";
			                }
                      if ($_SESSION['perfil'] == "7") {
                        $documentoCoordinador = $_SESSION['num_doc'];
                        $vsql = "SELECT u.Ciudad as ciudad, u.CodigoDANE AS codigodane FROM ubicacion u LEFT JOIN sedes$periodoActual s ON s.cod_mun_sede = u.CodigoDANE WHERE id_coordinador = $documentoCoordinador LIMIT 1 ";
                      }
                      // exit(var_dump($vsql));
                      $result = $Link->query($vsql);
                  ?>
                    <option value="">TODOS</option>
                  <?php while($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo $row["codigodane"]; ?>" <?php if ((isset($_POST['municipio']) && $_POST['municipio'] == $row["codigodane"]) || $municipio_defecto["CodMunicipio"] == $row["codigodane"]) { echo ' selected '; } ?>><?php echo $row["ciudad"]; ?></option>
                  <?php
                        }
                    } else {
                  ?>
                    <option value="">TODOS</option>
                  <?php
                    }
                  ?>
                  </select>
              </div>

              <div class="col-sm-3 form-group">
                <label for="departamento">Institución</label>
                  <select id="institucion" name="institucion" onchange="buscar_sedes();" class="form-control" <?php if($_SESSION['perfil'] == 6){ ?>required <?php } ?>>
                  <?php
                    if (isset($_POST["municipio"]) && $_POST["municipio"] != "" || $municipio_defecto["CodMunicipio"]) {
                      $municipio = (isset($_POST["municipio"])) ? $_POST["municipio"] : $municipio_defecto["CodMunicipio"];
                      $vsql = "select distinct s.cod_inst, s.nom_inst from sedes".$periodoactual." s LEFT JOIN instituciones i ON s.cod_inst = i.codigo_inst where cod_mun_sede = '$municipio' ";

                      if($_SESSION['perfil'] == 6){
                        $vsql .= " and cc_rector = $rectorDocumento ";
                      } 
                      else if($_SESSION['perfil'] == "7"){
                        $vsql .= " AND id_coordinador = $documentoCoordinador ";
                      }

                      $vsql .= " order by nom_inst asc ";
                      $result = $Link->query($vsql);
                  ?>
                  <option value="">Seleccione</option>
                  
                  
                  <?php while($row = $result->fetch_assoc()) {  ?>
                  <option value="<?php echo $row["cod_inst"]; ?>" <?php if (isset($_POST['institucion']) && ($_POST['institucion'] == $row["cod_inst"]) ) { echo 'selected'; }  ?>   ><?php echo $row["nom_inst"]; ?></option>
                  <?php
                        }
                    } else {
                  ?>
                  <option value="">TODOS</option>
                  <?php
                    }
                  ?>
                </select>
              </div>

							<div class="col-sm-3 form-group">
								<label for="sede">Sede</label>
								<select id="sede" name="sede" onchange="buscar_estudiantes();" class="form-control">
									<?php
									if (isset($_POST["institucion"]) && $_POST["institucion"] != "") {
										$institucion = $_POST["institucion"];
                    if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != "") {
                      $codigoSedes = "";
                      $documentoCoordinador = $_SESSION['num_doc'];
                      $consultaCodigoSedes = "SELECT cod_sede FROM sedes$periodoActual WHERE id_coordinador = $documentoCoordinador;";
                      $respuestaCodigoSedes = $Link->query($consultaCodigoSedes) or die('Error al consultar el código de la sede ' . mysqli_error($Link));
                      if ($respuestaCodigoSedes->num_rows > 0) {
                        $codigoInstitucion = '';
                        while ($dataCodigoSedes = $respuestaCodigoSedes->fetch_assoc()) {
                          $codigoSedeRow = $dataCodigoSedes['cod_sede'];
                          $consultaCodigoInstitucion = "SELECT cod_inst FROM sedes$periodoActual WHERE cod_sede = $codigoSedeRow;";
                          $respuestaCodigoInstitucion = $Link->query($consultaCodigoInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
                          if ($respuestaCodigoInstitucion->num_rows > 0) {
                            $dataCodigoInstitucion = $respuestaCodigoInstitucion->fetch_assoc();
                            $codigoInstitucionRow = $dataCodigoInstitucion['cod_inst'];
                            if ($codigoInstitucionRow == $codigoInstitucion || $codigoInstitucion == '') {
                              $codigoSedes .= "'$codigoSedeRow'".",";
                              $codigoInstitucion = $codigoInstitucionRow; 
                            }
                          }
                        }
                      }
                      $codigoSedes = substr($codigoSedes, 0 , -1);
                      $condicionCoordinador = " AND cod_sede IN ($codigoSedes) ";
                    }

										$vsql = "select distinct cod_sede, nom_sede from sedes".$periodoactual." where cod_inst = '$institucion' $condicionCoordinador order by nom_sede asc";

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
                      <select name="mesinicial" id="mesinicial" onchange="actualizarmes();" class="form-control">
                      <?php
                        $vsql="SELECT TABLE_NAME as mes FROM information_schema.TABLES WHERE  table_schema = '$Database' AND   TABLE_NAME LIKE 'entregas_res_%'";
				                $result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link));
			                  while($row = $result->fetch_assoc()) {
					                $aux = $row['mes'];
					                $aux = substr($aux, 13, -2);
                      ?>
					              <option value="<?php echo $aux; ?>" <?php if (isset($_POST['mesinicial']) && $_POST['mesinicial'] == $aux ) {echo " selected "; } ?>>
                        <?php
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
  					            ?>
                        </option>
				              <?php
                        }
                      ?>
                      </select>
                    </div>

                    <div class="col-sm-6 nopadding">
                      <?php $periodoActualCompleto = $_SESSION['periodoActualCompleto']; ?>
                       <select name="annoinicial" id="annoinicial" onchange="actualizaranno();" class="form-control">
                          <option value="<?php echo $periodoActualCompleto; ?>" selected="selected"> <?php echo $periodoActualCompleto; ?> </option>
                       </select>
                    </div>
                    </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3 form-group">
      				  <div class="i-checks">
                  <label>
                    <input type="checkbox" value="" name="graficar" id="graficar" <?php if (isset($_POST['graficar'])) { echo ' checked '; } ?>> Graficar resultados
                  </label>
                </div>
              </div>

              <div class="col-sm-4 form-group">
                <div class="i-checks">
                  <label>
                    <input type="checkbox" value="" name="detallar" id="detallar" <?php if (isset($_POST['detallar'])) { echo ' checked '; } ?>> Detallar información de titulares de derecho
                  </label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3 form-group">
                <input type="hidden" id="municipio_nm" name="municipio_nm" value="">
                <input type="hidden" id="institucion_nm" name="institucion_nm" value="">
                <input type="hidden" id="sede_nm" name="sede_nm" value="">
                <input type="hidden" id="estudiante_nm" name="estudiante_nm" value="">
                <input type="hidden" id="resultado" name="resultado" value="mostrar">
                <button type="button" name="enviar" id="enviar" onclick="enviarForm();" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
              </div>
            </div>
          </form>
          <hr>
        <?php
          if (count($_POST)>0) {
            $mes = $_POST['mesinicial'];
            $annoinicial = $_POST['annoinicial'];
            $municipio = $_POST['municipio'];
            $institucion = $_POST['institucion'];
            $sede = $_POST['sede'];

            $dias="SELECT * FROM planilla_dias WHERE ano = '$annoinicial' AND mes = '$mes'";
            $result = $Link->query($dias) or die ('Unable to execute query. '. mysqli_error($Link));
            $rowDias = $result->fetch_assoc();

            $annoinicial = substr($annoinicial, 2, 2);
            include 'res_resumen.php';
            if (isset($_POST['graficar'])) { include 'res_grafica.php'; }
            if (isset($_POST['detallar'])) { include 'res_titulares.php'; }
          }// Termina el if que valida que se reciban los parametros post
        ?>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Ventana de formulario de exportación para la priorización -->
<div class="modal inmodal fade" id="ventana_formulario_exportar_entregas" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-upload fa-lg" aria-hidden="true"></i> Exportar ejecución  </h3>
      </div>
      <div class="modal-body">
        <form action="" name="formulario_exportar_entregas" id="formulario_exportar_entregas">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="mes_exportar">Mes</label>
                <select class="form-control" name="mes_exportar" id="mes_exportar" required>
                  <option value="">Selección</option>
                  <?php
                    $consultaMes = "SELECT distinct MES AS mes FROM planilla_semanas;";
                    $resultadoMes = $Link->query($consultaMes);
                    if($resultadoMes->num_rows > 0){
                      while($registros = $resultadoMes->fetch_assoc()) {
                  ?>
                      <option value="<?php echo $registros["mes"]; ?>"><?php echo $registros["mes"]; ?></option>
                  <?php
                      }
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="semana_exportar">Semana</label>
                <select class="form-control" name="semana_exportar" id="semana_exportar" required>
                  <option value="">Selección</option>
                </select>
              </div>
            </div>
            <?php if ($_SESSION['p_Municipio'] == "0"): ?>
              <div class="col-md-4">
              <div class="form-group">
                <label for="zona_exportar">Zona</label>
                <select class="form-control" name="zona_exportar" id="zona_exportar" required>
                  <option value="">Selección</option>
                   <?php
                    $consultaZona = "SELECT distinct Zona_Pae AS zona FROM sedes$periodoActual;";
                    $resultadoZona = $Link->query($consultaZona);
                    if($resultadoZona->num_rows > 0){
                      while($registros = $resultadoZona->fetch_assoc()) {
                  ?>
                      <option value="<?= $registros["zona"]; ?>"><?= $registros["zona"]; ?></option>
                  <?php
                      }
                    }
                  ?>
                </select>
              </div>
            </div>
            <?php endif ?>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-outline btn-sm" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" id="exportar_entregas">Aceptar</button>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/consultas/js/select_anidados.js"></script>
<script>
  $(document).ready(function () {
    $(document).on('click', '#boton_abri_ventana_exportar_entregas', function(){ abrir_ventana_exportar_entregas(); });
    $(document).on('change', '#mes_exportar', function(){ buscarSemanasMesExportar($(this)); });
    $(document).on('click', '#exportar_entregas', function(){ exportar_entregas(); });

    $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', });

    jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
  });

  // function enviarForm(){
  //   var municipio = '';
  //   var institucion = '';
  //   var sede = '';
  //   municipio = $('#municipio').val();
  //   institucion = $('#institucion').val();
  //   sede = $('#sede').val();
    
    
  //   var bandera = 0;
  //   alert(institucion);
  //   if(institucion == ""){
  //     alert("El campo institución es obligatorio");
  //     bandera++;
  //   }

  //   if(bandera == 0){
  //     $('#parametros').submit();
  //   }
  // }

  function actualizaranno(){
    aux = $('#annoinicial').val();
    $('#annofinal').val(aux);
  }

  function actualizarmes(){
    aux = $('#mesinicial').val();
    if(aux < 10){aux='0'+aux;}
    $('#mesfinal').val(aux);
  }

  function enviarForm(){
  	if($('#parametros').valid()){
  		$('#parametros').submit();
  	}
  }
</script>

<?php  if (count($_POST) > 0) { ?>
	<script type="text/javascript">
  	$(document).ready( function () {
  		$('#box-table-dr').DataTable({
  			order: [ 1, 'desc' ],
  			pageLength: 10,
  			responsive: true,
        buttons: [ {extend: 'excel', title: 'Entregas_resumidas', className: 'btnExportarExcel1'/*, exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } */} ],
        dom: 'lr<"containerBtn1"><"inputFiltro"f>tip<"html5buttons"B>',
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
      var botonAcciones = '<div class="dropdown pull-right">'+
                            '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                              'Acciones <span class="caret"></span>'+
                            '</button>'+
                            '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                              '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel1\').click();"><i class="fa fa-file-pdf-o"></i> Exportar </a></li>'+
                            '</ul>'+
                          '</div>';
      $('.containerBtn1').html(botonAcciones);
  	});
	</script>

  <?php if (isset($_POST['graficar'])) { ?>
    <!-- ChartJS-->
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/chartJs/Chart.min.js"></script>
    <script src="<?php echo $baseUrl; ?>/modules/consultas/js/consultas_graficos.js"></script>
  <?php } if (isset($_POST['detallar'])) { ?>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#box-table-d').DataTable({
          order: [ 1, 'desc' ],
          pageLength: 25,
          responsive: true,
          buttons: [{extend: 'excel', title: 'Titulares_de_derechos', className: 'btnExportarExcel2'/*, exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } */}],
          dom: 'lr<"containerBtn2"><"inputFiltro"f>tip<"html5buttons"B>',
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
          },
          initComplete: function() {
            var botonAcciones2 = '<div class="dropdown pull-right">'+
                            '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                              'Acciones <span class="caret"></span>'+
                            '</button>'+
                            '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                              '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel2\').click();"><i class="fa fa-file-pdf-o"></i> Exportar </a></li>'+
                            '</ul>'+
                          '</div>';
            $('.containerBtn2').html(botonAcciones2);
          }
        });
      });
    </script>
  <?php }
}
?>

<?php mysqli_close($Link); ?>

</body>
</html>