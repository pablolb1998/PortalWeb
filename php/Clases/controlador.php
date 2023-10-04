<?php
require_once 'estado.php';
require 'pinta.php';
require_once './consultas.php';

class Controlador
{
    public $miEstado;
    //valor por defecto en caso de no recibir
    function __construct($Estado = null){
        $this -> miEstado = new Estado();
        $this -> miEstado -> Estado = 0;
        $this -> miEstado -> Documentos = array();
        $this -> miEstado -> FiltrosDoc = array();
        $this -> miEstado -> acciones = array("archivos"=> 0,
                                        "observaciones" => 0,
                                        "añadir" => 0);
        $this -> miEstado -> tipo_App = $_SESSION["TipoPortal"];
        if(isset($_SESSION["header"])){
            $this -> miEstado -> header = $_SESSION["header"];
            $_SESSION["header"] = null;
        }else{
            $header_Empresa = '../html/header.html';
            $header = fopen($header_Empresa, "r");
            $this -> miEstado -> header = fread($header,filesize($header_Empresa));
            fclose($header);
        }
        
        //$this -> miEstado -> Estado = $IP;
    }
    //al final del script iguala la variable de Sesion al estado
    function __destruct(){
        $_SESSION["Controlador"]= $this;
    }
    function almacenarDocumentos($doc){
        $this -> miEstado["documentos"] = $doc;
    }


    function almacenarDatosSociedad($ls,$ids){
        if($ls!="" && $ls!= null){
            $this -> miEstado["lista_sociedades"] = $ls;
        }
        if($ids!="" && $ids!= null){
            $this -> miEstado["id_sociedad"] = $ids;
        }
    }
    
    //funciones de sql
    function comprobarBBDD($c){
        $datosBBDD = comprobarBD($c);
        if(!isset($datosBBDD[0]["Puerto"])){
            $this -> miEstado -> IP = $datosBBDD[0]["Servidor"];
        }else{
            $this -> miEstado -> IP = $datosBBDD[0]["Servidor"].','.$datosBBDD[0]["Puerto"];
        }
        //pruebas
        if($_SESSION["pinC"] == 123654){
            $this -> miEstado -> IP = '192.168.204.111';
        }elseif ($_SESSION["pinC"] == '65814415D75C') {
            $this -> miEstado -> IP = '81.169.167.5,23459';
        }
            
        $this -> miEstado -> bbdd = $datosBBDD[0]["BBDD"];
    }

    function IniciarSesion($usr, $pass){
        //si no encuentra usuario devuelve false
        $datosSesion = comprueba_usuario($usr, $pass);
        if($datosSesion != false){
            $this -> miEstado -> nombre_descriptivo = $datosSesion[2];
            $this -> miEstado -> IdIdentidad = $datosSesion[4];
            if($this -> miEstado -> tipo_App == 1){
                //PORTAL DEL CLIENTE: Añadir variables de sesion al comprobar usuario
                    $this -> miEstado -> IdCliente = $datosSesion[0];
                    $this -> miEstado -> nombre_cliente = $datosSesion[1];
                    $this -> miEstado -> lista_sociedades = compruebaSociedades($this -> miEstado -> IdCliente);
            }elseif($this -> miEstado -> tipo_App == 2){
                //PORTAL DEL EMPLEADO: Añadir variables de sesion al comprobar usuario
                $this -> miEstado -> IdPersonal = $datosSesion[0];
                $this -> miEstado -> lista_sociedades = compruebaSociedades($this -> miEstado -> IdPersonal);
                $this -> miEstado -> EstadoJornada = comprueba_jornada_personal();
                $this -> miEstado -> permisosSecciones = comprobarPermisosUsuarios();
                $this -> miEstado -> Documentos = extraerDocPersonal_Masivo();
                $this -> miEstado -> archivostiposAccesos = extraerArchivosTiposAccesos();
                $this -> miEstado -> linkDocumentoSubido = null;
            }
            return true;
        }else{
            return false;
        }
        
    }


