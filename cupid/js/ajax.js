


function objetoAjax(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
 
	try {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	} catch (E) {
		xmlhttp = false;
	}
}
 
if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	  xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}
 
//Función para recoger los datos del formulario y enviarlos por post  
function enviarDatosTrack(){
 
  //div donde se mostrará lo resultados
  divResultado = document.getElementById('resultado');
  //recogemos los valores de los inputs
  name=document.nuevo_track.nombre.value;
  album=document.nuevo_track.album.value;
  composer=document.nuevo_track.composer.value;
  milli=document.nuevo_track.milliseconds.value;
  by=document.nuevo_track.bytes.value;
  unit=document.nuevo_track.unitprice.value;
 
  //instanciamos el objetoAjax
  ajax=objetoAjax();
 
  //uso del medotod POST
  //archivo que realizará la operacion
  //registro.php
  ajax.open("POST", "cupid/insertar.php",true);
  //cuando el objeto XMLHttpRequest cambia de estado, la función se inicia
  ajax.onreadystatechange=function() {
	  //la función responseText tiene todos los datos pedidos al servidor
  	if (ajax.readyState==4) {
  		//mostrar resultados en esta capa
		divResultado.innerHTML = ajax.responseText
  		//llamar a funcion para limpiar los inputs
		LimpiarCampos();
	}
 }
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	//enviando los valores a registro.php para que inserte los datos
	ajax.send("nombre="+nom+"&album="+album+"&composer="+composer+"&milliseconds"+milli+"&bytes"+by+"&unitprice"+unit)
}
 
//función para limpiar los campos
function LimpiarCampos(){
  document.nuevo_track.nombre.value="";
  document.nuevo_track.album.value="";
  document.nuevo_track.composer.value="";
document.nuevo_track.milliseconds.value="";
document.nuevo_track.bytes.value="";
document.nuevo_track.unitprice.value="";
  document.nuevo_track.nombre.focus();
}




