<?php
session_start();

if(isset($_FILES["Archivo"])){
    $directorio_destino = $_SESSION["DirectorioEmpresa"] ."/inteco/subidasTemp/";
    //$nombre_archivo = $_FILES["Archivo"]["name"];
    // echo $directorio_destino;
    // echo $_FILES["Archivo"]["tmp_name"];
    //$tmp_name = $_FILES["Archivo"]["tmp_name"];

    ///funciona asi pero hay que cambiarlo con urgencia
    if (move_uploaded_file($_FILES["Archivo"]["tmp_name"], 'C:/xampp/htdocs/portaldecliente/inteco/subidasTemp/'.$_FILES["Archivo"]["name"] )) {
        echo 'C:/xampp/htdocs/portaldecliente/inteco/subidasTemp/'.$_FILES["Archivo"]["name"];
    } else {
        echo false;
    }
}else{
    echo false;
}
?>