    function ExtraerDoc_conCIF($Tipo = null,$Id = null,$cif = null){

        ExtraerDocumento_Por_CIF($Tipo,$Id,$cif);
    }
    //llama a la funcion que extrae los documentos y llena la clase estado con los mismos
    function cargaDocumentos_Parcial($tipoDoc = null){
        //optimizar para evitar conexión
        $doc = extraerDoc_parcial($tipoDoc, $this -> miEstado -> id_sociedad , $this -> miEstado -> IdCliente);
        $filtros = extraerFiltroDoc_Parcial($tipoDoc, $this -> miEstado -> id_sociedad , $this -> miEstado -> IdCliente);
        $this -> miEstado -> puntero_posicion = 30;
        if($doc !== null){
            switch ($tipoDoc) {
                case 1 :
                    if(empty($this -> miEstado -> Pedidos)){
                        $this -> miEstado -> Pedidos = $doc;
                        $this -> miEstado -> Documentos = array_merge($this -> miEstado -> Documentos, $doc);
                        $this -> miEstado -> FiltrosDoc = array_merge($this -> miEstado -> FiltrosDoc, $filtros);
                        //$this -> miEstado -> FiltrosP = $filtros;
                    }
                    break;
                case 2 :
                    if(empty($this -> miEstado -> Albaranes)){
                        $this -> miEstado -> Albaranes = $doc;
                        $this -> miEstado -> Documentos = array_merge($this -> miEstado -> Documentos, $doc);
                        $this -> miEstado -> FiltrosDoc = array_merge($this -> miEstado -> FiltrosDoc, $filtros);
                        //$this -> miEstado -> FiltrosA = $filtros;
                    }
                    break;
                case 3 :
                    if(empty($this -> miEstado -> Facturas)){
                        $this -> miEstado -> Facturas = $doc;
                        $this -> miEstado -> Documentos = array_merge($this -> miEstado -> Documentos, $doc);
                        $this -> miEstado -> FiltrosDoc = array_merge($this -> miEstado -> FiltrosDoc, $filtros);
                        //$this -> miEstado -> FiltrosF = $filtros;
                    }
                    break;
                case 4 :
                    if(empty($this -> miEstado -> Proyectos)){
                        $this -> miEstado -> Proyectos = $doc;
                        $this -> miEstado -> Documentos = array_merge($this -> miEstado -> Documentos, $doc);
                        $this -> miEstado -> FiltrosDoc = array_merge($this -> miEstado -> FiltrosDoc, $filtros);
                        //$this -> miEstado -> FiltrosPro = $filtros;
                    }
                    break;
                default:
                if(empty($this -> miEstado -> Presupuestos)){
                    $this -> miEstado -> Presupuestos = $doc;
                    $this -> miEstado -> Documentos = array_merge($this -> miEstado -> Documentos, $doc);
                    $this -> miEstado -> FiltrosDoc = array_merge($this -> miEstado -> FiltrosDoc, $filtros);
                    //$this -> miEstado -> FiltrosPre = $filtros;
                }
                    break;
            }
        }
    }
    //colocar sociedad
    function setSociedad($idS,$nS = null){
        $this -> miEstado -> id_sociedad = $idS;
        $ns = "";
        foreach ($_SESSION["Controlador"] -> miEstado -> lista_sociedades as $valor) {
            if($valor["idSociedad"] == $idS){
                $ns = $valor["NombreFiscal"];
            }
        }
        $this -> miEstado -> NombreSociedad = $ns; 
    }
    // iniciar o finalizar la jornada segun el estadp 
    function iniciarFinalizarJornada(){
        exec_up_Tiempos_Insert();
        if($this -> miEstado -> EstadoJornada[0] == 1){
            $this -> miEstado -> EstadoJornada[0] = 0;
        }else{
            $this -> miEstado -> EstadoJornada[0] = 1;
        }
        $this -> miEstado -> EstadoJornada[1] = date('Y-m-d H:i');
    }  
    //Navegación entre pestañas
    function navegarPestanas($ps){
        //volver a la aterior
        if($ps == -1){
            //salir del modo formulario
            
            if($this -> miEstado -> cargarForm == 1){
                $this -> miEstado -> cargarForm = 0;
            }else{
                $estadoAnterior = array_shift($this -> miEstado -> EstadosAnteriores);
                $this -> miEstado -> Estado = $estadoAnterior;
            }
            //reinicializar variables
            $this -> miEstado -> IdPropietario = null;
        }else{
            array_unshift($this -> miEstado -> EstadosAnteriores , $this -> miEstado -> Estado);
            $this -> miEstado -> Estado = $ps;
        }
        $this -> miEstado -> CadenaFiltro = null;
        $this -> cargarPermisosAcciones();
    }

