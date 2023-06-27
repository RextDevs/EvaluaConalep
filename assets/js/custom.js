const btnEstudiante = document.getElementById("btn-estudiante");
const btnMaestros = document.getElementById("btn-maestros");
const loginFormStudent = document.getElementById("login-form-student");
const loginFormTeacher = document.getElementById("login-form-teacher");

let selectedUserType;

btnEstudiante.addEventListener("click", () => {
    selectedUserType = "estudiante";
});

btnMaestros.addEventListener("click", () => {
    selectedUserType = "maestro";
});

function showLoginForm() {
    if (selectedUserType === "estudiante") {
        // Mostrar formulario de inicio de sesión para estudiantes
        loginFormStudent.classList.remove("hidden");
        loginFormTeacher.classList.add("hidden");
        // Agregar clase btn-success al botón ESTUDIANTE y eliminar clase btn-secondary
        btnEstudiante.classList.add("btn-success");
        btnEstudiante.classList.remove("btn-secondary");
        // Remover clase btn-secondary del botón MAESTRO y agregar clase btn-success
        btnMaestros.classList.remove("btn-success");
        btnMaestros.classList.add("btn-secondary");
    } else if (selectedUserType === "maestro") {
        // Mostrar formulario de inicio de sesión para maestros
        loginFormTeacher.classList.remove("hidden");
        loginFormStudent.classList.add("hidden");
        // Agregar clase btn-success al botón MAESTRO y eliminar clase btn-secondary
        btnMaestros.classList.add("btn-success");
        btnMaestros.classList.remove("btn-secondary");
        // Remover clase btn-secondary del botón ESTUDIANTE y agregar clase btn-success
        btnEstudiante.classList.remove("btn-success");
        btnEstudiante.classList.add("btn-secondary");
    }
}

// Llamar a la función de inicio de sesión cuando se hace clic en un botón
btnEstudiante.addEventListener("click", showLoginForm);
btnMaestros.addEventListener("click", showLoginForm);
