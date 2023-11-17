<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{

    public static function login(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                //comprobar si el usuario existe
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario) {
                    //verificar su contraseña
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        //autenticar el usuario

                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redireccionamiento

                        if ($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }

    public static function olvide(Router $router)
    {
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email',$auth->email);

                if($usuario && $usuario->confirmado === "1"){
                    //Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //alerta de exito
                    Usuario::setAlerta('exito','Revisa tu correo');

                }else{
                    Usuario::setAlerta('error','El Usuario no existe o no esta confirmado');
                   
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide_password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router)
    {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        
        //buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error','Token no valido');
            $error = true;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //leer el nuevo password y guardarlo
            $password = new Usuario($_POST);

            $alertas = $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = null;

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();

                if($resultado){
                    echo "<script>alert('Su contraseña ha sido cambiada exitosamente!')</script>";
                    header('Location: /');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperarPassword',[
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router)
    {

        $usuario = new Usuario;

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //revisar que alertas estan vacio
            if (empty($alertas)) {
                //verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    //hashear el password
                    $usuario->hashPassword(); {

                        //generar un token unico
                        $usuario->crearToken();

                        //enviar el correo

                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                        $email->enviarConfirmacion();
                        //inserta los dstos a la base de datos
                        $resultado = $usuario->guardar();
                        //redirecciona a la pagina del mensaje
                        if ($resultado) {
                            header('Location: /mensaje');
                        }
                    }
                }
            }
        }

        $router->render('auth/crear_cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router)
    {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            //mostrar alertas de errores mensaje
           Usuario::setAlerta('error', 'Token no valido');
        }else{
            //modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = '';
            $usuario->guardar();
            Usuario::setAlerta('exito','cuenta comprobada Correctamente');
        }
            $alertas= Usuario::getAlertas();
        $router->render('auth/confirmarCuenta', [
            'alertas' => $alertas
        ]);
    }
}
?>