    function cerrarSesion(){
    //Cerrar sesion reinicializando variables
        $this -> miEstado = new Estado();
        $this -> miEstado -> Estado = 0;
        $this -> miEstado -> Documentos = array();
        $this -> miEstado -> FiltrosDoc = array();
        $this -> miEstado -> tipo_App = $_SESSION["TipoPortal"];
        $this -> miEstado -> acciones = array("archivos"=> 0,
                                        "observaciones" => 0,
                                        "añadir" => 0);
        if(isset($_SESSION["header"])){
            $this -> miEstado -> header = $_SESSION["header"];
            $_SESSION["header"] = null;
        }else{
            $header_Empresa = '../html/header.html';
            $header = fopen($header_Empresa, "r");
            $this -> miEstado -> header = fread($header,filesize($header_Empresa));
            fclose($header);
        }
        
    }

    function cargarDatosForm(){
    //generar los formularios dinamicamente
        if(!isset($this -> miEstado -> formularios) || $this -> miEstado -> formularios == null){
            $json = '../php/documentos/Formularios_PE.json';
            $jsonData = file_get_contents($json);
            $formularios = json_decode($jsonData, true);
            $this -> miEstado -> formularios = $formularios['Pantallas'];
            $this -> miEstado -> dropdownsFormularios = extraerDropdownsFormsValores();
        }       
    }

    function cargarPermisosAcciones(){
        //Resetear el acceso a las acciones
        $this -> miEstado -> acciones["archivos"] = 0;
        $this -> miEstado -> acciones["anadirLinea"] = 0;
        switch ($this -> miEstado -> Estado) {   
            case 4.1 :
                $this -> miEstado -> acciones["archivos"] = 1;
                $this -> miEstado -> acciones["anadirLinea"] = 1;
                break;
            case 4.3 :
                $this -> miEstado -> acciones["archivos"] = 1;
                break;
            case 4.4 :
                $this -> miEstado -> acciones["anadirLinea"] = 1;
                break;
            case 4.5:
                $this -> miEstado -> acciones["archivos"] = 1;
                $this -> miEstado -> acciones["anadirLinea"] = 1;
                break;
            case 4.6:
                $this -> miEstado -> acciones["archivos"] = 1;
                $this -> miEstado -> acciones["anadirLinea"] = 1;
                break;
            case 4.7:
                $this -> miEstado -> acciones["archivos"] = 1;
                $this -> miEstado -> acciones["anadirLinea"] = 1;
                break;
            case 4.8:
                break;
            default:
                $this -> miEstado -> IdTipoPropietario = null;
                break;
        }

    }

    //Subir los archivos mediante llamada al servicio web
    function subirArchivosServicioWeb($pin,$IdtipoPropietario,$idPropietario,$idArchivoTipo,$url,$nombre_archivo){
        $url = "http://onixsw.esquio.es:8080/Funciones.aspx?SubirArchivo=1&pin=".$pin.
        "&IdTipoPropietario=".$IdtipoPropietario.'&IdPropietario='.$idPropietario.
        '&IdArchivoTipo='.$idArchivoTipo.'&URL='.$url.'&NombreArchivo='.$nombre_archivo;
        
        $response = file_get_contents($url);
        print_r($response);
        print_r($url);
        return true;
    }


