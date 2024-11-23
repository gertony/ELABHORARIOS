// Esperar a que el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const errorMessage = document.getElementById('error-message');

    // Función para manejar la validación en el cliente
    loginForm.addEventListener('submit', function(event) {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        // Verificar que los campos no estén vacíos
        if (username.trim() === '' || password.trim() === '') {
            event.preventDefault(); // Evitar que el formulario se envíe
            errorMessage.style.display = 'block'; // Mostrar mensaje de error
            errorMessage.textContent = 'Por favor, ingrese ambos campos.';
        } else {
            // Si todo está bien, ocultar el mensaje de error
            errorMessage.style.display = 'none';
        }
    });
});
