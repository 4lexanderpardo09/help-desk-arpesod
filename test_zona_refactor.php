<?php
require_once("config/conexion.php");
require_once("models/Usuario.php");
require_once("models/Regional.php");
require_once("models/Zona.php");

class Test extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // 1. Setup Data
        $regionalModel = new Regional();
        $usuarioModel = new Usuario();
        $zonaModel = new Zona();

        // Get Zone IDs
        $zonas = $zonaModel->get_zonas();
        $zona_norte_id = null;
        $zona_sur_id = null;
        foreach ($zonas as $z) {
            if ($z['zona_nom'] == 'Norte') $zona_norte_id = $z['zona_id'];
            if ($z['zona_nom'] == 'Sur') $zona_sur_id = $z['zona_id'];
        }

        if (!$zona_norte_id || !$zona_sur_id) {
            die("Zones not found in DB.\n");
        }

        // Create Regions with Zone IDs
        $reg_norte = $regionalModel->insert_regional("Region Norte Test", $zona_norte_id);
        $reg_sur = $regionalModel->insert_regional("Region Sur Test", $zona_sur_id);

        // Create Roles (Assuming IDs exist or fetching them)
        $stmt = $conectar->query("SELECT * FROM tm_cargo LIMIT 5");
        $cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($cargos) < 1) {
            die("Not enough cargos to test.\n");
        }
        $boss_role_id = $cargos[0]['car_id'];

        // Create Users
        // Boss Norte
        $boss_norte_id = $usuarioModel->insert_usuario("Boss", "Norte", "boss.norte@test.com", "123", 2, null, 0, $reg_norte, $boss_role_id);
        // Boss Sur
        $boss_sur_id = $usuarioModel->insert_usuario("Boss", "Sur", "boss.sur@test.com", "123", 2, null, 0, $reg_sur, $boss_role_id);

        // 2. Test Logic
        echo "Testing get_usuario_por_cargo_y_zona...\n";

        // Test Norte
        $found_boss_norte = $usuarioModel->get_usuario_por_cargo_y_zona($boss_role_id, 'Norte');
        if ($found_boss_norte && $found_boss_norte['usu_id'] == $boss_norte_id) {
            echo "SUCCESS: Found Boss Norte correctly.\n";
        } else {
            echo "FAILURE: Expected Boss Norte ($boss_norte_id), found " . ($found_boss_norte['usu_id'] ?? 'None') . "\n";
        }

        // Test Sur
        $found_boss_sur = $usuarioModel->get_usuario_por_cargo_y_zona($boss_role_id, 'Sur');
        if ($found_boss_sur && $found_boss_sur['usu_id'] == $boss_sur_id) {
            echo "SUCCESS: Found Boss Sur correctly.\n";
        } else {
            echo "FAILURE: Expected Boss Sur ($boss_sur_id), found " . ($found_boss_sur['usu_id'] ?? 'None') . "\n";
        }

        // Cleanup
        $conectar->query("DELETE FROM tm_usuario WHERE usu_id IN ($boss_norte_id, $boss_sur_id)");
        $conectar->query("DELETE FROM tm_regional WHERE reg_id IN ($reg_norte, $reg_sur)");
    }
}

$test = new Test();
$test->run();
