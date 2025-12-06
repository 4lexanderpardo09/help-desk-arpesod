<?php
require_once("config/conexion.php");

class Migration extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // 1. Create tm_zona table
        $sql = "CREATE TABLE IF NOT EXISTS tm_zona (
            zona_id INT AUTO_INCREMENT PRIMARY KEY,
            zona_nom VARCHAR(150) NOT NULL,
            est INT DEFAULT 1
        )";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        echo "Tabla 'tm_zona' creada o verificada.\n";

        // 2. Insert default zones
        $sql = "INSERT INTO tm_zona (zona_nom, est) SELECT 'Norte', 1 WHERE NOT EXISTS (SELECT * FROM tm_zona WHERE zona_nom = 'Norte')";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        $sql = "INSERT INTO tm_zona (zona_nom, est) SELECT 'Sur', 1 WHERE NOT EXISTS (SELECT * FROM tm_zona WHERE zona_nom = 'Sur')";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        echo "Zonas 'Norte' y 'Sur' insertadas/verificadas.\n";

        // 3. Modify tm_regional
        // Check if zona_id exists
        $sql = "SHOW COLUMNS FROM tm_regional LIKE 'zona_id'";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE tm_regional ADD COLUMN zona_id INT DEFAULT NULL";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            echo "Columna 'zona_id' agregada a tm_regional.\n";

            // Migrate data from 'zona' column if it exists
            $sql = "SHOW COLUMNS FROM tm_regional LIKE 'zona'";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                // Update zona_id based on zona text
                $sql = "UPDATE tm_regional r JOIN tm_zona z ON r.zona = z.zona_nom SET r.zona_id = z.zona_id";
                $stmt = $conectar->prepare($sql);
                $stmt->execute();
                echo "Datos migrados de 'zona' a 'zona_id'.\n";

                // Drop 'zona' column
                $sql = "ALTER TABLE tm_regional DROP COLUMN zona";
                $stmt = $conectar->prepare($sql);
                $stmt->execute();
                echo "Columna 'zona' eliminada de tm_regional.\n";
            }
        } else {
            echo "La columna 'zona_id' ya existe en tm_regional.\n";
        }
    }
}

$migration = new Migration();
$migration->run();
