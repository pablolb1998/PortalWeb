<?php
require_once "Conexion.php";

function comprueba_usuario($usuario, $contraseña){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Comprueba los datos de la tabla de clientes
    $sql = "EXECUTE seguridadunificada_identidad_select  @Usuario=?, @Contrasena=?;";
    $parm = array($usuario, $contraseña);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            return array($row['IdUsuario'], $row["Usuario"], $row["Nombre"],$row["IdTipoUsuario"]);
        }
    }
    sqlsrv_close( $conn );
}

//Mostrar las sociedades a la que este vinculado el cliente
function compruebaSociedades($Id){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    
    if($_SESSION["Controlador"] -> miEstado ->  tipo_App == 1){
        $sql = "SELECT S.idSociedad,S.NombreFiscal FROM Sociedades S 
        INNER JOIN dbo.Clientes_Sociedades CS ON CS.idSociedad = S.idSociedad WHERE idCliente =?;";
    }elseif($_SESSION["Controlador"] -> miEstado ->  tipo_App == 2){
        
        $sql = "SELECT S.idSociedad,S.NombreFiscal FROM Personal P 
        INNER JOIN dbo.Sociedades S ON P.idSociedad = S.idSociedad WHERE idpersonal =?;";
    }
    $ListaSociedades = array();
    $parm = array($Id);
    $stmt = sqlsrv_query($conn, $sql,$parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaSociedades, $row);
        }
        return $ListaSociedades;
    }
    sqlsrv_close( $conn );
}

//extraer los documentos del cliente
function extraerDoc_parcial($tipoDoc = null,$ids = null,$c = null){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Estraer los documentos
    $sql = "SELECT id,codigo,Fecha,NombreComercial,Descripcion,Estado,tipo,PinDescargas,OrigenImpresion,importe,PinDescargas,color
    FROM vw_PortalCliente_Documentos 
    WHERE tipo=? AND IdSociedad=? AND IdCliente=? 
    ORDER BY Fecha DESC, Codigo DESC;";
    $ListaDoc = array();
    $parm = array($tipoDoc,$ids, $c );
    $stmt = sqlsrv_query($conn, $sql, $parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaDoc, $row);
        }
        return $ListaDoc;
    }
    sqlsrv_close( $conn );
}

//extraer el contador de documentos del cliente
function extraerFiltroDoc_Parcial($tipoDoc = null,$ids = null,$c = null){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Estraer los documentos
    $sql = "SELECT tipo,IdSociedad,Estado,Filtro
    FROM vw_PCDocumentos_Web_Agrupados 
    WHERE tipo=? AND IdSociedad=? AND IdCliente=?;";
    $ListaF = array();
    $parm = array($tipoDoc,$ids, $c );
    $stmt = sqlsrv_query($conn, $sql, $parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaF, $row);
        }
        return $ListaF;
    }
    sqlsrv_close( $conn );
    
}

function ExtraerDocumento_Por_CIF($Tipo = null,$Id = null,$cif = null){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Estraer los documentos
    $DocCif = array();
    $sql = "SELECT PinDescargas,OrigenImpresion,id
     FROM dbo.vw_PortalCliente_Documentos WHERE tipo=? AND CONVERT(VARCHAR(50),HashBytes('MD5', CAST(id AS VARCHAR(50))),2)=? AND cif=?;";
    $parm = array($Tipo,$Id,$cif);
    $stmt = sqlsrv_query($conn, $sql, $parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($DocCif, $row);
        }
        return $DocCif;
    }
    sqlsrv_close( $conn );
    
}

//funciones de extraer datos de la jornada, actualizar para juntarlos
function comprueba_jornada_personal(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Comprueba los datos de la tabla de clientes
    $sql = "SELECT Fecha,Estado,IdCentroTrabajo FROM  dbo.vw_Personal_Estado_Appsheet WHERE idpersonal = ?;";
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            return array($row["Estado"],$row['Fecha'],$row['IdCentroTrabajo']);
        }
    }
    sqlsrv_close( $conn );
} 

function extraer_JornadaHistorico(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Comprueba los datos de la tabla de clientes
    $sql = "SELECT FechaInicio,FechaFin,HoraInicioFin,DiaIto,DuracionHoras 
    FROM  dbo.vw_TiemposPersonal_historico WHERE idpersonal = ? ORDER BY FechaInicio DESC ;";
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
    $DatosBD = array();
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($DatosBD, $row);
        }
        return $DatosBD;
    }
    sqlsrv_close( $conn );
} 

