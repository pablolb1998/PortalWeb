<?php
session_start();
require_once 'consultas.php';

if(isset($_POST['firmaDataUrl']) && $_POST['Id'] && $_POST['IdP'] && $_POST['IdTp'] && $_POST['IdS']) {
    $firmaDataUrl = $_POST['firmaDataUrl'];

    // Decodificar la URL de datos y guardar la imagen en el servidor
    $firmaBinario = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $firmaDataUrl));

    // Ruta donde se guardará la imagen (ajusta según tus necesidades)
    $rutaImagen = 'subidasTemp/'.$_SESSION["pinC"].'/'.$_POST['Id'].'.png';

    // Guardar la imagen en el servidor
    file_put_contents($rutaImagen, $firmaBinario);
    
    //consulta para insertar en PEFirma
    if(PEfirmaInsert($_POST['IdP'],
                    $_POST['IdTp'],
                    $_SESSION["pinC"].'_'.$_POST['Id'].'.png',
                    $_POST['Id'],
                    'http://www.areadecliente.de/php/'.$rutaImagen,
                    $_POST['IPc'],
                    $_POST['BBDD']
                    )){
    }
    
    $url = "http://onixsw.esquio.es:8080/Funciones.aspx?FirmarPDF=1&pin=".$_SESSION["pinC"].
    "&IdArchivo=".$_POST['Id'].'&TipoFirma='.$_POST['TipoF'].'&IdSociedad='.$_POST['IdS'];
    
    $response = file_get_contents($url);
    
    //$_SESSION["Controlador"] -> miEstado -> id_sociedad
    // Puedes devolver una respuesta al cliente si es necesario
    echo 1;
} else {
    echo 0;
}
?>

                