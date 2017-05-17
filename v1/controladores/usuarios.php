<?php

require '/../utilidades/FunctionManager.php';

class usuarios {

    const ESTADO_CREACION_EXITOSA = 1;
    const ESTADO_CREACION_FALLIDA = 2;
    const ESTADO_ERROR_BD = 3;
    const ESTADO_AUSENCIA_CLAVE_API = 4;
    const ESTADO_CLAVE_NO_AUTORIZADA = 5;
    const ESTADO_URL_INCORRECTA = 6;
    const ESTADO_FALLA_DESCONOCIDA = 7;
    const ESTADO_PARAMETROS_INCORRECTOS = 8;

    public static function post($peticion) {
        if ($peticion[0] == 'register') {
            /* {
              "nombre":"Alex Herrera",
              "apellidoMaterno":"Herrera",
              "apellidoPaterno":"Dominguez",
              "contrasena":"123456",
              "correo":"alepz.herrera@hotmail.com"
              } */
            return self::registrar();
        } else if ($peticion[0] == 'login') {
            /* {
              "contrasena":"123456",
              "correo":"alepz.herrera@hotmail.com"
              } */
            return self::loguear();
        } else if ($peticion[0] == 'getAllUsers') {
            /* {
              "contrasena":"123456",
              "correo":"alepz.herrera@hotmail.com"
              } */
            return self::getAllUsers();
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }

    /**
     * Crea un nuevo usuario en la base de datos
     */
    private function registrar() {
        $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);
        $resultado = FunctionManager::crear($usuario);

        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:
                http_response_code(200);
                return
                        [
                            "estado" => self::ESTADO_CREACION_EXITOSA,
                            "mensaje" => utf8_encode("Registro con Exito!")
                ];
                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
                break;
            default:
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Falla desconocida", 400);
        }
    }

    private function getAllUsers() {
        $respuesta = array();
        $body = file_get_contents('php://input');
        $usuario = json_decode($body);
        $correo = $usuario->correo;
        $contrasena = $usuario->contrasena;

        if (FunctionManager::autenticar($correo, $contrasena)) {
            $usuariosDB = FunctionManager::getAllUsersDB();
            if ($usuariosDB != NULL) {
                http_response_code(200);
                return ["estado" => 1, "usuario" => $usuariosDB];
            } else {
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Ha ocurrido un error");
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_PARAMETROS_INCORRECTOS, utf8_encode("Correo o contraseña invalidos"));
        }
    }

    private function loguear() {
        $respuesta = array();

        $body = file_get_contents('php://input');
        $usuario = json_decode($body);
        $correo = $usuario->correo;
        $contrasena = $usuario->contrasena;

        if (FunctionManager::autenticar($correo, $contrasena)) {
            $usuarioBD = FunctionManager::obtenerUsuarioPorCorreo($correo);

            if ($usuarioBD != NULL) {
                http_response_code(200);
                $respuesta["nombre"] = $usuarioBD["name"];
                $respuesta["correo"] = $usuarioBD["email"];
                return ["estado" => 1, "usuario" => $respuesta];
            } else {
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Ha ocurrido un error");
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_PARAMETROS_INCORRECTOS, utf8_encode("Correo o contraseña invalidos"));
        }
    }
}
