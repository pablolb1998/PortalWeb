

<?php
session_start();

$datosConn = file_get_contents("datosInicio.json");
$datosConn = json_decode($datosConn, true);
//pin de la bbdd
$_SESSION["pinC"] = $datosConn["pinC"];
// nombre empresa
$_SESSION["NombreM"] = $datosConn["NombreM"];
if($datosConn["imgLogo"] != ""){
    $_SESSION["imgLogo"] = $datosConn["imgLogo"];
}

if(isset($_GET["IdD"])){
    //$_SESSION["IdDocumento"] = hexdec($_GET["IdD"]);
    $_SESSION["IdDocumento"] = $_GET["IdD"];
    $_SESSION["tipoArchivo"] = $_GET["TipoArchivo"];
    $_SESSION["TipoPortal"] = 1.5;

}else{
    //Tipo App
    $_SESSION["TipoPortal"] = $datosConn["TipoPortal"];
    //$_SESSION["DirectorioOrigen"] = $datosConn["DirectorioOrigen"];
    if(isset($datosConn["imgLogo"]) && !isset($_ENV["imgLogo_".$_SESSION["pinC"]])){
        $cur_dir = getcwd();
        $_ENV["imgLogo_".$_SESSION["pinC"]] = $cur_dir.$datosConn["imgLogo"];
        
    }
}
$fechaCaducidad = time() + (365 * 24 * 60 * 60); // 1 año desde la ultima conexion en segundos
setcookie("pinCPortalE", $datosConn["pinC"], $fechaCaducidad, "/");
if(isset($datosConn["imgLogo"])){
    setcookie("LogoUsuarioPortalE",  $datosConn["imgLogo"] , $fechaCaducidad, "/");
}
setcookie("TipoPortalE",  $datosConn["TipoPortal"] , $fechaCaducidad, "/");
header('Location:../Index.html');
exit;


// session_start();

// $datosConn = file_get_contents("datosInicio.json");
// $datosConn = json_decode($datosConn, true);
// //pin de la bbdd
// $_SESSION["pinC"] = $datosConn["pinC"];
// // nombre empresa
// $_SESSION["NombreM"] = $datosConn["NombreM"];
// $_SESSION["imgLogo"] = $datosConn["imgLogo"];
// //directorio raiz
// $_SESSION["DirectorioEmpresa"] =  __DIR__;
// ;
// //header personalizado
// $header_Empresa = 'html/header.html';
// if(file_exists($header_Empresa)){
//     $header = fopen($header_Empresa, "r");
//     $headertxt = fread($header,filesize($header_Empresa));
//     $_SESSION["header"] = $headertxt;
//     fclose($header);
// }

// //comprobasr si es del tipo extraer archivos
// if(isset($_GET["IdD"])){
//     $_SESSION["IdDocumento"] = $_GET["IdD"];
//     $_SESSION["tipoArchivo"] = $_GET["TipoArchivo"];
//     $_SESSION["TipoPortal"] = 1.5;

// }else{
//     //Tipo App
//     $_SESSION["TipoPortal"] = $datosConn["TipoPortal"];
// }

// header('Location:'."../Index.html");
// exit;

?>