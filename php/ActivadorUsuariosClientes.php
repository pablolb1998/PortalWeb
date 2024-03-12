<?php
require_once 'consultas.php';
if(isset($_GET['a']) && isset($_GET['b']) && isset($_GET['c'])){
    $pin = $_GET['c'];
    $usuario = $_GET['b'];
    $IdIdentidad = $_GET['a'];
    $Ip;
    $datosBBDD = comprobarBD($pin,'sa','Iiaslgv52d');
    if(!isset($datosBBDD[0]["Puerto"])){
        $Ip = $datosBBDD[0]["Servidor"];
    }else{
        $Ip = $datosBBDD[0]["Servidor"].','.$datosBBDD[0]["Puerto"];
    }
    $datosBBDD = comprobarBD($pin,'sa','Iiaslgv52d');
    if(confirmarUsuarioSeguridadUnificada($Ip,$datosBBDD[0]["BBDD"],$IdIdentidad,$usuario)){
        header('Location:../Index.html');
    }

}



//http://www.areadecliente.de/index.php?IdD=3820&TipoArchivo=5&Pin=65814415D75C&ST=015106c7
//confirmarUsuarioSeguridadUnificada
?>
