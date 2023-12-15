
<?php
class Estado{
    //variables de estado?
    public $Estado;
    public $EstadosAnteriores = array();
    public $IdIdentidad;

    //variables documentos
    public $tipo_documento;
    public $filtrado_estado;

    public $nombreDocumentoPadre;

    //public $documentos = array();
    public $punteros;
    public $puntero_posicion;
    public $numero_total_documentos;

    public $Documentos = array();
    public $ArchivosDocumento = array();
    public $FiltrosDoc = array();
    
    public $CadenaFiltro;
    public $tipofiltro;

    
    //variables sociedad
    public $lista_sociedades = array();
    public $id_sociedad;
    public $NombreSociedad;
    public $PersonasContacto = array();

    //datos del cliente
    public $IdCliente;
    public $nombre_cliente;
    public $nombre_descriptivo;

    //datos del empleado
    public $IdPersonal;
    public $permisosSecciones = array();
    public $EstadoJornada = array();
    public $HistoricoJornada;

    //datos navegacion archivos PE
    public $IdTipoPropietario;
    public $IdPropietario;
    
    //generador formularios
    public $cargarForm;
    public $formularios = array();
    public $dropdownsFormularios = array();
    public $formulariosDuplasValores; 
    public $cargarFormFirma;
    public $IdDocumentoPadre;
    public $archivostiposAccesos;
    public $linkDocumentoSubido;

    //Datos de conexion
    public $IP;
    public $bbdd;

    //Datos de aplicación
    public $tipo_App;
    public $header;
    public $footer;

    //acciones
    public $acciones = array();
}

?>