<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '/../datos/ConexionBD.php';
require '/../containers/userContainer.php';

class FunctionManager {

    const NOMBRE_TABLA = "user";
    const ID_USUARIO = "idUser";
    const NOMBRE = "name";
    const APELLIDOMATERNO = "firstName";
    const APELLIDOPATERNO = "secondName";
    const CONTRASENA = "password";
    const CORREO = "email";

    public static function validarContrasena($contrasenaPlana, $contrasenaHash) {
        return password_verify($contrasenaPlana, $contrasenaHash);
    }

    public static function encriptarContrasena($contrasenaPlana) {
        if ($contrasenaPlana)
            return password_hash($contrasenaPlana, PASSWORD_DEFAULT);
        else
            return null;
    }

    public static function autenticar($correo, $contrasena) {
        $comando = "SELECT password FROM " . self::NOMBRE_TABLA .
                " WHERE " . self::CORREO . "=?";

        try {

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $correo);
            $sentencia->execute();

            if ($sentencia) {
                $resultado = $sentencia->fetch();
                if (self::validarContrasena($contrasena, $resultado['password'])) {
                    return true;
                } else
                    return false;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    public static function getAllUsersDB() {
        $comando = "SELECT * FROM user";
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $ret = [];
        if ($sentencia->execute()) {    
            $rows = $sentencia->fetchAll();
            foreach ($rows as $row) {
                $uc = new userContainer();
                $uc->idUser = $row['idUser'];
                $uc->nameUser = $row['name'];
                $uc->firstNameUser = $row['firstName'];
                $uc->secondNameUser = $row['secondName'];
                $uc->passwordUser = $row['password'];
                $uc->emailUser = $row['email'];
                $ret [] = $uc;
            }
            return $ret;
        } 
        return null;
    }

    public static function obtenerUsuarioPorCorreo($correo) {
        $comando = "SELECT name, firstName, secondName, password, email FROM user WHERE email = ?";
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $correo);

        if ($sentencia->execute())
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        else
            return null;
    }

    public static function crear($datosUsuario) {
        $nombre = $datosUsuario->nombre;
        $apellidoMaterno = $datosUsuario->apellidoMaterno;
        $apellidoPaterno = $datosUsuario->apellidoPaterno;
        $contrasena = $datosUsuario->contrasena;
        $contrasenaEncriptada = self::encriptarContrasena($contrasena);
        $correo = $datosUsuario->correo;

        try {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::NOMBRE . "," .
                    self::APELLIDOMATERNO . "," .
                    self::APELLIDOPATERNO . "," .
                    self::CONTRASENA . "," .
                    self::CORREO . ")" .
                    " VALUES(?,?,?,?,?)";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $apellidoMaterno);
            $sentencia->bindParam(3, $apellidoPaterno);
            $sentencia->bindParam(4, $contrasenaEncriptada);
            $sentencia->bindParam(5, $correo);

            $resultado = $sentencia->execute();

            if ($resultado) {
                return self::ESTADO_CREACION_EXITOSA;
            } else {
                return self::ESTADO_CREACION_FALLIDA;
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }
}
