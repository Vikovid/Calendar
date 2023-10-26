<?php 
	//Bibliotecas
	include ('includes/header.php');
	include ('DB/db.php');
	include ('functions/crud.php'); 
	include ('vendor/autoload.php');
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
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    // Verifica si existe un token de acceso
    if (isset($_SESSION['token']))
        $cliente->setAccessToken($_SESSION['token']);//Se establece el token de acceso en el cliente de Google

?>
<?php
	if (isset($_POST['agendar'])) {
		//Recolecta los datos
		$fecha = $_POST['fecha'];
        $hora =  $_POST['hora'];
        $nota =  $_POST['nota'];

		//Si el usuario Decide sincronizar la cita con Google Calendar
		if (isset($_POST['sincronizar'])) {
			//Si el usuario está autenticado y el token de acceso no ha expirado
			if (!$cliente->isAccessTokenExpired()) {
				date_default_timezone_set('America/Mexico_City');
				$fechaFin = date('Y-m-d', strtotime($fecha));
				$horaFin = date('H:i:s', strtotime('-6 hours', strtotime($hora)));
				$iniCita = new DateTime("$fechaFin $horaFin");
				$finCita = clone $iniCita;
				$finCita->modify('+30 minutes');

				$servicioCalendario = new Google_Service_Calendar($cliente);
				$evento = new Google_Service_Calendar_Event(array(
					'summary' => 'Cita',
					'description' => $nota,
					'start' => array(
						'dateTime' => $iniCita->format('Y-m-d\TH:i:s'),
						'timeZone' => 'America/Mexico_City',
					),
					'end' => array(
						'dateTime' => $finCita->format('Y-m-d\TH:i:s'),
						'timeZone' => 'America/Mexico_City',
					),
				));
				$calendarioId = 'primary';
				$eventoCreado = $servicioCalendario->events->insert($calendarioId, $evento);

				if ($eventoCreado){
					$idCalendar = $eventoCreado->getId();
					createCalendar($fecha,$hora,$nota,$idCalendar);
					echo "El evento se creó correctamente en Google Calendar.";
				}
				else 
					echo "Error al crear el evento en Google Calendar.";
			}
		} else 
			create($fecha,$hora,$nota);
	}
	$citas = read();
?>

<h1>Sincronización de datos con Google Calendar <span>(Versión 2)</span></h1>
	<?php if ($cliente->isAccessTokenExpired()) { ?>
		<p><b>Para sincronizar citas con Google Calendar, primero debe <a href="<?php echo $cliente->createAuthUrl();?>">Iniciar sesión con Google</a></b></p>
	<?php } else {?>
		<a href="actions/logout.php"> Cerrar Sesión</a>
	<?php } ?>
<form name="form1" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="fecha">
        <label>Introduzca una fecha: </label>
        <input type="date" name="fecha">
    </div>
    <br>
    <div class="hora">
        <label>Introduzca la hora: </label>
        <input type="time" name="hora">
    </div>
    <br>
    <div class="nota">
        <label>Descripción: </label>
        <textarea name="nota"></textarea>
    </div>
    <br>
    <div class="botones">
		<nav>
			&nbsp;
			<?php if (!$cliente->isAccessTokenExpired()):?>
				<label>Sincronizar con Google Calendar</label>
				<input type="checkbox" name="sincronizar" value="Sincronizar con Google">
			<?php endif; ?>
			<button type="submit" name="agendar">
				Agendar Cita
			</button>
		</nav>
		<br>
    </div>
</form>

<div class="tabla">
	<br>
	<br>
	<table>
		<thead>
			<tr>
				<th>Fecha</th>
				<th>Hora</th>
				<th>Nota</th>
				<th>Acción</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($citas as $cita):?>
				<tr>
					<td> <?php echo $cita['fecha']; ?> </td>
					<td> <?php echo $cita['hora']; ?> </td>
					<td> <?php echo $cita['nota']; ?> </td>
					<td> 
						<a href="actions/edit.php?id=<?php echo $cita['id']?>">Editar</a>
						<a href="actions/delete.php?id=<?php echo $cita['id']?>">Eliminar</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<br>
</div>
<?php include('includes/footer.php') ?>