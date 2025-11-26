<?php
require_once("config/conexion.php");

class MigrationFirmaPdf extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // 1. Agregar columna requiere_firma a tm_flujo_paso
        $sql_col = "ALTER TABLE tm_flujo_paso ADD COLUMN requiere_firma TINYINT(1) DEFAULT 0 AFTER es_paralelo";

        try {
            $conectar->query($sql_col);
            echo "Columna 'requiere_firma' agregada a 'tm_flujo_paso'.\n";
        } catch (PDOException $e) {
            echo "Error o la columna 'requiere_firma' ya existe: " . $e->getMessage() . "\n";
        }

        // 2. Crear tabla tm_flujo_paso_firma
        $sql_table = "CREATE TABLE IF NOT EXISTS tm_flujo_paso_firma (
            firma_id INT AUTO_INCREMENT PRIMARY KEY,
            paso_id INT NOT NULL,
            usu_id INT NULL, -- NULL significa que aplica para todos (o default), si tiene valor es específico
            coord_x FLOAT NOT NULL,
            coord_y FLOAT NOT NULL,
            pagina INT DEFAULT 1,
            est INT DEFAULT 1,
            FOREIGN KEY (paso_id) REFERENCES tm_flujo_paso(paso_id)
        );";

        try {
            $conectar->query($sql_table);
            echo "Tabla 'tm_flujo_paso_firma' creada o ya existe.\n";
        } catch (PDOException $e) {
            echo "Error creando tabla: " . $e->getMessage() . "\n";
        }
    }
}

$migration = new MigrationFirmaPdf();
$migration->run();
