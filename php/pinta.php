<?php
$documentos_completo;
$titulo;
$fitro1;
$filtro2;
$filtro3;
$nombre_cliente;

////////////////////////////////////
// putenv("CABECERA".'!');
// if (null !== getenv('CABECERA')) {
//     $cabecera = "../html/header.html";
//     $fileheader = fopen($cabecera, "r");
//     $cabecera = fread($fileheader, filesize($cabecera));
//     putenv("CABECERA = $cabecera");
// } 
// print_r(getenv('CABECERA'));
// $headertext =  getenv('CABECERA');
    



function pinta_contenido($estado){
    
    $titulo = "";
    $cabecera = "";
    $fileheadertext = "";
    switch ($estado) {
        case 0:
            $filename = "../html/login.html";
            break;
        case 0.5:

            $filename = "../html/campo_cif.html";
    
            break;
        case 1:
            $titulo = "Selecciona Sociedad";
            $filename = "../html/sociedades.html";
            $cabecera = "../html/header.html";
            break;
        case 2:
            $titulo = $_SESSION["Controlador"] -> miEstado -> NombreSociedad;
            $cabecera = "../html/header.html";
            
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $filename = "../html/secciones_Cliente.html";
                    break;
                case 2:
                    $filename = "../html/secciones_Empleado.html";
                    break;

            }
               
            break;  
        case 3:
            $titulo = "Mis Presupuestos";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $titulo = "Mis  Pedidos";
                    $cabecera = "../html/header.html";
                    $filename = "../html/documentos.html";
                    break;
                case 2:
                    $titulo = $_SESSION["Controlador"] -> miEstado -> NombreSociedad;
                    $cabecera = "../html/header.html";
                    $filename = "../html/secciones_Empleado_02.html";
                    break;

            }
            
            break;
        case 4.1:
            $titulo = "Mis Asistencias";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.3:
            $titulo = "Mis Contratos";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.4:
            $titulo = "Mis Documentos";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.5:
            $titulo = "Mi Formación";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.6:
            $titulo = "Mis Incidencias";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.7:
            $titulo = "Mi Material";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.8:
            $titulo = "Mi Nóminas";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;  
        case 5:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $titulo = "Mis Albaranes";
                    $cabecera = "../html/header.html";
                    $filename = "../html/documentos.html";
                    break;
                case 2:
                    $titulo = $_SESSION["Controlador"] -> miEstado -> NombreSociedad;
                    $cabecera = "../html/header.html";
                    $filename = "../html/Estado_Jornada_Empleado.html";
                    break;

            }
            
            break;
        case 6:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $titulo = "Mis Facturas";
                    //$_SESSION["tipo_documento"] = 3;
                    $cabecera = "../html/header.html";
                    $filename = "../html/documentos.html";
                    break;
                case 2:
                    $titulo = "Mis Facturas";
                    //$_SESSION["tipo_documento"] = 3;
                    $cabecera = "../html/header.html";
                    $filename = "../html/EstadoJornada.html";
                    break;
            }
                
            break;
        case 7:
                $titulo = "Mis Proyectos";
                $cabecera = "../html/header.html";
                $filename = "../html/documentos.html";
                break;
        default:
            $filename = "../html/login.html";
            break;
    
    }
  

    if ($cabecera != "") {
        //fileheader = fopen($cabecera, "r");
        //$filesizeheader = filesize($cabecera);
        //$fileheadertext = fread($fileheader, $filesizeheader);
        $fileheadertext = $_SESSION["Controlador"] -> miEstado -> header;
        $fileheadertext = str_replace("%NombreE%",$titulo,$fileheadertext);
    }




    $file = fopen($filename, "r");
    $filesize = filesize($filename);
    $filetext = fread($file, $filesize);
    $filetext =  $fileheadertext. $filetext;


    // $footer = fopen("../html/footer.html", "r");
    // $filesizef = filesize($filename);
    // $footer = fread($footer, $filesizef);
    

    //optimizar aqui
    if (in_array($_SESSION["Controlador"] -> miEstado -> Estado, array(1, 2, 3, 4, 5, 6, 7)) || ($_SESSION["Controlador"] -> miEstado -> Estado < 5 && $_SESSION["Controlador"] -> miEstado -> Estado > 4 )) {
        if ($_SESSION["Controlador"] -> miEstado -> Estado == 1){
            //Pestaña de sociedades del portal del comercial
            return $filetext.muestra_sociedades();
        }elseif(in_array($_SESSION["Controlador"] -> miEstado -> Estado , array(3, 4, 5, 6, 7)) && $_SESSION["Controlador"] -> miEstado -> tipo_App == 1){
            //Documentos portal del comercial
            $filetext = str_replace('<span id="filtros_dinamicos">',cargaFiltros(),$filetext);
            return $filetext.muestra_documentos();
        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 2 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
            // elegir la pestaña de la jornada correspondiente switch ($_SESSION["Controlador"] -> miEstado -> EstadoJornada[0]) {
            switch ($_SESSION["Controlador"] -> miEstado -> EstadoJornada[0]) {
                case 0:
                    $filetext = str_replace('id="pestanaFueraJornada" style="display: none;">','id="pestanaFueraJornada" style="display: flex;">',$filetext);
                    break;
                default:
                    $filetext = str_replace('id="pestanaEnJornada" style="display: none;">','id="pestanaEnJornada" style="display: flex;">',$filetext);
                    break;
            }
            return $filetext;


        }elseif( $_SESSION["Controlador"] -> miEstado -> Estado < 5 && $_SESSION["Controlador"] -> miEstado -> Estado > 4  && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> camposFormularios == null ){
        //Pestañas de navegacion
            $filetext = str_replace('%NombreEmpleado%',$_SESSION["Controlador"] -> miEstado -> nombre_descriptivo,$filetext);
            return $filetext.DibujaLineas_PortalEmpleado();
        }elseif( $_SESSION["Controlador"] -> miEstado -> Estado < 5 && $_SESSION["Controlador"] -> miEstado -> Estado > 4  && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == 1){
        //Generar los formularios
            return $filetext.cargaFormularioDinamico();

        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 5 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
        //Pestaña de jornada
            $filetext = str_replace('%NombreEmpleado%',$_SESSION["Controlador"] -> miEstado -> nombre_descriptivo,$filetext);
            try {
                $filetext = str_replace('%FechaAccionjornada%',$_SESSION["Controlador"] -> miEstado -> EstadoJornada[1] -> format('d/m/Y H:i'),$filetext);
            } catch (\Throwable $th) {
                $filetext = str_replace('%FechaAccionjornada%',$_SESSION["Controlador"] -> miEstado -> EstadoJornada[1],$filetext);
            }
                
            switch ($_SESSION["Controlador"] -> miEstado -> EstadoJornada[0]) {
                case 1:
                    $filetext = str_replace('<b style="color: %color%;"></b>','<b style="color: green;">En jornada</b>',$filetext);
                    $filetext = str_replace('%BotonIniciarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonFinalizarJornada%','block',$filetext);
                    $filetext = str_replace('%BotonPausarJornada%','block',$filetext);
                    $filetext = str_replace('%BotonretomarJornada%','none',$filetext);
                    break;
                case 2:
                    $filetext = str_replace('<b style="color: %color%;"></b>','<b style="color: grey;">En pausa</b>',$filetext);
                    $filetext = str_replace('%BotonIniciarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonFinalizarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonPausarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonretomarJornada%','block',$filetext);
                    break;
                default:
                $filetext = str_replace('<b style="color: %color%;"></b>','<b style="color: red;">Fuera de jornada</b>',$filetext);
                    $filetext = str_replace('%BotonIniciarJornada%','block',$filetext);
                    $filetext = str_replace('%BotonFinalizarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonPausarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonretomarJornada%','none',$filetext);
                    break;
            }

            $filetext .= cargarJornadaHistorico();
            return $filetext;

        }else{
            return $filetext;
        }

    } else {
        if(isset($_SESSION["imgLogo"])){
            $filetext = str_replace("%logoImg%",$_SESSION["imgLogo"],$filetext);
            if($_SESSION['Controlador'] -> miEstado -> tipo_App == 1){
                $filetext = str_replace("%NombrePortal%",'Portal del Cliente',$filetext);
            }else{
                $filetext = str_replace("%NombrePortal%",'Portal del Empleado',$filetext);
            }

        }else{
            $filetext = str_replace("%logoImg%",'https://esquio.es/wp-content/uploads/elementor/thumbs/logo-esquio-pq0g3tu6khq6p3k32wrm4q6iwu2nqga9msxptholvk.png',$filetext);
        }
        return $filetext;
    }

    fclose($file);
}

