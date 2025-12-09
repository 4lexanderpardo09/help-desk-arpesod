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
        $this->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        ); // A veces necesario para certificados autofirmados o errores SSL
        $this->Host = gethostbyname('smtp.gmail.com'); // Forzar resolucion a IP para evitar problemas de DNS/IPv6
        $this->SMTPDebug = 2; // Descomentar para ver errores en log si es necesario
        $this->Port = 465; // Probar con 465 y ssl, a veces 587 se bloquea
        $this->SMTPAuth = true;
        $this->Username = $this->gcorreo;
        $this->Password = str_replace(' ', '', $this->gpass); // Eliminar espacios si los hay
        $this->From = $this->gcorreo;
        $this->SMTPSecure = 'ssl'; // Cambiar a ssl para puerto 465
        $this->FromName = 'Sistema de Tickets';
        $this->CharSet = 'UTF-8';
        $this->addAddress($usu_correo);
        $this->WordWrap = 50;
        $this->isHTML(true);
        $this->Subject = 'Recuperar Contraseña';
        $cuerpo = file_get_contents('../public/recuperarcontrasena.html');

        $cuerpo = str_replace('[Link de recuperacion]', $link, $cuerpo);

        $this->Body = $cuerpo;

        return $this->send();
    }
}
