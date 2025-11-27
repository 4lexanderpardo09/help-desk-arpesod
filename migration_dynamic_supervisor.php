<?php
require_once("config/conexion.php");

class Migration extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Add campo_tipo column to tm_campo_plantilla
        $sql = "ALTER TABLE tm_campo_plantilla ADD COLUMN campo_tipo VARCHAR(50) DEFAULT 'text' AFTER campo_codigo";

        try {
            $conectar->query($sql);
            echo "Column 'campo_tipo' added to 'tm_campo_plantilla' successfully.\n";
        } catch (Exception $e) {
            echo "Error adding column: " . $e->getMessage() . "\n";
        }
    }
}

$migration = new Migration();
$migration->run();
