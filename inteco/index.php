<?php
session_start();

$datosConn = file_get_contents("datosInicio.json");
$datosConn = json_decode($datosConn, true);
//pin de la bbdd
$_SESSION["pinC"] = $datosConn["pinC"];
// nombre empresa
$_SESSION["NombreM"] = $datosConn["NombreM"];
$_SESSION["imgLogo"] = $datosConn["imgLogo"];


//header personalizado
$header_Empresa = 'html/header.html';
if(file_exists($header_Empresa)){
    $header = fopen($header_Empresa, "r");
    $headertxt = fread($header,filesize($header_Empresa));
    $_SESSION["header"] = $headertxt;
    fclose($header);
}

//comprobasr si es del tipo extraer archivos
if(isset($_GET["IdD"])){
    $_SESSION["IdDocumento"] = $_GET["IdD"];
    $_SESSION["tipoArchivo"] = $_GET["TipoArchivo"];
    $_SESSION["TipoPortal"] = 1.5;

}else{
    //Tipo App
    $_SESSION["TipoPortal"] = $datosConn["TipoPortal"];
}

$ruta = "../index.html";
header('Location:'."../index.html");
exit;
?>