    //funciones de para generar el contenido
    // optimizar el navegar pestañas
    function generarContenido($arrayDatos = array()){
        $arrayAuxiliarHtml = array();
        $msgError = "" ;
        $c = $this -> miEstado -> Estado;
        $this -> comprobarBBDD($_SESSION["pinC"]);
        $nav = "";
        if($c === 0 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 1){
            //PORTAL CLIENTE
            //inicia sesión y navega entre pestañas del portal del comercial
                $InicioS = $this -> IniciarSesion($arrayDatos[0], $arrayDatos[1]);
                $nav = 0;
                if(count($this -> miEstado -> lista_sociedades) == 1 && $InicioS){
                    $nav = 2;
                    $this -> setSociedad($this -> miEstado -> lista_sociedades[0]["idSociedad"]);
                }elseif($InicioS){
                    $nav = 1;
                }else{
                    $msgError = "Usuario o contraseña Incorrectos" ;
                }
                $this -> navegarPestanas($nav);
        }elseif($c === 0 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 2){
            //PORTAL EMLEADO
            $InicioS = $this -> IniciarSesion($arrayDatos[0], $arrayDatos[1]);
            $nav=0;
            if($InicioS){
                $nav = 2;
                $this -> setSociedad($this -> miEstado -> lista_sociedades[0]["idSociedad"]);
            }else{
                $msgError = "Usuario o contraseña Incorrectos" ;
            }
            $this -> navegarPestanas($nav);
        }elseif($this -> miEstado -> tipo_App == 1.5 && ($c === 0 ||($c === 0.5 && empty($arrayDatos))) ){
            //PORTAL CLIENTE DESCARGAR DOC
            //DESCARGAR DOCUMENTO MEDIANTE CIF
                $nav = 0.5;
                $this -> navegarPestanas($nav);
        }elseif($this -> miEstado -> tipo_App == 1.5 && $c === 0.5 && !empty($arrayDatos)){ 
            //PORTAL CLIENTE DESCARGAR DOC
            //DESCARGAR DOCUMENTO MEDIANTE CIF
            $DatosDoc = ExtraerDocumento_Por_CIF($_SESSION["tipoArchivo"],$_SESSION["IdDocumento"],$arrayDatos[0]);
            if(count($DatosDoc)>0){
                $valor = $DatosDoc[0]; 
                $id = $valor["id"];
                $PinDescargas = $valor["PinDescargas"];
                $Origen_impresion = $valor["OrigenImpresion"];
                $linkDescarga = 'http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerPDF=1&pin=' . $PinDescargas .'&IdOrigenImpresion='. $Origen_impresion .'&IdPropietario='. $id;
                return array($linkDescarga,'',1);
                //session_abort();
                die;
            } else {
                $msgError = "CIF Incorrecto";
            }
                

        }elseif($c === 1 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 1){
        //PORTAL CLIENTE coloca los datos de la sociedad seleccionada y navega a la siguiente pestaña
            $this -> setSociedad($arrayDatos[0]);
            $this -> navegarPestanas(2);
        }elseif ($c === 2 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 1) {
        //PORTAL CLIENTE la navegación va en funcion del tipo de documento 
            $nav = null;
            $this -> cargaDocumentos_Parcial($arrayDatos[0]);
            switch ($arrayDatos[0]) {
                case 1 :
                    $nav = 4;
                    break;
                case 2 :
                    $nav = 5;
                    break;
                case 3 :
                    $nav = 6;
                    break;
                default:
                    $nav = 3;
                    break;
            }
            $this -> navegarPestanas($nav);
        }elseif ($c === 2 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 2) {
        //PORTAL EMPLEADO la navegación Menu principal 
            $nav = null;
            switch ($arrayDatos[0]) {
                case 1 :
                    $nav = 4;
                    break;
                case 2 :
                    $this -> miEstado -> EstadoJornada = comprueba_jornada_personal();//comprobar el estado actual
                    $this -> miEstado -> HistoricoJornada = extraer_JornadaHistorico();// extraer el historico de la jornada
                    $nav = 5;//Pestaña de jornada
                    break;
                case 3 :
                    $nav = 6;
                    break;
                case 4 :
                    $nav = 7;
                    foreach($this -> miEstado -> Documentos as $documento){
                        if( $documento["tipoDocPortal"] != 3){
                            $dc = array($documento["descripcion"],
                                    $documento["FechaInicio"]-> format('Y-m-d H:i:s'),
                                    $documento["FechaInicio"]-> format('Y-m-d H:i:s'),
                                    "#800080",
                                    $documento["tipoDocPortal"]
                                );
                            array_push($arrayAuxiliarHtml,$dc);

                        }
                        
                    }
                    
                    break;
                default:
                    $nav = 3;
                    break;
            }
            
            $this -> miEstado -> puntero_posicion = 30;
            $this -> navegarPestanas($nav);
        }elseif ($c === 4 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 2) {
        //navegacion del submenu y adicion de acciones
            
            //PORTAL EMPLEADO  la navegación Submenu
            switch ($arrayDatos[0]) {   
                case 4.1 :
                    $this -> miEstado -> IdTipoPropietario = 143;
                    break;
                case 4.3 :
                    $this -> miEstado -> IdTipoPropietario = 24;
                    break;
                case 4.4 :
                    $this -> miEstado -> IdTipoPropietario = 23;
                    $this -> miEstado -> IdPropietario = $this -> miEstado -> IdPersonal;
                    break;
                case 4.5:
                    $this -> miEstado -> IdTipoPropietario = 144;
                    break;
                case 4.6:
                    $this -> miEstado -> IdTipoPropietario = 142;
                    break;
                case 4.7:
                    $this -> miEstado -> IdTipoPropietario = 145;
                    break;
                case 4.8:
                    $this -> miEstado -> IdTipoPropietario = 25;
                    break;
                default:
                    $this -> miEstado -> IdTipoPropietario = null;
                    break;
            }
           
            $this -> navegarPestanas($arrayDatos[0]);
        }elseif ($c > 4 && $c < 5 && $c != 4.4 && !empty($arrayDatos) && $arrayDatos[0] == 4.4 && $this -> miEstado -> tipo_App == 2) {
        //Navegar a la pestaña de documentos  
            //indicar el propietario
            //$this -> reinicializarAccionesArchivos();
            $this -> miEstado -> IdPropietario = $arrayDatos[1];
            $this -> navegarPestanas($arrayDatos[0]);
        }elseif($c == 4.4 && !empty($arrayDatos) && $arrayDatos[1] == 6 && $arrayDatos[2] != null ){
        //Navegar a la pestaña de firma desde documentos
            $this -> miEstado -> IdDocumentoPadre = $arrayDatos[2];
            $this -> miEstado -> cargarFormFirma = 1;
           
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == -1 && $arrayDatos[1] == 0 ){
        //Volver a la anterior pestaña
            $this -> navegarPestanas(-1);
        }elseif($this -> miEstado -> Estado == 7  && $this -> miEstado -> tipo_App == 2 && empty($arrayDatos) &&  $this -> miEstado -> cargarForm == 0){
        // cargar la pestaña de calendario
            foreach($this -> miEstado -> Documentos as $documento){
                if( $documento["tipoDocPortal"] != 3){
                    $dc = array($documento["descripcion"],
                            $documento["FechaInicio"]-> format('Y-m-d H:i:s'),
                            $documento["FechaInicio"]-> format('Y-m-d H:i:s'),
                            "#800080",
                            $documento["tipoDocPortal"]
                        );
                    array_push($arrayAuxiliarHtml,$dc);

                }
                
            }
        }elseif($this -> miEstado -> Estado == 7  && $this -> miEstado -> tipo_App == 2 && !empty($arrayDatos) &&  $this -> miEstado -> cargarForm == 1){
        // navegacion al formulario correspondiente desde la pestaña de calendario



        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 1 && in_array($this -> miEstado -> Estado, array(3,4,5,6,7)) && $this -> miEstado -> tipo_App == 1){
            //PORTAL CLIENTE
        //Sumar de 30  en 30 al puntero de mostrar documentos
            $this -> miEstado -> puntero_posicion += 30;
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 0 && in_array($this -> miEstado -> Estado, array(3,4,5,6,7)) && $this -> miEstado -> tipo_App == 1){
        //PORTAL CLIENTE filtrar por cadena de texto
            $this -> miEstado -> CadenaFiltro = null;
            $this -> miEstado -> tipofiltro = 1;
            if (!empty($arrayDatos[2])){
                $this -> miEstado -> CadenaFiltro = $arrayDatos[2];
            }
            $this -> miEstado -> puntero_posicion += count($this -> miEstado -> Documentos);
            

        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 2 && !empty($arrayDatos[2]) && in_array($this -> miEstado -> Estado, array(3,4,5,6,7)) && $this -> miEstado -> tipo_App == 1){
        //PORTAL CLIENTE filtrar por estado
            $this -> miEstado -> tipofiltro = 2;
            $this -> miEstado -> puntero_posicion += count($this -> miEstado -> Documentos);
            $this -> miEstado -> CadenaFiltro = $arrayDatos[2];
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3  && $this -> miEstado -> tipo_App == 2 && !isset($arrayDatos[2])){
        //Portal empleado cargar Formulario
            if($this -> miEstado -> Estado != 7){
                $this -> cargarDatosForm();
            }
            $this -> miEstado -> cargarForm = 1;
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3 && isset($arrayDatos[2])  && $this -> miEstado -> tipo_App == 2 ){
        //portal del empleado guardar o cancelar en la pestaña de formulario
            $this -> miEstado -> camposFormularios = null;
            if($arrayDatos[2] != 0){
                $arrayForm = array_filter($this -> miEstado -> formularios, function ($form) {
                    return $form["Estado"] == $this -> miEstado -> Estado;
                });
                $arrayIntermedio = array_shift($arrayForm);
                $arraycampos = $arrayIntermedio["Campos"];
                $arrayValores = array();
                foreach($arraycampos as $campo){
                    if($campo["OUTPUT"] == 0 && $campo["Mostrar"] == 0 && $campo["ValorAdicional"] == null){
                        $valorCampo = isset($campo["VariableAlmacenada"]) ? $_SESSION["Controlador"] -> miEstado -> {$campo["VariableAlmacenada"]}  : $campo["ValorPorDefecto"];
                        if($valorCampo == "%now%"){
                            $valorCampo = date('Ymd H:i:s');
                        }
                        array_push($arrayValores,$valorCampo);
                    }elseif($campo["Mostrar"] == 1 && $campo["ValorAdicional"] == null){
                        $valorCampo = array_shift($arrayDatos[2]);
                        if (DateTime::createFromFormat('Y-m-d\TH:i', $valorCampo) !== false) {
                            $valorCampo = DateTime::createFromFormat('Y-m-d\TH:i', $valorCampo) -> format('Ymd H:i:s');
                        }elseif(DateTime::createFromFormat('Y-m-d', $valorCampo) !== false){
                            $valorCampo = DateTime::createFromFormat('Y-m-d', $valorCampo) -> format('Ymd');
                        }
                        array_push($arrayValores,$valorCampo);
                    }
                }
                
                //subir archivo al direcotrio teporal
                

                    // $directorio_destino = "archivos_subidos/"; // Ruta donde deseas almacenar los archivos subidos
                    // $nombre_archivo = $_FILES["archivo"]["name"];
                    // $_SESSION["DirectorioRaizEmpresa"]
             
                $resultadoEjecucion = true;

                if( $this -> miEstado -> Estado == 4.4 ){
                    $temp = explode('/',$arrayValores[1]);
                    $nombreA = end($temp);
                    $this -> subirArchivosServicioWeb($_SESSION["pinC"],$this -> miEstado -> IdTipoPropietario,$this -> miEstado -> IdPropietario,$arrayValores[4],$arrayValores[1],$nombreA);
                }else{
                    $resultadoEjecucion = exect_Insert_From_Dinamico($arrayValores);
                    if($resultadoEjecucion == false ){
                        $msgError = "Ha ocurrido un error al insertal el registro";
                    }else{
                        array_unshift($_SESSION["Controlador"] -> miEstado -> Documentos,$resultadoEjecucion[0]);
                    }
                }
                
            }
            $this -> miEstado -> cargarForm = 0;
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 0 && $this -> miEstado -> Estado == 5 && $this -> miEstado -> tipo_App == 2){
            //PORTAL Empleado
            //iniciar/ finalizar jornada en la pestaña de tiempos
            $this -> iniciarFinalizarJornada();
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == -1 && $arrayDatos[1] == -1){
            $this -> cerrarSesion();
        }
        //navega a la siguiente pestaña segun el estado de la clase estado
        // optimizar todo el script de pintar
        // return pinta_contenido($this -> miEstado -> Estado)."idc :".$this -> miEstado -> IdCliente."pin :".$_SESSION["pinC"]."Estado:".$this -> miEstado -> Estado."tipo:".$nav."ip :".$this -> miEstado -> IP."bbdd :".$this -> miEstado -> bbdd);
        return array(pinta_contenido($this -> miEstado -> Estado, $this -> miEstado -> tipo_App)."A:".$this -> miEstado -> tipo_App. "idc :".$this -> miEstado -> IdCliente.
        $this -> miEstado -> IdPersonal."pin :".$_SESSION["pinC"]."Estado:".$this -> miEstado -> Estado."tipo:".$nav."ip :".$this -> miEstado -> IP."bbdd :".$this -> miEstado -> bbdd."IdTP :". $this -> miEstado -> IdPropietario,$msgError,0,$arrayAuxiliarHtml);
        
    }
}
    

?>