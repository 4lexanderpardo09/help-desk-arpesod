<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


require_once('../config/conexion.php');
require_once('../models/Ticket.php');
require_once('../models/Documento.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Email extends PHPMailer
{
    protected $gcorreo = 'mesadeayudaelectroc@gmail.com';
    protected $gpass = 'xcmo yrcr ekss gyuy';

    public function recuperar_contrasena($usu_correo, $link)
    {
        $this->isSMTP();
        $this->Host = 'smtp.gmail.com';
        $this->Port = 587;
        $this->SMTPAuth = true;
        $this->Username = $this->gcorreo;
        $this->Password = $this->gpass;
        $this->From = $this->gcorreo;
        $this->SMTPSecure = 'tls';
        $this->FromName = 'Sistema de Tickets';
        $this->CharSet = 'UTF-8';
        $this->addAddress($usu_correo);
        $this->WordWrap = 50;
        $this->isHTML(true);
        $this->Subject = 'Recuperar Contraseña';
        $cuerpo = file_get_contents('../public/recuperarcontrasena.html');

        $cuerpo = str_replace('[Link de recuperacion]', $link, $cuerpo);

        $this->Body = $cuerpo;

        $this->send();
    }
}
