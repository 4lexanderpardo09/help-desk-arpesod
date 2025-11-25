<?php
require_once("config/conexion.php");

class MigrationUsuAsig extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        try {
            // 1. Change usu_asig column to VARCHAR(255) to support multiple IDs
            // Note: We might need to drop foreign key constraint first if it exists.
            // Assuming 'usu_asig' might have a FK, let's try to drop it safely or just modify.
            // In MySQL, modifying the column usually works if data is compatible (INT -> VARCHAR is compatible).

            $sql = "ALTER TABLE tm_ticket MODIFY COLUMN usu_asig VARCHAR(255) NULL";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();

            echo "Columna usu_asig modificada a VARCHAR(255) exitosamente.<br>";
        } catch (Exception $e) {
            echo "Error en la migración: " . $e->getMessage();
        }
    }
}

$migration = new MigrationUsuAsig();
$migration->run();
