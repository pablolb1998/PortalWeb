<?php
session_start();
if(isset($_FILES["Archivo"])){
    
    if(!file_exists('subidasTemp')){
        mkdir('subidasTemp/', 0777, true);
        chown('subidasTemp/', 'www-data');
    }

    $directorioDestino = 'subidasTemp/'.$_SESSION["pinC"].'/';
    
    // Verificar si el directorio existe o crearlo si es necesario
    if (!file_exists($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
        chown($directorioDestino, 'www-data');
    }

    if (move_uploaded_file($_FILES["Archivo"]["tmp_name"], $directorioDestino . $_FILES["Archivo"]["name"] )){
        // $externalContent = file_get_contents('https://api64.ipify.org?format=json');
        // $externalData = json_decode($externalContent);
        $ruta = 'www.areadecliente.de/prueba/PortalWeb/php/';
        echo $ruta.$directorioDestino.$_FILES["Archivo"]["name"];
    } else {
        echo false;
    }
}else{
    echo false;
}
?>
