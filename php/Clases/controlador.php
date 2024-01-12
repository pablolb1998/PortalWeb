<?php
require_once 'estado.php';
require 'pinta.php';
require_once './consultas.php';

class Controlador
{
    public $miEstado;
    //valor por defecto en caso de no recibir
    function __construct($Estado = null){
        
            $_SESSION["pinC"] = $_COOKIE['pinCPortalE'];
        if(isset($_COOKIE['LogoUsuarioPortalE'])){
            $_SESSION["imgLogo"] = $_COOKIE['LogoUsuarioPortalE'];
        }
            
        
            $_SESSION["TipoPortal"] = $_COOKIE['TipoPortalE'];
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
        $IdTipoApp;
        if($datosSesion != false){
            $this -> miEstado -> nombre_descriptivo = $datosSesion[2];
            $this -> miEstado -> IdIdentidad = $datosSesion[4];
            $this -> miEstado -> permisosSecciones = comprobarPermisosUsuarios();
            if($this -> miEstado -> tipo_App == 1){
                //PORTAL DEL CLIENTE: Añadir variables de sesion al comprobar usuario
                    $this -> miEstado -> IdCliente = $datosSesion[0];
                    $this -> miEstado -> nombre_cliente = $datosSesion[1];
                    $this -> miEstado -> lista_sociedades = compruebaSociedades($this -> miEstado -> IdCliente);
                    $this -> miEstado -> PersonasContacto = compruebaPersonasContaco();
                    $this -> miEstado -> archivostiposAccesos = extraerArchivosTiposAccesos();
                    $IdTipoApp = 32;
            }elseif($this -> miEstado -> tipo_App == 2){
                //PORTAL DEL EMPLEADO: Añadir variables de sesion al comprobar usuario
                $this -> miEstado -> IdPersonal = $datosSesion[0];
                $this -> miEstado -> lista_sociedades = compruebaSociedades($this -> miEstado -> IdPersonal);
                $this -> miEstado -> EstadoJornada = comprueba_jornada_personal();
                $this -> miEstado -> Documentos = extraerDocPersonal_Masivo();
                $this -> miEstado -> archivostiposAccesos = extraerArchivosTiposAccesos();
                $this -> miEstado -> linkDocumentoSubido = null;
                $IdTipoApp = 33;
                $this -> cargarDatosForm();
            }
            crearRegistroEntrada_SeguridadUnificada($this -> miEstado -> IdIdentidad,$IdTipoApp);
            return true;
        }else{
            return false;
        }
        
    }

