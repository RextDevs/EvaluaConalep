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
    //Obtener los datos del alumno y demás datos de la base de datos
    $matricula = $_SESSION['matricula'];
    $sql = "SELECT * FROM estudiantes WHERE matricula = '$matricula'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //Si el estudiante existe, obtener el nombre del estudiante
        while($row = $result->fetch_assoc()) {
            $nombre = $row["nombre"];
            $matricula = $row["matricula"];
            $curp = $row["curp"];
            $grupo = $row["grupo"];
            $correo = $row["correo"];
            $telefono = $row["telefono"];
            $apellidos = $row["apellidos"];
            $fe_nac = $row["fe_nac"];
            $genero = $row["genero"];
        }
    } else {
        //Si el estudiante no existe, destruir session y mandar al login
        session_unset();
        session_destroy();
        header("Location: /login.php");
    }
    //Obtener el grupo del alumno
    $sql = "SELECT * FROM grupos WHERE id = '$grupo'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //Si el grupo existe, obtener el nombre del grupo
        while($row = $result->fetch_assoc()) {
            $grupo_number = $row["grupo"];
            $carrera = $row["carrera"];
            $turno = $row["turno"];
            $plantel = $row["plantel"];
        }
    } else {
        //Si el grupo no existe, mostrar mensaje de error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    //Obtener el semestre del alumno
    $sql = "SELECT * FROM semestre WHERE grupo = '$grupo'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //Si el semestre existe, obtener el nombre del semestre
        while($row = $result->fetch_assoc()) {
            $semestre = $row["semestre"];
            $semestre_id = $row["id"];
        }
    } else {
        //Si el semestre no existe, mostrar mensaje de error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    //Obtener plantel
    $sql = "SELECT * FROM plantel WHERE id = '$plantel'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //Si el plantel existe, obtener el nombre del plantel
        while($row = $result->fetch_assoc()) {
            $plantel = $row["nombre"];
        }
    } else {
        //Si el plantel no existe, mostrar mensaje de error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    //Obtener la materias del alumno y guardar las ID en un array
    $sql = "SELECT * FROM materia WHERE semestre = '$semestre_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //Si las materias existen, obtener el nombre de las materias y las id de maestro, guardarlo en un diccionario
        $dict = array();
        while($row = $result->fetch_assoc()) {
            $materia = $row["nombre"];
            $materia_id = $row["id"];
            $maestro = $row["maestro"];
            $clave = $row["clave"];
            $dict[$materia] = array(
                'maestro' => $maestro,
                'materia' => $materia,
                'id' => $materia_id,
                'clave' => $clave
            );
        }
    } else {
        //Si las materias no existen, mostrar mensaje de error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $materias_dict = array();
    //Hacer un for loop para cada materia
    foreach ($dict as $materia => $datos) {
        //Obtener la calificación del alumno en la materia
        $sql = "SELECT * FROM calificaciones WHERE materia = '$datos[id]' AND alumno = '$matricula'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            //Si la calificación existe, obtener la calificación y el status
            while($row = $result->fetch_assoc()) {
                $calificacion = $row["calificacion"];
                $status = $row["status"];
            }
        } else {
            //Si la calificación no existe, mostrar mensaje de error
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        //Obtener el nombre del maestro
        $sql = "SELECT * FROM maestro WHERE cuenta = '$datos[maestro]'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            //Si el maestro existe, obtener el nombre del maestro
            while($row = $result->fetch_assoc()) {
                $maestro = $row["nombre"];
                $apellidos_maestro = $row["apellidos"];
            }
        } else {
            //Si el maestro no existe, mostrar mensaje de error
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $materias_dict[$datos['materia']] = array(
            'maestro_nombre' => $maestro,
            'id' => $datos['id'],
            'maestro_apellidos' => $apellidos_maestro,
            'calificacion' => $calificacion,
            'materia' => $datos['materia'],
            'clave' => $datos['clave'],
            'status' => $status
        );
    }
    //cerrar la conexión
    $conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/Advanced-Pricing-Cards.css">
    <link rel="stylesheet" href="assets/css/Dark-Mode-Switch.css">
    <link rel="stylesheet" href="assets/css/Google-Style-Login-.css">
    <link rel="stylesheet" href="assets/css/Navbar-Right-Links-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/Toggle-Switch-toggle-switch.css">
    <link rel="stylesheet" href="assets/css/Toggle-Switch.css">
    <link rel="stylesheet" href="assets/css/Toggle-Switches.css">
</head>
<body>
    <!-- Incluir la navbar de php/navbar.php -->
    <?php include("php/navbar.php"); ?>

    <!-- Hero principal -->
    <section class="py-4 py-xl-5">
        <div class="container">
            <div class="border rounded border-0 d-flex flex-column justify-content-center align-items-center p-4 py-5" style="background: url(&quot;assets/img/Hero-Main.png&quot;) center / cover;">
                <div class="row">
                    <div class="col-md-10 col-xl-8 text-center d-flex d-sm-flex d-md-flex justify-content-center align-items-center mx-auto justify-content-md-start align-items-md-center justify-content-xl-center">
                        <div>
                            <h1 class="text-uppercase fw-bold mb-3" style="color: var(--bs-white);">PERFIL</h1>
                            <p class="mb-4" style="color: var(--bs-white);">INICIO &gt; PERFIL</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Sección de perfil -->
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div style="position: relative;">
                        <img class="img-fluid" src="assets/img/IMGUSER.png" style="display: block;">
                        <a class="btn btn-primary" role="button" style="position: absolute;padding: 10px 20px;margin-top: -45px;" href="#">Cargar imagen</a>
                    </div>
                    <div>
                        <h2><?php echo $apellidos . "* " . $nombre; ?></h2>
                        <h6>Matrícula: <?php echo $matricula; ?></h6>
                        <ul>
                            <li>Plantel: <?php echo $plantel; ?></li>
                            <li>Sexo: <?php echo $genero; ?></li>
                            <li>Carrera: <?php echo $carrera; ?></li>
                            <li>CURP: <?php echo $curp; ?></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-8">
                    <span class="font-monospace text-uppercase" style="font-weight: bold;font-size: 18px;">MÓDULOS DEL SEMESTRE <?php echo $semestre; ?></span>
                    <hr style="width: 10%;color: var(--bs-black);font-weight: bold;border-width: 1px;border-style: solid;">
                    <div>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <thead>
                                    <tr class="text-center">
                                        <th>Excelente:&nbsp;<img src="assets/img/Excelente.png"></th>
                                        <th>Regular:&nbsp;<img src="assets/img/Regular.png"></th>
                                        <th>Deficiente:&nbsp;<img src="assets/img/Deficiente.png"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <div class="table-responsive" style="background: var(--bs-border-color);height: 53px;overflow: hidden;text-align: center;">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th><i class="far fa-eye"></i><span>Recursos académicos</span></th>
                                        <th><i class="fas fa-clipboard-list"></i><span>Evaluación docente</span></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr class="fw-semibold" style="background: var(--bs-table-border-color);">
                                        <td>Status</td>
                                        <td>Clave/Módulo</td>
                                        <td>Docente</td>
                                        <td>Opciones</td>
                                    </tr>
                                    <!--Base
                                    <tr>
                                        <td>assets/img/{Status}.png</td>
                                        <td>({clave}) {Modulo}</td>
                                        <td>{NOMBRE}</td>
                                        <td>{EVALUACION DOCENTE}<br>{EVALUACION PARCIAL}</td>
                                    </tr>
                                    por cada materia mostrar eso
                                    -->
                                    <?php
                                    foreach ($materias_dict as $materia) {
                                        //imprimir cada el array $materia para probar
                                        //print_r($materia);
                                        echo "<tr>";
                                        echo "<td><img src='assets/img/" . $materia['status'] . ".png'></td>";
                                        echo "<td>(" . $materia['clave'] . ") " . $materia['materia'] . "</td>";
                                        echo "<td>" . $materia['maestro_apellidos'] . "* " . $materia['maestro_nombre'] . "</td>";
                                        //Conecatr BD
                                        include("php/config.php");
                                        $conn = new mysqli($host, $usename, $password, $database);
                                        if ($conn->connect_error) {
                                            die("Connection failed: " . $conn->connect_error);
                                        }
                                        //Checar si hay un examen activo de la materia y colocar un boton post que anexe ID de la materia, examen id y matricula
                                        $sql = "SELECT * FROM examenes WHERE `materia` = " . $materia['id'] . " AND `alumno` = " . $matricula . " AND `activo` = 1";
                                        $examen = $conn->query($sql) or die($conn->error);
                                        if ($examen = $examen->num_rows > 0) {
                                            $examen = $conn->query($sql)->fetch_assoc();
                                        }

                                        //Si hay un examen activo, agregar un boton de hacer examen
                                        if (isset($examen['id'])) {
                                            echo "<td><a class='btn btn-primary' role='button' href='cuestionario.php?id=" . $materia['id'] . "&examen=" . $examen['id'] . "&matricula=" . $matricula . "'>Hacer examen</a></td>";
                                        } else {
                                            echo "<td></td>";
                                        }
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div><a class="btn btn-success" role="button" style="margin-top: 5px;"><i class="icon ion-printer"></i><span class="fw-semibold">AVANCE INDIVIDUAL</span></a><a class="btn btn-danger text-uppercase" role="button" style="margin-top: 5px;"><i class="icon ion-printer"></i><span class="fw-semibold">Materias Reprobadas</span></a>
                </div>
            </div>
        </div>
    </section>
    <?php
     //cerrar conexion
        $conn->close();
    ?>
    <!-- Sección de footer -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/Advanced-Pricing-Cards-main.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/Dark-Mode-Switch-darkmode.js"></script>

</body>
</html>