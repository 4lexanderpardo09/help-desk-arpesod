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

class Email extends PHPMailer{
    protected $gcorreo = 'jhonalexander2016.com@gmail.com';
    protected $gpass = 'duuu rrvx zlov qhtz ';

    public function ticket_abierto($ticket_id){
        $ticket = new Ticket();
        $documento = new Documento();
        $datos = $ticket->listar_ticket_x_id($ticket_id);
        
        foreach ($datos as $row) {
            $id = $row['tick_id'];
            $nombre = $row['usu_nom'];
            $apellido = $row['usu_ape'];
            $titulo = $row['tick_titulo'];
            $categoria = $row['cat_nom'];
            $correo = $row['usu_correo'];
            $descripcion = $row['tick_descrip'];
        }
        
        $datos2 = $documento->get_documento_x_ticket($ticket_id);
        $archivos = [];

        foreach ($datos2 as $row2) {
            $archivoRuta = dirname(__DIR__) . "/public/document/ticket/" . $ticket_id . "/" . $row2['doc_nom'];
            if (file_exists($archivoRuta)) {
                $archivos[] = $archivoRuta;
            }
        }


        //igual
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
        $this->addAddress($correo);
        $this->WordWrap = 50;
        $this->isHTML(true);
        $this->Subject = 'Ticket Abierto';
        $cuerpo = file_get_contents('../public/enviarticket.html');

        $cuerpo = str_replace('[Nombre del producto/servicio]', $titulo, $cuerpo);
        $cuerpo = str_replace('[Numero de ticket]', $id ,$cuerpo);
        $cuerpo = str_replace('[Proporcione una descripción detallada]', $descripcion,  $cuerpo);
        $cuerpo = str_replace('[Nombre de la categoria]', $categoria, $cuerpo);

        $nombresArchivos = '';

        foreach ($archivos as $archivo) {
        $nombreA = basename($archivo);
        $nombresArchivos .= $nombreA . "<br>";
        }

        $cuerpo = str_replace('[Adjunte capturas de pantalla o videoclips relevantes]', $nombresArchivos, $cuerpo);
        $cuerpo = str_replace('[Su nombre]', $nombre . ' ' . $apellido, $cuerpo);
        $cuerpo = str_replace('[Su correo]', $correo, $cuerpo);

        $this->Body = $cuerpo;

        foreach ($archivos as $archivo) {
            $this->addAttachment($archivo);
        }
    
        $this->send();
    } 

    public function ticket_asignado($ticket_id){

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($ticket_id);
        
        foreach ($datos as $row) {
            $id = $row['tick_id'];
            $nombre = $row['usu_nom'];
            $apellido = $row['usu_ape'];
            $titulo = $row['tick_titulo'];
            $categoria = $row['cat_nom'];
            $correo = $row['usu_correo'];
            $descripcion = $row['tick_descrip'];
            $prioridad = $row['pd_nom'];
        }

        $datos2 = $ticket->listar_ticket_x_id_x_usuaarioasignado($ticket_id);

        foreach ($datos2 as $row2) {
            $id_asignado = $row2['tick_id'];
            $nombre_asignado = $row2['usu_nom'];
            $apellido_asignado = $row2['usu_ape'];
            $correo_asignado = $row2['usu_correo'];
            $fecha_asig = $row2['fech_asig'];
        }

        $datos3 = $ticket->listar_ticket_x_id_x_quien_asigno($ticket_id);

        foreach ($datos3 as $row3) {
            $id_asignado = $row3['tick_id']; 
            $nombre_how_asignado = $row3['usu_nom'];
            $apellido_how_asignado = $row3['usu_ape'];
            $correo__how_asignado = $row3['usu_correo'];
        }


        //igual
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
        $this->addAddress($correo);
        $this->addAddress($correo_asignado);
        $this->WordWrap = 50;
        $this->isHTML(true);
        $this->Subject = 'Ticket Asignado';
        $cuerpo = file_get_contents('../public/asignarticket.html');

        $cuerpo = str_replace('[Nombre del cliente]', $nombre . ' ' . $apellido, $cuerpo);
        $cuerpo = str_replace('[Número de ticket]', $id ,$cuerpo);
        $cuerpo = str_replace('[Breve descripcion del problema]', $descripcion,  $cuerpo);

        $cuerpo = str_replace('[Nombre del agente]', $nombre_asignado . ' ' . $apellido_asignado, $cuerpo);
        $cuerpo = str_replace('[Fecha]', $fecha_asig ,$cuerpo);
        $cuerpo = str_replace('[Alta/Media/Baja]', $prioridad ,$cuerpo);


        $cuerpo = str_replace('[Su nombre]', $nombre_how_asignado . ' ' . $apellido_how_asignado, $cuerpo);
        $cuerpo = str_replace('[Su correo]', $correo__how_asignado , $cuerpo);

        $this->Body = $cuerpo;
    
        $this->send();

        
    }

    public function ticket_cerrado($ticket_id){

        $ticket = new Ticket();
        $datos = $ticket->listar_ticket_x_id($ticket_id);
        
        foreach ($datos as $row) {
            $id = $row['tick_id'];
            $nombre = $row['usu_nom'];
            $apellido = $row['usu_ape'];
            $titulo = $row['tick_titulo'];
            $categoria = $row['cat_nom'];
            $correo = $row['usu_correo'];
            $descripcion = $row['tick_descrip'];
        }

        $datos3 = $ticket->listar_ticket_x_id_x_usuaarioasignado($ticket_id);

        foreach ($datos3 as $row3) {
            $id = $row3['tick_id'];
            $nombre_asignado = $row3['usu_nom'];
            $apellido_asignado = $row3['usu_ape'];  
            $correo_asignado = $row3['correo'];
        }


        //igual
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
        $this->addAddress($correo);
        $this->WordWrap = 50;
        $this->isHTML(true);
        $this->Subject = 'Ticket Cerrado';
        $cuerpo = file_get_contents('../public/finalizacionticket.html');

        $cuerpo = str_replace('[Nombre del cliente]', $nombre . ' ' . $apellido, $cuerpo);
        $cuerpo = str_replace('[Número de ticket]', $id ,$cuerpo);
        $cuerpo = str_replace('[Breve descripción del problema]', $titulo, $cuerpo);

        $cuerpo = str_replace('[Su nombre]', $nombre_asignado.' '. $apellido_asignado, $cuerpo);

        $this->Body = $cuerpo;
    
        $this->send();
           

    }
}

?>