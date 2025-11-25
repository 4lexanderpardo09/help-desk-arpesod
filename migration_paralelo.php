<?php
require_once("config/conexion.php");

class MigrationParalelo extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // 1. Crear tabla tm_ticket_paralelo
        $sql_table = "CREATE TABLE IF NOT EXISTS tm_ticket_paralelo (
            paralelo_id INT AUTO_INCREMENT PRIMARY KEY,
            tick_id INT NOT NULL,
            paso_id INT NOT NULL,
            usu_id INT NOT NULL,
            estado VARCHAR(20) DEFAULT 'Pendiente', -- 'Pendiente', 'Aprobado', 'Rechazado'
            fech_crea DATETIME DEFAULT CURRENT_TIMESTAMP,
            fech_cierre DATETIME NULL,
            comentario TEXT NULL,
            est INT DEFAULT 1
        );";

        try {
            $conectar->query($sql_table);
            echo "Tabla 'tm_ticket_paralelo' creada o ya existe.\n";
        } catch (PDOException $e) {
            echo "Error creando tabla: " . $e->getMessage() . "\n";
        }

        // 2. Agregar columna es_paralelo a tm_flujo_paso
        $sql_col = "ALTER TABLE tm_flujo_paso ADD COLUMN es_paralelo TINYINT(1) DEFAULT 0 AFTER necesita_aprobacion_jefe";

        try {
            $conectar->query($sql_col);
            echo "Columna 'es_paralelo' agregada a 'tm_flujo_paso'.\n";
        } catch (PDOException $e) {
            echo "Error o la columna 'es_paralelo' ya existe: " . $e->getMessage() . "\n";
        }
    }
}

$migration = new MigrationParalelo();
$migration->run();
