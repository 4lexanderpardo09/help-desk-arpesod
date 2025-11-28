<?php
require_once("config/conexion.php");

class Migration extends Conectar
{
    public function run()
    {
        $conexion = parent::conexion();

        $sql = "SHOW COLUMNS FROM tm_flujo_paso LIKE 'campo_id_referencia_jefe'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE tm_flujo_paso ADD COLUMN campo_id_referencia_jefe INT NULL AFTER est";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            echo "Columna campo_id_referencia_jefe agregada correctamente a tm_flujo_paso.\n";
        } else {
            echo "La columna campo_id_referencia_jefe ya existe en tm_flujo_paso.\n";
        }
    }
}

$migration = new Migration();
$migration->run();
