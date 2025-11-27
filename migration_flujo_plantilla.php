<?php
require_once("config/conexion.php");

class MigrationFlujoPlantilla extends Conectar
{
    public function up()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "CREATE TABLE IF NOT EXISTS tm_flujo_plantilla (
            flujo_plantilla_id INT AUTO_INCREMENT PRIMARY KEY,
            flujo_id INT NOT NULL,
            emp_id INT NOT NULL,
            plantilla_nom VARCHAR(255) NOT NULL,
            est INT DEFAULT 1,
            FOREIGN KEY (flujo_id) REFERENCES tm_flujo(flujo_id),
            FOREIGN KEY (emp_id) REFERENCES td_empresa(emp_id)
        );";

        try {
            $conectar->query($sql);
            echo "Table tm_flujo_plantilla created successfully.<br>";
        } catch (Exception $e) {
            echo "Error creating table: " . $e->getMessage() . "<br>";
        }
    }
}

$migration = new MigrationFlujoPlantilla();
$migration->up();
