<?php
require_once("config/conexion.php");

try {
    $dbh = Conectar::getConexion();

    $sql = "ALTER TABLE tm_usuario 
            ADD COLUMN usu_token_recuperacion VARCHAR(255) NULL AFTER usu_pass,
            ADD COLUMN usu_token_expiracion DATETIME NULL AFTER usu_token_recuperacion";

    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    echo "Migration successful: Columns added to tm_usuario.\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') { // Duplicate column error
        echo "Migration skipped: Columns already exist.\n";
    } else {
        echo "Migration failed: " . $e->getMessage() . "\n";
    }
}
