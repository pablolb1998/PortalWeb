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
    $UsuarioCreado ;
    $saludo = '';
    $linkBase = '';
    $email = '';
    $asuntoCorreo= '';
    if(count($arratLogin) == 5){
        $asuntoCorreo= 'Activación usuario Area de cliente';
        $email = $arratLogin[2];
        $UsuarioCreado = execSeguridadUnificada_Identidad_Insert($Ip,$datosBBDD[0]["BBDD"],$email,$arratLogin[3],$arratLogin[1],$arratLogin[0],3);
        $saludo = 'Bienvenido a nuestra área de cliente';
        $linkBase = 'http://www.areadecliente.de/php/ActivadorUsuariosClientes.php?';
        
    }else{
        $asuntoCorreo= 'Activación usuario portal del cliente';
        $email = $arratLogin[1];
        $UsuarioCreado = execSeguridadUnificada_Identidad_Insert($Ip,$datosBBDD[0]["BBDD"],$email,$arratLogin[2],$arratLogin[0],null,1);
        $saludo = 'Bienvenido a nuestro Portal del empleado';
        $linkBase = 'http://www.tuportal.de/empleado/php/ActivadorUsuariosClientes.php?';
    }

    if($UsuarioCreado != null && $UsuarioCreado != 0 ){
        
        //link de pruebas
        $link = $linkBase.'a='.$UsuarioCreado.'&c='.$_COOKIE['pinCPortalE'].'&b='.$email;
        //$link = 'localhost/portaldecliente/php/ActivadorUsuariosCliente.php?a='.$UsuarioCreado.'&c='.$Ip.'&d='. $datosBBDD[0]["BBDD"].'&b='.$arratLogin[2];
        // echo $link;
        
        $plantillaHtml = '../html/plantillas/plantilla_mailCreacion.html';
        $file = fopen($plantillaHtml, "r");
        $filesize = filesize($plantillaHtml);
        $plantilla = fread($file, $filesize);
        $htmlCorreo = str_replace(["%LinkActivacionPortal%",'%msgSaludo%','%usuario%'],[$link,$saludo,$email],$plantilla);
        //echo $htmlCorreo;
        if(execute_EnviarMail_CreacionUsuario($email,$htmlCorreo,$asuntoCorreo)){
            echo "Se ha generado el usuario correctamente, por favor revise su correo.";
        }else{
            echo "No se pudo crear el usuario especificado";
        }
        
    }else{
        
        echo "No se pudo crear el usuario especificado";
    }
    
}else{
    echo "Se ha perdido la conexión, vuelva a intentarlo mas tarde.";
}
?>