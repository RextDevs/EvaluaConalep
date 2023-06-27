<?php
    session_start();
    //Compobrar si ya se ha iniciado sesión
    if(isset($_SESSION['session'])){
        if($_SESSION['expire'] >= time()){
            session_unset();
            session_destroy();
            
        } else {
            //Redirigir a la página principal
            header("Location: /index.php");
        }
    }
    //Conexion con base de datos
    include("php/config.php");
    $conn = new mysqli($host, $usename, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    //Obtener los datos del formulario si se ha enviado
    if(isset($_POST['submit-s'])){
        $matricula = $_POST['matricula'];
        $curp = $_POST['curp'];
        //Obtener el id del estudiante
        $sql = "SELECT * FROM estudiantes WHERE matricula = '$matricula' AND curp = '$curp'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $nombre = $row["nombre"];
            }
            $_SESSION['nombre'] = $nombre;
            $_SESSION['matricula'] = $matricula;
            $_SESSION['tipo'] = "estudiante";
            $_SESSION['session'] = true;
            $_SESSION['expire'] = time() + 3600;
            //Redirigir a la página principal sin header
            echo "<script>window.location.href = '/index.php';</script>";
        } else {
            //Si el estudiante no existe, mostrar mensaje de error
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    if(isset($_POST['submit-t'])){
        print_r($_POST);
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];
        //Obtener el id del maestro
        $sql = "SELECT id FROM maestros WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            //Si el maestro existe, obtener el id del maestro
            while($row = $result->fetch_assoc()) {
                $id = $row["id"];
            }
            //Obtener el nombre del maestro
            $sql = "SELECT nombre FROM maestros WHERE id = '$id'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                //Si el maestro existe, obtener el nombre del maestro
                while($row = $result->fetch_assoc()) {
                    $nombre = $row["nombre"];
                }
                $_SESSION['id'] = $id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['tipo'] = "maestro";
                $_SESSION['session'] = true;
                $_SESSION['expire'] = time() + 3600;
                //Redirigir a la página principal
                header("Location: /index.php");
            } else {
                //Si el maestro no existe, mostrar mensaje de error
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            //Si el maestro no existe, mostrar mensaje de error
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>SAAC</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
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
    <div class="login-card"><img class="profile-img-card" src="assets/img/avatar_2x.png">
        <p class="profile-name-card"> </p>
        <div class="text-center">
            <div class="btn-group" role="group"><button class="btn btn-success" id="btn-estudiante" type="button">ESTUDIANTE</button><button class="btn btn-secondary" id="btn-maestros" type="button">MAESTRO</button></div>
        </div>
        <div id="login-form-student">
            <form class="form-signin" method="post" action="/login.php">
                <span class="reauth-email"> </span>
                <input class="form-control" type="text" id="inputEmail" required="" placeholder="MATRICULA" autofocus="" name="matricula">
                <input class="form-control" type="password" id="inputPassword" required="" placeholder="CURP" name="curp">
                <div class="checkbox">
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="formCheck-1" name="check"><label class="form-check-label" for="formCheck-1">Mantenerme conectado</label></div>
                </div><button class="btn btn-primary btn-lg d-block btn-signin w-100" type="submit" name="submit-s">Iniciar sesión&nbsp;</button>
            </form><a class="forgot-password" href="#">¿Olvidaste tu Matricula o CURP?</a>
        </div>
        <div id="login-form-teacher" class="hidden">
            <form class="form-signin" method="post" action="/login.php" target="_self">
                <span class="reauth-email"> </span>
                <input class="form-control" type="text" id="user_teacher" required="" placeholder="Cuenta de usuario" autofocus="" name="usuario">
                <input class="form-control" type="password" id="inputPassword-1" required="" placeholder="CONTRASEÑA" name="contrasena">
                <div class="checkbox">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="formCheck-2" name="check">
                        <label class="form-check-label" for="formCheck-2">Remember me</label></div>
                </div><button class="btn btn-primary btn-lg d-block btn-signin w-100" type="submit" name="submit-t">Iniciar sesión&nbsp;</button>
            </form><a class="forgot-password" href="#">¿Olvidaste tu Contraseña?</a>
        </div>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/Advanced-Pricing-Cards-main.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/Dark-Mode-Switch-darkmode.js"></script>
</body>

</html>