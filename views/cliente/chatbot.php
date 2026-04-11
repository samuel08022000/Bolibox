<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot - BOLIBOT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="user-page chatbot-page user-dashboard">

    <nav class="top-navbar">
        <div class="nav-inner">
            <a href="<?= url('cliente') ?>" class="logo">
                <i class="bi bi-box-seam"></i> BOLIBOX<span>.</span>
            </a>
           <div class="nav-links">
    <a class="nav-link" href="<?= url('cliente') ?>">Dashboard</a>
    <a class="nav-link" href="<?= url('nuestro-catalogo') ?>">Nuestro Catálogo</a>
    <a class="nav-link" href="<?= url('catalogos-asociados') ?>">Catálogos Asociados</a>
    <a class="nav-link" href="<?= url('pedidos') ?>">Mis Pedidos</a>
    <a class="nav-link" href="<?= url('chatbot') ?>">Bolibot</a>
</div>
            <a href="<?= url('/') ?>" class="btn-logout">
                <i class="bi bi-box-arrow-left"></i> Salir
            </a>
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

        sendButton.addEventListener('click', () => {
            const message = userInput.value;
            if (message.trim() !== '') {
                appendMessage(message, 'sent');
                userInput.value = '';

                setTimeout(() => {
                    const botResponse = `¡Recibido! Estamos procesando tu mensaje: "${message}"... pronto un empleado te contactará por WhatsApp para darte detalles.`;
                    appendMessage(botResponse, 'received');
                }, 1000);
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