<?php
    //Obtener los datos del POST
    $id = $_POST['id'];
    $matricula = $_POST['matricula'];
    //Obtener todas las respuestas hechas
    $respuestas = array();
    foreach ($_POST as $key => $value) {
        if ($key != 'id' && $key != 'matricula') {
            $respuestas[$key] = $value;
        }
    }
    //Subir las respuestas a la base de datos e incluir el archivo de config.php
    include("php/config.php");
    $conn = new mysqli($host, $usename, $password, $database);
    //Verificar si la conexión fue exitosa, si no, mostrar mensaje de error
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    //Subir las respuestas a la base de datos
    foreach ($respuestas as $key => $value) {
        $sql = "INSERT INTO respuestas (id, examen, matricula, pregunta, respuesta) VALUES (NULL, '$id', '$matricula', '$key', '$value')";
        $conn->query($sql);
    }
    //Poner el examen del alumno como terminado
    $sql = "UPDATE examenes SET activo = 0 WHERE id = '$id' AND alumno = '$matricula'";
    $conn->query($sql);
    //Cerrar la conexión
    $conn->close();
    //Redirigir al alumno a la página de inicio
    echo "<script>window.location.href = '/index.php';</script>";
?>