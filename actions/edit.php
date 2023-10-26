<?php 
    include ('../includes/header.php');
    include ('../functions/crud.php');
    include ('../vendor/autoload.php');

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
?>

<?php
    if (isset($_POST['actualizar'])) {
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $nota = $_POST['nota'];
        
        if (isset($_SESSION['token']) && $idGcalendar != '') {
            try {
                    // Instancia de Google_Service_Calendar
                $servicioCalendario = new Google_Service_Calendar($cliente);
                // Obtener el evento existente
                $evento = $servicioCalendario->events->get('primary', $idGcalendar);
            
                date_default_timezone_set('America/Mexico_City');
                $nuevaFecha = date('Y-m-d', strtotime($fecha));
                $nuevaHora = date('H:i:s', strtotime('-6 hours', strtotime($hora)));
                $nuevoIniCita = new DateTime("$nuevaFecha $nuevaHora");
                $nuevoFinCita = clone $nuevoIniCita;
                $nuevoFinCita->modify('+30 minutes');
            
                $evento->setDescription($nota);
                $evento->getStart()->setDateTime($nuevoIniCita->format('Y-m-d\TH:i:s'));
                $evento->getEnd()->setDateTime($nuevoFinCita->format('Y-m-d\TH:i:s'));
            
                $eventoActualizado = $servicioCalendario->events->update('primary', $evento->getId(), $evento);
            } catch (Google_Service_Exception $e) {
                echo "Error al actualizar el evento en Google Calendar: ".$e->getMessage();
            }
        }
        
        update($id,$fecha,$hora,$nota);
        header("location: ../index.php");
    }
?>

<h1>Editar cita del: <?php echo $cita['fecha'];?> </h1>
<form name="form1" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?id='.$id; ?>">
    <div class="fecha">
        <label>Introduzca una fecha: </label>
        <input type="date" name="fecha" value="<?php echo $cita['fecha']?>">
    </div>
    <br>
    <div class="hora">
        <label>Introduzca la hora: </label>
        <input type="time" name="hora" value="<?php echo $cita['hora']?>">
    </div>
    <br>
    <div class="nota">
        <label>Descripción: </label>
        <textarea name="nota"><?php echo $cita['nota']?></textarea>
    </div>
    <br>
    <div class="botones">
        <button type="submit" name="actualizar">
            Actualizar
        </button>
    </div>
</form>

<?php include ('../includes/footer.php');?>