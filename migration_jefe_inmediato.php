<?php
require_once("config/conexion.php");

class MigrationJefe extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "ALTER TABLE tm_flujo_paso ADD COLUMN necesita_aprobacion_jefe TINYINT(1) DEFAULT 0 AFTER permite_cerrar";

        try {
            $conectar->query($sql);
            echo "Columna 'necesita_aprobacion_jefe' agregada correctamente.\n";
        } catch (PDOException $e) {
            echo "Error o la columna ya existe: " . $e->getMessage() . "\n";
        }
    }
}

$migration = new MigrationJefe();
$migration->run();
