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
                    //$filename = "../html/secciones_Empleado.html";
                    break;

            }
               
            break;  
        case 3:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $titulo = "Mis Presupuestos";
                    $cabecera = "../html/header.html";
                    $filename = "../html/documentos.html";
                    break;
                case 2:
                    $cabecera = "../html/header.html";
                    break;

            }
            
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
                    //$filename = "../html/secciones_Empleado_02.html";
                    break;

            }
            
            break;
        case 4.1:
            $titulo = "Asistencias";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.3:
            $titulo = "Contratos";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.4:
            $titulo = "Documentos";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.5:
            $titulo = "Formación";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.6:
            $titulo = "Incidencias";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.7:
            $titulo = "Material";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;
        case 4.8:
            $titulo = "Nóminas";
            $cabecera = "../html/header.html";
            $filename = "../html/documentos.html";
            break;  
        case 4.9:
            $titulo = "Vacaciones";
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
               
                switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                    case 1:
                        $titulo = "Mis Proyectos";
                        $cabecera = "../html/header.html";
                        $filename = "../html/documentos.html";
                        break;
                    case 2:
                        $titulo = "Mi calendario";
                        $filename = "../html/documentos.html";
                        $cabecera = "../html/header.html";
                        break;
                }
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
        $fileheadertext = str_replace("%NombreE%",$titulo.($_SESSION["Controlador"] -> miEstado -> cargarForm == 1 ? ' / Nueva':''),$fileheadertext);
    }



    if(isset($filename) && $filename != "" ){
        $file = fopen($filename, "r");
        $filesize = filesize($filename);
        $filetext = fread($file, $filesize);
        $filetext =  $fileheadertext. $filetext;
    }else{
        $filetext = $fileheadertext.cargarSeccionesDinamicas();
    }
    


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
            $filetext = str_replace('%FuncionFiltrar%','aplicafiltros()',$filetext);
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
        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 3 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
        // añadir los filtros a el calendario
            $arrayFiltros = [
                ["idTipoDoc" => 1,
                "Descripcion" => "Asistencias"],
                ["idTipoDoc" => 2,
                "Descripcion" => "Contratos"],
                ["idTipoDoc" => 4,
                "Descripcion" => "Formación"],
                ["idTipoDoc" => 5,
                "Descripcion" => "Incidencias"],
                ["idTipoDoc" => 6,
                "Descripcion" => "Material"]
            ];
        
            // funcion para reindexarlo 
            
            $txt_filtros = '<span id="filtros_dinamicos">';
            if(count($arrayFiltros)>0){
                foreach($arrayFiltros as $valor){ 
                    $txt_filtros .= '<button onclick="" style="color:black;" class="dropdown-item" id="'.$valor["idTipoDoc"].'" >'.$valor["Descripcion"].'</button>';
                }
            }
            return $txt_filtros;


        }
        //Pestaña de Vacaciones del empleado
        elseif($_SESSION["Controlador"] -> miEstado -> Estado == 4.9 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == null){
          
            return $filetext.cargarVacacionesEmpleado();
        } elseif($_SESSION["Controlador"] -> miEstado -> Estado == 4.9 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == 1){
          
            return $filetext.cargarFormVacacionesEmpleado();
        }
        elseif( $_SESSION["Controlador"] -> miEstado -> Estado < 5 && $_SESSION["Controlador"] -> miEstado -> Estado > 4  && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == null && $_SESSION["Controlador"] -> miEstado -> cargarFormFirma == null ){
        //Pestañas de navegacion
            $filetext = str_replace('%NombreEmpleado%',$_SESSION["Controlador"] -> miEstado -> nombre_descriptivo,$filetext);

            ////////////////
                // $arrayFiltros = [
                //     ["idTipoDoc" => 1,
                //     "Descripcion" => "Asistencias"],
                //     ["idTipoDoc" => 2,
                //     "Descripcion" => "Contratos"],
                //     ["idTipoDoc" => 4,
                //     "Descripcion" => "Formación"],
                //     ["idTipoDoc" => 5,
                //     "Descripcion" => "Incidencias"],
                //     ["idTipoDoc" => 6,
                //     "Descripcion" => "Material"]
                // ];
            
                // // funcion para reindexarlo 
                
                // $txt_filtros = '<span id="filtros_dinamicos">';
                // if(count($arrayFiltros)>0){
                //     foreach($arrayFiltros as $valor){ 
                //         $txt_filtros .= '<div class="form-check">';
                //         $txt_filtros .= ' <label class="form-check-label" for="'.$valor["idTipoDoc"].'">'.$valor["Descripcion"].'</label>';
                //         $txt_filtros .= '<input class="form-check-input" type="checkbox" value="'.$valor["idTipoDoc"].'" id="'.$valor["idTipoDoc"].'">';
                //         $txt_filtros .= '</div>';
                //     }
                // }
                //  $txt_filtros .= '</span><button class="btn btn-primary">buscar</button>';

                //  $filetext = str_replace('<span id="filtros_dinamicos">',$txt_filtros,$filetext);
            /////////////////


            return $filetext.DibujaLineas_PortalEmpleado();
        }elseif( $_SESSION["Controlador"] -> miEstado -> Estado < 5 && $_SESSION["Controlador"] -> miEstado -> Estado > 4  && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == 1 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
        //Generar los formularios
            return $filetext.cargaFormularioDinamico();
        }elseif ($_SESSION["Controlador"] -> miEstado -> Estado == 4.4 && $_SESSION["Controlador"] -> miEstado -> cargarFormFirma == 1 ) {
            return $filetext.cargarFormularioFirma();
        }
        
        elseif($_SESSION["Controlador"] -> miEstado -> Estado == 5 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
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

        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 7 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == 0){
        
        //Pestaña de calendario (Pepe)
            $filtrosCalendario = '<span id="filtros_dinamicos">
                                    <div class="form-check form-switch"> <label class="form-check-label" for="1">Asistencias</label><input class="form-check-input" type="checkbox" value="1" id="1" checked=""></div>
                                    <div class="form-check form-switch"> <label class="form-check-label" for="2">Contratos</label><input class="form-check-input" type="checkbox" value="2" id="2" checked=""></div>
                                    <div class="form-check form-switch"> <label class="form-check-label" for="4">Formación</label><input class="form-check-input" type="checkbox" value="4" id="4" checked=""></div>
                                    <div class="form-check form-switch"> <label class="form-check-label" for="5">Incidencias</label><input class="form-check-input" type="checkbox" value="5" id="5" checked=""></div>
                                    <div class="form-check form-switch"> <label class="form-check-label" for="6">Material</label><input class="form-check-input" type="checkbox" value="6" id="6" checked=""></div>
                                </span>';
            $filetext = str_replace('<span id="filtros_dinamicos">',$filtrosCalendario,$filetext);
            $filetext = str_replace('%FuncionFiltrar%','aplicaFiltrosCalendario()',$filetext);
            return $filetext.'<div id="calendar"></div></div></div></div>
            <button onclick="dibuja_pagina([0,3])" style="all: initial;position:fixed;right:13%;bottom:10px;cursor: pointer;" class="btn_acciones"><img src="img/Portal_Empleado_Nuevo2.png"></button>';    
        
        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 7 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == 1){
        //selección de el tipo de documneto
            
            return $filetext.cargarSeleccionDocumentosCalendario();
        }else{
            return $filetext;
        }

    } else {
        if(isset($_SESSION["imgLogo"])){
            $filetext = str_replace("%logoImg%",$_SESSION["imgLogo"],$filetext);
            if($_SESSION['Controlador'] -> miEstado -> tipo_App == 1){
                $filetext = str_replace("%NombrePortal%",'Portal del Cliente',$filetext);
                $filetext = str_replace("%TipoUsuario%",'cliente',$filetext);
            }else{
                $filetext = str_replace("%NombrePortal%",'Portal del Empleado',$filetext);
                $filetext = str_replace("%TipoUsuario%",'empleado',$filetext);
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
        $acciones_globales = '';
        if($_SESSION["Controlador"] -> miEstado -> acciones["archivos"] == 1 ){
            $acciones_linea .= '<button onclick="dibuja_pagina([4.4,%IdProp%])"    style="all: initial;cursor: pointer;"><img class="pdf_icono" src="%ImgArchivos%"></button>';    
        }
        if($_SESSION["Controlador"] -> miEstado -> acciones["observaciones"] == 1 ){
            $acciones_linea .= '<a href="#" target="_blank"><img class="pdf_icono" src="img/Observaciones.png"></a>';    
        }
        if($_SESSION["Controlador"] -> miEstado -> acciones["anadirLinea"] == 1){
            $acciones_globales .= '<button onclick="dibuja_pagina([0,3])" style="all: initial;position:fixed;right:13%;bottom:10px;cursor: pointer;" class="btn_acciones"><img src="img/Portal_Empleado_Nuevo2.png"></button>';    
        }
        if($_SESSION["Controlador"] -> miEstado -> Estado == 4.4){
            $acciones_linea = '<a href="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerArchivo=1&pin='.$_SESSION["pinC"].'&IdArchivo=%IdProp%" target="_blank">
            <img class="pdf_icono" src="%imgTipoArchivo%"></a>';
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
            case 4.9:
                $tipoDocf = 8;
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
        //print_r($_SESSION["Controlador"] -> miEstado -> IdTipoPropietario);
        //print_r($_SESSION["Controlador"] -> miEstado -> IdPropietario);
        //sacar los documentos por tipo propietario y el propietario
        if ($tipoDocf == 3 && $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario != null && $_SESSION["Controlador"] -> miEstado -> IdPropietario != null){
            $arrayDoc = array_filter($arrayDoc, function ($docF) {
                return $docF["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario
                && $docF["IdPropietario"] ==  $_SESSION["Controlador"] -> miEstado -> IdPropietario;
            });
        }elseif($tipoDocf == 3 && $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario != null){
            $arrayDoc = array_filter($arrayDoc, function ($docF) {
                
                return $docF["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario;
            });
        }

        // funcion para reinedxarlo 
        $arrayDoc = array_values($arrayDoc);
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
                //elegir el color de la carpeta segun tenga archivos
                if($_SESSION["Controlador"] -> miEstado -> acciones["archivos"] == 1 ){
                    $imgRutaCarpeta = '';
                    $imgRutaCarpeta = 'img/Documentos2.png';
                    //print_r($carperta);
                    if(isset($valor["NumeroArchivos"]) && $valor["NumeroArchivos"]>0){
                        $imgRutaCarpeta = 'img/Documentos_verde2.png';
                    }
                    $acciones_linea_pintar = str_replace('%ImgArchivos%',$imgRutaCarpeta,$acciones_linea_pintar);
                }
                // incluir la accion de abrir el formulario de firma
                if(isset($valor["Firmable"]) && $valor["Firmable"] == 1 ){
                    $acciones_linea_pintar .= '<button onclick="dibuja_pagina([0,6,'.$valor["id"].'])"  style="all: initial;cursor: pointer;" >
                                        <img  src="img/firma2.png"></button>';
                }
                //elegir el icono
                if( $_SESSION["Controlador"] -> miEstado -> Estado == 4.4 ){
                    $imgExtension = 'img/descarga_generica.png';
                    $file_name = $valor["Documento"];
                    $temp = explode('.',$file_name);
                    $extension = end($temp);
                    
                    if($extension == 'pdf'){
                        $imgExtension = 'img/descarga_pdf.png';
                    }elseif($extension == 'docx'){
                        $imgExtension = 'img/descarga_word.png';
                    }elseif($extension == 'xlsx'){
                        $imgExtension = 'img/descarga_excell.png';
                    }
                    $acciones_linea_pintar = str_replace('%imgTipoArchivo%',$imgExtension,$acciones_linea_pintar);
                }

                
                

                //pintar cada linea
                $listaDoc .= '<tr data-tipo-servicio="'.$id.'" id="'.$id.'" >';
                $listaDoc .= '<td class="col-7"><div class="Identificador" name="Identificador"><h5>' . $descripcion. '<h5><br><details><summary></summary>';
                $listaDoc .='<p><b style="color:'.'Blue'.';">' . $descripcion2 . '</b><br>';
                $listaDoc .= '<span class="descripcion_doc">'.$descripcion .'</span></p></details></div></td></b>';
                $listaDoc .= '<td class="col-5"><div id="fecha_a" class="d-flex align-items-end flex-column" style="float:right;"><h5>'. $FechaInicio.'<h5>';
                $listaDoc .= '<div class="mt-1">' . $acciones_linea_pintar . '</a></div>';
                $listaDoc .= '</div></td></tr>'; 
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
        $listaDoc .= $acciones_globales;
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
    
    
    $txt_filtros .= '<span id="filtros_dinamicos">';
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
            $listaJ .= '<td class="col-5"><div id="fecha_a" class="d-flex align-items-end flex-column" style="float:right;color:'.'Blue'.';">'. $duracion;
            $listaJ .= '</p></div></td></tr>'; 
        }
        $listaJ .= "</tbody></table></span>";
     
 
        }else{
            $listaJ .= '<div class="d-flex justify-content-center mt-5 mb-5"> No hay elementos </div>';
        }
        $listaJ.='</div></div></div>';
        return $listaJ;
}

//pintar los formularios dinamicamente
function cargaFormularioDinamico(){

    //extraer los campos dinamicamente del JSON

    $arrayForm = array_filter($_SESSION["Controlador"] -> miEstado -> formularios, function ($form) {
        return $form["Estado"] == $_SESSION["Controlador"] -> miEstado -> Estado;
    });
    $arrayIntermedio = array_shift($arrayForm);
    $arrayCamposForm = $arrayIntermedio["Campos"];
    
    //Extraer los dropdowns que se van a usar
    $arraydropdown = array_filter($_SESSION["Controlador"] -> miEstado -> dropdownsFormularios, function ($filtro) use($arrayCamposForm){
        return in_array($filtro["IdTipoDefinicion"],array_column($arrayCamposForm, "IdTipoDefinicion")); 
    });
    
    //extraer el dropdown de Archivostipos
    if($_SESSION["Controlador"] -> miEstado -> Estado == 4.4){
        $arraydropdown = array_filter($_SESSION["Controlador"] -> miEstado -> archivostiposAccesos, function ($campos) {
            return $campos["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario;
        });
    }
    //print_r($_SESSION["Controlador"] -> miEstado -> Documentos);
    $camposForm = "";
    $camposForm = "<section class='row justify-content-center'>";
    $camposForm .= "<form %tipoForm% >";
    $formularioConArchivos = false;
    foreach($arrayCamposForm as $campo){
        if($campo["TipoDatoHtml"] == 'file'){
            $formularioConArchivos = true;
        }
        
        if($campo["SelectorDesplegable"] == 0 && $campo["OUTPUT"] == 0 && $campo["Mostrar"] == 1){
            // print_r($campo);'.($campo["Mostrar"] == 1 ? "" : 'display:none;').'
            
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style="'.($campo["Mostrar"] == 1 ? "" : 'display:none;').'">';
            $camposForm .= '<label for="'.$campo["Nombre_campo"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<input type="'.$campo["TipoDatoHtml"].'" class="form-control border-secondary" id = "'.$campo["Nombre_campo"].'" aria-describedby="'.$campo["Nombre_campo"].'"'
            .($campo["VariableAlmacenada"] != null ? ' value = "'. $_SESSION["Controlador"] -> miEstado -> {$campo["VariableAlmacenada"]}.'"' : ' value = "'. $campo["ValorPorDefecto"].'"').' '.($campo["Required"] == 1 ? "required" : "").'>';
            //$camposForm .= '<div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>''
            $camposForm .= '</div><br>';
        }elseif($campo["Mostrar"] == 1 && $campo["SelectorDesplegable"] == 1 && isset($campo["IdTipoDefinicion"]) && $campo["Variable"] != "IdArchivosTipo"){
            //filtrar el select
            $arraydropdownfiltrado = array_filter($arraydropdown, function ($valorDropdown) use($campo) {
                return $valorDropdown["IdTipoDefinicion"] == $campo["IdTipoDefinicion"];
            });
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="'.$campo["Nombre_campo"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="'.$campo["Nombre_campo"].'" id="'.$campo["Nombre_campo"].'">';
            $existeSelecion = 0;
            foreach($arraydropdownfiltrado as $valorSelecionable){
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['IdTipo'].'" selected>'.$valorSelecionable['Nombre'].'</option>';
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['IdTipo'].'">'.$valorSelecionable['Nombre'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div><br>';
        }elseif($campo["Variable"] == "IdArchivosTipo" && $campo["Mostrar"] == 1 && $campo["SelectorDesplegable"] == 1){
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="'.$campo["Nombre_campo"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="'.$campo["Nombre_campo"].'" id="'.$campo["Nombre_campo"].'">';
            $existeSelecion = 0;
            foreach($arraydropdown as $valorSelecionable){
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['IdArchivoTipo'].'" selected>'.$valorSelecionable['Nombre'].'</option>';
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['IdArchivoTipo'].'">'.$valorSelecionable['Nombre'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div><br>';
        }
        

    }
   
    $camposForm .= '<br><div class="bg-light"><br><br><br></div>';
    $camposForm .= '<div class="row col-11 justify-content-center">';
    $camposForm .= '<button class="col-5 col-lg-3 offset-1 btn  mt-3" onclick="dibuja_pagina([0,3,0])"  onmouseover="this.style.backgroundColor='."'#d6d5d3'".'" onmouseout="this.style.backgroundColor='."'#ffffff'".'";><h3>Cancelar</h3></button>';
    $camposForm .= '<button class="col-5 col-lg-3 offset-1 btn  mt-3" onclick="" type="submit" style="color:#0265bd;" onmouseover="this.style.backgroundColor='."'#d6d5d3'".'" onmouseout="this.style.backgroundColor='."'#ffffff'".'";><h3>Guardar</h3></button>';
    //$camposForm .= '<button class="btn btn-primary" type="submit">Submit form</button>';
    $camposForm .= "</div><br>";
    $camposForm .= "</form></section></div></div></div>";

    if($formularioConArchivos == true){
        $camposForm = str_replace("%tipoForm%",'enctype="multipart/form-data" class="formulario_Dinamico_Archivos col-10 col-md-12 mt-3"',$camposForm);
    }else{
        $camposForm = str_replace("%tipoForm%","class=' formulario_Dinamico col-10 col-md-12 mt-3'",$camposForm);
        
    }

    return $camposForm;
}


function cargarSeccionesDinamicas(){
    //fragmento comun
    $pestanaSecciones = "";
    $pestanaSecciones .= '<br><br><div class="row h-75 justify-content-center">';
    $pestanaSecciones .= '<div class="col-12 col-md-9">';
    $pestanaSecciones .= '<div class="card shadow-2-strong shadow">';
    $pestanaSecciones .= '<div class="button-container  justify-content-center pb-5 ">';
    if ($_SESSION["Controlador"] -> miEstado -> Estado == 2) {
        $pestanaSecciones .= '<div class="row col-12 col-lg-6 offset-lg-3">';
    } else {
        $pestanaSecciones .= '<div class="row  justify-content-center col-10 offset-1">';
    }
    
    

    //Portal Empleado Menu principal
    $arraySecciones = array_filter($_SESSION["Controlador"] -> miEstado -> permisosSecciones, function ($seccion) {
        return ($seccion["OrdenEstado"] == $_SESSION["Controlador"] -> miEstado -> Estado && $seccion["TipoApp"] == $_SESSION["Controlador"] -> miEstado -> tipo_App);
    });
    
    if(count($arraySecciones)>0){
        foreach($arraySecciones as $seccion){
            
            if($seccion['IdFormulario'] == 2612){
                $pestanaSecciones .= '<div class=" col-12 col-sm-6 mt-5" id="pestanaEnJornada" style="display: none;">';
                $pestanaSecciones .= '<button id="boton_secciones" onclick="dibuja_pagina([2])"';
                $pestanaSecciones .=  'class="btn btn-md border shadow bg-body rounded d-block mx-auto" >';
                $pestanaSecciones .=  '<img class="img-fluid" src="img/PortalEmpleado/Pestanamenu/portal-empleado-jornada-entrada-f.png" />';
                $pestanaSecciones .= '</button>';
                $pestanaSecciones .= '</div>';
              
                $pestanaSecciones .=  '<div class=" col-12 col-sm-6 mt-5" id="pestanaFueraJornada" style="display: none;">';
                $pestanaSecciones .= '<button id="boton_secciones" onclick="dibuja_pagina([2])"';
                $pestanaSecciones .= 'class="btn btn-md border shadow bg-body rounded d-block mx-auto" >';
                $pestanaSecciones .= '<img class="img-fluid" src="img/PortalEmpleado/Pestanamenu/portal-empleado-jornada-salida-f2.png"/>';
                $pestanaSecciones .= '</button>';
                $pestanaSecciones .= '</div>';
                
            }else{
                $pestanaSecciones .= '<div class="'.$seccion['EstiloPestaña'].'">';
                $pestanaSecciones .= '<button id="boton_secciones" onclick="dibuja_pagina(['.$seccion['ValorAccion'].'])"';
                $pestanaSecciones .= 'class="btn btn-md border shadow bg-body rounded d-block mx-auto" >';
                $pestanaSecciones .= '<img class="img-fluid" src="img/PortalEmpleado/Pestanamenu/'.$seccion['Imagen'].'"/>';
                $pestanaSecciones .= '</button>';
                $pestanaSecciones .= '</div>';
            }
           
            
        }
    }else{
        $pestanaSecciones = "No tienes permisos para acceder a la siguiente pestaña";
    }
    

    //cierre fragmento comun
    $pestanaSecciones .= '</div></div></div></div></div>';
    return $pestanaSecciones;
}


//cargar el formulario de firma
function cargarFormularioFirma(){
    $documentoFirmable = array_filter($_SESSION["Controlador"] -> miEstado -> Documentos, function ($documento) {
        return $documento["id"] == $_SESSION["Controlador"] -> miEstado -> IdDocumentoPadre;
    });
    $documentoFirmable = array_shift($documentoFirmable);
    print_r($documentoFirmable["Documento"]);
}


function cargarSeleccionDocumentosCalendario(){
    $secciones = $_SESSION["Controlador"] -> miEstado -> permisosSecciones;
    $opcionesArch = array_filter($secciones, function ($documento) {
        return !in_array($documento["IdFormulario"],[2641,2620,2616]);
    });


    $seccionbtn = '<div class="d-grid gap-2 col-6 mx-auto p-4" role="group">';

    foreach($opcionesArch as $doc){
        $seccionbtn .= '<button type="button " class=" btn btn-light" id="'.$doc["ValorAccion"].'" onclick="dibuja_pagina('.$doc["ValorAccion"].')" ">'.$doc["Nombre"].'</button>';

        // $seccionbtn .= '<input type="radio" class="btn-check"  autocomplete="off" >';
        // $seccionbtn .= '<label class="btn btn-outline-primary" for="btnradio1"></label>';
    }
    $seccionbtn .= '</div></div></div></div>';
    return $seccionbtn;
    
}
function cargarVacacionesEmpleado(){


   $seccionvcn='
   <section class="">
                          <nav>
                              <div class="nav nav-tabs mt-4 " id="nav-tab" role="tablist">
                                  <button class="nav-link" id="nav-graficos-tab" data-bs-toggle="tab"
                                      data-bs-target="#nav-graficos" type="button" role="tab" aria-controls="nav-graficos"
                                      aria-selected="false">Gráficos</button>
                                  <button class="nav-link active" id="nav-resumen-tab" data-bs-toggle="tab"
                                      data-bs-target="#nav-resumen" type="button" role="tab" aria-controls="nav-resumen"
                                      aria-selected="true" aria-expanded="true">Resumen</button>
  
                                  <button class="nav-link" id="nav-festivos-tab" data-bs-toggle="tab"
                                      data-bs-target="#nav-festivos" type="button" role="tab" aria-controls="nav-festivos"
                                      aria-selected="false">Festivos</button>
                                      
                                      <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">Seleccionar año</button>
                              </div>
                          </nav>
                          <div class="tab-content mt-2 p-3" id="nav-tabContent">
                              <!-- Contenido de Resumen. -->
                              <div class="tab-pane fade show active" id="nav-resumen" role="tabpanel"
                                  aria-labelledby="nav-resumen-tab">
                                  <div id="resumen">
  
                                  </div>
  
  
                               
                              </div>
                              
                              <!-- Contenido de Festivos. -->
                              <div class="tab-pane fade" id="nav-festivos" role="tabpanel"
                                  aria-labelledby="nav-festivos-tab">
                                  <div id="festivos">
  
                                  </div>
                              </div>
                            
                              <!-- Contenido de Gráficos. -->
                             
                              <canvas class="tab-pane fade" id="nav-graficos" role="tabpanel"
                                  aria-labelledby="nav-graficos-tab"></canvas>
                          </div>
  
  
                          <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
                              <div class="offcanvas-header">
                                  <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Seleccione el año deseado</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                              </div>
                              <div class="offcanvas-body">
                                  
                                  <div>
                                      <select class="form-select" id="selectYear">
                                          <option value="2022">2022</option>
                                          <option value="2023">2023</option>
                                          <option value="2024">2024</option>
                                          <!-- Agrega más opciones según tus necesidades -->
                                      </select>
                                  </div>
                                  <div class="input-group">
                                      <input type="text" class="form-control" placeholder="Buscar..."
                                          aria-label="Buscar..." id="filtro_txt">
                                      <div class="input-group-append">
                                          <button class="btn btn-outline-secondary" type="button"
                                              onclick="aplicafiltros()"><img src="Img/lupa.png"
                                                  width="17px"></button>
                                      </div>
                                  </div>
                                  <button onclick="dibuja_pagina([-1,-1])" style="color:black;"
                                  class="dropdown-item mt-2"><b>Cerrar Sesión</b></button>
                                
                              </div>
                          </div>
  
                          <form class="formulario_Dinamico ">
  
                              <button class="btn btn-primary floating-btn">
                                  <i class="bi bi-tsunami"></i>
                              </button>
                              <button class="btn btn-primary floating2-btn">
                                  <i class="bi bi-calendar"></i>
                              </button>
  
  
  
                              <div class="container mt-4">
                                  <div class="row justify-content-center">
                                      <button class="btn btn-primary col-2" onclick="window.location.href=\'inicio.html\'">
                                          <i class="bi bi-house-door"></i> Inicio
                                      </button>
                                  </div>
                              </div>
                          </form>
                      </section>
  ';
return $seccionvcn;

}

function cargarFormVacacionesEmpleado(){

    $seleccionvcn ='<div class="row  h-100 justify-content-center my-4">
               <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
                   <br>
                   <div class="card shadow-2-strong shadow">
   
   
   
                       <section class="row justify-content-center col-10 col-md-6 col-sm-6 col-xs-6 offset-1 offset-lg-3 ">
                           <label class="tipo">Tipo</label>
                               <div class="mb-3  col-12  input-group-lg "  style="margin-left: 16px;">
                                   
                                   <div class="btn-group-vertical col-12 ">
                                       <button type="button" class="btn btn-light  mb-2">Solicitud de Vacaciones</button>
                                       <button type="button" class="btn btn-light col-6 mb-2">Asistencia</button>
                                       <button type="button" class="btn btn-light col-6 mb-2">Material</button>
                                       
                                     </div>
                                 
                                              
                                           
                                       
                                   
                                   </div>
                                       
                               <form class="formulario_Dinamico ">
                               
                              
                           
                               
                               <div class="row col-11 justify-content-center">
                                   <button
                                       class="col-5  offset-1   btn mt-3 " onclick="dibuja_pagina([0,3,0])"
                                       onmouseover="this.style.backgroundColor=\'#d6d5d3\'"
                                       onmouseout="this.style.backgroundColor=\'#ffffff\';">
                                       Cancelar
                                   </button>
                                   <button class="col-5 offset-1 btn  mt-3 " onclick="" type="submit"
                                       style="color:#0265bd; font-size: 20px; text-align: center;" onmouseover="this.style.backgroundColor=\'#d6d5d3\'"
                                       onmouseout="this.style.backgroundColor=\'#ffffff\';"  id="save" >
                                       Siguiente
                                   </button></div><br>
                           </form>
           
                   </div>
               </div>
           </div>
   ';



    return $seleccionvcn;

}