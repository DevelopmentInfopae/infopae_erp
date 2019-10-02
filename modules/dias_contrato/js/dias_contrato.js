$(document).ready(function()
{
	// Captura de eventos DOM
	$(document).on('click', '#crearDiaContrato', crearDiasContrato);
	$(document).on('click', '#btnGuardarPlanillaDiasMes', crearPlanillaDiasMes);
	$(document).on('change', '#ciclo', function() { cargarMenus($(this).val()); });
	$(document).on('click', '#confirmarGuardarRegistrosMes', confirmarGuardarRegistrosMes);

	// Configuración del plugins toastr.
	toastr.options = {
     "closeButton": true,
     "debug": false,
     "progressBar": true,
     "preventDuplicates": false,
     "positionClass": "toast-top-right",
     "onclick": null,
     "showDuration": "400",
     "hideDuration": "1000",
     "timeOut": "2000",
     "extendedTimeOut": "1000",
     "showEasing": "swing",
     "hideEasing": "linear",
     "showMethod": "fadeIn",
     "hideMethod": "fadeOut"
   }

  // Configuración del plugin validate.
  jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

  // Cofigurar evento al cerrar ventana modal de formulario.
  $('#ventanaFormulario').on('hidden.bs.modal', function(e){ $('#frmCrearDiasContrato')[0].reset(); })
});

function calcularSemanaContrato(date)
{
	$.ajax({
		type: 'post',
		url: 'functions/fn_dias_contrato_calcular_semana_contrato.php',
		dataType: 'json',
		data:
		{
			dia : date.date(),
			semana : date.week(),
			mes : ((date.month())+1),
			diaSemana : date.weekday()
		},
		beforeSend:function() { $('#loader').fadeIn(); },
		success:function(data)
		{ console.log(data);
			if(data.estado == 1)
			{
				$('#loader').fadeOut();
				$('#dia').val(data.datos.dia);
				$('#mes').val(data.datos.mes);
				$('#semana').val(data.datos.semana);
				$('#semanaCompleta').val(data.datos.semanaCompleta);
				$('#diaSemana').val(date.weekday());
				$('#ciclo').val(data.datos.ciclo);
				if (data.datos.ciclo != '')
				{
					$('#ciclo').prop('disabled', true);
				}
				else
				{
					$('#ciclo').prop('disabled', false);
				}
				cargarMenus(data.datos.ciclo);
				$('#ventanaFormulario').modal();
			}
			else
			{
			 	Command: toastr.warning(
          data.mensaje,
          "Advertencia",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
			}
		},
		error:function(data){
			console.log(data);
			Command: toastr.error(
        'Al parecer existe un error en el servidor. Por favor comuníquese con el administrador del sitio Info PAE.',
        "Error",
        {
          onHidden : function(){ $('#loader').fadeOut(); }
        }
      );
		}
	});
}

function cargarMenus(ciclo)
{
	$.ajax({
		type: 'post',
		url: 'functions/fn_dias_contrato_cargar_menus.php',
		dataType: 'html',
		data:
		{
			ciclo : ciclo
		},
		beforeSend:function() { $('#loader').fadeIn(); },
		success:function(data)
		{
			$('#loader').fadeOut();
			$('#menu').html(data);
		},
		error:function(data){
			console.log(data);
			Command: toastr.error(
        'Al parecer existe un error en el servidor. Por favor comuníquese con el administrador del sitio Info PAE.',
        "Error",
        {
          onHidden : function(){ $('#loader').fadeOut(); }
        }
      );
		}
	});
}

function crearDiasContrato()
{
	if($('#frmCrearDiasContrato').valid())
	{
		$.ajax({
			type: 'post',
			url: 'functions/fn_dias_contrato_crear.php',
			data: {
				dia: $('#dia').val(),
				mes: $('#mes').val(),
				menu: $('#menu').val(),
				ciclo: $('#ciclo').val(),
				semana: $('#semana').val(),
				diaSemana: $('#diaSemana').val(),
				semanaCompleta: $('#semanaCompleta').val()
			},
			dataType: 'json',
			beforeSend: function() { $('#loader').fadeIn(); },
			success: function(data)
			{
				if (data.estado == 1)
				{
					Command: toastr.success(data.mensaje, "Guardado Exitoso", {
						onHidden: function()
						{
							dia = (($('#dia').val().length) == 1) ? '0'+$('#dia').val() : $('#dia').val();
							mes = (($('#mes').val().length) == 1) ? '0'+$('#mes').val() : $('#mes').val();

							$('#diasContrato').fullCalendar('renderEvent', {
			          title  : 'Ciclo: '+ $('#ciclo').val() +' - Menú '+ $('#menu').val(),
			          start  : $('#periodoActualCompleto').val()+'-'+ mes +'-'+dia
			        });

							$('#loader').fadeOut();
							$('#ventanaFormulario').modal('hide');
						}
					});
				}
				else
				{
					Command: toastr.warning(data.mensaje, 'Advertencia', { onHidden: function(){ $('#loader').fadeOut(); } });
				}
			},
			error: function(data)
			{
				console.log(data);
				Command: toastr.error( 'Al parecer existe un error en el servidor. Por favor comuníquese con el administrador del sitio Info PAE.', 'Error', {
	          onHidden : function(){ $('#loader').fadeOut(); }
	        }
	      );
			}
		});
	}
}

function confirmarGuardarRegistrosMes()
{
	var date = $('#diasContrato').fullCalendar('getDate');
	$('#ventanaConfirmar .modal-body p').html('¿Realmente desea guardar el mes actual?');
	$('#idAConfirmar').val(((date.month())+1));
	$('#ventanaConfirmar').modal();
}

function crearPlanillaDiasMes()
{
	// alert($('#idAConfirmar').val());
	$.ajax({
		type: 'post',
		url: 'functions/fn_dias_contrato_crear_planilla_dias_mes.php',
		data: {
			mes: $('#idAConfirmar').val()
		},
		dataType: 'json',
		beforeSend: function() { $('#loader').fadeIn(); },
		success: function(data)
		{ console.log(data);
			if (data.estado == 1)
			{
				Command: toastr.success(data.mensaje, "Guardado Exitoso", {
					onHidden: function()
					{
						$('#loader').fadeOut();
					}
				});
			}
			else
			{
				Command: toastr.warning(data.mensaje, 'Advertencia', { onHidden: function(){ $('#loader').fadeOut(); } });
			}
		},
		error: function(data)
		{
			console.log(data);
			Command: toastr.error( 'Al parecer existe un error en el servidor. Por favor comuníquese con el administrador del sitio Info PAE.', 'Error', {
          onHidden : function(){ $('#loader').fadeOut(); }
        }
      );
		}
	});
}