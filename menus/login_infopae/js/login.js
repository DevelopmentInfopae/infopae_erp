$(document).keypress(function(e) {
    if(e.which == 13) {
        doLogin();
    }
});


function doLogin() {
    var user=$("#username").val();
    var pass=$("#password").val();
    var periodoActual=$("#periodo").val();
    var bandera = 0;
    if(user == ''){
        alert('Debe escribir un nombre de usuario.');
        $("#username").focus();
        bandera++;
    }else if(pass == ''){
        alert('Debe escribir una contraseña');
        $("#password").focus();
        bandera++;
    }
    if(bandera == 0){
        pass=md5($("#password").val());
        $.ajax({
        	type: "POST",
        	url: "functions/fn_login.php",
            data:{
                user:user,
                pass:pass,
    			periodoActual:periodoActual
            },
        	success: function(i){
          		console.log(i);
                if (i==1 || i==5) {
                    window.location = "index.php";
                } else if (i==6){
                    window.location = "index_rector.php";
                } else if (i==4) {
                    window.location = "carga_de_archivos.php";
                } else if (i==0) {
                    alert('Usuario o contraseña incorrectos ');
                } else if (i=='nueva_clave') {
                    window.location = "cambiar_clave.php";
                }
                else{
                    $('#debug').html(i);
                }
        	}
        });
    }
}

var tecla;
function capturaTecla(e)
{
    if(document.all)
        tecla=event.keyCode; // ie
    else
    {
        tecla=e.which;   // Netscape/Firefox/Opera
    }
    if(tecla==13)
    {
        //alert('A pulsado la tecla enter');
		doLogin();
    }
}

//document.onkeydown = capturaTecla;
function iSubmitEnter(oEvento, oFormulario){
    var iAscii;
    if (oEvento.keyCode)
    iAscii = oEvento.keyCode;
    else if (oEvento.which)
    iAscii = oEvento.which;
    else
    return false;
    if (iAscii == 13) oFormulario.submit();
    return true;
}