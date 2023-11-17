<?php
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{

    public $email;
    public $nombre; 
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion(){
        //crear el objeto de email

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        //Set HTML

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong> ".$this->nombre. "</strong> Has creado tu cuenta en App Salon, solo debes confirmala presionando el siguiente enlace </p>";
        $contenido .= "<p>Presiona aqui: <a href='". $_ENV['APP_URL'] ."/confirmarCuenta?token=".$this->token."'>Confirmar tu cuenta</a></p>";
        $contenido .= "<p>Si tu no has solicitado esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //enviar el mail
        $send = $mail->send();
    }

    public function enviarInstrucciones(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Restablecer Contraseña';

        //Set HTML

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong> Hola".$this->nombre. "</strong> Has solicitado Restablecer tu Contraseña, Sigue el siguiente enlace para hacerlo </p>";
        $contenido .= "<p>Presiona aqui: <a href='". $_ENV['APP_URL'] ."/recuperar?token=".$this->token."'>Restablecer Contraseña</a></p>";
        $contenido .= "<p>Si tu no has solicitado esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //enviar el mail
        $send = $mail->send();
    }
}
?>