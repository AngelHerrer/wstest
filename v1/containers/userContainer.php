<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class userContainer {

    public $idUser = 0;
    public $nameUser = "";
    public $firstNameUser = "";
    public $secondNameUser = "";
    public $passwordUser = "";
    public $emailUser = "";
    
    function getIdUser() {
        return $this->idUser;
    }

    function getNameUser() {
        return $this->nameUser;
    }

    function getFirstNameUser() {
        return $this->firstNameUser;
    }

    function getSecondNameUser() {
        return $this->secondNameUser;
    }

    function getPasswordUser() {
        return $this->passwordUser;
    }

    function getEmailUser() {
        return $this->emailUser;
    }

    function setIdUser($idUser) {
        $this->idUser = $idUser;
    }

    function setNameUser($nameUser) {
        $this->nameUser = $nameUser;
    }

    function setFirstNameUser($firstNameUser) {
        $this->firstNameUser = $firstNameUser;
    }

    function setSecondNameUser($secondNameUser) {
        $this->secondNameUser = $secondNameUser;
    }

    function setPasswordUser($passwordUser) {
        $this->passwordUser = $passwordUser;
    }

    function setEmailUser($emailUser) {
        $this->emailUser = $emailUser;
    }
}
