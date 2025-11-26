<?php
require_once("config/conexion.php");

class Migration extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "CREATE TABLE IF NOT EXISTS tm_documento_flujo (
            doc_flujo_id INT AUTO_INCREMENT PRIMARY KEY,
            tick_id INT NOT NULL,
            flujo_id INT,
            paso_id INT,
            usu_id INT,
            doc_nom VARCHAR(255) NOT NULL,
            fech_crea DATETIME DEFAULT CURRENT_TIMESTAMP,
            est INT DEFAULT 1
        );";

        try {
            $conectar->query($sql);
            echo "Table tm_documento_flujo created successfully.\n";
        } catch (Exception $e) {
            echo "Error creating table: " . $e->getMessage() . "\n";
        }
    }
}

$migration = new Migration();
$migration->run();
