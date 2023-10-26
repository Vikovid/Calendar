<?php
    include ("../includes/header.php");
    include ("../functions/crud.php");
    include ("../vendor/autoload.php");

    //Instancia de Google_Client para identificar al usuario
    $cliente = new Google_Client();
    $cliente->setClientId('954800726997-rb4nujm1loup384ioa6a2kj3vq1p0fdd.apps.googleusercontent.com');
    $cliente->setClientSecret('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');
    $cliente->setRedirectUri('http://localhost/proyecto1/index.php');
    $cliente->addScope(Google_Service_Calendar::CALENDAR);
    //Autentifica el usuario de Google
    if (isset($_GET['code'])) {
        $token = $cliente->fetchAccessTokenWithAuthCode($_GET['code']); //Se obtiene el Token de Acceso
        $_SESSION['token'] = $token;
        header('Location: ../index.php');
        exit;
    }
    // Verifica si existe un token de acceso
    if (isset($_SESSION['token']))
        $cliente->setAccessToken($_SESSION['token']);//Se establece el token de acceso en el cliente de Google

    $id = $_GET['id'];  
    $cita = readById($id);
    $idGcalendar = $cita['idEvent'];

    if ( isset($_SESSION['token']) && $idGcalendar != '' ) {
        $cliente->setAccessToken($_SESSION['token']);
        $servicioCalendario = new Google_Service_Calendar($cliente);
        
        try {
            $servicioCalendario->events->delete('primary',$idGcalendar);
        } catch (Google_Service_Exception $e) {
            echo "error al eliminar el evento de Google Calendar: ".$e->getMessage();
        }
    }
    delete($id);
    header("location: ../index.php");
?>