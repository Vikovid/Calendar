<?php
    include ("../DB/db.php");

    function create ($fecha, $hora, $nota) {
        global $connection;
        $sql = "INSERT INTO citas (id,fecha,hora,nota,idEvent) "; 
        $sql.= "VALUES (NULL,'$fecha','$hora','$nota',NULL) ";

        $result = mysqli_query($connection, $sql);
        if (!$result)
            die("Query Failed");
            
        return true;
    }
    function createCalendar($fecha, $hora, $nota, $idEvent) {
        global $connection;
        $sql = " INSERT INTO citas (id, fecha, hora, nota, idEvent) ";
        $sql.= " VALUES (NULL,'$fecha','$hora','$nota','$idEvent') ";

        $result = mysqli_query($connection, $sql);
        if (!$result)
            die("Query Failed");

        return true;
    }
    function read() {
        global $connection;
        $sql = "SELECT * FROM citas ORDER BY fecha ASC";

        $result = mysqli_query($connection, $sql);
        if (!$result)
            die("Query Failed");

        $rows = array();
        while ($row = mysqli_fetch_assoc($result))
            $rows[] = $row;

        return $rows;
    }        
    function update ($id, $fecha, $hora, $nota) {
        global $connection;
        $sql = "UPDATE citas ";
        $sql.= "SET fecha ='$fecha', hora = '$hora', nota = '$nota' ";
        $sql.= "WHERE id = $id ";

        $result = mysqli_query($connection,$sql);
        if (!$result)
            die("Query Failed");

        return true;
    }
    function delete ($id) {
        global $connection;
        $sql = "DELETE FROM citas WHERE id=$id LIMIT 1";
    
        $result=mysqli_query($connection,$sql);
        if (!$result)
            die ("Query Failed");
    
        return true;
    }
    function readById($id) {
        global $connection;
        $sql = "SELECT * FROM citas WHERE id = $id LIMIT 1";

        $result = mysqli_query($connection, $sql);
        if (!$result)
            die("Query Failed");

        $row = mysqli_fetch_assoc($result);
        return $row;
    }        
?>