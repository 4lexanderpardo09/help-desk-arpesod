<?php

session_start();

class Conectar{
    protected $dbh;

    protected function Conexion(){
        try{
           // $conectar = $this->dbh = new PDO("mysql:host=localhost;dbname=helpdeskdb", "root", "@Ap200905");
            $conectar = $this->dbh = new PDO("mysql:host=mesadeayuda.electrocreditosdelcauca.com;dbname=electroc_mesadeayuda", "electroc_webmast ", "4Rxf1vNLYW_w");

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