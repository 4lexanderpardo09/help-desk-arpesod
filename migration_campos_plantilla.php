<?php
require_once("config/conexion.php");

class Migration extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Table for configuring fields in a step
        $sql1 = "CREATE TABLE IF NOT EXISTS tm_campo_plantilla (
            campo_id INT AUTO_INCREMENT PRIMARY KEY,
            paso_id INT NOT NULL,
            campo_nombre VARCHAR(255) NOT NULL,
            campo_codigo VARCHAR(100) NOT NULL,
            coord_x DECIMAL(10,2) NOT NULL,
            coord_y DECIMAL(10,2) NOT NULL,
            pagina INT DEFAULT 1,
            est INT DEFAULT 1,
            fech_crea DATETIME DEFAULT CURRENT_TIMESTAMP
        );";

        // Table for storing values for a specific ticket
        $sql2 = "CREATE TABLE IF NOT EXISTS td_ticket_campo_valor (
            tick_campo_id INT AUTO_INCREMENT PRIMARY KEY,
            tick_id INT NOT NULL,
            campo_id INT NOT NULL,
            valor TEXT,
            fech_crea DATETIME DEFAULT CURRENT_TIMESTAMP,
            est INT DEFAULT 1
        );";

        try {
            $conectar->query($sql1);
            echo "Table tm_campo_plantilla created successfully.\n";
            $conectar->query($sql2);
            echo "Table td_ticket_campo_valor created successfully.\n";
        } catch (Exception $e) {
            echo "Error creating tables: " . $e->getMessage() . "\n";
        }
    }
}

$migration = new Migration();
$migration->run();
