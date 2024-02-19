
<?php
    include '../../header.php';

    if ($permisos['informes'] == "0" || $permisos['informes'] == "1") {
?>      <script type="text/javascript">
            window.open('<?= $baseUrl ?>', '_self');
        </script>
<?php
        exit(); 
    } else {
?>  <script type="text/javascript">
        const list = document.querySelector(".li_informes");
        list.className += " active ";
        const list2 = document.querySelector(".li_informeCoberturaAtendida");
        list2.className += " active ";
    </script>
<?php
    }
   
    $nameLabel = get_titles('informes', 'informeCoberturaAtendida', $labels);
    $titulo = $nameLabel;
    $periodoActual = $_SESSION['periodoActual'];
    $grupos_etarios = $_SESSION['cant_gruposEtarios'];

    // primero buscamos los meses activos de las planillas
    $respuestaMeses = $Link->query(" SELECT DISTINCT MES AS mes FROM planilla_semanas ");
    if ($respuestaMeses->num_rows > 0) {
        while ($dataMeses = $respuestaMeses->fetch_object()) {
            $meses[] = $dataMeses;
            $ultimoMes = $dataMeses->mes;
        }
        $nomMes = array("01" => "ENERO", 
                        "02" => "FEBRERO", 
                        "03" => "MARZO", 
                        "04" => "ABRIL", 
                        "05" => "MAYO", 
                        "06" => "JUNIO", 
                        "07" => "JULIO", 
                        "08" => "AGOSTO", 
                        "09" => "SEPTIEMBRE", 
                        "10" => "OCTUBRE", 
                        "11" => "NOVIEMBRE", 
                        "12" => "DICIEMBRE");                
    }

    /****** buscamos las semanas si existe un GET de mes*****/
    $semanas = [];
    if (isset($_GET['mes']) && $_GET['mes'] != '') {
        $respuestaSemanas = $Link->query("SELECT DISTINCT SEMANA AS semana FROM planilla_semanas WHERE MES ='" .$_GET['mes']. "'");
        if ($respuestaSemanas->num_rows > 0) {
            while ($dataSemanas = $respuestaSemanas->fetch_object()) {
                $semanas[] = $dataSemanas;
            }
        }
    }


