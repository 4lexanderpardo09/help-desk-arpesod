<?php
require_once('config/conexion.php');

class MigrationAddJefeColumn extends Conectar
{
    public function run()
    {
        $conectar = parent::getConexion();

        // Check if column exists
        $sql = "SHOW COLUMNS FROM tm_ticket LIKE 'usu_id_jefe_aprobador'";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            echo "Adding usu_id_jefe_aprobador column to tm_ticket...\n";
            $sql = "ALTER TABLE tm_ticket ADD COLUMN usu_id_jefe_aprobador INT NULL DEFAULT NULL AFTER est";
            $conectar->exec($sql);
            echo "Column added successfully.\n";
        } else {
            echo "Column usu_id_jefe_aprobador already exists.\n";
        }
    }
}

$migration = new MigrationAddJefeColumn();
$migration->run();
