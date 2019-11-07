$(document).ready(function(){
	$.fn.datepicker.dates['en'] = {
	    days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
	    daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab", "Dom"],
	    daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
	    months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	    monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
	};
	$(".datepicker").datepicker();
	$('input').iCheck({ checkboxClass: 'icheckbox_square-green ' });

	$('#mes_fin').val($('#mes_inicio').val());
	$('#nomMesFin').val($('#mes_inicio option:selected').text());

	$('#fecha_inicio_elaboracion').trigger('change');
	// $('#fecha_fin_elaboracion').datepicker('setStartDate', $('#fecha_inicio_elaboracion').val());
	// $('#fecha_inicio_elaboracion').datepicker('setEndDate', $('#fecha_fin_elaboracion').val());
});

$('#fecha_inicio_elaboracion').on('change', function(){

	var fecha_desde = $(this).val();
	var fecha = fecha_desde.split('-');
	var ultimoDia = new Date(fecha[0], fecha[1], 0);

	$('#fecha_fin_elaboracion').datepicker('setStartDate', $(this).val());
	$('#fecha_fin_elaboracion').datepicker('setEndDate', fecha[0]+'-'+fecha[1]+'-'+ultimoDia.getDate());
	$('#fecha_fin_elaboracion').datepicker('setDate', fecha[0]+'-'+fecha[1]+'-'+ultimoDia.getDate());
});

/*$('#fecha_fin_elaboracion').on('change', function(){
	$('#fecha_inicio_elaboracion').datepicker('setEndDate', $(this).val());
});*/

$('#tipo_documento').on('change', function(){

	$('#loader').fadeIn();

	var tdoc = $("#tipo_documento option:selected").text();

	$.ajax({
	   type: "POST",
	   url: "functions/fn_trazabilidad_obtener_responsables.php",
	   data: { "tipo_documento" : tdoc },
	   beforeSend: function(){},
	   success: function(data){
	     $('#proveedor').html(data);
	     $('#loader').fadeOut();
	   }
	 });

});

$('#tipo_filtro').on('change', function(){

	$('#loader').fadeIn();

	mesinicio = $('#mes_inicio').val();

	var filtro = $(this).val();
		$('#divBodegas').css('display', 'none');
		$('#divConductores').css('display', 'none');
		$('#divProductos').css('display', 'none');
		$('#divGrupoEtario').css('display', 'none');
		$('#divTipoComplemento').css('display', 'none');
		$('#divFechaVencimiento').css('display', 'none');

	if (filtro == "1") {
		$.ajax({
		    type: "POST",
		    url: "functions/fn_trazabilidad_obtener_bodegas.php",
		    beforeSend: function(){},
		    success: function(data){
		      $('#bodegas').html(data);
		    }
		  });
		$('#divBodegas').css('display', '');
	} else if (filtro == "2") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$.ajax({
		    type: "POST",
		    url: "functions/fn_trazabilidad_obtener_conductores.php",
		    data : {"mestabla" : mesinicio},
		    beforeSend: function(){},
		    success: function(data){
		      $('#conductor').html(data);
		    }
		  });
		$('#divConductores').css('display', '');
	} else if (filtro == "3") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$.ajax({
		    type: "POST",
		    url: "functions/fn_trazabilidad_obtener_productos.php",
		    data : {"mestabla" : mesinicio},
		    beforeSend: function(){},
		    success: function(data){
		      $('#producto').html(data);
		    }
		  });
		$('#divProductos').css('display', '');
	} else if (filtro == "4") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$.ajax({
		    type: "POST",
		    url: "functions/fn_trazabilidad_obtener_grupo_etarios.php",
		    beforeSend: function(){},
		    success: function(data){
		      $('#grupo_etario').html(data);
		    }
		  });
		$('#divGrupoEtario').css('display', '');
	} else if (filtro == "5") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$('#divTipoComplemento').css('display', '');
	} else if (filtro == "6") {
		fechaDe(2);
		$('#fecha_de').val(2);
		$('#divFechaVencimiento').css('display', '');
	}

	setTimeout(function() {
		$('#loader').fadeOut();
				}, 1000);
});

$('#mes_inicio').on('change', function(){
	$('#mes_fin').val($(this).val());
	$('#nomMesFin').val($('#mes_inicio option:selected').text());
/*	$.ajax({
	   type: "POST",
	   url: "functions/fn_trazabilidad_obtener_municipios.php",
	   data: { "mes_tabla" : $('#mes_inicio').val() },
	   beforeSend: function(){},
	   success: function(data){
	     $('#municipio').html(data);
	     $('#loader').fadeOut();
	   }
	 });*/
});

$('#fecha_de').on('change', function(){
	fechaDe($(this).val());
});

function fechaDe(num){
	if (num==1) {
		$('#fechaElaboracion').css('display', '');
		$('#fechaDiasDespachos').css('display', 'none');
	} else if (num==2) {
		$('#fechaElaboracion').css('display', 'none');
		$('#fechaDiasDespachos').css('display', '');
	}
}

$('#dia_inicio').on('change', function(){

	if ($(this).val() <= 30) {
		$('#dia_fin').val(parseInt($(this).val())+1);
	} else if ($(this).val() == 31) {
		$('#dia_fin').val($(this).val());
	}
});