//codigo para que no me muestre error de variable indefinida, ya que si esta indefinida es porque no han elejido sede en el formulario.
$sedeActual = isset($_GET['sede']) ? $_GET['sede'] : null;

    $guardoCompleCober = [];
    $respuestaComparacion = $Link->query("SELECT DISTINCT semana FROM sedes_cobertura WHERE cod_sede = '$sedeActual' ");

    if ($respuestaComparacion->num_rows > 0) {
        while ($dataComparacion = $respuestaComparacion->fetch_object()) {
            $guardoCompleCober[] = $dataComparacion->semana;
        }
    }


    //el alerta esta en la linea 573
  //----------------------alerta para verificar que si hay informacion en la semana seleccionada-------
    $activaralert = false;
    if (isset($_GET['semana']) && in_array($_GET['semana'], $guardoCompleCober)) {
        //si la semana que elijo esta en la cobertura, osea tiene informacion, me la trae
        $activaralert = false;
    } else if(isset($_GET['semana']) && $_GET['semana'] === '') {
       //si no elije semana me trae todo
        $activaralert = false;
    }else{
       //si la semana que elijo no esta en cobertura, no tiene info, entonces el alerta
        $activaralert = true;
    }





    /****** buscamos los días del get***********/ 
    // $dias = [];
    // if (isset($_GET['semana']) && $_GET['semana'] != '') {
    //     $consultaDias = "SELECT DISTINCT DIA AS dia, MES AS mes FROM planilla_semanas WHERE SEMANA = '" .$_GET['semana']. "'"; 
    //     $respuestaDia = $Link->query($consultaDias);
    //     if ($respuestaDia->num_rows > 0) {
    //         while ($dataDia = $respuestaDia->fetch_object()) {
    //             $dias[] = $dataDia;
    //         }
    //     }
    // }

    /***** Manejo de municipios *****/
    $municipios = [];
    $consultaMunicipio = " SELECT codigoDANE, Ciudad FROM ubicacion WHERE 1=1 ";
    if ($_SESSION['p_Municipio'] != "0") {
        $consultaMunicipio .= " AND codigoDANE = '" .$_SESSION['p_Municipio']. "' ";
    }else{
        $consultaMunicipio .= " AND codigoDANE LIKE '" .$_SESSION['p_CodDepartamento']. "%' ";
    }
    $respuestaMunicipio = $Link->query($consultaMunicipio) or die ('Error consultando municipios');
    if ($respuestaMunicipio->num_rows > 0) {
        while ($dataMunicipio = $respuestaMunicipio->fetch_object()) {
            $municipios[] = $dataMunicipio;
        }
    }

    /**** Manejo complementos ****/
    $complementos = [];
    if (isset($_GET['semana']) && $_GET['semana'] != '') {
        $consultaComplementos = " SELECT CODIGO FROM tipo_complemento WHERE ValorRacion > 0 ORDER BY CODIGO ";
        $respuestaComplementos = $Link->query($consultaComplementos) or die ('Error consultado complementos Ln 85');
        if ($respuestaComplementos->num_rows > 0) {
            while ($dataComplementos = $respuestaComplementos->fetch_object()) {
                $complementos[] = $dataComplementos;
            }
        }
    }

    /****** Manejo de rutas ********/
    $rutas = [];
    $consultaRutas = " SELECT ID, Nombre FROM rutas ";
    $respuestaRutas = $Link->query($consultaRutas) or die ('Error consultado las rutas Ln 96');
    if ($respuestaRutas->num_rows > 0) {
        while ($dataRutas = $respuestaRutas->fetch_object()) {
            $rutas[] = $dataRutas;
        }
    }

    /****** Manejo de tipo de alimentos ********/
    // $tipoAlimentos = [];
    // $consultaTipoAlimentos = " SELECT Id, Descripcion 
    //                             FROM tipo_despacho 
    //                             WHERE Id IN ( SELECT DISTINCT(TipoDespacho) FROM productos$periodoActual ) AND id != 4
    //                             ORDER BY Id DESC ";
    // $respuestaTipoAlimentos = $Link->query($consultaTipoAlimentos) or die ('Error consultado el tipo de alimento Ln 107');
    // if ($respuestaTipoAlimentos->num_rows > 0) {
    //     while ($dataTipoAlimentos = $respuestaTipoAlimentos->fetch_object()) {
    //         $tipoAlimentos[] = $dataTipoAlimentos;
    //     }
    // }

    /****** Manejo de instituciones ************/
    $instituciones = [];
    if (isset($_GET['municipio']) && $_GET['municipio'] != '') {
        $consultaInst = " SELECT codigo_inst, nom_inst FROM instituciones WHERE cod_mun = '" .$_GET['municipio']. "'";
        $respuestaInst = $Link->query($consultaInst) or die ('Error consultado las instituciones Ln 118');
        if ($respuestaInst->num_rows > 0) {
            while ($dataInst = $respuestaInst->fetch_object()) {
                $instituciones[] = $dataInst;
            }
        }
    } 

    /********** Manejo de sedes **********/
    $sedes = [];
    if (isset($_GET['institucion']) && $_GET['institucion'] != '') {
        $consultaSedes = " SELECT cod_sede, nom_sede FROM sedes$periodoActual WHERE cod_inst = " .$_GET['institucion']." ";
        if (isset($_GET['sector']) && $_GET['sector'] != '') {
            $consultaSedes .= " AND sector = " .$_GET['sector']. " ";
        }
        $respuestaSedes = $Link->query($consultaSedes) or die ('Error al consultar las sedes Ln 130');
        if ($respuestaSedes->num_rows > 0) {
            while ($dataSedes = $respuestaSedes->fetch_object()) {
                $sedes[] = $dataSedes;
            }
        }
    }

    /***** Manejo grupos etarios ******/
    $consultaGruposE = " SELECT ID, equivalencia_grado FROM grupo_etario ";
    $respuestaGruposE = $Link->query($consultaGruposE) or die('Err consulta los grupos etarios LN 145');
    if ($respuestaGruposE->num_rows > 0) {
        while ($dataGruposE = $respuestaGruposE->fetch_object()) {
            $gruposE[$dataGruposE->ID] = $dataGruposE->equivalencia_grado; 
        }
    }

    $colspan = ($grupos_etarios * 2) + 2;
?>
<style>
    .my-class-color{
        color : #16987e;
        font-weight: bold;
    }
    .my-class-total{
        color : #016090;
        background-color: #F9FADF;
        font-weight: bold;
    }
