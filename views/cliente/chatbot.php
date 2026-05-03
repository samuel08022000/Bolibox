<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario'])) {
    header("Location: " . url('login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot - BOLIBOT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        
    /* Esto le dice al navegador que respete los espacios y saltos de línea */
    .chat-body .chat-message {
        white-space: pre-wrap; 
        word-wrap: break-word; /* Evita que el texto largo se salga de la burbuja */
    }
</style>
</head>
<body class="user-page chatbot-page user-dashboard">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="<?= url('cliente') ?>" class="logo">
                <i class="bi bi-box-seam"></i> BOLIBOX<span>.</span>
            </a>
            <div class="nav-links">
                <a class="nav-link" href="<?= url('nuestro-catalogo') ?>">Nuestro Catálogo</a>
                <a class="nav-link" href="<?= url('catalogos-asociados') ?>">Catálogos Asociados</a>
                <a class="nav-link" href="<?= url('pedidos') ?>">Mis Pedidos</a>
                <a class="nav-link" href="<?= url('chatbot') ?>">Bolibot</a>
            </div>
            
            <a href="<?= url('carrito') ?>" class="btn btn-outline-light rounded-pill px-3 me-2 ms-3 border-0">
                <i class="bi bi-cart3"></i> Mi Carrito
            </a>
            <a href="<?= url('/') ?>" class="btn-logout"><i class="bi bi-box-arrow-left"></i> Salir</a>
        </div>
    </nav>

    <div class="whatsapp-wrapper">
        <div class="whatsapp-tooltip">
            Empiece con su primer pedido aquí
        </div>
        <a href="https://wa.me/59178778387" target="_blank" class="whatsapp-float" title="Contactar con Empleado">
            <i class="bi bi-whatsapp"></i>
        </a>
    </div>

    <div class="container" style="margin-top: 40px;">
        
        <div class="section-header-user text-center">
            <p class="text-muted small m-0">Atención Automatizada</p>
            <h1 class="section-title-user mx-auto">Asistente Virtual Bolibot</h1>
            <p class="text-muted m-0 mx-auto" style="max-width: 800px;">Simula una conversación con nuestro bot para recibir cotizaciones rápidas o resolver dudas.</p>
        </div>

        <div class="d-flex justify-content-center mt-5">
            <div class="chat-container">
                <div class="chat-header">
                    <div class="logo-chat"><i class="bi bi-box-seam-fill ms-1"></i> BOLI<span>BOX</span></div>
                    <span class="small m-0 text-muted">Online - Asistente</span>
                </div>
                <div class="chat-body" id="chatBody">
                    <div class="chat-message received">¡Hola! ¿Cómo puedo ayudarte hoy con tu importación?</div>
                </div>
                <div class="chat-input-area">
                    <input type="text" id="userInput" placeholder="Escribe tu mensaje aquí...">
                    <button class="btn btn-naranja text-white fw-bold px-3" id="sendButton"><i class="bi bi-send-fill"></i></button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const chatBody = document.getElementById('chatBody');
        const userInput = document.getElementById('userInput');
        const sendButton = document.getElementById('sendButton');

        function appendMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${type}`;
            messageDiv.textContent = message;
            chatBody.appendChild(messageDiv);
            chatBody.scrollTop = chatBody.scrollHeight; 
        }

        sendButton.addEventListener('click', async () => {
    const message = userInput.value;
    if (message.trim() !== '') {
        appendMessage(message, 'sent');
        userInput.value = '';

        try {
            const response = await fetch('http://127.0.0.1:5000/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ "message": message }) // Enviamos el mensaje a Python
            });

            const data = await response.json();
            appendMessage(data.response, 'received'); // Mostramos la respuesta real de Python
        } catch (error) {
            appendMessage("❌ Error: No se pudo conectar con Bolibot.", 'received');
        }
    }
});
                
        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendButton.click();
            }
        });
    </script>
</body>
</html>