
<?php
require_once "Clases/controlador.php";
require_once 'Clases/estado.php';
session_start();

if (!isset($_SESSION["Controlador"])) {
    $_SESSION["Controlador"] = new Controlador;
}

function devuelveContenido(){
    if(!isset($_POST['arrayDatos'])){
        return $_SESSION["Controlador"] -> generarContenido();
    }else{
        return $_SESSION["Controlador"] -> generarContenido($_POST['arrayDatos']);   
    }  
}
// $respuestaJSON = json_encode(array($_SESSION["TipoPortal"],0,0));
 $respuestaJSON = json_encode(devuelveContenido());
echo $respuestaJSON;
?>