</style>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading contendor_titulos">
	<div class="col-md-6 col-lg-8">
		<h1><strong><?= $nameLabel; ?></strong></h1>
		<ol class="breadcrumb">
		  	<li>
				<a href="<?php echo $baseUrl; ?>">Inicio</a>
		  	</li>
		  	<li class="active">
				<strong><?= $nameLabel ?></strong>
		  	</li>
		</ol>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight contenedorParametros">
    <div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-sm-11">
                            <h2 style="display:inline;">Parámetros de Consulta</h2>
                        </div>
                        <div class="col-sm-1" id="father">
                            <div class="" id="loaderAjax">
                                <i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i>
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>   
				<div class="ibox-content contentBackground">
					<form class="col-lg-12" action="cobertura.php" name="formInformeProyeccion" id="formInformeProyeccion" method="GET" >
						<div class="row">
                            <div class="col-md-4 col-sm-12 form-group form-mes">
                                <label for="mes">Mes*</label>
                                <select name="mes" id="mes" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php
                                        foreach ($meses as $key => $value) {
                                    ?>
                                            <option value="<?= $value->mes ?>" <?= (isset($_GET['mes']) && $_GET['mes'] == $value->mes) ? 'selected' : '' ?> ><?= $nomMes[$value->mes] ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-4 col-sm-12 form-group form-semana">
                                <label for="semana">Semana*</label>
                                <select name="semana" id="semana" class="form-control" required>
                                    <option value="">Seleccione...</option>    
                                    <?php
                                        foreach ($semanas as $key => $value) {
                                    ?>
                                            <option value="<?= $value->semana ?>" <?= (isset($_GET['semana']) && $_GET['semana'] == $value->semana) ? 'selected' : '' ?> ><?= $value->semana ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-4 col-sm-12 form-group form-complementos">
                                <label for="complementos">Complementos*</label>
                                <div id="complementos">
                                    <?php $aux=-1 ?>
                                    <?php  foreach($complementos as $keyC => $valueC): $aux++; ?>
                                        <div class="complemento">
                                            <label>
                                                <input  type="checkbox" 
                                                        class="complemento i-checks" 
                                                        id="complemento<?= $aux; ?>" 
                                                        name="complemento<?= $aux; ?>" 
                                                        value="<?= $valueC->CODIGO; ?>" 
                                                        style="margin-bottom: 5px;" 
                                                        <?= (isset($_GET['complemento'.$aux]) && $_GET['complemento'.$aux] != '') ? 'checked' : '' ?>
                                                >
                                                <?= $valueC->CODIGO  ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            

                        </div><!--  row -->

                        <div class="row">




                        <div class="col-md-4 col-sm-12 form-group form-municipio">
								<label for="municipio">Municipio</label>
                                <select name="municipio" id="municipio" class="form-control">
                                       
                                    <?php
                                        foreach ($municipios as $keyM => $valueM) {
                                    ?>
                                            <option value="<?= $valueM->codigoDANE ?>" 
                                                    <?= (isset($_GET['municipio']) && $_GET['municipio'] == $valueM->codigoDANE) ? 'selected' : '' ?> 
                                            ><?= $valueM->Ciudad ?>
                                            </option>
                                    <?php
                                        }
                                    ?> 
                                </select>
							</div>
                            

                            <div class="col-md-4 col-sm-12 form-group">
                                <label for="ruta">Ruta</label>
                                
                                <select name="ruta" id="ruta" class="form-control">
                                    <option value="">Seleccione...</option>    
                                    <?php
                                        foreach ($rutas as $keyR => $valueR) {
                                    ?>
                                            <option value="<?= $valueR->ID ?>" 
                                                    <?= (isset($_GET['ruta']) && $_GET['ruta'] == $valueR->ID) ? 'selected' : '' ?> 
                                            ><?= $valueR->Nombre ?>
                                            </option>
                                    <?php
                                        }
                                    ?> 
                                </select>


                            </div>

                            <div class="col-md-4 col-sm-12 form-group">
                                <label for="sector">Sector</label>
                                <select name="sector" id="sector" class="form-control">
                                    <option value="">Seleccione...</option><!--     rural = 1, urbano = 2 -->
                                    <option value="1" <?= (isset($_GET['sector']) && $_GET['sector'] == 1) ? 'selected' : '' ?>> Rural </option>        
                                    <option value="2" <?= (isset($_GET['sector']) && $_GET['sector'] == 2) ? 'selected' : '' ?>> Urbano </option>        
                                </select>
                            </div>
                        </div><!--  row -->
                        
                        <div class="row">
                            <div class="col-md-4 col-sm-12 form-group">
                                <label for="institucion">Institución</label>
                                <select name="institucion" id="institucion" class="form-control">
                                    <option value="">Seleccione...</option>    
                                    <?php
                                        foreach ($instituciones as $keI => $valueI) {
                                    ?>
                                            <option value="<?= $valueI->codigo_inst ?>" 
                                                    <?= (isset($_GET['institucion']) && $_GET['institucion'] == $valueI->codigo_inst) ? 'selected' : '' ?> 
                                            ><?= $valueI->nom_inst ?>
                                            </option>
                                    <?php
                                        }
                                    ?> 
                                </select>
                            </div>

                            <div class="col-md-4 col-sm-12 form-group">
                                <label for="sede">Sede</label>
                                <select name="sede" id="sede" class="form-control">
                                    <option value="">Seleccione...</option>    
                                    <?php
                                        foreach ($sedes as $keS => $valueS) {
                                    ?>
                                            <option value="<?= $valueS->cod_sede ?>" 
                                                    <?= (isset($_GET['sede']) && $_GET['sede'] == $valueS->cod_sede) ? 'selected' : '' ?> 
                                            ><?= $valueS->nom_sede ?>
                                            </option>
                                    <?php
                                        }
                                    ?> 
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-sm-12 form-group">
                                <button class="btn_generates" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong><i class="fa fa-search"></i> Buscar</strong></button>
                            </div>
                        </div>
                    </form>        
                </div><!--  ibox-content -->
            </div><!--  ibox -->
        </div><!--  col-lg-12 -->
    </div><!--  row -->
</div><!--  wrapper -->

<?php if(isset($_GET['mes']) && $_GET['mes']!= ''): ?>
    <div class="wrapper wrapper-content animated fadeInRight contenedor_results" id='date_table' >
        <div class="row">
            <div class="col-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content contentBackground">		
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered  selectableRows" id="box-table-movimientos" >
                                <thead>
                                    <tr>
                                        <th rowspan='2' class="text-center">INSTITUCIÓN</th>
                                        <th rowspan='2' class="text-center">SEDE</th>
                                        <th rowspan='2' class="text-center">SECTOR</th>
                                        <th rowspan='2' class="text-center">COMPLEMENTO</th>
                                    </tr>
                             
                                </thead>
                                <br>
                                <tbody id="tbodyInformeProyecciones">
                                    
                                </tbody>
                                <tfoot>
                                    <tr style="height: 4em;">
                                        <th class="text-center">En espera de datos.</th>
                                        <th class="text-center">En espera de datos.</th>
                                        <th class="text-center">En espera de datos.</th>
                                        <th class="text-center">En espera de datos.</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div><!-- /.ibox-content -->
                </div><!-- /.ibox float-e-margins -->
            </div><!-- /.col-lg-12 -->
        </div><!-- /.row -->
    </div><!-- /.wrapper wrapper-content animated fadeInRight -->
<?php endif; ?>

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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<!--  -->
<script type="text/javascript">
    $(document).ready(function() {
        var buttonCommon = {
            exportOptions: {
                format: {
                    body: function ( data, row, column, node ) {
                        if (column > 7) { 
                            if (data - Math.floor(data) == 0) { 
                                return data;
                            } else { console.log(data)
                                return parseFloat(data.replace( ',', '.' ));
                            }
                        }else{
                            return data;
                        }
                    }
                }
            }
        };
        
        $('select').select2();
        $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
        toastr.options = { 
            newestOnTop: true, 
            closeButton: false, 
            progressBar: true, 
            preventDuplicates: false, 
            showMethod: 'slideDown', 
            timeOut: 2500, 
	    };

        // manejo de la semana
        $('#mes').on('change', function(){ 
            get_week($(this).val());
            $('#dias').html('');  
        })

        // manejo días
        $('#semana').on('change', function(){
            get_days($('#mes').val(), $(this).val());
            get_complementos($('#mes').val(), $(this).val());
        })

        // manejo de municipios
        $('#municipio').on('change', function(){
    		if($('#municipio').val() != ''){
                $('#ruta').select2('val', '');
                $('#ruta').prop('required', false);
                $('#ruta').prop( "disabled", true );
                $('#institucion').prop( "disabled", false );
                $('#sede').prop( "disabled", false );
                get_institutions($(this).val(), $('#sector').val())
		    }else{
                $('#ruta').prop( "disabled", false );
                $('#institucion').select2('val', '');
                $('#institucion').prop( "disabled", true );
                $('#sede').select2('val', '');
                $('#sede').prop( "disabled", true );	
		    }
        })
        if ($('#municipio').val() != '') {
            $('#ruta').select2('val', '');
            $('#ruta').prop('required', false);
            $('#ruta').prop( "disabled", true );
            $('#institucion').prop( "disabled", false );
            $('#sede').prop( "disabled", false );
        }

        // manejo de rutas
        $('#ruta').on('change', function(){
    		if($('#ruta').val() != ''){
                $('#municipio').select2('val', '');
                $('#municipio').prop('required', false);
                $('#municipio').prop( "disabled", true );
                $('#institucion').prop( "disabled", true );
                $('#sede').prop( "disabled", true );
		    }else{
                $('#municipio').prop( "disabled", false );
                $('#institucion').prop( "disabled", false );
                $('#sede').prop( "disabled", false );	
		    }
        })
        if ($('#ruta').val() != '') {
            $('#municipio').select2('val', '');
            $('#municipio').prop('required', false);
            $('#municipio').prop( "disabled", true );
            $('#institucion').prop( "disabled", true );
            $('#sede').prop( "disabled", true );
        }

        $('#sector').on('change', function(){
            $('#institucion').select2('val', '');
            get_institutions($('#municipio').val(), $(this).val())
        })
        
        $('#institucion').on('change', function(){
            $('#sede').select2('val', '');
            if ($(this).val() != '') {
                get_sede($(this).val(), $('#sector').val());
            }
        })

        // validamos y enviamos el formulario
        $('#btnBuscar').click(function(){
            $('#formInformeProyeccion').submit();
        })

        bandera = 0;
        var diasActuales = new Array();
        var complements = new Array();
            
        // manejo de mes formulario 
        if($('#mes').val() == ''){
			bandera++;
			Command: toastr.warning("Debe seleccionar un <strong>mes</strong> para realizar el informe.", "No hay mes seleccionado.", {onHidden : function(){}});
    		$('#mes').select2('open').select2('close');
            $('#mes').parent('.form-mes').find('.select2-selection--single').css('border-color', '#FF5252')
		}else{
            var mes = $('#mes').val();
            $('#mes').parent('.form-mes').find('.select2-selection--single').css('border-color', '#e7eaec')
        }

        // manejo de semana
        if (bandera == 0) {
                var semana = $('#semana').val();
                $('#semana').parent('.form-semana').find('.select2-selection--single').css('border-color', '#e7eaec')
        }
 

//acá el alerta de si la semana seleccionada coincide con alguna de sedes_cobertura y por ende tiene informacion necesaria
// arriba en la linea 70 esta la validacion 
// Verifica el valor de la variable en JavaScript
let activarAlert = <?php echo json_encode($activaralert)?>;

if (bandera == 0) {
    if (activarAlert) {
    Command: toastr.warning("La <strong>semana</strong> que elejiste no tiene informacion.", 
    "Elije otro mes y/o otra semana.", {onHidden : function(){}});
    return
}
}


        // manejo de dias
        // if (bandera == 0) {
        //     $('#dias .dia:checked').each(function() {
		// 	    var aux = $(this).val();
		// 	    diasActuales.push(aux);
		//     });
		//     if (diasActuales.length == 0) {
		// 	    bandera++;
		// 	    Command: toastr.warning("Debe seleccionar al menos un <strong>día</strong> para realizar el informe.", "No hay día seleccionado.", {onHidden : function(){}});
    	// 	    $('#dias').css('border', '1px solid #FF5252');
		//     }else{
        //         $('#dias').css('border', '1px solid #e5e6e7');
        //     }
        // }

        // manejo de complementos
        if (bandera == 0) {
            $('#complementos .complemento:checked').each(function() {
			    var aux = $(this).val();
			    complements.push(aux);
		    });
        }



        if (bandera == 0) {
            if($('#municipio').val() == '' && $('#ruta').val() == ''){
                bandera++;
                Command: toastr.warning("Debe seleccionar un <strong>municipio o ruta</strong> para realizar el informe.", "No hay municipio ó ruta seleccionada.", {onHidden : function(){}});
                $('#municipio').select2('open').select2('close');
                $('#municipio').parent('.form-municipio').find('.select2-selection--single').css('border-color', '#FF5252')
            }else{
                if ($('#municipio').val() != '') {
                    var municipio = $('#municipio').val();
                    var ruta = '';
                }
                if ($('#ruta').val() != '') {
                    var municipio = '';
                    var ruta = $('#ruta').val();
                }
                $('#semana').parent('.form-semana').find('.select2-selection--single').css('border-color', '#e7eaec')
            }
        }

      
  
 
   


        if (bandera == 0) {
            datosForm = {
                "mes" : mes,
                "semana" : semana,
                "complementos" : complements,
                "municipio" : municipio,
                "ruta" : ruta,
                "sector" : $('#sector').val(),
                "institucion" : $('#institucion').val(),
                "sede" : $('#sede').val()
            }
                console.log(datosForm);

            datos = { "data" : datosForm}
        $.ajax({
		    type: "POST",
		    url: "functions/fn_get_datatable.php",
		    data: datos,
		    beforeSend: function(){
			    $('#loaderAjax').fadeIn();
		    },
	    })
	    .done(function(data){
            console.log(data);
		    $('#semana').select2('destroy');
            $('#semana').html(data);
		    $('#semana').select2();
        })
	    .fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Error:", textStatus, errorThrown);
            console.log("Response:", jqXHR.responseText);
        })

	    .always(function(){
		    $('#loaderAjax').fadeOut();
	    });
            // dataset1 = $('#box-table-movimientos').DataTable({
            //     ajax: {
            //         method: 'POST',
            //         url: 'functions/fn_get_datatable.php',
            //         data:{
            //             datos: datosForm
            //         }
            //     },
            // })
        
        }
    })

    function get_week(mes){
        datos = { "mes" : mes}
        $.ajax({
		    type: "POST",
		    url: "functions/fn_get_week.php",
		    data: datos,
		    beforeSend: function(){
			    $('#loaderAjax').fadeIn();
		    },
	    })
	    .done(function(data){ 
		    $('#semana').select2('destroy');
            $('#semana').html(data);
		    $('#semana').select2();
        })
	    .fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Error:", textStatus, errorThrown);
            console.log("Response:", jqXHR.responseText);
        })

	    .always(function(){
		    $('#loaderAjax').fadeOut();
	    });
    } 

    function get_days(mes, semana){
        datos = { "mes" : mes, "semana" : semana}
        $.ajax({
		    type: "POST",
		    url: "functions/fn_get_days.php",
		    data: datos,
		    beforeSend: function(){
			    $('#loaderAjax').fadeIn();
		    },
	    })
	    .done(function(data){ 
            $('#dias').html(data);
            $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
        })
	    .fail(function(){ console.log(data) })
	    .always(function(){
		    $('#loaderAjax').fadeOut();
	    });
    } 

    function get_complementos(mes, semana){
        datos = { "mes" : mes, "semana" : semana}
        $.ajax({
		    type: "POST",
		    url: "functions/fn_get_complementos.php",
		    data: datos,
		    beforeSend: function(){
			    $('#loaderAjax').fadeIn();
		    },
	    })
	    .done(function(data){ 
            $('#complementos').html(data);
            $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
        })
	    .fail(function(){ console.log(data) })
	    .always(function(){
		    $('#loaderAjax').fadeOut();
	    });
    } 

    function get_institutions(municipio, sector){
        datos = { "municipio" : municipio, "sector" : sector }
        $.ajax({
		    type: "POST",
		    url: "functions/fn_get_institutions.php",
		    data: datos,
		    beforeSend: function(){
			    $('#loaderAjax').fadeIn();
		    },
	    })
	    .done(function(data){ 
            $('#institucion').html(data);
        })
	    .fail(function(){ console.log(data) })
	    .always(function(){
		    $('#loaderAjax').fadeOut();
	    });
    } 
    
    function get_sede(institucion, sector){
        datos = { "institucion" : institucion, "sector" : sector }
        $.ajax({
		    type: "POST",
		    url: "functions/fn_get_sede.php",
		    data: datos,
		    beforeSend: function(){
			    $('#loaderAjax').fadeIn();
		    },
	    })
	    .done(function(data){ 
            $('#sede').html(data);
        })
	    .fail(function(){ console.log(data) })
	    .always(function(){
		    $('#loaderAjax').fadeOut();
	    });
    }
    
</script>

