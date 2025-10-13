<?php

//Asignar la faltaq de valores como error fatal en vez de como warming
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_message = "Error [$errno] en $errfile:$errline: $errstr";
    file_put_contents('errores.txt', $error_message . "\n", FILE_APPEND);
    file_put_contents('errores.txt', '['.date('Y-m-d H:i:s').']' . ' -> ' . $error_message . ' -> ', FILE_APPEND);
});
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $error_message = "Fatal error [{$error['type']}] en {$error['file']}:{$error['line']} - {$error['message']}";
        file_put_contents('errores.txt', '['.date('Y-m-d H:i:s').'] ' . $error_message . "\n", FILE_APPEND);
        
        // También podrías mostrar algo al usuario si estás en modo desarrollo:
        // echo json_encode(['error' => 'Algo salió mal.']);
    }
});
set_exception_handler(function($exception) {
    $error_message = "Excepción no capturada: " . $exception->getMessage();
    file_put_contents('errores.txt', '['.date('Y-m-d H:i:s').'] ' . $error_message . "\n", FILE_APPEND);
    var_dump($exception);
    echo json_encode(["Ha ocurrido un error inesperado", null, 0]);
});
require_once "Clases/controlador.php";
require_once 'Clases/estado.php';
session_start();

try {
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
    //throw new Exception("Error intencional");
} catch (Exception $e) {
    // $error_message = "Error: " . $e->getMessage();
    // file_put_contents('errores.txt', $error_message . "\n", FILE_APPEND);
    $respuestaJSON = json_encode(array("Ha ocurrido un error inesperado",null,0));
}

// $respuestaJSON = json_encode(array($_SESSION["TipoPortal"],0,0));
echo $respuestaJSON;
?>
