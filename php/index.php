<?php

//Asignar la faltaq de valores como error fatal en vez de como warming
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (strpos($errstr, 'Undefined array key') !== false) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    return false;
});

require_once "Clases/controlador.php";
require_once 'Clases/estado.php';
session_start();

// try {
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
    $respuestaJSON = json_encode(devuelveContenido());
// } catch (\Throwable $th) {
//     $mensajeDeError = $th->getMessage();
//     $respuestaJSON = json_encode(array($mensajeDeError,null,0));
// }

// $respuestaJSON = json_encode(array($_SESSION["TipoPortal"],0,0));


echo $respuestaJSON;
?>
