<?php
require_once("config/conexion.php");

class Migration extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "ALTER TABLE tm_flujo_paso ADD COLUMN requiere_campos_plantilla INT DEFAULT 0";

        try {
            $conectar->query($sql);
            echo "Column requiere_campos_plantilla added successfully.\n";
        } catch (Exception $e) {
            echo "Error adding column: " . $e->getMessage() . "\n";
        }
    }
}

$migration = new Migration();
$migration->run();