    //llama a la funcion que extrae los documentos y llena la clase estado con los mismos
    function cargaDocumentos_Parcial($tipoDoc = null){
        //optimizar para evitar conexión
        $doc = null;
        $filtros  = null;
        $fecha = date('Y-m-d H:i:s');
        $primeraEjecucion = false;
        $this -> miEstado -> puntero_posicion = 30;
        
            switch ($tipoDoc) {
                case 1 :
                    if(empty($this -> miEstado -> FechaPedidos)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaPedidos = $fecha;
                    break;
                case 2 :
                    if(empty($this -> miEstado -> FechaAlbaranes)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaAlbaranes = $fecha;
                    break;
                case 3 :
                    if(empty($this -> miEstado -> FechaFacturas)){
                        
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaFacturas = $fecha;
                    break;
                case 4 :
                    if(empty($this -> miEstado -> FechaProyectos)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaProyectos = $fecha;
                    break;
                case 5:
                    if(empty($this -> miEstado -> FechaTareas)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaTareas = $fecha;
                    break;
                default:
                    if(empty($this -> miEstado -> FechaPresupuestos)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaPresupuestos = $fecha;
                    break;
            }
        if($primeraEjecucion){
            $doc = extraerDoc_parcial($tipoDoc, $this -> miEstado -> id_sociedad , $this -> miEstado -> IdCliente);
            $filtros = extraerFiltroDoc_Parcial($tipoDoc, $this -> miEstado -> id_sociedad , $this -> miEstado -> IdCliente);
        }else{
            $doc = extraerDoc_parcial($tipoDoc, $this -> miEstado -> id_sociedad , $this -> miEstado -> IdCliente,$fecha);
        }
        if($doc !== null){
            $this -> miEstado -> Documentos = array_merge($doc, $this -> miEstado -> Documentos );
        }
        if($filtros !== null){
            $this -> miEstado -> FiltrosDoc = array_merge($this -> miEstado -> FiltrosDoc, $filtros);
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
    function iniciarFinalizarJornada($lat = null,$long = null ){
        exec_up_Tiempos_Insert($lat,$long);
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
            $this -> miEstado -> nombreDocumentoPadre = null;
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
        $this -> miEstado -> acciones["adjunto"] = 0;
        $this -> miEstado -> acciones["descarga"] = 0;
        $this -> miEstado -> acciones["anadirLinea"] = 0;
        $this -> miEstado -> acciones["modalValidaciones"] = 0;
        $this -> miEstado -> acciones["modalSubirDoc"] = 0;
        $this -> miEstado -> acciones["desplegado"] = 0;
        if($this -> miEstado -> tipo_App == 2){
            //Portal del empleado
            switch ($this -> miEstado -> Estado) {   
                case 4.1 :
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["desplegado"] = 1;
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
                    $this -> miEstado -> acciones["archivos"] = 1;
                    break;
                case 4.9:
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["desplegado"] = 1;
                    break;
                default:
                    $this -> miEstado -> IdTipoPropietario = null;
                    break;
            }
        }else{
            //Portal del comercial
            switch ($this -> miEstado -> Estado) { 
                case 6 :
                    $this -> miEstado -> acciones["descarga"] = 1;
                    $this -> miEstado -> acciones["desplegado"] = 1;
                    break;
                case 8 :
                    $this -> miEstado -> acciones["modalValidaciones"] = 1;
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["desplegado"] = 1;
                    $this -> miEstado -> IdTipoPropietario = null;
                    break;
                case 9 :
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["modalSubirDoc"] = 1;
                    $this -> miEstado -> acciones["adjunto"] = 1;
                    
                    $this -> miEstado -> IdTipoPropietario = 2;
                    break;
                default:
                    $this -> miEstado -> acciones["descarga"] = 1;
                    break;
            }
        }

    }

    //Subir los archivos mediante llamada al servicio web
    function subirArchivosServicioWeb($pin,$IdtipoPropietario,$idPropietario,$idArchivoTipo,$url,$nombre_archivo){
        $url = "http://onixsw.esquio.es:8080/Funciones.aspx?SubirArchivo=1&pin=".$pin.
        "&IdTipoPropietario=".$IdtipoPropietario.'&IdPropietario='.$idPropietario.
        '&IdArchivoTipo='.$idArchivoTipo.'&URL=http://'.urlencode($url).'&NombreArchivo='.$nombre_archivo;
        
        $response = file_get_contents($url);
        return true;
    }
    function descargaArchivoServicoWeb($IdP,$nombre_archivo,$tipoDoc,$OI = null){
        $nombre_archivo = "";
        if($arrayDatos[3] == "OPDF"){
            $url_archivo ="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerPDF=1&pin=".$_SESSION["pinC"].'&IdOrigenImpresion='.$OI.'&IdPropietario='.$IdP;
            $nombre_archivo = $nombre_archivo;
        }else{
            $url_archivo ="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerArchivo=1&pin=".$_SESSION["pinC"].'&IdArchivo='.$IdP;
            $nombre_archivo = $nombre_archivo;
        }
        

        
        $directorio_destino = "subidasTemp/".$_SESSION["pinC"]."/";
        // Verificar si el directorio existe, si no, crearlo
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }

        // Realiza la solicitud HTTP para obtener el contenido del archivo
        $response = file_get_contents($url_archivo);
        if ($response === false) {
            $msgError = 'Error al obtener el archivo desde el servicio web.';
        } else {
            // Específica la ruta donde deseas guardar el archivo
            $rutaGuardado = $directorio_destino.$nombre_archivo; // Reemplaza esto con la ruta y nombre deseado
        
            // Guarda el contenido en un archivo en el servidor
            if (file_put_contents($rutaGuardado, $response) !== false) {
                //$abrirNuebaPestaña = 1;
                return array('php/'.$rutaGuardado,1,1);
            } else {
                return 0;
            }
        }


    }

    //funciones de para generar el contenido
    // optimizar el navegar pestañas
    function generarContenido($arrayDatos = array()){
        $arrayAuxiliarHtml = array();
        $msgError = "" ;
        $abrirNuebaPestaña = 0;
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
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 10){
        //Abrir la pestaña de cambio de contraseña
            
            $this -> navegarPestanas($arrayDatos[0]); 
        }elseif(!empty($arrayDatos) && $this -> miEstado -> Estado == 11){
            //Abrir la pestaña de cambio de contraseña
            if(strlen($arrayDatos[0])<5){
                $msgError = 'La nueva contraseña ha de tener una longitud mínima de 5 caracteres.';
            }elseif($arrayDatos[0] === $arrayDatos[1]){
                if(reestablecerContrasenaConfirmar($arrayDatos[0])){ 
                    $msgError = 'Contraseña modificada con exito.';
                    if($_SESSION["pinC"] == 123654){
                        $this -> cerrarSesion();
                        return array('../portaldecliente/Index.html',0,1,$msgError);
                    }else{
                        return array('../Index.html',0,1,$msgError);
                    }
                        
                    
                }else{
                    $msgError = 'No se ha podido modificar la contraseña.';
                }

            }else{
                $msgError = 'La contraseña nueva ha de coincidir';
            }
             
        }elseif(!empty($arrayDatos) && $c == 10 && $arrayDatos[0] != -1){
        //confirmar el cambio de contraseña
            if(strlen($arrayDatos[2])<5){
                $msgError = 'La nueva contraseña ha de tener una longitud mínima de 5 caracteres.';
            }elseif($arrayDatos[2] === $arrayDatos[1]){
                if(cambiarContrasena($arrayDatos[0],$arrayDatos[1])){
                    $msgError = 'Contraseña modificada con exito.';
                }else{
                    $msgError = 'No se ha podido modificar la contraseña.';
                }

            }else{
                $msgError = 'La contraseña nueva ha de coincidir';
            }
               // print_r(pruebaC());

        }elseif($c === 1 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 1){
        //PORTAL CLIENTE coloca los datos de la sociedad seleccionada y navega a la siguiente pestaña
            $this -> setSociedad($arrayDatos[0]);
            $this -> navegarPestanas(2);
        }elseif ($c === 2 && !empty($arrayDatos) && $arrayDatos[0] != -1  && $this -> miEstado -> tipo_App == 1) {
        //PORTAL CLIENTE la navegación va en funcion del tipo de documento 
            $nav = null;
            $this -> cargaDocumentos_Parcial($arrayDatos[0]);
            //$msgError = "entre";
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
                case 4:
                    $nav = 7;
                    break;
                case 5:
                    $nav = 8;
                    break;
                default:
                    $nav = 3;
                    break;
            }
            $this -> navegarPestanas($nav);
        
        }elseif($c === 8 && !empty($arrayDatos) && $arrayDatos[0] != -1  && $arrayDatos[0] != 0 && $this -> miEstado -> tipo_App == 1) {
        //Portal del cliente navegacion a la pestaña de archivos de cada documento 
                $this -> miEstado -> IdPropietario = $arrayDatos[1];
                $this -> miEstado -> ArchivosDocumento = extraerArchivosCliente_Documento();
            //print_r($arrayDatos[0]);
            $this -> navegarPestanas($arrayDatos[0]);
        }elseif($c == 9 && !empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3 && isset($arrayDatos[2]) && $this -> miEstado -> tipo_App ==  1){
        //guardar el archivo
                $subida = $this -> subirArchivosServicioWeb($_SESSION["pinC"],
                                                    2,
                                                    $this -> miEstado -> IdPropietario,
                                                    $arrayDatos[2]['IdAT'],
                                                    $arrayDatos[2]['Archivo'],
                                                    $arrayDatos[2]['Nombre']);
                $this -> miEstado -> ArchivosDocumento = extraerArchivosCliente_Documento();
                $msgError = 'Documento adjuntado con exito.';
          
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
                            if( !in_array($documento["tipoDocPortal"],array(2,3,4))){
                                $fi;
                                $ff;
                                try{
                                   $fi = ($documento["FechaInicio"] ? $documento["FechaInicio"] -> format('Y-m-d H:i:s') : '1900-01-01 00:00:00');
                                   $ff = ($documento["FechaFin"] ? $documento["FechaFin"] -> format('Y-m-d H:i:s') : '1900-01-01 00:00:00');
                                }catch (e){
            
                                }
                                if($ff && $fi){
                                    $dc = array($documento["descripcion"],
                                        $fi,
                                        $ff,
                                        $documento["color"],
                                        $documento["tipoDocPortal"]
                                    );
                                    array_push($arrayAuxiliarHtml,$dc);
            
                                }
                                
            
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
                case 4.9:
                    //Vacaciones de personal
                    $this -> miEstado -> IdTipoPropietario = 146;
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

            switch ($this -> miEstado -> Estado) {
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

            $arrayDoc = array_values(array_filter($this -> miEstado -> Documentos, function ($docF) use($tipoDocf) {
                return $docF["tipoDocPortal"] == $tipoDocf
                && $docF["id"] ==  $this -> miEstado -> IdPropietario;
            }));
            $this -> miEstado -> nombreDocumentoPadre = $arrayDoc[0]['descripcion'];
            $this -> navegarPestanas($arrayDatos[0]);
        }elseif($c == 4.4 && !empty($arrayDatos) && $arrayDatos[1] == 6 && $arrayDatos[2] != null ){
        //Navegar a la pestaña de firma desde documentos
            $this -> miEstado -> IdDocumentoPadre = $arrayDatos[2];
            //$this -> miEstado -> cargarFormFirma = 1;
           
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == -1 && $arrayDatos[1] == 0 ){
        //Volver a la anterior pestaña
            $this -> navegarPestanas(-1);
        }elseif($this -> miEstado -> Estado == 7  && $this -> miEstado -> tipo_App == 2 ){
        // cargar la pestaña de calendario
        //print_r($this -> miEstado -> Estado);
            foreach($this -> miEstado -> Documentos as $documento){
                if( !in_array($documento["tipoDocPortal"],array(2,3,4))){
                    $fi;
                    $ff;
                    try{
                       $fi = ($documento["FechaInicio"] ? $documento["FechaInicio"] -> format('Y-m-d H:i:s') : '1900-01-01 00:00:00');
                       $ff = ($documento["FechaFin"] ? $documento["FechaFin"] -> format('Y-m-d H:i:s') : '1900-01-01 00:00:00');
                    }catch (e){

                    }
                    if($ff && $fi){
                        $dc = array($documento["descripcion"],
                            $fi,
                            $ff,
                            $documento["color"],
                            $documento["tipoDocPortal"]
                        );
                        array_push($arrayAuxiliarHtml,$dc);

                    }
                    

                }
                
            }
        //}elseif($this -> miEstado -> Estado == 7  && $this -> miEstado -> tipo_App == 2 && !empty($arrayDatos) &&  $this -> miEstado -> cargarForm == 1){
        // navegacion al formulario correspondiente desde la pestaña de calendario

        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 1 && in_array($this -> miEstado -> Estado, array(3,4,5,6,7,8,9)) && $this -> miEstado -> tipo_App == 1){
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
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 4 && in_array($this -> miEstado -> Estado, array(3,4,5,6,7,9)) && $this -> miEstado -> tipo_App == 1){
        //PORTAL CLIENTE descargar documentos en local cristal report o archivos subidos a onix
            // $arrayDatos[2][0] 
            // $arrayDatos[2][1] 
            //print_r($arrayDatos[2][2]);
            //$descarga = descargaArchivoServicoWeb($arrayDatos[2][0],$arrayDatos[2][2],$arrayDatos[3],$arrayDatos[2][1]);
            $nombre_archivo = "";
            if($arrayDatos[3] == "OPDF"){
                $url_archivo ="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerPDF=1&pin=".$_SESSION["pinC"].'&IdOrigenImpresion='.$arrayDatos[2][1].'&IdPropietario='.$arrayDatos[2][0];
                $nombre_archivo = $arrayDatos[2][2];
            }else{
                $url_archivo ="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerArchivo=1&pin=".$_SESSION["pinC"].'&IdArchivo='.$arrayDatos[2][0];
                $nombre_archivo = $arrayDatos[2][1];
            }
            

            
            $directorio_destino = "subidasTemp/".$_SESSION["pinC"]."/";
            // Verificar si el directorio existe, si no, crearlo
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0755, true);
            }

            // Realiza la solicitud HTTP para obtener el contenido del archivo
            $response = file_get_contents($url_archivo);
            if ($response === false) {
                $msgError = 'Error al obtener el archivo desde el servicio web.';
            } else {
                // Específica la ruta donde deseas guardar el archivo
                $rutaGuardado = $directorio_destino.$nombre_archivo; // Reemplaza esto con la ruta y nombre deseado
            
                // Guarda el contenido en un archivo en el servidor
                if (file_put_contents($rutaGuardado, $response) !== false) {
                    //$abrirNuebaPestaña = 1;
                    return array('php/'.$rutaGuardado,1,1);
                } else {
                    $msgError = 'Error al guardar el archivo en el servidor.';
                }
            }
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3  && $this -> miEstado -> tipo_App == 1 && $c == 8){
        //Portal del cliente insertar la solicitud correspondiente
                //print_r("entre");
                $solicitud = SolicitudTareas_Insert($arrayDatos[2][1]);
                if($solicitud){
                    $msgError = 'Solicitud subida correctamente';
                }else{
                    $msgError = 'No se ha podido realizar su solicitud';
                }
                $this -> cargaDocumentos_Parcial(5);
                
                
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3  && $this -> miEstado -> tipo_App == 2 && !isset($arrayDatos[2])){
        //Portal empleado cargar Formulario
            if($this -> miEstado -> Estado != 7){
                $this -> cargarDatosForm();
            }
            $this -> miEstado -> cargarForm = 1;
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 0 && $c == 4.4  && $this -> miEstado -> tipo_App == 2 && isset($arrayDatos[2])){
        //portal del empleado marcar como dirmado  documento (optimizar)
            
            foreach($this -> miEstado -> Documentos as $doc){
                if($doc['id'] == $arrayDatos[2] && $doc['tipoDocPortal'] == 3 ){
                    $doc['Firmado'] = 1;
                }
            }

        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3 && isset($arrayDatos[2])  && $this -> miEstado -> tipo_App == 2 ){
        //portal del empleado guardar o cancelar en la pestaña de formulario
            $this -> miEstado -> camposFormularios = null;


            if( $this -> miEstado -> Estado == 4.4 ){
                $subida = $this -> subirArchivosServicioWeb($_SESSION["pinC"],
                                                $this -> miEstado -> IdTipoPropietario,
                                                $this -> miEstado -> IdPropietario,
                                                $arrayDatos[2][0],
                                                $arrayDatos[3],
                                                $arrayDatos[4]);
                 //$this -> subirArchivosServicioWeb($_SESSION["pinC"],$this -> miEstado -> IdTipoPropietario,$this -> miEstado -> IdPropietario,$arrayValores[4],$arrayValores[1],$nombreA);
            }




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
                        }elseif($valorCampo == '%randmKey%'){
                            $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $claveGenerada = '';

                            for ($i = 0; $i < 9; $i++) {
                                $claveGenerada .= $caracteres[rand(0, strlen($caracteres) - 1)];
                            }
                            $valorCampo =$claveGenerada;
                        }
                        array_push($arrayValores,$valorCampo);
                    }elseif($campo["TipoDatoHtml"] == "file"){
                        $valorCampo = $arrayDatos[3];
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

               if($arrayIntermedio["Instruccion"] != ""){
                $resultadoEjecucion = exect_Insert_From_Dinamico($arrayValores);
               }
                
                if($resultadoEjecucion == false ){
                    $msgError = "Ha ocurrido un error al insertal el registro";
                }else{
                    array_unshift($_SESSION["Controlador"] -> miEstado -> Documentos,$resultadoEjecucion[0]);
                }
                
                
            }
            $this -> miEstado -> cargarForm = 0;
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 0 && $this -> miEstado -> Estado == 5 && $this -> miEstado -> tipo_App == 2){
        //PORTAL Empleado Iniciar/ finalizar jornada en la pestaña de tiempos
            if(isset($arrayDatos[2]) && isset($arrayDatos[3])){
                $this -> iniciarFinalizarJornada($arrayDatos[2],$arrayDatos[3]);
            }else{
                $this -> iniciarFinalizarJornada();
            }
            //$this -> miEstado -> EstadoJornada = comprueba_jornada_personal();//comprobar el estado actual
            $this -> miEstado -> HistoricoJornada = extraer_JornadaHistorico();// extraer el historico de la jornada
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == -1 && $arrayDatos[1] == -1){
            $this -> cerrarSesion();
        }
        //navega a la siguiente pestaña segun el estado de la clase estado
        // optimizar todo el script de pintar
        // return pinta_contenido($this -> miEstado -> Estado)."idc :".$this -> miEstado -> IdCliente."pin :".$_SESSION["pinC"]."Estado:".$this -> miEstado -> Estado."tipo:".$nav."ip :".$this -> miEstado -> IP."bbdd :".$this -> miEstado -> bbdd);
        //print_r($this -> miEstado -> Estado);
        $txtErr = '';
        if($_SESSION["pinC"] == 123654){
            $txtErr = "A:".$this -> miEstado -> tipo_App. "idI :".$this -> miEstado -> IdIdentidad.
            $this -> miEstado -> IdPersonal."pin :".$_SESSION["pinC"]."Estado:".$this -> miEstado -> Estado."tipo:".$nav."ip :".$this -> miEstado -> IP."bbdd :".$this -> miEstado -> bbdd."IdTP :". $this -> miEstado -> IdTipoPropietario;
        }
        
        return array(pinta_contenido($this -> miEstado -> Estado, $this -> miEstado -> tipo_App).$txtErr,$msgError,$abrirNuebaPestaña,$arrayAuxiliarHtml);
        
    }
}
    

?>