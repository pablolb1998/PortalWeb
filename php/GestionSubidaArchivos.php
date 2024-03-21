<?php
session_start();
if(isset($_FILES["Archivo"])){
    
    if(!file_exists('subidasTemp')){
        mkdir('subidasTemp/', 0755, true);
        chown('subidasTemp/', 'www-data');
    }

    $directorioDestino = 'subidasTemp/'.$_SESSION["pinC"].'/';
    
    // Verificar si el directorio existe o crearlo si es necesario
    if (!file_exists($directorioDestino)) {
        mkdir($directorioDestino, 0755, true);
        chown($directorioDestino, 'www-data');
    }

    if (move_uploaded_file($_FILES["Archivo"]["tmp_name"], $directorioDestino . str_replace([' ', '/'] ,['','_'] ,$_FILES["Archivo"]["name"]) )){
        $host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $urlCompleta = "https://$host$requestUri";
        $ruta = str_replace('GestionSubidaArchivos.php','',$urlCompleta);
        
        echo $ruta.$directorioDestino.$_FILES["Archivo"]["name"];
    } else {
        echo false;
    }
}else{
    echo false;
}
?>
