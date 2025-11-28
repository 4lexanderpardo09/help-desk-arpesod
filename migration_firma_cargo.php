<?php
require_once("config/conexion.php");

class Migration extends Conectar
{
    public function run()
    {
        $conexion = parent::conexion();

        $sql = "SHOW COLUMNS FROM tm_flujo_paso_firma LIKE 'car_id'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE tm_flujo_paso_firma ADD COLUMN car_id INT NULL AFTER usu_id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            echo "Columna car_id agregada correctamente a tm_flujo_paso_firma.\n";
        } else {
            echo "La columna car_id ya existe en tm_flujo_paso_firma.\n";
        }
    }
}

$migration = new Migration();
$migration->run();