//extraer todos los doc del personal
function extraerDocPersonal_Masivo(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $DatosBD = array();
    //1 - Asistencias
        $sql1 = "SELECT IdPersonalAsistencia AS id,tipo AS descripcion,Justificada AS descripcion2 ,* FROM dbo.vw_PEAsistencias 
        WHERE IdPersonal = ?";
        $DatosBD = array();
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql1,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 1;
                array_push($DatosBD, $row);
            }
        }
    //2 - Contratos
        $sql2 = "SELECT idpersonalcontrato AS id,tipo AS descripcion,EstadoContrato AS descripcion2,
        fechainicio AS FechaInicio, ISNULL(fechafin,'1900-01-01 00:00:00') AS FechaFin,observaciones FROM dbo.vw_PEContratos 
        WHERE idpersonal = ? ORDER BY fechainicio DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql2,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 2;
                array_push($DatosBD, $row);
            }
        }
    //3 - Documentos
        $sql2 = "SELECT IdArchivo AS id, Nombre AS descripcion, tipoArchivo AS descripcion2,FechaCreacionRegistro AS FechaInicio,
        IdTipoPropietario,IdPropietario,* 
        FROM dbo.vw_PEArchivosPersonal 
        WHERE idpersonal = ? ORDER BY fechainicio DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql2,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 3;
                array_push($DatosBD, $row);
            }
        }
    //4 - Formacion
        $sql2 = "SELECT IdPersonalFormacion AS id,Curso AS descripcion,validada AS descripcion2,Fecha AS FechaInicio 
        FROM dbo.vw_PEFormacion
        WHERE IdPersonal = ? ORDER BY Fecha DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql2,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 4;
                array_push($DatosBD, $row);
            }
        }
    //5 - Incidencias
        $sql5 = "SELECT IdPersonalIncidencia AS id,TipoIncidencia AS descripcion,Justificada AS descripcion2,Fecha AS FechaInicio 
        FROM dbo.vw_PEIncidencias
        WHERE IdPersonal = ? ORDER BY Fecha DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql5,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 5;
                array_push($DatosBD, $row);
            }
        }

    //6 - Material
        $sql6 = "SELECT IdPersonalMaterial AS id,Material AS descripcion,Cantidad AS descripcion2,Fecha AS FechaInicio 
        FROM dbo.vw_PEMaterial
        WHERE IdPersonal = ? ORDER BY Fecha DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql6,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 6;
                array_push($DatosBD, $row);
            }
        }
    //7 - Nominas
        $sql7 = "SELECT IdPersonalSalario AS id,Liquido AS descripcion,SalarioBruto AS descripcion2,FechaPago AS FechaInicio 
        FROM dbo.vw_selectSalariosAppsheet
        WHERE IdPersonal = ? ORDER BY Fecha DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql7,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 7;
                array_push($DatosBD, $row);
            }
        }
   
   
        return $DatosBD;
    sqlsrv_close( $conn );

}

//iniciar o finalizar jornada
function exec_up_Tiempos_Insert(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = "DECLARE @IdTiempo INT;
    EXEC up_Tiempos_Insert
    @IdTiempo = @IdTiempo OUTPUT,
	@IdSociedad = ?,
	@IdCentroTrabajo = ?,
	@IdPersonal = ?,
    @FechaInicio = ?,
	@InicioFin= ?,
    @FechaImputacion = ?";
    $parm = array($_SESSION["Controlador"] -> miEstado -> id_sociedad, 
            $_SESSION["Controlador"] -> miEstado ->EstadoJornada[2],
            $_SESSION["Controlador"] -> miEstado -> IdPersonal,
            date('Ymd H:i:s'),
            $_SESSION["Controlador"] -> miEstado ->EstadoJornada[0],
            date('Ymd H:i:s'));
    // print_r($parm);
    // print_r('<br>');
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    } 
    sqlsrv_close( $conn );

}

function comprobarBD($c){
    $conn = ConexionBD("85.214.41.17,23459","IntecoDistribucion");
    //sacar el primero en caso de que coincidan
    $sql = "SELECT TOP 1 CA.BBDD,ISNULL(ca.ServidorExterno,CA.Servidor) AS Servidor ,ISNULL(CA.PuertoExterno,CA.Puerto) AS Puerto FROM Clientes C 
    INNER JOIN dbo.Clientes_Aplicaciones CA ON CA.IdCliente = C.IdCliente WHERE Codigo LIKE ?;";
    $DatosBD = array();
    $parm = array($c);
    //$parm = array($IdCliente);
    $stmt = sqlsrv_query($conn,$sql,$parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($DatosBD, $row);
        }
        return $DatosBD;
    }
    sqlsrv_close( $conn );
}

?>
