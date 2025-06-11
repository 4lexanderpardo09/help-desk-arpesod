<?php

session_start();

class Conectar{
    protected $dbh;

    protected function Conexion(){
        try{
           // $conectar = $this->dbh = new PDO("mysql:host=localhost;dbname=helpdeskdb", "root", "@Ap200905");
            $conectar = $this->dbh = new PDO("mysql:host=mesadeayuda.electrocreditosdelcauca.com;dbname=helpdeskdb", "root", "");

            return $conectar;
        }catch(Exception $e){
            print "ERROR DB" . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function set_names(){
        return $this->dbh->query("SET NAMES 'utf8'");
    }

    public function ruta(){
       // return "http://localhost:8000/";
       return "https://mesadeayuda.electrocreditosdelcauca.com/";

       
    }
}

?>