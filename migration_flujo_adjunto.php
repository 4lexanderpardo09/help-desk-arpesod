<?php
require_once("config/conexion.php");

class MigrationFlujoAdjunto extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "SHOW COLUMNS FROM tm_flujo LIKE 'flujo_nom_adjunto'";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE tm_flujo ADD COLUMN flujo_nom_adjunto VARCHAR(255) NULL";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            echo "Columna 'flujo_nom_adjunto' agregada correctamente a 'tm_flujo'.\n";
        } else {
            echo "La columna 'flujo_nom_adjunto' ya existe en 'tm_flujo'.\n";
        }
    }
}

$migration = new MigrationFlujoAdjunto();
$migration->run();
