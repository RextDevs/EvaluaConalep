<?php
    //Iniciar la sesión
    session_start();
    if(isset($_SESSION['session'])){
        if($_SESSION['expire'] <= time()){
            session_unset();
            session_destroy();
            echo "<script>window.location.href = '/login.php';</script>";
        }
        $_SESSION['expire'] = time() + 3600;
    }
    else {
        echo "<script>window.location.href = '/login.php';</script>";
    }
    //Incluir el archivo /php/config.php <= <= 
    include("php/config.php");
    //Probar la conexión con la base de datos, si funcionar mostrar mensaje de conexión exitosa, si no, mostrar mensaje de error
    $conn = new mysqli($host, $usename, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    //Obtener la ID del examen y matricula del alumno, si no hay, redirigir a la página de inicio
    if (isset($_GET['id']) && isset($_GET['matricula'])) {
        $id = $_GET['id'];
        $matricula = $_GET['matricula'];
        $GLOBAL_ALUMNO_MATRICULA = $matricula;
    }
    else {
        echo "<script>window.location.href = '/index.php';</script>";
    }
    $GLOBAL_EXAMEN_ID = $id;
    //Verificar si la matricula del alumno es correcta, si no, redirigir a la página de inicio
    $sql = "SELECT * FROM examenes WHERE id = '$id' AND alumno = '$matricula' and activo = 1" or die("Error in the consult.." . mysqli_error($conn));
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo "<script>window.location.href = '/index.php';</script>";
    }
    //Obtener el nombre del alumno
    $sql = "SELECT * FROM estudiantes WHERE matricula = '$matricula'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $GLOBAL_ALUMNO_NOMBRE = $row['nombre'];
    //Obtener el nombre del examen
    $sql = "SELECT * FROM examenes WHERE id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $id_materia = $row['materia'];
    //Obtener el nombre de la materia y clave
    $sql = "SELECT * FROM materia WHERE id = '$id_materia'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $GLOBAL_MATERIA_NOMBRE = $row['nombre'];
    $GLOBAL_MATERIA_CLAVE = $row['clave'];
    //Obtener cada una de las preguntas del examen y guardarlas en un diccionario
    $sql = "SELECT * FROM preguntas WHERE examen = '$id'";
    $result = $conn->query($sql);
    $GLOBAL_PREGUNTAS = array();
    while($row = $result->fetch_assoc()) {
        $GLOBAL_PREGUNTAS[$row['id']] = $row;
    }

?>

<!DOCTYPE html>
<html data-bs-theme="dark" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Cuestionario</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>

<body style="background: var(--bs-dark-border-subtle);">
    <form action="submit.php" method="post">
        <div class="d-lg-flex flex-wrap justify-content-lg-center">
            <div style="width: 80%;background: var(--bs-body-bg);border-radius: 16px;margin: 12px;">
                <div>
                    <div style="height: 10px;background: #bc6d0a;"></div>
                    <h2>Examen</h2>
                    <h6><?php echo $GLOBAL_MATERIA_CLAVE; ?> - <?php echo $GLOBAL_MATERIA_NOMBRE; ?></h6>
                    <hr><span><?php echo $GLOBAL_ALUMNO_NOMBRE; ?> - <?php echo $GLOBAL_ALUMNO_MATRICULA; ?></span>
                </div>
            </div>
            <?php
                //Por cada una de las preguntas, crear preguntas
                foreach($GLOBAL_PREGUNTAS as $pregunta) {
                    //Si el tipo es texto
                    if($pregunta['tipo'] == 0) {
                        echo "<div style='width: 80%;background: var(--bs-body-bg);border-radius: 16px;margin: 12px;'>
                                <div>
                                    <h3>".$pregunta['nombre']."</h3>
                                </div>
                                <div style='padding: 10PX;'>
                                    <input class='form-control' type='text' style='margin-top: 12px;margin-bottom: 12px;border-style: hidden;border-bottom-style: solid;border-bottom-color: var(--bs-emphasis-color);' name=".$pregunta['id']." placeholder='Respuesta de texto' required=''>
                                </div>
                            </div>";
                    }
                    //Si el tipo es radio
                    if($pregunta['tipo'] == 1) {
                        echo "<div style='width: 80%;background: var(--bs-body-bg);border-radius: 16px;margin: 12px;'>
                                <div>
                                    <h3>".$pregunta['nombre']."</h3>
                                </div>
                                <div style='padding: 10PX;'>";
                        //Obtener las respuestas de la pregunta
                        $sql = "SELECT * FROM p_respuestas WHERE pregunta = '".$pregunta['id']."'";
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()) {
                            echo "<div class='form-check'><input class='form-check-input' type='radio' id='formCheck-1' style='border-style: solid;border-color: var(--bs-emphasis-color);border-bottom-color: var(--bs-emphasis-color);' name=".$pregunta['id']." value=".$row['id']."><label class='form-check-label' for='formCheck-1'>".$row['nombre']."</label></div>";
                        }
                        echo "</div>
                        </div>";
                    }
                    //Si el tipo es checkbox
                    if($pregunta['tipo'] == 2) {
                        echo "<div style='width: 80%;background: var(--bs-body-bg);border-radius: 16px;margin: 12px;'>
                                <div>
                                    <h3>".$pregunta['nombre']."</h3>
                                </div>
                                <div style='padding: 10PX;'>";
                        //Obtener las respuestas de la pregunta
                        $sql = "SELECT * FROM p_respuestas WHERE pregunta = '".$pregunta['id']."'";
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()) {
                            echo "<div class='form-check'><input class='form-check-input' type='checkbox' id='formCheck-4' style='border-style: solid;border-color: var(--bs-emphasis-color);' name=".$pregunta['id']." value=".$row['id']."><label class='form-check-label' for='formCheck-4'>".$row['nombre']."</label></div>";
                        }
                        echo "
                            </div>
                            </div>";
                    }
                }
            ?>
        </div>
        <input type="hidden" name="id" value="<?php echo $GLOBAL_EXAMEN_ID; ?>">
        <input type="hidden" name="matricula" value="<?php echo $GLOBAL_ALUMNO_MATRICULA; ?>">
        <div class="d-flex flex-wrap justify-content-center" style="margin-top: 12px;">
            <button class="btn btn-primary" type="submit" style="margin: 12px;">Enviar</button>
        </div>
    </form>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>