function muestra_sociedades(){

    //**************************************************************************************/

    //---------------------------------MOSTRAR SOCIEDADES---------------------------------\\

    //**************************************************************************************/
        $tablaSociedades = "";
        $tablaSociedades = "<form class='formulario'>";
        foreach ($_SESSION["Controlador"] -> miEstado -> lista_sociedades as $valor) {
            $id_sociedad = $valor["idSociedad"];
            $sociedad = $valor["NombreFiscal"];
                
            $tablaSociedades .= "<div class='form-group mt-4'>";
            $tablaSociedades .= "<div class='form-check'>";
            $tablaSociedades .= "<label class='form-check-label' for='".$id_sociedad."'>".$sociedad."</label>";
            $tablaSociedades .= "<input class='form-check-input' type='radio' name='radio_sociedad' id='".$id_sociedad."' value='".$id_sociedad."' required>";
            $tablaSociedades .= "</div></div>";
        }

        
        //cambie por la funcion dibuja pagina
        $tablaSociedades .= "<input onclick='dibuja_pagina()' style='float:right; margin-bottom:1.3em;' class='btn btn-outline-primary' type='submit' name='enviar' value='Siguiente'>";
        $tablaSociedades .= "</form>";
        $tablaSociedades .= "<br>";
        return $tablaSociedades;
}

