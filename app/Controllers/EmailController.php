<?php
// app/Controllers/EmailController.php

class EmailController {

    private $from = "soportebolibox@gmail.com";

    private function enviarCorreo($para, $asunto, $mensajeHtml) {
        $cabeceras  = "MIME-Version: 1.0\r\n";
        $cabeceras .= "Content-type: text/html; charset=UTF-8\r\n";
        $cabeceras .= "From: Bolibox <" . $this->from . ">\r\n";
        $cabeceras .= "Reply-To: " . $this->from . "\r\n";
        $cabeceras .= "X-Mailer: PHP/" . phpversion();

        // Envolvemos el mensaje en una plantilla básica y limpia
        $plantilla = "
        <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
            <div style='max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 10px; border-top: 5px solid #FF8C00; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>
                <h2 style='color: #111827; text-align: center; margin-bottom: 20px;'>BOLI<span style='color: #FF8C00;'>BOX</span></h2>
                <div style='color: #444; font-size: 16px; line-height: 1.6;'>
                    $mensajeHtml
                </div>
                <div style='margin-top: 30px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 15px;'>
                    Este es un correo automático, por favor no respondas a este mensaje.
                </div>
            </div>
        </div>";

        return mail($para, $asunto, $plantilla, $cabeceras);
    }

    public function enviarActivacion($email, $nombre, $enlace) {
        $asunto = "Activa tu cuenta de Bolibox";
        $html = "
            <h3 style='color:#111827;'>¡Hola $nombre!</h3>
            <p>Gracias por registrarte en Bolibox. Para activar tu cuenta y empezar a gestionar tus importaciones, haz clic en el siguiente botón:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='$enlace' style='background-color: #FF8C00; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Activar mi Cuenta</a>
            </div>
            <p>Si el botón no funciona, copia y pega este enlace en tu navegador:<br><small>$enlace</small></p>
            <p>Si no fuiste tú quien creó esta cuenta, simplemente ignora este correo.</p>";
        
        $this->enviarCorreo($email, $asunto, $html);
    }

    public function enviarOTP($email, $otp) {
        $asunto = "Código de verificación de seguridad - Bolibox";
        $html = "
            <h3 style='color:#111827;'>Código de Acceso</h3>
            <p>Para iniciar sesión, ingresa el siguiente código de seguridad de 6 dígitos:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <span style='font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #FF8C00; background: #fffaf0; padding: 15px 30px; border-radius: 8px; border: 1px dashed #FF8C00;'>$otp</span>
            </div>
            <p><strong>Nota:</strong> Este código expirará en 10 minutos por razones de seguridad.</p>";

        $this->enviarCorreo($email, $asunto, $html);
    }

    public function enviarAlertaBloqueo($email) {
        $asunto = "⚠️ Alerta de Seguridad: Cuenta bloqueada - Bolibox";
        $html = "
            <h3 style='color:#dc3545;'>Alerta de Seguridad</h3>
            <p>Hemos detectado <strong>5 intentos de inicio de sesión fallidos</strong> en tu cuenta de Bolibox. Por tu protección, hemos bloqueado temporalmente el acceso por 15 minutos.</p>
            <p><strong>¿Fuiste tú?</strong><br>No te preocupes, espera 15 minutos e intenta de nuevo, o utiliza la opción '¿Olvidaste tu contraseña?'.</p>
            <p><strong>¿No fuiste tú?</strong><br>Alguien podría estar intentando acceder a tu cuenta. Te recomendamos cambiar tu contraseña una vez finalice el bloqueo.</p>";

        $this->enviarCorreo($email, $asunto, $html);
    }

    public function enviarRecuperacion($email, $enlace) {
        $asunto = "Restablecer Contraseña - Bolibox";
        $html = "
            <h3 style='color:#111827;'>Recuperación de Contraseña</h3>
            <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Haz clic en el siguiente botón para crear una nueva:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='$enlace' style='background-color: #111827; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Restablecer Contraseña</a>
            </div>
            <p>Si el botón no funciona, copia y pega este enlace en tu navegador:<br><small>$enlace</small></p>
            <p>Este enlace expirará en 1 hora. Si no solicitaste este cambio, puedes ignorar este mensaje.</p>";

        $this->enviarCorreo($email, $asunto, $html);
    }
}
?>