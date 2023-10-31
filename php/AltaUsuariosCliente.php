<?php
require_once 'consultas.php';
$Ip = null;
$datosBBDD = comprobarBD($_COOKIE['pinCPortalE']);
if(!isset($datosBBDD[0]["Puerto"])){
    $Ip = $datosBBDD[0]["Servidor"];
}else{
    $Ip = $datosBBDD[0]["Servidor"].','.$datosBBDD[0]["Puerto"];
}
if($_COOKIE['pinCPortalE'] == 123654){
    $Ip = '192.168.204.111';
}


if(isset($_POST['arrayDatos'])){
    $arratLogin = $_POST['arrayDatos'];
    $UsuarioCreado = execSeguridadUnificada_Identidad_Insert($Ip,$datosBBDD[0]["BBDD"],$arratLogin[0],$arratLogin[2],$arratLogin[3],$arratLogin[1]);
        if($UsuarioCreado != null && $UsuarioCreado != 0 ){
            
            //link de pruebas
            $link = 'http://www.areadecliente.de/php/ActivadorUsuariosCliente.php?a='.$UsuarioCreado.'&c='.$_COOKIE['pinCPortalE'].'&b='.$arratLogin[2];
            //$link = 'localhost/portaldecliente/php/ActivadorUsuariosCliente.php?a='.$UsuarioCreado.'&c='.$Ip.'&d='. $datosBBDD[0]["BBDD"].'&b='.$arratLogin[2];
           // echo $link;
            
            $plantillaHtml = '../html/plantillas/plantilla_mail.html';
            $file = fopen($plantillaHtml, "r");
            $filesize = filesize($plantillaHtml);
            $plantilla = fread($file, $filesize);
            $htmlCorreo = str_replace("%LinkActivacionPortal%",$link,$plantilla);
            //echo $htmlCorreo;
            execute_EnviarMail_CreacionUsuario($arratLogin[2],$htmlCorreo);
            echo "Se ha generado el usuario correctamente, por favor revise su correo.";
        }else{
            
            echo "No se pudo crear el usuario especificado";
        }
    
}else{
    echo "Fallo al recibir los datos en el servidor";
}
?>