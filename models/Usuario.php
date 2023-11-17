<?php
    namespace Model;

use Model\ActiveRecord;

    class Usuario extends ActiveRecord{
        
        protected static $tabla  = 'usuarios';
        protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password','telefono', 'admin', 'confirmado', 'token'];

        public $id;
        public $nombre;
        public $apellido;
        public $email;
        public $password;
        public $telefono;
        public $admin;
        public $confirmado;
        public $token;

        public function __construct($args = [])
        {
            $this->id = $args['id'] ?? null;
            $this->nombre = $args['nombre'] ?? '';
            $this->apellido = $args['apellido'] ?? '';
            $this->email = $args['email'] ?? '';
            $this->password = $args['password'] ?? '';
            $this->telefono = $args['telefono'] ?? '';
            $this->admin = $args['admin'] ?? '0';
            $this->confirmado = $args['confirmado'] ?? '0';
            $this->token = $args['token'] ?? '';
        }

        //mensaje de validacion para la creacion de una cuenta

        public function validarNuevaCuenta (){
            if(!$this->nombre){
                self::$alertas['error'][] = 'El nombre es obligatorio';
            }

            if(!$this->apellido){
                self::$alertas['error'][] = 'El Apellido es obligatorio';
            }

            if(!$this->telefono){
                self::$alertas['error'][] = 'El Telefono es obligatorio';
            }

            if(!$this->email){
                self::$alertas['error'][] = 'El correo es obligatorio';
            }

            if(!$this->password){
                self::$alertas['error'][] = 'La contraseña es obligatorio';
            }

            if(strlen($this->password) < 6){
                self::$alertas['error'][] = "La contraseña debe tener una logitud superior a 6 digitos";
            }

            return self::$alertas;
        }

        public function validarLogin(){
            if(!$this->email){
                self::$alertas['error'][] = "El email es obligatorio";
            }

            if(!$this->password){
                self::$alertas['error'][] = "la contraseña es obligatoria es obligatorio";
            }

            return self::$alertas;
        }

        public function validarPassword(){
            if(!$this->password){
                self::$alertas['error'][] = "Es obligatorio llenar el campo";
            }
            if(strlen($this->password) < 6){
                self::$alertas['error'][] = "La contraseña debe tener al menos 6 caracteres";
            }

            return self::$alertas;
        }

        public function validarEmail(){
            if(!$this->email){
                self::$alertas['error'][] = "El email es obligatorio";
            }
            return self::$alertas;
        }
        //revisar si el usuario ya existe
        public function existeUsuario(){
            $query = "SELECT * FROM ". self::$tabla . " WHERE email = '" .$this->email . "' LIMIT 1";

            $resultado = self::$db->query($query);

            if($resultado->num_rows){
                self::$alertas['error'][] = "El correo ya se encuentra registrado";
            }

            return $resultado;
        }

        public function hashPassword(){
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        }

        public function crearToken(){
            $this->token = uniqid();
        }

        public function comprobarPasswordAndVerificado($password){
            $resultado = password_verify($password,  $this->password);
            
            if(!$resultado || !$this->confirmado){
                self::$alertas['error'][] = "La contraseña es incorrecta o tu cuenta no ha sido confirmado";
            }else{
                return true;
            }
        }
    }
?>