//dibujar los documentos del portal del comercial segun la pestaña
function muestra_documentos(){
    //optimizar al cambiar la manera de almacenar los documentos y los filtros "array_filter()"
    if (in_array($_SESSION["Controlador"] -> miEstado -> Estado , array(3, 4, 5, 6, 7))) {
        $arrayDoc = array();
        $tipoDocf = 0;
        switch ($_SESSION["Controlador"] -> miEstado -> Estado) {
            case 4 :
                $tipoDocf = 1;
                break;
            case 5 :
                $tipoDocf = 2;
                break;
            case 6 :
                $tipoDocf = 3;
                break;
            case 7 :
                $tipoDocf = 4; 
                break;
            default:
                $tipoDocf = 0;
                break;
        }
        // funcion para aplicar un filtro a cada elemento del array
        $arrayDoc = array_filter($_SESSION["Controlador"] -> miEstado -> Documentos, function ($docF) use($tipoDocf) {
            return $docF["tipo"] == $tipoDocf;
        });

        //filtro por tipo 1 de filtro es decir string 
        if($_SESSION["Controlador"] -> miEstado -> CadenaFiltro !== null && $_SESSION["Controlador"] -> miEstado -> tipofiltro == 1){
            $arrayDoc = array_filter($arrayDoc, function ($docF) use($arrayDoc) {
                return str_contains($docF["Descripcion"],$_SESSION["Controlador"] -> miEstado -> CadenaFiltro);
            });
        }
        
        //filtro por tipo 2 de filtro es estado del doc 
        if($_SESSION["Controlador"] -> miEstado -> CadenaFiltro !== null && $_SESSION["Controlador"] -> miEstado -> tipofiltro == 2 ){
            $arrayDoc = array_filter($arrayDoc, function ($docF) use($arrayDoc) {
                return $docF["Estado"] == $_SESSION["Controlador"] -> miEstado -> CadenaFiltro;
            });
        }

        // funcion para reinedxarlo 
        $arrayDoc = array_values($arrayDoc);
        //print_r($arrayDoc);
        $listaDoc = "";
        $listaDoc .= "<section>";
        if(count($arrayDoc)>0){

            $listaDoc .=  "<table class='table table-striped table-bordered-bottom' id='cuerpo'>";

            $listaDoc .=  "<tbody id='myTable'>";

            for($dc = 0; ($dc < count($arrayDoc)) && ($dc < $_SESSION["Controlador"] -> miEstado -> puntero_posicion) ; $dc++ ){
                $valor = $arrayDoc[$dc]; 
                $id = $valor["id"];
                $codigo = $valor["codigo"];
                $PinDescargas = $valor["PinDescargas"];
                $Origen_impresion = $valor["OrigenImpresion"];
                $importe = $valor["importe"];
                $descripcion = $valor["Descripcion"];
                $newDate = $valor['Fecha'] -> format('d/m/Y');
                $estado = $valor["Estado"];
                $color = $valor["color"];

                //optimizar/cambiar
                if (strlen($descripcion) <= 0) {
                    $descripcion = "Sin descripción";
                }

                if($newDate==date("d/m/Y")){
                    $listaDoc .= "<tr style='background-color:#B0CDFF54' data-tipo-servicio='$estado' id='$estado'>";
                }else{
                    $listaDoc .= "<tr data-tipo-servicio='$estado' id='$estado'>";
                }
                $listaDoc .= '<td class="col-7"><div class="Identificador" name="Identificador">' . $codigo . ' - ' . $newDate . '<br><details><summary></summary>';
                $listaDoc .='<p><b style="color:'.$color.';">' . $estado . '</b><br>';
                $listaDoc .= '<span class="descripcion_doc">'.$descripcion .'</span></p></details></div></td>';
                $listaDoc .= '<td class="col-5"><div class="precio" name="precio" ><b id="importe_documento" style="float:right;color:'.$color.';">' . $importe;
                $listaDoc .= '</b><br><a href="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerPDF=1&pin=' . $PinDescargas .'&IdOrigenImpresion='.$Origen_impresion.'&IdPropietario='. $id. '" target="_blank">';
                $listaDoc .= '<img class="pdf_icono" src="img/descargar-pdf.png"></a></div></td></tr>'; 
                }
                $listaDoc .= "</tbody></table></span>";
                $listaDoc .= "<div class='container text-center'>";
                $listaDoc .= "<div class='row'>";
                $listaDoc .= "<div class='col'>";
                //optimizar moverlo a un documento aparte
                if (count($arrayDoc) > $_SESSION["Controlador"] -> miEstado -> puntero_posicion) {
                    $listaDoc .= '<button type="button" class="btn btn-primary mb-3" onclick="dibuja_pagina([0,1])">Ver más...</button>';
                }
        }else{
            $listaDoc .= '<div class="d-flex justify-content-center mt-5 mb-5"> No hay elementos </div>';
        }
        
        $listaDoc .= "</div></div></div></div></div></section></div></div></div>";
        return $listaDoc;
    }
}

