<?php
require_once('config/conexion.php');

class MigrationAddCargoToParallelUsers extends Conectar {
    public function up() {
        $conectar = parent::Conexion();
        
        // 1. Add car_id column if not exists
        $sql = "SHOW COLUMNS FROM tm_flujo_paso_usuarios LIKE 'car_id'";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE tm_flujo_paso_usuarios ADD COLUMN car_id INT NULL DEFAULT NULL AFTER usu_id";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            echo "Column 'car_id' added.\n";
        }

        // 2. Drop existing Primary Key
        try {
            $sql = "ALTER TABLE tm_flujo_paso_usuarios DROP PRIMARY KEY";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            echo "Primary Key dropped.\n";
        } catch (Exception $e) {
            echo "PK drop failed (maybe it didn't exist?): " . $e->getMessage() . "\n";
        }

        // 3. Make usu_id nullable
        $sql = "ALTER TABLE tm_flujo_paso_usuarios MODIFY COLUMN usu_id INT NULL DEFAULT NULL";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        echo "Column 'usu_id' modified to be nullable.\n";

        // 4. Add new ID column to be the primary key (optional but good practice)
        // Or just add a unique constraint if needed. For now, we just need to allow NULLs.
        // Let's add an auto-increment ID if it doesn't exist, or just leave it without PK for now (simple link table).
        
        // Check if 'id' column exists
        $sql = "SHOW COLUMNS FROM tm_flujo_paso_usuarios LIKE 'id'";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
             $sql = "ALTER TABLE tm_flujo_paso_usuarios ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST";
             $stmt = $conectar->prepare($sql);
             $stmt->execute();
             echo "Added 'id' primary key column.\n";
        }
    }
}

$migration = new MigrationAddCargoToParallelUsers();
$migration->up();
?>
