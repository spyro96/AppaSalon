<?php
    namespace Controllers;

use Model\Citas;
use Model\CitaServicio;
use Model\Servicio;

    class APIController{

        public static function index(){

            $servicios = Servicio::all();

           echo json_encode($servicios);
        }

        public static function guardar(){
            
            //almacena la cita y devuelve el id
            $cita = new Citas($_POST);
            $resultado = $cita->guardar();

            $id = $resultado['id'];

            //almacena los servicios con el ID de la cita
            $idServicios = explode(",", $_POST['servicios']);

            foreach($idServicios as $idServicio){
                $args = [
                    'citaId' => $id,
                    'servicioId' => $idServicio
                ];
                $citaServicios = new CitaServicio($args);
                $citaServicios->guardar();
            }


            //retornamos una respuesta          
            echo json_encode ( ['resultado' => $resultado] );
        }

        public static function eliminar(){
          
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $id = $_POST['id'];
                $cita = Citas::find($id);
                $cita->eliminar();
                header('Location:'. $_SERVER['HTTP_REFERER']);
            }
        }
    }

?>