//Dibujar las lineas de cualquier  del portal de empleado
function DibujaLineas_PortalEmpleado(){
    //optimizar al cambiar la manera de almacenar los documentos y los filtros "array_filter()"
        $arrayDoc = array();
        $tipoDocf = 0;
        $acciones_linea = '';
        if($_SESSION["Controlador"] -> miEstado -> acciones["archivos"] == 1 ){
            $acciones_linea = '<button onclick="dibuja_pagina([4.4,%IdProp%])"    style="all: initial;cursor: pointer;"><img class="pdf_icono" src="img/Documentos2.png"></button>';    
        }
        if($_SESSION["Controlador"] -> miEstado -> acciones["observaciones"] == 1 ){
            $acciones_linea = '<a href="#" target="_blank"><img class="pdf_icono" src="img/Observaciones.png"></a>';    
        }
        if(isset($_SESSION["Controlador"] -> miEstado -> acciones["anadirLinea"]) == 1 && $_SESSION["Controlador"] -> miEstado -> acciones["anadirLinea"] == 1){
            $acciones_linea .= '<button onclick="dibuja_pagina([0,3])" style="all: initial;position:fixed;right:13%;bottom:20px;cursor: pointer;" class="btn_acciones"><img class="pdf_icono" src="img/Portal_Empleado_Nuevo2.png" width="100"></button>';    
        }
        if($_SESSION["Controlador"] -> miEstado -> Estado == 4.4){
            $acciones_linea = '<a href="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerArchivo=1&pin='.$_SESSION["pinC"].'&IdArchivo=%IdProp%" target="_blank">
            <img class="pdf_icono" src="img/descargar-pdf.png"></a>';
        }
        
       
        switch ($_SESSION["Controlador"] -> miEstado -> Estado) {
            case 4.1 :
                $tipoDocf = 1;
                break;
            case 4.3 :
                $tipoDocf = 2;
                break;
            case 4.4 :
                $tipoDocf = 3;
                break;
            case 4.5:
                $tipoDocf = 4;
                break;
            case 4.6:
                $tipoDocf = 5;
                break;
            case 4.7:
                $tipoDocf = 6;
                break;
            case 4.8:
                $tipoDocf = 7;
            break;
            case 7 :
                $tipoDocf = 4; 
                break;
            default:
                $tipoDocf = 1;
                break;
        }

        // funcion para aplicar un filtro a cada elemento del array
        $arrayDoc = array_filter($_SESSION["Controlador"] -> miEstado -> Documentos, function ($docF) use($tipoDocf) {
            return $docF["tipoDocPortal"] == $tipoDocf;
        });
        //sacar los documentos por tipo propietario y el propietario
        if ($tipoDocf == 3 && $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario != null ){
            $arrayDoc = array_filter($arrayDoc, function ($docF) {
                return $docF["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario
                && $docF["IdPropietario"] ==  $_SESSION["Controlador"] -> miEstado -> IdPropietario;
            });
        }

        //print_r($arrayDoc);
        //filtro por tipo 1 de filtro es decir string 
        // if($_SESSION["Controlador"] -> miEstado -> CadenaFiltro !== null && $_SESSION["Controlador"] -> miEstado -> tipofiltro == 1){
        //     $arrayDoc = array_filter($arrayDoc, function ($docF) use($arrayDoc) {
        //         return str_contains($docF["Descripcion"],$_SESSION["Controlador"] -> miEstado -> CadenaFiltro);
        //     });
        // }
        
        //filtro por tipo 2 de filtro es estado del doc 
        // if($_SESSION["Controlador"] -> miEstado -> CadenaFiltro !== null && $_SESSION["Controlador"] -> miEstado -> tipofiltro == 2 ){
        //     $arrayDoc = array_filter($arrayDoc, function ($docF) use($arrayDoc) {
        //         return $docF["Estado"] == $_SESSION["Controlador"] -> miEstado -> CadenaFiltro;
        //     });
        // }

        // funcion para reinedxarlo 
        $arrayDoc = array_values($arrayDoc);
        //print_r($arrayDoc);
        $listaDoc = "";
        $listaDoc .= "<section>";
        if(count($arrayDoc)>0){

            $listaDoc .=  "<table class='table table-striped table-bordered-bottom' id='cuerpo'>";

            $listaDoc .=  "<tbody id='myTable'>";
            for($dc = 0; $dc < count($arrayDoc) ; $dc++ ){
               
                $valor = $arrayDoc[$dc];
                $id = $valor["id"];
                $descripcion = $valor["descripcion"];
                $descripcion2 = $valor["descripcion2"];
                try {
                    if(isset($valor["FechaInicio"])){
                        $FechaInicio = $valor["FechaInicio"] -> format('d/m/Y');
                    }else{
                        $FechaInicio = '';
                    }
                } catch (\Throwable $th) {
                    $FechaInicio = '';
                }
                try {
                    if(isset($valor["FechaFin"])){
                        $FechaFin = $valor["FechaFin"] -> format('d/m/Y');
                    }
                } catch (\Throwable $th) {
                    $FechaFin = '';
                }
                    
                //$color = $valor["color"];

                //optimizar/cambiar
                if (strlen($descripcion) <= 0) {
                    $descripcion = "Sin descripción";
                }
                //añadir a las acciones el id del propietario y el tipo
                $acciones_linea_pintar = str_replace('%IdProp%',$id,$acciones_linea);
                
                //pintar cada linea
                $listaDoc .= '<tr data-tipo-servicio="'.$id.'" id="'.$id.'" >';
                $listaDoc .= '<td class="col-7"><div class="Identificador" name="Identificador">' . $descripcion. '<br><details><summary></summary>';
                $listaDoc .='<p><b style="color:'.'Blue'.';">' . $descripcion2 . '</b><br>';
                $listaDoc .= '<span class="descripcion_doc">'.$descripcion .'</span></p></details></div></td></b>';
                $listaDoc .= '<td class="col-5"><div id="fecha_a" class="d-flex align-items-end flex-column" style="float:right;color:'.'Blue'.';"><b>'. $FechaInicio.'</b>';
                $listaDoc .= '<div class="mt-1">' . $acciones_linea_pintar . '</a></div>';
                $listaDoc .= '</p></div></td></tr>'; 
            }
            $listaDoc .= "</tbody></table></span>";
            $listaDoc .= "<div class='container text-center'>";
            $listaDoc .= "<div class='row'>";
            $listaDoc .= "<div class='col'>";
            
            //optimizar moverlo a un documento aparte
            // if (count($arrayDoc) > $_SESSION["Controlador"] -> miEstado -> puntero_posicion) {
            //     $listaDoc .= '<button type="button" class="btn btn-primary mb-3" onclick="dibuja_pagina([0,1])">Ver más...</button>';
            // }
        
        }else{
            $listaDoc .= '<div class="d-flex justify-content-center mt-5 mb-5"> No hay elementos </div>';
        }
        
        $listaDoc .= "</div></div></div></div></div></section></div></div></div>";
        return $listaDoc;
}

function cargaFiltros(){
    $arrayFiltros = array();
    $tipoDocf = 0;
        switch ($_SESSION["Controlador"] -> miEstado -> Estado) {
            case 4 :
                $tipoDocf = 1;
                break;
            case 5 :
                $tipoDocf = 2;
                break;
            case 6 :
                $tipoDocf = 3;
                break;
            case 7 :
                $tipoDocf = 4; 
                break;
            default:
                $tipoDocf = 0;
                break;
        }
     // funcion para aplicar un filtro a cada elemento del array
     
     $arrayFiltros = array_filter($_SESSION["Controlador"] -> miEstado -> FiltrosDoc, function ($docF) use($tipoDocf) {
        return $docF["tipo"] == $tipoDocf;
    });

    // funcion para reinedxarlo 
    $arrayFiltros = array_values($arrayFiltros);
    $txt_filtros = '<span id="filtros_dinamicos">';
    if(count($arrayFiltros)>0){
        foreach($arrayFiltros as $valor){ 
            $txt_filtros .= '<button onclick="aplicafiltros('."'".$valor["Estado"]."'".')" style="color:black;" class="dropdown-item" id="'.$valor["Estado"].'" >'.$valor["Filtro"].'</button>';
        }
    }
    return $txt_filtros;
}

function cargarJornadaHistorico(){
    $arrayJ = $_SESSION["Controlador"] -> miEstado -> HistoricoJornada;
    $arrayJ = array_values($arrayJ);
    $listaJ = "";
    $listaJ .= "<div class='row h-75 justify-content-center mt-5'>";
    $listaJ .= '<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">';
    $listaJ .= '<div class="card shadow-2-strong shadow pb-5 col-12">';
    if(count($arrayJ)>0){
        
        $listaJ .=  "<table class='table table-striped table-bordered-bottom' id='cuerpo'>";
        $listaJ .=  "<tbody id='myTable'>";
        for($dc = 0; $dc < count($arrayJ) ; $dc++ ){
           
            $valor = $arrayJ[$dc];
            $hora = $valor["HoraInicioFin"];
            if($valor["DuracionHoras"] !== null){
                $duracion = round($valor["DuracionHoras"],2);
            }else{
                $duracion = 0;
            }
           
            try {
                if(isset($valor["FechaInicio"])){
                    $FechaInicio = $valor["FechaInicio"] -> format('d/m/Y');
                }else{
                    $FechaInicio = '';
                }
            } catch (\Throwable $th) {
                $FechaInicio = '';
            }
            try {
                if(isset($valor["DiaIto"])){
                    $dia = $valor["DiaIto"] -> format('d/m/Y');
                }else{
                    $dia = '';
                }
            } catch (\Throwable $th) {
                $dia = '';
            }

            try {
                if(isset($valor["FechaFin"])){
                    $FechaFin = $valor["FechaFin"] -> format('d/m/Y');
                }
            } catch (\Throwable $th) {
                $FechaFin = '';
            }
                
            
            $listaJ .= '<tr>';
            $listaJ .= '<td class="col-7"><div class="Identificador" name="Identificador">' . $dia. '<br><details><summary></summary>';
            $listaJ .='<p><b style="color:'.'Blue'.';">' . $hora . '</b><br>';
            $listaJ .= '<span class="descripcion_doc">'.$hora .'</span></p></details></div></td></b>';
            $listaJ .= '<td class="col-5"><div id="fecha_a" class="d-flex align-items-end flex-column" style="float:right;color:'.'Blue'.';"><b>'. $duracion.'</b>';
            $listaJ .= '</p></div></td></tr>'; 
        }
        $listaJ .= "</tbody></table></span>";
     
 
        }else{
            $listaJ .= '<div class="d-flex justify-content-center mt-5 mb-5"> No hay elementos </div>';
        }
        $listaJ.='</div></div></div>';
        return $listaJ;
}

function cargaFormularioDinamico(){

    $arrayForm = array_filter($_SESSION["Controlador"] -> miEstado -> formularios, function ($form) {
        return $form["Estado"] == $_SESSION["Controlador"] -> miEstado -> Estado;
    });
    //print_r($_SESSION["Controlador"] -> miEstado -> camposFormularios);
    //print_r($arrayForm);
    $arrayForm = $arrayForm[0]["Campos"];
    
    $camposForm = "";
    $camposForm = "<section class='row justify-content-center'>";
    $camposForm .= "<form class='col-10 col-md-12 mt-3'>";
    foreach($arrayForm as $campo){
        //print_r($campo);
        if($campo["Mostrar"] == 1){
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1">';
            $camposForm .= '<label for="'.$campo["Nombre_campo"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<input type="'.$campo["TipoDatoHtml"].'" class="form-control" id="'.$campo["Nombre_campo"].'" aria-describedby="'.$campo["Nombre_campo"].'">';
            //$camposForm .= '<div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>''
            $camposForm .= '</div>';
        }
        

    }
    $camposForm .= '<br><div class="bg-light"><br><br><br></div>';
    $camposForm .= '<div class="row col-11 justify-content-center">';
    $camposForm .= '<div class="col-5 col-lg-3 offset-1 btn  mt-3" onclick="dibuja_pagina([0,3,0])"  onmouseover="this.style.backgroundColor='."'#d6d5d3'".'" onmouseout="this.style.backgroundColor='."'#ffffff'".'";><h3>Cancelar</h3></div>';
    $camposForm .= '<div class="col-5 col-lg-3 offset-1 btn  mt-3" onclick="dibuja_pagina([0,3,1])" style="color:#0265bd;" onmouseover="this.style.backgroundColor='."'#d6d5d3'".'" onmouseout="this.style.backgroundColor='."'#ffffff'".'";><h3>Guardar</h3></div>';
    $camposForm .= "</div><br>";
    $camposForm .= "</form></section></div></div></div>";
    return $camposForm;
}